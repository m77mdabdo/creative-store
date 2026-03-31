<?php

namespace App\Services\SystemBackup;

use App\Models\AuditLog;
use App\Models\SystemBackup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use ZipArchive;

class SystemBackupService
{
    public function create(string $type = 'manual'): SystemBackup
    {
        $this->ensureBackupDirectoryExists();

        if ($this->hasRecentRunningBackup()) {
            throw new RuntimeException('A backup process is already running or was started very recently.');
        }

        $startedAt = now();

        $systemBackup = SystemBackup::query()->create([
            'type'        => $type,
            'file_path'   => '',
            'size_bytes'  => null,
            'status'      => 'failed',
            'started_at'  => $startedAt,
            'finished_at' => null,
            'note'        => null,
        ]);

        try {
            $fileName = $this->generateBackupFileName($type);
            $relativePath = 'backups/' . $fileName;
            $absolutePath = storage_path('app/' . $relativePath);

            $this->createZipBackup($absolutePath);

            $sizeBytes = File::exists($absolutePath) ? File::size($absolutePath) : null;

            $systemBackup->update([
                'file_path'   => $relativePath,
                'size_bytes'  => $sizeBytes,
                'status'      => 'success',
                'finished_at' => now(),
                'note'        => 'Backup completed successfully.',
            ]);

            $this->writeAuditLog('create', $systemBackup, [], [
                'type'       => $type,
                'file_path'  => $relativePath,
                'size_bytes' => $sizeBytes,
                'status'     => 'success',
            ]);

            return $systemBackup;
        } catch (Throwable $e) {
            $systemBackup->update([
                'status'      => 'failed',
                'finished_at' => now(),
                'note'        => mb_substr($e->getMessage(), 0, 1000),
            ]);

            $this->writeAuditLog('create_failed', $systemBackup, [], [
                'type'    => $type,
                'status'  => 'failed',
                'message' => mb_substr($e->getMessage(), 0, 1000),
            ]);

            throw $e;
        }
    }

    public function deleteWithFile(SystemBackup $systemBackup): void
    {
        if (! $systemBackup->exists) {
            return;
        }

        $oldValues = [
            'id'        => $systemBackup->id,
            'file_path' => $systemBackup->file_path,
            'status'    => $systemBackup->status,
            'type'      => $systemBackup->type,
        ];

        $absolutePath = $this->resolveAbsolutePath($systemBackup->file_path);

        if ($absolutePath && File::exists($absolutePath)) {
            File::delete($absolutePath);
        }

        $systemBackup->delete();

        $this->writeAuditLog('delete', $systemBackup, $oldValues, []);
    }

    public function canDownload(SystemBackup $systemBackup): bool
    {
        $absolutePath = $this->resolveAbsolutePath($systemBackup->file_path);

        return $absolutePath !== null && File::exists($absolutePath);
    }

    public function resolveAbsolutePath(?string $filePath): ?string
    {
        if (! is_string($filePath) || trim($filePath) === '') {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $filePath), '/');
        $absolute = storage_path('app/' . $normalized);

        $backupsRoot = realpath(storage_path('app/backups'));
        $targetDir = realpath(dirname($absolute));

        if (! $backupsRoot || ! $targetDir) {
            return null;
        }

        if (! str_starts_with($targetDir, $backupsRoot)) {
            return null;
        }

        return $absolute;
    }

    public function humanReadableSize(?int $bytes): string
    {
        if (is_null($bytes) || $bytes < 0) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = (float) $bytes;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return number_format($size, $unitIndex === 0 ? 0 : 2) . ' ' . $units[$unitIndex];
    }

    public function cleanupOldBackups(int $days = 30): int
    {
        $deleted = 0;

        $records = SystemBackup::query()
            ->whereNotNull('finished_at')
            ->where('finished_at', '<', now()->subDays($days))
            ->get();

        foreach ($records as $record) {
            $this->deleteWithFile($record);
            $deleted++;
        }

        return $deleted;
    }

    protected function ensureBackupDirectoryExists(): void
    {
        $path = storage_path('app/backups');

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    protected function hasRecentRunningBackup(): bool
    {
        return SystemBackup::query()
            ->whereNull('finished_at')
            ->where('started_at', '>=', now()->subMinutes(2))
            ->exists();
    }

    protected function generateBackupFileName(string $type): string
    {
        $timestamp = now()->format('Ymd_His');
        $environment = config('app.env', 'production');
        $random = Str::upper(Str::random(6));

        return "backup_{$type}_{$environment}_{$timestamp}_{$random}.zip";
    }

    protected function createZipBackup(string $absolutePath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create backup archive.');
        }

        $includePaths = [
            base_path('app'),
            base_path('bootstrap'),
            base_path('config'),
            base_path('database'),
            base_path('public'),
            base_path('resources'),
            base_path('routes'),
            base_path('artisan'),
            base_path('composer.json'),
            base_path('composer.lock'),
            base_path('.env.example'),
        ];

        foreach ($includePaths as $path) {
            if (! File::exists($path)) {
                continue;
            }

            if (File::isFile($path)) {
                $zip->addFile($path, basename($path));
                continue;
            }

            $this->addDirectoryToZip($zip, $path, basename($path));
        }

        $zip->addFromString('metadata.json', json_encode([
            'created_at'  => now()->toDateTimeString(),
            'environment' => config('app.env'),
            'app_name'    => config('app.name'),
            'php_version' => PHP_VERSION,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $zip->close();
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $directory, string $insidePath): void
    {
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            $realPath = $file->getRealPath();

            if (! $realPath) {
                continue;
            }

            if ($this->shouldSkipFile($realPath)) {
                continue;
            }

            $relativePath = $insidePath . '/' . ltrim(str_replace($directory, '', $realPath), DIRECTORY_SEPARATOR);
            $relativePath = str_replace('\\', '/', $relativePath);

            $zip->addFile($realPath, $relativePath);
        }
    }

    protected function shouldSkipFile(string $path): bool
    {
        $path = str_replace('\\', '/', $path);

        $skipPatterns = [
            '/storage/logs/',
            '/storage/framework/cache/',
            '/storage/framework/sessions/',
            '/storage/framework/views/',
            '/storage/app/backups/',
            '/vendor/',
            '/node_modules/',
            '/.git/',
        ];

        foreach ($skipPatterns as $pattern) {
            if (str_contains($path, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function writeAuditLog(string $action, SystemBackup $systemBackup, array $oldValues = [], array $newValues = []): void
    {
        try {
            AuditLog::query()->create([
                'user_id'        => Auth::id(),
                'action'         => $action,
                'auditable_type' => SystemBackup::class,
                'auditable_id'   => $systemBackup->id,
                'old_values'     => ! empty($oldValues) ? $oldValues : null,
                'new_values'     => ! empty($newValues) ? $newValues : null,
                'ip_address'     => app()->runningInConsole() ? null : request()?->ip(),
            ]);
        } catch (Throwable $e) {
            // no-op
        }
    }
}

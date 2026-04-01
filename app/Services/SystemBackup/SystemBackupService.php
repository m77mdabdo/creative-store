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

            $tempSqlPath = $this->createDatabaseDump();
            $publicFilesSummary = $this->buildPublicFilesSummary();

            $this->createZipBackup($absolutePath, $tempSqlPath, $publicFilesSummary);

            if (File::exists($tempSqlPath)) {
                File::delete($tempSqlPath);
            }

            $sizeBytes = File::exists($absolutePath) ? File::size($absolutePath) : null;

            $noteParts = [
                'Backup completed successfully.',
                $publicFilesSummary['note'] ?? null,
            ];

            $systemBackup->update([
                'file_path'   => $relativePath,
                'size_bytes'  => $sizeBytes,
                'status'      => 'success',
                'finished_at' => now(),
                'note'        => trim(implode(' ', array_filter($noteParts))),
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

        $tmpPath = storage_path('app/backups/tmp');

        if (! File::exists($tmpPath)) {
            File::makeDirectory($tmpPath, 0755, true);
        }
    }

    protected function hasRecentRunningBackup(): bool
    {
        return SystemBackup::query()
            ->whereNull('finished_at')
            ->where('started_at', '>=', now()->subMinutes(10))
            ->exists();
    }

    protected function generateBackupFileName(string $type): string
    {
        $timestamp = now()->format('Ymd_His');
        $environment = config('app.env', 'production');
        $random = Str::upper(Str::random(6));

        return "backup_{$type}_{$environment}_{$timestamp}_{$random}.zip";
    }

    protected function createZipBackup(string $absolutePath, string $tempSqlPath, array $publicFilesSummary): void
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

        if (File::exists($tempSqlPath)) {
            $zip->addFile($tempSqlPath, 'database/database.sql');
        }

        if (($publicFilesSummary['include'] ?? false) === true && ! empty($publicFilesSummary['files'])) {
            foreach ($publicFilesSummary['files'] as $file) {
                $zip->addFile($file['real_path'], $file['zip_path']);
            }
        }

        $zip->addFromString('metadata.json', json_encode([
            'created_at'               => now()->toDateTimeString(),
            'environment'              => config('app.env'),
            'app_name'                 => config('app.name'),
            'php_version'              => PHP_VERSION,
            'db_connection'            => config('database.default'),
            'db_database'              => config('database.connections.' . config('database.default') . '.database'),
            'contains_database_dump'   => true,
            'contains_public_files'    => ($publicFilesSummary['include'] ?? false) === true,
            'public_files_total_size'  => $publicFilesSummary['total_size'] ?? 0,
            'public_files_total_count' => $publicFilesSummary['total_count'] ?? 0,
            'public_files_note'        => $publicFilesSummary['note'] ?? null,
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

protected function createDatabaseDump(): string
{
    $defaultConnection = config('database.default');

    if ($defaultConnection !== 'mysql') {
        throw new \RuntimeException('Database backup currently supports mysql connection only.');
    }

    $connection = config('database.connections.mysql');

    $host = (string) ($connection['host'] ?? '127.0.0.1');
    $port = (string) ($connection['port'] ?? '3306');
    $database = (string) ($connection['database'] ?? '');
    $username = (string) ($connection['username'] ?? '');
    $password = (string) ($connection['password'] ?? '');

    if ($database === '' || $username === '') {
        throw new \RuntimeException('Database credentials are incomplete for mysql dump.');
    }

    $mysqldumpPath = (string) env('MYSQLDUMP_PATH', 'mysqldump');
    $tempSqlPath = storage_path('app/backups/tmp/database-' . now()->format('Ymd-His-u') . '.sql');

    if (! \Illuminate\Support\Facades\File::exists(dirname($tempSqlPath))) {
        \Illuminate\Support\Facades\File::makeDirectory(dirname($tempSqlPath), 0755, true);
    }

    $command = escapeshellarg($mysqldumpPath)
        . ' -h ' . escapeshellarg($host)
        . ' -P ' . escapeshellarg($port)
        . ' -u ' . escapeshellarg($username);

    if ($password !== '') {
        $command .= ' -p' . escapeshellarg($password);
    }

    $command .= ' --single-transaction --quick --routines --triggers --skip-lock-tables '
        . escapeshellarg($database)
        . ' > ' . escapeshellarg($tempSqlPath) . ' 2> ' . escapeshellarg($tempSqlPath . '.error');

    exec($command, $output, $resultCode);

    $errorOutput = '';

    if (\Illuminate\Support\Facades\File::exists($tempSqlPath . '.error')) {
        $errorOutput = trim((string) \Illuminate\Support\Facades\File::get($tempSqlPath . '.error'));
        \Illuminate\Support\Facades\File::delete($tempSqlPath . '.error');
    }

    if (
        $resultCode !== 0
        || ! \Illuminate\Support\Facades\File::exists($tempSqlPath)
        || \Illuminate\Support\Facades\File::size($tempSqlPath) === 0
    ) {
        if (\Illuminate\Support\Facades\File::exists($tempSqlPath)) {
            \Illuminate\Support\Facades\File::delete($tempSqlPath);
        }

        throw new \RuntimeException(
            'Database dump failed' . ($errorOutput !== '' ? ': ' . $errorOutput : '.')
        );
    }

    return $tempSqlPath;
}

    protected function buildPublicFilesSummary(): array
    {
        $includePublicFiles = filter_var(env('BACKUP_INCLUDE_PUBLIC_FILES', false), FILTER_VALIDATE_BOOL);

        if (! $includePublicFiles) {
            return [
                'include'     => false,
                'files'       => [],
                'total_size'  => 0,
                'total_count' => 0,
                'note'        => 'Public files backup disabled.',
            ];
        }

        $publicPath = storage_path('app/public');

        if (! File::exists($publicPath)) {
            return [
                'include'     => false,
                'files'       => [],
                'total_size'  => 0,
                'total_count' => 0,
                'note'        => 'Public files directory not found.',
            ];
        }

        $maxSizeMb = (float) env('BACKUP_PUBLIC_FILES_MAX_SIZE_MB', 300);
        $maxSizeBytes = (int) round($maxSizeMb * 1024 * 1024);

        $excludedExtensions = array_filter(array_map(
            fn ($ext) => strtolower(trim($ext)),
            explode(',', (string) env('BACKUP_EXCLUDED_EXTENSIONS', 'mp4,mov,avi,mkv,zip,rar,7z,iso'))
        ));

        $files = [];
        $totalSize = 0;
        $totalCount = 0;

        foreach (File::allFiles($publicPath) as $file) {
            $realPath = $file->getRealPath();

            if (! $realPath) {
                continue;
            }

            $extension = strtolower($file->getExtension());

            if ($extension !== '' && in_array($extension, $excludedExtensions, true)) {
                continue;
            }

            $size = (int) $file->getSize();

            if (($totalSize + $size) > $maxSizeBytes) {
                return [
                    'include'     => false,
                    'files'       => [],
                    'total_size'  => $totalSize,
                    'total_count' => $totalCount,
                    'note'        => 'Public files skipped because size exceeded limit of ' . $maxSizeMb . ' MB.',
                ];
            }

            $relativePath = str_replace(
                '\\',
                '/',
                ltrim(str_replace($publicPath, '', $realPath), DIRECTORY_SEPARATOR)
            );

            $files[] = [
                'real_path' => $realPath,
                'zip_path'  => 'storage/app/public/' . $relativePath,
                'size'      => $size,
            ];

            $totalSize += $size;
            $totalCount++;
        }

        return [
            'include'     => true,
            'files'       => $files,
            'total_size'  => $totalSize,
            'total_count' => $totalCount,
            'note'        => 'Public files included successfully.',
        ];
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

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Backup\BulkDeleteBackupRequest;
use App\Http\Requests\Dashboard\Backup\IndexBackupRequest;
use App\Models\AuditLog;
use App\Models\SystemBackup;
use App\Services\SystemBackup\SystemBackupService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

class BackupController extends Controller
{
    public function __construct(
        protected SystemBackupService $systemBackupService
    ) {
    }

    public function index(IndexBackupRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 20);

        $query = SystemBackup::query();

        $this->applyFilters($query, $filters);

        $backups = $query
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        foreach ($backups as $backup) {
            $backup->human_size = $this->systemBackupService->humanReadableSize($backup->size_bytes);
            $backup->file_exists = $this->systemBackupService->canDownload($backup);
        }

        $stats = [
            'total'     => SystemBackup::query()->count(),
            'success'   => SystemBackup::query()->where('status', 'success')->count(),
            'failed'    => SystemBackup::query()->where('status', 'failed')->count(),
            'scheduled' => SystemBackup::query()->where('type', 'scheduled')->count(),
        ];

        return view('dashboard.backups.index', [
            'backups' => $backups,
            'filters' => [
                'q'         => $filters['q'] ?? '',
                'type'      => $filters['type'] ?? '',
                'status'    => $filters['status'] ?? '',
                'date_from' => $filters['date_from'] ?? '',
                'date_to'   => $filters['date_to'] ?? '',
                'per_page'  => $perPage,
            ],
            'stats' => $stats,
        ]);
    }

    public function create(): RedirectResponse
    {
        try {
            $backup = $this->systemBackupService->create('manual');

            return redirect()
                ->route('dashboard.backups.show', $backup)
                ->with('success', 'Manual backup created successfully.');
        } catch (Throwable $e) {
            return redirect()
                ->route('dashboard.backups.index')
                ->with('error', 'Backup process failed: ' . $e->getMessage());
        }
    }

    public function show(SystemBackup $systemBackup): View
    {
        $fileExists = $this->systemBackupService->canDownload($systemBackup);
        $humanSize = $this->systemBackupService->humanReadableSize($systemBackup->size_bytes);

        return view('dashboard.backups.show', [
            'systemBackup' => $systemBackup,
            'fileExists'   => $fileExists,
            'humanSize'    => $humanSize,
        ]);
    }

    public function download(SystemBackup $systemBackup)
    {
        $absolutePath = $this->systemBackupService->resolveAbsolutePath($systemBackup->file_path);

        if (! $absolutePath || ! file_exists($absolutePath)) {
            abort(404);
        }

        $this->writeAuditLog('download', $systemBackup, [], [
            'file_path' => $systemBackup->file_path,
        ]);

        return response()->download($absolutePath);
    }

    public function destroy(SystemBackup $systemBackup): RedirectResponse
    {
        $this->systemBackupService->deleteWithFile($systemBackup);

        return redirect()
            ->route('dashboard.backups.index')
            ->with('success', 'Backup history record deleted successfully.');
    }

    public function bulkDelete(BulkDeleteBackupRequest $request): RedirectResponse
    {
        $ids = $request->validated('ids');

        $records = SystemBackup::query()
            ->whereIn('id', $ids)
            ->get();

        foreach ($records as $record) {
            $this->systemBackupService->deleteWithFile($record);
        }

        return redirect()
            ->route('dashboard.backups.index')
            ->with('success', 'Selected backup history records deleted successfully.');
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $q = trim((string) ($filters['q'] ?? ''));

        if ($q !== '') {
            $query->where(function (Builder $builder) use ($q) {
                $builder->where('file_path', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%")
                    ->orWhere('note', 'like', "%{$q}%");

                if (is_numeric($q)) {
                    $builder->orWhere('id', (int) $q);
                }
            });
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('started_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('started_at', '<=', $filters['date_to']);
        }
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

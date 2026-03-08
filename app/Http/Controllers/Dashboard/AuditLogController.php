<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AuditLog\IndexAuditLogRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\Export\CsvCell;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(IndexAuditLogRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 20);

        $query = AuditLog::query()
            ->with([
                'user:id,name,email',
            ]);

        $this->applyFilters($query, $filters);

        $auditLogs = $query
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $users = User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->limit(200)
            ->get();

        $actions = AuditLog::query()
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $auditableTypes = AuditLog::query()
            ->select('auditable_type')
            ->whereNotNull('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type');

        return view('dashboard.audit.index', [
            'auditLogs'      => $auditLogs,
            'users'          => $users,
            'actions'        => $actions,
            'auditableTypes' => $auditableTypes,
            'filters'        => $this->normalizedFilters($filters, $perPage),
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load([
            'user:id,name,email',
        ]);

        return view('dashboard.audit.show', [
            'auditLog'      => $auditLog,
            'oldValuesRows' => $this->flattenAuditValues((array) ($auditLog->old_values ?? [])),
            'newValuesRows' => $this->flattenAuditValues((array) ($auditLog->new_values ?? [])),
        ]);
    }

    public function export(IndexAuditLogRequest $request): StreamedResponse
    {
        $filters = $request->validated();

        $query = AuditLog::query()
            ->with(['user:id,name,email']);

        $this->applyFilters($query, $filters);

        $fileName = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'User',
                'Email',
                'Action',
                'Auditable Type',
                'Auditable ID',
                'IP Address',
                'Created At',
            ]);

            $query->latest('id')
                ->chunk(500, function ($logs) use ($handle) {
                    foreach ($logs as $log) {
                        fputcsv($handle, CsvCell::row([
                            $log->id,
                            $log->user?->name,
                            $log->user?->email,
                            $log->action,
                            $log->auditable_type,
                            $log->auditable_id,
                            $log->ip_address,
                            optional($log->created_at)->format('Y-m-d H:i:s'),
                        ]));
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function print(IndexAuditLogRequest $request): View
    {
        $filters = $request->validated();

        $query = AuditLog::query()
            ->with(['user:id,name,email']);

        $this->applyFilters($query, $filters);

        $auditLogs = $query
            ->latest('id')
            ->limit(500)
            ->get();

        return view('dashboard.audit.print', [
            'auditLogs' => $auditLogs,
            'filters'   => $this->normalizedFilters($filters, 500),
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $q = trim((string) ($filters['q'] ?? ''));

        if ($q !== '') {
            $query->where(function (Builder $builder) use ($q) {
                $builder->where('action', 'like', "%{$q}%")
                    ->orWhere('auditable_type', 'like', "%{$q}%")
                    ->orWhere('ip_address', 'like', "%{$q}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($q) {
                        $userQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });

                if (is_numeric($q)) {
                    $builder->orWhere('id', (int) $q)
                        ->orWhere('auditable_id', (int) $q)
                        ->orWhere('user_id', (int) $q);
                }
            });
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (! empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (! empty($filters['auditable_id'])) {
            $query->where('auditable_id', (int) $filters['auditable_id']);
        }

        if (! empty($filters['ip_address'])) {
            $query->where('ip_address', 'like', '%' . $filters['ip_address'] . '%');
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }

    protected function normalizedFilters(array $filters, int $perPage): array
    {
        return [
            'q'              => $filters['q'] ?? '',
            'user_id'        => $filters['user_id'] ?? '',
            'action'         => $filters['action'] ?? '',
            'auditable_type' => $filters['auditable_type'] ?? '',
            'auditable_id'   => $filters['auditable_id'] ?? '',
            'ip_address'     => $filters['ip_address'] ?? '',
            'date_from'      => $filters['date_from'] ?? '',
            'date_to'        => $filters['date_to'] ?? '',
            'per_page'       => $perPage,
        ];
    }

    protected function flattenAuditValues(array $values, string $prefix = ''): array
    {
        $rows = [];

        foreach ($values as $key => $value) {
            $fullKey = $prefix !== '' ? $prefix . '.' . $key : (string) $key;

            if (is_array($value)) {
                $rows = array_merge($rows, $this->flattenAuditValues($value, $fullKey));
                continue;
            }

            $rows[] = [
                'key'   => $fullKey,
                'value' => $this->stringifyAuditValue($value),
            ];
        }

        return $rows;
    }

    protected function stringifyAuditValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $value;
    }
}

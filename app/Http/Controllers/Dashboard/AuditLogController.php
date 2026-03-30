<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AuditLog\IndexAuditLogRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

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
            'filters'        => [
                'q'              => $filters['q'] ?? '',
                'user_id'        => $filters['user_id'] ?? '',
                'action'         => $filters['action'] ?? '',
                'auditable_type' => $filters['auditable_type'] ?? '',
                'auditable_id'   => $filters['auditable_id'] ?? '',
                'ip_address'     => $filters['ip_address'] ?? '',
                'date_from'      => $filters['date_from'] ?? '',
                'date_to'        => $filters['date_to'] ?? '',
                'per_page'       => $perPage,
            ],
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load([
            'user:id,name,email',
        ]);

        return view('dashboard.audit.show', [
            'auditLog' => $auditLog,
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
}

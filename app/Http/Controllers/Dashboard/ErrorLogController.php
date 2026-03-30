<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ErrorLog\BulkErrorLogActionRequest;
use App\Http\Requests\Dashboard\ErrorLog\IndexErrorLogRequest;
use App\Models\ErrorLog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class ErrorLogController extends Controller
{
    public function index(IndexErrorLogRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 20);

        $query = ErrorLog::query();

        $this->applyFilters($query, $filters);

        $errorLogs = $query
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $levels = ErrorLog::query()
            ->select('level')
            ->whereNotNull('level')
            ->distinct()
            ->orderBy('level')
            ->pluck('level');

        $services = ErrorLog::query()
            ->select('service')
            ->whereNotNull('service')
            ->distinct()
            ->orderBy('service')
            ->pluck('service');

        $stats = [
            'total'      => ErrorLog::query()->count(),
            'resolved'   => ErrorLog::query()->whereNotNull('resolved_at')->count(),
            'unresolved' => ErrorLog::query()->whereNull('resolved_at')->count(),
            'critical'   => ErrorLog::query()->whereIn('level', ['critical', 'alert', 'emergency'])->count(),
        ];

        return view('dashboard.error-logs.index', [
            'errorLogs' => $errorLogs,
            'levels'    => $levels,
            'services'  => $services,
            'filters'   => [
                'q'            => $filters['q'] ?? '',
                'level'        => $filters['level'] ?? '',
                'service'      => $filters['service'] ?? '',
                'resolved'     => $filters['resolved'] ?? '',
                'search_trace' => $filters['search_trace'] ?? '0',
                'date_from'    => $filters['date_from'] ?? '',
                'date_to'      => $filters['date_to'] ?? '',
                'per_page'     => $perPage,
            ],
            'stats' => $stats,
        ]);
    }

    public function show(ErrorLog $errorLog): View
    {
        return view('dashboard.error-logs.show', [
            'errorLog' => $errorLog,
        ]);
    }

    public function resolve(ErrorLog $errorLog): RedirectResponse
    {
        if (is_null($errorLog->resolved_at)) {
            $errorLog->update([
                'resolved_at' => now(),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Error log marked as resolved successfully.');
    }

    public function bulkResolve(BulkErrorLogActionRequest $request): RedirectResponse
    {
        $ids = $request->validated('ids');

        ErrorLog::query()
            ->whereIn('id', $ids)
            ->whereNull('resolved_at')
            ->update([
                'resolved_at' => now(),
                'updated_at'  => now(),
            ]);

        return redirect()
            ->back()
            ->with('success', 'Selected error logs marked as resolved successfully.');
    }

    public function bulkDelete(BulkErrorLogActionRequest $request): RedirectResponse
    {
        $ids = $request->validated('ids');

        ErrorLog::query()
            ->whereIn('id', $ids)
            ->delete();

        return redirect()
            ->back()
            ->with('success', 'Selected error logs deleted successfully.');
    }

    public function clearResolved(): RedirectResponse
    {
        ErrorLog::query()
            ->whereNotNull('resolved_at')
            ->delete();

        return redirect()
            ->route('dashboard.error-logs.index')
            ->with('success', 'Resolved error logs cleared successfully.');
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $q = trim((string) ($filters['q'] ?? ''));
        $searchTrace = (string) ($filters['search_trace'] ?? '0') === '1';

        if ($q !== '') {
            $query->where(function (Builder $builder) use ($q, $searchTrace) {
                $builder->where('message', 'like', "%{$q}%")
                    ->orWhere('level', 'like', "%{$q}%")
                    ->orWhere('service', 'like', "%{$q}%");

                if ($searchTrace) {
                    $builder->orWhere('trace', 'like', "%{$q}%");
                }

                if (is_numeric($q)) {
                    $builder->orWhere('id', (int) $q);
                }
            });
        }

        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (! empty($filters['service'])) {
            $query->where('service', $filters['service']);
        }

        if (($filters['resolved'] ?? '') === 'yes') {
            $query->whereNotNull('resolved_at');
        }

        if (($filters['resolved'] ?? '') === 'no') {
            $query->whereNull('resolved_at');
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }
}

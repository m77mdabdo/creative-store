<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ShippingRate\StoreShippingRateRequest;
use App\Http\Requests\Dashboard\ShippingRate\UpdateShippingRateRequest;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingRateController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'zone_id' => ['nullable', 'integer', 'exists:shipping_zones,id'],
            'method_id' => ['nullable', 'integer', 'exists:shipping_methods,id'],
            'status' => ['nullable', 'in:active,inactive'],
            'sort' => ['nullable', 'in:newest,oldest,price_asc,price_desc,priority_asc,priority_desc'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
        ]);

        $perPage = (int) $request->input('per_page', 15);

        $query = ShippingRate::query()
            ->with([
                'zone:id,name,code,country',
                'method:id,name,code',
            ]);

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhereHas('zone', function ($zoneQ) use ($search) {
                        $zoneQ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('country', 'like', "%{$search}%");
                    })
                    ->orWhereHas('method', function ($methodQ) use ($search) {
                        $methodQ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('zone_id')) {
            $query->where('shipping_zone_id', $request->integer('zone_id'));
        }

        if ($request->filled('method_id')) {
            $query->where('shipping_method_id', $request->integer('method_id'));
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        match ($request->input('sort', 'priority_asc')) {
            'newest'        => $query->orderByDesc('id'),
            'oldest'        => $query->orderBy('id'),
            'price_asc'     => $query->orderBy('price')->orderBy('priority'),
            'price_desc'    => $query->orderByDesc('price')->orderBy('priority'),
            'priority_desc' => $query->orderByDesc('priority')->orderBy('price'),
            default         => $query->orderBy('priority')->orderBy('price'),
        };

        $shippingRates = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => ShippingRate::count(),
            'active' => ShippingRate::where('is_active', true)->count(),
            'inactive' => ShippingRate::where('is_active', false)->count(),
            'zones' => ShippingZone::count(),
            'methods' => ShippingMethod::count(),
        ];

        $zones = ShippingZone::query()
            ->orderBy('name')
            ->pluck('name', 'id');

        $methods = ShippingMethod::query()
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('dashboard.shipping-rates.index', compact(
            'shippingRates',
            'stats',
            'zones',
            'methods'
        ));
    }

public function create(): View
{
    $shippingRate = new ShippingRate();

    $zones = ShippingZone::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name', 'country']);

    $methods = ShippingMethod::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name', 'code']);

    return view('dashboard.shipping-rates.create', compact('shippingRate', 'zones', 'methods'));
}

    public function store(StoreShippingRateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['priority'] = (int) ($data['priority'] ?? 0);

        ShippingRate::create($data);

        return redirect()
            ->route('dashboard.shipping-rates.index')
            ->with('success', 'Shipping rate created successfully.');
    }

    public function edit(ShippingRate $shippingRate): View
    {
        $zones = ShippingZone::query()
            ->orderBy('name')
            ->get(['id', 'name', 'country']);

        $methods = ShippingMethod::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('dashboard.shipping-rates.edit', compact(
            'shippingRate',
            'zones',
            'methods'
        ));
    }

    public function update(UpdateShippingRateRequest $request, ShippingRate $shippingRate): RedirectResponse
    {
        $data = $request->validated();

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['priority'] = (int) ($data['priority'] ?? 0);

        $shippingRate->update($data);

        return redirect()
            ->route('dashboard.shipping-rates.index')
            ->with('success', 'Shipping rate updated successfully.');
    }

    public function toggleStatus(ShippingRate $shippingRate): RedirectResponse
    {
        $shippingRate->update([
            'is_active' => ! $shippingRate->is_active,
        ]);

        return back()->with('success', 'Shipping rate status updated.');
    }

    public function destroy(ShippingRate $shippingRate): RedirectResponse
    {
        $shippingRate->delete();

        return redirect()
            ->route('dashboard.shipping-rates.index')
            ->with('success', 'Shipping rate deleted successfully.');
    }
}

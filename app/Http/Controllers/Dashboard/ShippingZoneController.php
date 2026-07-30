<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ShippingZone\StoreShippingZoneRequest;
use App\Http\Requests\Dashboard\ShippingZone\UpdateShippingZoneRequest;
use App\Models\ShippingZone;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShippingZoneController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'in:active,inactive'],
            'sort' => ['nullable', 'in:newest,oldest,name_asc,name_desc,country_asc,country_desc'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
        ]);

        $perPage = (int) $request->input('per_page', 15);

        $query = ShippingZone::query()->search($request->q);

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        match ($request->input('sort', 'name_asc')) {
            'newest'      => $query->orderByDesc('id'),
            'oldest'      => $query->orderBy('id'),
            'name_desc'   => $query->orderByDesc('name'),
            'country_asc' => $query->orderBy('country')->orderBy('name'),
            'country_desc'=> $query->orderByDesc('country')->orderBy('name'),
            default       => $query->orderBy('name'),
        };

        $shippingZones = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => ShippingZone::count(),
            'active' => ShippingZone::where('is_active', true)->count(),
            'inactive' => ShippingZone::where('is_active', false)->count(),
            'rates_count' => \App\Models\ShippingRate::count(),
        ];

        return view('dashboard.shipping-zones.index', compact('shippingZones', 'stats'));
    }

    public function create(): View
    {
        return view('dashboard.shipping-zones.create');
    }

    public function store(StoreShippingZoneRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        ShippingZone::create($data);

        return redirect()
            ->route('dashboard.shipping-zones.index')
            ->with('success', __('Shipping zone created successfully.'));
    }

    public function edit(ShippingZone $shippingZone): View
    {
        return view('dashboard.shipping-zones.edit', compact('shippingZone'));
    }

    public function update(UpdateShippingZoneRequest $request, ShippingZone $shippingZone): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $shippingZone->update($data);

        return redirect()
            ->route('dashboard.shipping-zones.index')
            ->with('success', __('Shipping zone updated successfully.'));
    }

    public function toggleStatus(ShippingZone $shippingZone): RedirectResponse
    {
        $shippingZone->update([
            'is_active' => ! $shippingZone->is_active,
        ]);

        return back()->with('success', __('Shipping zone status updated.'));
    }

    public function destroy(ShippingZone $shippingZone): RedirectResponse
    {
        if ($shippingZone->rates()->exists()) {
            return back()->with('error', __('Cannot delete this shipping zone because it is linked to shipping rates.'));
        }

        $shippingZone->delete();

        return redirect()
            ->route('dashboard.shipping-zones.index')
            ->with('success', __('Shipping zone deleted successfully.'));
    }
}

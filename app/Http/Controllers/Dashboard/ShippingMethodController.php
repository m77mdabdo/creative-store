<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ShippingMethod\StoreShippingMethodRequest;
use App\Http\Requests\Dashboard\ShippingMethod\UpdateShippingMethodRequest;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShippingMethodController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'in:active,inactive'],
            'sort' => ['nullable', 'in:newest,oldest,name_asc,name_desc,sort_asc,sort_desc'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
        ]);

        $perPage = (int) $request->input('per_page', 15);

        $query = ShippingMethod::query()->search($request->q);

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        match ($request->input('sort', 'sort_asc')) {
            'newest'    => $query->orderByDesc('id'),
            'oldest'    => $query->orderBy('id'),
            'name_asc'  => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'sort_desc' => $query->orderByDesc('sort_order')->orderBy('name'),
            default     => $query->orderBy('sort_order')->orderBy('name'),
        };

        $shippingMethods = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => ShippingMethod::count(),
            'active' => ShippingMethod::where('is_active', true)->count(),
            'inactive' => ShippingMethod::where('is_active', false)->count(),
            'rates_count' => \App\Models\ShippingRate::count(),
            'shipments_count' => \App\Models\Shipment::count(),
        ];

        return view('dashboard.shipping-methods.index', compact('shippingMethods', 'stats'));
    }

    public function create(): View
    {
        return view('dashboard.shipping-methods.create');
    }

    public function store(StoreShippingMethodRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        ShippingMethod::create($data);

        return redirect()
            ->route('dashboard.shipping-methods.index')
            ->with('success', 'Shipping method created successfully.');
    }

    public function edit(ShippingMethod $shippingMethod): View
    {
        return view('dashboard.shipping-methods.edit', compact('shippingMethod'));
    }

    public function update(UpdateShippingMethodRequest $request, ShippingMethod $shippingMethod): RedirectResponse
    {
        $data = $request->validated();

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $shippingMethod->update($data);

        return redirect()
            ->route('dashboard.shipping-methods.index')
            ->with('success', 'Shipping method updated successfully.');
    }

    public function toggleStatus(ShippingMethod $shippingMethod): RedirectResponse
    {
        $shippingMethod->update([
            'is_active' => ! $shippingMethod->is_active,
        ]);

        return back()->with('success', 'Shipping method status updated.');
    }

    public function destroy(ShippingMethod $shippingMethod): RedirectResponse
    {
        if ($shippingMethod->rates()->exists()) {
            return back()->with('error', 'Cannot delete this shipping method because it is linked to shipping rates.');
        }

        if ($shippingMethod->shipments()->exists()) {
            return back()->with('error', 'Cannot delete this shipping method because it is linked to shipments.');
        }

        $shippingMethod->delete();

        return redirect()
            ->route('dashboard.shipping-methods.index')
            ->with('success', 'Shipping method deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Shipment\StoreShipmentRequest;
use App\Http\Requests\Dashboard\Shipment\UpdateShipmentRequest;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\ShipmentTrackingLog;

class ShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'string', 'max:50'],
            'method_id' => ['nullable', 'integer', 'exists:shipping_methods,id'],
            'sort' => ['nullable', 'in:newest,oldest,shipped_desc,shipped_asc,delivered_desc,delivered_asc'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
        ]);

        $perPage = (int) $request->input('per_page', 15);

        $query = Shipment::query()
            ->with([
                'order:id,order_number,user_id,total,status,payment_status',
                'order.user:id,name,email',
                'method:id,name,code',
            ])
            ->search($request->q);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method_id')) {
            $query->where('shipping_method_id', $request->integer('method_id'));
        }

        match ($request->input('sort', 'newest')) {
            'oldest'         => $query->orderBy('id'),
            'shipped_desc'   => $query->orderByDesc('shipped_at')->orderByDesc('id'),
            'shipped_asc'    => $query->orderBy('shipped_at')->orderByDesc('id'),
            'delivered_desc' => $query->orderByDesc('delivered_at')->orderByDesc('id'),
            'delivered_asc'  => $query->orderBy('delivered_at')->orderByDesc('id'),
            default          => $query->orderByDesc('id'),
        };

        $shipments = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Shipment::count(),
            'pending' => Shipment::where('status', Shipment::STATUS_PENDING)->count(),
            'in_transit' => Shipment::whereIn('status', [
                Shipment::STATUS_PROCESSING,
                Shipment::STATUS_SHIPPED,
                Shipment::STATUS_IN_TRANSIT,
            ])->count(),
            'delivered' => Shipment::where('status', Shipment::STATUS_DELIVERED)->count(),
            'failed' => Shipment::whereIn('status', [
                Shipment::STATUS_FAILED,
                Shipment::STATUS_CANCELLED,
            ])->count(),
        ];

        $methods = ShippingMethod::query()
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('dashboard.shipments.index', compact(
            'shipments',
            'stats',
            'methods'
        ));
    }

    public function create(): View
    {
        $shipment = new Shipment();

        $orders = Order::query()
            ->with('user:id,name,email')
            ->latest('id')
            ->take(200)
            ->get();

        $methods = ShippingMethod::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.shipments.create', compact(
            'shipment',
            'orders',
            'methods'
        ));
    }

    public function store(StoreShipmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === Shipment::STATUS_SHIPPED && empty($data['shipped_at'])) {
            $data['shipped_at'] = now();
        }

        if ($data['status'] === Shipment::STATUS_DELIVERED && empty($data['delivered_at'])) {
            $data['delivered_at'] = now();
        }

        Shipment::create($data);

        return redirect()
            ->route('dashboard.shipments.index')
            ->with('success', __('Shipment created successfully.'));
    }

    public function show(Shipment $shipment): View
    {
        $shipment->load([
            'order.user:id,name,email,phone',
            'order.items',
            'method:id,name,code,description',
            'trackingLogs',
             'deliveryAttempts',
        ]);

        return view('dashboard.shipments.show', compact('shipment'));
    }

    public function edit(Shipment $shipment): View
    {
        $orders = Order::query()
            ->with('user:id,name,email')
            ->latest('id')
            ->take(200)
            ->get();

        $methods = ShippingMethod::query()
            ->orderBy('name')
            ->get();

        return view('dashboard.shipments.edit', compact(
            'shipment',
            'orders',
            'methods'
        ));
    }

public function update(UpdateShipmentRequest $request, Shipment $shipment): RedirectResponse
{
    $data = $request->validated();

    // =========================
    // Handle timestamps
    // =========================
    if (
        $data['status'] === Shipment::STATUS_SHIPPED
        && empty($data['shipped_at'])
        && ! $shipment->shipped_at
    ) {
        $data['shipped_at'] = now();
    }

    if (
        $data['status'] === Shipment::STATUS_DELIVERED
        && empty($data['delivered_at'])
        && ! $shipment->delivered_at
    ) {
        $data['delivered_at'] = now();
    }

    // =========================
    // Detect changes (IMPORTANT 🔥)
    // =========================
    $shouldTrack =
        ($shipment->status !== ($data['status'] ?? $shipment->status)) ||
        ($shipment->current_location !== ($data['current_location'] ?? $shipment->current_location)) ||
        ($shipment->carrier_status !== ($data['carrier_status'] ?? $shipment->carrier_status));

    // =========================
    // Update shipment
    // =========================
    $shipment->update($data);

    // =========================
    // Create tracking log ONLY if changed
    // =========================
    if ($shouldTrack) {
        \App\Models\ShipmentTrackingLog::create([
            'shipment_id' => $shipment->id,
            'status' => $data['status'] ?? $shipment->status,
            'location' => $data['current_location'] ?? $shipment->current_location,
            'carrier_status' => $data['carrier_status'] ?? $shipment->carrier_status,
            'note' => 'Auto update from dashboard',
            'tracked_at' => now(),
        ]);
    }

    return redirect()
        ->route('dashboard.shipments.index')
        ->with('success', __('Shipment updated successfully.'));
}

    public function toggleStatus(Shipment $shipment): RedirectResponse
    {
        $nextStatus = match ($shipment->status) {
            Shipment::STATUS_PENDING => Shipment::STATUS_PROCESSING,
            Shipment::STATUS_PROCESSING => Shipment::STATUS_SHIPPED,
            Shipment::STATUS_SHIPPED => Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_IN_TRANSIT => Shipment::STATUS_DELIVERED,
            default => Shipment::STATUS_PENDING,
        };

        $data = ['status' => $nextStatus];

        if ($nextStatus === Shipment::STATUS_SHIPPED && ! $shipment->shipped_at) {
            $data['shipped_at'] = now();
        }

        if ($nextStatus === Shipment::STATUS_DELIVERED && ! $shipment->delivered_at) {
            $data['delivered_at'] = now();
        }

        $shipment->update($data);

        return back()->with('success', __('Shipment status updated.'));
    }

    public function destroy(Shipment $shipment): RedirectResponse
    {
        $shipment->delete();

        return redirect()
            ->route('dashboard.shipments.index')
            ->with('success', __('Shipment deleted successfully.'));
    }
}

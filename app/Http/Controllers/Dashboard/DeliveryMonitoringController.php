<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAttempt;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliveryMonitoringController extends Controller
{
    public function storeAttempt(Request $request, Shipment $shipment): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:scheduled,out_for_delivery,delivered,failed,rescheduled,cancelled'],
            'driver_name' => ['nullable', 'string', 'max:150'],
            'driver_phone' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'failure_reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'attempted_at' => ['nullable', 'date'],
            'next_attempt_at' => ['nullable', 'date'],
        ]);

        $attemptNo = ((int) $shipment->deliveryAttempts()->max('attempt_no')) + 1;

        $attempt = DeliveryAttempt::create([
            'shipment_id' => $shipment->id,
            'attempt_no' => $attemptNo,
            'status' => $request->status,
            'driver_name' => $request->driver_name,
            'driver_phone' => $request->driver_phone,
            'location' => $request->location,
            'failure_reason' => $request->failure_reason,
            'notes' => $request->notes,
            'attempted_at' => $request->attempted_at ?: now(),
            'next_attempt_at' => $request->next_attempt_at,
        ]);

        // مزامنة حالة الشحنة
        $shipmentStatus = match ($request->status) {
            'scheduled' => 'pending',
            'out_for_delivery' => 'in_transit',
            'delivered' => 'delivered',
            'failed' => 'failed_return',
            'rescheduled' => 'pending',
            'cancelled' => 'failed_return',
            default => $shipment->status,
        };

        $shipment->update([
            'status' => $shipmentStatus,
            'current_location' => $request->location ?: $shipment->current_location,
            'carrier_status' => $request->status,
            'delivered_at' => $request->status === 'delivered' && ! $shipment->delivered_at ? now() : $shipment->delivered_at,
        ]);

        return back()->with('success', 'Delivery attempt added successfully.');
    }
}

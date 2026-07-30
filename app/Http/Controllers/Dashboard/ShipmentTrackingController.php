<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentTrackingLog;
use Illuminate\Http\Request;

class ShipmentTrackingController extends Controller
{
    public function store(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'carrier_status' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        ShipmentTrackingLog::create([
            'shipment_id' => $shipment->id,
            'status' => $request->status,
            'location' => $request->location,
            'carrier_status' => $request->carrier_status,
            'note' => $request->note,
            'tracked_at' => now(),
        ]);

        // تحديث الشحنة نفسها
        $shipment->update([
            'status' => $request->status ?? $shipment->status,
            'current_location' => $request->location ?? $shipment->current_location,
            'carrier_status' => $request->carrier_status ?? $shipment->carrier_status,
        ]);

        return back()->with('success', __('Tracking added'));
    }
}

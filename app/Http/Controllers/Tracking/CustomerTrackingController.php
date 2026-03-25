<?php

namespace App\Http\Controllers\Tracking;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerTrackingController extends Controller
{
    public function searchForm(): View
    {
        return view('tracking.search');
    }

    public function track(Request $request): View
    {
        $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:150'],
            'order_number' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $trackingNumber = trim((string) $request->input('tracking_number'));
        $orderNumber = trim((string) $request->input('order_number'));
        $phone = trim((string) $request->input('phone'));

        if ($trackingNumber === '' && ($orderNumber === '' || $phone === '')) {
            return back()
                ->withErrors([
                    'tracking_number' => 'Enter tracking number, or enter order number with phone number.',
                ])
                ->withInput();
        }

        $shipmentQuery = Shipment::query()->with([
            'order.user:id,name,email,phone',
            'order.addressSnapshot',
            'method:id,name,code',
            'trackingLogs',
            'deliveryAttempts',
        ]);

        if ($trackingNumber !== '') {
            $shipmentQuery->where('tracking_number', $trackingNumber);
        } else {
            $shipmentQuery->whereHas('order', function ($q) use ($orderNumber, $phone) {
                $q->where('order_number', $orderNumber)
                    ->where(function ($subQ) use ($phone) {
                        $subQ->whereHas('user', function ($userQ) use ($phone) {
                            $userQ->where('phone', $phone);
                        });

                        if (class_exists(\App\Models\OrderAddressSnapshot::class)) {
                            $subQ->orWhereHas('addressSnapshot', function ($snapshotQ) use ($phone) {
                                $snapshotQ->where('phone', $phone);
                            });
                        }
                    });
            });
        }

        $shipment = $shipmentQuery->latest('id')->first();

        return view('tracking.result', compact('shipment', 'trackingNumber', 'orderNumber', 'phone'));
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\Shipping\ShipmentCarrierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShipmentCarrierController extends Controller
{
    public function __construct(private readonly ShipmentCarrierService $carriers)
    {
    }

    public function book(Shipment $shipment): RedirectResponse
    {
        try {
            $shipment = $this->carriers->book($shipment);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __('Waybill :number created.', ['number' => $shipment->tracking_number]));
    }

    public function sync(Shipment $shipment): RedirectResponse
    {
        $new = $this->carriers->sync($shipment);

        return back()->with('success', trans_choice('{0} Tracking is up to date.|{1} 1 new tracking update.|[2,*] :count new tracking updates.', $new, ['count' => $new]));
    }

    public function label(Shipment $shipment): StreamedResponse
    {
        abort_unless($shipment->tracking_number, 404);

        $path = $this->carriers->labelPath($shipment->tracking_number);

        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, 'label-' . $shipment->tracking_number . '.pdf');
    }
}

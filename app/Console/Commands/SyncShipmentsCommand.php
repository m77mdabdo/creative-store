<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Services\Shipping\ShipmentCarrierService;
use Illuminate\Console\Command;
use Throwable;

class SyncShipmentsCommand extends Command
{
    protected $signature = 'shipments:sync';

    protected $description = 'Pull tracking updates from the carrier for shipments still on the way';

    public function handle(ShipmentCarrierService $carriers): int
    {
        $updated = 0;

        Shipment::query()
            ->whereNotNull('tracking_number')
            ->whereIn('status', [Shipment::STATUS_SHIPPED, Shipment::STATUS_IN_TRANSIT])
            ->with('order.addressSnapshot')
            ->chunkById(100, function ($shipments) use ($carriers, &$updated) {
                foreach ($shipments as $shipment) {
                    try {
                        $updated += $carriers->sync($shipment);
                    } catch (Throwable $e) {
                        report($e);
                        $this->warn("Shipment {$shipment->id}: {$e->getMessage()}");
                    }
                }
            });

        $this->info("{$updated} new tracking event(s).");

        return self::SUCCESS;
    }
}

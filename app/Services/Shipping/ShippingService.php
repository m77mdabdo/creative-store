<?php

namespace App\Services\Shipping;

use App\Models\Address;
use App\Models\ShippingRate;
use App\Support\Shipping\DeliveryPromise;

class ShippingService
{
    public function cartSubtotal(array $cart): float
    {
        return (float) collect($cart)->sum(function ($item) {
            return ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 1));
        });
    }

    /**
     * Total cart weight in kg. Items stored before weights were kept in the
     * session are looked up in two queries instead of being counted as 0.
     */
    public function cartWeight(array $cart): float
    {
        $missing = collect($cart)->filter(fn ($item) => ! array_key_exists('weight', $item));

        $variantWeights = \App\Models\ProductVariant::query()
            ->whereIn('id', $missing->pluck('variant_id')->filter()->unique())
            ->pluck('weight', 'id');

        $productWeights = \App\Models\Product::query()
            ->whereIn('id', $missing->pluck('product_id')->filter()->unique())
            ->pluck('weight', 'id');

        return round((float) collect($cart)->sum(function ($item) use ($variantWeights, $productWeights) {
            $weight = $item['weight']
                ?? $variantWeights[$item['variant_id'] ?? 0]
                ?? $productWeights[$item['product_id'] ?? 0]
                ?? 0;

            return (float) $weight * max(1, (int) ($item['quantity'] ?? 1));
        }), 3);
    }

public function availableOptions(Address $address, array $cart): array
{
    $subtotal = $this->cartSubtotal($cart);
    $weight = $this->cartWeight($cart);

    $country = trim((string) $address->country);
    $city = trim((string) $address->city);
    $district = trim((string) $address->district);

    $countryAliases = array_unique(array_filter([
        $country,
        $this->normalizeCountry($country),
    ]));

    $cityAliases = array_unique(array_filter([
        $city,
        $this->normalizeCity($city),
    ]));

    $rates = ShippingRate::with(['method', 'zone'])
        ->where('is_active', true)
        ->whereHas('method', function ($q) {
            $q->where('is_active', true);
        })
        ->whereHas('zone', function ($q) use ($countryAliases) {
            $q->where('is_active', true)
              ->where(function ($sub) use ($countryAliases) {
                  foreach ($countryAliases as $country) {
                      $sub->orWhere('country', $country);
                  }
              });
        })
        // An empty bound means "no limit"; plain comparisons would drop those
        // rates because NULL <= x is never true in SQL.
        ->where(fn ($q) => $q->whereNull('min_weight')->orWhere('min_weight', '<=', $weight))
        ->where(fn ($q) => $q->whereNull('max_weight')->orWhere('max_weight', '>=', $weight))
        ->where(function ($q) use ($cityAliases) {
            $q->whereNull('city');

            foreach ($cityAliases as $city) {
                $q->orWhere('city', $city);
            }
        })
        ->where(function ($q) use ($district) {
            $q->whereNull('district');

            if ($district !== '') {
                $q->orWhere('district', $district);
            }
        })
        ->orderByRaw("
            CASE
                WHEN city IS NULL THEN 2
                ELSE 1
            END
        ")
        ->orderBy('priority')
        ->get();

    $grouped = $rates->groupBy('shipping_method_id')->map(function ($items) use ($cityAliases) {
        return $items->sortBy(function ($rate) use ($cityAliases) {
            if (!is_null($rate->city) && in_array($rate->city, $cityAliases, true)) {
                return 1;
            }

            if (is_null($rate->city)) {
                return 2;
            }

            return 3;
        })->first();
    })->values();

    return $grouped->sortBy(fn ($rate) => $rate->method->sort_order ?? PHP_INT_MAX)->map(function ($rate) use ($subtotal) {
        $shippingFee = (float) $rate->price;

        if (!is_null($rate->free_shipping_threshold) && $subtotal >= (float) $rate->free_shipping_threshold) {
            $shippingFee = 0;
        }

        $methodName = \Illuminate\Support\Facades\Lang::has('shipping.methods.' . $rate->method->code)
            ? __('shipping.methods.' . $rate->method->code)
            : $rate->method->name;

        return [
            'rate_id' => $rate->id,
            'shipping_method_id' => $rate->shipping_method_id,
            'method_name' => $methodName,
            'method_code' => $rate->method->code,
            'price' => $shippingFee,
            'eta_min_days' => $rate->eta_min_days,
            'eta_max_days' => $rate->eta_max_days,
            'delivery' => DeliveryPromise::make($rate->eta_min_days, $rate->eta_max_days)->toArray(),
            'label' => $methodName . ' - ' . number_format($shippingFee, 2) . ' ' . __('SAR'),
        ];
    })->values()->toArray();
}

private function normalizeCountry(?string $country): ?string
{
    $country = trim((string) $country);

    return match ($country) {
        'السعودية', 'المملكة العربية السعودية', 'KSA' => 'Saudi Arabia',
        'Saudi Arabia', 'Kingdom of Saudi Arabia' => 'Saudi Arabia',
        default => $country,
    };
}

private function normalizeCity(?string $city): ?string
{
    $city = trim((string) $city);

    return match ($city) {
        'الرياض' => 'Riyadh',
        'Riyadh' => 'الرياض',

        'جدة' => 'Jeddah',
        'Jeddah' => 'جدة',

        'مكة', 'مكة المكرمة' => 'Makkah',
        'Makkah', 'Mecca' => 'مكة',

        'المدينة', 'المدينة المنورة' => 'Madinah',
        'Madinah', 'Medina' => 'المدينة',

        'الدمام' => 'Dammam',
        'Dammam' => 'الدمام',

        'الخبر' => 'Khobar',
        'Khobar', 'Al Khobar' => 'الخبر',

        'الظهران' => 'Dhahran',
        'Dhahran' => 'الظهران',

        default => $city,
    };
}

    /**
     * Order totals for the checkout summary. Built from the same rounded
     * parts CheckoutService uses, so the page, the order and the amount
     * sent to the payment gateway always match.
     */
    public function orderTotalsFromCartSummary(array $cartSummary, float $shippingFee): array
    {
        $subtotal = round((float) ($cartSummary['subtotal'] ?? 0), 2);
        $discount = round(min((float) ($cartSummary['discount'] ?? 0), $subtotal), 2);
        $taxTotal = round((float) ($cartSummary['tax_total'] ?? 0), 2);
        $shippingFee = round($shippingFee, 2);

        return [
            'subtotal'     => $subtotal,
            'discount'     => $discount,
            'shipping_fee' => $shippingFee,
            'tax_total'    => $taxTotal,
            'total'        => round($subtotal - $discount + $taxTotal + $shippingFee, 2),
        ];
    }
}

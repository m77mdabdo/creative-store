<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Address\AddressIndexRequest;
use App\Http\Requests\Dashboard\Address\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(AddressIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $query = Address::query()
            ->with([
                'user:id,name,email,phone',
            ]);

        $query->when(!empty($filters['q']), function ($q) use ($filters) {
            $search = trim((string) $filters['q']);

            $q->where(function ($sub) use ($search) {
                $sub->where('label', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('postal_code', 'like', "%{$search}%");
            })
            ->orWhereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });

        $query->when(!empty($filters['city']), function ($q) use ($filters) {
            $q->where('city', $filters['city']);
        });

        $query->when(isset($filters['is_default']) && $filters['is_default'] !== '', function ($q) use ($filters) {
            $q->where('is_default', (bool) $filters['is_default']);
        });

        $query->when(isset($filters['has_user']) && $filters['has_user'] !== '', function ($q) use ($filters) {
            if ((string) $filters['has_user'] === '1') {
                $q->whereNotNull('user_id');
            } elseif ((string) $filters['has_user'] === '0') {
                $q->whereNull('user_id');
            }
        });

        $sort = $filters['sort'] ?? 'latest';

        match ($sort) {
            'oldest'     => $query->oldest(),
            'city_asc'   => $query->orderBy('city'),
            'city_desc'  => $query->orderByDesc('city'),
            'name_asc'   => $query->orderBy('full_name'),
            'name_desc'  => $query->orderByDesc('full_name'),
            default      => $query->latest(),
        };

        $addresses = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total_addresses'   => Address::count(),
            'default_addresses' => Address::where('is_default', true)->count(),
            'with_users'        => Address::whereNotNull('user_id')->count(),
            'guest_addresses'   => Address::whereNull('user_id')->count(),
        ];

        $cities = Address::query()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('dashboard.addresses.index', compact('addresses', 'filters', 'stats', 'cities'));
    }

    public function show(Address $address): View
    {
        $address->load([
            'user:id,name,email,phone',
        ]);

        return view('dashboard.addresses.show', compact('address'));
    }

    public function edit(Address $address): View
    {
        $address->load([
            'user:id,name,email,phone',
        ]);

        return view('dashboard.addresses.edit', compact('address'));
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($address, $data) {
            $address->update([
                'label'        => $data['label'] ?? null,
                'full_name'    => $data['full_name'],
                'phone'        => $data['phone'],
                'country'      => $data['country'],
                'city'         => $data['city'],
                'district'     => $data['district'] ?? null,
                'street'       => $data['street'],
                'building_no'  => $data['building_no'] ?? null,
                'apartment_no' => $data['apartment_no'] ?? null,
                'postal_code'  => $data['postal_code'] ?? null,
                'lat'          => $data['lat'] ?? null,
                'lng'          => $data['lng'] ?? null,
                'notes'        => $data['notes'] ?? null,
                'is_default'   => !empty($data['is_default']),
            ]);

            if (!empty($data['is_default']) && $address->user_id) {
                Address::query()
                    ->where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });

        return redirect()
            ->route('dashboard.addresses.index')
            ->with('success', 'Address updated successfully.');
    }

    public function toggleDefault(Address $address): RedirectResponse
    {
        DB::transaction(function () use ($address) {
            if ($address->user_id) {
                Address::query()
                    ->where('user_id', $address->user_id)
                    ->update(['is_default' => false]);
            }

            $address->update([
                'is_default' => ! $address->is_default,
            ]);

            if ($address->is_default && $address->user_id) {
                Address::query()
                    ->where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });

        return back()->with('success', 'Default address status updated.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        $address->delete();

        return redirect()
            ->route('dashboard.addresses.index')
            ->with('success', 'Address deleted successfully.');
    }
}

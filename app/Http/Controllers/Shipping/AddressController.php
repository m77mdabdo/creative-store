<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\StoreAddressRequest;
use App\Http\Requests\Shipping\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = Address::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('site.shipping.addresses.index', compact('addresses'));
    }

    public function create(): View
    {
        return view('site.shipping.addresses.create');
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        if ($data['is_default']) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        if (!Address::where('user_id', auth()->id())->exists()) {
            $data['is_default'] = true;
        }

        Address::create($data);

        return redirect()->route('shipping.addresses.index')->with('success', __('Address added successfully.'));
    }

    public function edit(Address $address): View
    {
        abort_unless($address->user_id === auth()->id(), 403);

        return view('site.shipping.addresses.edit', compact('address'));
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === auth()->id(), 403);

        $data = $request->validated();
        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        if ($data['is_default']) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('shipping.addresses.index')->with('success', __('Address updated successfully.'));
    }

    public function destroy(Address $address): RedirectResponse
    {
        abort_unless($address->user_id === auth()->id(), 403);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $newDefault = Address::where('user_id', auth()->id())->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return back()->with('success', __('Address deleted successfully.'));
    }

    public function setDefault(Address $address): RedirectResponse
    {
        abort_unless($address->user_id === auth()->id(), 403);

        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', __('Default address updated.'));
    }
}

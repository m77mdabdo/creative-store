@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">
        <h3 class="mtext-111 cl2 p-b-16">Shipping</h3>

        @if(session('success'))
            <div class="alert alert-success m-b-20">{{ session('success') }}</div>
        @endif

        @if(session('error'))
    <div class="alert alert-danger m-b-20">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger m-b-20">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <div class="row">
            <div class="col-md-8">
                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-b-30">
                    <div class="d-flex justify-content-between align-items-center p-b-20">
                        <h4 class="mtext-105 cl2">Shipping Address</h4>
                        <a href="{{ route('shipping.addresses.create') }}" class="btn btn-sm btn-dark">Add New Address</a>
                    </div>

                   @forelse($addresses as $address)
<div class="bor8 p-3 m-b-15">
    <div class="d-flex justify-content-between align-items-start">

        {{-- Address Info --}}
        <label style="cursor:pointer; width:100%;">
            <input type="radio" name="address_id" value="{{ $address->id }}"
                {{ (session('shipping.address_id', optional($addresses->firstWhere('is_default', true))->id) == $address->id) ? 'checked' : '' }}>

            <strong>{{ $address->full_name }}</strong><br>
            {{ $address->phone }}<br>
            {{ $address->country }}, {{ $address->city }}, {{ $address->district }}<br>
            {{ $address->street }}
        </label>

        {{-- Buttons --}}
        <div class="d-flex gap-2">

            <a href="{{ route('shipping.addresses.edit', $address->id) }}"
               class="btn btn-sm btn-outline-dark">
                Edit
            </a>

            <form action="{{ route('shipping.addresses.destroy', $address->id) }}"
                  method="POST"
                  onsubmit="return confirm('Delete this address?')">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-sm btn-outline-danger">
                    Delete
                </button>
            </form>

        </div>

    </div>
</div>
@empty
<p>No address found.</p>
@endforelse
                </div>

                <div class="bor10 p-lr-40 p-t-30 p-b-40">
                    <h4 class="mtext-105 cl2 p-b-20">Shipping Methods</h4>
                    <div id="shipping-options">
                        <p class="text-muted">Select address first to load shipping methods.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="bor10 p-lr-40 p-t-30 p-b-40">
                    <h4 class="mtext-105 cl2 p-b-20">Order Summary</h4>

                    <div class="flex-w flex-t p-b-13">
                        <div class="size-208"><span class="stext-110 cl2">Subtotal:</span></div>
                        <div class="size-209"><span class="mtext-110 cl2" id="summary-subtotal">{{ number_format($totals['subtotal'], 2) }} SAR</span></div>
                    </div>

                    <div class="flex-w flex-t p-b-13">
                        <div class="size-208"><span class="stext-110 cl2">Discount:</span></div>
                        <div class="size-209"><span class="mtext-110 cl2" id="summary-discount">{{ number_format($totals['discount'], 2) }} SAR</span></div>
                    </div>

                    <div class="flex-w flex-t p-b-13">
                        <div class="size-208"><span class="stext-110 cl2">Shipping:</span></div>
                        <div class="size-209"><span class="mtext-110 cl2" id="summary-shipping">{{ number_format($totals['shipping_fee'], 2) }} SAR</span></div>
                    </div>

                    <div class="flex-w flex-t p-b-13">
                        <div class="size-208"><span class="stext-110 cl2">Tax:</span></div>
                        <div class="size-209"><span class="mtext-110 cl2" id="summary-tax">{{ number_format($totals['tax_total'], 2) }} SAR</span></div>
                    </div>

                    <div class="flex-w flex-t p-b-30">
                        <div class="size-208"><span class="mtext-101 cl2">Total:</span></div>
                        <div class="size-209"><span class="mtext-110 cl2" id="summary-total">{{ number_format($totals['total'], 2) }} SAR</span></div>
                    </div>

                   <form action="{{ route('shipping.place-order') }}" method="POST" id="place-order-form">
    @csrf
    <input type="hidden" name="address_id" id="place-order-address-id">
    <input type="hidden" name="shipping_method_id" id="place-order-shipping-method-id">
    <input type="hidden" name="shipping_rate_id" id="place-order-shipping-rate-id">
    <input type="hidden" name="payment_method" value="moyasar">

    <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
        Proceed to Payment
    </button>
</form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addressInputs = document.querySelectorAll('input[name="address_id"]');
    const shippingOptionsBox = document.getElementById('shipping-options');

    function loadShippingOptions(addressId) {
        fetch("{{ route('shipping.options') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ address_id: addressId })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('place-order-address-id').value = addressId;

            if (!data.success || !data.options.length) {
                shippingOptionsBox.innerHTML = '<p class="text-danger">No shipping methods available for this address.</p>';
                return;
            }

            let html = '';
            data.options.forEach(option => {
                html += `
                    <div class="bor8 p-3 m-b-15">
                        <label class="w-100">
                            <input type="radio" name="shipping_option"
                                   data-method-id="${option.shipping_method_id}"
                                   data-rate-id="${option.rate_id}">
                            <strong>${option.method_name}</strong><br>
                            ${option.price.toFixed(2)} SAR<br>
                            ${option.eta_min_days ?? 0} - ${option.eta_max_days ?? 0} days
                        </label>
                    </div>
                `;
            });

            shippingOptionsBox.innerHTML = html;

            document.querySelectorAll('input[name="shipping_option"]').forEach(input => {
                input.addEventListener('change', function () {
                    fetch("{{ route('shipping.select-method') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            shipping_method_id: this.dataset.methodId,
                            shipping_rate_id: this.dataset.rateId
                        })
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            document.getElementById('place-order-shipping-method-id').value = this.dataset.methodId;
                            document.getElementById('place-order-shipping-rate-id').value = this.dataset.rateId;

                            document.getElementById('summary-shipping').innerText = response.totals.shipping_fee.toFixed(2) + ' SAR';
                            document.getElementById('summary-tax').innerText = response.totals.tax_total.toFixed(2) + ' SAR';
                            document.getElementById('summary-total').innerText = response.totals.total.toFixed(2) + ' SAR';
                        }
                    });
                });
            });
        });
    }

    addressInputs.forEach(input => {
        input.addEventListener('change', function () {
            loadShippingOptions(this.value);
        });
    });

    const checkedAddress = document.querySelector('input[name="address_id"]:checked');
    if (checkedAddress) {
        loadShippingOptions(checkedAddress.value);
    }
});


</script>
<script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addressInputs = document.querySelectorAll('input[name="address_id"]');
    const shippingOptionsBox = document.getElementById('shipping-options');
    const placeOrderForm = document.getElementById('place-order-form');

    function applySelectedShipping(input) {
        fetch("{{ route('shipping.select-method') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                shipping_method_id: input.dataset.methodId,
                shipping_rate_id: input.dataset.rateId
            })
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                document.getElementById('place-order-shipping-method-id').value = input.dataset.methodId;
                document.getElementById('place-order-shipping-rate-id').value = input.dataset.rateId;

                document.getElementById('summary-shipping').innerText = response.totals.shipping_fee.toFixed(2) + ' SAR';
                document.getElementById('summary-tax').innerText = response.totals.tax_total.toFixed(2) + ' SAR';
                document.getElementById('summary-total').innerText = response.totals.total.toFixed(2) + ' SAR';
            }
        });
    }

    function loadShippingOptions(addressId) {
        fetch("{{ route('shipping.options') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ address_id: addressId })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('place-order-address-id').value = addressId;

            if (!data.success || !data.options.length) {
                shippingOptionsBox.innerHTML = '<p class="text-danger">No shipping methods available for this address.</p>';
                document.getElementById('place-order-shipping-method-id').value = '';
                document.getElementById('place-order-shipping-rate-id').value = '';
                return;
            }

            let html = '';

            data.options.forEach((option, index) => {
                html += `
                    <div class="bor8 p-3 m-b-15">
                        <label class="w-100" style="cursor:pointer;">
                            <input type="radio"
                                   name="shipping_option"
                                   data-method-id="${option.shipping_method_id}"
                                   data-rate-id="${option.rate_id}"
                                   ${index === 0 ? 'checked' : ''}>
                            <strong>${option.method_name}</strong><br>
                            ${option.price.toFixed(2)} SAR<br>
                            ${option.eta_min_days ?? 0} - ${option.eta_max_days ?? 0} days
                        </label>
                    </div>
                `;
            });

            shippingOptionsBox.innerHTML = html;

            const shippingInputs = document.querySelectorAll('input[name="shipping_option"]');

            shippingInputs.forEach(input => {
                input.addEventListener('change', function () {
                    applySelectedShipping(this);
                });
            });

            if (shippingInputs.length > 0) {
                applySelectedShipping(shippingInputs[0]);
            }
        });
    }

    addressInputs.forEach(input => {
        input.addEventListener('change', function () {
            loadShippingOptions(this.value);
        });
    });

    const checkedAddress = document.querySelector('input[name="address_id"]:checked');
    if (checkedAddress) {
        loadShippingOptions(checkedAddress.value);
    }

    placeOrderForm.addEventListener('submit', function(e) {
        const addressId = document.getElementById('place-order-address-id').value;
        const shippingMethodId = document.getElementById('place-order-shipping-method-id').value;
        const shippingRateId = document.getElementById('place-order-shipping-rate-id').value;

        if (!addressId || !shippingMethodId || !shippingRateId) {
            e.preventDefault();
            alert('Please select address and shipping method first.');
        }
    });
});
</script>
@endsection

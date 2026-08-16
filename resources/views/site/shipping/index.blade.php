@extends('site.app.layout')

@section('title', __('site.checkout'))

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">


        <h3 class="mtext-111 cl2 p-b-16">
            {{ __('site.shipping') }}
        </h3>

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

            <!-- LEFT -->
            <div class="col-md-8">

                <!-- ADDRESS -->
                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-b-30">

                    <div class="d-flex justify-content-between align-items-center p-b-20">

                        <h4 class="mtext-105 cl2">
                            {{ __('site.shipping_address') }}
                        </h4>

                        <a href="{{ route('shipping.addresses.create') }}"
                           class="btn btn-sm btn-dark">
                            {{ __('site.add_new_address') }}
                        </a>
                    </div>

                    @forelse($addresses as $address)

                        <div class="bor8 p-3 m-b-15">
                            <div class="d-flex justify-content-between align-items-start">

                                <label style="cursor:pointer; width:100%;">
                                    <input type="radio"
                                           name="address_id"
                                           value="{{ $address->id }}"
                                           {{ (session('shipping.address_id', optional($addresses->firstWhere('is_default', true))->id) == $address->id) ? 'checked' : '' }}>

                                    <strong>{{ $address->full_name }}</strong><br>
                                    {{ $address->phone }}<br>
                                    {{ $address->country }}, {{ $address->city }}, {{ $address->district }}<br>
                                    {{ $address->street }}
                                </label>

                                <div class="d-flex gap-2">

                                    <a href="{{ route('shipping.addresses.edit', $address->id) }}"
                                       class="btn btn-sm btn-outline-dark">
                                        {{ __('site.edit') }}
                                    </a>

                                    <form action="{{ route('shipping.addresses.destroy', $address->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ __('site.delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            {{ __('site.delete') }}
                                        </button>
                                    </form>

                                </div>

                            </div>
                        </div>

                    @empty
                        <p>{{ __('site.no_address_found') }}</p>
                    @endforelse

                </div>

                <!-- SHIPPING METHODS -->
                <div class="bor10 p-lr-40 p-t-30 p-b-40">

                    <h4 class="mtext-105 cl2 p-b-20">
                        {{ __('site.shipping_methods') }}
                    </h4>

                    <div id="shipping-options">
                        <p class="text-muted">
                            {{ __('site.select_address_first') }}
                        </p>
                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="col-md-4">

                <div class="bor10 p-lr-40 p-t-30 p-b-40">

                    <h4 class="mtext-105 cl2 p-b-20">
                        {{ __('site.order_summary') }}
                    </h4>

                    <div class="flex-w flex-t p-b-13">
                        <div class="size-208">
                            <span class="stext-110 cl2">{{ __('site.subtotal') }}:</span>
                        </div>
                        <div class="size-209">
                            <span class="mtext-110 cl2" id="summary-subtotal">
                                {{ money($totals['subtotal']) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-w flex-t p-b-13">
                        <div class="size-208">
                            <span class="stext-110 cl2">{{ __('site.discount') }}:</span>
                        </div>
                        <div class="size-209">
                            <span class="mtext-110 cl2" id="summary-discount">
                                {{ money($totals['discount']) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-w flex-t p-b-13">
                        <div class="size-208">
                            <span class="stext-110 cl2">{{ __('site.shipping') }}:</span>
                        </div>
                        <div class="size-209">
                            <span class="mtext-110 cl2" id="summary-shipping">
                                {{ money($totals['shipping_fee']) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-w flex-t p-b-13">
                        <div class="size-208">
                            <span class="stext-110 cl2">{{ __('site.vat') }}:</span>
                        </div>
                        <div class="size-209">
                            <span class="mtext-110 cl2" id="summary-tax">
                                {{ money($totals['tax_total']) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-w flex-t p-b-30">
                        <div class="size-208">
                            <span class="mtext-101 cl2">{{ __('site.total') }}:</span>
                        </div>
                        <div class="size-209">
                            <span class="mtext-110 cl2" id="summary-total">
                                {{ money($totals['total']) }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('shipping.place-order') }}" method="POST" id="place-order-form">
                        @csrf
                        <input type="hidden" name="address_id" id="place-order-address-id">
                        <input type="hidden" name="shipping_method_id" id="place-order-shipping-method-id">
                        <input type="hidden" name="shipping_rate_id" id="place-order-shipping-rate-id">
                        <input type="hidden" name="payment_method" value="moyasar">

                        <button type="submit"
                                class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                            {{ __('site.proceed_to_payment') }}
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
    const placeOrderForm = document.getElementById('place-order-form');

    const submitButton = placeOrderForm.querySelector('button[type="submit"]');
    let latestSelection = 0;

    // Only the most recent selection may update the form. Without this an
    // older response (the auto-selected first option) could arrive last and
    // leave the form and the session pointing at different methods.
    function applySelectedShipping(input) {
        const selection = ++latestSelection;
        submitButton.disabled = true;

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
            if (selection !== latestSelection) {
                return;
            }

            if (response.success) {
                document.getElementById('place-order-shipping-method-id').value = input.dataset.methodId;
                document.getElementById('place-order-shipping-rate-id').value = input.dataset.rateId;

                document.getElementById('summary-shipping').innerText = response.totals.shipping_fee.toFixed(2) + ' ' + @json(__('SAR'));
                document.getElementById('summary-tax').innerText = response.totals.tax_total.toFixed(2) + ' ' + @json(__('SAR'));
                document.getElementById('summary-total').innerText = response.totals.total.toFixed(2) + ' ' + @json(__('SAR'));
            }
        })
        .finally(() => {
            if (selection === latestSelection) {
                submitButton.disabled = false;
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
                shippingOptionsBox.innerHTML = '<p class="text-danger">{{ __('site.no_shipping_methods') }}</p>';
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
                            ${option.price.toFixed(2)} ${@json(__('SAR'))}<br>
                            ${option.eta_min_days ?? 0} - ${option.eta_max_days ?? 0} {{ __('site.days') }}
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
            alert('{{ __('site.select_address_and_shipping') }}');
        }

    });

});
</script>

@endsection

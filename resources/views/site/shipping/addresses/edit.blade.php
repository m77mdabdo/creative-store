@extends('site.app.layout')

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-shipping-addresses-edit.css') }}">

<section class="address-page">
    <div class="container">

        <div class="address-card">

            <h3 class="address-title">{{ __('site.edit_address') }}</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('shipping.addresses.update', $address) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-12 m-b-20">
                        <label class="address-label">{{ __('site.search_address') }}</label>

                        <input
                            type="text"
                            id="autocomplete"
                            class="form-control address-input"
                            placeholder="{{ __('site.search_address_placeholder') }}"
                            value="{{ trim(($address->street ?? '') . ' ' . ($address->district ?? '') . ' ' . ($address->city ?? '') . ' ' . ($address->country ?? '')) }}"
                        >
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               name="label"
                               class="form-control address-input"
                               placeholder="{{ __('site.label') }}"
                               value="{{ old('label', $address->label) }}">
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               name="full_name"
                               class="form-control address-input"
                               placeholder="{{ __('site.full_name') }}"
                               value="{{ old('full_name', $address->full_name) }}"
                               required>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               name="phone"
                               class="form-control address-input"
                               placeholder="{{ __('site.phone') }}"
                               value="{{ old('phone', $address->phone) }}"
                               required>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               name="country"
                               id="country"
                               class="form-control address-input"
                               placeholder="{{ __('site.country') }}"
                               value="{{ old('country', $address->country) }}"
                               required>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               name="city"
                               id="city"
                               class="form-control address-input"
                               placeholder="{{ __('site.city') }}"
                               value="{{ old('city', $address->city) }}"
                               required>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               name="district"
                               id="district"
                               class="form-control address-input"
                               placeholder="{{ __('site.district') }}"
                               value="{{ old('district', $address->district) }}">
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               name="street"
                               id="street"
                               class="form-control address-input"
                               placeholder="{{ __('site.street') }}"
                               value="{{ old('street', $address->street) }}">
                    </div>

                    <div class="col-md-3 m-b-20">
                        <input type="text"
                               name="building_no"
                               id="building_no"
                               class="form-control address-input"
                               placeholder="{{ __('site.building_no') }}"
                               value="{{ old('building_no', $address->building_no) }}">
                    </div>

                    <div class="col-md-3 m-b-20">
                        <input type="text"
                               name="apartment_no"
                               class="form-control address-input"
                               placeholder="{{ __('site.apartment_no') }}"
                               value="{{ old('apartment_no', $address->apartment_no) }}">
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               name="postal_code"
                               id="postal_code"
                               class="form-control address-input"
                               placeholder="{{ __('site.postal_code') }}"
                               value="{{ old('postal_code', $address->postal_code) }}">
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               id="lat_display"
                               class="form-control address-input"
                               placeholder="{{ __('site.latitude') }}"
                               value="{{ old('lat', $address->lat) }}"
                               readonly>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text"
                               id="lng_display"
                               class="form-control address-input"
                               placeholder="{{ __('site.longitude') }}"
                               value="{{ old('lng', $address->lng) }}"
                               readonly>
                    </div>

                    <input type="hidden" name="lat" id="lat" value="{{ old('lat', $address->lat) }}">
                    <input type="hidden" name="lng" id="lng" value="{{ old('lng', $address->lng) }}">

                    <div class="col-md-12 m-b-20">
                        <textarea
                            name="notes"
                            class="form-control address-textarea"
                            rows="4"
                            placeholder="{{ __('site.notes') }}">{{ old('notes', $address->notes) }}</textarea>
                    </div>

                    <div class="col-md-12 m-b-20">
                        <label>
                            <input
                                type="checkbox"
                                name="is_default"
                                value="1"
                                {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                            >
                            {{ __('site.set_default_address') }}
                        </label>
                    </div>

                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-dark submit-btn">
                            {{ __('site.update_address') }}
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</section>

<script>
let autocomplete;

function initGoogleAddressAutocomplete() {

    const input = document.getElementById('autocomplete');

    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');

    const latDisplay = document.getElementById('lat_display');
    const lngDisplay = document.getElementById('lng_display');

    latDisplay.value = latInput.value;
    lngDisplay.value = lngInput.value;

    autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['address'],
        componentRestrictions: { country: ['sa'] },
        fields: ['address_components', 'geometry']
    });

    autocomplete.addListener('place_changed', function () {

        const place = autocomplete.getPlace();

        if (!place.geometry) {
            return;
        }

        let streetNumber = '';
        let route = '';
        let city = '';
        let district = '';
        let country = '';
        let postalCode = '';

        place.address_components.forEach(component => {

            const types = component.types;

            if (types.includes('street_number')) {
                streetNumber = component.long_name;
            }

            if (types.includes('route')) {
                route = component.long_name;
            }

            if (types.includes('locality')) {
                city = component.long_name;
            }

            if (
                types.includes('sublocality') ||
                types.includes('sublocality_level_1') ||
                types.includes('neighborhood')
            ) {
                district = component.long_name;
            }

            if (types.includes('country')) {
                country = component.long_name;
            }

            if (types.includes('postal_code')) {
                postalCode = component.long_name;
            }

        });

        document.getElementById('street').value = route;
        document.getElementById('building_no').value = streetNumber;
        document.getElementById('city').value = city;
        document.getElementById('district').value = district;
        document.getElementById('country').value = country;
        document.getElementById('postal_code').value = postalCode;

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        latInput.value = lat;
        lngInput.value = lng;

        latDisplay.value = lat;
        lngDisplay.value = lng;

    });

}

window.initGoogleAddressAutocomplete = initGoogleAddressAutocomplete;
</script>

<script async
src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initGoogleAddressAutocomplete">
</script>

@endsection

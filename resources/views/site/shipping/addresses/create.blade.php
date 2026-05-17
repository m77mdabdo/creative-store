@extends('site.app.layout')

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-shipping-addresses-create.css') }}">

<section class="address-page">
    <div class="container">

        <div class="address-card">

            <h3 class="address-title">
                {{ __('site.add_address') }}
            </h3>

            @if ($errors->any())
                <div class="alert alert-danger m-b-20">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('shipping.addresses.store') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-6 m-b-20">
                        <input type="text" name="label" class="form-control address-input"
                               placeholder="{{ __('site.label') }}"
                               value="{{ old('label') }}">
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text" name="full_name" class="form-control address-input"
                               placeholder="{{ __('site.full_name') }}"
                               value="{{ old('full_name') }}" required>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text" name="phone" class="form-control address-input"
                               placeholder="{{ __('site.phone') }}"
                               value="{{ old('phone') }}" required>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text" name="country" id="country"
                               class="form-control address-input"
                               placeholder="{{ __('site.country') }}"
                               value="{{ old('country', __('site.default_country')) }}" required>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text" name="city" id="city"
                               class="form-control address-input"
                               placeholder="{{ __('site.city') }}"
                               value="{{ old('city') }}" required>
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text" name="district" id="district"
                               class="form-control address-input"
                               placeholder="{{ __('site.district') }}"
                               value="{{ old('district') }}">
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text" name="street" id="street"
                               class="form-control address-input"
                               placeholder="{{ __('site.street') }}"
                               value="{{ old('street') }}">
                    </div>

                    <div class="col-md-3 m-b-20">
                        <input type="text" name="building_no"
                               class="form-control address-input"
                               placeholder="{{ __('site.building_no') }}"
                               value="{{ old('building_no') }}">
                    </div>

                    <div class="col-md-3 m-b-20">
                        <input type="text" name="apartment_no"
                               class="form-control address-input"
                               placeholder="{{ __('site.apartment_no') }}"
                               value="{{ old('apartment_no') }}">
                    </div>

                    <div class="col-md-6 m-b-20">
                        <input type="text" name="postal_code" id="postal_code"
                               class="form-control address-input"
                               placeholder="{{ __('site.postal_code') }}"
                               value="{{ old('postal_code') }}">
                    </div>

                    <div class="col-md-12 m-b-25">
                        <div class="border rounded p-3 map-box">

                            <div class="d-flex flex-wrap justify-content-between align-items-center m-b-15">
                                <strong>{{ __('site.location') }}</strong>

                                <div class="location-actions d-flex gap-2 flex-wrap">
                                    <button type="button" id="use-current-location"
                                            class="btn btn-dark btn-sm">
                                        {{ __('site.use_my_location') }}
                                    </button>
                                </div>
                            </div>

                            <div class="m-b-15">
                                <input type="text" id="location-search"
                                       class="form-control address-input"
                                       placeholder="{{ __('site.search_location') }}">
                            </div>

                            <div id="location-status" class="small text-muted m-b-15">
                                {{ __('site.location_help') }}
                            </div>

                            <div id="map" style="height: 420px;"></div>

                            <div class="row m-t-15">
                                <div class="col-md-6 m-b-15">
                                    <label>{{ __('site.latitude') }}</label>
                                    <input type="text" id="lat_display" class="form-control" readonly>
                                </div>

                                <div class="col-md-6 m-b-15">
                                    <label>{{ __('site.longitude') }}</label>
                                    <input type="text" id="lng_display" class="form-control" readonly>
                                </div>
                            </div>

                            <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                            <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
                        </div>
                    </div>

                    <div class="col-md-12 m-b-20">
                        <textarea name="notes"
                                  class="form-control address-textarea"
                                  rows="4"
                                  placeholder="{{ __('site.notes') }}">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-md-12 m-b-25">
                        <label>
                            <input type="checkbox" name="is_default" value="1"
                                   {{ old('is_default') ? 'checked' : '' }}>
                            {{ __('site.set_default_address') }}
                        </label>
                    </div>

                    <div class="col-md-12 text-center">
                        <button type="submit"
                                class="submit-btn flex-c-m stext-101 cl0 bg3 bor14 hov-btn3 trans-04">
                            {{ __('site.save_address') }}
                        </button>
                    </div>

                </div>
            </form>

        </div>

    </div>
</section>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const latDisplay = document.getElementById('lat_display');
    const lngDisplay = document.getElementById('lng_display');
    const statusBox = document.getElementById('location-status');
    const searchInput = document.getElementById('location-search');

    const cityInput = document.getElementById('city');
    const districtInput = document.getElementById('district');
    const streetInput = document.getElementById('street');
    const countryInput = document.getElementById('country');
    const postalCodeInput = document.getElementById('postal_code');

    const defaultLat = parseFloat(latInput.value || 24.7136);
    const defaultLng = parseFloat(lngInput.value || 46.6753);

    const map = L.map('map').setView([defaultLat, defaultLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([defaultLat, defaultLng], {
        draggable: true
    }).addTo(map);

    function updateLatLng(lat, lng) {
        latInput.value = lat;
        lngInput.value = lng;
        latDisplay.value = lat;
        lngDisplay.value = lng;
    }

    async function reverseGeocode(lat, lng) {
        statusBox.innerHTML = @json(__('site.fetching_address_details'));

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
            const data = await response.json();

            if (data && data.address) {
                const address = data.address;

                countryInput.value = address.country || countryInput.value || @json(__('site.default_country'));
                cityInput.value = address.city || address.town || address.state_district || address.state || cityInput.value;
                districtInput.value = address.suburb || address.neighbourhood || address.city_district || districtInput.value;
                streetInput.value = address.road || streetInput.value;
                postalCodeInput.value = address.postcode || postalCodeInput.value;

                statusBox.innerHTML = '<span class="text-success">' + @json(__('site.location_selected_success')) + '</span>';
            } else {
                statusBox.innerHTML = '<span class="text-warning">' + @json(__('site.location_selected_no_address')) + '</span>';
            }
        } catch (error) {
            statusBox.innerHTML = '<span class="text-danger">' + @json(__('site.location_load_failed')) + '</span>';
        }
    }

    async function searchLocation(query) {
        if (!query) return;

        statusBox.innerHTML = @json(__('site.searching_for_location'));

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}`);
            const results = await response.json();

            if (results.length > 0) {
                const place = results[0];
                const lat = parseFloat(place.lat);
                const lng = parseFloat(place.lon);

                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                updateLatLng(lat, lng);
                await reverseGeocode(lat, lng);
            } else {
                statusBox.innerHTML = '<span class="text-danger">' + @json(__('site.no_matching_location')) + '</span>';
            }
        } catch (error) {
            statusBox.innerHTML = '<span class="text-danger">' + @json(__('site.location_search_failed')) + '</span>';
        }
    }

    marker.on('dragend', async function () {
        const position = marker.getLatLng();
        updateLatLng(position.lat, position.lng);
        await reverseGeocode(position.lat, position.lng);
    });

    map.on('click', async function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        marker.setLatLng([lat, lng]);
        updateLatLng(lat, lng);
        await reverseGeocode(lat, lng);
    });

    document.getElementById('use-current-location').addEventListener('click', function () {
        if (!navigator.geolocation) {
            statusBox.innerHTML = '<span class="text-danger">' + @json(__('site.geolocation_not_supported')) + '</span>';
            return;
        }

        statusBox.innerHTML = @json(__('site.detecting_current_location'));

        navigator.geolocation.getCurrentPosition(
            async function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);
                updateLatLng(lat, lng);
                await reverseGeocode(lat, lng);
            },
            function (error) {
                let message = @json(__('site.unable_get_current_location'));

                if (error.code === error.PERMISSION_DENIED) {
                    message = @json(__('site.location_permission_denied'));
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    message = @json(__('site.location_information_unavailable'));
                } else if (error.code === error.TIMEOUT) {
                    message = @json(__('site.location_request_timeout'));
                }

                statusBox.innerHTML = `<span class="text-danger">${message}</span>`;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchLocation(this.value);
        }
    });

    updateLatLng(defaultLat, defaultLng);
    reverseGeocode(defaultLat, defaultLng);

    setTimeout(function () {
        map.invalidateSize();
    }, 300);
});
</script>

@endsection

@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">
        <h3 class="mtext-111 cl2 m-b-30">Add Address</h3>

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
                    <input type="text" name="label" class="form-control" placeholder="Label" value="{{ old('label') }}">
                </div>

                <div class="col-md-6 m-b-20">
                    <input type="text" name="full_name" class="form-control" placeholder="Full Name" value="{{ old('full_name') }}" required>
                </div>

                <div class="col-md-6 m-b-20">
                    <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ old('phone') }}" required>
                </div>

                <div class="col-md-6 m-b-20">
                    <input type="text" name="country" id="country" class="form-control" placeholder="Country" value="{{ old('country', 'Saudi Arabia') }}" required>
                </div>

                <div class="col-md-6 m-b-20">
                    <input type="text" name="city" id="city" class="form-control" placeholder="City" value="{{ old('city') }}" required>
                </div>

                <div class="col-md-6 m-b-20">
                    <input type="text" name="district" id="district" class="form-control" placeholder="District" value="{{ old('district') }}">
                </div>

                <div class="col-md-6 m-b-20">
                    <input type="text" name="street" id="street" class="form-control" placeholder="Street" value="{{ old('street') }}">
                </div>

                <div class="col-md-3 m-b-20">
                    <input type="text" name="building_no" class="form-control" placeholder="Building No" value="{{ old('building_no') }}">
                </div>

                <div class="col-md-3 m-b-20">
                    <input type="text" name="apartment_no" class="form-control" placeholder="Apartment No" value="{{ old('apartment_no') }}">
                </div>

                <div class="col-md-6 m-b-20">
                    <input type="text" name="postal_code" id="postal_code" class="form-control" placeholder="Postal Code" value="{{ old('postal_code') }}">
                </div>

                <div class="col-md-12 m-b-25">
                    <div class="border rounded p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center m-b-15">
                            <strong class="d-block m-b-10">Location</strong>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" id="use-current-location" class="btn btn-dark btn-sm">
                                    Use My Current Location
                                </button>
                            </div>
                        </div>

                        <div class="m-b-15">
                            <input type="text" id="location-search" class="form-control" placeholder="Search location, area, or street">
                        </div>

                        <div id="location-status" class="small text-muted m-b-15">
                            Search for a place, or use your current location, then adjust the pin on the map.
                        </div>

                        <div id="map" style="height: 420px; border-radius: 12px; overflow: hidden;"></div>

                        <div class="row m-t-15">
                            <div class="col-md-6 m-b-15">
                                <label class="form-label">Latitude</label>
                                <input type="text" id="lat_display" class="form-control" readonly>
                            </div>
                            <div class="col-md-6 m-b-15">
                                <label class="form-label">Longitude</label>
                                <input type="text" id="lng_display" class="form-control" readonly>
                            </div>
                        </div>

                        <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                        <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
                    </div>
                </div>

                <div class="col-md-12 m-b-20">
                    <textarea name="notes" class="form-control" rows="4" placeholder="Notes">{{ old('notes') }}</textarea>
                </div>

                <div class="col-md-12 m-b-20">
                    <label>
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        Set as default address
                    </label>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-dark">Save Address</button>
                </div>
            </div>
        </form>
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
        statusBox.innerHTML = 'Fetching address details...';

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
            const data = await response.json();

            if (data && data.address) {
                const address = data.address;

                countryInput.value = address.country || countryInput.value || 'Saudi Arabia';
                cityInput.value = address.city || address.town || address.state_district || address.state || cityInput.value;
                districtInput.value = address.suburb || address.neighbourhood || address.city_district || districtInput.value;
                streetInput.value = address.road || streetInput.value;
                postalCodeInput.value = address.postcode || postalCodeInput.value;

                statusBox.innerHTML = '<span class="text-success">Location selected successfully.</span>';
            } else {
                statusBox.innerHTML = '<span class="text-warning">Location selected, but no address details were found.</span>';
            }
        } catch (error) {
            statusBox.innerHTML = '<span class="text-danger">Could not load address details from the map.</span>';
        }
    }

    async function searchLocation(query) {
        if (!query) return;

        statusBox.innerHTML = 'Searching for location...';

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
                statusBox.innerHTML = '<span class="text-danger">No matching location found.</span>';
            }
        } catch (error) {
            statusBox.innerHTML = '<span class="text-danger">Search failed. Please try again.</span>';
        }
    }

    marker.on('dragend', async function (e) {
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
            statusBox.innerHTML = '<span class="text-danger">Geolocation is not supported by your browser.</span>';
            return;
        }

        statusBox.innerHTML = 'Detecting your current location...';

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
                let message = 'Unable to get your current location.';

                if (error.code === error.PERMISSION_DENIED) {
                    message = 'Location permission was denied.';
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    message = 'Location information is unavailable.';
                } else if (error.code === error.TIMEOUT) {
                    message = 'Location request timed out.';
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

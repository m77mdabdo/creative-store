@extends('dashboard.app.layout')

@section('title', __('Edit Address'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ __('Edit Address') }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('dashboard.addresses.update', $address) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>{{ __('Label') }}</label>
                                <input type="text" name="label" class="form-control" value="{{ old('label', $address->label) }}">
                                @error('label') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>{{ __('Full Name') }}</label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $address->full_name) }}" required>
                                @error('full_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>{{ __('Phone') }}</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $address->phone) }}" required>
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>{{ __('Country') }}</label>
                                <input type="text" name="country" class="form-control" value="{{ old('country', $address->country) }}" required>
                                @error('country') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>{{ __('City') }}</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $address->city) }}" required>
                                @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>{{ __('District') }}</label>
                                <input type="text" name="district" class="form-control" value="{{ old('district', $address->district) }}">
                                @error('district') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>{{ __('Street') }}</label>
                                <input type="text" name="street" class="form-control" value="{{ old('street', $address->street) }}" required>
                                @error('street') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>{{ __('Building No') }}</label>
                                <input type="text" name="building_no" class="form-control" value="{{ old('building_no', $address->building_no) }}">
                                @error('building_no') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>{{ __('Apartment No') }}</label>
                                <input type="text" name="apartment_no" class="form-control" value="{{ old('apartment_no', $address->apartment_no) }}">
                                @error('apartment_no') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>{{ __('Postal Code') }}</label>
                                <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $address->postal_code) }}">
                                @error('postal_code') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>{{ __('Latitude') }}</label>
                                <input type="text" name="lat" class="form-control" value="{{ old('lat', $address->lat) }}">
                                @error('lat') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>{{ __('Longitude') }}</label>
                                <input type="text" name="lng" class="form-control" value="{{ old('lng', $address->lng) }}">
                                @error('lng') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>{{ __('Notes') }}</label>
                                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $address->notes) }}</textarea>
                                @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="is_default" value="1" class="form-check-input" id="is_default"
                                        @checked(old('is_default', $address->is_default))>
                                    <label for="is_default" class="form-check-label">{{ __('Default Address') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary">{{ __('Save') }}</button>
                        <a href="{{ route('dashboard.addresses.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

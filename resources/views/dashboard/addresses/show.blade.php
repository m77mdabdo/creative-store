@extends('dashboard.app.layout')

@section('title', __('Address Details'))

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>{{ __('Address Details') }}</h1>
            <a href="{{ route('dashboard.addresses.edit', $address) }}" class="btn btn-primary">{{ __('Edit') }}</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="220">{{ __('Label') }}</th>
                            <td>{{ $address->label ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Full Name') }}</th>
                            <td>{{ $address->full_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Phone') }}</th>
                            <td>{{ $address->phone }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('User') }}</th>
                            <td>
                                @if($address->user)
                                    {{ $address->user->name }} <br>
                                    <small>{{ $address->user->email }}</small>
                                @else
                                    <span class="badge badge-secondary">{{ __('Guest') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Country') }}</th>
                            <td>{{ $address->country }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('City') }}</th>
                            <td>{{ $address->city }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('District') }}</th>
                            <td>{{ $address->district ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Street') }}</th>
                            <td>{{ $address->street }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Building No') }}</th>
                            <td>{{ $address->building_no ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Apartment No') }}</th>
                            <td>{{ $address->apartment_no ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Postal Code') }}</th>
                            <td>{{ $address->postal_code ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Latitude') }}</th>
                            <td>{{ $address->lat ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Longitude') }}</th>
                            <td>{{ $address->lng ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Notes') }}</th>
                            <td>{{ $address->notes ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Default') }}</th>
                            <td>
                                @if($address->is_default)
                                    <span class="badge badge-success">{{ __('Default') }}</span>
                                @else
                                    <span class="badge badge-light">{{ __('No') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}</th>
                            <td>{{ $address->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

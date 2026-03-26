@extends('dashboard.app.layout')

@section('title', 'Address Details')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Address Details</h1>
            <a href="{{ route('dashboard.addresses.edit', $address) }}" class="btn btn-primary">Edit</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="220">Label</th>
                            <td>{{ $address->label ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td>{{ $address->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $address->phone }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>
                                @if($address->user)
                                    {{ $address->user->name }} <br>
                                    <small>{{ $address->user->email }}</small>
                                @else
                                    <span class="badge badge-secondary">Guest</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Country</th>
                            <td>{{ $address->country }}</td>
                        </tr>
                        <tr>
                            <th>City</th>
                            <td>{{ $address->city }}</td>
                        </tr>
                        <tr>
                            <th>District</th>
                            <td>{{ $address->district ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Street</th>
                            <td>{{ $address->street }}</td>
                        </tr>
                        <tr>
                            <th>Building No</th>
                            <td>{{ $address->building_no ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Apartment No</th>
                            <td>{{ $address->apartment_no ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Postal Code</th>
                            <td>{{ $address->postal_code ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Latitude</th>
                            <td>{{ $address->lat ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Longitude</th>
                            <td>{{ $address->lng ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td>{{ $address->notes ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Default</th>
                            <td>
                                @if($address->is_default)
                                    <span class="badge badge-success">Default</span>
                                @else
                                    <span class="badge badge-light">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $address->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

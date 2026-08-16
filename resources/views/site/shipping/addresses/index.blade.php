@extends('site.app.layout')

@section('title', __('site.my_addresses'))

@section('content')

<link rel="stylesheet" href="{{ versioned_asset('css/site/site-shipping-addresses-index.css') }}">

<section class="address-page">
    <div class="container">

        <!-- HEADER -->
        <div class="address-header d-flex justify-content-between align-items-center flex-wrap">

            <h3 class="address-title">
                {{ __('site.my_addresses') }}
            </h3>

            <a href="{{ route('shipping.addresses.create') }}"
               class="btn btn-dark add-btn">
                {{ __('site.add_address') }}
            </a>

        </div>

        <!-- SUCCESS -->
        @if(session('success'))
            <div class="alert alert-success m-b-20">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">

            @forelse($addresses as $address)

                <div class="col-md-6 m-b-20">
                    <div class="address-card">

                        <div class="address-name">
                            {{ $address->full_name }}
                        </div>

                        <div class="address-info">
                            {{ $address->phone }}
                        </div>

                        <div class="address-info">
                            {{ $address->country }} - {{ $address->city }} - {{ $address->district }}
                        </div>

                        <div class="address-info">
                            {{ $address->street }}
                        </div>

                        @if($address->is_default)
                            <span class="address-badge">
                                {{ __('site.default') }}
                            </span>
                        @endif

                        <!-- ACTIONS -->
                        <div class="address-actions">

                            <a href="{{ route('shipping.addresses.edit', $address) }}"
                               class="btn btn-sm btn-primary">
                                {{ __('site.edit') }}
                            </a>

                            <form method="POST" action="{{ route('shipping.addresses.destroy', $address) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">
                                    {{ __('site.delete') }}
                                </button>
                            </form>

                            @if(!$address->is_default)
                                <form method="POST" action="{{ route('shipping.addresses.default', $address) }}">
                                    @csrf

                                    <button type="submit" class="btn btn-sm btn-secondary">
                                        {{ __('site.set_default') }}
                                    </button>
                                </form>
                            @endif

                        </div>

                    </div>
                </div>

            @empty

                <div class="col-12">
                    <div class="empty-state">
                        <p>{{ __('site.no_addresses') }}</p>
                    </div>
                </div>

            @endforelse

        </div>

    </div>
</section>

@endsection

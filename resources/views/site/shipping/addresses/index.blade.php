@extends('site.app.layout')

@section('content')

<style>
.address-page {
    padding-top: 70px;
    padding-bottom: 90px;
    background: #fff;
}

.address-header {
    margin-bottom: 30px;
}

.address-title {
    font-size: 30px;
    font-weight: 800;
    color: #222;
}

.add-btn {
    border-radius: 999px;
    padding: 10px 22px;
}

.address-card {
    border: 1px solid #eee;
    border-radius: 22px;
    padding: 22px;
    background: #fff;
    transition: all .3s ease;
    height: 100%;
}

.address-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,.08);
}

.address-name {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 5px;
}

.address-info {
    font-size: 14px;
    color: #666;
    margin-bottom: 4px;
}

.address-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    background: #28a745;
    color: #fff;
    margin-top: 8px;
}

.address-actions {
    margin-top: 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.address-actions .btn {
    border-radius: 999px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #777;
}

html[dir="rtl"] .address-header {
    flex-direction: row-reverse;
}

html[dir="rtl"] .address-card {
    text-align: right;
}

html[dir="rtl"] .address-actions {
    flex-direction: row-reverse;
}

</style>

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

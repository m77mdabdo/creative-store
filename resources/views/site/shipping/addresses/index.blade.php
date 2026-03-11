@extends('site.app.layout')

@section('content')
<section class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center m-b-30">
            <h3 class="mtext-111 cl2">My Addresses</h3>
            <a href="{{ route('shipping.addresses.create') }}" class="btn btn-dark">Add Address</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success m-b-20">{{ session('success') }}</div>
        @endif

        <div class="row">
            @forelse($addresses as $address)
                <div class="col-md-6 m-b-20">
                    <div class="bor10 p-4">
                        <h5>{{ $address->full_name }}</h5>
                        <p>{{ $address->phone }}</p>
                        <p>{{ $address->country }} - {{ $address->city }} - {{ $address->district }}</p>
                        <p>{{ $address->street }}</p>

                        @if($address->is_default)
                            <span class="badge bg-success">Default</span>
                        @endif

                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('shipping.addresses.edit', $address) }}" class="btn btn-sm btn-primary">Edit</a>

                            <form method="POST" action="{{ route('shipping.addresses.destroy', $address) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>

                            @if(!$address->is_default)
                                <form method="POST" action="{{ route('shipping.addresses.default', $address) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary">Set Default</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p>No addresses yet.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection

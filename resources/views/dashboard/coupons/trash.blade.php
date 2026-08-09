@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between">
            <h1>{{ __('Trashed Coupons') }}</h1>
            <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Scope') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Deleted At') }}</th>
                                <th style="width:220px;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ $coupon->product_id ? __('Product') : __('Cart') }}</td>
                                    <td>{{ status_label($coupon->type) }}</td>
                                    <td>{{ $coupon->deleted_at?->translatedFormat('d M Y H:i') }}</td>
                                    <td>
                                        @if($canEdit)
                                            <form action="{{ route('dashboard.coupons.restore', $coupon->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">{{ __('Restore') }}</button>
                                            </form>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('dashboard.coupons.force-delete', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Delete this coupon permanently?')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">{{ __('Delete Permanently') }}</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">{{ __('Trash is empty.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($coupons->hasPages())
                    <div class="card-footer">
                        {{ $coupons->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection 

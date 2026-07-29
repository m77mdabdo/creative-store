@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Coupons') }}</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    @if($canCreate)
                        <a href="{{ route('dashboard.coupons.create') }}" class="btn btn-primary">{{ __('Create Coupon') }}</a>
                    @endif

                    @if($canViewTrash)
                        <a href="{{ route('dashboard.coupons.trash') }}" class="btn btn-outline-secondary">{{ __('Trash') }}</a>
                    @endif
                </div>
            </div>
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

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.coupons.index') }}">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control" value="{{ $filters['search'] }}" placeholder="{{ __('Code / product') }}">
                            </div>

                            <div class="col-md-3 form-group">
                                <label>{{ __('Scope') }}</label>
                                <select name="scope" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="cart" {{ $filters['scope'] === 'cart' ? 'selected' : '' }}>{{ __('Cart Coupon') }}</option>
                                    <option value="product" {{ $filters['scope'] === 'product' ? 'selected' : '' }}>{{ __('Product Coupon') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label>{{ __('Type') }}</label>
                                <select name="type" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="fixed" {{ $filters['type'] === 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                                    <option value="percent" {{ $filters['type'] === 'percent' ? 'selected' : '' }}>{{ __('Percent') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="inactive" {{ $filters['status'] === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">{{ __('Filter') }}</button>
                                <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($canEdit)
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('dashboard.coupons.bulk-action') }}" method="POST">
                            @csrf

                            <div class="d-flex mb-3">
                                <select name="action" class="form-control mr-2" style="max-width:220px;" required>
                                    <option value="">{{ __('Bulk Action') }}</option>
                                    <option value="activate">{{ __('Activate') }}</option>
                                    <option value="deactivate">{{ __('Deactivate') }}</option>
                                    <option value="delete">{{ __('Move to Trash') }}</option>
                                </select>

                                <button type="submit" class="btn btn-dark">{{ __('Apply') }}</button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;">
                                                <input type="checkbox" onclick="document.querySelectorAll('.coupon-check').forEach(el => el.checked = this.checked)">
                                            </th>
                                            <th>{{ __('Code') }}</th>
                                            <th>{{ __('Scope') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Value') }}</th>
                                            <th>{{ __('Usage') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Starts') }}</th>
                                            <th>{{ __('Expires') }}</th>
                                            <th style="width:220px;">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($coupons as $coupon)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="ids[]" value="{{ $coupon->id }}" class="coupon-check">
                                                </td>
                                                <td><strong>{{ $coupon->code }}</strong></td>
                                                <td>
                                                    @if($coupon->product_id)
                                                        Product:
                                                        {{ $coupon->product->name ?? ('Product #' . $coupon->product_id) }}
                                                    @else
                                                        Cart
                                                    @endif
                                                </td>
                                                <td>{{ status_label($coupon->type) }}</td>
                                                <td>{{ number_format((float) $coupon->value, 2) }}</td>
                                                <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $coupon->is_active ? 'success' : 'secondary' }}">
                                                        {{ $coupon->is_active ? __('Active') : __('Inactive') }}
                                                    </span>
                                                </td>
                                                <td>{{ $coupon->starts_at?->format('d M Y H:i') ?? '-' }}</td>
                                                <td>{{ $coupon->expires_at?->format('d M Y H:i') ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('dashboard.coupons.show', $coupon) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>

                                                    @if($canEdit)
                                                        <a href="{{ route('dashboard.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>

                                                        <form action="{{ route('dashboard.coupons.toggle-status', $coupon) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-info">
                                                                {{ $coupon->is_active ? __('Disable') : __('Enable') }}
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($canDelete)
                                                        <form action="{{ route('dashboard.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Move this coupon to trash?')))">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">{{ __('No coupons found.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>

                    @if($coupons->hasPages())
                        <div class="card-footer">
                            {{ $coupons->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </section>
</div>
@endsection 

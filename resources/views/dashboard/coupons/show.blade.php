@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between">
            <h1>{{ __('Coupon Details') }}</h1>
            <div>
                @if($canEdit)
                    <a href="{{ route('dashboard.coupons.edit', $coupon) }}" class="btn btn-warning">{{ __('Edit') }}</a>
                @endif
                <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <p><strong>{{ __('Code:') }}</strong> {{ $coupon->code }}</p>
                    <p><strong>{{ __('Scope:') }}</strong> {{ $coupon->product_id ? __('Product Coupon') : __('Cart Coupon') }}</p>
                    <p><strong>{{ __('Product:') }}</strong> {{ $coupon->product->name ?? '-' }}</p>
                    <p><strong>{{ __('Type:') }}</strong> {{ status_label($coupon->type) }}</p>
                    <p><strong>{{ __('Value:') }}</strong> {{ number_format((float) $coupon->value, 2) }}</p>
                    <p><strong>{{ __('Min Order:') }}</strong> {{ number_format((float) ($coupon->min_order_amount ?? 0), 2) }}</p>
                    <p><strong>{{ __('Max Discount:') }}</strong> {{ number_format((float) ($coupon->max_discount_amount ?? 0), 2) }}</p>
                    <p><strong>{{ __('Usage Limit:') }}</strong> {{ $coupon->usage_limit ?? '∞' }}</p>
                    <p><strong>{{ __('Per User Limit:') }}</strong> {{ $coupon->per_user_limit ?? '∞' }}</p>
                    <p><strong>{{ __('Used Count:') }}</strong> {{ $coupon->used_count }}</p>
                    <p><strong>{{ __('Status:') }}</strong> {{ $coupon->is_active ? __('Active') : __('Inactive') }}</p>
                    <p><strong>{{ __('Starts At:') }}</strong> {{ $coupon->starts_at?->format('d M Y H:i') ?? '-' }}</p>
                    <p><strong>{{ __('Expires At:') }}</strong> {{ $coupon->expires_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Coupon Usage History') }}</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Discount') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupon->usages as $usage)
                                <tr>
                                    <td>{{ $usage->user->name ?? 'User #' . $usage->user_id }}</td>
                                    <td>{{ $usage->order->order_number ?? 'Order #' . $usage->order_id }}</td>
                                    <td>{{ number_format((float) $usage->discount_amount, 2) }}</td>
                                    <td>{{ $usage->created_at?->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ __('No usage history found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 

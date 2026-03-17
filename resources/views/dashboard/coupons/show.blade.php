@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between">
            <h1>Coupon Details</h1>
            <div>
                @if($canEdit)
                    <a href="{{ route('dashboard.coupons.edit', $coupon) }}" class="btn btn-warning">Edit</a>
                @endif
                <a href="{{ route('dashboard.coupons.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <p><strong>Code:</strong> {{ $coupon->code }}</p>
                    <p><strong>Scope:</strong> {{ $coupon->product_id ? 'Product Coupon' : 'Cart Coupon' }}</p>
                    <p><strong>Product:</strong> {{ $coupon->product->name ?? '-' }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($coupon->type) }}</p>
                    <p><strong>Value:</strong> {{ number_format((float) $coupon->value, 2) }}</p>
                    <p><strong>Min Order:</strong> {{ number_format((float) ($coupon->min_order_amount ?? 0), 2) }}</p>
                    <p><strong>Max Discount:</strong> {{ number_format((float) ($coupon->max_discount_amount ?? 0), 2) }}</p>
                    <p><strong>Usage Limit:</strong> {{ $coupon->usage_limit ?? '∞' }}</p>
                    <p><strong>Per User Limit:</strong> {{ $coupon->per_user_limit ?? '∞' }}</p>
                    <p><strong>Used Count:</strong> {{ $coupon->used_count }}</p>
                    <p><strong>Status:</strong> {{ $coupon->is_active ? 'Active' : 'Inactive' }}</p>
                    <p><strong>Starts At:</strong> {{ $coupon->starts_at?->format('d M Y H:i') ?? '-' }}</p>
                    <p><strong>Expires At:</strong> {{ $coupon->expires_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Coupon Usage History</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Order</th>
                                <th>Discount</th>
                                <th>Date</th>
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
                                    <td colspan="4" class="text-center text-muted">No usage history found.</td>
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

@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order Details</h1>
                </div>
                <div class="col-sm-6 text-sm-right">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.home') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.orders.index') }}">Orders</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                    </ol>
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

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">

                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Order Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Order Number</strong>
                                    <div>{{ $order->order_number }}</div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Placed At</strong>
                                    <div>{{ $order->placed_at?->format('d M Y, h:i A') ?? $order->created_at?->format('d M Y, h:i A') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Currency</strong>
                                    <div>{{ $order->currency ?? 'SAR' }}</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Order Status</strong>
                                    <div>
                                        <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Payment Status</strong>
                                    <div>
                                        <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Shipping Status</strong>
                                    <div>
                                        <span class="badge badge-{{ $order->shipping_status === 'delivered' ? 'success' : ($order->shipping_status === 'cancelled' ? 'danger' : 'info') }}">
                                            {{ ucfirst($order->shipping_status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Payment Method</strong>
                                    <div>{{ strtoupper($order->payment_method ?? '-') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Shipping Method</strong>
                                    <div>{{ $order->shippingMethod->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Coupon</strong>
                                    <div>{{ $order->coupon->code ?? '-' }}</div>
                                </div>
                            </div>

                            @if($order->estimated_delivery_from || $order->estimated_delivery_to)
                                <hr>
                                <div>
                                    <strong>Estimated Delivery:</strong>
                                    {{ $order->estimated_delivery_from?->format('d M Y') ?: '-' }}
                                    -
                                    {{ $order->estimated_delivery_to?->format('d M Y') ?: '-' }}
                                </div>
                            @endif

                            @if($order->cancelled_at)
                                <hr>
                                <div class="text-danger">
                                    <strong>Cancelled At:</strong> {{ $order->cancelled_at?->format('d M Y, h:i A') }}
                                </div>
                                @if($order->cancellation_reason)
                                    <div class="text-danger">
                                        <strong>Cancellation Reason:</strong> {{ $order->cancellation_reason }}
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if($canEdit ?? false)
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Update Order Statuses</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('dashboard.orders.update-statuses', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label>Order Status</label>
                                            <select name="status" class="form-control">
                                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label>Payment Status</label>
                                            <select name="payment_status" class="form-control">
                                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label>Shipping Status</label>
                                            <select name="shipping_status" class="form-control">
                                                <option value="pending" {{ $order->shipping_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ $order->shipping_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="shipped" {{ $order->shipping_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ $order->shipping_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="cancelled" {{ $order->shipping_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12 form-group">
                                            <label>Admin Note</label>
                                            <textarea name="note" rows="3" class="form-control" placeholder="Optional note for this status update..."></textarea>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save mr-1"></i> Update Statuses
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Shipment Management</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('dashboard.orders.shipment.upsert', $order) }}" method="POST">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Carrier</label>
                                            <input type="text" name="carrier" class="form-control"
                                                   value="{{ old('carrier', $order->shipment->carrier ?? '') }}"
                                                   placeholder="e.g. Aramex / SMSA / DHL">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label>Tracking Number</label>
                                            <input type="text" name="tracking_number" class="form-control"
                                                   value="{{ old('tracking_number', $order->shipment->tracking_number ?? '') }}"
                                                   placeholder="Tracking number">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label>Shipment Status</label>
                                            <select name="status" class="form-control">
                                                @php $shipmentStatus = old('status', $order->shipment->status ?? $order->shipping_status ?? 'pending'); @endphp
                                                <option value="pending" {{ $shipmentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ $shipmentStatus === 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="shipped" {{ $shipmentStatus === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ $shipmentStatus === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="cancelled" {{ $shipmentStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label>Shipped At</label>
                                            <input type="datetime-local" name="shipped_at" class="form-control"
                                                   value="{{ old('shipped_at', optional($order->shipment?->shipped_at)->format('Y-m-d\TH:i')) }}">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label>Delivered At</label>
                                            <input type="datetime-local" name="delivered_at" class="form-control"
                                                   value="{{ old('delivered_at', optional($order->shipment?->delivered_at)->format('Y-m-d\TH:i')) }}">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label>Current Location</label>
                                            <input type="text" name="current_location" class="form-control"
                                                   value="{{ old('current_location', $order->shipment->current_location ?? '') }}"
                                                   placeholder="Current shipment location">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label>Carrier Status</label>
                                            <input type="text" name="carrier_status" class="form-control"
                                                   value="{{ old('carrier_status', $order->shipment->carrier_status ?? '') }}"
                                                   placeholder="Carrier status text">
                                        </div>

                                        <div class="col-md-12 form-group">
                                            <label>Shipment Notes</label>
                                            <textarea name="notes" rows="3" class="form-control" placeholder="Optional shipment note...">{{ old('notes', $order->shipment->notes ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-truck mr-1"></i> Save Shipment
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($order->status !== 'cancelled')
                            <div class="card card-outline card-danger">
                                <div class="card-header">
                                    <h3 class="card-title">Cancel Order</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('dashboard.orders.cancel', $order) }}" method="POST">
                                        @csrf

                                        <div class="form-group">
                                            <label>Cancellation Reason</label>
                                            <textarea name="reason" rows="3" class="form-control" required placeholder="Write cancellation reason..."></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order? Stock will be restored.')">
                                            <i class="fas fa-times-circle mr-1"></i> Cancel Order
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Order Items</h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Tax</th>
                                        <th>Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->items as $item)
                                        <tr>
                                            <td>
                                                <div><strong>{{ $item->product_name_snapshot }}</strong></div>

                                                @if($item->product)
                                                    <small class="text-muted d-block">Product ID: {{ $item->product->id }}</small>
                                                @endif

                                                @if($item->color)
                                                    <small class="text-muted d-block">Color: {{ $item->color }}</small>
                                                @endif

                                                @if($item->size)
                                                    <small class="text-muted d-block">Size: {{ $item->size }}</small>
                                                @endif

                                                @if(!empty($item->variant_options))
                                                    @foreach($item->variant_options as $option)
                                                        <small class="text-muted d-block">
                                                            {{ $option['name'] ?? 'Option' }}: {{ $option['value'] ?? '-' }}
                                                        </small>
                                                    @endforeach
                                                @endif
                                            </td>

                                            <td>{{ $item->sku_snapshot ?: '-' }}</td>
                                            <td>{{ (int) $item->quantity }}</td>
                                            <td>{{ number_format((float) $item->unit_price, 2) }} {{ $order->currency }}</td>
                                            <td>{{ number_format((float) $item->tax_amount, 2) }} {{ $order->currency }}</td>
                                            <td><strong>{{ number_format((float) $item->line_total, 2) }} {{ $order->currency }}</strong></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No items found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-4 ml-auto">
                                    <table class="table table-sm">
                                        <tr>
                                            <th>Subtotal</th>
                                            <td class="text-right">{{ number_format((float) $order->subtotal, 2) }} {{ $order->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount</th>
                                            <td class="text-right">- {{ number_format((float) $order->discount, 2) }} {{ $order->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipping</th>
                                            <td class="text-right">{{ number_format((float) $order->shipping_fee, 2) }} {{ $order->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>VAT</th>
                                            <td class="text-right">{{ number_format((float) $order->tax_total, 2) }} {{ $order->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total</th>
                                            <td class="text-right"><strong>{{ number_format((float) $order->total, 2) }} {{ $order->currency }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Status Timeline</h3>
                        </div>
                        <div class="card-body">
                            @forelse($order->statusLogs as $log)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div>
                                        <strong>{{ $log->from_status ?: 'N/A' }}</strong>
                                        →
                                        <strong>{{ $log->to_status ?: 'N/A' }}</strong>
                                    </div>
                                    <div class="text-muted small">
                                        By: {{ $log->changer->name ?? 'System' }}
                                        | {{ $log->created_at?->format('d M Y, h:i A') }}
                                    </div>
                                    @if($log->note)
                                        <div class="mt-1">{{ $log->note }}</div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted mb-0">No status logs found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Customer</h3>
                        </div>
                        <div class="card-body">
                            @if($order->user)
                                <div><strong>Name:</strong> {{ $order->user->name }}</div>
                                <div><strong>Email:</strong> {{ $order->user->email }}</div>
                                <div><strong>Phone:</strong> {{ $order->user->phone ?? '-' }}</div>
                                <div><strong>User ID:</strong> {{ $order->user->id }}</div>
                            @else
                                <div><strong>Guest Email:</strong> {{ $order->guest_email ?? '-' }}</div>
                                <div><strong>Guest Phone:</strong> {{ $order->guest_phone ?? '-' }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Shipping Address Snapshot</h3>
                        </div>
                        <div class="card-body">
                            @if($order->addressSnapshot)
                                <div><strong>{{ $order->addressSnapshot->full_name }}</strong></div>
                                <div>{{ $order->addressSnapshot->phone }}</div>
                                <div>{{ $order->addressSnapshot->street }}</div>
                                <div>{{ $order->addressSnapshot->district }}</div>
                                <div>{{ $order->addressSnapshot->city }}, {{ $order->addressSnapshot->country }}</div>

                                @if($order->addressSnapshot->postal_code)
                                    <div>Postal Code: {{ $order->addressSnapshot->postal_code }}</div>
                                @endif

                                @if($order->addressSnapshot->building_no)
                                    <div>Building No: {{ $order->addressSnapshot->building_no }}</div>
                                @endif

                                @if($order->addressSnapshot->apartment_no)
                                    <div>Apartment No: {{ $order->addressSnapshot->apartment_no }}</div>
                                @endif

                                @if($order->addressSnapshot->notes)
                                    <hr>
                                    <div><strong>Notes:</strong> {{ $order->addressSnapshot->notes }}</div>
                                @endif
                            @else
                                <p class="text-muted mb-0">No address snapshot found.</p>
                            @endif
                        </div>
                    </div>

                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">Payments</h3>
                        </div>
                        <div class="card-body">
                            @forelse($order->payments as $payment)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div><strong>Gateway:</strong> {{ strtoupper($payment->gateway) }}</div>
                                    <div><strong>Status:</strong> {{ ucfirst($payment->status) }}</div>
                                    <div><strong>Amount:</strong> {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</div>
                                    <div><strong>Transaction ID:</strong> {{ $payment->transaction_id ?: '-' }}</div>
                                    <div><strong>Paid At:</strong> {{ $payment->paid_at?->format('d M Y, h:i A') ?: '-' }}</div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No payments found.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Shipment</h3>
                        </div>
                        <div class="card-body">
                            @if($order->shipment)
                                <div><strong>Carrier:</strong> {{ $order->shipment->carrier ?: '-' }}</div>
                                <div><strong>Tracking Number:</strong> {{ $order->shipment->tracking_number ?: '-' }}</div>
                                <div><strong>Status:</strong> {{ ucfirst($order->shipment->status ?: '-') }}</div>
                                <div><strong>Location:</strong> {{ $order->shipment->current_location ?: '-' }}</div>
                                <div><strong>Carrier Status:</strong> {{ $order->shipment->carrier_status ?: '-' }}</div>
                                <div><strong>Shipped At:</strong> {{ $order->shipment->shipped_at?->format('d M Y, h:i A') ?: '-' }}</div>
                                <div><strong>Delivered At:</strong> {{ $order->shipment->delivered_at?->format('d M Y, h:i A') ?: '-' }}</div>

                                @if($order->shipment->notes)
                                    <hr>
                                    <div><strong>Notes:</strong> {{ $order->shipment->notes }}</div>
                                @endif
                            @else
                                <p class="text-muted mb-0">No shipment record found.</p>
                            @endif
                        </div>
                    </div>

                    <div class="card card-outline card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Returns / Refunds</h3>
                        </div>
                        <div class="card-body">
                            @forelse($order->returnRequests as $return)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div><strong>Status:</strong> {{ ucfirst($return->status) }}</div>
                                    <div><strong>Reason:</strong> {{ $return->reason ?: '-' }}</div>
                                    <div><strong>Requested At:</strong> {{ $return->requested_at?->format('d M Y, h:i A') ?: '-' }}</div>
                                    <div><strong>Processed At:</strong> {{ $return->processed_at?->format('d M Y, h:i A') ?: '-' }}</div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No returns found.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="text-right mb-3">
                        <a href="{{ route('dashboard.orders.invoice', $order) }}" target="_blank" class="btn btn-outline-dark">
                            <i class="fas fa-file-invoice mr-1"></i> Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

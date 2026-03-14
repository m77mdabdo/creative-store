@extends('dashboard.app.layout')

@section('content')
<div class="content-wrapper" style="background: #f4f6f9;">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="mb-1" style="font-weight:700; color:#1f2937;">Dashboard Overview</h1>
                    <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }} 👋</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge badge-light p-2 shadow-sm" style="font-size: 13px;">
                        <i class="far fa-calendar-alt mr-1"></i>
                        {{ now()->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- Top Stats --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box shadow-sm" style="border-radius:18px; background:linear-gradient(135deg,#111827,#1f2937); color:#fff;">
                        <div class="inner">
                            <h3>{{ number_format($totalSales, 2) }}</h3>
                            <p>Total Sales</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sack-dollar"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box shadow-sm" style="border-radius:18px; background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#fff;">
                        <div class="inner">
                            <h3>{{ $ordersCount }}</h3>
                            <p>Orders Count</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box shadow-sm" style="border-radius:18px; background:linear-gradient(135deg,#059669,#047857); color:#fff;">
                        <div class="inner">
                            <h3>{{ $paidOrdersCount }}</h3>
                            <p>Paid Orders</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-circle-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box shadow-sm" style="border-radius:18px; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff;">
                        <div class="inner">
                            <h3>{{ $pendingOrdersCount }}</h3>
                            <p>Pending Orders</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Secondary Stats --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm border-0" style="border-radius:18px;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted mb-1">Products Count</div>
                                <h3 class="mb-0 font-weight-bold">{{ $productsCount }}</h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:52px;height:52px;background:#eef2ff;color:#4338ca;">
                                <i class="fas fa-box fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm border-0" style="border-radius:18px;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted mb-1">Customers Count</div>
                                <h3 class="mb-0 font-weight-bold">{{ $customersCount }}</h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:52px;height:52px;background:#ecfeff;color:#0891b2;">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm border-0" style="border-radius:18px;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted mb-1">Low Stock Products</div>
                                <h3 class="mb-0 font-weight-bold">{{ $lowStockProductsCount }}</h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:52px;height:52px;background:#fff7ed;color:#ea580c;">
                                <i class="fas fa-triangle-exclamation fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm border-0" style="border-radius:18px;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted mb-1">New Reviews</div>
                                <h3 class="mb-0 font-weight-bold">{{ $newReviewsCount }}</h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:52px;height:52px;background:#fdf4ff;color:#c026d3;">
                                <i class="fas fa-star fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unread messages + Chart --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm border-0" style="border-radius:18px;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted mb-1">Unread Contact Messages</div>
                                <h3 class="mb-0 font-weight-bold">{{ $unreadContactMessagesCount }}</h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:52px;height:52px;background:#fef2f2;color:#dc2626;">
                                <i class="fas fa-envelope fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-12">
                    <div class="card shadow-sm border-0" style="border-radius:20px;">
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h3 class="card-title font-weight-bold mb-2" style="font-size:20px;">Sales Chart</h3>
                                <span class="text-muted" style="font-size:13px;">Last 7 days</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Latest Orders + Top Products --}}
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0" style="border-radius:20px;">
                        <div class="card-header bg-white border-0 pt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold" style="font-size:20px;">Latest Orders</h3>
                                <a href="{{ route('dashboard.orders.index') }}" class="btn btn-sm btn-outline-dark" style="border-radius:10px;">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background:#f8fafc;">
                                    <tr>
                                        <th style="padding:16px;">Order</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latestOrders as $order)
                                        <tr>
                                            <td style="padding:16px;">
                                                <a href="{{ route('dashboard.orders.show', $order->id) }}" class="font-weight-bold text-dark">
                                                    #{{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $order->user->name ?? $order->guest_email ?? 'Guest' }}</td>
                                            <td>
                                                <span class="badge badge-light px-3 py-2" style="border-radius:10px;">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }} px-3 py-2" style="border-radius:10px;">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </td>
                                            <td class="font-weight-bold">{{ number_format($order->total, 2) }}</td>
                                            <td class="text-muted">{{ $order->created_at?->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0" style="border-radius:20px;">
                        <div class="card-header bg-white border-0 pt-4">
                            <h3 class="card-title font-weight-bold" style="font-size:20px;">Top Products</h3>
                        </div>
                        <div class="card-body p-0">
                            @forelse($topProducts as $product)
                                <div class="d-flex align-items-center px-3 py-3 border-bottom">
                                    <div class="mr-3">
                                        <div class="d-flex align-items-center justify-content-center"
                                             style="width:50px;height:50px;border-radius:14px;background:#f3f4f6;">
                                            <i class="fas fa-box text-secondary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold text-dark">
                                            {{ $product->name ?? 'Unnamed Product' }}
                                        </div>
                                        <small class="text-muted">
                                            SKU: {{ $product->sku ?? '-' }}
                                        </small>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-weight-bold">{{ (int) ($product->sold_qty ?? 0) }}</div>
                                        <small class="text-muted">sold</small>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    No product sales data yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0" style="border-radius:20px;">
                        <div class="card-header bg-white border-0 pt-4">
                            <h3 class="card-title font-weight-bold" style="font-size:20px;">Recent Activity</h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline timeline-inverse">
                                @forelse($recentActivities as $activity)
                                    <div class="time-label mb-3">
                                        <span class="bg-dark px-3 py-2" style="border-radius:12px;">
                                            {{ $activity->created_at?->format('d M Y') }}
                                        </span>
                                    </div>

                                    <div>
                                        <i class="fas fa-history bg-primary"></i>
                                        <div class="timeline-item shadow-sm" style="border-radius:16px;">
                                            <span class="time">
                                                <i class="far fa-clock"></i>
                                                {{ $activity->created_at?->format('h:i A') }}
                                            </span>
                                            <h3 class="timeline-header border-0">
                                                <strong>{{ $activity->user->name ?? 'System' }}</strong>
                                                <span class="text-muted">performed</span>
                                                {{ $activity->action }}
                                            </h3>
                                            <div class="timeline-body text-muted">
                                                {{ $activity->auditable_type ?? 'Activity' }}
                                                @if($activity->auditable_id)
                                                    #{{ $activity->auditable_id }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        No recent activity found.
                                    </div>
                                @endforelse
                                <div>
                                    <i class="far fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const salesChartLabels = @json($salesChartLabels);
    const salesChartData = @json($salesChartData);

    const ctx = document.getElementById('salesChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesChartLabels,
            datasets: [{
                label: 'Sales',
                data: salesChartData,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                borderColor: '#2563eb',
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#2563eb',
                pointRadius: 4,
                pointHoverRadius: 5
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#eef2f7'
                    }
                }
            }
        }
    });
</script>
@endpush

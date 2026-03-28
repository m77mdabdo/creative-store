@extends('dashboard.app.layout')

@section('title', 'Abandoned Cart Recovery Dashboard')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Abandoned Cart Recovery Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_recoveries'] }}</h3>
                            <p>Total Recovery Logs</p>
                        </div>
                        <div class="icon"><i class="fas fa-history"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['recovery_rate'] }}%</h3>
                            <p>Recovery Rate</p>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format((float) $stats['recovered_revenue'], 2) }}</h3>
                            <p>Recovered Revenue (SAR)</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format((float) $stats['revenue_estimation'], 2) }}</h3>
                            <p>Revenue Estimation (SAR)</p>
                        </div>
                        <div class="icon"><i class="fas fa-calculator"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $stats['pending_count'] }}</h3>
                            <p>Pending</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['sent_count'] }}</h3>
                            <p>Sent</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['failed_count'] }}</h3>
                            <p>Failed</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['recovered_count'] }}</h3>
                            <p>Recovered</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['expired_count'] }}</h3>
                            <p>Expired</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filters</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.abandoned-cart-recoveries.index') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <input type="text" name="q" class="form-control"
                                       placeholder="Email, token, session, user..."
                                       value="{{ $filters['q'] ?? '' }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    @foreach(['pending','sent','failed','recovered','expired'] as $status)
                                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="channel" class="form-control">
                                    <option value="">All Channels</option>
                                    <option value="email" @selected(($filters['channel'] ?? '') === 'email')>Email</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <input type="date" name="date_from" class="form-control"
                                       value="{{ $filters['date_from'] ?? '' }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <input type="date" name="date_to" class="form-control"
                                       value="{{ $filters['date_to'] ?? '' }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="sort" class="form-control">
                                    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest</option>
                                    <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest</option>
                                    <option value="expires_asc" @selected(($filters['sort'] ?? '') === 'expires_asc')>Expires ASC</option>
                                    <option value="expires_desc" @selected(($filters['sort'] ?? '') === 'expires_desc')>Expires DESC</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select name="per_page" class="form-control">
                                    <option value="15" @selected(($filters['per_page'] ?? 15) == 15)>15</option>
                                    <option value="25" @selected(($filters['per_page'] ?? '') == 25)>25</option>
                                    <option value="50" @selected(($filters['per_page'] ?? '') == 50)>50</option>
                                    <option value="100" @selected(($filters['per_page'] ?? '') == 100)>100</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <button class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Sent / Failed / Recovered Breakdown</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <h4>{{ $statusBreakdown['pending'] }}</h4>
                            <p>Pending</p>
                        </div>
                        <div class="col-md-2">
                            <h4>{{ $statusBreakdown['sent'] }}</h4>
                            <p>Sent</p>
                        </div>
                        <div class="col-md-2">
                            <h4>{{ $statusBreakdown['failed'] }}</h4>
                            <p>Failed</p>
                        </div>
                        <div class="col-md-3">
                            <h4>{{ $statusBreakdown['recovered'] }}</h4>
                            <p>Recovered</p>
                        </div>
                        <div class="col-md-3">
                            <h4>{{ $statusBreakdown['expired'] }}</h4>
                            <p>Expired</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recovery Logs</h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>User / Email</th>
                            <th>Cart</th>
                            <th>Channel</th>
                            <th>Status</th>
                            <th>Cart Value</th>
                            <th>Abandoned At</th>
                            <th>Sent At</th>
                            <th>Recovered At</th>
                            <th>Expires At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recoveries as $recovery)
                            @php
                                $cartValue = $recovery->cart?->items?->sum(function ($item) {
                                    return ((float) $item->price_snapshot) * ((int) $item->quantity);
                                }) ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $recovery->id }}</td>
                                <td>
                                    @if($recovery->user)
                                        <div>{{ $recovery->user->name }}</div>
                                        <small class="text-muted">{{ $recovery->user->email }}</small>
                                    @else
                                        {{ $recovery->email ?: '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($recovery->cart)
                                        <a href="{{ route('dashboard.carts.show', $recovery->cart) }}">
                                            Cart #{{ $recovery->cart->id }}
                                        </a>
                                    @else
                                        <span class="text-muted">Deleted Cart</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($recovery->channel) }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($recovery->status) {
                                            'pending' => 'badge badge-secondary',
                                            'sent' => 'badge badge-info',
                                            'failed' => 'badge badge-danger',
                                            'recovered' => 'badge badge-success',
                                            'expired' => 'badge badge-warning',
                                            default => 'badge badge-light',
                                        };
                                    @endphp
                                    <span class="{{ $badgeClass }}">{{ ucfirst($recovery->status) }}</span>
                                </td>
                                <td>{{ number_format((float) $cartValue, 2) }} SAR</td>
                                <td>{{ $recovery->cart_abandoned_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                <td>{{ $recovery->sent_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                <td>{{ $recovery->recovered_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                <td>{{ $recovery->expires_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                <td>
                                    <a href="{{ route('dashboard.abandoned-cart-recoveries.show', $recovery) }}" class="btn btn-sm btn-info">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">No recovery logs found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{ $recoveries->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

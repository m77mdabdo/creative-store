@extends('dashboard.app.layout')

@section('title', __('site.Dashboard Overview'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-main: #0d1117;
        --bg-card: #161b27;
        --bg-card2: #1c2333;
        --accent: #e94560;
        --gold: #f5a623;
        --teal: #00b4d8;
        --green: #2ec4b6;
        --purple: #7c3aed;
        --blue: #3b82f6;
        --text: #e2e8f0;
        --muted: #8892a4;
        --border: rgba(255,255,255,0.07);
    }

    body, .content-wrapper {
        background: var(--bg-main) !important;
        font-family: 'Cairo', sans-serif !important;
        color: var(--text) !important;
    }

    .content-wrapper {
        background: linear-gradient(135deg, #0d1117 0%, #1a1a2e 60%, #16213e 100%) !important;
        min-height: 100vh;
    }

    /* Header */
    .dash-header {
        background: rgba(255,255,255,0.03);
        border-bottom: 1px solid var(--border);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0;
    }

    .dash-logo {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, var(--accent), var(--purple));
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 800; color: #fff;
    }

    .dash-live-badge {
        background: rgba(233,69,96,0.15);
        border: 1px solid rgba(233,69,96,0.3);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 12px;
        color: var(--accent);
        display: flex; align-items: center; gap: 6px;
    }

    .dash-live-dot {
        width: 6px; height: 6px;
        background: var(--accent);
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }

    /* Section title */
    .section-title {
        font-size: 11px;
        color: var(--muted);
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .welcome-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
    }

    /* Stat Box Dark */
    .stat-box {
        background: linear-gradient(135deg, var(--bg-card), var(--bg-card2));
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        margin-bottom: 16px;
        transition: transform 0.2s, border-color 0.2s;
    }

    .stat-box:hover {
        transform: translateY(-3px);
        border-color: rgba(255,255,255,0.15);
    }

    .stat-box .glow-circle {
        position: absolute;
        top: -25px; left: -25px;
        width: 90px; height: 90px;
        border-radius: 50%;
        opacity: 0.12;
    }

    .stat-box .stat-label {
        font-size: 11px;
        color: var(--muted);
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    .stat-box .stat-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 6px;
        line-height: 1.1;
    }

    .stat-box .stat-sub {
        font-size: 11px;
        color: var(--muted);
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 11px;
        margin-top: 10px;
    }

    .stat-badge.up   { background: rgba(46,196,182,0.15); color: #2ec4b6; }
    .stat-badge.warn { background: rgba(233,69,96,0.15);  color: #e94560; }
    .stat-badge.info { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .stat-badge.gold { background: rgba(245,166,35,0.15); color: #f5a623; }

    /* Progress bar */
    .stat-progress {
        background: rgba(255,255,255,0.07);
        border-radius: 4px;
        height: 4px;
        margin-top: 12px;
        overflow: hidden;
    }
    .stat-progress-fill {
        height: 100%;
        border-radius: 4px;
        background: var(--teal);
    }

    /* Small card (white style for secondary stats) */
    .mini-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        transition: transform 0.2s;
    }
    .mini-card:hover { transform: translateY(-2px); }

    .mini-card-label {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .mini-card-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
    }

    .mini-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }

    .mini-card .btn-more {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.15);
        color: var(--muted);
        border-radius: 10px;
        font-size: 12px;
        padding: 4px 14px;
        margin-top: 10px;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }
    .mini-card .btn-more:hover {
        background: rgba(255,255,255,0.07);
        color: var(--text);
    }

    /* Chart Card */
    .chart-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .chart-card .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .chart-card .chart-sub {
        font-size: 12px;
        color: var(--muted);
    }

    /* Table Card */
    .table-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .table-card .table-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-card .table-card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
    }

    .table-card table {
        width: 100%;
        border-collapse: collapse;
    }

    .table-card thead th {
        background: rgba(255,255,255,0.03);
        padding: 12px 20px;
        font-size: 11px;
        color: var(--muted);
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 500;
        border-bottom: 1px solid var(--border);
    }

    .table-card tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.15s;
    }

    .table-card tbody tr:hover {
        background: rgba(255,255,255,0.03);
    }

    .table-card tbody td {
        padding: 14px 20px;
        font-size: 13px;
        color: var(--text);
    }

    .table-card tbody tr:last-child {
        border-bottom: none;
    }

    .order-link {
        color: var(--teal);
        font-weight: 600;
        text-decoration: none;
    }
    .order-link:hover { color: #fff; }

    /* Status badges */
    .status-badge {
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .status-paid    { background: rgba(46,196,182,0.15); color: #2ec4b6; }
    .status-pending { background: rgba(245,166,35,0.15); color: #f5a623; }
    .status-default { background: rgba(255,255,255,0.07); color: var(--muted); }

    /* Top Products */
    .product-row {
        display: flex;
        align-items: center;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        transition: background 0.15s;
    }
    .product-row:hover { background: rgba(255,255,255,0.03); }
    .product-row:last-child { border-bottom: none; }

    .product-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: rgba(255,255,255,0.05);
        display: flex; align-items: center; justify-content: center;
        color: var(--muted);
        margin-left: 12px;
        flex-shrink: 0;
    }

    .product-name { font-size: 13px; font-weight: 600; color: var(--text); }
    .product-sku  { font-size: 11px; color: var(--muted); }
    .product-sold { font-size: 15px; font-weight: 700; color: var(--gold); }

    /* Timeline */
    .timeline-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .timeline-item-custom {
        display: flex;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }
    .timeline-item-custom:last-child { border-bottom: none; }

    .timeline-dot {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: rgba(59,130,246,0.2);
        border: 1px solid rgba(59,130,246,0.4);
        display: flex; align-items: center; justify-content: center;
        color: #3b82f6;
        font-size: 13px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .timeline-content .t-user { font-size: 13px; font-weight: 600; color: var(--text); }
    .timeline-content .t-action { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .timeline-content .t-time   { font-size: 11px; color: var(--muted); margin-top: 4px; }

    /* Gradient accent cards */
    .accent-card {
        background: linear-gradient(135deg, var(--accent), var(--purple));
        border-radius: 16px;
        padding: 20px;
        color: #fff;
        margin-bottom: 16px;
    }

    /* Btn view all */
    .btn-view-all {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.15);
        color: var(--muted);
        border-radius: 10px;
        font-size: 12px;
        padding: 6px 16px;
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-view-all:hover {
        background: rgba(255,255,255,0.07);
        color: var(--text);
        text-decoration: none;
    }

    /* Content padding */
    .content-wrapper > .content { padding: 0 24px 32px; }
    .content-wrapper > .content-header { padding: 20px 24px 12px; }

    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

    /* Overrides for AdminLTE conflicts */
    .small-box { display: none !important; }
    .content-header h1 { color: var(--text) !important; font-family: 'Cairo', sans-serif !important; }
    .card { background: transparent !important; border: none !important; box-shadow: none !important; }
</style>
@endpush

@section('content')
<div class="content-wrapper">

    {{-- Top Header Bar --}}
    <div class="dash-header">
        <div style="display:flex;align-items:center;gap:14px;">
            <div class="dash-logo">ك</div>
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--text);">creative_store</div>
                <div style="font-size:11px;color:var(--muted);">{{ __('site.Dashboard Overview') }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="dash-live-badge">
                <div class="dash-live-dot"></div>
                {{ __('site.Live') }}
            </div>
            <div style="background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:8px;padding:6px 14px;font-size:12px;color:var(--muted);">
                <i class="far fa-calendar-alt" style="margin-left:5px;"></i>
                {{ now()->format('d M Y') }}
            </div>
            <div style="width:36px;height:36px;background:linear-gradient(135deg,var(--accent),var(--purple));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </div>
    </div>

    <section class="content-header">
        <div class="container-fluid">
            @include('dashboard.app.partials.alerts')
            <div class="section-title">{{ __('site.Overview') }}</div>
            <div class="welcome-title">{{ __('site.Welcome back') }}, {{ auth()->user()->name }} 👋</div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- Top Sales Stats --}}
            <div class="row">
                @if(auth()->user()->hasPermission('dashboard.sales-totals.view'))
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="stat-box">
                        <div class="glow-circle" style="background:var(--gold);"></div>
                        <div class="stat-label">{{ __('site.Total Sales') }}</div>
                        <div class="stat-value" style="color:var(--gold);">{{ number_format($totalSales, 2) }}</div>
                        <div class="stat-sub">{{ __('site.SAR') }}</div>
                        <div><span class="stat-badge up">↑ {{ __('site.This Month') }}</span></div>
                    </div>
                </div>
                @endif

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="stat-box">
                        <div class="glow-circle" style="background:var(--teal);"></div>
                        <div class="stat-label">{{ __('site.Orders Count') }}</div>
                        <div class="stat-value" style="color:var(--teal);">{{ $ordersCount }}</div>
                        <div class="stat-sub">{{ __('site.Total orders') }}</div>
                        <div><span class="stat-badge info">↑ {{ __('site.View All') }}</span></div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="stat-box">
                        <div class="glow-circle" style="background:var(--green);"></div>
                        <div class="stat-label">{{ __('site.Paid Orders') }}</div>
                        <div class="stat-value" style="color:var(--green);">{{ $paidOrdersCount }}</div>
                        <div class="stat-sub">{{ __('site.from') }} {{ $ordersCount }}</div>
                        <div class="stat-progress">
                            <div class="stat-progress-fill" style="width:{{ $ordersCount > 0 ? round($paidOrdersCount/$ordersCount*100) : 0 }}%; background:var(--green);"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="stat-box">
                        <div class="glow-circle" style="background:var(--accent);"></div>
                        <div class="stat-label">{{ __('site.Pending Orders') }}</div>
                        <div class="stat-value" style="color:var(--accent);">{{ $pendingOrdersCount }}</div>
                        <div class="stat-sub">{{ __('site.Need review') }}</div>
                        <div><span class="stat-badge warn">! {{ __('site.Alert') }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Review Stats --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="stat-box">
                        <div class="glow-circle" style="background:var(--blue);"></div>
                        <div class="stat-label">{{ __('site.Total Reviews') }}</div>
                        <div class="stat-value" style="color:var(--blue);">{{ $stats['totalReviews'] ?? 0 }}</div>
                        <div style="display:flex;align-items:center;gap:4px;margin-top:8px;">
                            @for($i=1;$i<=5;$i++)
                                <span style="font-size:13px;color:{{ $i <= round($stats['averageRating'] ?? 0) ? 'var(--gold)' : 'rgba(245,166,35,0.25)' }};">★</span>
                            @endfor
                            <span style="font-size:12px;color:var(--muted);margin-right:4px;">{{ number_format($stats['averageRating'] ?? 0, 1) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="stat-box">
                        <div class="glow-circle" style="background:var(--green);"></div>
                        <div class="stat-label">{{ __('site.Approved Reviews') }}</div>
                        <div class="stat-value" style="color:var(--green);">{{ $stats['approvedReviews'] ?? 0 }}</div>
                        <div class="stat-sub">{{ __('site.from') }} {{ $stats['totalReviews'] ?? 0 }} {{ __('site.review') }}</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="stat-box">
                        <div class="glow-circle" style="background:var(--accent);"></div>
                        <div class="stat-label">{{ __('site.Pending Reviews') }}</div>
                        <div class="stat-value" style="color:var(--accent);">{{ $stats['pendingReviews'] ?? 0 }}</div>
                        <div class="stat-sub">{{ __('site.Awaiting review') }}</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="stat-box">
                        <div class="glow-circle" style="background:var(--teal);"></div>
                        <div class="stat-label">{{ __('site.Customers Count') }}</div>
                        <div class="stat-value" style="color:var(--teal);">{{ $customersCount }}</div>
                        <div><span class="stat-badge up">↑ +23 {{ __('site.New') }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Secondary Stats --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="mini-card">
                        <div>
                            <div class="mini-card-label">{{ __('site.Products Count') }}</div>
                            <div class="mini-card-value">{{ $productsCount ?? ($stats['totalProducts'] ?? 0) }}</div>
                            <a href="{{ route('dashboard.products.index') }}" class="btn-more">{{ __('site.More info') }}</a>
                        </div>
                        <div class="mini-icon" style="background:rgba(124,58,237,0.15);color:#7c3aed;">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="mini-card">
                        <div>
                            <div class="mini-card-label">{{ __('site.Active Products') }}</div>
                            <div class="mini-card-value">{{ $stats['activeProducts'] ?? 0 }}</div>
                            <a href="{{ route('dashboard.products.index', ['status' => 'active']) }}" class="btn-more">{{ __('site.More info') }}</a>
                        </div>
                        <div class="mini-icon" style="background:rgba(46,196,182,0.15);color:var(--green);">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="mini-card">
                        <div>
                            <div class="mini-card-label">{{ __('site.Low Stock Products') }}</div>
                            <div class="mini-card-value" style="color:var(--accent);">{{ $lowStockProductsCount }}</div>
                        </div>
                        <div class="mini-icon" style="background:rgba(233,69,96,0.15);color:var(--accent);">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="mini-card">
                        <div>
                            <div class="mini-card-label">{{ __('site.Unread Messages') }}</div>
                            <div class="mini-card-value" style="color:var(--accent);">{{ $unreadContactMessagesCount ?? ($stats['unreadMessages'] ?? 0) }}</div>
                            <a href="{{ route('dashboard.contact-messages.index') }}" class="btn-more">{{ __('site.More info') }}</a>
                        </div>
                        <div class="mini-icon" style="background:rgba(233,69,96,0.1);color:var(--accent);">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Completion Rate Accent Card --}}
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="accent-card">
                        <div style="font-size:11px;opacity:0.75;margin-bottom:6px;">{{ __('site.Completion Rate') }}</div>
                        <div style="font-size:32px;font-weight:800;margin-bottom:8px;">
                            {{ $ordersCount > 0 ? round($paidOrdersCount / $ordersCount * 100, 1) : 0 }}%
                        </div>
                        <div style="background:rgba(255,255,255,0.2);border-radius:4px;height:6px;overflow:hidden;margin-bottom:8px;">
                            <div style="background:#fff;height:100%;width:{{ $ordersCount > 0 ? round($paidOrdersCount/$ordersCount*100) : 0 }}%;border-radius:4px;"></div>
                        </div>
                        <div style="font-size:11px;opacity:0.75;">{{ $paidOrdersCount }} {{ __('site.from') }} {{ $ordersCount }} {{ __('site.orders completed') }}</div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-12">
                    <div class="mini-card" style="flex-direction:column;align-items:flex-start;gap:10px;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);">{{ __('site.Store Status') }}</div>
                        <div style="width:100%;">
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border);">
                                <span style="font-size:12px;color:var(--muted);">{{ __('site.New Reviews') }}</span>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:14px;font-weight:600;">{{ $newReviewsCount }}</span>
                                    <span class="stat-badge gold" style="margin:0;">{{ __('site.New') }}</span>
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border);">
                                <span style="font-size:12px;color:var(--muted);">{{ __('site.Unread Messages') }}</span>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:14px;font-weight:600;">{{ $unreadContactMessagesCount ?? 0 }}</span>
                                    <span class="stat-badge warn" style="margin:0;">{{ __('site.Unread') }}</span>
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;">
                                <span style="font-size:12px;color:var(--muted);">{{ __('site.Pending Orders') }}</span>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:14px;font-weight:600;">{{ $pendingOrdersCount }}</span>
                                    <span class="stat-badge warn" style="margin:0;">{{ __('site.Alert') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sales Chart --}}
            @if(auth()->user()->hasPermission('dashboard.sales-totals.view'))
            <div class="row">
                <div class="col-12">
                    <div class="chart-card">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                            <div>
                                <div class="chart-title">{{ __('site.Sales Chart') }}</div>
                                <div class="chart-sub">{{ __('site.Last 7 days') }}</div>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:8px;height:8px;border-radius:50%;background:var(--teal);"></div>
                                <span style="font-size:12px;color:var(--muted);">{{ __('site.sales') }}</span>
                            </div>
                        </div>
                        <div style="height:280px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Latest Orders + Top Products --}}
            <div class="row">
                <div class="col-lg-8">
                    <div class="table-card">
                        <div class="table-card-header">
                            <div class="table-card-title">{{ __('site.Latest Orders') }}</div>
                            <a href="{{ route('dashboard.orders.index') }}" class="btn-view-all">{{ __('site.View All') }}</a>
                        </div>
                        <div style="overflow-x:auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ __('site.Order') }}</th>
                                        <th>{{ __('site.Customer') }}</th>
                                        <th>{{ __('site.Status') }}</th>
                                        <th>{{ __('site.Payment') }}</th>
                                        @if(auth()->user()->hasPermission('orders.view-totals'))
                                            <th>{{ __('site.Total') }}</th>
                                        @endif
                                        <th>{{ __('site.Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latestOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('dashboard.orders.show', $order->id) }}" class="order-link">
                                                #{{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td style="color:var(--muted);">{{ $order->user->name ?? $order->guest_email ?? __('site.Guest') }}</td>
                                        <td>
                                            <span class="status-badge status-default">
                                                {{ __('site.status_' . strtolower($order->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">
                                                {{ __('site.status_' . strtolower($order->payment_status)) }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->hasPermission('orders.view-totals'))
                                            <td style="font-weight:700;color:var(--gold);">{{ number_format($order->total, 2) }}</td>
                                        @endif
                                        <td style="color:var(--muted);font-size:12px;">{{ $order->created_at?->translatedFormat('d M Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasPermission('orders.view-totals') ? 6 : 5 }}" style="text-align:center;color:var(--muted);padding:32px;">
                                            {{ __('site.No orders found.') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="table-card">
                        <div class="table-card-header">
                            <div class="table-card-title">{{ __('site.Top Products') }}</div>
                        </div>
                        @forelse($topProducts as $product)
                        <div class="product-row">
                            <div class="product-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div style="flex:1;">
                                <div class="product-name">{{ $product->name ?? 'Unnamed Product' }}</div>
                                <div class="product-sku">{{ __('site.SKU') }}: {{ $product->sku ?? '-' }}</div>
                            </div>
                            <div style="text-align:left;">
                                <div class="product-sold">{{ (int)($product->sold_qty ?? 0) }}</div>
                                <div style="font-size:10px;color:var(--muted);">{{ __('site.sold') }}</div>
                            </div>
                        </div>
                        @empty
                        <div style="text-align:center;color:var(--muted);padding:32px;font-size:13px;">
                            {{ __('site.No product sales data yet.') }}
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="row">
                <div class="col-12">
                    <div class="timeline-card">
                        <div style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:16px;">
                            {{ __('site.Recent Activity') }}
                        </div>
                        @forelse($recentActivities as $activity)
                        <div class="timeline-item-custom">
                            <div class="timeline-dot">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="t-user">
                                    {{ $activity->user->name ?? __('site.System') }}
                                    <span style="font-weight:400;color:var(--muted);"> — {{ $activity->action }}</span>
                                </div>
                                <div class="t-action">
                                    {{ $activity->auditable_type ?? __('site.Activity') }}
                                    @if($activity->auditable_id) #{{ $activity->auditable_id }} @endif
                                </div>
                                <div class="t-time">
                                    <i class="far fa-clock" style="margin-left:4px;"></i>
                                    {{ $activity->created_at?->translatedFormat('d M Y — h:i A') }}
                                </div>
                            </div>
                        </div>
                        @empty
                        <div style="text-align:center;color:var(--muted);padding:24px;font-size:13px;">
                            {{ __('site.No recent activity found.') }}
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
@if(auth()->user()->hasPermission('dashboard.sales-totals.view'))
<script>
    const salesChartLabels = @json($salesChartLabels);
    const salesChartData   = @json($salesChartData);
    const chartEl = document.getElementById('salesChart');

    if (chartEl) {
        const ctx = chartEl.getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(0,180,216,0.25)');
        gradient.addColorStop(1, 'rgba(0,180,216,0.01)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesChartLabels,
                datasets: [{
                    label: '{{ __("site.sales") }}',
                    data: salesChartData,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    backgroundColor: gradient,
                    borderColor: '#00b4d8',
                    pointBackgroundColor: '#00b4d8',
                    pointBorderColor: '#0d1117',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1c2333',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        titleColor: '#e2e8f0',
                        bodyColor: '#00b4d8',
                        padding: 12,
                        cornerRadius: 10
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#8892a4', font: { family: 'Cairo' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#8892a4', font: { family: 'Cairo' } }
                    }
                }
            }
        });
    }
</script>
@endif
@endpush

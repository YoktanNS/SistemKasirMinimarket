@extends('layouts.supplier')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt me-2"></i>Supplier Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <small class="text-muted">Last updated: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</small>
        </div>
    </div>
</div>

<!-- Welcome Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title mb-1">Welcome back, <strong class="text-primary">{{ $supplier->nama_supplier }}</strong>! 🎉</h4>
                        <p class="card-text text-muted mb-0">
                            <i class="fas fa-calendar me-1"></i>
                            Today is {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="bg-primary text-white rounded-pill px-3 py-2 d-inline-block">
                            <small><i class="fas fa-bell me-1"></i> Supplier Portal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom stats-card h-100 bg-custom-primary text-white">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">
                            Total Purchase Orders
                        </div>
                        <div class="h2 mb-0 font-weight-bold">{{ $stats['total_po'] }}</div>
                        <small class="mt-1 opacity-75">
                            <i class="fas fa-history me-1"></i>All time orders
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom stats-card h-100 bg-custom-warning text-white">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">
                            Pending Confirmation
                        </div>
                        <div class="h2 mb-0 font-weight-bold">{{ $stats['pending_po'] }}</div>
                        <small class="mt-1 opacity-75">
                            <i class="fas fa-clock me-1"></i>Need your action
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-half fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom stats-card h-100 bg-custom-info text-white">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">
                            In Delivery
                        </div>
                        <div class="h2 mb-0 font-weight-bold">{{ $stats['ongoing_delivery'] }}</div>
                        <small class="mt-1 opacity-75">
                            <i class="fas fa-shipping-fast me-1"></i>On the way
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom stats-card h-100 bg-custom-success text-white">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">
                            Completed Orders
                        </div>
                        <div class="h2 mb-0 font-weight-bold">{{ $stats['completed_po'] }}</div>
                        <small class="mt-1 opacity-75">
                            <i class="fas fa-check-double me-1"></i>Successfully delivered
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Purchase Orders -->
<div class="card card-custom">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2 text-primary"></i>Recent Purchase Orders
            </h5>
            <a href="{{ route('supplier.purchase-orders.index') }}" class="btn btn-sm btn-custom-primary">
                <i class="fas fa-list me-1"></i>View All
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($recentPOs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Confirmation</th>
                            <th>Delivery</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPOs as $po)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $po->no_po }}</strong>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($po->tanggal_po)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $po->total_item }} item(s)
                                </span>
                            </td>
                            <td>
                                <strong class="text-success">Rp {{ number_format($po->total_harga, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-custom {{ $po->status_konfirmasi_badge }}">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                    {{ $po->status_konfirmasi_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-custom {{ $po->status_pengiriman_badge }}">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                    {{ $po->status_pengiriman_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('supplier.purchase-orders.show', $po->po_id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-inbox fa-4x text-muted opacity-50"></i>
                </div>
                <h5 class="text-muted mb-2">No Purchase Orders Yet</h5>
                <p class="text-muted mb-4">Purchase orders from SmartMart Campus will appear here</p>
                <div class="text-muted">
                    <small><i class="fas fa-info-circle me-1"></i> You'll be notified when new orders arrive</small>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-4 mb-3">
        <div class="card card-custom bg-light">
            <div class="card-body text-center">
                <i class="fas fa-cog fa-2x text-primary mb-3"></i>
                <h6>Profile Settings</h6>
                <p class="text-muted small mb-3">Update your company information and bank details</p>
                <a href="{{ route('supplier.profile.edit') }}" class="btn btn-sm btn-outline-primary">
                    Manage Profile
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-custom bg-light">
            <div class="card-body text-center">
                <i class="fas fa-credit-card fa-2x text-success mb-3"></i>
                <h6>Payment Info</h6>
                <p class="text-muted small mb-3">View your payment history and invoices</p>
                <a href="{{ route('supplier.payments.index') }}" class="btn btn-sm btn-outline-success">
                    View Payments
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-custom bg-light">
            <div class="card-body text-center">
                <i class="fas fa-question-circle fa-2x text-info mb-3"></i>
                <h6>Need Help?</h6>
                <p class="text-muted small mb-3">Contact SmartMart Campus support team</p>
                <button class="btn btn-sm btn-outline-info" disabled>
                    Contact Support
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
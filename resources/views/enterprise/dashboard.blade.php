@extends('layouts.newapp')

@section('title', 'Operations Dashboard | ANSH Enterprises')
@section('page-title', 'Operations Dashboard')

@section('content')
<main class="employee-management-page enterprise-page dashboard-page">
    <div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div><div class="eyebrow">ANSH ENTERPRISES · OPERATIONS</div><h3 class="mb-1">Good to see you</h3><p class="text-muted mb-0">A clear view of your contracts, workforce, fleet, and services.</p></div>
        <a class="btn btn-primary" href="{{ route('resources.index', 'contracts') }}"><i class="bi bi-file-earmark-plus me-1"></i> Manage contracts</a>
    </div>
    @if (session('status'))<div class="alert alert-info alert-dismissible fade show" role="status">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif

    <div class="row g-3 mb-4">
        @foreach ([['contracts','Total contracts','bi-file-earmark-text','blue'],['active_contracts','Active contracts','bi-activity','green'],['employees','Active employees','bi-people','violet'],['vehicles','Active fleet vehicles','bi-truck','orange'],['service_logs','Services today','bi-journal-check','teal'],['pending_invoices','Invoices to follow up','bi-receipt','rose']] as [$key,$label,$icon,$color])
            <div class="col-12 col-sm-6 col-xl-4"><div class="metric-card metric-card-{{ $color }}"><div class="metric-icon"><i class="bi {{ $icon }}"></i></div><div><div class="metric-label">{{ $label }}</div><div class="metric-value">{{ number_format($counts[$key]) }}</div></div></div></div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8"><section class="card records-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center"><div><h5 class="mb-1">Recent contracts</h5><div class="text-muted small">Latest government service agreements</div></div><a class="btn btn-sm btn-light border" href="{{ route('resources.index', 'contracts') }}">View all <i class="bi bi-arrow-right ms-1"></i></a></div>
            <div class="table-responsive"><table class="table align-middle enterprise-table mb-0"><thead><tr><th>Contract</th><th>Client</th><th>Service</th><th>Period</th><th>Status</th></tr></thead><tbody>
                @forelse ($recentContracts as $contract)
                    <tr><td><strong>{{ $contract->contract_number }}</strong><div class="text-muted small">{{ $contract->organization ?: $contract->client_name }}{{ $contract->department ? ' · '.$contract->department : '' }}</div></td><td>{{ $contract->client_name }}</td><td>{{ ucwords($contract->service_type) }}</td><td>{{ \Illuminate\Support\Carbon::parse($contract->start_date)->format('d M Y') }}<div class="text-muted small">to {{ $contract->end_date ? \Illuminate\Support\Carbon::parse($contract->end_date)->format('d M Y') : 'Ongoing' }}</div></td><td><span class="status-pill status-{{ str_replace('_','-',$contract->status) }}">{{ ucwords(str_replace('_',' ',$contract->status)) }}</span></td></tr>
                @empty <tr><td colspan="5" class="text-center text-muted py-5">No contracts recorded yet. Add your first contract to get started.</td></tr> @endforelse
            </tbody></table></div>
        </section></div>
        <div class="col-12 col-xl-4"><section class="card records-card h-100"><div class="card-header"><h5 class="mb-1">Quick access</h5><div class="text-muted small">Jump to a key work area</div></div><div class="quick-links">
            @foreach ([['clients','Government clients','bi-building','Manage departments and contacts'],['employees','Manpower','bi-people','Employee records and attendance'],['vehicles','Ambulance & cab fleet','bi-truck','Availability and compliance dates'],['deployments','Contract deployments','bi-diagram-3','Assign people and vehicles'],['service-logs','Service logs','bi-journal-check','Trips and service delivery'],['invoices','Billing','bi-receipt','Invoices and payment tracking']] as [$key,$label,$icon,$hint])
                <a class="quick-link" href="{{ route('resources.index', $key) }}"><span class="quick-icon"><i class="bi {{ $icon }}"></i></span><span class="flex-grow-1"><strong>{{ $label }}</strong><small>{{ $hint }}</small></span><i class="bi bi-chevron-right text-muted"></i></a>
            @endforeach
            <a class="quick-link" href="{{ route('salary.index') }}"><span class="quick-icon"><i class="bi bi-wallet2"></i></span><span class="flex-grow-1"><strong>Salary processing</strong><small>Calculate wages, PF, ESI, and net salary</small></span><i class="bi bi-chevron-right text-muted"></i></a>
        </div></section></div>
    </div>
</main>
@endsection

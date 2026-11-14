<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom">
        <div class="sidebar-brand me-auto"><span class="brand-mark">A</span><span>ANSH Enterprises</span></div>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
        <li class="nav-title">Overview</li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2 nav-icon"></i> Dashboard</a></li>
        <li class="nav-title">Business</li>
        @foreach (['clients' => ['bi-building', 'Government Clients'], 'contracts' => ['bi-file-earmark-text', 'Contracts']] as $key => [$icon, $label])
            <li class="nav-item"><a class="nav-link {{ request()->is($key) ? 'active' : '' }}" href="{{ route('resources.index', $key) }}"><i class="bi {{ $icon }} nav-icon"></i> {{ $label }}</a></li>
        @endforeach
        <li class="nav-title">Service Operations</li>
        @foreach (['employees' => ['bi-people', 'Employees & Manpower'], 'attendance' => ['bi-calendar-check', 'Attendance'], 'vehicles' => ['bi-truck', 'Ambulance & Cab Fleet'], 'deployments' => ['bi-diagram-3', 'Contract Deployments']] as $key => [$icon, $label])
            <li class="nav-item"><a class="nav-link {{ request()->is($key) ? 'active' : '' }}" href="{{ route('resources.index', $key) }}"><i class="bi {{ $icon }} nav-icon"></i> {{ $label }}</a></li>
        @endforeach
        <li class="nav-title">Finance</li>
        @foreach (['invoices' => ['bi-receipt', 'Invoices'], 'payments' => ['bi-cash-coin', 'Payments']] as $key => [$icon, $label])
            <li class="nav-item"><a class="nav-link {{ request()->is($key) ? 'active' : '' }}" href="{{ route('resources.index', $key) }}"><i class="bi {{ $icon }} nav-icon"></i> {{ $label }}</a></li>
        @endforeach
        <li class="nav-title">Payroll</li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('salary.index') ? 'active' : '' }}" href="{{ route('salary.index') }}"><i class="bi bi-wallet2 nav-icon"></i> Salary processing</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('salary.rules*') ? 'active' : '' }}" href="{{ route('salary.rules') }}"><i class="bi bi-sliders nav-icon"></i> Salary rules master</a></li>
        @can('manage-users')
            <li class="nav-title">Administration</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-person-gear nav-icon"></i> User management</a></li>
        @endcan
    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex"><button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button></div>
</div>

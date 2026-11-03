<header class="header header-sticky p-0 mb-3">
    <div class="container-fluid border-bottom px-4">
        <button class="header-toggler" type="button" aria-label="Toggle navigation" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="header-title ms-3">@yield('page-title', 'Operations Portal')</div>
        <div class="ms-auto d-flex align-items-center gap-3 py-2">
            <span class="company-chip"><i class="bi bi-shield-check"></i> ANSH Enterprises</span>
            <div class="dropdown">
                <button class="btn btn-link user-menu-button dropdown-toggle text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="user-avatar"><i class="bi bi-person-fill"></i></span>
                    <span class="text-start"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'super-admin' ? 'Super admin' : 'Admin · read only' }}</small></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>My profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Log out</button></form></li>
                </ul>
            </div>
        </div>
    </div>
</header>

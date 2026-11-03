@extends('layouts.newapp')

@section('title', 'User Management | ANSH Enterprises')
@section('page-title', 'User Management')

@section('content')
<main class="employee-management-page enterprise-page">
    <div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4"><div><div class="eyebrow">ADMINISTRATION · SUPER-ADMIN</div><h3 class="mb-1">User management</h3><p class="text-muted mb-0">Manage portal accounts and assign access roles.</p></div><button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#userModal" data-mode="create"><i class="bi bi-person-plus me-1"></i> Add user</button></div>
    @if (session('success')) <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @if (session('error')) <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
    <section class="card records-card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3"><div><h5 class="mb-1">Portal users</h5><div class="text-muted small">{{ number_format($users->total()) }} account(s)</div></div>
            <form method="GET" action="{{ route('users.index') }}" class="filter-bar d-flex flex-wrap gap-2"><div class="input-group search-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search name or email"></div><select class="form-select filter-select" name="role"><option value="">All roles</option><option value="super-admin" @selected(request('role') === 'super-admin')>Super admin</option><option value="admin" @selected(request('role') === 'admin')>Admin · read only</option></select><button class="btn btn-light border" type="submit">Filter</button>@if(request()->query())<a href="{{ route('users.index') }}" class="btn btn-light border">Reset</a>@endif</form>
        </div>
        <div class="table-responsive"><table class="table align-middle enterprise-table mb-0"><thead><tr><th>User</th><th>Email</th><th>Role</th><th>Joined</th><th class="text-end">Actions</th></tr></thead><tbody>
            @forelse ($users as $user)
                <tr><td><div class="d-flex align-items-center gap-2"><span class="table-user-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><div><strong>{{ $user->name }}</strong>@if($user->id === auth()->id())<span class="text-muted small ms-1">(you)</span>@endif</div></div></td><td>{{ $user->email }}</td><td><span class="user-role-badge {{ $user->role === 'admin' ? 'admin' : '' }}">{{ $user->role === 'super-admin' ? 'Super admin · full access' : 'Admin · read only' }}</span></td><td>{{ $user->created_at->format('d M Y') }}</td><td class="text-end text-nowrap"><button class="btn btn-sm btn-icon btn-view" type="button" title="View user" data-bs-toggle="modal" data-bs-target="#userViewModal" data-record="{{ json_encode($user->only(['name','email','role','created_at'])) }}"><i class="bi bi-eye"></i></button><button class="btn btn-sm btn-icon btn-edit" type="button" title="Edit" data-bs-toggle="modal" data-bs-target="#userModal" data-mode="edit" data-id="{{ $user->id }}" data-record="{{ json_encode($user->only(['name','email','role'])) }}"><i class="bi bi-pencil-square"></i></button>@if($user->id !== auth()->id())<button class="btn btn-sm btn-icon btn-delete" type="button" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-id="{{ $user->id }}" data-label="{{ $user->name }}"><i class="bi bi-trash3"></i></button>@endif</td></tr>
            @empty <tr><td colspan="5" class="empty-state"><span class="empty-icon"><i class="bi bi-people"></i></span><strong>No user accounts found</strong><span>Adjust the filters or add a user account.</span></td></tr> @endforelse
        </tbody></table></div>
        @if($users->hasPages())<div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2"><small class="text-muted">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</small>{{ $users->links() }}</div>@endif
    </section>
</main>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalTitle" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><form id="userForm" method="POST" action="{{ route('users.store') }}">@csrf<input type="hidden" id="userMethod" name="_method" value="POST"><div class="modal-header"><div><div class="eyebrow">ACCOUNT ACCESS</div><h5 class="modal-title" id="userModalTitle">Add user</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3">
    <div class="col-md-6"><label class="form-label" for="userName">Full name</label><input class="form-control" id="userName" name="name" maxlength="120" required></div>
    <div class="col-md-6"><label class="form-label" for="userEmail">Email address</label><input class="form-control" id="userEmail" name="email" type="email" maxlength="160" required></div>
    <div class="col-md-6"><label class="form-label" for="userRole">Role</label><select class="form-select" id="userRole" name="role" required><option value="admin">Admin · read only</option><option value="super-admin">Super admin · full access</option></select></div>
    <div class="col-md-6"><label class="form-label" for="userPassword">Password <span class="text-muted fw-normal" id="passwordHint">(required)</span></label><input class="form-control" id="userPassword" name="password" type="password" autocomplete="new-password" minlength="8"><div class="form-text">Minimum 8 characters.</div></div>
    <div class="col-md-6"><label class="form-label" for="userPasswordConfirmation">Confirm password</label><input class="form-control" id="userPasswordConfirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8"></div>
    <div class="col-12"><div class="alert alert-info small mb-0"><strong>Admin</strong> accounts can view portal records and their own profile. <strong>Super-admin</strong> accounts can create, edit, and delete records and users.</div></div>
    </div></div><div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save user</button></div></form></div></div></div>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"><div class="modal-content"><form id="deleteUserForm" method="POST" action="{{ route('users.destroy', 0) }}">@csrf @method('DELETE')<div class="modal-header"><div><div class="eyebrow">CONFIRM ACTION</div><h5 class="modal-title" id="deleteUserTitle">Delete user?</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="mb-0 text-muted">Permanently delete <strong id="deleteUserLabel" class="text-body"></strong> and their sign-in access?</p></div><div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i>Delete user</button></div></form></div></div></div>
<div class="modal fade" id="userViewModal" tabindex="-1" aria-labelledby="userViewTitle" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><div><div class="eyebrow">USER ACCOUNT</div><h5 class="modal-title" id="userViewTitle">User details</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-0"><table class="table table-bordered employee-details-table mb-0"><tbody id="userViewRows"></tbody></table></div><div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button></div></div></div></div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const userModal = document.getElementById('userModal');
    const userForm = document.getElementById('userForm');
    const method = document.getElementById('userMethod');
    const title = document.getElementById('userModalTitle');
    const password = document.getElementById('userPassword');
    const passwordConfirm = document.getElementById('userPasswordConfirmation');
    const passwordHint = document.getElementById('passwordHint');
    const deleteModal = document.getElementById('deleteUserModal');
    const deleteForm = document.getElementById('deleteUserForm');
    const deleteLabel = document.getElementById('deleteUserLabel');
    const viewModal = document.getElementById('userViewModal');
    viewModal.addEventListener('show.bs.modal', event => {
        const user = JSON.parse(event.relatedTarget.dataset.record);
        const rows = document.getElementById('userViewRows'); rows.replaceChildren();
        [['Full name', user.name], ['Email address', user.email], ['Role', user.role === 'super-admin' ? 'Super admin · full access' : 'Admin · read only'], ['Joined', user.created_at ? new Intl.DateTimeFormat('en-IN', {dateStyle:'medium'}).format(new Date(user.created_at)) : '—']].forEach(([labelText, valueText]) => {
            const row = document.createElement('tr'), label = document.createElement('th'), value = document.createElement('td');
            label.scope = 'row'; label.textContent = labelText; value.textContent = valueText || '—'; row.append(label, value); rows.append(row);
        });
        document.getElementById('userViewTitle').textContent = `User · ${user.name || ''}`;
    });
    userModal.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        const editing = trigger?.dataset.mode === 'edit';
        const user = editing ? JSON.parse(trigger.dataset.record) : {name:'', email:'', role:'admin'};
        title.textContent = editing ? 'Edit user' : 'Add user';
        userForm.action = editing ? `/users/${trigger.dataset.id}` : '/users';
        method.value = editing ? 'PUT' : 'POST';
        document.getElementById('userName').value = user.name || '';
        document.getElementById('userEmail').value = user.email || '';
        document.getElementById('userRole').value = user.role || 'admin';
        password.value = '';
        passwordConfirm.value = '';
        password.required = !editing;
        passwordConfirm.required = !editing;
        passwordHint.textContent = editing ? '(leave blank to keep current password)' : '(required)';
    });
    deleteModal.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        deleteForm.action = `/users/${trigger.dataset.id}`;
        deleteLabel.textContent = trigger.dataset.label;
    });
});
</script>
@endsection

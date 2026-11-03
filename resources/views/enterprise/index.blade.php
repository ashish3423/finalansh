@extends('layouts.newapp')

@section('title', $definition['title'].' | ANSH Enterprises')
@section('page-title', $definition['title'])

@section('content')
@if ($module === 'employees')
    @php
        $employeeDetailFields = [
            ['key' => 'employee_code', 'label' => 'Employee code'],
            ['key' => 'name', 'label' => 'Full name'],
            ['key' => 'role', 'label' => 'Role / designation'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'license_number', 'label' => 'Driving / professional licence #'],
            ['key' => 'joining_date', 'label' => 'Joining date', 'type' => 'date'],
            ['key' => 'daily_wages_rate', 'label' => 'Daily wages rate', 'type' => 'currency'],
            ['key' => 'photo_path', 'label' => 'Photo', 'type' => 'photo'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            ['key' => 'address', 'label' => 'Address'],
        ];
    @endphp
@else
    @php
        $recordDetailFields = collect($definition['fields'])->map(function ($field, $name) use ($options) {
            [$label, $type] = $field;
            return ['key' => $name, 'label' => $label, 'type' => $type,
                'options' => $type === 'select' ? ($field[3] ?? []) : ($options[$name] ?? [])];
        })->values();
    @endphp
@endif
<main class="employee-management-page enterprise-page">
    <div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div><div class="eyebrow">ANSH ENTERPRISES · OPERATIONS</div><h3 class="mb-1">{{ $definition['title'] }}</h3><p class="text-muted mb-0">Manage records, review details, and keep your service operations up to date.</p></div>
        @can('manage-records')<button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#recordModal" data-mode="create"><i class="bi bi-plus-lg me-1"></i> Add {{ $definition['singular'] }}</button>@endcan
    </div>

    @if (session('success')) <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif
    @if (session('error')) <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the following:</strong><ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <section class="card records-card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div><h5 class="mb-1">{{ $definition['title'] }} list</h5><div class="text-muted small">{{ number_format($rows->total()) }} {{ strtolower($definition['singular']) }} record(s)</div></div>
            <form method="GET" action="{{ route('resources.index', $module) }}" class="filter-bar d-flex flex-wrap gap-2">
                <div class="input-group search-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search records..."></div>
                @foreach ($definition['filters'] as $filter => $filterOptions)
                    <select class="form-select filter-select" name="{{ $filter }}" aria-label="Filter by {{ str_replace('_', ' ', $filter) }}"><option value="">All {{ str_replace('_', ' ', $filter) }}</option>@foreach ($filterOptions as $value => $label)<option value="{{ $value }}" @selected(request($filter) === $value)>{{ $label }}</option>@endforeach</select>
                @endforeach
                <button class="btn btn-light border" type="submit">Filter</button>
                @if (request()->query()) <a class="btn btn-light border" href="{{ route('resources.index', $module) }}">Reset</a> @endif
            </form>
        </div>
        <div class="table-responsive">
            <table class="table align-middle enterprise-table mb-0">
                <thead><tr>@foreach ($definition['columns'] as $label)<th>{{ $label }}</th>@endforeach <th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($definition['columns'] as $column => $label)
                            @php $value = $row->{$column} ?? null; @endphp
                            <td>
                                @if ($module === 'employees' && $column === 'name')
                                    <div class="d-flex align-items-center gap-2"><span class="employee-photo-frame">@if($row->photo_path)<img src="{{ asset('storage/'.$row->photo_path) }}" alt="{{ $row->name }}">@else<span>{{ mb_strtoupper(mb_substr($row->name, 0, 1)) }}</span>@endif</span><span class="fw-semibold">{{ $row->name }}</span></div>
                                @elseif ($column === 'status')
                                    <span class="status-pill status-{{ strtolower(str_replace('_', '-', $value ?? '')) }}">{{ ucwords(str_replace('_', ' ', $value ?? '')) }}</span>
                                @elseif (isset($relationLabels[$column]))
                                    <span class="fw-semibold">{{ $relationLabels[$column][$value] ?? '—' }}</span>
                                @elseif (in_array($column, ['service_type', 'payment_method', 'contract_category'], true))
                                    {{ ucwords(str_replace('_', ' ', $value ?? '—')) }}
                                @elseif (in_array($column, ['contract_value', 'monthly_salary', 'daily_wages_rate', 'monthly_rate', 'amount', 'total_amount', 'subtotal', 'tax_amount'], true))
                                    ₹{{ number_format((float) $value, 2) }}
                                @elseif (str_contains($column, 'date') && $value)
                                    {{ \Illuminate\Support\Carbon::parse($value)->format(str_contains($column, 'at') ? 'd M Y, h:i A' : 'd M Y') }}
                                @else
                                    {{ filled($value) ? $value : '—' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end text-nowrap">
                                @if ($module === 'employees')<button class="btn btn-sm btn-icon btn-view" type="button" title="View employee" data-bs-toggle="modal" data-bs-target="#employeeViewModal" data-record="{{ json_encode($row) }}"><i class="bi bi-eye"></i></button>
                                @else<button class="btn btn-sm btn-icon btn-view" type="button" title="View {{ strtolower($definition['singular']) }}" data-bs-toggle="modal" data-bs-target="#recordViewModal" data-record="{{ json_encode($row) }}"><i class="bi bi-eye"></i></button>@endif
                                @can('manage-records')
                                    <button class="btn btn-sm btn-icon btn-edit" type="button" title="Edit" data-bs-toggle="modal" data-bs-target="#recordModal" data-mode="edit" data-id="{{ $row->id }}" data-record="{{ json_encode($row) }}"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-icon btn-delete" type="button" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $row->id }}" data-label="{{ $row->{array_key_first($definition['columns'])} ?? $row->id }}"><i class="bi bi-trash3"></i></button>
                                @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($definition['columns']) + 1 }}" class="empty-state"><span class="empty-icon"><i class="bi {{ $definition['icon'] }}"></i></span><strong>No {{ strtolower($definition['title']) }} found</strong><span>Try adjusting your filters @can('manage-records')or add your first record.@else or clear the filters.@endcan</span>@can('manage-records')<button class="btn btn-primary btn-sm mt-2" type="button" data-bs-toggle="modal" data-bs-target="#recordModal" data-mode="create">Add {{ $definition['singular'] }}</button>@endcan</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($rows->hasPages()) <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2"><small class="text-muted">Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }}</small>{{ $rows->links() }}</div> @endif
    </section>
</main>

@can('manage-records')<div class="modal fade {{ $module === 'employees' ? 'employee-form-modal' : '' }}" id="recordModal" tabindex="-1" aria-labelledby="recordModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
        <form id="recordForm" class="{{ $module === 'employees' ? 'employee-form' : '' }}" method="POST" action="{{ route('resources.store', $module) }}" @if ($module === 'employees') enctype="multipart/form-data" @endif>
            @csrf <input type="hidden" name="_method" id="recordMethod" value="POST">
            <div class="modal-header"><div><div class="eyebrow">ANSH ENTERPRISES</div><h5 class="modal-title" id="recordModalTitle">Add {{ $definition['singular'] }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body {{ $module === 'employees' ? 'employee-modal-scroll' : '' }}">
                @if ($module === 'employees')<div class="alert alert-info small py-2 mb-3"><i class="bi bi-magic me-1"></i>Employee code is generated automatically when this employee is saved.</div>@endif
                <div class="row g-3">
                @foreach ($definition['fields'] as $name => $field)
                    @php [$label, $type] = $field; $isSelect = in_array($type, ['select', 'relation', 'relation_optional'], true); @endphp
                    <div class="col-md-{{ in_array($type, ['textarea', 'file'], true) ? '12' : '6' }}" @if ($type === 'edit-date') data-edit-only-field @endif>
                        <label class="form-label" for="field-{{ $name }}">{{ $label }}</label>
                        @if ($isSelect)
                            <select class="form-select" id="field-{{ $name }}" name="{{ $name }}" {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>
                                @if ($type === 'relation_optional') <option value="">Not assigned</option> @elseif ($type === 'relation') <option value="">Select {{ strtolower($label) }}</option> @endif
                                @foreach (($type === 'select' ? $field[3] : ($options[$name] ?? [])) as $value => $optionLabel)<option value="{{ $value }}">{{ $optionLabel }}</option>@endforeach
                            </select>
                        @elseif ($type === 'textarea')
                            <textarea class="form-control" id="field-{{ $name }}" name="{{ $name }}" rows="3" maxlength="3000"></textarea>
                        @elseif ($type === 'file')
                            <div class="d-flex align-items-center gap-3 mb-2"><img id="employeePhotoPreview" class="employee-photo-preview d-none" alt="Employee photo preview"><span id="employeePhotoPlaceholder" class="text-muted small">JPG, PNG or WebP · up to 2 MB</span></div>
                            <input class="form-control" id="field-{{ $name }}" type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp">
                        @elseif ($type === 'edit-date')
                            <input class="form-control" id="field-{{ $name }}" type="date" name="{{ $name }}">
                        @else
                            <input class="form-control" id="field-{{ $name }}" type="{{ $type }}" name="{{ $name }}" step="{{ $type === 'number' ? '0.01' : '' }}" {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>
                        @endif
                        @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div></div>
            <div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save {{ $definition['singular'] }}</button></div>
        </form>
    </div></div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
        <form id="deleteForm" method="POST" action="{{ route('resources.destroy', [$module, 0]) }}">
            @csrf @method('DELETE')
            <div class="modal-header"><div><div class="eyebrow">CONFIRM ACTION</div><h5 class="modal-title" id="deleteModalTitle">Delete {{ $definition['singular'] }}?</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body"><p class="mb-0 text-muted">This will permanently delete <strong class="text-body" id="deleteRecordLabel">this record</strong>. This action cannot be undone.</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i>Delete {{ $definition['singular'] }}</button></div>
        </form>
    </div></div>
</div>
@endcan
@if ($module === 'employees')
<div class="modal fade" id="employeeViewModal" tabindex="-1" aria-labelledby="employeeViewModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><div><div class="eyebrow">EMPLOYEE RECORD</div><h5 class="modal-title" id="employeeViewModalTitle">Employee details</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body p-0"><table class="table table-bordered employee-details-table mb-0"><tbody id="employeeDetailsRows"></tbody></table></div>
        <div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button></div>
    </div></div>
</div>
@else
<div class="modal fade" id="recordViewModal" tabindex="-1" aria-labelledby="recordViewModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><div><div class="eyebrow">RECORD DETAILS</div><h5 class="modal-title" id="recordViewModalTitle">{{ $definition['singular'] }} details</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body p-0"><table class="table table-bordered employee-details-table mb-0"><tbody id="recordDetailsRows"></tbody></table></div>
        <div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button></div>
    </div></div>
</div>
@endif
@endsection

@section('scripts')
@can('manage-records')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('recordModal');
    const form = document.getElementById('recordForm');
    const method = document.getElementById('recordMethod');
    const title = document.getElementById('recordModalTitle');
    const baseUrl = @json(url('/'.$module));
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteLabel = document.getElementById('deleteRecordLabel');
    const defaults = @json(collect($definition['fields'])->mapWithKeys(fn ($field, $name) => [$name => '']));
    const photoInput = document.getElementById('field-photo');
    const photoPreview = document.getElementById('employeePhotoPreview');
    const photoPlaceholder = document.getElementById('employeePhotoPlaceholder');

    modal.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        const editing = trigger?.dataset.mode === 'edit';
        const record = editing ? JSON.parse(trigger.dataset.record) : defaults;
        title.textContent = `${editing ? 'Edit' : 'Add'} {{ $definition['singular'] }}`;
        form.querySelectorAll('[data-edit-only-field]').forEach(field => field.classList.toggle('d-none', !editing));
        form.action = editing ? `${baseUrl}/${trigger.dataset.id}` : baseUrl;
        method.value = editing ? 'PUT' : 'POST';
        form.querySelectorAll('[name]').forEach(input => {
            if (input.name === '_token' || input.name === '_method') return;
            if (input.type === 'file') return;
            let value = record[input.name] ?? '';
            if (input.type === 'datetime-local' && value) value = String(value).replace(' ', 'T').slice(0, 16);
            input.value = value;
        });
        if (photoInput) {
            photoInput.value = '';
            const imageUrl = editing && record.photo_path ? `${@json(asset('storage'))}/${record.photo_path}` : '';
            photoPreview.src = imageUrl;
            photoPreview.classList.toggle('d-none', !imageUrl);
            photoPlaceholder.classList.toggle('d-none', !!imageUrl);
        }
    });
    if (photoInput) {
        photoInput.addEventListener('change', () => {
            const file = photoInput.files?.[0];
            if (!file) return;
            photoPreview.src = URL.createObjectURL(file);
            photoPreview.classList.remove('d-none');
            photoPlaceholder.classList.add('d-none');
        });
    }

    deleteModal.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        deleteForm.action = `${baseUrl}/${trigger.dataset.id}`;
        deleteLabel.textContent = trigger.dataset.label || 'this record';
    });
});
</script>
@endcan
@if ($module === 'employees')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('employeeViewModal');
    const rows = document.getElementById('employeeDetailsRows');
    const details = @json($employeeDetailFields);
    const storageUrl = @json(asset('storage'));

    modal.addEventListener('show.bs.modal', event => {
        const employee = JSON.parse(event.relatedTarget.dataset.record);
        document.getElementById('employeeViewModalTitle').textContent = `Employee · ${employee.employee_code || ''}`;
        rows.replaceChildren();

        details.forEach(field => {
            const row = document.createElement('tr');
            const label = document.createElement('th');
            const value = document.createElement('td');
            label.scope = 'row';
            label.textContent = field.label;
            let content = employee[field.key];

            if (field.type === 'photo') {
                if (content) {
                    const photo = document.createElement('img');
                    photo.src = `${storageUrl}/${content}`;
                    photo.alt = `${employee.name || 'Employee'} photo`;
                    photo.className = 'employee-detail-photo';
                    value.append(photo);
                } else {
                    value.textContent = 'No photo uploaded';
                }
            } else if (field.type === 'date' && content) {
                value.textContent = new Intl.DateTimeFormat('en-IN', {day:'2-digit', month:'short', year:'numeric'}).format(new Date(`${content}T00:00:00`));
            } else if (field.type === 'currency' && content !== null && content !== '') {
                value.textContent = `${new Intl.NumberFormat('en-IN', {style:'currency', currency:'INR'}).format(Number(content))} / day`;
            } else if (field.type === 'status' && content) {
                value.textContent = String(content).replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
            } else {
                value.textContent = content || '—';
            }

            row.append(label, value);
            rows.append(row);
        });
    });
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('recordViewModal');
    const rows = document.getElementById('recordDetailsRows');
    const fields = @json($recordDetailFields);
    modal.addEventListener('show.bs.modal', event => {
        const record = JSON.parse(event.relatedTarget.dataset.record);
        rows.replaceChildren();
        fields.forEach(field => {
            if (field.type === 'file') return;
            const row = document.createElement('tr');
            const label = document.createElement('th');
            const value = document.createElement('td');
            label.scope = 'row'; label.textContent = field.label;
            let content = record[field.key];
            if (content !== null && content !== undefined && content !== '') {
                if (field.type === 'select' || field.type.startsWith('relation')) content = field.options?.[content] ?? content;
                if (field.type === 'date') content = new Intl.DateTimeFormat('en-IN', {day:'2-digit', month:'short', year:'numeric'}).format(new Date(`${content}T00:00:00`));
                else if (field.type === 'datetime-local' || field.type === 'datetime') content = new Intl.DateTimeFormat('en-IN', {dateStyle:'medium', timeStyle:'short'}).format(new Date(String(content).replace(' ', 'T')));
                else if (field.type === 'number' && /amount|rate|value|salary|wage|cost/i.test(field.key)) content = new Intl.NumberFormat('en-IN', {style:'currency', currency:'INR'}).format(Number(content));
                else if (field.key === 'status' || field.key === 'service_type' || field.key === 'payment_method') content = String(content).replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
                value.textContent = content;
            } else value.textContent = '—';
            row.append(label, value); rows.append(row);
        });
    });
});
</script>
@endif
@endsection

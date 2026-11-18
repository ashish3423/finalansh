@extends('layouts.newapp')

@section('title', $definition['title'].' | ANSH Enterprises')
@section('page-title', $definition['title'])

@section('content')
@if ($module === 'employees')
    @php
        $employeeDetailFields = [
            ['key' => 'photo_path', 'label' => 'Photo', 'type' => 'photo'],
            ['key' => 'employee_code', 'label' => 'Employee code'],
            ['key' => 'name', 'label' => 'Full name'],
            ['key' => 'role', 'label' => 'Role / designation'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'license_number', 'label' => 'Driving / professional licence #'],
            ['key' => 'employee_category', 'label' => 'Employee category'],
            ['key' => 'work_location', 'label' => 'Client Location'],
            ['key' => 'department', 'label' => 'Department'],
            ['key' => 'aadhar_number', 'label' => 'Aadhar number'],
            ['key' => 'account_number', 'label' => 'Account number'],
            ['key' => 'ifsc_code', 'label' => 'IFSC code'],
            ['key' => 'uan_number', 'label' => 'UAN number'],
            ['key' => 'esi_number', 'label' => 'ESI number'],
            ['key' => 'ref_num1', 'label' => 'Reference number 1'],
            ['key' => 'ref_num2', 'label' => 'Reference number 2'],
            ['key' => 'id_card_status', 'label' => 'ID card status'],
            ['key' => 'dress_status', 'label' => 'Dress status'],
            ['key' => 'epf_exempt', 'label' => 'EPF exempt for this employee', 'type' => 'boolean'],
            ['key' => 'esi_exempt', 'label' => 'ESI exempt for this employee', 'type' => 'boolean'],
            ['key' => 'police_verification_status', 'label' => 'Police verification completed'],
            ['key' => 'police_verification_certificate_number', 'label' => 'Police verification certificate number'],
            ['key' => 'joining_date', 'label' => 'Joining date', 'type' => 'date'],
            ['key' => 'exit_date', 'label' => 'Date of exit', 'type' => 'date'],
            ['key' => 'daily_wages_rate', 'label' => 'Daily wages rate', 'type' => 'currency'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            ['key' => 'address', 'label' => 'Address'],
        ];
    @endphp
@else
    @php
        $recordDetailFields = collect($definition['fields'])->map(function ($field, $name) use ($options) {
            [$label, $type] = $field;
            return ['key' => $name, 'label' => $label, 'type' => $type,
                'options' => in_array($type, ['select', 'dynamic-select', 'conditional-select'], true) ? ($type === 'select' ? ($field[3] ?? []) : ($type === 'conditional-select' ? ($field[5] ?? []) : ($options[$name] ?? []))) : ($options[$name] ?? [])];
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
                <div class="input-group search-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input class="form-control" name="search" value="{{ request('search') }}" placeholder="{{ $module === 'deployments' ? 'Contract number, employee or vehicle' : 'Search records...' }}"></div>
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
                                @elseif (in_array($column, ['service_type', 'payment_method', 'contract_category', 'billing_cycle', 'category'], true))
                                    {{ ucwords(str_replace('_', ' ', $value ?? '—')) }}
                                @elseif (in_array($column, ['contract_value', 'monthly_cost', 'monthly_salary', 'daily_wages_rate', 'monthly_rate', 'amount', 'total_amount', 'subtotal', 'tax_amount'], true))
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
                                    @if ($module === 'deployments')
                                        @if ($row->employee_id && $row->vehicle_id)
                                            <button class="btn btn-sm btn-icon btn-view" type="button" title="Move employee" data-bs-toggle="modal" data-bs-target="#recordModal" data-mode="edit" data-resource-target="employee" data-id="{{ $row->id }}" data-record="{{ json_encode($row) }}"><i class="bi bi-person-up"></i></button>
                                            <button class="btn btn-sm btn-icon btn-view" type="button" title="Move vehicle" data-bs-toggle="modal" data-bs-target="#recordModal" data-mode="edit" data-resource-target="vehicle" data-id="{{ $row->id }}" data-record="{{ json_encode($row) }}"><i class="bi bi-truck"></i></button>
                                        @else
                                            <button class="btn btn-sm btn-icon btn-view" type="button" title="Move to another contract" data-bs-toggle="modal" data-bs-target="#recordModal" data-mode="edit" data-resource-target="{{ $row->employee_id ? 'employee' : 'vehicle' }}" data-id="{{ $row->id }}" data-record="{{ json_encode($row) }}"><i class="bi bi-arrow-left-right"></i></button>
                                        @endif
                                    @endif
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
                @if ($module === 'deployments')
                    <input type="hidden" id="deploymentMoveResource" name="move_resource" value="">
                    <div class="col-md-6">
                        <label class="form-label" for="deploymentContract">Active contract</label>
                        <select class="form-select" id="deploymentContract" name="contract_id" data-validation-rules="required|exists:contracts,id" required><option value="">Select contract</option>
                            @foreach ($deploymentContracts as $contract)<option value="{{ $contract->id }}" data-service-type="{{ $contract->service_type }}">{{ $contract->contract_number }} · {{ ucfirst($contract->service_type) }}</option>@endforeach
                        </select>
                        <div class="invalid-feedback d-none" data-client-error-for="contract_id" aria-live="polite"></div>
                    </div>
                    <div class="col-md-6"><label class="form-label" for="deploymentServiceType">Service type</label><input class="form-control" id="deploymentServiceType" value="Select a contract" readonly></div>
                    <div class="col-12" id="deploymentEmployeeSection">
                        <div class="d-flex align-items-center justify-content-between mb-2"><div><label class="form-label mb-0">Manpower / crew</label><div class="text-muted small">Employees already assigned to another active contract are unavailable.</div></div><button class="btn btn-sm btn-light border" id="addDeploymentEmployee" type="button"><i class="bi bi-plus-lg me-1"></i>Add employee</button></div>
                        <div class="vstack gap-2" id="deploymentEmployeeRows"></div>
                    </div>
                    <div class="col-12" id="deploymentVehicleSection">
                        <div class="d-flex align-items-center justify-content-between mb-2"><div><label class="form-label mb-0">Ambulances / cabs</label><div class="text-muted small">Select one or more available vehicles.</div></div><button class="btn btn-sm btn-light border" id="addDeploymentVehicle" type="button"><i class="bi bi-plus-lg me-1"></i>Add vehicle</button></div>
                        <div class="vstack gap-2" id="deploymentVehicleRows"></div>
                    </div>
                    <template id="deploymentEmployeeTemplate"><div class="deployment-assignment-row employee-assignment-row row g-2 align-items-end"><div class="col-md-7"><label class="form-label">Employee</label><select class="form-select deployment-employee-select"><option value="">Select employee</option>@foreach ($deploymentEmployees as $employee)<option value="{{ $employee->id }}" data-assigned-deployment="{{ $assignedEmployeeRows[$employee->id] ?? '' }}">{{ $employee->employee_code }} — {{ $employee->name }} ({{ $employee->role }})</option>@endforeach</select><div class="invalid-feedback d-none" data-client-error aria-live="polite"></div></div><div class="col-md-4 deployment-position-wrap"><label class="form-label">Crew role</label><select class="form-select deployment-position-select"><option value="">Select role</option><option value="driver">Driver</option><option value="assistant">Assistant</option><option value="paramedic">Paramedic</option><option value="attendant">Attendant</option><option value="other">Other</option></select><div class="invalid-feedback d-none" data-client-error aria-live="polite"></div></div><div class="col-md-1 text-end"><button class="btn btn-icon btn-delete remove-deployment-row" type="button" aria-label="Remove employee"><i class="bi bi-x-lg"></i></button></div></div></template>
                    <template id="deploymentVehicleTemplate"><div class="deployment-assignment-row row g-2 align-items-end"><div class="col-11"><label class="form-label">Vehicle</label><select class="form-select deployment-vehicle-select"><option value="">Select vehicle</option>@foreach ($deploymentVehicles as $vehicle)<option value="{{ $vehicle->id }}" data-service-type="{{ $vehicle->service_type }}" data-assigned-deployment="{{ $assignedVehicleRows[$vehicle->id] ?? '' }}">{{ $vehicle->registration_number }}{{ $vehicle->ambulance_model ? ' — '.($vehicle->ambulance_category ? $vehicle->ambulance_category.' · ' : '').$vehicle->ambulance_model : '' }}{{ $vehicle->year ? ' · '.$vehicle->year : '' }} ({{ ucfirst($vehicle->service_type) }})</option>@endforeach</select><div class="invalid-feedback d-none" data-client-error aria-live="polite"></div></div><div class="col-1 text-end"><button class="btn btn-icon btn-delete remove-deployment-row" type="button" aria-label="Remove vehicle"><i class="bi bi-x-lg"></i></button></div></div></template>
                @else
                @foreach ($definition['fields'] as $name => $field)
                    @php [$label, $type] = $field; $isSelect = in_array($type, ['select', 'edit-select', 'conditional-select', 'relation', 'relation_optional', 'dynamic-select', 'conditional-relation'], true); @endphp
                    @if ($type === 'edit-select')<input type="hidden" id="field-{{ $name }}-create" name="{{ $name }}" value="active" data-create-status>@endif
                    @if ($module === 'vehicles' && $name === 'ambulance_type_id')
                        <div class="col-md-6 d-none" id="vehicleModelCategoryField">
                            <label class="form-label" id="vehicleModelCategoryLabel" for="vehicleModelCategory">Ambulance type</label>
                            <select class="form-select" id="vehicleModelCategory" name="vehicle_model_category">
                                <option value="">Select ambulance type</option>
                            </select>
                            <div class="invalid-feedback d-none" data-client-error aria-live="polite"></div>
                        </div>
                    @endif
                    <div class="col-md-{{ in_array($type, ['textarea', 'file', 'conditional-file'], true) ? '12' : '6' }} @if (in_array($type, ['conditional-text', 'conditional-select', 'conditional-file', 'conditional-relation', 'conditional-date'], true)) d-none @endif @if (in_array($type, ['edit-date', 'edit-select'], true)) d-none @endif" data-client-unique-values="{{ json_encode($uniqueValues[$name] ?? []) }}" @if (in_array($type, ['edit-date', 'edit-select'], true)) data-edit-only-field @endif @if (in_array($type, ['conditional-text', 'conditional-select', 'conditional-file', 'conditional-relation', 'conditional-date'], true)) data-condition-field="field-{{ $field[3] }}" data-condition-value="{{ $field[4] }}" data-condition-required="{{ in_array($type, ['conditional-file', 'conditional-relation'], true) || str_starts_with($field[2], 'required_if:') ? 'true' : 'false' }}" data-condition-preserve="{{ $type === 'conditional-date' ? 'true' : 'false' }}" @endif>
                        <label class="form-label" for="field-{{ $name }}" @if ($module === 'ambulance-types' && $name === 'category') id="ambulanceCategoryLabel" @endif>{{ $label }}</label>
                        @if ($isSelect)
                            <select class="form-select" id="field-{{ $name }}" name="{{ $name }}" data-validation-rules="{{ $field[2] }}" @if ($module === 'ambulance-types' && $name === 'category') data-ambulance-options="{{ json_encode($field[5]) }}" data-cab-options="{{ json_encode($field[6]) }}" @endif @if ($type === 'edit-select') data-edit-only-select disabled @endif {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>
                                @if ($type === 'relation_optional') <option value="">Not assigned</option> @elseif (in_array($type, ['relation', 'dynamic-select', 'conditional-relation', 'conditional-select'], true)) <option value="">Select {{ strtolower($label) }}</option> @endif
                                @foreach (in_array($type, ['select', 'edit-select'], true) ? $field[3] : ($type === 'conditional-select' ? $field[5] : ($options[$name] ?? [])) as $value => $optionLabel)<option value="{{ $value }}" @if ($module === 'vehicles' && $name === 'ambulance_type_id' && isset($vehicleModelMeta[$value])) data-service-type="{{ $vehicleModelMeta[$value]['service_type'] }}" data-category="{{ $vehicleModelMeta[$value]['category'] }}" @endif @if ($module === 'contracts' && $name === 'government_client_id' && isset($clientDetails[$value])) data-client-department="{{ $clientDetails[$value]->department }}" data-client-address="{{ $clientDetails[$value]->address }}" @endif>{{ $optionLabel }}</option>@endforeach
                            </select>
                        @elseif ($type === 'switch')
                            <input type="hidden" name="{{ $name }}" value="{{ $field[4] ?? 0 }}">
                            <div class="form-check form-switch pt-2"><input class="form-check-input" id="field-{{ $name }}" type="checkbox" name="{{ $name }}" data-validation-rules="{{ $field[2] }}" value="{{ $field[3] ?? 1 }}" data-checked-value="{{ $field[3] ?? 1 }}" data-unchecked-value="{{ $field[4] ?? 0 }}" role="switch" aria-label="{{ $label }}"></div>
                        @elseif ($type === 'textarea')
                            <textarea class="form-control" id="field-{{ $name }}" name="{{ $name }}" data-validation-rules="{{ $field[2] }}" rows="3" maxlength="3000"></textarea>
                        @elseif ($type === 'file')
                            <div class="d-flex align-items-center gap-3 mb-2"><img id="employeePhotoPreview" class="employee-photo-preview d-none" alt="Employee photo preview"><span id="employeePhotoPlaceholder" class="text-muted small">JPG, PNG or WebP · up to 2 MB</span></div>
                            <input class="form-control" id="field-{{ $name }}" type="file" name="{{ $name }}" data-validation-rules="{{ $field[2] }}" accept="image/jpeg,image/png,image/webp">
                        @elseif ($type === 'conditional-file')
                            <input class="form-control" id="field-{{ $name }}" type="file" name="{{ $name }}" data-validation-rules="{{ $field[2] }}" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                            <small class="form-text text-muted">PDF or Word file, up to 10 MB. Leave empty to keep the current file.</small>
                            <small class="d-block" data-existing-file-for="{{ $name }}"></small>
                        @elseif (in_array($type, ['edit-date', 'conditional-date'], true))
                            <input class="form-control" id="field-{{ $name }}" type="date" name="{{ $name }}" data-validation-rules="{{ $field[2] }}">
                        @elseif ($type === 'conditional-text')
                            <input class="form-control" id="field-{{ $name }}" type="text" name="{{ $name }}" data-validation-rules="{{ $field[2] }}" maxlength="{{ str_contains($field[2], 'max:120') ? 120 : 100 }}">
                        @else
                            <input class="form-control" id="field-{{ $name }}" type="{{ $type }}" name="{{ $name }}" data-validation-rules="{{ $field[2] }}" @if ($type === 'number') step="{{ str_contains($field[2], 'integer') ? '1' : '0.01' }}" @if (str_contains($field[2], 'min:0')) min="0" @endif @endif {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>
                        @endif
                        <div class="invalid-feedback d-none" data-client-error-for="{{ $name }}" aria-live="polite"></div>
                        @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                @endforeach
                @endif
                @if ($module === 'deployments')<div class="col-12"><div class="invalid-feedback d-none" id="deploymentResourceError" aria-live="polite">Add at least one employee or vehicle.</div></div>@endif
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
    const employeeStorageUrl = @json(asset('storage'));
    const deploymentMode = @json($module === 'deployments');
    const contractMode = @json($module === 'contracts');
    const vehicleMode = @json($module === 'vehicles');
    const vehicleModelSelect = document.getElementById('field-ambulance_type_id');
    const vehicleModelCategorySelect = document.getElementById('vehicleModelCategory');
    const vehicleModelCategoryField = document.getElementById('vehicleModelCategoryField');
    const vehicleModelCategories = @json($vehicleModelCategories);
    const vehicleModelOptions = vehicleMode && vehicleModelSelect
        ? [...vehicleModelSelect.options].map(option => option.cloneNode(true))
        : [];
    const ambulanceMasterMode = @json($module === 'ambulance-types');
    const masterCategorySelect = document.getElementById('field-category');
    const deploymentContract = document.getElementById('deploymentContract');
    const deploymentServiceType = document.getElementById('deploymentServiceType');
    const employeeRows = document.getElementById('deploymentEmployeeRows');
    const vehicleRows = document.getElementById('deploymentVehicleRows');
    let currentDeploymentId = '';
    let currentDeploymentRecord = null;

    const syncVehicleModelOptions = () => {
        if (!vehicleMode || !vehicleModelSelect || !vehicleModelCategorySelect) return;
        const serviceType = document.getElementById('field-service_type')?.value || '';
        const categoryOptions = vehicleModelCategories[serviceType] || [];
        const selectedModelId = vehicleModelSelect.value;
        const selectedModel = vehicleModelOptions.find(option => option.value === selectedModelId);
        const currentCategory = vehicleModelCategorySelect.value;
        const modelCategory = selectedModel?.dataset.serviceType === serviceType ? selectedModel.dataset.category : '';
        const selectedCategory = categoryOptions.includes(currentCategory)
            ? currentCategory
            : (categoryOptions.includes(modelCategory) ? modelCategory : '');
        const typeLabel = serviceType === 'cab' ? 'Cab type' : 'Ambulance type';
        vehicleModelCategoryField.classList.toggle('d-none', !['ambulance', 'cab'].includes(serviceType));
        document.getElementById('vehicleModelCategoryLabel').textContent = typeLabel;
        vehicleModelCategorySelect.required = ['ambulance', 'cab'].includes(serviceType);
        vehicleModelCategorySelect.replaceChildren(new Option(`Select ${typeLabel.toLowerCase()}`, ''));
        categoryOptions.forEach(category => vehicleModelCategorySelect.add(new Option(category, category)));
        vehicleModelCategorySelect.value = selectedCategory;
        const matchingModels = vehicleModelOptions.filter(option =>
            !option.dataset.serviceType || (option.dataset.serviceType === serviceType && option.dataset.category === selectedCategory)
        );
        vehicleModelSelect.replaceChildren(...matchingModels.map(option => option.cloneNode(true)));
        vehicleModelSelect.required = ['ambulance', 'cab'].includes(serviceType);
        vehicleModelSelect.value = matchingModels.some(option => option.value === selectedModelId) ? selectedModelId : '';
    };

    const syncAmbulanceMasterOptions = (preferredValue = masterCategorySelect?.value || '') => {
        if (!ambulanceMasterMode || !masterCategorySelect) return;
        const isCab = document.getElementById('field-service_type')?.value === 'cab';
        const choices = JSON.parse(isCab ? masterCategorySelect.dataset.cabOptions : masterCategorySelect.dataset.ambulanceOptions);
        const label = document.getElementById('ambulanceCategoryLabel');
        if (label) label.textContent = isCab ? 'Cab type' : 'Ambulance type';
        masterCategorySelect.replaceChildren(new Option(`Select ${isCab ? 'cab' : 'ambulance'} type`, ''));
        Object.entries(choices).forEach(([value, text]) => masterCategorySelect.add(new Option(text, value)));
        masterCategorySelect.value = Object.hasOwn(choices, preferredValue) ? preferredValue : '';
    };

    const validationFields = [...form.querySelectorAll('[data-validation-rules]')];
    let currentFormRecord = null;
    let validationSubmitted = false;
    form.noValidate = true;

    const setFieldError = (input, message = '') => {
        const error = [...form.querySelectorAll('[data-client-error-for]')].find(item => item.dataset.clientErrorFor === input.name)
            || input.parentElement?.querySelector('[data-client-error]');
        input.classList.toggle('is-invalid', !!message);
        input.setAttribute('aria-invalid', message ? 'true' : 'false');
        if (error) {
            error.textContent = message;
            error.classList.toggle('d-none', !message);
            error.classList.toggle('d-block', !!message);
            if (message) input.setAttribute('aria-describedby', error.id || (error.id = `error-${input.name || input.id}`));
            else input.removeAttribute('aria-describedby');
        }
        return !!message;
    };

    const validateField = input => {
        const wrapper = input.closest('[data-condition-field], [data-edit-only-field], .col-md-6, .col-md-12');
        if (input.disabled || wrapper?.classList.contains('d-none')) return setFieldError(input);
        const rules = (input.dataset.validationRules || '').split('|').filter(Boolean);
        const value = input.type === 'checkbox' ? (input.checked ? input.dataset.checkedValue : input.dataset.uncheckedValue) : input.value.trim();
        const file = input.type === 'file' ? input.files?.[0] : null;
        const requiredIf = rules.find(rule => rule.startsWith('required_if:'));
        const requiredIfParts = requiredIf ? requiredIf.slice('required_if:'.length).split(',') : [];
        const requiredIfField = requiredIfParts.shift();
        const requiredIfController = requiredIfField ? document.getElementById(`field-${requiredIfField}`) : null;
        const requiredIfValue = requiredIfController?.type === 'checkbox'
            ? (requiredIfController.checked ? requiredIfController.dataset.checkedValue : requiredIfController.dataset.uncheckedValue)
            : requiredIfController?.value;
        const required = rules.includes('required')
            || (input.type === 'file' && wrapper?.dataset.conditionRequired === 'true' && !wrapper.querySelector('[data-existing-file-for] a'))
            || (requiredIfController && requiredIfParts.includes(String(requiredIfValue)));

        if (required && (input.type === 'file' ? !file : value === '')) return setFieldError(input, 'This field is required.');

        const prohibitedUnless = rules.find(rule => rule.startsWith('prohibited_unless:'));
        if (prohibitedUnless && (value !== '' || file)) {
            const [fieldName, ...allowedValues] = prohibitedUnless.slice('prohibited_unless:'.length).split(',');
            const controller = document.getElementById(`field-${fieldName}`);
            const controllerValue = controller?.type === 'checkbox'
                ? (controller.checked ? controller.dataset.checkedValue : controller.dataset.uncheckedValue)
                : controller?.value;
            if (!allowedValues.includes(String(controllerValue))) return setFieldError(input, 'This field is not allowed for the selected option.');
        }

        if (input.type === 'file' && file) {
            const extension = file.name.split('.').pop().toLowerCase();
            const mimeRule = rules.find(rule => rule.startsWith('mimes:'));
            if (mimeRule && !mimeRule.slice(6).split(',').includes(extension)) return setFieldError(input, `Unsupported file type. Allowed types: ${mimeRule.slice(6).split(',').join(', ')}.`);
            const maxRule = rules.find(rule => rule.startsWith('max:'));
            if (maxRule && file.size > Number(maxRule.slice(4)) * 1024) return setFieldError(input, `The file must be no larger than ${Number(maxRule.slice(4)) / 1024} MB.`);
        }
        if (value !== '' && rules.includes('email') && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return setFieldError(input, 'Enter a valid email address.');
        if (value !== '' && rules.includes('date') && Number.isNaN(Date.parse(value))) return setFieldError(input, 'Enter a valid date.');
        if (value !== '' && rules.includes('date_format:H:i') && !/^([01]\d|2[0-3]):[0-5]\d$/.test(value)) return setFieldError(input, 'Enter a valid time.');

        if (value !== '' && (rules.includes('numeric') || rules.includes('integer') || input.type === 'number')) {
            if (!Number.isFinite(Number(value))) return setFieldError(input, 'Enter a valid number.');
            if (rules.includes('integer') && !Number.isInteger(Number(value))) return setFieldError(input, 'Enter a whole number.');
        }
        const minRule = rules.find(rule => rule.startsWith('min:'));
        const maxRule = rules.find(rule => rule.startsWith('max:'));
        if (value !== '' && input.type === 'number') {
            if (minRule && Number(value) < Number(minRule.slice(4))) return setFieldError(input, `The value must be at least ${minRule.slice(4)}.`);
            if (maxRule && Number(value) > Number(maxRule.slice(4))) return setFieldError(input, `The value must be no greater than ${maxRule.slice(4)}.`);
        } else if (value !== '' && maxRule && value.length > Number(maxRule.slice(4))) {
            return setFieldError(input, `Use no more than ${maxRule.slice(4)} characters.`);
        } else if (value !== '' && minRule && value.length < Number(minRule.slice(4))) {
            return setFieldError(input, `Use at least ${minRule.slice(4)} characters.`);
        }
        const greaterThanRule = rules.find(rule => rule.startsWith('gt:'));
        if (value !== '' && greaterThanRule && Number(value) <= Number(greaterThanRule.slice(3))) return setFieldError(input, `The value must be greater than ${greaterThanRule.slice(3)}.`);

        const inRule = rules.find(rule => rule.startsWith('in:'));
        if (value !== '' && inRule && !inRule.slice(3).split(',').includes(value)) return setFieldError(input, 'Choose a valid option.');
        if (input.tagName === 'SELECT' && value && ![...input.options].some(option => option.value === value && !option.disabled)) return setFieldError(input, 'Choose a valid option.');
        if (value !== '' && rules.some(rule => rule.startsWith('exists:')) && input.tagName === 'SELECT'
            && ![...input.options].some(option => option.value === value && !option.disabled)) return setFieldError(input, 'Choose a valid option.');

        const uniqueRule = rules.find(rule => rule.startsWith('unique:'));
        if (value !== '' && uniqueRule) {
            const existingValues = JSON.parse(wrapper?.dataset.clientUniqueValues || '[]');
            const normalizedValue = value.toLocaleLowerCase();
            const unchangedOnEdit = currentFormRecord && String(currentFormRecord[input.name] ?? '').toLocaleLowerCase() === normalizedValue;
            if (!unchangedOnEdit && existingValues.some(existing => String(existing).toLocaleLowerCase() === normalizedValue)) return setFieldError(input, 'This value is already in use.');
        }
        const comparisonRule = rules.find(rule => rule.startsWith('after_or_equal:'));
        if (value !== '' && comparisonRule) {
            const otherField = comparisonRule.slice('after_or_equal:'.length);
            const otherInput = document.getElementById(`field-${otherField}`);
            if (otherInput?.value && value < otherInput.value) return setFieldError(input, `This date must be on or after ${otherInput.labels?.[0]?.textContent || otherField}.`);
        }
        return setFieldError(input);
    };

    const validateVehicleCategory = () => {
        if (!vehicleMode || !vehicleModelCategorySelect) return false;
        const serviceType = document.getElementById('field-service_type')?.value;
        const required = ['ambulance', 'cab'].includes(serviceType);
        const allowedCategories = vehicleModelCategories[serviceType] || [];
        const error = required && !vehicleModelCategorySelect.value
            ? `Select a ${serviceType === 'cab' ? 'cab' : 'ambulance'} type.`
            : (required && !allowedCategories.includes(vehicleModelCategorySelect.value) ? 'Choose a valid vehicle type.' : '');
        return setFieldError(vehicleModelCategorySelect, error);
    };

    const validateDeploymentFields = () => {
        if (!deploymentMode) return false;
        let invalid = validationFields.map(input => validateField(input)).some(Boolean);
        const contractType = deploymentContract.selectedOptions[0]?.dataset.serviceType || '';
        const selectedEmployees = [];
        const selectedVehicles = [];
        const seenEmployees = new Set();
        const seenVehicles = new Set();
        employeeRows.querySelectorAll('.employee-assignment-row').forEach(row => {
            const employee = row.querySelector('.deployment-employee-select');
            const role = row.querySelector('.deployment-position-select');
            const employeeValue = employee.value;
            const employeeInvalid = !!employeeValue && (!employee.selectedOptions[0] || employee.selectedOptions[0].disabled
                || ![...employee.options].some(option => option.value === employeeValue && !option.disabled));
            const duplicateEmployee = !!employeeValue && seenEmployees.has(employeeValue);
            const roleRequired = ['ambulance', 'cab'].includes(contractType) && !!employeeValue;
            const roleAllowed = !role.value || [...role.options].some(option => option.value === role.value && !option.disabled);
            const roleInvalid = (roleRequired && !role.value) || !roleAllowed;
            setFieldError(employee, employeeInvalid ? 'Choose an available employee.' : (duplicateEmployee ? 'Choose this employee only once.' : ''));
            setFieldError(role, roleInvalid ? (roleRequired && !role.value ? 'Choose a crew role.' : 'Choose a valid crew role.') : '');
            if (employeeInvalid || duplicateEmployee || roleInvalid) invalid = true;
            if (employeeValue) {
                selectedEmployees.push(employeeValue);
                seenEmployees.add(employeeValue);
            }
        });
        vehicleRows.querySelectorAll('.deployment-assignment-row').forEach(row => {
            const vehicle = row.querySelector('.deployment-vehicle-select');
            const vehicleValue = vehicle.value;
            const vehicleInvalid = !!vehicleValue && (!vehicle.selectedOptions[0] || vehicle.selectedOptions[0].disabled
                || ![...vehicle.options].some(option => option.value === vehicleValue && !option.disabled));
            const duplicateVehicle = !!vehicleValue && seenVehicles.has(vehicleValue);
            setFieldError(vehicle, vehicleInvalid ? 'Choose an available vehicle matching the service type.' : (duplicateVehicle ? 'Choose this vehicle only once.' : ''));
            if (vehicleInvalid || duplicateVehicle) invalid = true;
            if (vehicleValue) {
                selectedVehicles.push(vehicleValue);
                seenVehicles.add(vehicleValue);
            }
        });
        const resourceError = document.getElementById('deploymentResourceError');
        const missingResource = selectedEmployees.length + selectedVehicles.length === 0;
        resourceError.classList.toggle('d-none', !missingResource);
        resourceError.classList.toggle('d-block', missingResource);
        if (missingResource) invalid = true;
        return invalid;
    };

    const validateForm = () => {
        let invalid = validationFields.map(input => validateField(input)).some(Boolean);
        if (vehicleMode && validateVehicleCategory()) invalid = true;
        if (deploymentMode && validateDeploymentFields()) invalid = true;
        if (invalid) {
            const firstInvalid = form.querySelector('.is-invalid') || document.querySelector('#deploymentResourceError:not(.d-none)');
            firstInvalid?.focus?.({ preventScroll: true });
            firstInvalid?.scrollIntoView?.({ behavior: 'smooth', block: 'center' });
        }
        return invalid;
    };

    form.noValidate = true;
    form.addEventListener('submit', event => {
        validationSubmitted = true;
        if (validateForm()) event.preventDefault();
    });
    form.addEventListener('input', () => { if (validationSubmitted) validateForm(); });
    form.addEventListener('change', () => { if (validationSubmitted) queueMicrotask(validateForm); });

    const addEmployeeAssignmentRow = (employeeId = '', position = '', editing = false) => {
        const row = document.getElementById('deploymentEmployeeTemplate').content.firstElementChild.cloneNode(true);
        const select = row.querySelector('.deployment-employee-select');
        const role = row.querySelector('.deployment-position-select');
        select.value = employeeId;
        const normalizedRole = String(position || '').trim().toLowerCase();
        role.value = ['driver', 'assistant', 'paramedic', 'attendant', 'other'].includes(normalizedRole)
            ? normalizedRole
            : (normalizedRole ? 'other' : '');
        if (editing) { select.name = 'employee_id'; role.name = 'position'; select.required = true; }
        row.querySelector('.remove-deployment-row').classList.toggle('d-none', editing);
        employeeRows.append(row);
        return row;
    };
    const addVehicleAssignmentRow = (vehicleId = '', editing = false) => {
        const row = document.getElementById('deploymentVehicleTemplate').content.firstElementChild.cloneNode(true);
        const select = row.querySelector('.deployment-vehicle-select');
        select.value = vehicleId;
        if (editing) { select.name = 'vehicle_id'; select.required = true; }
        row.querySelector('.remove-deployment-row').classList.toggle('d-none', editing);
        vehicleRows.append(row);
        return row;
    };
    const syncDeploymentForm = (record = null) => {
        if (!deploymentMode) return;
            const serviceType = deploymentContract.selectedOptions[0]?.dataset.serviceType || '';
        deploymentServiceType.value = serviceType ? serviceType.charAt(0).toUpperCase() + serviceType.slice(1) : 'Select a contract';
        const activeRecord = record || currentDeploymentRecord;
        const editing = !!activeRecord;
        const activeRecordId = String(activeRecord?.id || currentDeploymentId || '');
        const employeeEdit = editing && !!activeRecord.employee_id;
        const vehicleEdit = editing && !!activeRecord.vehicle_id;
        document.getElementById('deploymentVehicleSection').classList.toggle('d-none', serviceType === 'manpower' || employeeEdit);
        document.getElementById('deploymentEmployeeSection').classList.toggle('d-none', vehicleEdit);
        document.getElementById('addDeploymentEmployee').classList.toggle('d-none', editing);
        document.getElementById('addDeploymentVehicle').classList.toggle('d-none', editing);
        const selectedEmployeeIds = [...employeeRows.querySelectorAll('.deployment-employee-select')].map(select => select.value).filter(Boolean);
        employeeRows.querySelectorAll('.employee-assignment-row').forEach(row => {
            const roleWrap = row.querySelector('.deployment-position-wrap');
            roleWrap.classList.toggle('d-none', !['ambulance', 'cab'].includes(serviceType));
            const select = row.querySelector('.deployment-employee-select');
            const roleSelect = row.querySelector('.deployment-position-select');
            roleSelect.required = ['ambulance', 'cab'].includes(serviceType) && !!select.value;
            select.querySelectorAll('option[data-assigned-deployment]').forEach(option => {
                const assignedElsewhere = !!option.dataset.assignedDeployment && option.dataset.assignedDeployment !== activeRecordId;
                const selectedInAnotherRow = selectedEmployeeIds.includes(option.value) && option.value !== select.value;
                option.disabled = assignedElsewhere || selectedInAnotherRow;
            });
            if (editing) { select.name = 'employee_id'; roleSelect.name = 'position'; }
            else { select.name = `employee_assignments[${[...employeeRows.children].indexOf(row)}][employee_id]`; roleSelect.name = `employee_assignments[${[...employeeRows.children].indexOf(row)}][position]`; }
        });
        const selectedVehicleIds = [...vehicleRows.querySelectorAll('.deployment-vehicle-select')].map(select => select.value).filter(Boolean);
        vehicleRows.querySelectorAll('.deployment-vehicle-select').forEach(select => {
            select.querySelectorAll('option[data-service-type]').forEach(option => {
                const assignedElsewhere = !!option.dataset.assignedDeployment && option.dataset.assignedDeployment !== activeRecordId;
                const selectedInAnotherRow = selectedVehicleIds.includes(option.value) && option.value !== select.value;
                option.disabled = assignedElsewhere || selectedInAnotherRow || option.dataset.serviceType !== serviceType;
            });
            select.name = editing ? 'vehicle_id' : 'vehicle_ids[]';
        });
    };

    if (deploymentMode) {
        document.getElementById('addDeploymentEmployee').addEventListener('click', () => { addEmployeeAssignmentRow(); syncDeploymentForm(); });
        document.getElementById('addDeploymentVehicle').addEventListener('click', () => { addVehicleAssignmentRow(); syncDeploymentForm(); });
        modal.addEventListener('click', event => {
            if (event.target.closest('.remove-deployment-row')) {
                event.target.closest('.deployment-assignment-row').remove();
                syncDeploymentForm();
            }
        });
        deploymentContract.addEventListener('change', () => {
            const selectedService = deploymentContract.selectedOptions[0]?.dataset.serviceType || '';
            vehicleRows.querySelectorAll('.deployment-vehicle-select').forEach(select => { if (select.selectedOptions[0] && select.selectedOptions[0].dataset.serviceType !== selectedService) select.value = ''; });
            syncDeploymentForm();
        });
        modal.addEventListener('change', event => {
            if (event.target.matches('.deployment-employee-select, .deployment-vehicle-select')) syncDeploymentForm();
        });
        form.addEventListener('submit', () => {
            employeeRows.querySelectorAll('.employee-assignment-row').forEach(row => { if (!row.querySelector('.deployment-employee-select').value && !row.querySelector('.deployment-position-select').value) row.remove(); });
            vehicleRows.querySelectorAll('.deployment-assignment-row').forEach(row => { if (!row.querySelector('.deployment-vehicle-select').value) row.remove(); });
            syncDeploymentForm();
        });
    }

    const syncConditionalFields = () => form.querySelectorAll('[data-condition-field]').forEach(wrapper => {
        const controller = document.getElementById(wrapper.dataset.conditionField);
        const input = wrapper.querySelector('input, select, textarea');
        const controllerValue = controller?.type === 'checkbox'
            ? (controller.checked ? controller.dataset.checkedValue : controller.dataset.uncheckedValue)
            : controller?.value;
        const visible = wrapper.dataset.conditionValue.split('|').includes(String(controllerValue));
        wrapper.classList.toggle('d-none', !visible);
        if (input && wrapper.dataset.conditionPreserve === 'true') input.disabled = !visible;
        if (input && wrapper.dataset.conditionRequired === 'true') {
            const hasExistingFile = !!wrapper.querySelector('[data-existing-file-for] a');
            input.required = visible && !(input.type === 'file' && hasExistingFile);
        }
        if (!visible && input && wrapper.dataset.conditionPreserve !== 'true') input.value = '';
    });

    modal.addEventListener('show.bs.modal', event => {
        validationSubmitted = false;
        validationFields.forEach(input => setFieldError(input));
        if (vehicleModelCategorySelect) setFieldError(vehicleModelCategorySelect);
        document.getElementById('deploymentResourceError')?.classList.add('d-none');
        if (vehicleMode && vehicleModelSelect) vehicleModelSelect.replaceChildren(...vehicleModelOptions.map(option => option.cloneNode(true)));
        const trigger = event.relatedTarget;
        const editing = trigger?.dataset.mode === 'edit';
        const record = editing ? JSON.parse(trigger.dataset.record) : defaults;
        currentFormRecord = editing ? record : null;
        let deploymentDisplayRecord = record;
        if (deploymentMode) {
            employeeRows.replaceChildren();
            vehicleRows.replaceChildren();
            deploymentContract.value = '';
            currentDeploymentId = editing ? String(record.id || '') : '';
            const resourceTarget = trigger?.dataset.resourceTarget || (record.employee_id ? 'employee' : 'vehicle');
            if (editing && record.employee_id && record.vehicle_id) {
                deploymentDisplayRecord = {
                    ...record,
                    employee_id: resourceTarget === 'employee' ? record.employee_id : null,
                    vehicle_id: resourceTarget === 'vehicle' ? record.vehicle_id : null,
                };
            }
            currentDeploymentRecord = editing ? deploymentDisplayRecord : null;
            const moveResource = document.getElementById('deploymentMoveResource');
            if (moveResource) moveResource.value = editing ? resourceTarget : '';
            if (!editing) { addEmployeeAssignmentRow(); addVehicleAssignmentRow(); }
        }
        title.textContent = `${editing ? (trigger?.dataset.resourceTarget ? 'Move' : 'Edit') : 'Add'} {{ $definition['singular'] }}`;
        form.querySelectorAll('[data-edit-only-field]').forEach(field => {
            field.classList.toggle('d-none', !editing);
            const select = field.querySelector('[data-edit-only-select]');
            if (select) select.disabled = !editing;
        });
        form.querySelectorAll('[data-create-status]').forEach(input => input.disabled = editing);
        form.action = editing ? `${baseUrl}/${trigger.dataset.id}` : baseUrl;
        method.value = editing ? 'PUT' : 'POST';
        form.querySelectorAll('[name]').forEach(input => {
            if (input.name === '_token' || input.name === '_method') return;
            if (input.type === 'hidden') return;
            if (input.type === 'checkbox') { input.checked = String(record[input.name]) === input.dataset.checkedValue || record[input.name] === true; return; }
            if (input.type === 'file') return;
            let value = record[input.name] ?? '';
            if (input.type === 'datetime-local' && value) value = String(value).replace(' ', 'T').slice(0, 16);
            input.value = value;
        });
        form.querySelectorAll('[data-existing-file-for]').forEach(container => {
            container.replaceChildren();
            const path = record[`${container.dataset.existingFileFor}_path`];
            if (!editing || !path) return;
            const link = document.createElement('a');
            link.href = `${employeeStorageUrl}/${path}`;
            link.target = '_blank';
            link.rel = 'noopener';
            link.textContent = `Current file: ${String(path).split('/').pop()}`;
            container.append(link);
        });
        if (deploymentMode) {
            deploymentContract.value = editing ? String(deploymentDisplayRecord.contract_id || '') : '';
            if (editing && deploymentDisplayRecord.employee_id) addEmployeeAssignmentRow(deploymentDisplayRecord.employee_id, deploymentDisplayRecord.position, true);
            if (editing && deploymentDisplayRecord.vehicle_id) addVehicleAssignmentRow(deploymentDisplayRecord.vehicle_id, true);
            syncDeploymentForm(editing ? deploymentDisplayRecord : null);
        }
        syncConditionalFields();
        syncVehicleModelOptions();
        syncAmbulanceMasterOptions(record.category || '');
        if (photoInput) {
            photoInput.value = '';
            const imageUrl = editing && record.photo_path ? `${@json(asset('storage'))}/${record.photo_path}` : '';
            photoPreview.src = imageUrl;
            photoPreview.classList.toggle('d-none', !imageUrl);
            photoPlaceholder.classList.toggle('d-none', !!imageUrl);
        }
    });
    form.addEventListener('change', event => {
        if (event.target.matches('[id^="field-"]')) syncConditionalFields();
        if (vehicleMode && ['field-service_type', 'vehicleModelCategory'].includes(event.target.id)) syncVehicleModelOptions();
        if (ambulanceMasterMode && event.target.id === 'field-service_type') syncAmbulanceMasterOptions(masterCategorySelect.value);
        if (contractMode && event.target.id === 'field-government_client_id') {
            const selectedClient = event.target.selectedOptions[0];
            const department = document.getElementById('field-department');
            const address = document.getElementById('field-address');
            if (department) department.value = selectedClient?.dataset.clientDepartment || '';
            if (address) address.value = selectedClient?.dataset.clientAddress || '';
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
            } else if (field.type === 'boolean') {
                value.textContent = content ? 'Yes' : 'No';
            } else if (['employee_category', 'id_card_status', 'dress_status', 'police_verification_status'].includes(field.key) && content) {
                value.textContent = String(content).replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
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
    const vehicleTypeMaster = @json($module === 'ambulance-types');
    const todayUtc = Date.parse(`${@json(now('Asia/Kolkata')->toDateString())}T00:00:00Z`);
    modal.addEventListener('show.bs.modal', event => {
        const record = JSON.parse(event.relatedTarget.dataset.record);
        rows.replaceChildren();
        fields.forEach(field => {
            if (field.type === 'file') return;
            const row = document.createElement('tr');
            const label = document.createElement('th');
            const value = document.createElement('td');
            label.scope = 'row'; label.textContent = vehicleTypeMaster && field.key === 'category' && record.service_type === 'cab' ? 'Cab Type' : field.label;
            let content = record[field.key];
            if (content !== null && content !== undefined && content !== '') {
                if (field.type === 'select' || field.type === 'conditional-select' || field.type === 'dynamic-select' || field.type.startsWith('relation') || field.type === 'conditional-relation') content = field.options?.[content] ?? content;
                if (field.type === 'date' && ['insurance_expiry', 'permit_expiry', 'fitness_expiry'].includes(field.key)) {
                    const expiryDate = String(content).slice(0, 10);
                    const expiryUtc = Date.parse(`${expiryDate}T00:00:00Z`);
                    const daysRemaining = Math.round((expiryUtc - todayUtc) / 86400000);
                    const formattedDate = new Intl.DateTimeFormat('en-IN', {day:'2-digit', month:'short', year:'numeric'}).format(new Date(expiryUtc));
                    value.classList.add('expiry-value');
                    const date = document.createElement('span');
                    date.textContent = formattedDate;
                    value.append(date);
                    if (daysRemaining <= 7) {
                        const badge = document.createElement('span');
                        badge.className = `expiry-days-pill ${daysRemaining < 0 ? 'expired' : (daysRemaining === 0 ? 'today' : 'soon')}`;
                        badge.textContent = daysRemaining < 0 ? `Expired ${Math.abs(daysRemaining)} day(s) ago` : (daysRemaining === 0 ? 'Expires today' : `${daysRemaining} day(s) left`);
                        value.append(badge);
                    }
                }
                else if (field.type === 'date') content = new Intl.DateTimeFormat('en-IN', {day:'2-digit', month:'short', year:'numeric'}).format(new Date(`${content}T00:00:00`));
                else if (field.type === 'datetime-local' || field.type === 'datetime') content = new Intl.DateTimeFormat('en-IN', {dateStyle:'medium', timeStyle:'short'}).format(new Date(String(content).replace(' ', 'T')));
                else if (field.type === 'number' && /amount|rate|value|salary|wage|cost/i.test(field.key)) content = new Intl.NumberFormat('en-IN', {style:'currency', currency:'INR'}).format(Number(content));
                else if (field.key === 'status' || field.key === 'service_type' || field.key === 'payment_method') content = String(content).replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
                if (!(field.type === 'date' && ['insurance_expiry', 'permit_expiry', 'fitness_expiry'].includes(field.key))) value.textContent = content;
            } else value.textContent = '—';
            row.append(label, value); rows.append(row);
        });
    });
});
</script>
@endif
@endsection

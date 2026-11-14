@extends('layouts.newapp')

@section('title', 'Salary Processing | ANSH Enterprises')
@section('page-title', 'Salary Processing')

@section('content')
<main class="employee-management-page enterprise-page">
    <div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4"><div><div class="eyebrow">PAYROLL · EMPLOYEES &amp; MANPOWER</div><h3 class="mb-1">Salary processing</h3><p class="text-muted mb-0">Select an employee and working days to calculate wages, statutory contributions, deductions, and net pay.</p></div>@can('manage-records')<button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#salaryModal" data-mode="create"><i class="bi bi-calculator me-1"></i> Calculate salary</button>@endcan</div>
    @if (session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if (session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="card records-card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3"><div><h5 class="mb-1">Salary records</h5><div class="text-muted small">{{ number_format($salaries->total()) }} payroll record(s)</div></div>
            <form method="GET" action="{{ route('salary.index') }}" id="salaryFilterForm" class="filter-bar d-flex flex-wrap gap-2"><div class="input-group search-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Employee, code, contract"></div><div class="month-picker salary-month-filter" data-month-picker><div class="input-group"><input class="form-control month-picker-display" id="salaryMonthFilterDisplay" type="text" readonly placeholder="Choose month" aria-label="Pay month"><button class="btn btn-light border month-picker-toggle" type="button" data-month-toggle aria-label="Choose pay month" aria-expanded="false"><i class="bi bi-calendar-month"></i></button></div><input type="hidden" id="salaryMonthFilter" data-month-value name="month" value="{{ request('month') }}"><div class="month-picker-panel" data-month-panel hidden><div class="month-picker-header"><button class="btn btn-sm btn-light border" type="button" data-year-step="-1" aria-label="Previous year"><i class="bi bi-chevron-left"></i></button><strong data-month-year></strong><button class="btn btn-sm btn-light border" type="button" data-year-step="1" aria-label="Next year"><i class="bi bi-chevron-right"></i></button></div><div class="month-picker-grid">@foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $monthIndex => $monthName)<button class="month-picker-month" type="button" data-month-option="{{ $monthIndex + 1 }}">{{ $monthName }}</button>@endforeach</div></div></div><button class="btn btn-light border" type="submit">Filter</button>@if(request()->query())<a class="btn btn-light border" href="{{ route('salary.index') }}">Reset</a>@endif<button class="btn btn-outline-success" id="salaryExportButton" type="submit" formaction="{{ route('salary.export.form-b') }}" @disabled(!request('month')) title="Select a pay month to export Form B"><i class="bi bi-file-earmark-excel me-1"></i>Export Form B</button><small class="w-100 text-muted" id="salaryExportSelectionMessage" aria-live="polite">No employees selected: export all salaries for the chosen month.</small></form>
        </div>
        <div class="table-responsive"><table class="table align-middle enterprise-table salary-table mb-0"><thead><tr><th><input class="form-check-input" type="checkbox" id="salarySelectPage" aria-label="Select all employees on this page"></th><th>Pay month</th><th>Employee</th><th>Contract</th><th>Days × rate</th><th>Gross salary</th><th>Employee deductions</th><th>Employer PF + ESI</th><th>Bonus</th><th>Net salary</th><th class="text-end">Actions</th></tr></thead><tbody>
            @forelse($salaries as $salary)
                <tr><td><input class="form-check-input salary-export-checkbox" type="checkbox" value="{{ $salary->employee_id }}" aria-label="Select {{ $salary->employee_name_snapshot }} for Form B export"></td><td><strong>{{ \Illuminate\Support\Carbon::parse($salary->salary_month)->format('M Y') }}</strong></td><td><strong>{{ $salary->employee_name_snapshot }}</strong><div class="text-muted small">{{ $salary->employee_code_snapshot }} · {{ $salary->designation_snapshot }}</div></td><td>{{ $salary->contract_number_snapshot ?: '—' }}</td><td>{{ $salary->working_days }} × ₹{{ number_format((float)$salary->daily_wages_rate, 2) }}</td><td class="fw-semibold">₹{{ number_format((float)$salary->gross_salary, 2) }}</td><td>₹{{ number_format((float)$salary->total_deductions, 2) }}<div class="text-muted small">PF ₹{{ number_format((float)$salary->pf_employee_amount, 2) }} · ESI ₹{{ number_format((float)$salary->esi_employee_amount, 2) }}</div></td><td>₹{{ number_format((float)($salary->pf_employer_amount + $salary->esi_employer_amount), 2) }}</td><td>@if($salary->bonus_eligible)<span class="status-pill status-active">Eligible</span>@else<span class="status-pill">Not eligible</span>@endif<div>₹{{ number_format((float)$salary->bonus_amount, 2) }}</div></td><td class="fw-bold text-success">₹{{ number_format((float)$salary->net_salary, 2) }}</td><td class="text-end text-nowrap"><button class="btn btn-sm btn-icon btn-view" type="button" title="View salary" data-bs-toggle="modal" data-bs-target="#salaryViewModal" data-record="{{ json_encode($salary) }}"><i class="bi bi-eye"></i></button>@can('manage-records')<button class="btn btn-sm btn-icon btn-edit" type="button" title="Edit / recalculate" data-bs-toggle="modal" data-bs-target="#salaryModal" data-mode="edit" data-id="{{ $salary->id }}" data-record="{{ json_encode($salary) }}"><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-icon btn-delete" type="button" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteSalaryModal" data-id="{{ $salary->id }}" data-label="{{ $salary->employee_code_snapshot }} · {{ \Illuminate\Support\Carbon::parse($salary->salary_month)->format('M Y') }}"><i class="bi bi-trash3"></i></button>@endcan</td></tr>
            @empty<tr><td colspan="11" class="empty-state"><span class="empty-icon"><i class="bi bi-wallet2"></i></span><strong>No salary records found</strong><span>Adjust the filters or calculate the first salary for an employee.</span>@can('manage-records')<button class="btn btn-primary btn-sm mt-2" type="button" data-bs-toggle="modal" data-bs-target="#salaryModal" data-mode="create">Calculate salary</button>@endcan</td></tr>@endforelse
        </tbody></table></div>
        @if($salaries->hasPages())<div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2"><small class="text-muted">Showing {{ $salaries->firstItem() }}–{{ $salaries->lastItem() }} of {{ $salaries->total() }}</small>{{ $salaries->links() }}</div>@endif
    </section>
</main>

<div class="modal fade" id="salaryViewModal" tabindex="-1" aria-labelledby="salaryViewTitle" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><div><div class="eyebrow">PAYROLL RECORD</div><h5 class="modal-title" id="salaryViewTitle">Salary details</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-0"><table class="table table-bordered employee-details-table mb-0"><tbody id="salaryViewRows"></tbody></table></div><div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button></div></div></div></div>

@can('manage-records')
<div class="modal fade" id="salaryModal" tabindex="-1" aria-labelledby="salaryModalTitle" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><form id="salaryForm" method="POST" action="{{ route('salary.store') }}">@csrf<input type="hidden" name="_method" id="salaryMethod" value="POST">
    <div class="modal-header"><div><div class="eyebrow">EMPLOYEE PAYROLL</div><h5 class="modal-title" id="salaryModalTitle">Calculate salary</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <div class="modal-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="salaryMonthDisplay">Pay month</label><div class="month-picker" data-month-picker><div class="input-group"><input class="form-control month-picker-display" id="salaryMonthDisplay" type="text" readonly required placeholder="Choose month" aria-label="Pay month"><button class="btn btn-light border month-picker-toggle" type="button" data-month-toggle aria-label="Choose pay month" aria-expanded="false"><i class="bi bi-calendar-month"></i></button></div><input type="hidden" id="salaryMonth" data-month-value name="salary_month" value="{{ now('Asia/Kolkata')->format('Y-m') }}"><div class="month-picker-panel" data-month-panel hidden><div class="month-picker-header"><button class="btn btn-sm btn-light border" type="button" data-year-step="-1" aria-label="Previous year"><i class="bi bi-chevron-left"></i></button><strong data-month-year></strong><button class="btn btn-sm btn-light border" type="button" data-year-step="1" aria-label="Next year"><i class="bi bi-chevron-right"></i></button></div><div class="month-picker-grid">@foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $monthIndex => $monthName)<button class="month-picker-month" type="button" data-month-option="{{ $monthIndex + 1 }}">{{ $monthName }}</button>@endforeach</div></div></div></div>
        <div class="col-md-6"><label class="form-label" for="salaryEmployee">Employee</label><select class="form-select" id="salaryEmployee" name="employee_id" required><option value="">Select employee</option>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->employee_code }} — {{ $employee->name }}</option>@endforeach</select></div>
        <div class="col-12"><div id="salaryEmployeeLoader" class="d-none d-flex align-items-center justify-content-center gap-2 py-2 text-primary" role="status" aria-live="polite"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Loading employee details…</span></div><div id="salaryEmployeeLoadError" class="alert alert-danger py-2 mb-0 d-none" role="alert"></div></div>
        <div class="col-md-4"><label class="form-label" for="employeeDesignation">Designation</label><input class="form-control" id="employeeDesignation" readonly placeholder="Auto-filled"></div>
        <div class="col-md-4"><label class="form-label" for="employeeCategory">Employee category</label><input class="form-control" id="employeeCategory" readonly placeholder="Auto-filled"></div>
        <div class="col-md-4"><label class="form-label" for="employeeDailyRate">Daily wages rate</label><input class="form-control" id="employeeDailyRate" readonly placeholder="Auto-filled"></div>
        <div class="col-md-4"><label class="form-label" for="employeeContract">Assigned contract</label><input class="form-control" id="employeeContract" readonly placeholder="No active assignment"></div>
        <div class="col-md-4"><label class="form-label" for="workingDays">Working days</label><input class="form-control" id="workingDays" name="working_days" type="number" min="1" max="31" step="1" required placeholder="Enter days worked"></div>
        <div class="col-md-4"><label class="form-label" for="earnedWages">Earned wages</label><input class="form-control salary-calculated" id="earnedWages" readonly value="₹0.00"></div>
        <div class="col-md-4"><label class="form-label" for="salaryGross">Gross salary including bonus</label><input class="form-control salary-calculated" id="salaryGross" readonly value="₹0.00"></div>
        <div class="col-12"><div class="salary-section-label">Statutory contributions</div></div>
        <div class="col-md-3"><label class="form-label">PF · employee</label><input class="form-control salary-calculated" id="pfEmployee" readonly value="₹0.00"><div class="form-text" id="epfStatusNote">EPF applies according to the salary rules master.</div></div>
        <div class="col-md-3"><label class="form-label">PF · employer</label><input class="form-control salary-calculated" id="pfEmployer" readonly value="₹0.00"></div>
        <div class="col-md-3"><label class="form-label">ESI · employee</label><input class="form-control salary-calculated" id="esiEmployee" readonly value="₹0.00"><div class="form-text" id="esiStatusNote">ESI applies when eligible under the salary rules master.</div></div>
        <div class="col-md-3"><label class="form-label">ESI · employer</label><input class="form-control salary-calculated" id="esiEmployer" readonly value="₹0.00"></div>
        <div class="col-md-4"><label class="form-label" for="bonusAmount">Bonus amount (₹)</label><input class="form-control" id="bonusAmount" name="bonus_amount" type="number" min="0" step="0.01" value="0" disabled><div class="form-text" id="bonusEligibilityNote">Select an employee and enter working days to check eligibility.</div></div>
        <div class="col-md-4"><label class="form-label">Total employee deductions</label><input class="form-control salary-calculated" id="salaryDeductions" readonly value="₹0.00"></div>
        <div class="col-md-4"><label class="form-label">Net salary</label><input class="form-control salary-calculated salary-net" id="salaryNet" readonly value="₹0.00"></div>
        <div class="col-12"><div class="salary-formula-note"><i class="bi bi-info-circle me-1"></i>Employer PF and ESI are employer costs and are not deducted from employee net pay. Eligibility is based on the salary rules master and saved working days in the current accounting year.</div></div>
    </div></div>
    <div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save salary</button></div>
</form></div></div></div>

<div class="modal fade" id="deleteSalaryModal" tabindex="-1" aria-labelledby="deleteSalaryTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form id="deleteSalaryForm" method="POST" action="{{ route('salary.destroy', 0) }}">@csrf @method('DELETE')<div class="modal-header"><div><div class="eyebrow">CONFIRM ACTION</div><h5 class="modal-title" id="deleteSalaryTitle">Delete salary record?</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">Delete <strong id="deleteSalaryLabel"></strong>?</div><div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger" type="submit">Delete salary</button></div></form></div></div></div>
@endcan
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const formatMonth = value => {
        const match = /^(\d{4})-(\d{2})$/.exec(value || '');
        return match ? `${monthNames[Number(match[2]) - 1]} ${match[1]}` : '';
    };
    const renderMonthPicker = widget => {
        const hidden = widget.querySelector('[data-month-value]');
        const display = widget.querySelector('.month-picker-display');
        const selected = /^(\d{4})-(\d{2})$/.exec(hidden.value || '');
        const state = widget.monthPickerState;
        display.value = formatMonth(hidden.value);
        widget.querySelector('[data-month-year]').textContent = state.year;
        widget.querySelectorAll('[data-month-option]').forEach(button => {
            const month = String(button.dataset.monthOption).padStart(2, '0');
            const active = !!selected && selected[1] === String(state.year) && selected[2] === month;
            button.classList.toggle('active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    };
    document.querySelectorAll('[data-month-picker]').forEach(widget => {
        const hidden = widget.querySelector('[data-month-value]');
        const panel = widget.querySelector('[data-month-panel]');
        const toggle = widget.querySelector('[data-month-toggle]');
        const initialYear = Number((hidden.value || '').slice(0, 4)) || new Date().getFullYear();
        widget.monthPickerState = {year:initialYear};
        renderMonthPicker(widget);
        toggle.addEventListener('click', () => {
            panel.hidden = !panel.hidden;
            toggle.setAttribute('aria-expanded', panel.hidden ? 'false' : 'true');
            const selected = /^(\d{4})-(\d{2})$/.exec(hidden.value || '');
            if (!panel.hidden && selected) widget.monthPickerState.year = Number(selected[1]);
            renderMonthPicker(widget);
        });
        widget.querySelector('.month-picker-display').addEventListener('click', () => toggle.click());
        widget.querySelectorAll('[data-year-step]').forEach(button => button.addEventListener('click', () => {
            widget.monthPickerState.year += Number(button.dataset.yearStep);
            renderMonthPicker(widget);
        }));
        widget.querySelectorAll('[data-month-option]').forEach(button => button.addEventListener('click', () => {
            const month = String(button.dataset.monthOption).padStart(2, '0');
            hidden.value = `${widget.monthPickerState.year}-${month}`;
            renderMonthPicker(widget);
            panel.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
            hidden.dispatchEvent(new Event('input', {bubbles:true}));
            hidden.dispatchEvent(new Event('change', {bubbles:true}));
        }));
        widget.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !panel.hidden) {
                panel.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
            }
        });
        document.addEventListener('click', event => {
            if (!widget.contains(event.target) && !panel.hidden) {
                panel.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
    window.setMonthPickerValue = (hidden, value) => {
        hidden.value = value;
        const widget = hidden.closest('[data-month-picker]');
        if (widget) {
            widget.monthPickerState.year = Number((value || '').slice(0, 4)) || new Date().getFullYear();
            renderMonthPicker(widget);
        }
    };

    const monthFilter = document.getElementById('salaryMonthFilter');
    const exportButton = document.getElementById('salaryExportButton');
    const exportForm = document.getElementById('salaryFilterForm');
    const selectPage = document.getElementById('salarySelectPage');
    const selectionMessage = document.getElementById('salaryExportSelectionMessage');
    const rowCheckboxes = Array.from(document.querySelectorAll('.salary-export-checkbox'));
    const selectionKey = () => `salary-form-b-selection-${monthFilter.value || 'all'}`;
    const readSelection = () => {
        try { return new Set(JSON.parse(sessionStorage.getItem(selectionKey()) || '[]').map(String)); }
        catch { return new Set(); }
    };
    let selectedEmployees = readSelection();
    const saveSelection = () => sessionStorage.setItem(selectionKey(), JSON.stringify([...selectedEmployees]));
    const refreshSelection = () => {
        rowCheckboxes.forEach(checkbox => { checkbox.checked = selectedEmployees.has(checkbox.value); });
        const selectedOnPage = rowCheckboxes.filter(checkbox => checkbox.checked).length;
        selectPage.checked = rowCheckboxes.length > 0 && selectedOnPage === rowCheckboxes.length;
        selectPage.indeterminate = selectedOnPage > 0 && selectedOnPage < rowCheckboxes.length;
        selectionMessage.textContent = selectedEmployees.size
            ? `${selectedEmployees.size} employee(s) selected for export.`
            : 'No employees selected: export all salaries for the chosen month.';
    };
    refreshSelection();
    selectPage.addEventListener('change', () => {
        rowCheckboxes.forEach(checkbox => selectPage.checked ? selectedEmployees.add(checkbox.value) : selectedEmployees.delete(checkbox.value));
        saveSelection(); refreshSelection();
    });
    rowCheckboxes.forEach(checkbox => checkbox.addEventListener('change', () => {
        if (checkbox.checked) selectedEmployees.add(checkbox.value);
        else selectedEmployees.delete(checkbox.value);
        saveSelection(); refreshSelection();
    }));
    monthFilter.addEventListener('change', () => {
        exportButton.disabled = !monthFilter.value;
        selectedEmployees = readSelection();
        refreshSelection();
    });
    exportButton.addEventListener('click', () => {
        exportForm.querySelectorAll('[data-export-employee-id]').forEach(input => input.remove());
        selectedEmployees.forEach(employeeId => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'employee_ids[]'; input.value = employeeId;
            input.dataset.exportEmployeeId = 'true'; exportForm.append(input);
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('salaryViewModal');
    const fields = [['Pay month','salary_month','month'],['Employee code','employee_code_snapshot'],['Employee name','employee_name_snapshot'],['Designation','designation_snapshot'],['Assigned contract','contract_number_snapshot'],['Working days','working_days'],['Daily wages rate','daily_wages_rate','money'],['Earned wages','earned_wages','money'],['PF wage basis','pf_wage_basis','money'],['EPF exempt for employee','epf_exempt','bool'],['PF employee rate','pf_employee_rate','percent'],['PF employee contribution','pf_employee_amount','money'],['PF employer rate','pf_employer_rate','percent'],['PF employer contribution','pf_employer_amount','money'],['ESI exempt for employee','esi_exempt','bool'],['ESI applicable','esi_applicable','bool'],['ESI employee rate','esi_employee_rate','percent'],['ESI employee contribution','esi_employee_amount','money'],['ESI employer rate','esi_employer_rate','percent'],['ESI employer contribution','esi_employer_amount','money'],['Bonus eligible','bonus_eligible','bool'],['Bonus amount','bonus_amount','money'],['Gross salary','gross_salary','money'],['Total deductions','total_deductions','money'],['Net salary','net_salary','money']];
    modal.addEventListener('show.bs.modal', event => {
        const record = JSON.parse(event.relatedTarget.dataset.record), rows = document.getElementById('salaryViewRows'); rows.replaceChildren();
        document.getElementById('salaryViewTitle').textContent = `Salary · ${record.employee_code_snapshot || ''} · ${record.salary_month || ''}`;
        fields.forEach(([labelText, key, type]) => {
            const row = document.createElement('tr'), label = document.createElement('th'), value = document.createElement('td');
            label.scope = 'row'; label.textContent = labelText; let content = record[key];
            if (content === null || content === undefined || content === '') content = '—';
            else if (type === 'money') content = new Intl.NumberFormat('en-IN', {style:'currency', currency:'INR'}).format(Number(content));
            else if (type === 'percent') content = `${Number(content)}%`;
            else if (type === 'bool') content = Number(content) ? 'Yes' : 'No';
            else if (type === 'month') content = new Intl.DateTimeFormat('en-IN', {month:'long', year:'numeric'}).format(new Date(`${String(content).slice(0,7)}-01T00:00:00`));
            value.textContent = content; row.append(label, value); rows.append(row);
        });
    });
});
</script>
@can('manage-records')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('salaryModal');
    const form = document.getElementById('salaryForm');
    const method = document.getElementById('salaryMethod');
    const employeeSelect = document.getElementById('salaryEmployee');
    const monthInput = document.getElementById('salaryMonth');
    const daysInput = document.getElementById('workingDays');
    const bonusInput = document.getElementById('bonusAmount');
    const note = document.getElementById('bonusEligibilityNote');
    const salaryUrl = @json(url('/salary'));
    const previewUrl = @json(url('/salary/employees'));
    const deleteModal = document.getElementById('deleteSalaryModal');
    const deleteForm = document.getElementById('deleteSalaryForm');
    const employeeLoader = document.getElementById('salaryEmployeeLoader');
    const employeeLoadError = document.getElementById('salaryEmployeeLoadError');
    let previewAbort;
    const money = value => new Intl.NumberFormat('en-IN', {style:'currency', currency:'INR', minimumFractionDigits:2}).format(Number(value || 0));
    const putMoney = (id, value) => { document.getElementById(id).value = money(value); };

    async function updatePreview() {
        const id = employeeSelect.value;
        const days = daysInput.value || '0';
        previewAbort?.abort();
        if (!id || !monthInput.value) {
            previewAbort = null;
            employeeLoader.classList.add('d-none');
            employeeLoadError.classList.add('d-none');
            ['employeeDesignation','employeeCategory','employeeDailyRate','employeeContract'].forEach(fieldId => document.getElementById(fieldId).value = '');
            return;
        }
        const request = new AbortController();
        previewAbort = request;
        employeeLoadError.classList.add('d-none');
        employeeLoader.classList.remove('d-none');
        ['employeeDesignation','employeeCategory','employeeDailyRate','employeeContract'].forEach(fieldId => document.getElementById(fieldId).value = '');
        ['earnedWages','salaryGross','pfEmployee','pfEmployer','esiEmployee','esiEmployer','salaryDeductions','salaryNet'].forEach(fieldId => putMoney(fieldId, 0));
        try {
            const query = new URLSearchParams({salary_month:monthInput.value, working_days:days});
            const response = await fetch(`${previewUrl}/${id}/preview?${query}`, {headers:{'Accept':'application/json'}, signal:request.signal});
            if (!response.ok) throw new Error('Unable to load this employee’s salary details. Please try again.');
            const data = await response.json();
            if (previewAbort !== request) return;
            document.getElementById('employeeDesignation').value = data.designation || '—';
            document.getElementById('employeeCategory').value = data.employee_category ? String(data.employee_category).replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase()) : '—';
            document.getElementById('employeeDailyRate').value = `${money(data.daily_wages_rate)} / day`;
            document.getElementById('employeeContract').value = data.contract_number || 'No active assignment';
            putMoney('earnedWages', data.earned_wages);
            putMoney('pfEmployee', data.pf_employee_amount);
            putMoney('pfEmployer', data.pf_employer_amount);
            putMoney('esiEmployee', data.esi_employee_amount);
            putMoney('esiEmployer', data.esi_employer_amount);
            document.getElementById('epfStatusNote').textContent = data.epf_exempt ? 'EPF is excluded for this employee; employee and employer contributions are both zero.' : 'EPF applies according to the salary rules master.';
            document.getElementById('esiStatusNote').textContent = data.esi_exempt ? 'ESI is excluded for this employee; employee and employer contributions are both zero.' : (data.esi_applicable ? 'ESI applies under the current salary rules.' : 'ESI is not applicable under the current salary rules.');
            const bonus = data.bonus_eligible ? Number(bonusInput.value || 0) : 0;
            bonusInput.disabled = !data.bonus_eligible;
            if (!data.bonus_eligible) bonusInput.value = '0';
            note.textContent = data.bonus_eligible
                ? `Eligible: ${data.bonus_eligible_days} accounting-year working days recorded. Current guideline range: ${money(data.bonus_minimum_guideline)}–${money(data.bonus_maximum_guideline)}; remaining maximum: ${money(data.bonus_maximum_remaining)}.`
                : `Not eligible under current settings (${data.bonus_eligible_days}/${data.bonus_minimum_days} accounting-year days; monthly wage limit ${money(data.bonus_monthly_eligibility_limit)}).`;
            const deductions = Number(data.pf_employee_amount) + Number(data.esi_employee_amount);
            putMoney('salaryDeductions', deductions);
            putMoney('salaryGross', Number(data.earned_wages) + bonus);
            putMoney('salaryNet', Number(data.earned_wages) + bonus - deductions);
        } catch (error) {
            if (error.name !== 'AbortError' && previewAbort === request) {
                employeeLoadError.textContent = error.message || 'Unable to load employee details. Please try again.';
                employeeLoadError.classList.remove('d-none');
            }
        } finally {
            if (previewAbort === request) employeeLoader.classList.add('d-none');
        }
    }

    [employeeSelect, monthInput, daysInput].forEach(element => element.addEventListener('change', updatePreview));
    daysInput.addEventListener('input', updatePreview);
    bonusInput.addEventListener('input', updatePreview);
    modal.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        const editing = trigger?.dataset.mode === 'edit';
        const record = editing ? JSON.parse(trigger.dataset.record) : null;
        document.getElementById('salaryModalTitle').textContent = editing ? 'Edit / recalculate salary' : 'Calculate salary';
        form.action = editing ? `${salaryUrl}/${trigger.dataset.id}` : salaryUrl;
        method.value = editing ? 'PUT' : 'POST';
        employeeSelect.value = record?.employee_id || '';
        window.setMonthPickerValue(monthInput, record?.salary_month?.slice(0,7) || @json(now('Asia/Kolkata')->format('Y-m')));
        daysInput.value = record?.working_days || '';
        bonusInput.value = record?.bonus_amount || '0';
        bonusInput.disabled = true;
        ['employeeDesignation','employeeCategory','employeeDailyRate','employeeContract'].forEach(id => document.getElementById(id).value = '');
        ['earnedWages','salaryGross','pfEmployee','pfEmployer','esiEmployee','esiEmployer','salaryDeductions','salaryNet'].forEach(id => putMoney(id, 0));
        document.getElementById('epfStatusNote').textContent = 'EPF applies according to the salary rules master.';
        document.getElementById('esiStatusNote').textContent = 'ESI applies when eligible under the salary rules master.';
        note.textContent = 'Select an employee and enter working days to check eligibility.';
        employeeLoader.classList.add('d-none');
        employeeLoadError.classList.add('d-none');
        if (editing) updatePreview();
    });
    deleteModal.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        deleteForm.action = `${salaryUrl}/${trigger.dataset.id}`;
        document.getElementById('deleteSalaryLabel').textContent = trigger.dataset.label;
    });
});
</script>
@endcan
@endsection

@extends('layouts.newapp')

@section('title', 'Salary Rules Master | ANSH Enterprises')
@section('page-title', 'Salary Rules Master')

@section('content')
<main class="employee-management-page enterprise-page">
    <div class="page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4"><div><div class="eyebrow">PAYROLL · CONFIGURATION</div><h3 class="mb-1">Salary rules master</h3><p class="text-muted mb-0">Maintain the rates, wage limits, and eligibility thresholds used in payroll calculations.</p></div><a class="btn btn-light border" href="{{ route('salary.index') }}"><i class="bi bi-arrow-left me-1"></i> Salary processing</a></div>
    @if (session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Rules and thresholds are configurable and can vary by establishment, state, and applicable notifications. Confirm the current requirements before finalising payroll. Existing salary records retain their saved calculation values.</div>

    <form method="POST" action="{{ route('salary.rules.update') }}">@csrf @method('PUT')
        <div class="row g-4">
            <div class="col-12 col-xl-6"><section class="card records-card h-100"><div class="card-header"><h5 class="mb-1"><i class="bi bi-bank me-2 text-primary"></i>Provident fund (PF)</h5><div class="text-muted small">Employee and employer contribution rates and monthly wage basis.</div></div><div class="card-body p-4"><div class="row g-3">
                @foreach ([['pf_employee_rate','Employee contribution rate','%'],['pf_employer_rate','Employer contribution rate','%'],['pf_monthly_wage_ceiling','Monthly wage ceiling','₹']] as [$name,$label,$unit])
                    <div class="col-md-6"><label class="form-label" for="{{ $name }}">{{ $label }} ({{ $unit }})</label><input class="form-control" id="{{ $name }}" name="{{ $name }}" type="number" step="0.01" min="0" value="{{ old($name, $settings->{$name}) }}" required @cannot('manage-records') disabled @endcannot></div>
                @endforeach
            </div><div class="form-text mt-3">PF contributions are calculated on the lower of earned wages and the configured ceiling.</div></div></section></div>

            <div class="col-12 col-xl-6"><section class="card records-card h-100"><div class="card-header"><h5 class="mb-1"><i class="bi bi-heart-pulse me-2 text-danger"></i>Employee State Insurance (ESI)</h5><div class="text-muted small">Contribution rates, coverage limit, and employee-share exemption.</div></div><div class="card-body p-4"><div class="row g-3">
                @foreach ([['esi_employee_rate','Employee contribution rate','%'],['esi_employer_rate','Employer contribution rate','%'],['esi_monthly_wage_limit','Monthly wage eligibility limit','₹'],['esi_employee_exemption_daily_wage','Employee share exemption daily wage','₹']] as [$name,$label,$unit])
                    <div class="col-md-6"><label class="form-label" for="{{ $name }}">{{ $label }} ({{ $unit }})</label><input class="form-control" id="{{ $name }}" name="{{ $name }}" type="number" step="0.01" min="0" value="{{ old($name, $settings->{$name}) }}" required @cannot('manage-records') disabled @endcannot></div>
                @endforeach
            </div><div class="form-text mt-3">ESI is applied when monthly earned wages are within the configured eligibility limit. The daily threshold can exempt the employee share.</div></div></section></div>

            <div class="col-12"><section class="card records-card"><div class="card-header"><h5 class="mb-1"><i class="bi bi-gift me-2 text-warning"></i>Bonus eligibility and guidance</h5><div class="text-muted small">Eligibility is checked against the configured wage limit and recorded working days in the accounting year.</div></div><div class="card-body p-4"><div class="row g-3">
                @foreach ([['bonus_monthly_eligibility_limit','Monthly wage eligibility limit','₹'],['bonus_monthly_calculation_ceiling','Optional monthly bonus wage cap','₹'],['bonus_minimum_rate','Minimum bonus rate','%'],['bonus_maximum_rate','Maximum bonus rate','%'],['bonus_minimum_days','Minimum days worked in accounting year','days']] as [$name,$label,$unit])
                    <div class="col-12 col-sm-6 col-xl-3"><label class="form-label" for="{{ $name }}">{{ $label }} ({{ $unit }})</label><input class="form-control" id="{{ $name }}" name="{{ $name }}" type="number" step="{{ $unit === 'days' ? '1' : '0.01' }}" min="0" value="{{ old($name, $settings->{$name}) }}" required @cannot('manage-records') disabled @endcannot></div>
                @endforeach
            </div><div class="form-text mt-3">Set the wage limit from the applicable appropriate-government notification; a limit of 0 keeps bonus eligibility disabled until configured. A bonus wage cap of 0 uses actual wages without an extra cap. The bonus amount field is enabled only when the employee meets the configured wage and accounting-year day thresholds.</div></div></section></div>
        </div>
        @can('manage-records')<div class="d-flex justify-content-end mt-4"><button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i>Save salary rules</button></div>@endcan
    </form>
</main>
@endsection

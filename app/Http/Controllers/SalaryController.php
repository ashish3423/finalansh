<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('employee_salaries');

        if ($request->filled('month') && preg_match('/^\d{4}-\d{2}$/', $request->string('month')->toString())) {
            $query->where('salary_month', $request->input('month').'-01');
        }
        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($builder) use ($search) {
                $builder->where('employee_name_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('employee_code_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('designation_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('contract_number_snapshot', 'like', '%'.$search.'%');
            });
        }

        $salaries = $query->orderByDesc('salary_month')->orderBy('employee_name_snapshot')->paginate(15)->withQueryString();
        $employees = DB::table('employees')->where('status', 'active')->orderBy('employee_code')->get(['id', 'employee_code', 'name']);

        return view('salary.index', compact('salaries', 'employees'));
    }

    public function exportFormB(Request $request)
    {
        $filters = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'search' => ['nullable', 'string', 'max:160'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'distinct', 'exists:employees,id'],
        ]);
        $month = Carbon::createFromFormat('Y-m', $filters['month'])->startOfMonth();
        $query = DB::table('employee_salaries')
            ->where('salary_month', $month->toDateString());

        if (! empty($filters['employee_ids'])) {
            $query->whereIn('employee_id', $filters['employee_ids']);
        }

        if (filled($filters['search'] ?? null)) {
            $search = trim($filters['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('employee_name_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('employee_code_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('designation_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('contract_number_snapshot', 'like', '%'.$search.'%');
            });
        }

        $salaries = $query->orderBy('employee_name_snapshot')->get();
        $templatePath = resource_path('templates/form-b-wage-register.xlsx');
        abort_unless(is_file($templatePath), 500, 'The Form B wage register template is missing.');

        $workbook = IOFactory::load($templatePath);
        $sheet = $workbook->getActiveSheet();
        $sheet->setTitle($month->format('M y'));
        $sheet->setCellValue('A7', sprintf(
            "NAME OF ESTABLISHMENT: M/s ANSH ENTERPRISES\nFor the period from: %s to %s",
            $month->format('d.m.Y'),
            $month->copy()->endOfMonth()->format('d.m.Y')
        ));

        $baseDataRow = 11;
        $templateLastRow = 20;
        foreach ($salaries as $index => $salary) {
            $row = $baseDataRow + $index;
            if ($row > $templateLastRow) {
                $sheet->duplicateStyle($sheet->getStyle("A{$templateLastRow}:AA{$templateLastRow}"), "A{$row}:AA{$row}");
                $sheet->getRowDimension($row)->setRowHeight($sheet->getRowDimension($templateLastRow)->getRowHeight());
            }

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", mb_strtoupper($salary->employee_name_snapshot));
            $sheet->setCellValue("C{$row}", (float) $salary->daily_wages_rate);
            $sheet->setCellValue("D{$row}", (int) $salary->working_days);
            $sheet->setCellValue("E{$row}", '-');
            $sheet->setCellValue("F{$row}", "=C{$row}*D{$row}");
            $sheet->setCellValue("G{$row}", '-');
            foreach (['H', 'I', 'J'] as $column) {
                $sheet->setCellValue("{$column}{$row}", 'NA');
            }
            $sheet->setCellValue("K{$row}", (float) $salary->bonus_amount);
            $sheet->setCellValue("L{$row}", 0);
            $sheet->setCellValue("M{$row}", 0);
            $sheet->setCellValue("N{$row}", "=F{$row}+K{$row}+L{$row}+M{$row}");
            $sheet->setCellValue("O{$row}", (float) $salary->pf_employee_amount);
            $sheet->setCellValue("P{$row}", (float) $salary->esi_employee_amount);
            foreach (['Q', 'R', 'S', 'T', 'U'] as $column) {
                $sheet->setCellValue("{$column}{$row}", 'NA');
            }
            $sheet->setCellValue("V{$row}", "=SUM(O{$row}:U{$row})");
            $sheet->setCellValue("W{$row}", "=N{$row}-V{$row}");
            $sheet->setCellValue("X{$row}", 'NA');
            $sheet->setCellValue("Y{$row}", null);
            $sheet->setCellValue("Z{$row}", null);
            $sheet->setCellValue("AA{$row}", null);
        }

        $lastRow = max($templateLastRow, $baseDataRow + $salaries->count() - 1);
        $sheet->getPageSetup()->setPrintArea("A1:AA{$lastRow}");
        $workbook->getProperties()->setTitle('Form B Wage Register · ANSH Enterprises');
        $workbook->getProperties()->setSubject('Wage register for '.$month->format('F Y'));
        $fileName = 'Form-B-Wage-Register-'.$month->format('M-Y').'.xlsx';

        return response()->streamDownload(function () use ($workbook): void {
            (new Xlsx($workbook))->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function rules()
    {
        $settings = DB::table('salary_rule_settings')->orderBy('id')->first();

        return view('salary.rules', compact('settings'));
    }

    public function updateRules(Request $request)
    {
        $data = $request->validate([
            'pf_employee_rate' => ['required', 'numeric', 'between:0,100'],
            'pf_employer_rate' => ['required', 'numeric', 'between:0,100'],
            'pf_monthly_wage_ceiling' => ['required', 'numeric', 'min:0'],
            'esi_employee_rate' => ['required', 'numeric', 'between:0,100'],
            'esi_employer_rate' => ['required', 'numeric', 'between:0,100'],
            'esi_monthly_wage_limit' => ['required', 'numeric', 'min:0'],
            'esi_employee_exemption_daily_wage' => ['required', 'numeric', 'min:0'],
            'bonus_monthly_eligibility_limit' => ['required', 'numeric', 'min:0'],
            'bonus_monthly_calculation_ceiling' => ['required', 'numeric', 'min:0'],
            'bonus_minimum_rate' => ['required', 'numeric', 'between:0,100'],
            'bonus_maximum_rate' => ['required', 'numeric', 'between:0,100', 'gte:bonus_minimum_rate'],
            'bonus_minimum_days' => ['required', 'integer', 'min:0', 'max:366'],
        ]);
        $settings = DB::table('salary_rule_settings')->orderBy('id')->first();
        DB::table('salary_rule_settings')->where('id', $settings->id)->update($data + ['updated_at' => now()]);

        return back()->with('success', 'Salary rule settings updated. New payroll calculations will use these settings. Existing payroll records keep their saved calculation values.');
    }

    public function employeeInfo(Request $request, int $employee)
    {
        $validated = $request->validate([
            'salary_month' => ['required', 'date_format:Y-m'],
            'working_days' => ['required', 'integer', 'min:0', 'max:31'],
        ]);
        $employeeRecord = $this->employeeRecord($employee);
        $settings = (array) DB::table('salary_rule_settings')->orderBy('id')->first();
        $month = Carbon::createFromFormat('Y-m', $validated['salary_month'])->startOfMonth();
        $this->ensureWorkingDaysFitMonth($month, (int) $validated['working_days']);

        return response()->json($this->calculationPreview($employeeRecord, $settings, $month, (int) $validated['working_days']));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayroll($request);
        $employee = $this->employeeRecord((int) $data['employee_id']);
        $month = Carbon::createFromFormat('Y-m', $data['salary_month'])->startOfMonth();
        $settings = (array) DB::table('salary_rule_settings')->orderBy('id')->first();
        $calculation = $this->calculationPreview($employee, $settings, $month, (int) $data['working_days']);
        $bonusAmount = $calculation['bonus_eligible'] ? (float) ($data['bonus_amount'] ?? 0) : 0;
        $this->ensureBonusWithinYearMaximum($bonusAmount, $calculation);

        try {
            DB::table('employee_salaries')->insert($this->payrollData($month, $calculation, $bonusAmount) + [
                'created_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $exception) {
            if (str_contains($exception->getMessage(), 'employee_salaries_employee_id_salary_month_unique')) {
                return back()->withInput()->withErrors(['employee_id' => 'A salary record already exists for this employee and month.']);
            }
            throw $exception;
        }

        return back()->with('success', 'Salary calculated and saved.');
    }

    public function update(Request $request, int $salary)
    {
        $existing = DB::table('employee_salaries')->where('id', $salary)->first();
        abort_unless($existing, 404);
        $data = $this->validatePayroll($request, $salary);
        $employee = $this->employeeRecord((int) $data['employee_id']);
        $month = Carbon::createFromFormat('Y-m', $data['salary_month'])->startOfMonth();
        $settings = (array) DB::table('salary_rule_settings')->orderBy('id')->first();
        $calculation = $this->calculationPreview($employee, $settings, $month, (int) $data['working_days']);
        $bonusAmount = $calculation['bonus_eligible'] ? (float) ($data['bonus_amount'] ?? 0) : 0;
        $this->ensureBonusWithinYearMaximum($bonusAmount, $calculation);

        DB::table('employee_salaries')->where('id', $salary)->update($this->payrollData($month, $calculation, $bonusAmount) + ['updated_at' => now()]);

        return back()->with('success', 'Salary record recalculated and updated.');
    }

    public function destroy(int $salary)
    {
        $deleted = DB::table('employee_salaries')->where('id', $salary)->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Salary record deleted.');
    }

    private function validatePayroll(Request $request, ?int $id = null): array
    {
        $month = $request->input('salary_month');
        $unique = Rule::unique('employee_salaries', 'employee_id')
            ->where(fn ($query) => $query->where('salary_month', $month.'-01'));
        if ($id) {
            $unique->ignore($id);
        }

        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id', $unique],
            'salary_month' => ['required', 'date_format:Y-m'],
            'working_days' => ['required', 'integer', 'min:1', 'max:31'],
            'bonus_amount' => ['nullable', 'numeric', 'min:0'],
        ], [
            'employee_id.unique' => 'A salary record already exists for this employee and month.',
        ]);

        $month = Carbon::createFromFormat('Y-m', $data['salary_month'])->startOfMonth();
        $this->ensureWorkingDaysFitMonth($month, (int) $data['working_days']);

        return $data;
    }

    private function employeeRecord(int $id): object
    {
        $employee = DB::table('employees')->where('id', $id)->where('status', 'active')->first();
        abort_unless($employee, 404, 'Select an active employee.');

        $deployment = DB::table('contract_deployments')
            ->join('contracts', 'contracts.id', '=', 'contract_deployments.contract_id')
            ->where('contract_deployments.employee_id', $id)
            ->where('contract_deployments.status', 'active')
            ->where('contracts.status', 'active')
            ->orderByDesc('contract_deployments.start_date')
            ->select('contracts.id as contract_id', 'contracts.contract_number')
            ->first();

        $employee->contract_id = $deployment?->contract_id;
        $employee->contract_number = $deployment?->contract_number;

        return $employee;
    }

    private function calculationPreview(object $employee, array $settings, Carbon $month, int $workingDays): array
    {
        $earnedWages = round((float) $employee->daily_wages_rate * $workingDays, 2);
        $epfExempt = (bool) $employee->epf_exempt;
        $esiExempt = (bool) $employee->esi_exempt;
        $pfBasis = min($earnedWages, (float) $settings['pf_monthly_wage_ceiling']);
        $pfEmployee = $epfExempt ? 0 : round($pfBasis * (float) $settings['pf_employee_rate'] / 100);
        $pfEmployer = $epfExempt ? 0 : round($pfBasis * (float) $settings['pf_employer_rate'] / 100);
        $esiApplicable = ! $esiExempt && $earnedWages > 0 && $earnedWages <= (float) $settings['esi_monthly_wage_limit'];
        $esiEmployee = $esiApplicable && (float) $employee->daily_wages_rate > (float) $settings['esi_employee_exemption_daily_wage']
            ? round($earnedWages * (float) $settings['esi_employee_rate'] / 100)
            : 0;
        $esiEmployer = $esiApplicable ? round($earnedWages * (float) $settings['esi_employer_rate'] / 100) : 0;
        $yearStart = $month->month >= 4 ? $month->copy()->month(4)->startOfMonth() : $month->copy()->subYear()->month(4)->startOfMonth();
        $priorPayrolls = DB::table('employee_salaries')->where('employee_id', $employee->id)
            ->where('salary_month', '>=', $yearStart->toDateString())->where('salary_month', '<', $month->toDateString())
            ->get(['working_days', 'earned_wages', 'bonus_amount']);
        $priorDays = (int) $priorPayrolls->sum('working_days');
        $priorBonusPaid = (float) $priorPayrolls->sum('bonus_amount');
        $bonusDays = $priorDays + $workingDays;
        $bonusEligibilityLimit = (float) $settings['bonus_monthly_eligibility_limit'];
        $bonusEligible = $bonusEligibilityLimit > 0
            && $earnedWages > 0
            && $earnedWages <= $bonusEligibilityLimit
            && $bonusDays >= (int) $settings['bonus_minimum_days'];
        $bonusMonthlyCeiling = (float) $settings['bonus_monthly_calculation_ceiling'];
        $bonusBasis = $priorPayrolls->sum(fn ($payroll): float => $bonusMonthlyCeiling > 0 ? min((float) $payroll->earned_wages, $bonusMonthlyCeiling) : (float) $payroll->earned_wages)
            + ($bonusMonthlyCeiling > 0 ? min($earnedWages, $bonusMonthlyCeiling) : $earnedWages);
        $deductions = $pfEmployee + $esiEmployee;

        $bonusMinimumGuideline = round($bonusBasis * (float) $settings['bonus_minimum_rate'] / 100, 2);
        $bonusMaximumGuideline = round($bonusBasis * (float) $settings['bonus_maximum_rate'] / 100, 2);

        return [
            'employee_id' => (int) $employee->id,
            'employee_code' => $employee->employee_code,
            'employee_name' => $employee->name,
            'designation' => $employee->role,
            'employee_category' => $employee->employee_category,
            'contract_id' => $employee->contract_id,
            'contract_number' => $employee->contract_number,
            'working_days' => $workingDays,
            'daily_wages_rate' => (float) $employee->daily_wages_rate,
            'earned_wages' => $earnedWages,
            'pf_wage_basis' => $pfBasis,
            'epf_exempt' => $epfExempt,
            'pf_employee_rate' => (float) $settings['pf_employee_rate'],
            'pf_employer_rate' => (float) $settings['pf_employer_rate'],
            'pf_employee_amount' => $pfEmployee,
            'pf_employer_amount' => $pfEmployer,
            'esi_applicable' => $esiApplicable,
            'esi_exempt' => $esiExempt,
            'esi_employee_rate' => (float) $settings['esi_employee_rate'],
            'esi_employer_rate' => (float) $settings['esi_employer_rate'],
            'esi_employee_amount' => $esiEmployee,
            'esi_employer_amount' => $esiEmployer,
            'bonus_eligible' => $bonusEligible,
            'bonus_eligible_days' => $bonusDays,
            'bonus_amount' => 0,
            'bonus_basis' => $bonusBasis,
            'bonus_minimum_rate' => (float) $settings['bonus_minimum_rate'],
            'bonus_maximum_rate' => (float) $settings['bonus_maximum_rate'],
            'bonus_minimum_guideline' => $bonusMinimumGuideline,
            'bonus_maximum_guideline' => $bonusMaximumGuideline,
            'bonus_maximum_remaining' => max(0, $bonusMaximumGuideline - $priorBonusPaid),
            'bonus_minimum_days' => (int) $settings['bonus_minimum_days'],
            'bonus_monthly_eligibility_limit' => (float) $settings['bonus_monthly_eligibility_limit'],
            'total_deductions' => $deductions,
            'gross_salary' => $earnedWages,
            'net_salary' => $earnedWages - $deductions,
        ];
    }

    private function ensureWorkingDaysFitMonth(Carbon $month, int $workingDays): void
    {
        if ($workingDays > $month->daysInMonth) {
            throw ValidationException::withMessages([
                'working_days' => 'Working days cannot exceed '.$month->daysInMonth.' days for the selected month.',
            ]);
        }
    }

    private function ensureBonusWithinYearMaximum(float $bonusAmount, array $calculation): void
    {
        if ($bonusAmount > (float) $calculation['bonus_maximum_remaining']) {
            throw ValidationException::withMessages([
                'bonus_amount' => 'Bonus amount exceeds the remaining configured maximum for this accounting year (₹'.number_format((float) $calculation['bonus_maximum_remaining'], 2).').',
            ]);
        }
    }

    private function payrollData(Carbon $month, array $calculation, float $bonusAmount): array
    {
        $calculation['bonus_amount'] = round($bonusAmount, 2);
        $calculation['gross_salary'] = round($calculation['earned_wages'] + $bonusAmount, 2);
        $calculation['net_salary'] = round($calculation['gross_salary'] - $calculation['total_deductions'], 2);

        return [
            'employee_id' => $calculation['employee_id'],
            'salary_month' => $month->toDateString(),
            'employee_code_snapshot' => $calculation['employee_code'],
            'employee_name_snapshot' => $calculation['employee_name'],
            'designation_snapshot' => $calculation['designation'],
            'contract_number_snapshot' => $calculation['contract_number'],
            'working_days' => $calculation['working_days'],
            'daily_wages_rate' => $calculation['daily_wages_rate'],
            'earned_wages' => $calculation['earned_wages'],
            'pf_wage_basis' => $calculation['pf_wage_basis'],
            'epf_exempt' => $calculation['epf_exempt'],
            'pf_employee_rate' => $calculation['pf_employee_rate'],
            'pf_employer_rate' => $calculation['pf_employer_rate'],
            'pf_employee_amount' => $calculation['pf_employee_amount'],
            'pf_employer_amount' => $calculation['pf_employer_amount'],
            'esi_applicable' => $calculation['esi_applicable'],
            'esi_exempt' => $calculation['esi_exempt'],
            'esi_employee_rate' => $calculation['esi_employee_rate'],
            'esi_employer_rate' => $calculation['esi_employer_rate'],
            'esi_employee_amount' => $calculation['esi_employee_amount'],
            'esi_employer_amount' => $calculation['esi_employer_amount'],
            'bonus_eligible' => $calculation['bonus_eligible'],
            'bonus_eligible_days' => $calculation['bonus_eligible_days'],
            'bonus_amount' => $calculation['bonus_amount'],
            'gross_salary' => $calculation['gross_salary'],
            'total_deductions' => $calculation['total_deductions'],
            'net_salary' => $calculation['net_salary'],
        ];
    }
}

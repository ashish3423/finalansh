<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EnterpriseController extends Controller
{
    private function modules(): array
    {
        return config('enterprise');
    }

    private function module(string $key): array
    {
        abort_unless(isset($this->modules()[$key]), 404);

        return $this->modules()[$key];
    }

    public function dashboard()
    {
        $counts = [
            'contracts' => DB::table('contracts')->count(),
            'active_contracts' => DB::table('contracts')->where('status', 'active')->count(),
            'employees' => DB::table('employees')->where('status', 'active')->count(),
            'vehicles' => DB::table('vehicles')->whereIn('status', ['available', 'deployed'])->count(),
            'pending_invoices' => DB::table('invoices')->whereIn('status', ['sent', 'part_paid', 'overdue'])->count(),
            'service_logs' => DB::table('service_logs')->whereDate('service_at', today())->count(),
        ];
        $recentContracts = DB::table('contracts')
            ->join('government_clients', 'government_clients.id', '=', 'contracts.government_client_id')
            ->select('contracts.*', 'government_clients.name as client_name')
            ->orderByDesc('contracts.created_at')->limit(6)->get();

        return view('enterprise.dashboard', compact('counts', 'recentContracts'));
    }

    public function index(Request $request, string $module)
    {
        $definition = $this->module($module);
        $query = DB::table($definition['table']);

        foreach ($definition['filters'] as $filter => $options) {
            if ($request->filled($filter) && array_key_exists($request->input($filter), $options)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($builder) use ($definition, $search) {
                foreach ($definition['search'] as $column) {
                    $builder->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        $rows = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $options = $this->fieldOptions($definition);
        $relationLabels = $this->relationLabels($definition);

        return view('enterprise.index', compact('module', 'definition', 'rows', 'options', 'relationLabels'));
    }

    public function store(Request $request, string $module)
    {
        $definition = $this->module($module);
        $data = $this->validated($request, $definition);
        $photo = $data['photo'] ?? null;
        unset($data['photo']);
        if ($module === 'employees') {
            if ($photo) {
                $data['photo_path'] = $photo->store('employees', 'public');
            }
            DB::transaction(function () use ($data): void {
                $employeeData = $data;
                $employeeData['employee_code'] = $this->nextEmployeeCode();
                DB::table('employees')->insert($employeeData + ['created_at' => now(), 'updated_at' => now()]);
            }, 3);
        } else {
            DB::table($definition['table'])->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        }

        return back()->with('success', $definition['singular'].' created successfully.');
    }

    public function update(Request $request, string $module, int $id)
    {
        $definition = $this->module($module);
        $record = DB::table($definition['table'])->where('id', $id)->first();
        abort_unless($record, 404);
        $data = $this->validated($request, $definition, $id);
        $photo = $data['photo'] ?? null;
        unset($data['photo']);
        if ($module === 'employees' && $photo) {
            $data['photo_path'] = $photo->store('employees', 'public');
        }
        DB::table($definition['table'])->where('id', $id)->update($data + ['updated_at' => now()]);
        if ($module === 'employees' && $photo && $record->photo_path) {
            Storage::disk('public')->delete($record->photo_path);
        }

        return back()->with('success', $definition['singular'].' updated successfully.');
    }

    public function destroy(string $module, int $id)
    {
        $definition = $this->module($module);
        $record = DB::table($definition['table'])->where('id', $id)->first();
        abort_unless($record, 404);

        try {
            $deleted = DB::table($definition['table'])->where('id', $id)->delete();
        } catch (QueryException) {
            return back()->with('error', 'This record is being used by another record and cannot be deleted.');
        }

        abort_unless($deleted, 404);
        if ($module === 'employees' && $record->photo_path) {
            Storage::disk('public')->delete($record->photo_path);
        }

        return back()->with('success', $definition['singular'].' deleted successfully.');
    }

    private function validated(Request $request, array $definition, ?int $id = null): array
    {
        $rules = [];
        foreach ($definition['fields'] as $column => $field) {
            $rule = $field[2];
            if ($id && str_contains($rule, '|unique:')) {
                $rule .= ','.$id;
            }
            $rules[$column] = $rule;
        }

        $data = $request->validate($rules);
        foreach ($data as $key => $value) {
            if (is_string($value) && str_contains($value, 'T') && ($definition['fields'][$key][1] ?? null) === 'datetime-local') {
                $data[$key] = str_replace('T', ' ', $value).(strlen($value) === 16 ? ':00' : '');
            }
        }

        return $data;
    }

    private function nextEmployeeCode(): string
    {
        $period = now('Asia/Kolkata')->format('my');
        $prefix = 'AE/'.$period.'/';
        $codes = DB::table('employees')
            ->where('employee_code', 'like', $prefix.'%')
            ->lockForUpdate()
            ->pluck('employee_code');
        $lastNumber = $codes->map(fn (string $code): int => (int) substr($code, strrpos($code, '/') + 1))->max() ?? 0;

        return $prefix.str_pad((string) ($lastNumber + 1), 2, '0', STR_PAD_LEFT);
    }

    private function fieldOptions(array $definition): array
    {
        $options = [];
        foreach ($definition['fields'] as $column => $field) {
            if (in_array($field[1], ['relation', 'relation_optional'], true)) {
                $options[$column] = DB::table($field[3])->orderBy($field[4])->pluck($field[4], 'id')->all();
            }
        }

        return $options;
    }

    private function relationLabels(array $definition): array
    {
        $labels = [];
        foreach ($definition['fields'] as $column => $field) {
            if (in_array($field[1], ['relation', 'relation_optional'], true)) {
                $labels[$column] = DB::table($field[3])->pluck($field[4], 'id')->all();
            }
        }

        return $labels;
    }
}

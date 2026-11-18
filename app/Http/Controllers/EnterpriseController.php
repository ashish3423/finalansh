<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
            $query->where(function ($builder) use ($definition, $search, $module) {
                foreach ($definition['search'] as $column) {
                    $builder->orWhere($column, 'like', '%'.$search.'%');
                }

                if ($module === 'deployments') {
                    $pattern = '%'.$search.'%';
                    $builder->orWhereExists(function ($subquery) use ($pattern) {
                        $subquery->selectRaw('1')->from('contracts')
                            ->whereColumn('contracts.id', 'contract_deployments.contract_id')
                            ->where('contracts.contract_number', 'like', $pattern);
                    })->orWhereExists(function ($subquery) use ($pattern) {
                        $subquery->selectRaw('1')->from('employees')
                            ->whereColumn('employees.id', 'contract_deployments.employee_id')
                            ->where('employees.name', 'like', $pattern);
                    })->orWhereExists(function ($subquery) use ($pattern) {
                        $subquery->selectRaw('1')->from('vehicles')
                            ->whereColumn('vehicles.id', 'contract_deployments.vehicle_id')
                            ->where('vehicles.registration_number', 'like', $pattern);
                    });
                }
            });
        }

        $rows = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $options = $this->fieldOptions($definition);
        $relationLabels = $this->relationLabels($definition, $options);
        $uniqueValues = [];
        foreach ($definition['fields'] as $column => $field) {
            foreach (explode('|', $field[2]) as $rule) {
                if (! str_starts_with($rule, 'unique:')) {
                    continue;
                }
                [$table, $uniqueColumn] = array_pad(explode(',', substr($rule, 7)), 2, $column);
                $uniqueValues[$column] = DB::table($table)->pluck($uniqueColumn)->filter()->values()->all();
            }
        }
        $deploymentContracts = $deploymentEmployees = $deploymentVehicles = $assignedEmployeeRows = $assignedVehicleRows = $clientDetails = [];
        $vehicleModelMeta = $vehicleModelCategories = [];
        if ($module === 'vehicles') {
            $vehicleModels = DB::table('ambulance_types')->get(['id', 'service_type', 'category']);
            foreach ($vehicleModels as $vehicleModel) {
                $vehicleModelMeta[$vehicleModel->id] = ['service_type' => $vehicleModel->service_type, 'category' => $vehicleModel->category];
                if ($vehicleModel->category !== null) {
                    $vehicleModelCategories[$vehicleModel->service_type][$vehicleModel->category] = $vehicleModel->category;
                }
            }
            foreach ($vehicleModelCategories as $type => $categories) {
                $vehicleModelCategories[$type] = array_values($categories);
            }
        }
        if ($module === 'contracts') {
            $clientDetails = DB::table('government_clients')->get(['id', 'department', 'address'])->keyBy('id');
        }
        if ($module === 'deployments') {
            $deploymentContracts = DB::table('contracts')->where('status', 'active')->orderBy('contract_number')->get(['id', 'contract_number', 'service_type']);
            $deploymentEmployees = DB::table('employees')->where('status', 'active')->orderBy('employee_code')->get(['id', 'employee_code', 'name', 'role']);
            $deploymentVehicles = DB::table('vehicles')
                ->leftJoin('ambulance_types as at', 'at.id', '=', 'vehicles.ambulance_type_id')
                ->whereNotIn('vehicles.status', ['maintenance', 'inactive'])
                ->orderBy('vehicles.registration_number')
                ->get(['vehicles.id', 'vehicles.registration_number', 'vehicles.service_type', 'vehicles.year', 'at.service_type as model_service_type', 'at.category as ambulance_category', 'at.name as ambulance_model']);
            $activeAssignments = DB::table('contract_deployments as d')
                ->join('contracts as c', 'c.id', '=', 'd.contract_id')
                ->where('d.status', 'active')->where('c.status', 'active')
                ->get(['d.id', 'd.employee_id', 'd.vehicle_id']);
            foreach ($activeAssignments as $assignment) {
                if ($assignment->employee_id) {
                    $assignedEmployeeRows[$assignment->employee_id] = $assignment->id;
                }
                if ($assignment->vehicle_id) {
                    $assignedVehicleRows[$assignment->vehicle_id] = $assignment->id;
                }
            }
            foreach ($deploymentEmployees as $employee) {
                $relationLabels['employee_id'][$employee->id] = trim($employee->employee_code.' — '.$employee->name);
            }
        }

        return view('enterprise.index', compact('module', 'definition', 'rows', 'options', 'relationLabels', 'uniqueValues', 'deploymentContracts', 'deploymentEmployees', 'deploymentVehicles', 'assignedEmployeeRows', 'assignedVehicleRows', 'clientDetails', 'vehicleModelMeta', 'vehicleModelCategories'));
    }

    public function store(Request $request, string $module)
    {
        $definition = $this->module($module);
        if ($module === 'deployments') {
            $this->storeDeployments($request);

            return back()->with('success', 'Deployment assignments created successfully.');
        }
        $data = $this->validated($request, $definition);
        if ($module === 'contracts') {
            $data['title'] = $data['contract_number'];
        }
        if ($module === 'vehicles') {
            $this->assertVehicleModelMatchesType($data, $request->input('vehicle_model_category'));
        }
        if ($module === 'employees') {
            $this->processEmployeeUploads($data, $definition);
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
        if ($module === 'deployments') {
            $this->updateDeployment($request, $record);

            return back()->with('success', 'Deployment assignment moved successfully.');
        }
        $data = $this->validated($request, $definition, $id);
        if ($module === 'contracts') {
            $data['title'] = $data['contract_number'];
        }
        if ($module === 'vehicles') {
            $this->assertVehicleModelMatchesType($data, $request->input('vehicle_model_category'));
        }
        if ($module === 'designations') {
            $oldName = $record->name;
            DB::transaction(function () use ($definition, $data, $id, $oldName): void {
                DB::table($definition['table'])->where('id', $id)->update($data + ['updated_at' => now()]);
                if ($oldName !== $data['name']) {
                    DB::table('employees')->where('role', $oldName)->update(['role' => $data['name'], 'updated_at' => now()]);
                }
            });

            return back()->with('success', $definition['singular'].' updated successfully.');
        }
        $oldUploadPaths = $module === 'employees' ? $this->processEmployeeUploads($data, $definition, $record) : [];
        DB::table($definition['table'])->where('id', $id)->update($data + ['updated_at' => now()]);
        foreach ($oldUploadPaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return back()->with('success', $definition['singular'].' updated successfully.');
    }

    public function destroy(string $module, int $id)
    {
        $definition = $this->module($module);
        $record = DB::table($definition['table'])->where('id', $id)->first();
        abort_unless($record, 404);

        if ($module === 'designations' && DB::table('employees')->where('role', $record->name)->exists()) {
            return back()->with('error', 'This designation is assigned to employees and cannot be deleted.');
        }
        if ($module === 'ambulance-types' && DB::table('vehicles')->where('ambulance_type_id', $record->id)->exists()) {
            return back()->with('error', 'This vehicle model is assigned to fleet vehicles and cannot be deleted.');
        }

        try {
            $deleted = DB::table($definition['table'])->where('id', $id)->delete();
        } catch (QueryException) {
            return back()->with('error', 'This record is being used by another record and cannot be deleted.');
        }

        abort_unless($deleted, 404);
        if ($module === 'deployments' && $record->vehicle_id) {
            $stillAssigned = DB::table('contract_deployments as d')->join('contracts as c', 'c.id', '=', 'd.contract_id')
                ->where('d.vehicle_id', $record->vehicle_id)->where('d.status', 'active')->where('c.status', 'active')->exists();
            if (! $stillAssigned) {
                DB::table('vehicles')->where('id', $record->vehicle_id)->where('status', 'deployed')->update(['status' => 'available', 'updated_at' => now()]);
            }
        }
        if ($module === 'employees') {
            foreach ($this->employeeUploadPathColumns($definition) as $pathColumn) {
                if ($record->{$pathColumn}) {
                    Storage::disk('public')->delete($record->{$pathColumn});
                }
            }
        }

        return back()->with('success', $definition['singular'].' deleted successfully.');
    }

    private function storeDeployments(Request $request): void
    {
        $data = $this->deploymentInput($request, true);
        $contract = DB::table('contracts')->where('id', $data['contract_id'])->where('status', 'active')->first();
        if (! $contract) {
            throw ValidationException::withMessages(['contract_id' => 'Choose an active contract.']);
        }
        if (in_array($contract->service_type, ['ambulance', 'cab'], true)) {
            foreach ($data['employee_assignments'] ?? [] as $assignment) {
                if (! in_array($assignment['position'] ?? null, ['driver', 'assistant', 'paramedic', 'attendant', 'other'], true)) {
                    throw ValidationException::withMessages(['employee_assignments' => 'Choose a crew role for each employee assigned to an ambulance or cab contract.']);
                }
            }
        }

        DB::transaction(function () use ($data, $contract): void {
            $employeeIds = collect($data['employee_assignments'] ?? [])->pluck('employee_id')->map(fn ($id) => (int) $id)->all();
            $vehicleIds = array_map('intval', $data['vehicle_ids'] ?? []);
            if ($employeeIds) {
                DB::table('employees')->whereIn('id', $employeeIds)->lockForUpdate()->get();
            }
            if ($vehicleIds) {
                DB::table('vehicles')->whereIn('id', $vehicleIds)->lockForUpdate()->get();
            }
            $this->assertResourcesAvailable($employeeIds, $vehicleIds);
            if ($vehicleIds && in_array($contract->service_type, ['ambulance', 'cab'], true)) {
                $matchingVehicles = DB::table('vehicles')->whereIn('id', $vehicleIds)->where('service_type', $contract->service_type)->count();
                if ($matchingVehicles !== count($vehicleIds)) {
                    throw ValidationException::withMessages(['vehicle_ids' => 'Choose vehicles that match the contract service type.']);
                }
            } elseif ($vehicleIds) {
                throw ValidationException::withMessages(['vehicle_ids' => 'Vehicles can only be assigned to ambulance or cab contracts.']);
            }

            foreach ($data['employee_assignments'] ?? [] as $assignment) {
                $position = $assignment['position'] ?? null;
                if ($contract->service_type === 'manpower') {
                    $position = DB::table('employees')->where('id', $assignment['employee_id'])->value('role');
                }
                $this->insertDeployment($contract, (int) $assignment['employee_id'], null, $position);
            }
            foreach ($vehicleIds as $vehicleId) {
                $this->insertDeployment($contract, null, $vehicleId, null);
                DB::table('vehicles')->where('id', $vehicleId)->update(['status' => 'deployed', 'updated_at' => now()]);
            }
        }, 3);
    }

    private function updateDeployment(Request $request, object $record): void
    {
        $data = $this->deploymentInput($request, false);
        $contract = DB::table('contracts')->where('id', $data['contract_id'])->where('status', 'active')->first();
        if (! $contract) {
            throw ValidationException::withMessages(['contract_id' => 'Choose an active contract.']);
        }
        $combinedRecord = (bool) $record->employee_id && (bool) $record->vehicle_id;
        $moveResource = $combinedRecord ? ($data['move_resource'] ?? null) : ($record->employee_id ? 'employee' : 'vehicle');
        $employeeId = $moveResource === 'employee' ? (int) ($data['employee_id'] ?? 0) : null;
        $vehicleId = $moveResource === 'vehicle' ? (int) ($data['vehicle_id'] ?? 0) : null;
        if ((! $employeeId && ! $vehicleId) || ($combinedRecord && ! $moveResource)) {
            throw ValidationException::withMessages(['resource' => 'Select one resource matching this deployment.']);
        }
        if ($employeeId && in_array($contract->service_type, ['ambulance', 'cab'], true)
            && ! in_array($data['position'] ?? null, ['driver', 'assistant', 'paramedic', 'attendant', 'other'], true)) {
            throw ValidationException::withMessages(['position' => 'Choose a crew role for this employee.']);
        }

        DB::transaction(function () use ($record, $contract, $employeeId, $vehicleId, $data, $combinedRecord, $moveResource): void {
            if ($employeeId) {
                DB::table('employees')->where('id', $employeeId)->lockForUpdate()->first();
            }
            if ($vehicleId) {
                DB::table('vehicles')->where('id', $vehicleId)->lockForUpdate()->first();
            }
            $this->assertResourcesAvailable($employeeId ? [$employeeId] : [], $vehicleId ? [$vehicleId] : [], (int) $record->id);
            if ($vehicleId && ($contract->service_type !== DB::table('vehicles')->where('id', $vehicleId)->value('service_type'))) {
                throw ValidationException::withMessages(['vehicle_id' => 'Choose a vehicle that matches the contract service type.']);
            }
            if ($combinedRecord) {
                $sourceContract = DB::table('contracts')->where('id', $record->contract_id)->first();
                if ($moveResource === 'employee') {
                    $this->insertDeployment($sourceContract, null, (int) $record->vehicle_id, null);
                } else {
                    $employeeRole = DB::table('employees')->where('id', $record->employee_id)->value('role');
                    $this->insertDeployment($sourceContract, (int) $record->employee_id, null, $record->position ?: $employeeRole);
                }
            }
            $position = $employeeId ? ($data['position'] ?? null) : null;
            if ($employeeId && $contract->service_type === 'manpower') {
                $position = DB::table('employees')->where('id', $employeeId)->value('role');
            }
            DB::table('contract_deployments')->where('id', $record->id)->update([
                'contract_id' => $contract->id, 'service_type' => $contract->service_type,
                'employee_id' => $employeeId, 'vehicle_id' => $vehicleId,
                'position' => $position,
                'start_date' => $record->start_date ?: today()->toDateString(), 'monthly_rate' => 0,
                'status' => 'active', 'updated_at' => now(),
            ]);
            if ($vehicleId) {
                DB::table('vehicles')->where('id', $vehicleId)->update(['status' => 'deployed', 'updated_at' => now()]);
            }
            if ($record->vehicle_id && (int) $record->vehicle_id !== (int) $vehicleId) {
                $stillAssigned = DB::table('contract_deployments as d')->join('contracts as c', 'c.id', '=', 'd.contract_id')
                    ->where('d.vehicle_id', $record->vehicle_id)->where('d.status', 'active')->where('c.status', 'active')
                    ->where('d.id', '<>', $record->id)->exists();
                if (! $stillAssigned) {
                    DB::table('vehicles')->where('id', $record->vehicle_id)->where('status', 'deployed')->update(['status' => 'available', 'updated_at' => now()]);
                }
            }
        }, 3);
    }

    private function deploymentInput(Request $request, bool $creating): array
    {
        $rules = [
            'contract_id' => ['required', 'integer', 'exists:contracts,id'],
            'employee_assignments' => ['nullable', 'array'],
            'employee_assignments.*.employee_id' => ['required', 'integer', 'distinct', 'exists:employees,id'],
            'employee_assignments.*.position' => ['nullable', 'in:driver,assistant,paramedic,attendant,other'],
            'vehicle_ids' => ['nullable', 'array'],
            'vehicle_ids.*' => ['required', 'integer', 'distinct', 'exists:vehicles,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'position' => ['nullable', 'in:driver,assistant,paramedic,attendant,other'],
            'move_resource' => ['nullable', 'in:employee,vehicle'],
        ];
        $data = $request->validate($rules);
        if ($creating && empty($data['employee_assignments']) && empty($data['vehicle_ids'])) {
            throw ValidationException::withMessages(['resource' => 'Add at least one employee or vehicle.']);
        }
        foreach ($data['employee_assignments'] ?? [] as $assignment) {
            if (! DB::table('employees')->where('id', $assignment['employee_id'])->where('status', 'active')->exists()) {
                throw ValidationException::withMessages(['employee_assignments' => 'Only active employees can be deployed.']);
            }
        }
        if (! empty($data['employee_id']) && ! DB::table('employees')->where('id', $data['employee_id'])->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['employee_id' => 'Only active employees can be deployed.']);
        }
        foreach ($data['vehicle_ids'] ?? [] as $vehicleId) {
            if (! DB::table('vehicles')->where('id', $vehicleId)->whereNotIn('status', ['maintenance', 'inactive'])->exists()) {
                throw ValidationException::withMessages(['vehicle_ids' => 'Vehicles in maintenance or inactive status cannot be deployed.']);
            }
        }
        if (! empty($data['vehicle_id']) && ! DB::table('vehicles')->where('id', $data['vehicle_id'])->whereNotIn('status', ['maintenance', 'inactive'])->exists()) {
            throw ValidationException::withMessages(['vehicle_id' => 'Vehicles in maintenance or inactive status cannot be deployed.']);
        }

        return $data;
    }

    private function assertResourcesAvailable(array $employeeIds, array $vehicleIds, ?int $exceptDeploymentId = null): void
    {
        $check = DB::table('contract_deployments as d')->join('contracts as c', 'c.id', '=', 'd.contract_id')
            ->where('d.status', 'active')->where('c.status', 'active')
            ->when($exceptDeploymentId, fn ($query) => $query->where('d.id', '<>', $exceptDeploymentId));
        if ($employeeIds && (clone $check)->whereIn('d.employee_id', $employeeIds)->exists()) {
            throw ValidationException::withMessages(['employee_assignments' => 'One or more selected employees are already assigned to an active contract.']);
        }
        if ($vehicleIds && (clone $check)->whereIn('d.vehicle_id', $vehicleIds)->exists()) {
            throw ValidationException::withMessages(['vehicle_ids' => 'One or more selected vehicles are already assigned to an active contract.']);
        }
    }

    private function insertDeployment(object $contract, ?int $employeeId, ?int $vehicleId, ?string $position): void
    {
        DB::table('contract_deployments')->insert([
            'contract_id' => $contract->id, 'service_type' => $contract->service_type,
            'employee_id' => $employeeId, 'vehicle_id' => $vehicleId, 'position' => $position,
            'start_date' => today()->toDateString(), 'end_date' => null, 'monthly_rate' => 0,
            'status' => 'active', 'notes' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);
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
        foreach ($definition['fields'] as $column => $field) {
            if ($field[1] !== 'conditional-file' || (string) ($data[$field[3]] ?? '') !== (string) $field[4] || $request->hasFile($column)) {
                continue;
            }

            $existingPath = $id ? DB::table($definition['table'])->where('id', $id)->value($column.'_path') : null;
            if (! $existingPath) {
                throw ValidationException::withMessages([$column => 'Upload the selected document before saving.']);
            }
        }

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
            if ($field[1] === 'dynamic-select' && $column === 'role') {
                $options[$column] = DB::table('designations')->orderBy('name')->pluck('name', 'name')->all();
            }
            if (in_array($field[1], ['relation', 'relation_optional'], true)) {
                $options[$column] = DB::table($field[3])->orderBy($field[4])->pluck($field[4], 'id')->all();
            }
            if ($field[1] === 'conditional-relation' && $field[5] === 'ambulance_types') {
                $options[$column] = DB::table($field[5])->orderBy('service_type')->orderBy('category')->orderBy('name')->pluck('name', 'id')->all();
            }
        }

        return $options;
    }

    private function processEmployeeUploads(array &$data, array $definition, ?object $record = null): array
    {
        $oldPaths = [];
        foreach ($definition['fields'] as $name => $field) {
            if (! in_array($field[1], ['file', 'conditional-file'], true)) {
                continue;
            }

            $upload = $data[$name] ?? null;
            unset($data[$name]);
            $pathColumn = $name === 'photo' ? 'photo_path' : $name.'_path';
            $existingPath = $record->{$pathColumn} ?? null;

            if ($field[1] === 'conditional-file' && (string) ($data[$field[3]] ?? '') !== (string) $field[4]) {
                if ($existingPath) {
                    $oldPaths[] = $existingPath;
                }
                $data[$pathColumn] = null;

                continue;
            }

            if ($upload) {
                $directory = $name === 'photo' ? 'employees' : 'employee-documents';
                $data[$pathColumn] = $upload->store($directory, 'public');
                if ($existingPath) {
                    $oldPaths[] = $existingPath;
                }
            }
        }

        return $oldPaths;
    }

    private function employeeUploadPathColumns(array $definition): array
    {
        $columns = [];
        foreach ($definition['fields'] as $name => $field) {
            if (in_array($field[1], ['file', 'conditional-file'], true)) {
                $columns[] = $name === 'photo' ? 'photo_path' : $name.'_path';
            }
        }

        return $columns;
    }

    private function relationLabels(array $definition, array $options = []): array
    {
        $labels = [];
        foreach ($definition['fields'] as $column => $field) {
            if (in_array($field[1], ['relation', 'relation_optional'], true)) {
                $labels[$column] = DB::table($field[3])->pluck($field[4], 'id')->all();
            }
            if ($field[1] === 'conditional-relation') {
                if ($field[5] === 'ambulance_types') {
                    $labels[$column] = DB::table($field[5])->get(['id', 'category', 'name'])
                        ->mapWithKeys(fn ($record) => [$record->id => ($record->category ? $record->category.' — ' : '').$record->name])->all();
                } else {
                    $labels[$column] = $options[$column] ?? [];
                }
            }
        }

        return $labels;
    }

    private function assertVehicleModelMatchesType(array $data, ?string $modelCategory): void
    {
        $modelId = $data['ambulance_type_id'] ?? null;
        $serviceType = $data['service_type'] ?? null;
        if (! $modelId || ! $modelCategory || ! DB::table('ambulance_types')->where('id', $modelId)->where('service_type', $serviceType)->where('category', $modelCategory)->exists()) {
            throw ValidationException::withMessages([
                'ambulance_type_id' => 'Choose a model that matches the selected vehicle type and category.',
            ]);
        }
    }
}

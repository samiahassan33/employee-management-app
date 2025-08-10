<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\IndexEmployeeRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeService $employeeService) {}

    public function index(IndexEmployeeRequest $request)
    {
        $employees = $this->employeeService->getPaginated($request->validated());
        
        return EmployeeResource::collection($employees);
    }

    public function show(Employee $employee)
    {
        $this->authorize('view', $employee);
        
        return EmployeeResource::make($employee->load('department'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            $employee = $this->employeeService->create($request->validated());
            
            return EmployeeResource::make($employee->load('department'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $updatedEmployee = $this->employeeService->update($employee->id, $request->validated());
            
            return EmployeeResource::make($updatedEmployee->load('department'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Employee $employee)
    {
        $this->authorize('delete', $employee);
        
        try {
            $this->employeeService->delete($employee->id);
            
            return response()->json([
                'message' => 'Employee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(string $term)
    {
        $employees = $this->employeeService->search($term);
        
        return EmployeeResource::collection($employees);
    }

    public function getByDepartment(int $departmentId)
    {
        $employees = $this->employeeService->getByDepartment($departmentId);
        
        return EmployeeResource::collection($employees);
    }

    public function restore(int $id)
    {
        $this->authorize('create', Employee::class);
        
        $employee = $this->employeeService->restore($id);
        
        return EmployeeResource::make($employee);
    }

    public function exportCsv(IndexEmployeeRequest $request)
    {
        $filename = 'employees_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        return $this->employeeService->exportToCsv($request->validated(), $filename);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\DepartmentService;

class DepartmentController extends Controller
{
    public function __construct(private DepartmentService $departmentService) {}

    public function index(Request $request)
    {
        $departments = $this->departmentService->getPaginated($request->all());
        
        return DepartmentResource::collection($departments);
    }

    public function show(Department $department)
    {
        return DepartmentResource::make($department->load('employees'));
    }

    public function store(StoreDepartmentRequest $request)
    {
        try {
            $department = $this->departmentService->create($request->validated());
            
            return DepartmentResource::make($department);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        try {
            $updatedDepartment = $this->departmentService->update($department->id, $request->validated());
            
            return DepartmentResource::make($updatedDepartment);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Department $department)
    {
        try {
            // Check if department has employees
            if ($department->employees()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete department with employees'
                ], 422);
            }

            $this->departmentService->delete($department->id);
            
            return response()->json([
                'message' => 'Department deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function withEmployees()
    {
        $departments = $this->departmentService->getWithEmployees();
        
        return DepartmentResource::collection($departments);
    }
}

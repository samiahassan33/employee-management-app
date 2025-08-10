<?php
namespace App\Services;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeService
{
    public function __construct(private EmployeeRepositoryInterface $employeeRepository) {}

    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        return $this->employeeRepository->getPaginated($filters, $perPage);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $employee = $this->employeeRepository->create($data);
            
            // Log employee creation
            Log::info('Employee created', [
                'employee_id' => $employee->id,
                'created_by' => auth()->id(),
            ]);

            return $employee;
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $employee = $this->employeeRepository->update($id, $data);
            
            Log::info('Employee updated', [
                'employee_id' => $id,
                'updated_by' => auth()->id(),
            ]);

            return $employee;
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $result = $this->employeeRepository->delete($id);
            
            Log::info('Employee deleted', [
                'employee_id' => $id,
                'deleted_by' => auth()->id(),
            ]);

            return $result;
        });
    }

    public function restore(int $id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();
        
        Log::info('Employee restored', [
            'employee_id' => $id,
            'restored_by' => auth()->id(),
        ]);

        return $employee->load('department');
    }

    public function search(string $term)
    {
        return $this->employeeRepository->search($term);
    }

    public function getByDepartment(int $departmentId)
    {
        return $this->employeeRepository->getByDepartment($departmentId);
    }

    public function exportToCsv(array $filters = [], string $filename = 'employees.csv')
    {
        $employees = $this->employeeRepository->getPaginated($filters, 1000);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Department', 'Joining Date']);
            
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->id,
                    $employee->name,
                    $employee->email,
                    $employee->phone,
                    $employee->department->name ?? '',
                    $employee->joining_date->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getStatistics()
    {
        return [
            'total_employees' => $this->employeeRepository->count(),
            'recent_joinings' => Employee::where('joining_date', '>=', now()->subDays(30))->count(),
            'by_department' => Employee::select('department_id', DB::raw('count(*) as count'))
                                    ->groupBy('department_id')
                                    ->with('department:id,name')
                                    ->get(),
        ];
    }
}

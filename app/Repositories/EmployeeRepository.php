<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    public function model()
    {
        return Employee::class;
    }

    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        $query = $this->model->with(['department'])->newQuery();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('department', function ($dept) use ($search) {
                      $dept->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Department filter
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // Date range filter
        if (!empty($filters['start_date'])) {
            $query->whereDate('joining_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('joining_date', '<=', $filters['end_date']);
        }

        // Sorting
        $sortBy = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getByDepartment(int $departmentId)
    {
        return $this->model->with(['department'])
                          ->where('department_id', $departmentId)
                          ->get();
    }

    public function search(string $term)
    {
        return $this->model->with(['department'])
                          ->where('name', 'LIKE', "%{$term}%")
                          ->orWhere('email', 'LIKE', "%{$term}%")
                          ->get();
    }
}


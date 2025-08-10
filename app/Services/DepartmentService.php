<?php

namespace App\Services;

use App\Repositories\Contracts\DepartmentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentService
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository) {}

    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        $query = $this->departmentRepository->model()->newQuery();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $sortBy = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $department = $this->departmentRepository->create($data);
            
            Log::info('Department created', [
                'department_id' => $department->id,
                'created_by' => auth()->id(),
            ]);

            return $department;
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $department = $this->departmentRepository->update($id, $data);
            
            Log::info('Department updated', [
                'department_id' => $id,
                'updated_by' => auth()->id(),
            ]);

            return $department;
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $result = $this->departmentRepository->delete($id);
            
            Log::info('Department deleted', [
                'department_id' => $id,
                'deleted_by' => auth()->id(),
            ]);

            return $result;
        });
    }

    public function getWithEmployees()
    {
        return $this->departmentRepository->getWithEmployeesCount();
    }

    public function findByCode(string $code)
    {
        return $this->departmentRepository->findByCode($code);
    }
}
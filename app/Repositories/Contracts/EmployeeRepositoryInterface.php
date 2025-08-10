<?php
namespace App\Repositories\Contracts;

interface EmployeeRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15);
    public function findByEmail(string $email);
    public function getByDepartment(int $departmentId);
    public function search(string $term);
}

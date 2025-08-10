<?php
namespace App\Repositories\Contracts;

interface DepartmentRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $code);
    public function getWithEmployeesCount();
}
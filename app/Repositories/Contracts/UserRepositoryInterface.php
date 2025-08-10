<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email);
    public function createWithRole(array $data, string $role);
}

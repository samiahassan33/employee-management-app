<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;

class EmployeePolicy
{
    /**
     * Create a new policy instance.
     */
        public function viewAny(User $user): bool
    {
        return $user->can('employees.view');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->can('employees.view');
    }

    public function create(User $user): bool
    {
        return $user->can('employees.create');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->can('employees.edit');
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->can('employees.delete');
    }

    public function restore(User $user, Employee $employee): bool
    {
        return $user->can('employees.create');
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->hasRole('admin');
    }
    }

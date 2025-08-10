<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;

class DepartmentPolicy
{
    /**
     * Create a new policy instance.
     */
  public function viewAny(User $user): bool
    {
        return $user->can('departments.view');
    }

    public function view(User $user, Department $department): bool
    {
        return $user->can('departments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('departments.create');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->can('departments.edit');
    }

    public function delete(User $user, Department $department): bool
    {
        // Cannot delete department with employees
        if ($department->employees()->count() > 0) {
            return false;
        }
        
        return $user->can('departments.delete');
    }
}

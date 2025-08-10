<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleNames()->first(),
            'roles' => $this->getRoleNames(),
            'permissions' => $this->when(
                $this->relationLoaded('permissions'),
                $this->getAllPermissions()->pluck('name')
            ),
            'is_admin' => $this->hasRole('admin'),
            'is_manager' => $this->hasRole('manager'),
            'is_employee' => $this->hasRole('employee'),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

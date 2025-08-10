<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'phone' => $this->phone,
            'joining_date' => $this->joining_date->format('Y-m-d'),
            'department' => DepartmentResource::make($this->whenLoaded('department')),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}

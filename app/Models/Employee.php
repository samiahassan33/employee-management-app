<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Department;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'department_id',
        'joining_date',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getYearsOfServiceAttribute()
    {
        return $this->joining_date ? $this->joining_date->diffInYears(now()) : 0;
    }

    public function getFormattedJoiningDateAttribute()
    {
        return $this->joining_date ? $this->joining_date->format('M d, Y') : null;
    }

    // Scopes
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeJoinedAfter($query, $date)
    {
        return $query->where('joining_date', '>=', $date);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
                ->orWhere('email', 'LIKE', "%{$term}%")
                ->orWhere('phone', 'LIKE', "%{$term}%");
        });
    }
}

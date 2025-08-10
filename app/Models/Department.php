<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function activeEmployees()
    {
        return $this->hasMany(Employee::class)->whereNull('deleted_at');
    }

    // Accessors
    public function getEmployeesCountAttribute()
    {
        return $this->employees()->count();
    }

    public function getActiveEmployeesCountAttribute()
    {
        return $this->activeEmployees()->count();
    }

    // Scopes
    public function scopeWithEmployees($query)
    {
        return $query->with(['employees' => function ($q) {
            $q->orderBy('name');
        }]);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Mutators
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
}

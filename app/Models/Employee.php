<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = ['municipal_service_id', 'employee_number', 'first_name', 'last_name', 'position', 'email', 'phone', 'hire_date', 'active'];

    protected function casts(): array
    {
        return ['hire_date' => 'date', 'active' => 'boolean'];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(MunicipalService::class, 'municipal_service_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(AdministrativeRequest::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

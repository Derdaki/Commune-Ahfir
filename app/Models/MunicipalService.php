<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MunicipalService extends Model
{
    protected $fillable = ['name', 'code', 'description', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(AdministrativeRequest::class);
    }
}

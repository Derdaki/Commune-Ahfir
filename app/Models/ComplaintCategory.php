<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplaintCategory extends Model
{
    protected $fillable = ['name_fr', 'name_en', 'name_ar', 'color', 'icon', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_'.app()->getLocale()} ?? $this->name_fr;
    }
}

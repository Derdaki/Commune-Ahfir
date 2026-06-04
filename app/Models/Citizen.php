<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Citizen extends Model
{
    protected $fillable = ['user_id', 'cin', 'first_name', 'last_name', 'birth_date', 'email', 'phone', 'address'];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function requests(): HasMany
    {
        return $this->hasMany(AdministrativeRequest::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(CitizenNotification::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

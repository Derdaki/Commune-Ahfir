<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitizenNotification extends Model
{
    protected $fillable = ['citizen_id', 'administrative_request_id', 'complaint_id', 'title', 'message', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(AdministrativeRequest::class, 'administrative_request_id');
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}

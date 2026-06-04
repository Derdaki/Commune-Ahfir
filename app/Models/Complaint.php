<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    public const STATUSES = ['new', 'processing', 'resolved', 'rejected'];

    public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    protected $fillable = ['reference', 'citizen_id', 'complaint_category_id', 'municipal_service_id', 'employee_id', 'subject', 'description', 'location', 'priority', 'status', 'channel', 'resolution', 'resolved_at'];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'complaint_category_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(MunicipalService::class, 'municipal_service_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ComplaintHistory::class)->latest();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(CitizenNotification::class);
    }
}

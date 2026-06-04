<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdministrativeRequest extends Model
{
    public const STATUSES = [
        'pending' => 'pending',
        'processing' => 'processing',
        'approved' => 'approved',
        'rejected' => 'rejected',
    ];

    public static function statusLabels(): array
    {
        return collect(array_keys(self::STATUSES))
            ->mapWithKeys(fn (string $status) => [$status => __("app.status.{$status}")])
            ->all();
    }

    protected $fillable = ['reference', 'citizen_id', 'municipal_service_id', 'employee_id', 'type', 'description', 'status', 'admin_notes', 'submitted_at', 'processed_at'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'processed_at' => 'datetime'];
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(MunicipalService::class, 'municipal_service_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(CitizenNotification::class);
    }
}

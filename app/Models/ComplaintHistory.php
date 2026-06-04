<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintHistory extends Model
{
    protected $fillable = ['complaint_id', 'user_id', 'action', 'old_status', 'new_status', 'comment', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

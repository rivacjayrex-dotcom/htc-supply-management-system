<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requisition_id',
        'action',
        'role',
        'remarks',
    ];

    /**
     * Get the requisition associated with this log.
     */
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(Requisition::class);
    }
}

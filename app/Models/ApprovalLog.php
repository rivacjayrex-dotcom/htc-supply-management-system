<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApprovalLog extends Model
{
    protected $fillable = [
        'requisition_id',
        'action',
        'role',
        'remarks',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(Requisition::class);
    }

    /**
     * The cryptographic digital signature generated for this action.
     */
    public function digitalSignature(): HasOne
    {
        return $this->hasOne(DigitalSignature::class);
    }
}

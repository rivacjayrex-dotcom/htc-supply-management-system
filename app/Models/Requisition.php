<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Requisition extends Model
{
    protected $fillable = [
        'user_id',
        'request_type',
        'status',
        'remarks',
        'grand_total'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RequisitionItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class);
    }

    /**
     * Check if the requisition is nearing the 3-day SLA (2 days elapsed since final approval)
     */
    public function isNearingDeadline(): bool
    {
        // Eligible when approved by the highest required authority but not yet released
        $isFullyApproved = ($this->status === 'approved_president') ||
                           ($this->request_type === 'minor' && $this->status === 'approved_vp');

        if ($isFullyApproved) {
            // Checked against updated_at (when the last approval occurred)
            return $this->updated_at->diffInDays(Carbon::now()) >= 2;
        }

        return false;
    }

    /**
     * Check if the requisition has exceeded the 3-day processing deadline
     */
    public function isOverdue(): bool
    {
        $isFullyApproved = ($this->status === 'approved_president') ||
                           ($this->request_type === 'minor' && $this->status === 'approved_vp');

        if ($isFullyApproved) {
            return $this->updated_at->diffInDays(Carbon::now()) >= 3;
        }

        return false;
    }

    /**
     * Institutional Control Tracking Number
     * e.g. MIN-CETE-20000001 or MAJ-CBMA-10000001
     */
    public function getTrackingCode(): string
    {
        $prefix = strtoupper($this->request_type) === 'MAJOR' ? 'MAJ' : 'MIN';
        $tierCode = strtoupper($this->request_type) === 'MAJOR' ? '10' : '20';
        $dept = strtoupper($this->user->department ?? 'HTC');
        $uniqueNumber = $tierCode . str_pad($this->id, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$dept}-{$uniqueNumber}";
    }

    // Optional: Attribute accessor so you can also call $req->tracking_code
    public function getTrackingCodeAttribute(): string
    {
        return $this->getTrackingCode();
    }
}

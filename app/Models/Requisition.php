<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Requisition extends Model
{
    protected $fillable = [
        'tracking_number', // e.g. MIN-CETE-20000001
        'user_id',
        'request_type',
        'status',
        'purpose',
        'remarks',
        'grand_total'
    ];

    /**
     * Boot function to automatically generate the institutional tracking code upon creation
     */
    protected static function booted()
    {
        static::created(function ($requisition) {
            if (empty($requisition->tracking_number)) {
                $prefix = strtoupper($requisition->request_type) === 'MAJOR' ? 'MAJ' : 'MIN';
                $tierCode = strtoupper($requisition->request_type) === 'MAJOR' ? '10' : '20';
                $dept = strtoupper($requisition->user->department ?? 'HTC');
                $uniqueNumber = $tierCode . str_pad($requisition->id, 6, '0', STR_PAD_LEFT);

                $requisition->tracking_number = "{$prefix}-{$dept}-{$uniqueNumber}";
                $requisition->saveQuietly();
            }
        });
    }

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
     * Accessor for tracking_code compatibility across Blade views
     */
    public function getTrackingCodeAttribute(): string
    {
        return $this->tracking_number ?? $this->getTrackingCodeFallback();
    }

    public function getTrackingCodeFallback(): string
    {
        $prefix = strtoupper($this->request_type) === 'MAJOR' ? 'MAJ' : 'MIN';
        $tierCode = strtoupper($this->request_type) === 'MAJOR' ? '10' : '20';
        $dept = strtoupper($this->user->department ?? 'HTC');
        $uniqueNumber = $tierCode . str_pad($this->id, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$dept}-{$uniqueNumber}";
    }

    /**
     * Check if nearing the 3-day SLA (2 days elapsed since final approval)
     */
    public function isNearingDeadline(): bool
    {
        $isFullyApproved = ($this->status === 'approved_president') ||
                           ($this->request_type === 'minor' && $this->status === 'approved_vp');

        if ($isFullyApproved) {
            return $this->updated_at->diffInDays(Carbon::now()) >= 2;
        }

        return false;
    }

    /**
     * Check if exceeded the 3-day processing deadline
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
}

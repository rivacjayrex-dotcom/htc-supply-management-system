<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SupplyRequest extends Model
{
    protected $fillable = [
        'user_id',
        'item_name',
        'specifications',
        'quantity',
        'unit',
        'unit_price',
        'total_amount',
        'request_type',
        'status',
        'remarks'
    ];

    /**
     * Get the user that owns the supply request.
     * This connects the request back to the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isNearingDeadline()
    {
        // If it's approved but not yet released
        if ($this->status == 'approved_president' || ($this->request_type == 'minor' && $this->status == 'approved_vp')) {
            // Check if it has been sitting for more than 2 days (48 hours)
            return $this->updated_at->diffInDays(Carbon::now()) >= 2;
        }
        return false;
    }
}

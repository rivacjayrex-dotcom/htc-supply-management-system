<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $fillable = ['user_id', 'request_type', 'status', 'remarks', 'grand_total'];

    public function items() {
        return $this->hasMany(RequisitionItem::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function logs() {
    return $this->hasMany(ApprovalLog::class);
}
}



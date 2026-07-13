<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $fillable = [
        'requisition_id', 'item_name', 'specifications',
        'quantity', 'unit', 'unit_price', 'subtotal'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $fillable = [
        'item_name',
        'specifications',
        'quantity',
        'unit',
        'unit_price'
    ];
}

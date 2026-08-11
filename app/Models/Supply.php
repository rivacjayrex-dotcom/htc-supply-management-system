<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    // Add all these new fields to the allowed list
    protected $fillable = [
        'item_name',
        'brand',
        'model_number',
        'category',
        'physical_description',
        'quantity',
        'unit',
        'unit_price',
        'min_stock_level',
    ];
}

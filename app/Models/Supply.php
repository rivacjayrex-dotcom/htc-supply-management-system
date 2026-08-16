<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $fillable = [
        'item_name', 'brand', 'model_number', 'category',
        'specifications', 'quantity', 'unit', 'unit_price', 'min_stock_level'
    ];
}

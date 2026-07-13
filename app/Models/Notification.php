<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // Add this block to allow data to be saved into these columns
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'icon',
        'type',
        'is_read'
    ];
}

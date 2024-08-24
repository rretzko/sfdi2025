<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoicePart extends Model
{
    protected $fillable = [
        'descr',
        'abbr',
        'order_by',
    ];
}

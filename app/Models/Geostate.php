<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Geostate extends Model
{

    protected $fillable = [
        'country_abbr',
        'name',
        'abbr',
    ];
}

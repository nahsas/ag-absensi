<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Setting extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        "value",
        "range_start",
        "range_end",
    ];

    protected $casts = [
        "id"=>"string"
    ];
}

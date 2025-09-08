<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Setting extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        "value"
    ];

    protected $casts = [
        "id"=>"string"
    ];
}

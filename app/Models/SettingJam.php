<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SettingJam extends Model
{
    use HasUuids;
    protected $fillable = [
        "nama_jam",
        "jam",
        "batas_jam"
    ];
}

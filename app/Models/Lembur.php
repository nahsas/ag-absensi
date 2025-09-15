<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserHasLembur;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lembur extends Model
{
    use HasUuids;

    protected $fillable = [
        "code",
        "start_date",
        "end_date"
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, UserHasLembur::class, 'lembur_id', 'user_id');
    }
}

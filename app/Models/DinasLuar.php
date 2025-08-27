<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DinasLuar extends Model
{
    use HasUuids;
    public $fillable = [
        'judul',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, UserDinasLuar::class, 'dinas_luar_id', 'user_id');
    }
}

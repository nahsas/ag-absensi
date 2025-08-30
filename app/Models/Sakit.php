<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Sakit extends Model
{
    protected $fillable = [
        'user_id',
        'absen_id',
        'bukti_sakit',
        'tanggal',
        'approved',
        'alasan',
    ];

    public function absen()
    { 
        return $this->belongsTo(Absen::class, "absen_id","id");
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public $casts = [
        "id"=>"string"
    ];
}

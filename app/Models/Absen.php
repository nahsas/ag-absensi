<?php

namespace App\Models;

use App\Models\Izin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Absen extends Model
{
    use HasUuids;
    protected $fillable = [
        'user_id',
        'keterangan',
        'bukti',
        'point',
        'tanggal_absen',
        'show',
    ];
    protected $casts = [
        'id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function izin()
    {
        return $this->hasOne(Izin::class,'absen_id','id');
    }
    public function sakit()
    {
        return $this->hasOne(Sakit::class,'absen_id','id');
    }
}

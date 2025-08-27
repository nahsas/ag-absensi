<?php

namespace App\Models;

use App\Models\SettingJam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolesSetting extends Model
{
    use HasUuids;

    protected $table = 'roles_setting';

    protected $fillable = [
        'name',
        'roles_id',
        'jam_id',
        'operator',
        'value',
        'point'
    ];

    protected $casts = [
        'id' => 'string', // atau tipe data lain yang sesuai
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'roles_id');
    }

    public function jam(): BelongsTo
    {
        return $this->belongsTo(SettingJam::class, 'jam_id');
    }
}

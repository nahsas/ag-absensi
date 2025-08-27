<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Roles extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function roleSettings(): HasMany
    {
        return $this->hasMany(RolesSetting::class);
    }
}

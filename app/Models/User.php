<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip',
        'nik',
        'name',
        'alamat',
        'password',
        'no_hp',
        'isFirstLogin',
        'roles_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'string', // atau tipe data lain yang sesuai
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(){
        return $this->hasOne(Roles::class, 'id', 'roles_id');
    }

    public function absen(){
        return $this->hasMany(Absen::class, 'user_id', 'id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role->name === 'superadmin';
    }

    public function canAccessPanel(Panel $panel):bool{
        return Filament::auth()->user()->role->name == 'admin' || Filament::auth()->user()->role->name == 'superadmin' ? true : false;
    }

}

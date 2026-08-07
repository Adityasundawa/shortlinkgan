<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_team',
        'name',
        'username',
        'user_label',
        'email',
        'password',
        'role',
    ];

    public static $rules = [
        'username' => 'required|unique:users',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    // public function getRedirectRoute() {
    //     return $this->hasRole('admin') ? route('admin.dashboard') : route('user.dashboard');
    // }

    public function team()
    {
        return $this->hasOne(Team::class, 'id', 'id_team');
    }

    public function isAdmin()
    {
        return $this->role === 'admin'; // Menggantikan 'role' dengan kolom yang sesuai untuk menentukan apakah pengguna adalah admin
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}

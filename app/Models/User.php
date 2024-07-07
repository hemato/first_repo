<?php

namespace App\Models;

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
        'name',
        'email',
        'password',
        'profile_image',
        'profile_description',
    ];

    public function adminlte_image()
    {
        return $this->profile_image ? asset('storage/' . $this->profile_image) : 'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        return $this->profile_description ? $this->profile_description : 'I\'m a bad guy';
    }

    public function adminlte_profile_url()
    {
//        return 'profile/' . $this->id;
        return 'profile/edit';
    }

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
}

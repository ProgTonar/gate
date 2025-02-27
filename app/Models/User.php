<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

    public function findForPassport($username)
    {
        return $this->where('login', strtolower($username))
            ->orWhere('email', strtolower($username))
            ->first()
        ;
    }

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'email',
        'login',
        'password',
        'user_type_id',
        'photo',
        'phone',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}

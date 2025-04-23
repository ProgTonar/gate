<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserType;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable, Authorizable;

    public function findForPassport($username)
    {

        $user = $this->where('login', strtolower($username))
            ->orWhere('email', strtolower($username))
            ->first();

        return $user;
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


    public function userType()
{
    return $this->belongsTo(UserType::class, 'user_type_id');
}
}

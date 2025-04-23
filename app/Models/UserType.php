<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $fillable = ['name', 'rus_name', 'short_rus_name'];



      public function users()
    {
        return $this->hasMany(User::class, 'user_type_id');
    }
}

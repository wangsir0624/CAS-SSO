<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    public function oauthUsers() {
        return $this->hasMany(OauthUser::class, 'user_id', 'id');
    }
}

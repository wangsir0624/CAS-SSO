<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OauthUser extends Model
{
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

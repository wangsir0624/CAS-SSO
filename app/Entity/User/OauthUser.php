<?php
namespace App\Entity\User;

abstract class OauthUser extends User {
    public $oauth_type = '';

    public $oauth_id = 0;

    public $identifier = '';
}
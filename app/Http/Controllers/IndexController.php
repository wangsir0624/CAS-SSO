<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __construct() {
        $this->middleware('sso_auth:0');
    }

    public function getIndex() {
        echo '您已登录';
    }
}

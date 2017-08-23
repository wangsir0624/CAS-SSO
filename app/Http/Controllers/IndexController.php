<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redis;
use App\Entity\User\User;

class IndexController extends Controller
{
    public function __construct() {
        $this->middleware('sso_auth:0');
    }

    public function getIndex(Request $request) {
        //获取登陆的用户信息
        list($userId, $type) = explode(':', Redis::get('TGT:' . $request->cookie('TGC-SSO')));
        $userEntity = User::find($userId, 'id', $type);

        return view('index', ['userEntity' => $userEntity]);
    }
}

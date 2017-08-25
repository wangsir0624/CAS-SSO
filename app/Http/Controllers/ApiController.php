<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redis;
use App\Entity\User\User;
use App\Model\User as UserModel;

class ApiController extends Controller
{
    public function getUserInfo(Request $request) {
        //服务器参数验证
        try {
            $this->validate($request, [
                'appKey' => 'required',
                'appSecret' => 'required',
                'code' => 'required'
            ]);
        } catch(\Exception $e) {
            return response()->json(['errcode' => 1, 'errmsg' => '参数错误']);
        }

        //检查app key和app secret是否正确
        $application = Application::where('app_key', $request->appKey)->available()->first();
        if(empty($application) || $application->app_secret != $request->appSecret) {
            return response()->json(['errcode' => 2, 'errmsg' => 'app_key或app_secret不正确']);
        }

        //获取用户信息
        $st = base64_decode($request->code);
        $tgc = Redis::get($st);
        if(empty($tgc)) {
            return response()->json(['errcode' => 3, 'errmsg' => 'code不正确或已过期']);
        }

        $tgt = Redis::get('TGT:' . $tgc);
        if(empty($tgt)) {
            return response()->json(['errcode' => 3, 'errmsg' => 'code不正确或已过期']);
        }

        list($userId, $type) = explode(':', Redis::get('TGT:' . $tgc));

        //销毁code
        Redis::del($st);

        $userEntity = User::find($userId, 'id', $type);
        if(empty($userEntity)) {
            return response()->json(['errcode' => 4, 'errmsg' => '用户不存在']);
        }

        return response()->json(array_merge($userEntity->toArray(), ['errcode' => 0, 'errmsg' => '']));
    }

    public function getUsers(Request $request) {
        //服务器参数验证
        try {
            $this->validate($request, [
                'appKey' => 'required',
                'appSecret' => 'required',
            ]);
        } catch(\Exception $e) {
            return response()->json(['errcode' => 1, 'errmsg' => '参数错误']);
        }

        //检查app key和app secret是否正确
        $application = Application::where('app_key', $request->appKey)->available()->first();
        if(empty($application) || $application->app_secret != $request->appSecret) {
            return response()->json(['errcode' => 2, 'errmsg' => 'app_key或app_secret不正确']);
        }

        $offset = intval($request->input('offset', 0));
        $size = min(intval($request->input('size', 15)), 100);
        $order = $request->input('order', 'created_time_asc');

        //获取用户
        $oldUsers = UserModel::orderBy('created_time', ($order == 'created_time_desc') ? 'desc' : 'asc')->skip($offset)->take($size + 1)->select('id', 'mobile', 'email', 'password', 'status', 'realname', 'gender', 'birthday', 'avatar')->get();
        $users = $oldUsers->slice(0, $size);

        //添加第三方信息
        foreach($users as $key =>  $user) {
            $oauthUsers = $user->oauthUsers()->select('oauth_type', 'identifier')->get();

            foreach($oauthUsers as $oauthUser) {
                switch($oauthUser['oauth_type']) {
                    case 'dingding':
                        $users[$key]->dingding_unionid = $oauthUser['identifier'];
                }
            }
        }

        $data = [
            'errcode' => 0,
            'errmsg' => '',
            'hasmore' => !($users->count() == $oldUsers->count()),
            'userlist' => $users->toArray()
        ];
        return response()->json($data);
    }
}

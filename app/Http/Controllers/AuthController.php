<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Redis;
use Wangjian\Dingding\DingdingClient;
use App\Services\Util;
use App\Entity\User\DingdingUser;
use Illuminate\Support\Str;
use App\Model\Application;
use DB;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('sso_auth:0')->only(['getLogout']);
        $this->middleware('sso_auth:1')->only(['getDingdingLogin']);
    }

    public function getDingdingLogin(Request $request, DingdingClient $client) {
        //登陆模式，1为扫码登陆，2为表单登陆，3为自定义扫码登陆
        $loginMode = $request->input('type', DingdingClient::LOGIN_QRCODE);

        //如果是自定义登陆模式，显示登陆页面
        if($loginMode == DingdingClient::LOGIN_CUSTOM_QRCODE) {
            if(empty($_GET['code'])) {
                return view('auth.dingding_custom_qrcode_login', [
                    'goto' => "https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid=" . app('config')['dingding.appId'] . "&response_type=code&scope=snsapi_login&state=" . strtoupper(\Illuminate\Support\Str::random(6)) . "&redirect_uri=" . urlencode(\Illuminate\Support\Facades\Request::fullurl())
                ]);
            }
        }

        //钉钉登陆
        try {
            $userInfo = $client->getOauthUser(Util::getDingdingSnsAccessToken(), $loginMode);
            if($userInfo['errcode'] != 0) {
                throw new \RuntimeException($userInfo['errmsg'], 'errcode');
            }
        } catch(\Exception $e) {
            //存储登陆记录日志
            $this->writeLoginLogs([
               'user_id' => 0,
                'login_type' => 'dingding',
                'login_status' => 0,
                'login_text' => $e->getMessage(),
                'login_time' => time(),
                'login_ip' => $request->getClientIp()
            ]);

            return redirect(Util::addParametersToUrl($request->fullUrl(), ['style' => 4, 'code' => '']))->with('error', $e->getMessage());
        }

        //获取用户详细信息
        $unionid = $userInfo['user_info']['unionid'];
        $userInfo = $client->getUserDetailByUnionid(Util::getDingdingAccessToken(), $unionid);

        //获取全局用户ID
        $globalUserId = DingdingUser::getGlobalUserId($userInfo);

        //设置TGC的值
        $tgc = uniqid('TGC', true);

        //设置TGT
        Redis::set('TGT:' . $tgc, $globalUserId . ':' . 'dingding');
        Redis::expire('TGT:' . $tgc, app('config')['sso.tgc_lifetime'] * 60);

        //存储登陆日志
        $this->writeLoginLogs([
            'user_id' => $globalUserId,
            'login_type' => 'dingding',
            'login_status' => 1,
            'login_text' => '登陆成功',
            'login_time' => time(),
            'login_ip' => $request->getClientIp()
        ]);

        //跳转
        if($request->has('service') && Str::startsWith($request->service, Application::getAllUrlPrefixs())) {
            //设置ST
            $stKey = uniqid('ST', true);
            $st = base64_encode($stKey);
            Redis::set($stKey, $tgc);
            Redis::expire($stKey, app('config')['sso.st_lifetime'] * 60);

            $response = redirect(Util::addParametersToUrl($request->service, ['code' => $st]));
        } else {
            $response = redirect('/');
        }

        return $response->cookie('TGC-SSO', $tgc, app('config')['sso.tgc_lifetime']);
    }

    public function getLogout(Request $request) {
        //删除TGT
        Redis::del('TGT:' . $request->cookie('TGC-SSO'));

        //子应用注销地址
        $logoutUrls = Application::getAllLogoutUrls();

        //注销后跳转地址
        $destination = '';
        if($request->has('service') && Str::startsWith($request->service, Application::getAllUrlPrefixs())) {
            $destination = $request->service;
        } else {
            $destination = url('dingding/login?type=4');
        }

        //过多少秒跳转
        $delay = 1;

        return response()->view('auth.logout', compact(['logoutUrls', 'destination', 'delay']))->cookie('TGC-SSO', '', -1);
    }

    protected function writeLoginLogs($data) {
        DB::table('login_logs')->insert($data);

        //如果登陆成功，更新用户的最近登陆时间和最近登陆IP
        if($data['login_status'] > 0) {
            DB::table('users')->where('id', $data['user_id'])->update([
                'last_login_time' => time(),
                'last_login_type' => $data['login_type'],
                'last_login_ip' => $data['login_ip']
            ]);
        }
    }
}

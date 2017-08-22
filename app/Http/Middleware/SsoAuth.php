<?php

namespace App\Http\Middleware;

use Closure;
use Redis;
use App\Services\Util;
use Illuminate\Support\Str;
use App\Model\Application;

class SsoAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type = 0)
    {
        //是否已在用户中心登陆
        $logged = $request->cookies->has('TGC-SSO') && Redis::exists('TGT:' . $request->cookie('TGC-SSO'));

        switch($type) {
            case 0:
                //检查是否存在全局会话
                if(!$logged) {
                    if($request->has('service') && Str::startsWith($request->service, Application::getAllUrlPrefixs())) {
                        $response = redirect('dingding/login?type=4&service=' . urlencode($request->service));
                    } else {
                        $response = redirect('dingding/login?type=4');
                    }

                    return $response;
                } else {
                    return $next($request);
                }
                break;
            case 1:
                if($logged) {
                    $response = null;
                    if($request->has('service') && Str::startsWith($request->service, Application::getAllUrlPrefixs())) {
                        //设置ST
                        $stKey = uniqid('ST', true);
                        $st = base64_encode($stKey);
                        Redis::set($stKey, $request->cookie('TGC-SSO'));
                        Redis::expire($stKey, app('config')['sso.st_lifetime'] * 60);

                        $response = redirect(Util::addParametersToUrl($request->service, ['code' => $st]));
                    } else {
                        $response = redirect('/');
                    }

                    return $response;
                } else {
                    return $next($request);
                }
                break;
        }
    }
}

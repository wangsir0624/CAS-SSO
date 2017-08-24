<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class TgcKeepalive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        //如果用户已经登陆，给TGC和TGT续期
        if($request->cookies->has('TGC-SSO') && Redis::exists('TGT:' . $request->cookie('TGC-SSO'))) {
            $response->cookie('TGC-SSO', $request->cookie('TGC-SSO'), app('config')['sso.tgc_lifetime']);
            Redis::expire('TGT:' . $request->cookie('TGC-SSO'), app('config')['sso.tgc_lifetime'] * 60);
        }

        return $response;
    }
}

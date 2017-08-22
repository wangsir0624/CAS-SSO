<?php
namespace App\Services;

use Redis;
use Illuminate\Support\Str;

class Util {
    public static function addParametersToUrl($url, $parameters) {
        foreach ($parameters as $key => $value) {
            $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
            $url = substr($url, 0, -1);
            if (strpos($url, '?') === false) {
                return ($url . '?' . $key . '=' . $value);
            } else {
                return ($url . '&' . $key . '=' . $value);
            }
        }

        return $url;
    }

    public static function getDingdingAccessToken() {
        if(!Redis::exists('dingding:access_token')) {
            $accessToken = app(\Wangjian\Dingding\DingdingClient::class)->getAccessToken();
            $accessToken = $accessToken['access_token'];

            Redis::set('dingding:access_token', $accessToken);
            Redis::expire('dingding:access_token', 7000);
        }

        return Redis::get('dingding:access_token');
    }

    public static function getDingdingSnsAccessToken() {
        if(!Redis::exists('dingding:sns_access_token')) {
            $accessToken = app(\Wangjian\Dingding\DingdingClient::class)->getSnsAccessToken();
            $accessToken = $accessToken['access_token'];

            Redis::set('dingding:sns_access_token', $accessToken);
            Redis::expire('dingding:sns_access_token', 7000);
        }

        return Redis::get('dingding:sns_access_token');
    }
}
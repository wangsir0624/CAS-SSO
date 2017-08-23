<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public $timestamps = false;

    public function scopeAvailable($query) {
        return $query->where('status', 1);
    }

    /**
     * 获取所有合法Services地址
     * @return array
     */
    public static function getAllUrlPrefixs() {
        $urlPrefix = self::available()->pluck('url_prefix');
        $urlPrefixArray = [];

        foreach($urlPrefix->all() as $urlString) {
            $urls = explode(',', $urlString);
            array_walk($urls, function($item) use (&$urlPrefixArray) {
                $urlPrefixArray[] = $item;
            });
        }

        return $urlPrefixArray;
    }

    /**
     * 获取所有子应用的注销地址
     * @return array
     */
    public static function getAllLogoutUrls() {
        $logoutUrls = self::available()->pluck('logout_url');
        $logoutUrlArray = [];

        foreach($logoutUrls->all() as $urlString) {
            $urls = explode(',', $urlString);
            array_walk($urls, function($item) use (&$logoutUrlArray) {
               $logoutUrlArray[] = $item;
            });
        }

        return $logoutUrlArray;
    }
}

<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public $timestamps = false;

    /**
     * 获取所有合法Services地址
     * @return array
     */
    public static function getAllUrlPrefixs() {
        $urlPrefix = Application::pluck('url_prefix');
        $urlPrefixArray = [];
        foreach($urlPrefix->all() as $urlString) {
            $urls = explode(',', $urlString);
            array_walk($urls, function($item) use (&$urlPrefixArray) {
                $urlPrefixArray[] = $item;
            });
        }

        return $urlPrefixArray;
    }
}

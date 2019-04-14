<?php
/**
 * Created by PhpStorm.
 * User: elati
 * Date: 2019/4/14
 * Time: 21:01
 */

namespace App\Tools;

use Illuminate\Support\Facades\Redis;

class RedisTools
{
    public static function hasKey(string $key) {
        return Redis::exists($key) == 1;
    }

    public static function setKeyWillExpired(string $key, string $value, int $expiredAt) {
        Redis::setex($key, $expiredAt, $value);
    }

    public static function setKeyEternal(string $key, string $value) {
        Redis::set($key, $value);
    }

    public static function getValueByKey(string $key) {
       return Redis::get($key);
    }
}
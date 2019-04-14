<?php
/**
 * Created by PhpStorm.
 * User: elati
 * Date: 2019/4/14
 * Time: 20:20
 */

namespace App\Tools;

//use App\Tools\Encrypt;
//use App\Tools\RedisTools;
use Carbon\Carbon;
use App\Token;


class TokenTools
{
    private static function prefix() {
        return env('REDIS_PREFIX', 'CourseSystem_') . 'token_';
    }

    /**
     * 一般用这个方法
     * @return string
     */
    public static function createAndSet(int $usersId, int $expiredAt) {
        $token = self::create();
        self::setToken($token, $usersId, $expiredAt);
        return $token;
    }

    public static function getTokenUserId(string $token) {
        if(env('TOKEN_USE_REDIS')) {
            return self::getTokenUserIdFromCache($token);
        }

        return self::getTokenUserIdFromDatabase($token);
    }

    public static function setToken(string $token, int $usersId, int $expiredAt) {
        if(env('TOKEN_USE_REDIS')) {
            self::setTokenToCache($token, $usersId, $expiredAt);
        } else {
            self::setTokenToDatabase($token, $usersId, $expiredAt);
        }
    }

    public static function create() {
        $chars = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM!@#$%^&*()';
        $strlen = strlen($chars);

        do {

            $randomStr = '';
            for($i = 0; $i < 6; ++$i) {
                $randomStr .= $chars[rand(0, $strlen - 1)];
            }
            $token = self::prefix() . Encrypt::encrypt($randomStr . strval(Carbon::now()->timestamp));

        } while(self::getTokenUserId($token) != null);

        return $token;
    }

    public static function getTokenUserIdFromCache(string $token) {
        return RedisTools::getValueByKey($token);
    }

    public static function getTokenUserIdFromDatabase(string $token) {
        $tokenTable = new Token();
        $tokenRow = $tokenTable->where('token', $token)->first();
        if($tokenRow == null) {
            return null;
        }
        return $tokenRow->users_id;
    }

    public static function setTokenToCache(string $token, int $usersId, int $expiredAt) {
        RedisTools::setKeyWillExpired($token, $usersId, $expiredAt);
    }

    public static function setTokenToDatabase(string $token, int $usersId, int $expiredAt) {
        $tokenTable = new Token();
        $tokenTable->insert([
            'users_id'      => $usersId,
            'token'         => $token,
            'expired_at'    => $expiredAt
        ]);
    }
}
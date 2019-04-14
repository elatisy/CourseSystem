<?php
/**
 * Created by PhpStorm.
 * User: elati
 * Date: 2019/4/14
 * Time: 20:23
 */

namespace App\Tools;

use Illuminate\Support\Facades\Hash;

class Encrypt
{
    public static function encrypt(string $str) {
        return Hash::make($str . config('encrypt.salt'));
    }

    public static function check(string $hash, string $origin) {
        return Hash::check($origin . config('encrypt.salt'), $hash);
    }
}
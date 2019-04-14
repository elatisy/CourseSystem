<?php
/**
 * Created by PhpStorm.
 * User: elati
 * Date: 2019/4/14
 * Time: 21:47
 */
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'    => 'user'
], function (){
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
});
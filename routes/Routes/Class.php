<?php
/**
 * Created by PhpStorm.
 * User: elatis
 * Date: 2019/4/22
 * Time: 10:16
 */

use Illuminate\Support\Facades\Route;


Route::group([
    'prefix'    => 'class'
], function (){
    Route::get('all', 'ClassController@getAllClasses');

    Route::group([
        'middleware'    => 'token'
    ], function (){
        Route::get('getbyusersid', 'ClassController@getByUsersId');
    });
});
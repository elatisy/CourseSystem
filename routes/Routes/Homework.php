<?php
/**
 * Created by PhpStorm.
 * User: elatis
 * Date: 2019/4/20
 * Time: 15:59
 */

use Illuminate\Support\Facades\Route;


Route::group([
    'prefix'        => 'homework'
], function (){

    Route::get('getbyclassname/{name}', 'HomeworkController@getHomeworkByClassName');

    Route::group([
        'middleware'    => 'token'
    ], function() {
       Route::post('create', 'HomeworkController@createHomework');
       Route::post('update', 'HomeworkController@updateHomework');
    });
});
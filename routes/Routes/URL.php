<?php
/**
 * Created by PhpStorm.
 * User: elatis
 * Date: 2019/4/20
 * Time: 13:48
 */

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'        => 'ppt',
    'middleware'    => 'token'
],function (){
    Route::get('getall', 'PPTController@getAllPPTs');
    Route::get('getclasses', 'PPTController@getClasses');
    Route::get('getbyclassname/{name}', 'PPTController@getPPTsByClassName');
    Route::post('delete', 'PPTController@deletePPT');
    Route::post('upload', 'PPTController@uploadPPT');
});

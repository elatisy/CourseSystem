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

    Route::get('getbyclassid/{id}', 'HomeworkController@getHomeworkByClassId');

    Route::group([
        'middleware'    => 'token'
    ], function() {
       Route::post('create', 'HomeworkController@createHomework');
       Route::post('update', 'HomeworkController@updateHomework');
    });

    Route::group([
        'prefix'    => 'data'
    ], function() {
        Route::get('getrecordbyclassesid/{classes_id}', 'HomeworkController@getHomeworkRecordsByClassesId');

        Route::group([
            'middleware'    => 'token'
        ], function() {
            Route::get('getrecordbyusersid', 'HomeworkController@getAllSelfHomeworkRecords');
            Route::post('getbyclassesidandusersid', 'HomeworkController@getHomeworkDataByClassesIdAndUsersId');
            Route::post('handin', 'HomeworkController@handInHomework');
        });
    });
});
<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('prefix' => 'api'), function()
{
    Route::get('measurement', 'MeasurementController@get');
    Route::get('score', 'ScoreController@get');
    Route::get('user', 'UserController@get');
});

Route::get('/', function()
{
    return View::make('viz');
});

Route::get('user/authorize', 'UserController@authorize');
//Route::get('user/savetoken', 'UserController@savetoken');
//function(){return View::make('hello');});
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
    //group(array('prefix' => 'api'), function()
    Route::get('measurement', 'MeasurementController@get');
    Route::post('measurement', 'MeasurementController@store');
    Route::get('eventtype', 'EventtypeController@get');
    Route::post('eventtype', 'EventtypeController@store');
    Route::get('source', 'SourceController@get');
    Route::get('user', 'UserController@get');
    Route::post('user', 'UserController@store');
    Route::get('user/image', 'UserController@getimage');
});

Route::get('/', function()
{
    return View::make('viz');
});


Route::get('hello', function()
{
    return View::make('hello');
});

Route::get('hello2', function()
{
    return View::make('hello2');
});
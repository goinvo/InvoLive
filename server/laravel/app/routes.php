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
    Route::resource('eventtype', 'EventtypeController');
    Route::resource('user', 'UserController');
});

Route::get('test', function()
{
	$measurement = Measurement::all();
	var_dump($measurement);
});

Route::get('group', function()
{
	$measurements = Measurement::select(DB::raw('type,user,MAX(value) as value,timestamp'))->groupBy(DB::raw('type, user'))->get();
	
	foreach ($measurements as $measurement){
		echo $measurement->type.' '.$measurement->user.' '.$measurement->value.' '.$measurement->timestamp;
		echo '<br>';
	}


	// $measurements->each(function($measurement) {
	// 	echo $measurement->type.' '.$measurement->user.' '.$measurement->value.' '.$measurement->timestamp;
	// 	echo '<br>';
	// });
});

Route::get('/', function()
{
	$measurements = Measurement::all();
	// $paginator = Paginator::make($measurements->toArray(), 15, 15);
	// echo $paginator;
	$measurements->each(function($measurement) {
		echo $measurement->type.' '.$measurement->user.' '.$measurement->value.' '.$measurement->timestamp;
		echo '<br>';
	});
});


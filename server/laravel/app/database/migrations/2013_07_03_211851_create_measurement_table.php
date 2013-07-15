<?php

use Illuminate\Database\Migrations\Migration;

class CreateMeasurementTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('measurements', function($table)
		{
			$table->increments('id');
			$table->integer('eventtype_id');
			$table->integer('source_id');
			$table->integer('user_id');
			$table->float('value');
			$table->dateTime('timestamp');

		});

		Schema::create('eventtypes', function($table)
		{
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('aggregation');
		});



		Schema::create('sources', function($table)
		{
			$table->increments('id');
			$table->string('name')->unique();
		});

		Schema::create('users', function($table)
		{
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('dropboxId')->unique();
			$table->string('email')->unique();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('measurements');
		Schema::drop('eventtypes');
		Schema::drop('sources');
		Schema::drop('users');
	}

}
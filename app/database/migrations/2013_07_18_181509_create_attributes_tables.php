<?php

use Illuminate\Database\Migrations\Migration;

class CreateAttributesTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attributes', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});

		Schema::create('eventtypes_attributes', function($table)
		{
			$table->increments('id');
			$table->integer('eventtype_id');
			$table->integer('attribute_id');
		});

		Schema::create('measurement_attributes', function($table)
		{
			$table->increments('id');
			$table->integer('measurement_id');
			$table->integer('attribute_id');
			$table->string('value');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attributes');
		Schema::drop('eventtypes_attributes');
		Schema::drop('measurement_attributes');
	}

}
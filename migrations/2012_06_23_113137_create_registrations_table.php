<?php

class Passkitserver_Create_Registrations_Table
{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('registrations', function($table)
		{
		    $table->increments('id');
		    $table->string('device_id', 100);
		    $table->string('pass_type', 100);
		    $table->string('serial_number', 100);
		    $table->timestamps();
		    $table->index('device_id');
		    $table->index('serial_number');
		    $table->foreign('device_id')->references('device_id')->on('devices')->on_delete('cascade')->on_update('cascade');
		    $table->foreign('serial_number')->references('serial_number')->on('passes')->on_delete('cascade')->on_update('cascade');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('registrations');
	}

}
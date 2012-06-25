<?php

class Passkitserver_Create_Devices_Table
{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('devices', function($table)
		{
		    $table->increments('id');
		    $table->string('device_id', 100);
		    $table->string('push_token', 100);
		    $table->timestamps();
		    $table->unique('device_id');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('devices');
	}

}
<?php

class Passkitserver_Create_Passes_Table
{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('passes', function($table)
		{
			$table->increments('id');
		    $table->string('serial_number', 100);
		    $table->string('auth_token', 100);
		    
		    /* add you custom pass fields here */
		    
		    $table->timestamps();
		    $table->unique('serial_number');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('passes');
	}

}
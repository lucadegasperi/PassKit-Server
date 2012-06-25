<?php

namespace PassKitServer\Models;

use Eloquent;

class Pass extends Eloquent
{
	public static $table = 'passes';
	public static $timestamps = true;
	
	public function registrations()
	{
		$this->has_many('Registration', 'serial_number');
	}
	
	public function generate()
	{
		
	}
}


?>
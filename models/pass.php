<?php

namespace PassKitServer\Models;

use Eloquent;

class Pass extends Eloquent
{
	public static $table = 'passes';
	public static $timestamps = true;
	public static $key = 'serial_number';
	
	public function registrations()
	{
		return $this->has_many('PassKitServer\Models\Registration', 'serial_number');
	}
	
	public function generate()
	{
		
	}
}


?>
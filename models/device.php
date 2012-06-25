<?php

namespace PassKitServer\Models;

use Eloquent;

class Device extends Eloquent
{
	public static $table = 'devices';
	public static $timestamps = true;
	
	public function registrations()
	{
		$this->has_many('Registration', 'device_id');
	}
}


?>
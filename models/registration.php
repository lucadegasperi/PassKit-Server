<?php

namespace PassKitServer\Models;

use Eloquent;

class Registration extends Eloquent
{
	public static $table = 'registrations';
	public static $timestamps = true;
	
	public function pass()
	{
		$this->has_one('Pass', 'serial_number');
	}
	
	public function device()
	{
		$this->has_one('Device', 'device_id');
	}
}


?>
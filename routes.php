<?php


use PassKitServer\Libraries\PassSigner;
use PassKitServer\Models\Device;
use PassKitServer\Models\Pass;
use PassKitServer\Models\Registration;

Route::get('(:bundle)', function()
{
	$c_url = Config::get('passkitserver::config.certificate_url');
	$c_pass = 'passkitcertificate';
	$p_url = Config::get('passkitserver::config.pass_url');
	$o_url = Config::get('passkitserver::config.output_url');
	$pass_signer = new PassSigner($p_url, $c_url, $c_pass, $o_url);
	$pass_signer->sign(true);
});

Route::get('(:bundle)/devices/(:any)/registrations/(:any)', array(
	'name' => 'get_updated_passes',
	function($device_id, $pass_type)
	{
		$last_update = Input::get('passesUpdatedSince', false);
		
		$registrations = Registration::with(array('pass' => function($query) use ($last_update)
		{
			if($last_update !== false)
			{
				$query->where('updated_at','>',$last_update);
			}
		}))
		->where('pass_type', '=', $pass_type)
		->where('device_id', '=', $device_id)
		->get();
		
		
		// prepare the response
		
		$response = array();
		
		$serials = array();
		
		$new_last_updated = 0;
		
		if(!empty($registrations))
		{
			foreach($registrations as $registration)
			{
				$pass = $registration->pass;
				if($pass != null)
				{
					$serials[] = $pass->serial_number;
					
					// get the latest date from all the updated passes, use it as the new last_update tag
					if(strtotime($pass->updated_at) > $new_last_updated)
					{
						$new_last_updated = strtotime($pass->updated_at);
					}
				}
					
			}
		}
		
		$response['serialNumbers'] = $serials;
		$response['lastUpdated'] = $new_last_updated;
		
		Log::info(json_encode($response));
		
		return Response::json($response);
	}
));

Event::listen('laravel.query', function($sql, $bindings, $time)
{
	//echo $sql;
	//print_r($bindings);
});



Route::post('(:bundle)/devices/(:any)/registrations/(:any)/(:any)', array(
	'name' => 'post_device',
	'before' => 'check_auth_token',
	function($device_id, $pass_type, $serial_number)
	{
		Log::info('device_id: '.$device_id);
		Log::info('pass_type: '.$pass_type);
		Log::info('serial_number: '.$serial_number);
		Log::info(json_encode($_SERVER));
	}
));

Route::delete('(:bundle)/devices/(:any)/registrations/(:any)/(:any)', array(
	'name' => 'delete_device',
	'before' => 'check_auth_token',
	function($device_id, $pass_type, $serial_number)
	{	
		Log::info('device_id: '.$device_id);
		Log::info('pass_type: '.$pass_type);
		Log::info('serial_number: '.$serial_number);
	}
));

Route::get('(:bundle)/passes/(:any)/(:any)', array(
	'name' => 'get_pass',
	'before' => 'check_auth_token',
	function($pass_type, $serial_number)
	{
		// check the auth token, how?
		
		// get the pass data, check for modifications
		
		// if modified send the pass otherwise 304
		
		// do the procedure to render, sign and compress the pass.
	}
));

Route::get('(:bundle)/log', array(
	'name' => 'get_log',
	function()
	{
		$it = new \RecursiveDirectoryIterator(path('storage').'logs');

		foreach(new \RecursiveIteratorIterator($it) as $file)
		{
			echo File::get($file->getPathname());
		}
	}
));

Route::post('(:bundle)/log', array(
	'name' => 'post_log',
	'before' => 'check_auth_token',
	function()
	{	
		Log::info(json_encode(Input::all()));
	}
));

Route::filter('check_auth_token', function(){
	
});


?>
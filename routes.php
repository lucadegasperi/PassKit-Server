<?php

namespace PassKitServer;

use Config;
use Route;
use PassKitServer\Libraries\PassSigner;

/**
 * Handle the documentation homepage.
 *
 * This page contains the "introduction" to Laravel.
 */
Route::get('(:bundle)', function()
{
	$c_url = Config::get('passkitserver::config.certificate_url');
	$c_pass = Config::get('passkitserver::config.certificate_password');
	$p_url = Config::get('passkitserver::config.pass_url');
	$o_url = Config::get('passkitserver::config.output_url');
	$pass_signer = new PassSigner($p_url, $c_url, $c_pass, $o_url);
	$pass_signer->sign(true);
});

?>
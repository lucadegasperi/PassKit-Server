<?php

return array(

	'temporary_directory' => path('storage') . 'work',
	
	'certificate_url' => Bundle::path('passkitserver'). 'contents' . DS . 'certificates' . DS . 'certificate.p12',
	
	'pass_url' => Bundle::path('passkitserver'). 'contents' . DS . 'passes' . DS . 'Coupon',
	
	'output_url' => path('storage') . 'passes'. DS . 'Coupon.pkpass',

);

?>
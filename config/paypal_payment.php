<?php

return array(
	# Account credentials from developer portal
	'Account' => array(
		'ClientId' => 'AXwdekd3isQIWfC-S_z_lbYEi-A8yQC1NcSUMxsuYTtWtourB1kPGv9Ny5SwKPw4euua6qbvVnldf53Z',
		'ClientSecret' => 'EJjRF5s13K5eJGxrO2SsYfuzYC8xua_r4YrcnNpa3UdEjBf3y7oy_I5YuhLHDTLWviMeWOCZkMgK7qrV',
	),

	# Connection Information
	'Http' => array(
		'ConnectionTimeOut' => 30,
		'Retry' => 1,
		//'Proxy' => 'http://[username:password]@hostname[:port][/path]',
	),

	# Service Configuration
	'Service' => array(
		# For integrating with the live endpoint,
		# change the URL to https://api.paypal.com !
		# For devlopment URL : https://api.sandbox.paypal.com
		'EndPoint' => 'https://api.paypal.com',
	),


	# Logging Information
	'Log' => array(
		'LogEnabled' => true,

		# When using a relative path, the log file is created
		# relative to the .php file that is the entry point
		# for this request. You can also provide an absolute
		# path here
		'FileName' => '../PayPal.log',

		# Logging level can be one of FINE, INFO, WARN or ERROR
		# Logging is most verbose in the 'FINE' level and
		# decreases as you proceed towards ERROR
		'LogLevel' => 'FINE',
	),
);

<?php
return array(

	'version' => '1',

	'route' => [
		'v1' => [ 'prefix' => 'api/v1' ]
	],

	'keys' => [
		env('API_KEY', 'secret')
	],

);
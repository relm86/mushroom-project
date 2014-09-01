<?php
return array(	
	"base_url"   => "http://localhost/relm86/picture-foods.com/api/public/social/auth",
	"providers"  => array (
		"Google"		=> array (
			"enabled"	=> true,
			"keys"		=> array ( "id" => "ID", "secret" => "SECRET" ),
			),
		"Facebook" => array (
			"enabled"	=> true,
			"keys"		=> array ( "id" => "1450693038551335", "secret" => "25bce6ce1fda6346a032ab73bded2fe5" ),
			//"scope"		=> "email",
			"scope"		=> "email, user_about_me, user_birthday, user_hometown", // optional
			"display" => "popup" // optional
			),
		"Twitter" => array (
			"enabled"	=> true,
			"keys"		=> array ( "key" => "ID", "secret" => "SECRET" )
			)
	),

	"debug_mode"	=> false,
	"debug_file" => "hybridauth.log", 

);
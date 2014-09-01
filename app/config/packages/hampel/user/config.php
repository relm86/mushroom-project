<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Database connection name
	|--------------------------------------------------------------------------
	|
	| The name of the connection we will use to connect to the database
	| containing the auth tables.
	|
	| If your authentication tables are in a different database to your main
	| application data, or even if they are in the same table, but use a
	| different table prefix, you should create a second database connection
	| for app/config/database.php and specify that name here.
	|
	| Default value: 'default'
	|
	*/

	'connection' => 'default',

	/*
	|--------------------------------------------------------------------------
	| ID field for users
	|--------------------------------------------------------------------------
	|
	| The name of the field which stores the unique id
	|
	| Note - if using the provided database migrations, leave this at the
	| default value.
	|
	| Also remember that while database column names are not case sensitive,
	| array keys in PHP are, so id is NOT the same as ID. If your database
	| uses ID for the ID field, make sure you change this value to ID.
	|
	| Default value: 'id'
	|
	*/

	'id_field' => 'ID',

	/*
	|--------------------------------------------------------------------------
	| Username field for authentication
	|--------------------------------------------------------------------------
	|
	| The name of the field which stores the username
	|
	| Note - if using the provided database migrations, leave this at the
	| default value.
	|
	| Default value: 'username'
	|
	*/

	'username_field' => 'user_login',

	/*
	|--------------------------------------------------------------------------
	| Email field for authentication
	|--------------------------------------------------------------------------
	|
	| The name of the field which stores the email address
	|
	| Note - if using the provided database migrations, leave this at the
	| default value.
	|
	|
	| Default value: 'email'
	|
	*/

	'email_field' => 'user_email',

	/*
	|--------------------------------------------------------------------------
	| Password field for authentication
	|--------------------------------------------------------------------------
	|
	| The name of the field which stores the password
	|
	| Note - if using the provided database migrations, leave this at the
	| default value.
	|
	| Default value: 'password'
	|
	*/

	'password_field' => 'user_pass',

	/*
	|--------------------------------------------------------------------------
	| Remember Token field for authentication
	|--------------------------------------------------------------------------
	|
	| The name of the field which stores the remember token
	|
	| Note - if using the provided database migrations, leave this at the
	| default value.
	|
	| Default value: 'remember_token'
	|
	*/

	'remember_token_field' => 'remember_token',

);

?>

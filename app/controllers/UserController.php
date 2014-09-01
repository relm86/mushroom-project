<?php

use Hautelook\Phpass\PasswordHash;
use Hampel\WordPress\Hashing\WordPressHasher;

class UserController extends BaseController {

	/*
	==================
		user_reg API endpoint
	==================
	*/

	public function user_reg() {
		$user_input = array(
			'user_login' => Input::get('username'),
			'user_pass' => Input::get('password'),
			'user_nicename' => Input::get('username'),
			'user_email' => Input::get('user_email'),
			'user_url' => Input::get('website'),
			'display_name' => trim(Input::get('first_name').' '.Input::get('last_name'))
		);

		// check if username is email address
		$is_email = TRUE;
		$validator = Validator::make(
			array('user_login' => Input::get('username')),
			array('user_login' => 'email')
			);

		if($validator->fails())	{
			$is_email = FALSE;
		}

		if($is_email==FALSE) {
			// create USER using wordpress LOGIC
			
			// validate again
			$user = new User($user_input);
			$user->validate();

			// wphash
			$hasher = new PasswordHash(8, true);
			$wphash = new WordPressHasher($hasher);
			$user->user_pass = $wphash->make($user->user_pass);

			// compat mode
			if($user->user_url=='') {
				$user->user_url = '';
			}
			$date = new \DateTime;
			$user->user_registered = $date;

			if (!$user->save())	
			{
				App::abort(500, 'User was not saved.');
			}
			
			//get files
			if (Input::hasFile('photo')){
				$files = Input::file('photo');
			}
			$user->saveImage(Input::all(), $files, $user->ID);
			
			return Response::json($user->toArray(), 201);

		} else {
			App::abort(500, 'username is in email address format, rejected.');
		}
	}

	public function user_facebook_reg() {
		// check is exist facebook with email and fbIdentifier
		$fb_count = Facebook::where('fbIdentifier', '=', Input::get('fbIdentifier'))
						->where('email', '=', Input::get('email'))->count();
		if($fb_count>0) {
			$facebook = Facebook::where('fbIdentifier', '=', Input::get('fbIdentifier'))
						->where('email', '=', Input::get('email'))->first();
			return Response::json($facebook->toArray(), 201);
		} else {
			$facebook = new Facebook(Input::get());
			$facebook->validate();
			if (!$facebook->save()) {
				App::abort(500, 'Facebook profile was not saved');
			}
			return Response::json($facebook->toArray(), 201);
		}
	}

	public function user_login(){
		// init global App Config
		$myApp = App::make('myApp');

		if (!Request::getUser()) {
			App::abort(401, 'A valid username is required');
		}

		$is_email = TRUE;
		// check if username is email address
		$validator = Validator::make(
			array('username' => Request::getUser()),
			array('username' => 'email')
			);

		if($validator->fails())	{
			$is_email = FALSE;
		}

		if($is_email==FALSE) {
			$userdata = $array = array(
				'user_login' => Request::getUser(),
				'password' => Request::getPassword()
			);

			if (!Auth::attempt($userdata)) {
				App::abort(401, 'Invalid username password data');
			} else {
				$myApp->wp_user_id = Auth::user()->id;
			}
		} else {
			$fb_count = Facebook::where('email', '=', Request::getUser())
						->where('fbIdentifier', '=', Request::getPassword())
						->count();
			if($fb_count==1) {
//				echo 'FACEBOOK LOGIN SUCCESS';
				$myApp->wp_user_id = Request::getPassword();
			} else {
				App::abort(401, 'Invalid facebook username password data');
			}
		}
	}

	public function user_login_return(){
		//return Response::json(Auth::user());	
		$user = User::where('ID', Auth::user()->id)->first();

		$picture = DB::table('fs_user_photos')->where('wp_user_id', '=',  Auth::user()->id)->first();
		if(isset($picture)) {
			$user->photo = $picture->imgPath;
		} else {
			$user->photo = '';
		}
		
		return Response::json($user);
	}

}	
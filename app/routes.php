<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
/*
Route::get('social/{action?}', array("as" => "hybridauth", function($action = "")
{
	// check URL segment
	if ($action == "auth") {
		// process authentication
		try {
			Hybrid_Endpoint::process();
		}
		catch (Exception $e) {
			// redirect back to http://URL/social/
			return Redirect::route('hybridauth');
		}
		return;
	}
	try {
		// create a HybridAuth object
		$socialAuth = new Hybrid_Auth(app_path() . '/config/hybridauth.php');
		// authenticate with Google
		$provider = $socialAuth->authenticate("Facebook");
		// fetch user profile
		$userProfile = $provider->getUserProfile();
	}
	catch(Exception $e) {
		// exception codes can be found on HybBridAuth's web site
		return $e->getMessage();
	}
	// access user profile data
	echo "Connected with: <b>{$provider->id}</b><br />";
	echo "As: <b>{$userProfile->displayName}</b><br />";
	echo "<pre>" . print_r( $userProfile, true ) . "</pre><br />";

	// logout
	$provider->logout();
}));
*/

Route::group(array('prefix' => 'v1'), function()
{
	// ADMIN Feature
	Route::get('admin_truncate_bookmark', array('before' => 'api.auth|api.limit', 'uses' => 'BookmarkController@p_admin_truncate'));
	Route::get('admin_truncate_picture', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_admin_truncate'));
	Route::get('admin_truncate_recipe', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_admin_truncate'));


	Route::post('picture_add', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_add'));
	Route::post('picture_edit/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_edit'))->where('id', '\d+');
	Route::delete('picture_delete/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_delete'))->where('id', '\d+');
	Route::get('picture_list', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_list'));
	Route::get('picture_list_all', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_list_all'));

	// Done Feature
	Route::post('user_reg', 'UserController@user_reg');
	Route::post('user_facebook_reg', 'UserController@user_facebook_reg');
	Route::post('recipe_add', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_add'));
	Route::post('recipe_edit/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_edit'))->where('id', '\d+');
	Route::delete('recipe_delete/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_delete'))->where('id', '\d+');
	Route::get('recipe_list', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_list'));
	Route::get('recipe_list_user/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_list_user'))->where('id', '\d+');
	Route::get('recipe_list_all', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_list_all'));
	Route::get('recipe_search/{keyword}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_search'));
	Route::get('recipe_search_all/{keyword}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_search_all'));
	Route::post('bookmark_add', array('before' => 'api.auth|api.limit', 'uses' => 'BookmarkController@p_add'));
	Route::get('bookmark_list', array('before' => 'api.auth|api.limit', 'uses' => 'BookmarkController@p_list'));
	Route::get('bookmark_list_all', array('before' => 'api.auth|api.limit', 'uses' => 'BookmarkController@p_list_all'));
	Route::get('bookmark_list_user/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'BookmarkController@p_list_user'))->where('id', '\d+');
	Route::delete('bookmark_delete/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'BookmarkController@p_delete'))->where('id', '\d+');

	Route::get('recipe_count', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_count'));
	Route::get('bookmark_count', array('before' => 'api.auth|api.limit', 'uses' => 'BookmarkController@p_count'));
	Route::get('recipe_like_status/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_like_status'))->where('id', '\d+');
	Route::post('recipe_like', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_like'));
	Route::post('recipe_unlike', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_unlike'));
	Route::post('recipe_dislike', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_dislike'));
	Route::post('recipe_undislike', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_undislike'));

	Route::get('like_count/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_like_count'))->where('id', '\d+');
	//Route::get('like_list', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_like_list'));
	Route::get('like_list_all', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_like_list_all'));
	Route::get('like_list_user/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_like_list_user'))->where('id', '\d+');
	Route::get('like_list/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'RecipeController@p_like_list'))->where('id', '\d+');


	// Partial Feature
	// login using facebook
	Route::post('user_login', array('before' => 'api.auth|api.limit', 'uses' => 'UserController@user_login_return'));


	// Unimplemented Feature
	Route::post('photo_add', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_add'));
	Route::post('photo_edit/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_edit'))->where('id', '\d+');
	Route::delete('photo_delete/{id}', array('before' => 'api.auth|api.limit', 'uses' => 'PhotoController@p_delete'))->where('id', '\d+');

	Route::post('user_forgot_password', array('before' => 'api.auth|api.limit', 'uses' => 'UserController@user_forgot_password'));
	Route::get('home_background_image', array('before' => 'api.auth|api.limit', 'uses' => 'HomeController@home_background_image'));


});
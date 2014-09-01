<?php

use Illuminate\Auth\UserInterface;

class User extends Hampel\WordPress\Auth\Models\WordPressUser
{

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = array('user_pass');

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array('user_login', 'user_pass', 'user_nicename', 'user_email', 'user_email', 'user_url', 'display_name', 'user_registered');

	/**
	 * The date fields for the model.clear
	 *
	 * @var array
	 */

	/**
	 * Validate the model's attributes.
	 *
	 * @return void
	 */
	public function validate()
	{
		$val = Validator::make($this->attributes, array(
			'user_login' => 'alpha_dash|required|unique:users',
			'user_email' => 'required|email|unique:users',
			'first_name' => 'max:100',
			'last_name' => 'required_with:first_name|max:100',
			'user_url' => 'max:100|url',
			'user_pass' => 'min:4',
		));

		if ($val->fails())
		{
			throw new ValidationException($val);
		}
	}

	public function saveImage($input, $file, $wp_user_id){
		$destinationPath = 'uploads/profile/';
		$filename = str_random(8) . '-' . $file->getClientOriginalName();
		$file->move($destinationPath, $filename);
		// check exist
		$img_count = DB::table('fs_user_photos')->where('wp_user_id', '=', $wp_user_id)->count();
		if($img_count>0) {
			DB::table('fs_user_photos')->where('wp_user_id', '=', $wp_user_id)->update(array('imgPath'=>$destinationPath.$filename));
		} else {
			// if not exist
			$id = DB::table('fs_user_photos')->insertGetId(array('wp_user_id' => $wp_user_id, 'imgPath' => $destinationPath.$filename));
		}
	}

	public function toArray() {
		$data = parent::toArray();
		// Profile Picture
		$picture = DB::table('fs_user_photos')->where('wp_user_id', '=', $data['ID'])->first();
		if(isset($picture)) {
			$data['photo'] = $picture->imgPath;
		} else {
			$data['photo'] = '';
		}
		return $data;
	}
	
}
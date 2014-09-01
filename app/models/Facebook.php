<?php

use Illuminate\Auth\UserInterface;

class Facebook extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'fs_facebook_profile';

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	//protected $hidden = array('active');
	protected $hidden = array();

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	//protected $fillable = array('wp_user_id', 'recipe_id', 'img_order', 'img_path', 'img_width', 'img_height');
	protected $fillable = array('fbIdentifier', 'email', 'displayName', 'firstName', 'lastName', 'photoURL');
	// 'is_subsitem', 'subsitem',

	/**
	 * The date fields for the model.clear
	 *
	 * @var array
	 */
	protected $dates = array('created_at', 'updated_at');

	public static function display_All() {
		$facebook = Facebook::get();

		if (!$facebook) {
			throw new NotFoundException('Facebook profile was not found');
		}
		
		return $facebook;
	}

	/**
	 * Find a facebook by the given id
	 *
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findById($id) {
		//$item = static::find($id);
		$facebook = Facebook::where('id','=', $id)->get();

		if (!$facebook) {
			throw new NotFoundException('Facebook was not found');
		}

		return $facebook;
	}	
	

	/**
	 * Validate the model's attributes.
	 *
	 * @return void
	 */
	public function validate($id=null)
	{
		$rules = array(
			'fbIdentifier'	=> 'required|unique:fs_facebook_profile,fbIdentifier',
			'email' 		=> 'unique:fs_facebook_profile,email',
			'displayName' 	=> '',
			'firstName'		=> '',
			'lastName'		=> '',
			'photoURL'		=> '',
		);
		
		$val = Validator::make($this->attributes, $rules);
		if ($val->fails()) {
			throw new ValidationException($val);
		}
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$data = parent::toArray();
		$data['id'] = (int) $data['id'];
		$data['created_at'] = $this->fromDateTime($this->created_at);
		$data['updated_at'] = $this->fromDateTime($this->updated_at);
		return $data;
	}

	public static function admin_truncate()
	{
		DB::table('fs_facebook_profile')->truncate();
	}

}
<?php

use Illuminate\Auth\UserInterface;

class Photo extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'fs_photos';

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	//protected $hidden = array('active');
	protected $hidden = array('title', 'description');

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	//protected $fillable = array('wp_user_id', 'recipe_id', 'img_order', 'img_path', 'img_width', 'img_height');
	protected $fillable = array('wp_user_id', 'imgPath', 'imgWidth', 'imgHeight');
	// 'is_subsitem', 'subsitem',

	/**
	 * The date fields for the model.clear
	 *
	 * @var array
	 */
	protected $dates = array('created_at', 'updated_at');

	public static function display_All() {
		$photo = Photo::get();
		if (!$photo) {
			throw new NotFoundException('Pictures was not found');
		}
		return $photo;
	}

	/**
	 * Find a photo by the given id
	 *
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findById($id) {
		//$item = static::find($id);
		$photo = Photo::where('id','=', $id)->get();

		if (!$photo) {
			throw new NotFoundException('Photo was not found');
		}

		return $photo;
	}	
	
	/**
	 * Find a photo by ownership by the given id
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwner($owner_id)
	{
		$photo = Photo::where('wp_user_id','=', $owner_id)->get();
		if (!$photo) {
			throw new NotFoundException('Photo was not found');
		}
		return $photo;
	}

	/**
	 * Find a photo by ID, and verify its ownership by the given user
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwnerAndId($owner_id, $id)
	{
		$photo = static::find($id);
		if (!$photo) {
			throw new NotFoundException('Photo was not found');
		}

		if ($photo->wp_user_id !== $owner_id) {
			throw new PermissionException('This photo is not owned by you');
		}

		return $photo;
	}

	/**
	 * Validate the model's attributes.
	 *
	 * @return void
	 */
	public function validate($id=null)
	{
		$rules = array(
			'wp_user_id'	=> 'required',
			'title' 		=> '',
			'description'	=> '',
			'imgPath'		=> '',
			'imgWidth'		=> 'integer|required',
			'imgHeight'		=> 'integer|required',
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
		// go to public/uploads
		$destinationPath = 'uploads/';

		$files = File::files($destinationPath);
		foreach ($files as $key => $file) {
			File::delete($file);
		}
		DB::table('fs_photos')->truncate();
	}

}
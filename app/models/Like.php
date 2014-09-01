<?php

use Illuminate\Auth\UserInterface;

class Like extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'fs_recipes_like_rel';

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	//protected $hidden = array('active');
//	protected $hidden = array('title', 'description');

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	//protected $fillable = array('wp_user_id', 'recipe_id', 'img_order', 'img_path', 'img_width', 'img_height');
	protected $fillable = array('wp_user_id', 'recipe_id', 'status');
	// 'is_subsitem', 'subsitem',

	/**
	 * The date fields for the model.clear
	 *
	 * @var array
	 */
	protected $dates = array('created_at', 'updated_at');

	public static function display_All() {
		$like = Like::get();
		if (!$like) {
			throw new NotFoundException('Like was not found');
		}
		return $like;
	}

	/**
	 * Find a like by the given id
	 *
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findById($id) {
		//$item = static::find($id);
		$like = Like::where('id','=', $id)->get();

		if (!$like) {
			throw new NotFoundException('Like was not found');
		}

		return $like;
	}	
	
	/**
	 * Find a like by ownership by the given id
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwner($owner_id)
	{
		$like = Like::where('wp_user_id','=', $owner_id)->get();
		if (!$like) {
			throw new NotFoundException('Like was not found');
		}
		return $like;
	}

	/**
	 * Find a like by ID, and verify its ownership by the given user
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwnerAndId($owner_id, $id)
	{
		$like = static::find($id);
		if (!$like) {
			throw new NotFoundException('like was not found');
		}

		if ($like->wp_user_id !== $owner_id) {
			throw new PermissionException('Like is not owned by you');
		}

		return $like;
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
			'recipe_id' 	=> 'required',
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
		if($data['status']=='L') {
			$data['status'] = 'Like';
		} else {
			$data['status'] = 'Dislike';
		}
		$data['created_at'] = $this->fromDateTime($this->created_at);
		$data['updated_at'] = $this->fromDateTime($this->updated_at);
		return $data;
	}

	public static function admin_truncate()
	{
		DB::table('fs_recipes_like_rel')->truncate();
	}

}
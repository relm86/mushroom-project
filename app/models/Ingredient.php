<?php

use Illuminate\Auth\UserInterface;

class Ingredient extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'fs_recipes_ingredient_rel';

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
	protected $fillable = array('wp_user_id', 'recipe_id', 'sort', 'ingCount', 'ingDescription');
	// 'is_subsitem', 'subsitem',

	/**
	 * The date fields for the model.clear
	 *
	 * @var array
	 */
	protected $dates = array('created_at', 'updated_at');

	/**
	 * Find a ingredient by the given id
	 *
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findById($id)
	{
		//$item = static::find($id);
		$ingredient = Ingredient::where('id','=', $id)->get();

		if (!$ingredient)
		{
			throw new NotFoundException('Ingredient was not found');
		}

		return $ingredient;
	}	
	
	/**
	 * Find a ingredient by ownership by the given id
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwner($owner_id)
	{
		$ingredient = Ingredient::where('wp_user_id','=', $owner_id)->get();
		if (!$ingredient) {
			throw new NotFoundException('Ingredient was not found');
		}
		
/*
		if ((int) $item->user_id !== $owner_id)
		{
			throw new PermissionException('Insufficient access privileges for this item');
		}
*/
		return $ingredient;
	}

	/**
	 * Find a ingredient by ID, and verify its ownership by the given user
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwnerAndId($owner_id, $id) {

		$ingredient = static::find($id);

		if (!$ingredient) {
			throw new NotFoundException('Ingredient was not found');
		}

		if ($ingredient->wp_user_id !== $owner_id) {
			throw new PermissionException('Insufficient access privileges for this ingredient');
		}

		return $ingredient;
	}

	/**
	 * Validate the model's attributes.
	 *
	 * @return void
	 */
	public function validate($id=null)
	{
		$rules = array(
			'wp_user_id'		=> 'required',
			'recipe_id'			=> 'integer|required',
			'sort'				=> 'integer',
			'ingCount'			=> 'integer',
			'ingDescription'	=> '',
		);
		
//		print_r($rules);
//		print_r($this->attributes);
		//die();
		//$val = Validator::make($this->attributes, $rules);
		
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
	public function toArray() {
		$data = parent::toArray();
		$data['id'] = (int) $data['id'];
		$data['created_at'] = $this->fromDateTime($this->created_at);
		$data['updated_at'] = $this->fromDateTime($this->updated_at);
		return $data;
	}

	public static function admin_truncate() {
		DB::table('fs_recipes_ingredient_rel')->truncate();
	}

}
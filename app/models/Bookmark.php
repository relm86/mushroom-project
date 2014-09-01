<?php

use Illuminate\Auth\UserInterface;

class Bookmark extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'fs_bookmarks';

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
	protected $fillable = array('wp_user_id', 'recipe_id');
	// 'is_subsitem', 'subsitem',

	/**
	 * The date fields for the model.clear
	 *
	 * @var array
	 */
	protected $dates = array('created_at', 'updated_at');

	public static function display_All() {
		$bookmark = Bookmark::get();

		if (!$bookmark) {
			throw new NotFoundException('Bookmarks was not found');
		}
		return $bookmark;
	}

	/**
	 * Find a recipe by the given keywords
	 *
	 * @param  string  $keyword
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByKeywordAll($keyword) {
		
		$bookmark = Bookmark::where('title','LIKE', '%'.trim($keyword).'%')
		->get();
		
		/*
		$item = Item::where('active','=', '1')
		->orWhere(function($query) use ($keyword)
		{
			//echo 'KEYWORD '.$keyword;
			$query->where('name','LIKE', '%'.trim($keyword).'%')
				  ->where('description', 'LIKE', '%'.trim($keyword).'%');
		})
		->get();		
		*/
		//$item = Item::whereRaw('active = 1', array(20))->get();
		//$item = Item::whereRaw(' active = "1" ', array($keyword))->get();
		
		if (!$bookmark) {
			throw new NotFoundException('Bookmark was not found');
		}

		return $bookmark;
	}		


	/**
	 * Find a recipe by the given keywords
	 *
	 * @param  string  $keyword
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByKeyword($keyword)
	{
		$myApp = App::make('myApp');
		$owner_id = $myApp->wp_user_id;

		$bookmark = Bookmark::where('title','LIKE', '%'.trim($keyword).'%')
		->where('wp_user_id','=', $owner_id)
		->get();
		
		/*
		$item = Item::where('active','=', '1')
		->orWhere(function($query) use ($keyword)
		{
			//echo 'KEYWORD '.$keyword;
			$query->where('name','LIKE', '%'.trim($keyword).'%')
				  ->where('description', 'LIKE', '%'.trim($keyword).'%');
		})
		->get();		
		*/
		//$item = Item::whereRaw('active = 1', array(20))->get();
		//$item = Item::whereRaw(' active = "1" ', array($keyword))->get();
		
		if (!$bookmark) {
			throw new NotFoundException('Bookmark was not found');
		}

		return $bookmark;
	}		
	
	/**
	 * Find a recipe by the given id
	 *
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findById($id) {
		//$item = static::find($id);
		$bookmark = Bookmark::where('id','=', $id)->get();

		if (!$bookmark) {
			throw new NotFoundException('Bookmark was not found');
		}

		return $bookmark;
	}	
	
	/**
	 * Find a recipe by ownership by the given id
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwner($owner_id) {

		$bookmark = Bookmark::where('wp_user_id','=', $owner_id)->get();
		if (!$bookmark) {
			throw new NotFoundException('Bookmark was not found');
		}
		
/*
		if ((int) $item->user_id !== $owner_id)
		{
			throw new PermissionException('Insufficient access privileges for this item');
		}
*/
		return $bookmark;
	}

	/**
	 * Find a recipe by ID, and verify its ownership by the given user
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwnerAndId($owner_id, $id) {

		$bookmark = static::find($id);
		if (!$bookmark) {
			throw new NotFoundException('Bookmark was not found');
		}

		if ($bookmark->wp_user_id !== $owner_id) {
			throw new PermissionException('Insufficient access privileges for this recipe');
		}

		return $bookmark;
	}

	/**
	 * Validate the model's attributes.
	 *
	 * @return void
	 */
	public function validate($id=null) {
		$rules = array(
			'wp_user_id'=> 'integer|required|exists:users,ID',
			'recipe_id' 	=> 'integer|required|max:255',
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
/*	
		$data = parent::toArray();
		$data['id'] = (int) $data['id'];
		$data['created_at'] = $this->fromDateTime($this->created_at);
		$data['updated_at'] = $this->fromDateTime($this->updated_at);
		return $data;
*/
		$data = parent::toArray();
//		$data['id'] = (int) $data['id'];
		$recipe = Recipe::where('id',$data['recipe_id'])->first();
		$data['id'] = $recipe->id;
		$data['title'] = $recipe->title;
		$data['recipe'] = $recipe->recipe;
//		print_r($recipe);die();
		
		if($recipe->main_picture_id==null || $recipe->main_picture_id=='0') {
			$data['main_picture_id']= '';
		} else {
			$photo = Photo::where('id','=', $recipe->main_picture_id)->first();
			$data['photo']['imgPath'] = $photo->imgPath;
			$data['photo']['imgWidth'] = $photo->imgWidth;
			$data['photo']['imgHeight'] = $photo->imgHeight;
		}
		if(strlen($recipe->wp_user_id)>10) {
			$author = Facebook::where('fbIdentifier', '=', $recipe->wp_user_id)->first();
			$data['author']['displayName'] =  $author['displayName'];
			$data['author']['email'] =  $author['email'];
			$data['author']['wp_user_id'] =  $author['fbIdentifier'];
			$data['author']['photo'] =  $author['photoURL'];
		} else {
			$author = User::where('ID', '=', $recipe->wp_user_id)->first();
			$data['author']['displayName'] =  $author['display_name'];
			$data['author']['email'] =  $author['user_email'];
			$data['author']['wp_user_id'] =  $author['ID'];
			
			$picture = DB::table('fs_user_photos')->where('wp_user_id', '=', $recipe->wp_user_id)->first();
			if(isset($picture)) {
				$data['author']['photo'] = $picture->imgPath;
			} else {
				$data['author']['photo'] = '';
			}
		}
		$ingredients = Ingredient::where('recipe_id', '=', $recipe->id)
					->orderBy('sort', 'asc')
					->get();
		foreach ($ingredients as $key => $ingredient) {
			$data['ingredient'][] = array(
				'sort' => $ingredient->sort,
				'ingCount' => $ingredient->ingCount,
				'ingDescription'  => $ingredient->ingDescription,
			);
		}

		$steps = Step::where('recipe_id', '=', $recipe->id)
					->orderBy('sort', 'asc')
					->get();
		foreach ($steps as $key => $step) {
			$data['step'][] = array(
				'sort' => $step->sort,
				'stepImageID' => $step->stepImageID,
				'stepDetails'  => $step->stepDetails,
			);
		}

		$data['created_at'] = $this->fromDateTime($this->created_at);
		$data['updated_at'] = $this->fromDateTime($this->updated_at);
		return $data;		
	}

	public static function admin_truncate() {
		DB::table('fs_bookmarks')->truncate();
	}

}
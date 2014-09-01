<?php

use Illuminate\Auth\UserInterface;

class Recipe extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'fs_recipes';

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
	protected $fillable = array('wp_user_id', 'title', 'recipe', 'main_picture_id');
	// 'is_subsitem', 'subsitem',

	/**
	 * The date fields for the model.clear
	 *
	 * @var array
	 */
	protected $dates = array('created_at', 'updated_at');

	public static function display_All()
	{
		$recipe = Recipe::get();
		$myApp = App::make('myApp');
		$owner_id = $myApp->wp_user_id;
		//$recipe = Recipe::where('wp_user_id','!=', $owner_id)->get();

		if (!$recipe)
		{
			throw new NotFoundException('Recipes was not found');
		}
		
		return $recipe;
	}

	/**
	 * Find a recipe by the given keywords
	 *
	 * @param  string  $keyword
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByKeywordAll($keyword)
	{
		
		$recipe = Recipe::where('title','LIKE', '%'.trim($keyword).'%')
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
		
		if (!$recipe)
		{
			throw new NotFoundException('Recipe was not found');
		}

		return $recipe;
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

		$recipe = Recipe::where('title','LIKE', '%'.trim($keyword).'%')
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
		
		if (!$recipe)
		{
			throw new NotFoundException('Recipe was not found');
		}

		return $recipe;
	}		
	
	/**
	 * Find a recipe by the given id
	 *
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findById($id)
	{
		//$item = static::find($id);
		$recipe = Recipe::where('id','=', $id)->get();

		if (!$recipe)
		{
			throw new NotFoundException('Recipe was not found');
		}

		return $recipe;
	}	
	
	/**
	 * Find a recipe by ownership by the given id
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwner($owner_id)
	{
		$recipe = Recipe::where('wp_user_id','=', $owner_id)->get();
		if (!$recipe) {
			throw new NotFoundException('Recipe was not found');
		}

		return $recipe;
	}

	/**
	 * Find a recipe by ID, and verify its ownership by the given user
	 *
	 * @param  Illuminate\Auth\UserInterface|int  $owner
	 * @param  int  $id
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public static function findByOwnerAndId($owner_id, $id)
	{

		$recipe = static::find($id);

		if (!$recipe) {
			throw new NotFoundException('Recipe was not found');
		}

		if ($recipe->wp_user_id !== $owner_id) {
			throw new PermissionException('Insufficient access privileges for this recipe');
		}

		return $recipe;
	}

	/**
	 * Validate the model's attributes.
	 *
	 * @return void
	 */
	public function validate($id=null)
	{
		$rules = array(
			'wp_user_id'=> 'required',
			'title' 	=> 'required|max:255',
			'recipe' 	=> '',
			'main_picture_id' => '',
		);
		
//		print_r($rules);
//		print_r($this->attributes);
		//die();
		//$val = Validator::make($this->attributes, $rules);
		
		$val = Validator::make($this->attributes, $rules);

		if ($val->fails())
		{
			throw new ValidationException($val);
		}
	}

	/**
	 * Save images to different Table
	 *
	 * @access public
	 * @param array $input
	 * @param array $files
	 * @param int $recipe_id
	 *
	 */
	public function saveImage($input, $files, $recipe_id){
//		print_r($files);
		$owner = Auth::user();
		$owner_id = ($owner instanceof UserInterface) ? (int) $owner->id : (int) $owner;

		$i = 0;
		// select max() from table Photo with that recipe_id
		$max = Photo::where('recipe_id','=',$recipe_id)
					->where('wp_user_id','=',$owner_id)
					->max('img_order');
		if($max>0) {
			$i = $max;
		}

		foreach($files as $file){
			// apply $i increment
			$i++;

			$destinationPath = 'uploads/';
			$filename = str_random(8) . '-' . $file->getClientOriginalName();
			$file->move($destinationPath, $filename);
			
			$size = getimagesize($destinationPath.$filename);

			$photo = New Photo(
				array(
				'wp_user_id' => $owner_id,
				'recipe_id' => $recipe_id,
				'img_order' => $i,
				'img_path' => $destinationPath.$filename,
				'img_width' => $size[0],
				'img_height' => $size[1]
			));
			$photo->save();

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
		if($this['main_picture_id']==null || $this['main_picture_id']=='0') {
			$data['main_picture_id']= '';
		} else {
			$photo = Photo::where('id','=', $data['main_picture_id'])->first();
			if(isset($photo)) {
				$data['photo']['imgPath'] = $photo->imgPath;
				//$data['photo']['imgPath'] = public_path().$photo->imgPath;
				$data['photo']['imgWidth'] = $photo->imgWidth;
				$data['photo']['imgHeight'] = $photo->imgHeight;
			}
		}
		
		// photos
/*		
		$pictures = Photo::where('recipe_id', '=', $data['id'])
					->orderBy('img_order', 'asc')
					->get();
		foreach ($pictures as $key => $picture) {
			$data['picture'][] = array(
				'picture_order' => $picture->img_order,
				'picture_path'  => url($picture->img_path),
				'picture_width' => $picture->img_width,
				'picture_height'=> $picture->img_height
			);
		}
*/

		// Recipe creator
		if(strlen($data['wp_user_id'])>10) {
			// query from facebook data
			$author = Facebook::where('fbIdentifier', '=', $data['wp_user_id'])->first();
			$data['author']['displayName'] =  $author['displayName'];
			$data['author']['email'] =  $author['email'];
			$data['author']['wp_user_id'] =  $author['fbIdentifier'];
			$data['author']['photo'] =  $author['photoURL'];
		} else {
			// query from users
			$author = User::where('ID', '=', $data['wp_user_id'])->first();
			$data['author']['displayName'] =  $author['display_name'];
			$data['author']['email'] =  $author['user_email'];
			$data['author']['wp_user_id'] =  $author['ID'];
			
			$picture = DB::table('fs_user_photos')->where('wp_user_id', '=', $data['wp_user_id'])->first();
			if(isset($picture)) {
				$data['author']['photo'] = $picture->imgPath;
			} else {
				$data['author']['photo'] = '';
			}
		}

		// Ingredients
		$ingredients = Ingredient::where('recipe_id', '=', $data['id'])
					->orderBy('sort', 'asc')
					->get();
		foreach ($ingredients as $key => $ingredient) {
			$data['ingredient'][] = array(
				'sort' => $ingredient->sort,
				'ingCount' => $ingredient->ingCount,
				'ingDescription'  => $ingredient->ingDescription,
			);
		}

		// Steps
		$steps = Step::where('recipe_id', '=', $data['id'])
					->orderBy('sort', 'asc')
					->get();
		foreach ($steps as $key => $step) {
			$data['step'][] = array(
				'sort' => $step->sort,
				'stepImageID' => $step->stepImageID,
				'stepDetails'  => $step->stepDetails,
			);
		}

		// Like
		$xlike = Recipe::like_statistik($data['id']);
		$data['like'] = $xlike;
		
		$data['created_at'] = $this->fromDateTime($this->created_at);
		$data['updated_at'] = $this->fromDateTime($this->updated_at);
		return $data;
	}
	
	public static function like_statistik($id) {
		$like_count = Like::where('recipe_id', $id)->count();
		if($like_count>0) {
			$ret['count'] = $like_count;
			$like = Like::where('wp_user_id',Auth::user()->id)
					->where('recipe_id', $id)->first();
			if(isset($like->status)) {
				$ret['current_user_like'] = $like->status;
			} else {
				$ret['current_user_like'] = '-';
			}
			$like = Like::where('recipe_id', $id)->get();
			$ret['like_count'] = 0;
			$ret['dislike_count'] = 0;
			foreach($like as $like_data) {
				if($like_data->status=='L') {
					$ret['like_count'] = $ret['like_count'] + 1;
				}

				if($like_data->status=='D') {
					$ret['dislike_count'] = $ret['dislike_count'] + 1;
				}
			}
		} else {
			$ret['count'] = 0;
			$ret['current_user_like'] = '-';
			$ret['like_count'] = 0;
			$ret['dislike_count'] = 0;
		}
		return $ret;
	
	}

	public static function admin_truncate() {
		DB::table('fs_recipes')->truncate();
	}

}
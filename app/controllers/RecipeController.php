<?php

class RecipeController extends BaseController {

	public function p_add(){
	/**
	  * name ; The name of the recipe.
	  * description ; The description of the recipe.
	  **/
		// init global App Config
		$myApp = App::make('myApp');

		$recipe = new Recipe(Input::get());
		$recipe->wp_user_id = $myApp->wp_user_id;
		
		// cek apakah main_picture_id adalah milik sendiri atau tidak
		
		$recipe->validate();

		if (!$recipe->save()) {
			App::abort(500, 'Recipe was not saved');
		}

		if(trim(Input::get('ingredients'))!='') {
			// foreach, parse and save
			$ingredients = json_decode(Input::get('ingredients'), true);
			foreach ($ingredients as $key => $ingredient_var) {
				// $key is unused
				$ingredient = New Ingredient($ingredient_var);
				$ingredient->wp_user_id = $myApp->wp_user_id;
				$ingredient->recipe_id = $recipe->id;
				$ingredient->validate();
				if (!$ingredient->save()) {
					App::abort(500, 'Ingredients was not saved');
				}
			}
		}

		if(trim(Input::get('steps'))!='') {
			// foreach, parse and save
			$steps = json_decode(Input::get('steps'), true);
			foreach ($steps as $key => $step_var) {
				// $key is unused
				$step = New Step($step_var);
				$step->wp_user_id = $myApp->wp_user_id;
				$step->recipe_id = $recipe->id;
				$step->validate();
				if (!$step->save()) {
					App::abort(500, 'Steps was not saved');
				}
			}
		}

		//die();
/*
		//get files
		$files = array();
		foreach (Input::file('picture') as $key => $value) {
			$files[] = $value;
		}

		$recipe->saveImage(Input::all(), $files, $recipe->id);
*/
		return Response::json($recipe->toArray(), 201);	
	}

	public function p_edit($id){
		$myApp = App::make('myApp');
		$recipe = Recipe::findByOwnerAndId($myApp->wp_user_id, $id);

		$recipe->fill(Input::get());
		
		// cek apakah main_picture_id adalah milik sendiri atau tidak
		
		
		$recipe->validate($recipe->item_id);

		if (!$recipe->save()) {
			App::abort(500, 'Recipe was not updated');
		}

		return Response::json($recipe->toArray());
	}
/*	
	public function p_picture_add($id){
		$recipe = Recipe::findByOwnerAndId(Auth::user(), $id);

		if(!$recipe) {
			App::abort(500, 'Recipe is not found or incorrect logged user.');
		}

		//get files
		$files = array();
		foreach (Input::file('picture') as $key => $value) {
			$files[] = $value;
		}

		$recipe->saveImage(Input::all(), $files, $recipe->id);

		return Response::json($recipe->toArray(), 201);	

	}
	
	public function p_picture_delete($id){	
		$recipe = Recipe::findByOwnerAndId(Auth::user(), $id);
		if(!$recipe) {
			App::abort(500, 'Recipe is not found or incorrect logged user.');
		}
	}
*/

	public function p_search($keyword){
		$recipe = Recipe::findByKeyword($keyword);
		return Response::json($recipe->toArray());
	}

	public function p_search_all($keyword){
		$recipe = Recipe::findByKeywordAll($keyword);
		return Response::json($recipe->toArray());
	}

	public function p_delete($id){
		$myApp = App::make('myApp');
		$recipe = Recipe::findByOwnerAndId($myApp->wp_user_id, $id);
		$recipe->delete();
		return Response::make(null, 204);
	}

	public function p_list(){
		$myApp = App::make('myApp');
		$recipe = Recipe::findByOwner($myApp->wp_user_id);
		return Response::json($recipe->toArray());
	}
	
	public function p_count(){
		$myApp = App::make('myApp');
		$recipe = Recipe::findByOwner($myApp->wp_user_id);
		$count = $recipe->count();
		$ret['count'] = $count;
		return Response::json($ret);
	}

	public function p_list_user($id){
		$recipe = Recipe::findByOwner($id);
		return Response::json($recipe->toArray());	
	}

	public function p_list_all(){
		$recipe = Recipe::display_All();
		return Response::json($recipe->toArray());
	}

	public function p_like_status($id){
		return Response::json(Recipe::like_statistik($id));
	}

	public function p_like_list(){
		$count = Like::where('wp_user_id',Auth::user()->id)->count();
//		$count = $recipe->count();
		$ret['total_like'] = $count;
		return Response::json($ret);
	}

	public function p_like_count($id){
		$count = Like::where('wp_user_id',$id)->count();
//		$count = $recipe->count();
		$ret['total_like'] = $count;
		return Response::json($ret);
	}

	
	public function p_like_list_all(){
		$like = Like::get();
		$ret = array();
		foreach ($like as $value) {
			$recipe = Recipe::find($value->recipe_id)->first();
			$ret[] = $recipe;
		}
		return Response::json($ret);
	}

	public function p_like_list_user($id){
		$like = Like::where('wp_user_id',$id)->get();
		$ret = array();
		foreach ($like as $value) {
			$recipe = Recipe::find($value->recipe_id)->first();
			$ret[] = $recipe;
		}
		return Response::json($ret);
	}

	public function p_like(){
		//check is exist
		$like_count = Like::where('wp_user_id',Auth::user()->id)
		->where('recipe_id', Input::get('recipe_id'))->count();
		if($like_count>0) {
			App::abort(400, 'Like was not saved, recipe already liked/dislike');
		}

		$like = new Like(Input::get());
		$like->wp_user_id = Auth::user()->id;
		$like->status='L';
		$like->validate();
		if (!$like->save())
		{
			App::abort(400, 'Like was not saved');
		}
		return Response::json($like->toArray(), 201);
	}

	public function p_dislike(){
		//check is exist
		$like_count = Like::where('wp_user_id',Auth::user()->id)
		->where('recipe_id', Input::get('recipe_id'))->count();
		if($like_count>0) {
			App::abort(400, 'Dislike was not saved, recipe already liked/dislike');
		}

		$like = new Like(Input::get());
		$like->wp_user_id = Auth::user()->id;
		$like->status='D';
		$like->validate();
		if (!$like->save())
		{
			App::abort(400, 'Dislike was not saved');
		}
		return Response::json($like->toArray(), 201);
	}
	
	public function p_unlike(){
		//check is exist
		$like_count = Like::where('wp_user_id',Auth::user()->id)
		->where('recipe_id', Input::get('recipe_id'))->count();
		if($like_count>0) {
			$like = Like::where('wp_user_id',Auth::user()->id)
			->where('recipe_id', Input::get('recipe_id'))
			->where('status', 'L')
			->first();
			$like->delete();
			return Response::make(null, 204);
		} else {
			App::abort(400, 'Unlike was not saved, recipe is not unliked');
		}
	}

	public function p_undislike(){
		//check is exist
		$like_count = Like::where('wp_user_id',Auth::user()->id)
		->where('recipe_id', Input::get('recipe_id'))->count();
		if($like_count>0) {
			$like = Like::where('wp_user_id',Auth::user()->id)
			->where('recipe_id', Input::get('recipe_id'))
			->where('status', 'D')
			->first();
			$like->delete();
			return Response::make(null, 204);
		} else {
			App::abort(400, 'Undislike was not saved, recipe is not undisliked');
		}
	}
	
	
	public function p_admin_truncate(){
		Recipe::admin_truncate();
	}
	
}
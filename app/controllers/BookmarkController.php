<?php

class BookmarkController extends BaseController {
	
	public function p_add(){
	/**
	  * name ; The name of the bookmark.
	  * description ; The description of the bookmark.
	  **/	
		$bookmark = new Bookmark(Input::get());
		$bookmark->wp_user_id = Auth::user()->id;
		
		// check exist
		$bookmark_count = Bookmark::where('wp_user_id',Auth::user()->id)
		->where('recipe_id', Input::get('recipe_id'))->count();
		if($bookmark_count>0) {
			App::abort(400, 'Bookmark was not saved, recipe already bookmarked');
		}
		
		$bookmark->validate();

		if (!$bookmark->save())
		{
			App::abort(400, 'Bookmark was not saved');
		}
		
		return Response::json($bookmark->toArray(), 201);	
	}

	public function p_delete($id){
		$bookmark = Bookmark::findByOwnerAndId(Auth::user(), $id);
		$bookmark->delete();
		return Response::make(null, 204);
	}
	
	public function p_list(){
		$bookmark = Bookmark::findByOwner(Auth::user()->id);
		return Response::json($bookmark->toArray());
	}

	public function p_count(){
		$myApp = App::make('myApp');
		$bookmark = Bookmark::findByOwner($myApp->wp_user_id);
		$count = $bookmark->count();
		$ret['count'] = $count;
		return Response::json($ret);
	}
	
	
	public function p_list_user($id){
		$bookmark = Bookmark::findByOwner($id);
		return Response::json($bookmark->toArray());	
	}

	public function p_list_all(){
		$bookmark = Bookmark::display_All();
		return Response::json($bookmark->toArray());
	}

	public function p_admin_truncate(){
		Bookmark::admin_truncate();
	}

	
}
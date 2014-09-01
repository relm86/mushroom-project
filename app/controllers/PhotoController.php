<?php

class photoController extends BaseController {
	
	public function p_add(){
		if(Input::file('picture')!='') {
			// init global App Config
			$myApp = App::make('myApp');

			$file = Input::file('picture');
			$destinationPath = 'uploads/';
			$filename = str_random(8) . '-' . $file->getClientOriginalName();
			$file->move($destinationPath, $filename);
			$size = getimagesize($destinationPath.$filename);
			if(trim(Input::get('title'))=='') {
				$title = $filename;
			} else {
				$title = Input::get('title');
			}
			if(trim(Input::get('description'))=='') {
				$description = '';
			} else {
				$description = Input::get('description');
			}

			$photo = New Photo(
				array(
				'wp_user_id'	=> $myApp->wp_user_id,
				'title'			=> $title,
				'description'	=> $description,
				'imgPath'		=> $destinationPath.$filename,
				'imgWidth'		=> $size[0],
				'imgHeight'		=> $size[1]
			));
//			$photo->save();
			$photo->validate();
	/*
			$photo = new Photo(Input::get());
			$photo->wp_user_id = $myApp->wp_user_id;
			$photo->validate();
	*/
			if (!$photo->save()) {
				App::abort(500, 'Picture was not saved');
			}
			
			return Response::json($photo->toArray(), 201);	

		} else {
			App::abort(500, 'Picture variable is empty');
		}
	}

	public function p_edit($id){
		$myApp = App::make('myApp');
		if(Input::file('picture')!=''){
			App::abort(500, 'Picture file variable should not be passed to this API call.');	
		} else {
			$photo = Photo::findByOwnerAndId($myApp->wp_user_id, $id);
			$photo->fill(Input::get());
			$photo->validate($photo->item_id);
			if (!$photo->save()) {
				App::abort(500, 'Picture was not updated');
			}
			return Response::json($photo->toArray());
		}
	}

	public function p_delete($id){
		$myApp = App::make('myApp');
		$photo = Photo::findByOwnerAndId($myApp->wp_user_id, $id);
		$photo->delete();
		return Response::make(null, 204);
	}

	public function p_list(){
		$myApp = App::make('myApp');
		$photo = Photo::findByOwner($myApp->wp_user_id);
		return Response::json($photo->toArray());	
	}
	
	public function p_list_all(){
		$photos = Photo::display_All();
		return Response::json($photos->toArray());
	}

	public function p_admin_truncate(){
		Photo::admin_truncate();
	}

/*	
	public function p_search($keyword){
		$photo = Photo::findByKeyword($keyword);
		return Response::json($photo->toArray());
	}

	public function p_delete($id){
		// init global App Config
		$myApp = App::make('myApp');

		$photo = Photo::findByOwnerAndId($myApp->wp_user_id, $id);
		$photo->delete();
		return Response::make(null, 204);
	}
	
	public function p_user($id){
		$photo = Photo::findByOwner($id);
		return Response::json($photo->toArray());	
	}

	public function p_detail($id){
		$photo = Photo::findById($id);
		return Response::json($photo->toArray());
	}
*/

}
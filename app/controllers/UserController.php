<?php

class UserController extends BaseController {

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		

		$data =  Input::all();
		$validator = Validator::make($data, 
			array('name' => 'required'));
		if ($validator->fails())
		{
		    return Response::json(array(
	        	'message'=> 'Input validation failed'),400
			);
		}

		if( User::createUser(Input::get('name')) ){
			return Response::json(array(
		        'message'=>'User saved'),200
			);
		} else {
			return Response::json(array(
		        'message'=>'User already exists.'),400
			);
		}
	}


	public function get(){
		$users = User::all();

		$result = array();
		foreach($users as $user){
			array_push($result, array('name' => $user->name, 'avatar' => $user->getAvatar()));
		}

		return Response::json(array(
		        'message'=>$result),200
		);
	}

}
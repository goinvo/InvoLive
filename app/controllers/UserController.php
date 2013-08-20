<?php

class UserController extends BaseController {

	/*
	*	Get all users in the system
	*/
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

	/*
	*	Stores services OAuth tokens for users
	*/
	public function authorize(){
		$data =  Input::all();
		$validator = Validator::make($data, 
			array(
				'liveid' => 'required|exists:users,id',
				'service' => 'required|exists:sources,name'
				));
		if ($validator->fails())
		{
			echo 'Authorization request error.';
			return;
		}

		$service = Input::get('service');
		$user = User::find(Input::get('liveid'));

		return $user->authorize($service);
	}


}
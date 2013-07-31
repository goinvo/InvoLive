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
			echo 'Request issue';
		} else {
			$service = Input::get('service');
			$liveid = Input::get('liveid');
			$success = false;

			if($service == 'fitbit'){

				/*
				*	Fitbit OAuth authorization subroutines
				*/
				
				$key = Config::get('live.fitbit-key');
			    $secret = Config::get('live.fitbit-secret');

			    // get tokens
			    $fitbit = new FitBitPHP($key, $secret);
			    $fitbit->initSession('http://live.dev/user/authorize?&service=fitbit&liveid='.$liveid);

			    // save tokens
			    if($fitbit->sessionStatus() == 2){
			    	$user = User::find($liveid);
					$user->fitbitToken = $fitbit->getOAuthToken();
					$user->fitbitSecret = $fitbit->getOAuthSecret();
					$user->save();
					$success = true;
				}

				// return View::make('fitbitAuth',  array('liveid' => Input::get('liveid')));
			} else if( $service == 'withings' ) {
				
				/*
				*	Withings OAuth authorization subroutines
				*/

				$key = Config::get('live.withings-key');
				$secret = Config::get('live.withings-secret');

				// get tokens
				$withings = new WithingsPHP($key, $secret);
				$withings->initSession('http://live.dev/user/authorize?&service=withings&liveid='.$liveid);

				// save tokens
				if($withings->sessionStatus() == 2){
					$user = User::find($liveid);
					$user->withingsToken = $withings->getOAuthToken();
					$user->withingsSecret = $withings->getOAuthSecret();
					
					// withings callback returns withings id under param 'userid'
					$user->withingsId = $liveid = Input::get('userid');
					
					$user->save();
					$success = true;
				}

			} else {

			}

			if ($success) {
				$msg =  ucfirst($service).' is now authorized for '.$user->name.'.';
				return View::make('msg', array('msg' => $msg));
			}
		}
	}


}
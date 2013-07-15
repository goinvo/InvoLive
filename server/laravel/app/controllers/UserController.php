<?php

class UserController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

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

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//res
	}

	public function get(){
		$events = DB::table('users')->lists('name');

		return Response::json(array(
		        'message'=>$events),200
		);
	}

	public function getimage(){
		$id = User::getId(Input::get('user'));
		if($id == null) {
			return Response::json(array(
			        'message'=>'User not found.'),400
			);
		} else {
			return Response::download(User::find($id)->getImage(), 'pic.jpg');
		}
	}



}
<?php

class EventtypeController extends BaseController {

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
		$eventtype = new Eventtype;

		$data =  Input::all();
		$validator = Validator::make($data, 
			array('name' => 'required'));
		if ($validator->fails())
		{
		    return Response::json(array(
	        	'message'=> 'Input validation failed'),400
			);
		}

		if(Eventtype::createEvent(Input::get('name')) == True){
			return Response::json(array(
		        'message'=>'Event type saved'),200
			);
		} else {
			return Response::json(array(
		        'message'=>'Event type already exists.'),400
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
		//
	}

}
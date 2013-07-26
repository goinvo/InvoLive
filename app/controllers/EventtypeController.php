<?php

class EventtypeController extends BaseController {

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

	public function get(){
		$events = array();

		// groups
		foreach(array_keys(Eventtype::$groups) as $group){
			array_push($events, array('name' => ucfirst($group)));
		}
		// single events
		foreach(Eventtype::all() as $event){
			array_push($events, array('name' => ucfirst($event->name)));
		}

		return Response::json(array(
		        'message'=>$events),200
		);

	}

}
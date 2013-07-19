<?php

class MeasurementController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$measurements = Measurement::all();
		var_dump($measurements);
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

	private function validationFailure($msg){
		return Response::json(array(
	        'message'=> $msg),400
		);
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
			array('eventtype' => 'required', 'user' => 'required', 'source' => 'required', 'value' => 'required|numeric'));
		if ($validator->fails())
		{
		    return $this->validationFailure('Input validation failed');
		}

		$result = Measurement::createMeasurement(Input::get('user'), Input::get('eventtype'), Input::get('source'), Input::get('value'), Input::get('value'));
		
		if($result['success']) {
			return Response::json(array(
		        'message'=>'Measurement saved'),200
			);
		} else {
			return Response::json(array(
		        'message'=>$result['message']),400
			);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function get()
	{
		$data =  Input::all();

		$event = Input::get('eventtype');
		$user = Input::get('user');
		$source = Input::get('source');
		$time = Input::get('time');
		$resolution = Input::get('resolution');
		$attributes = Input::get('attributes');

		$query = Measurement::getMeasurement($user, $event, $source);

		// limits time scope of query
		if($time != null) {
			$query = TimeQuery::interval($query, TimeQuery::stringToDate($time));
		}

		// does aggregation if needed
		if($resolution != null) {
			$query = TimeQuery::aggregate($query, TimeQuery::aggregateBy($resolution));
			Measurement::aggregateMethod($query, 'COUNT');
		}

		$results = $query->get();
		$payload = array();

		foreach($results as $result){
			$entry = array(
				'user'=>$result->user->name,
				'eventtype'=>$result->eventtype->name,
				'value'=>$result->value,
				'timestamp'=>$result->timestamp);

			if($attributes == 'all') {
				$attrs = $result->getAllAttributes;
				foreach($attrs as $attr){
					$entry[$attr->name()] = $attr->value;
				}
			// 
			} else if($attributes != null){
				$attr = $result->getSingleAttribute($attributes);
				if($attr != null) {
					$entry[$attr->name()] = $attr->value;
				}
			} else {

			}

			array_push($payload,  $entry);
		}

		return Response::json(array(
	        'message'=>$payload),200
		);
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
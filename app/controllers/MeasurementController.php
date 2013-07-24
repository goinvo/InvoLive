<?php

class MeasurementController extends BaseController {

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
	 * @return Response
	 */
	public function get(){
		$event = Input::get('eventtype');
		$user = Input::get('user');
		$source = Input::get('source');
		$time = Input::get('time');
		$resolution = Input::get('resolution');
		$attributes = Input::get('attributes');

		// query results
		$payload = array();


		// There are two scenarios
		// 1 Specific event type 
		// 2 Group of events
		//
		// Results for groups of events are built by issuing multiple
		// queries for each event.
		// This is done because each query should always be about
		// a single event because a single event may have attributes, filters
		// or aggreation methods only valid for itself and not other events.

		// Group of events
		$event = strtolower($event);
		if(array_key_exists($event, Eventtype::$groups)){
			foreach (Eventtype::$groups[$event] as $event) {
				$payload = array_merge($payload, $this->getEventMeasurements($user, $event, $source, $time, $resolution, $attributes));
			}
		// Specific event
		} else {
			$payload = array_merge($payload, $this->getEventMeasurements($user, $event, $source, $time, $resolution, $attributes));
		}

		return Response::json(array(
	        'message'=>$payload),200
		);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEventMeasurements($user, $event, $source, $time, $resolution, $attributes)
	{

		// query model
		$query = Measurement::getMeasurement(
			$user,
			$event, 
			$source, 
			$time, 
			$resolution
		);
		
		$results = $query->get();

		// prepare payload array
		$payload = array();

		foreach($results as $result){
			// save query results
			$entry = array(
				'value'=>$result->value,
				'timestamp'=> timestamp,
				'eventtype'=>$result->eventtype->name
			);
			
			// do not list the source name if user already specified it
			if($source == null){
				$entry['source'] = $result->source->name;
			}
			if($user == null){
				$entry['user'] = $result->user->name;
			}

			// show attributes to the specific entry
			// eg. Dropbox file creation event has a filename attribute

			// do not show attributes if query has to be aggregated by day
			// we cannot aggregate filenames as of yet (may get solved later)

			// query asking for all events
			if($resolution == null and $attributes == 'all') {
				$attrs = $result->getAllAttributes;
				foreach($attrs as $attr){
					$entry[$attr->name()] = $attr->value;
				}
			// query is asking for a specific event
			} else if($resolution == null and $attributes != null){
				$attr = $result->getSingleAttribute($attributes);
				if($attr != null) {
					$entry[$attr->name()] = $attr->value;
				}
			} else {

			}
			array_push($payload,  $entry);
		}
		// return query results
		return $payload;
	}

}
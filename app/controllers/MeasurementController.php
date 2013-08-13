<?php

class MeasurementController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @return Response
	 */
	public function get(){
		$events = Input::get('eventtype');
		$users = Input::get('user');
		$source = Input::get('source');
		$start = Input::get('startdate');
		$end = Input::get('enddate');
		$time = Input::get('time');
		$resolution = Input::get('resolution');
		$attributes = Input::get('attributes');

		// query results
		$payload = array();

		// Results for groups of events are built by issuing multiple
		// queries for each event.
		// This is done because each query should always be about
		// a single event because a single event may have attributes, filters
		// or aggreation methods only valid for itself and not other events.

		if(gettype($events) != 'array') $events = array($events);
		if(gettype($users) != 'array') $users = array($users);

		foreach($users as $user){
			foreach ($events as $event) {
				array_push($payload, $this->getEventMeasurements($user, $event, $source, $start, $end, $resolution, $attributes));
			}
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
	public function getEventMeasurements($user, $event, $source, $start, $end, $resolution, $attributes)
	{

		// query model
		$query = Measurement::getMeasurement(
			$user,
			$event, 
			$source, 
			$start, 
			$end,
			$resolution
		);
		
		$results = $query->get();

		// prepare payload array
		$payload = array();
		$values = array();

		// high level info on payload
		if($source != null)
			$payload['source'] = $source;
		if($user != null)
			$payload['user'] = $user;

		$payload['eventtype'] = $event;

		// payload datapoints
		foreach($results as $result){
			// save query results
			$entry = array(
				'value'=>$result->value,
				'timestamp'=> $result->timestamp
			);
			
			// do not list the source name if user already specified it
			if($source == null)
				$entry['source'] = $result->source->name;
			if($user == null)
				$entry['user'] = $result->user->name;

			// show attributes to the specific entry
			// eg. Dropbox file creation event has a filename attribute

			// do not show attributes if query has to be aggregated by day
			// we cannot aggregate filenames as of yet (may get solved later)

			// query asking for all events
			if($resolution == null and $attributes == 'all') {
				$attrs = $result->attributes;
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
			array_push($values,  $entry);
		}
		// return query results
		$payload['data'] = $values; 
		return $payload;
	}

}
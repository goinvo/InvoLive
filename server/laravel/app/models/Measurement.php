<?php
class Measurement extends Eloquent
{
	public $timestamps = false;

	public static function createMeasurement($user, $eventtype, $source, $value, $timestamp){

		// check for valid user
		$user_id = User::getId($user);
		if ($user_id == null) return array('success' => False, 'message' => 'User '.$user.' not found.');

		// check for valid event type
		$event_id = Eventtype::getId($eventtype);
		if($event_id == null) return array('success' => False, 'message' => 'Event type '.$eventtype.' not found.');

		// check for valid source
		$source_id = Source::getId($source);
		if($source_id == null) return array('success' => False, 'message' => 'Source '.$source.' not found.');
		
		// check for duplicates
		$dup_query = Measurement::where('user_id', $user_id)
			->where('eventtype_id', $event_id)
			->where('source_id', $source_id)
			->where('value', $value)
			->where('timestamp',$timestamp->format('Y-m-d H:i:s'));
		if( $dup_query->first() != null){
			return array('success' => False, 'message' => 'Duplicate.');
		}

		// create new measurement and save
		$measurement = new Measurement;
		$measurement->user_id = $user_id;
		$measurement->eventtype_id = $event_id;
		$measurement->source_id = $source_id;
		$measurement->value = $value;
		$measurement->timestamp = $timestamp;

		$measurement->save();

		return array('success' => True, 'measurement' => $measurement);
	}

	public static function aggregateMethod($query, $method){
		return $query->select(DB::raw('user_id, eventtype_id, source_id, '.$method.'(value) as value, timestamp'));
	}

	public static function getMeasurement($user = null, $event = null, $source = null){
		
		$query = Measurement::query();

		if($event != null && $event != 'all') $query->where('eventtype_id', Eventtype::getId($event));
		if($user != null) $query->where('user_id', User::getId($user));
		if($source != null) $query->where('source_id', Source::getId($source));

		return $query;

	}

	public function addAttribute($attribute, $value){
		// check valid attribute
		$attr_id = Attribute::getId($attribute);
		if($attr_id == null) return array('success' => False, 'message' => 'Attribute '.$attribute.' not found.');
		
		$newAttribute = new measurementAttribute;
		$newAttribute->meausurement_id = $this->id;
		$newAttribute->attribute_id = $attr_id;
		$newAttribute->value = $value;

		$newAttribute->save();
	}

	// public function getAttribute($attribute){

	// }

	public function getSingleAttribute($attribute){
		$attr_id = Attribute::getId($attribute);
		if($attr_id == null) return null;
		
		return MeasurementAttribute::where('attribute_id', $attr_id)->where('measurement_id', $this->id)->first();
	}

	public function getAllAttributes(){
		return $this->hasMany('MeasurementAttribute');
	}

	public function user()
	{
	 	return $this->belongsTo('User');
	}

	public function eventtype()
	{
	 	return $this->belongsTo('Eventtype');
	}


}
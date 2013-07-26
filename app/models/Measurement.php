<?php
class Measurement extends Eloquent
{
	public $timestamps = false;

	public static function createMeasurement($user, $eventtype, $source, $value, $timestamp, $overwriteOldEntries = True){

		// check for valid user
		$user_id = User::getId($user);
		if ($user_id == null) return array('success' => False, 'message' => 'User '.$user.' not found.');

		// check for valid event type
		$event_id = Eventtype::getId($eventtype);
		if($event_id == null) return array('success' => False, 'message' => 'Event type '.$eventtype.' not found.');

		// check for valid source
		$source_id = Source::getId($source);
		if($source_id == null) return array('success' => False, 'message' => 'Source '.$source.' not found.');
		
		// check for old entries and overwrite them if needed
		if ($overwriteOldEntries) {
			$old_entry = Measurement::where('user_id', $user_id)
				->where('eventtype_id', $event_id)
				->where('source_id', $source_id)
				->where('timestamp',$timestamp->format('Y-m-d H:i:s'));
			if( $old_entry->first() != null){
				$measurement = $old_entry->first();
				$measurement->value = $value;
				return array('success' => True, 'measurement' => $measurement);
			}
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

	public static function getMeasurement($user = null, $event = null, $source = null, $time = null, $resolution = null){
		

		$query = Measurement::query();

		// handles grouping by day/month... grouping by users... etc
		$grouping = new GroupQuery;

		$query->where('eventtype_id', Eventtype::getId($event));
		if($user != null) $query->where('user_id', User::getId($user));
		if($source != null) $query->where('source_id', Source::getId($source));

		// limits time scope of query if needed
		if($time != null) {
			$query = TimeQuery::interval($query, TimeQuery::stringToDate($time), TimeQuery::stringToDate('now'));
		}

		// does daily aggregation if needed
		if($resolution != null) {
			$grouping->byTime($resolution);
			// do not forget to also group by user if grouping by time
			$grouping->byUser();

			// each event needs to be aggregated in some way
			// some use averages (eg. Temperature) some use sum (eg. Files created)
			// these lines figure out what type of aggregation to use
			if( Eventtype::getId($event) != null ) {
				$aggregationType = Eventtype::find(Eventtype::getId($event))->first()->aggregation;
				GroupQuery::using($query, $aggregationType);
			}
		}

		// add grouping string to query
		$grouping->apply($query);

		return $query;

	}

	public function addAttribute($attribute, $value){
		// check valid attribute
		$attr_id = Attribute::getId($attribute);
		if($attr_id == null) return array('success' => False, 'message' => 'Attribute '.$attribute.' not found.');
		
		$newAttribute = new measurementAttribute;
		$newAttribute->measurement_id = $this->id;
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

	public function source()
	{
	 	return $this->belongsTo('Source');
	}

	public function eventtype()
	{
	 	return $this->belongsTo('Eventtype');
	}


}
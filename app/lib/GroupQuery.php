<?php

class GroupQuery {

	private $groupString = null;

	private function addGrouping($group){
		if($this->groupString == null) {
			$this->groupString = $group;
		} else {
			$this->groupString = $this->groupString.', '.$group;
		}
	}

	public function byTime($delta){
		if($delta == 'hour') $this->addGrouping('YEAR(timestamp), MONTH(timestamp), DAY(timestamp), HOUR(timestamp)');
		if($delta == 'day') $this->addGrouping('YEAR(timestamp), MONTH(timestamp), DAY(timestamp)');
		return $this;
	}

	public function byUser(){
		$this->addGrouping('user_id');
	}

	public function byEvent(){
		$this->addGrouping('eventtype_id');
	}

	public static function using($query, $method){
		return $query->select(DB::raw('user_id, eventtype_id, source_id, '.$method.'(value) as value, timestamp'));
	}

	public function apply($query){
		if($this->groupString != null) {
			$query->groupBy(DB::raw($this->groupString));
		}
	}

}
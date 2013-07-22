<?php

class GroupQuery {

	private $groupString = null;

	public static function stringToDate($start){
		if($start == 'now') return \Carbon\Carbon::now();
		if($start == 'lasthour') return \Carbon\Carbon::now()->subHour();
		if($start == 'lastday') return \Carbon\Carbon::now()->subDay();
		if($start == 'lastweek') return \Carbon\Carbon::now()->subWeek();
		if($start == 'lastmonth') return \Carbon\Carbon::now()->subMonth();
		if($start == 'lastyear') return \Carbon\Carbon::now()->subYear();
		if($start == 'alltime') return DateTime::createFromFormat('U',0);
		return \Carbon\Carbon::now()->subHour();
	}

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

	public static function interval($query, $start, $end=null) {
		$query->where('timestamp', '>=', $start);
		if($end != null) $query->where('timestamp', '<=', $end);
		return $query;
	}

	public function apply($query){
		if($this->groupString != null) {
			$query->groupBy(DB::raw($this->groupString));
		}
	}

}
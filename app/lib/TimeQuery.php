<?php

class TimeQuery {

	private $startDate = null;
	private $endDate = null;

	/*
	*	Converts string to DateTime object
	*/
	public static function stringToDate($date){

		if($date == 'now') return \Carbon\Carbon::now();
		if($date == 'lasthour') return \Carbon\Carbon::now()->subHour();
		if($date == 'yesterday') return \Carbon\Carbon::now()->subDay();
		if($date == 'lastweek') return \Carbon\Carbon::now()->subWeek();
		if($date == 'lastmonth') return \Carbon\Carbon::now()->subMonth();
		if($date == 'lastyear') return \Carbon\Carbon::now()->subYear();
		if($date == 'alltime') return DateTime::createFromFormat('U',0);

		// date string
		$newDate = DateTime::createFromFormat('Y-m-d', $date);
		if($newDate !== false) return $newDate->setTime(0,0);

		// fall back to current time otherwise
		return \Carbon\Carbon::now();
	}

	public function start($date){
		if(gettype($date) == 'string') $date = TimeQuery::stringToDate($date);
		$this->startDate = $date;
	}

	public function end($date){
		if(gettype($date) == 'string') $date = TimeQuery::stringToDate($date);
		$this->endDate = $date;
	}

	public function apply($query){
		if($this->startDate !== null) {
			$query->where('timestamp', '>=', $this->startDate);
		}
		if($this->endDate !== null) {
			$query->where('timestamp', '<=', $this->endDate);
		}
		return $query;
	}

}
<?php

class TimeQuery {

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

	public static function interval($query, $start, $end=null) {
		$query->where('timestamp', '>=', $start);
		if($end != null) $query->where('timestamp', '<=', $end);
		return $query;
	}

}
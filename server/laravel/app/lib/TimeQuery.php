<?php

class TimeQuery {

	public static function stringToDate($start){
		if($start == 'now') return \Carbon\Carbon::now();
		if($start == 'lasthour') return \Carbon\Carbon::now()->subHour();
		if($start == 'lastday') return \Carbon\Carbon::now()->subDay();
		if($start == 'lastweek') return \Carbon\Carbon::now()->subWeek();
		if($start == 'lastmonth') return \Carbon\Carbon::now()->subMonth();
		if($start == 'lastyear') return \Carbon\Carbon::now()->subYear();
		if($start == 'alltime') return new DateTime(1970,1,1,0,0,0,0);
		return null;
	}

	public static function interval($query, $start, $end=null) {
		$query->where('timestamp', '>=', $start);
		if($end != null) $query->where('timestamp', '<=', $end);
		return $query;
	}

	public static function aggregateBy($delta){
		if($delta == 'hour') return 'YEAR(datefield), MONTH(datefield), DAY(datefield), HOUR(datefield)';
		if($delta == 'day') return 'YEAR(datefield), MONTH(datefield), DAY(datefield)';
		return null;
	}

	public static function aggregate($query, $delta, $mode){
		return $query->groupBy($delta);
	}

}
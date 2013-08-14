<?php

class Score {
	/*
	*	Metrics
	*/
	private $metrics = array(
		array(
			"name" => "productivity",
			"submetrics"  => array(
				array(
					"name" => "Dropbox",
					"events" => array("Files created", "Files deleted", "Files moved", "Files renamed"),
					"weight" => 10
				),
				array(
					"name" => "Workhours",
					"events" => "Actual Work Hours",
					"weight" => 10
				)
			)
		),
		array(
			"name" => "happiness",
			"submetrics"  => array(
				array(
					"name" => "Workhours",
					"events" => "Actual Work Hours",
					"weight" => 10
				)
			)
		),
		array(
			"name" => "health",
			"submetrics" => array(
				array(
					"name" => "Steps",
					"events" => "steps",
					"weight" => 10
				)
			)
		)
	);

	private function sum($datapoints){
		$value = 0;
		foreach ($datapoints as $datapoint) {
			$value += $datapoint->value;
		}
		return $value;
	}

	/*
	*	Scoring functions
	*/

	private function scoreDropbox($datapoints){
		var_dump($this->sum($datapoints));
		return 50;
	}

	private function scoreWorkhours($datapoints){
		return 70;
	}

	private function scoreSteps($datapoints){
		return 60;
	}

	/*
	*	Get datapoint
	*/
	private function getDatapoints($user, $events, $startdate, $enddate){

		if(gettype($events) != 'array') $events = array($events);
		$datapoints = array();

		// get datapoints for each event
		foreach ($events as $event) {
			$query = Measurement::getMeasurement(
				$user,
				$event, 
				null, 
				$startdate, 
				$enddate
			);

			$results = $query->get();
			foreach($results as $entry){
				array_push($datapoints, $entry);
			}
		}
		return $datapoints;
	}

	private function scoreSubmetric($user, $submetric, $startdate, $enddate) {
		// get datapoints
		$datapoints = $this->getDatapoints($user, $submetric["events"], $startdate, $enddate);
		$name = $submetric["name"];

		switch ($name) {
			case "Dropbox":
				return $this->scoreDropbox($datapoints);
			case "Workhours":
				return $this->scoreWorkhours($datapoints);
			case "Steps":
				return $this->scoreSteps($datapoints);
			default:
				return 50;
		}

	}

	private function scoreMetric($user, $metric, $startdate, $enddate) 
	{
		$score = 0;
		$submetrics = count($metric["submetrics"]);

		foreach ($metric["submetrics"] as $submetric){
			$score += $this->scoreSubmetric($user, $submetric, $startdate, $enddate);
		}
		return $score/$submetrics;
	}

	public function getUserScores($users, $startdate = 'lastmonth', $enddate = 'now', $combine = false){
		if(gettype($users) != 'array') $users = array($users);
		
		$scores = array();
		$userCount = count($users);

		// average scores
		if($combine) {
			foreach ($this->metrics as $metric) {
				$metricScore = 0;
				foreach ($users as $user) {
					$metricScore += $this->scoreMetric($user, $metric, $startdate, $enddate);
				}
				array_push($scores, 
					array("name" => $metric["name"], 
					"value" => $metricScore/$userCount)
				);
			}
		// do not average score
		} else {
			foreach ($users as $user) {
				$userscore = array();
				foreach ($this->metrics as $metric) {
					array_push($userscore, 
						array("name" => $metric["name"], 
							 "value" => $this->scoreMetric($user, $metric, $startdate, $enddate))
					);
				}
				array_push($scores, array("name" => $user, "scores" => $userscore));
			}
		}
		return $scores;
	}

	public function gd(){
		return $this->getUserScores(["Reshma", "Juhan"]);
	}


}
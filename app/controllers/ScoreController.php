<?php

class ScoreController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function get()
	{	
		$users = Input::get('user');
		$startDate = Input::get('startdate');
		$endDate = Input::get('enddate');

		$validator = Validator::make(
		    array('user' => $users, 'startdate' => $startDate, 'enddate' => $endDate),
		    array('user' => 'required', 'startdate' => 'date_format:Y-m-d', 'enddate' => 'date_format:Y-m-d')
		);
		if ($validator->fails())
		{
			return Response::json(array(
		        'message'=>'Bad score request.'),400
			);
		}

		$score = new Score();
		$payload = null;
		if($users == 'studio'){
			$payload = $score->getStudioScores($startDate, $endDate);
		} else {
			$payload = $score->getUserScores($users, $startDate, $endDate);
		}

		return Response::json(array(
	        'message'=>$payload),200
		);
	}


}
<?php

class ScoreController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function get()
	{	
		$score = new Score();
		var_dump($score->gd());
	}


}
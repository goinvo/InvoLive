<?php

class SourceController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function get(){
		$sources = Source::all();

		$result = array();
		foreach($sources as $source){
			array_push($result, $source->name);
		}

		return Response::json(array(
		        'message'=>$result),200
		);
	}

}
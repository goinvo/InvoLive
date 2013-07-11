<?php
class Source extends Eloquent
{
	public $timestamps = false;

	public static function getId($name)
    {
    	$result = Source::where('name', $name)->first();
    	if($result != null){
    		return $result->id;
    	} else {
    		return null;
    	}
    }
}
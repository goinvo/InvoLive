<?php
class Eventtype extends Eloquent
{
	public $timestamps = false;

	public static function getId($name)
    {
    	$result = Eventtype::where('name', $name)->first();
    	if($result != null){
    		return $result->id;
    	} else {
    		return null;
    	}
    }

    public function aggregateMethod(){
        return Eventtype::aggregationCodes($this->aggregation);
    }

}
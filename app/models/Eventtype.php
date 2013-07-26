<?php
class Eventtype extends Eloquent
{
	public $timestamps = false;

    // identify groups of events
    public static $groups = array(
        'dropbox actions' => 
        array(
            'Files created',
            'Files edited',
            'Files moved',
            'Files deleted'
    ));


	public static function getId($name)
    {
    	$result = Eventtype::where('name', $name)->first();
    	if($result != null){
    		return $result->id;
    	} else {
    		return null;
    	}
    }

    public static function createEvent($name){
        if( Eventtype::getId($name) != null) {
            return False;
        } else {
            $event = new Eventtype;
            $event->name = $name;
            $event->save();
            return True;
        }
    }

    public function aggregateMethod(){
        return Eventtype::aggregationCodes($this->aggregation);
    }

}
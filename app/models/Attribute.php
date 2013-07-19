<?php
class Attribute extends Eloquent
{
    public $timestamps = false;

    public static function getId($name)
    {
        $result = Attribute::where('name', $name)->first();
        if($result != null){
            return $result->id;
        } else {
            return null;
        }
    }   
}
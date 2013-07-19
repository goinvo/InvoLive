<?php
class MeasurementAttribute extends Eloquent
{
    public $timestamps = false;

    protected $table = 'measurement_attributes';

    public function name(){
        return Attribute::find($this->attribute_id)->name;
    }
}
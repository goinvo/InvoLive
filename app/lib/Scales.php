<?php

class Scale {

	private $type = "linear";

	private $domain = array(0, 100);
	private $range = array(0, 100);
	private $clamp = true;

	public function __construct($domain = null, $range = null) {
		if($domain != null) $this->domain = $domain;
		if($range != null) $this->range = $range;
	}

	public function setDomain($start, $end){
		$this->domain = array($start, $end);
	}

	public function setRange($start, $end){
		$this->range = array($start, $end);
	}

	public function setClamp($clamp){
		$this->clamp = $clamp;
	}

	public function convert($value){
		$scaled = 0;
		$d = $this->domain;
		$r = $this->range;

		// clamp
		if($this->clamp){
			$value = max($d[0], min($d[1], $value));
		}

		if($this->type == "linear"){
			$scaled = (($value - $d[0])*($r[1] - $r[0])/($d[1] - $d[0])) + $r[0];
		}

		return $scaled;
	}

}
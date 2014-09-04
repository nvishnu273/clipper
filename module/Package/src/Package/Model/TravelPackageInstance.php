<?php
namespace Package\Model;

use Package\Model\PackagePlan;

class TravelPackageInstance
{
	public $bookingId;
	public $packagecode;
	public $start;
	public $airport;
	public $assignedTo;
	public $checkInHotel;
	public $checkInHotelAddress;
	public $checkInTime;
	public $cost;
	public $destination;
	public $end;
	public $name;	
	public $nights;	
	public $status;
	public $packageplan;	
	
	public function __get($name) {
		return $this->internalData[$name];
	}
	public function __set($name, $value) {
		$this->internalData[$name] = $value;
	}
}
 
 ?>
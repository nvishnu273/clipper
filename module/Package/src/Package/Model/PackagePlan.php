<?php

namespace Package\Model;

class PackagePlan
{
	public $night;
	public $startlocation; /* address */
	public $checkout;
	public $endlocation;/* address */
	public $checkin;	
	public $visitingplaces;	/* array of address */
	/*
	public function exchangeArray($data)
	{
		$this->night     = (!empty($data['night'])) ? $data['night'] : null;
		$this->startlocation = (!empty($data['startlocation'])) ? $data['startlocation'] : null;
		$this->checkout  = (!empty($data['checkout'])) ? $data['checkout'] : null;
		$this->endlocation     = (!empty($data['endlocation'])) ? $data['endlocation'] : null;
		$this->checkin = (!empty($data['checkin'])) ? $data['checkin'] : null;
		$this->visitingplaces  = (!empty($data['visitingplaces'])) ? $data['visitingplaces'] : null;		
	}
	*/
}
?>

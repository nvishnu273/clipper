<?php

namespace Account\Model;

class Notification
{
	public $messageId;
	public $packageId;
	public $dateCreated;
    public $dateProcessed;
	public $processed;	
	public $messageType;	

	public function exchangeArray($data)
    {    	
        $this->messageId     = (!empty($data['MessageId'])) ? $data['MessageId'] : null;
        $this->packageId = (!empty($data['PacakgeId'])) ? $data['PacakgeId'] : null;
        $this->dateCreated  = (!empty($data['DateCreated'])) ? $data['DateCreated'] : null;
        $this->dateProcessed  = (!empty($data['DateProcessed'])) ? $data['DateProcessed'] : null;
        $this->processed     = (!empty($data['Processed'])) ? $data['Processed'] : 0;
        $this->messageType     = (!empty($data['MessageType'])) ? $data['MessageType'] : null;
    }
}
?>

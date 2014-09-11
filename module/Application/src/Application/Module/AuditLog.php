<?php

namespace Application\Model;

class AuditLog
{
	public $id;
	public $message;
	public $createdDate;

	public function exchangeArray($data)
    {    	
        $this->id     = (!empty($data['Id'])) ? $data['Id'] : null;
        $this->message = (!empty($data['Message'])) ? $data['Message'] : null;        
        $this->createdDate = (!empty($data['CreatedDate'])) ? $data['CreatedDate'] : null;
    }
}
?>

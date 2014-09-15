<?php

namespace Account\Model;

class Customer
{
	public $id;
	public $firstname;
	public $lastname;
	public $email;	
	public $paymentToken;	
	public $activated;

	public function exchangeArray($data)
    {    	
        $this->id     = (!empty($data['Id'])) ? $data['Id'] : null;
        $this->firstname = (!empty($data['FirstName'])) ? $data['FirstName'] : null;
        $this->lastname  = (!empty($data['LastName'])) ? $data['LastName'] : null;
        $this->email     = (!empty($data['Email'])) ? $data['Email'] : null;
        $this->activated     = (!empty($data['Activated'])) ? $data['Activated'] : null;
    }
}
?>

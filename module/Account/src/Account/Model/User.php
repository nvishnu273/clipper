<?php

namespace Account\Model;

class User
{
	public $id;
	public $firstname;
	public $lastname;
	public $userType;
	public $userName;
	public $role;
	public $permission;

	public function exchangeArray($data)
    {
        $this->id     = (!empty($data['Id'])) ? $data['Id'] : null;
        $this->firstname = (!empty($data['FirstName'])) ? $data['FirstName'] : null;
        $this->lastname  = (!empty($data['LastName'])) ? $data['LastName'] : null;
        $this->userType     = (!empty($data['Type'])) ? $data['Type'] : null;

        if (!isset($this->userType)){
			$this->role=array('customer');
		}
		elseif ($this->userType==1){
			$this->role=array('agent');
		}
		elseif ($this->userType==2){
			$this->role=array('manager');
		}	
    }
}
?>

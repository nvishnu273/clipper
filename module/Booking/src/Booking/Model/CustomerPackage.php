<?php
namespace Booking\Model;


class CustomerPackage
{
	public $Id;
	public $CustomerId;
	public $CustomerPackageId;
	public $StartDate;
	public $FirstName;
	public $LastName;
	public $Email;
	public $AssignedTo;
	public $Status;

	public function exchangeArray($data)
    {  
        $this->Id     = (!empty($data['Id'])) ? $data['Id'] : null;
        $this->CustomerId     = (!empty($data['CustomerId'])) ? $data['CustomerId'] : null;
        $this->CustomerPackageId = (!empty($data['CustomerPackageId'])) ? $data['CustomerPackageId'] : null;
        $this->StartDate = (!empty($data['StartDate'])) ? $data['StartDate'] : null;
        $this->FirstName = (!empty($data['FirstName'])) ? $data['FirstName'] : null;
        $this->LastName = (!empty($data['LastName'])) ? $data['LastName'] : null;                     
        $this->Email     = (!empty($data['Email'])) ? $data['Email'] : null;               
        $this->AssignedTo     = (!empty($data['AssignedTo'])) ? $data['AssignedTo'] : null;
        $this->Status  = (!empty($data['Status'])) ? $data['Status'] : null;      
    }
    
}
 
 ?>
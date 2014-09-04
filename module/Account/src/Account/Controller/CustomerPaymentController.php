<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Account\Form\Customer;
use Account\Model\CustomerTable;
use Zend\View\Model\JsonModel;

class CustomerPaymentController extends AbstractRestfulController
{

	protected $customerPaymentTable;
	
	// :customer/payment/1
	public function get($id)
	{	
		/*
		$travel_db_conn=new travel_db_mysqli($traveldb_hostname,$root_username,$root_password,$traveldb_database);
		
		$getCustomerQuery="select PaymentToken from TravelAppUser.Customer where ID=$id";
		$customer_result=$travel_db_conn->query($getCustomerQuery);	
		$row=$customer_result->fetch_row();	
		
		return $row[0];
		*/
	}


	public function create($data)
	{		
		/*
		$travel_db_conn=new travel_db_mysqli($traveldb_hostname,$root_username,$root_password,$traveldb_database);		
		$updatePaymentTokenQuery="update TravelAppUser.Customer set PaymentToken='$token' where Id=$customerID";
		$updatePaymentTokenQuery_result=$travel_db_conn->query($updatePaymentTokenQuery);		
		$affectedRows=$travel_db_conn->affected_rows;
		$travel_db_conn->close();	
		return $affectedRows!=-1;
		*/
	}


	/* returns Customer table from service locator */
	public function getCustomerPaymentTable()
	{
		if (!$this->customerPaymentTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerPaymentTable = $sm->get('Account\Model\CustomerPaymentTable');	        
	    }
	    return $this->customerPaymentTable;
	}
}


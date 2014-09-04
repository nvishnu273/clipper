<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;


use Account\Model\Customer;
use Account\Model\CustomerTable;
use Zend\View\Model\JsonModel;

class CustomerController extends AbstractRestfulController
{

	protected $customerTable;
	
	public function getList()
	{		
		return new JsonModel(array(
	        'Customers' => $this->getCustomerTable()->fetchAll(),	        
	    ));
	}	

	public function get($id)
	{				
		
		return new JsonModel(array(
	        'Customer' => $this->getCustomerTable()->fetch($id),	        
	    ));	
	}


	public function create($data)
	{		
		$newCustomer=new Customer();
		$newCustomer->firstname=$data['firstname'];
		$newCustomer->lastname=$data['lastname'];
		$newCustomer->email=$data['email'];
		$password  =  $data['password'];
				
		return new JsonModel(array('User' => $this->getCustomerTable()->saveCustomer($newCustomer,$password)));	
	}

	public function update($id, $data)
	{
		$newCustomer=new Customer();
		$newCustomer->id=$id;
		$newCustomer->firstname=$data['firstname'];
		$newCustomer->lastname=$data['lastname'];
		$newCustomer->email=$data['email'];
		$password  = (!empty($data['password'])) ? $data['password'] : null;
		$this->getCustomerTable()->saveCustomer($newCustomer,$password);		
		return new JsonModel(array(
	        'Account' => $data,	        
	    ));	
	}

	public function delete($id)
	{
		return new JsonModel(array(
			'deleted' => $this->getCustomerTable()->deleteCustomer($id)
		));
	}

	
	/* returns Customer table from service locator */
	public function getCustomerTable()
	{
		if (!$this->customerTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerTable = $sm->get('Account\Model\CustomerTable');	        
	    }
	    return $this->customerTable;
	}
}


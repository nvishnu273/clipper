<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Account\Model\Notification;
use Account\Model\Customer;
use Account\Model\CustomerTable;
use Zend\View\Model\JsonModel;

class CustomerController extends AbstractRestfulController
{

	protected $customerTable;
	protected $customerNotificationTable;
	protected $snsClient;

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

		$user = $this->getCustomerTable()->fetchByEmail($data['email']);

		if ($user) {
			return new JsonModel(array('message' => 'User exists'));
		}

		$newCustomer=new Customer();
		$newCustomer->firstname=$data['firstname'];
		$newCustomer->lastname=$data['lastname'];
		$newCustomer->email=$data['email'];
		$password  =  $data['password'];
		$id = $this->getCustomerTable()->saveCustomer($newCustomer,$password);

		if ($id > 0) {
			$arn=$this->getServiceLocator()->get('config')['sns_config']['internal_topic_arn'];
			$result = $this->getSnsClient()->publish(array(
				'TopicArn' => $arn,
				'Message' => $id,
				'Subject' => "New user registration."
			));

			$newNotification=new Notification();
			$newNotification->messageId=$result['MessageId'];
			$newNotification->packageId=$id;
			$newNotification->dateCreated=date("Y-m-d H:i:s");
			$newNotification->processed=false;
			$newNotification->messageType='NewRegistration';
			
			$this->getCustomerNotificationTable()->createNotification($newNotification);	
		}
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

	public function getSnsClient()
	{
		if (!$this->snsClient) {
	        $sm = $this->getServiceLocator();	        
	        $this->snsClient = $sm->get('SnsClient');	        
	    }
	    return $this->snsClient;
	}

	public function getCustomerNotificationTable()
	{
		if (!$this->customerNotificationTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerNotificationTable = $sm->get('Account\Model\NotificationTable');	        
	    }
	    return $this->customerNotificationTable;
	}

}


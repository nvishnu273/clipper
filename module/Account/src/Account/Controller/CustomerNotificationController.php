<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Account\Model\Notification;
use Account\Model\NotificationTable;
use Zend\View\Model\JsonModel;

class CustomerNotificationController extends AbstractRestfulController
{

	protected $customerNotificationTable;
	protected $snsClient;
	
	public function get($id)
	{			
		$allGetValues = $this->params()->fromQuery();		

		if (empty($allGetValues['messageId'])) {					
			return new JsonModel($this->getCustomerNotificationTable()->fetchAll($id));		    
		}
		else {			
			return new JsonModel(array(
		        'Notification' => $this->getCustomerNotificationTable()->fetch($allGetValues['messageId']),	        
		    ));
		}		
	}


	public function create($data)
	{		
		if ($this->getRequest()->isPost()){
			$input = $this->getRequest()->getContent();
			$postedData=json_decode($input,true);
			$newNotification=new Notification();
			$newNotification->messageId=$postedData['messageId'];
			$newNotification->packageId=$postedData['packageId'];
			$newNotification->dateCreated=date("Y-m-d H:i:s");
			$newNotification->processed=$postedData['processed'];
			$newNotification->messageType=$postedData['messageType'];
			
			
			$result = $this->getSnsClient()->publish(array(
					'TopicArn' => $this->getServiceLocator()->get('config')['sns_config']['topic_arn'];
					'Message' => $postedData['packageId'],
					'Subject' => "There are changes to the package"
				));
			
			return new JsonModel(array('Notification' => $this->getCustomerNotificationTable()->createNotification($newNotification)));	
		}
	}

	public function update($messageId, $data)
	{		
		if ($this->getRequest()->isPut()){
			$input = $this->getRequest()->getContent();
			$postedData=json_decode($input,true);
			$newNotification=new Notification();
			$newNotification->messageId=$postedData['messageId'];			
			$newNotification->dateProcessed=date("Y-m-d H:i:s");
			$newNotification->processed=$postedData['processed'];
			
			
			return new JsonModel(array('Notification' => $this->getCustomerNotificationTable()->updateNotification($newNotification)));	
		}
		
	}

	
	/* returns Customer table from service locator */
	public function getCustomerNotificationTable()
	{
		if (!$this->customerNotificationTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerNotificationTable = $sm->get('Account\Model\NotificationTable');	        
	    }
	    return $this->customerNotificationTable;
	}

	public function getSnsClient()
	{
		if (!$this->snsClient) {
	        $sm = $this->getServiceLocator();	        
	        $this->snsClient = $sm->get('SnsClient');	        
	    }
	    return $this->snsClient;
	}
}


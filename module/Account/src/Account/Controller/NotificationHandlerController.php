<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Account\Model\Notification;
use Account\Model\NotificationTable;
use Zend\View\Model\JsonModel;

class NotificationHandlerController extends AbstractRestfulController
{

	protected $customerNotificationTable;
	protected $auditLogTable;
	

	public function customerAction()
	{		
		if ($this->getRequest()->isPost()){


			$notificationMessage = $this->getRequest()->getContent();
			$message = json_decode($notificationMessage);
			$this->getAuditLogTable()->create($notificationMessage);

			
			switch ($message->Type) {
				/*case 'SubscriptionConfirmation':
					$this->getAuditLogTable()->create($message);					
					$curl_session=curl_init();
					curl_setopt($curl_session, CURLOPT_URL, $message->SubscribeURL));
					curl_setopt($curl_session, CURLOPT_HEADER, 0);
					curl_exec($curl_session);
					curl_close($curl_session);
					return new JsonModel(array('result' => "Subscription confirmed"));		*/			
				case 'Notification':
				

					//Do an actual process			
					sleep(5); 

					if ($this->getCustomerNotificationTable()->fetch($message->MessageId)) {
						$this->getAuditLogTable()->create("Start processing the notification... " . $message->Message);
						$newNotification=new Notification();
						$newNotification->messageId=$message->MessageId;			
						$newNotification->dateProcessed=date("Y-m-d H:i:s");
						$newNotification->processed=true;
						$this->getCustomerNotificationTable()->updateNotification($newNotification);						
					}
					else {
						$this->getAuditLogTable()->create($message->MessageId . ' not found.');
					}
					break;
				case 'UnsubscribeConfirmation':
					
					break;
			}
			
		}
	}

	public function getAuditLogTable()
	{
		if (!$this->auditLogTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->auditLogTable = $sm->get('Account\Model\AuditLogTable');	        
	    }
	    return $this->auditLogTable;
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


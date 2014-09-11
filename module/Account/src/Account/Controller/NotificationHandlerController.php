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
			//var_dump($this->getAuditLogTable());
			$this->getAuditLogTable()->create($notificationMessage);
			/*
			$notificationMessage = Aws\Sns\MessageValidator\Message::fromRawPostData();
			$type = $notificationMessage->get('Type');
			$messageId = $notificationMessage->get('MessageId');
			$message = json_decode($notificationMessage->get('Message'),true);	
			$subject = $notificationMessage->get('Subject');
			*/
			
			
			/*
			$message = json_decode($notificationMessage);	
			var_dump($message);
			
			$type = $message->messageType;
			$messageId = $message->MessageId;
			$messageData = $message->Message; //json_decode($message['Message'],true);	

			$subject = $message->Subject;
			*/
			/*
			return new JsonModel(array('t'=>$messageData));
			
			switch ($type) {
				case 'SubscriptionConfirmation':
					$file = new SplFileObject('notification.log', 'a');
					$file->fwrite("Subscription URL... " . $message->get('SubscribeURL') . PHP_EOL );	
					$curl_session=curl_init();
					curl_setopt($curl_session, CURLOPT_URL, $message->get('SubscribeURL'));
					curl_setopt($curl_session, CURLOPT_HEADER, 0);
					curl_exec($curl_session);
					curl_close($curl_session);
					echo "Subscription confirmed";
					break;
				case 'Notification':
					
					$file = new SplFileObject('notification.log', 'a');	
					$file->fwrite("Starting to log subscription notification... " . PHP_EOL );
					$file->fwrite("Subscription notification message... " . $message . PHP_EOL );
					$file->fwrite("Subscription notification message id... " . $messageId . PHP_EOL );
					$file->fwrite("Subscription notification subject... " . $subject . PHP_EOL );
					$file->fwrite("Subscription notification packageid... " . $message['packageid'] . PHP_EOL );

					//Do an actual process			
					sleep(5); 
					if (travel_db_respository::processNotification($messageId,$file)){
						$file->fwrite("Start processing the notification... " . $message['packageid'] . PHP_EOL );
						travel_db_respository::markPackageForNotification($messageId,$message['packageid']);
					}
					
					break;
				case 'UnsubscribeConfirmation':
					echo "Unsubscription complete";
					break;
			}
			*/
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

}


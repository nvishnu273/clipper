<?php
namespace Account\Model;



use Zend\Db\TableGateway\TableGateway;
use Account\Model\Notification;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NotificationTable implements ServiceLocatorAwareInterface    
{
	protected $service_manager;

	protected $tableGateway;

	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->service_manager = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->service_manager;
    }

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll($customerId)
	{
		
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = 'select distinct a.CustomerPackageId, c.DateCreated,c.MessageType  from ' 
					//. $qi('CustomerPackage a')
					. 'TravelAppUser.CustomerPackage a'
					. ' join ' 
					//. $qi('Customer b') 
					. 'TravelAppUser.Customer b'
					. ' on a.CustomerId=b.Id ' 
					. ' left join ' 
					//. $qi('Notification c') 
					. 'TravelAppUser.Notification c'
					. ' on a.CustomerPackageId=c.PacakgeId '
					. ' where a.CustomerId=' . $fp('Customer')
					. ' and c.Processed=1';

		

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
		    'Customer' => $customerId
		);

		$results = $statement->execute($parameters);
		
		$notifications=[];
		foreach($results as $notification) {
		  $notifications[]=$notification;
		}

		return $notifications;
	}

	public function fetchAllInternal()
	{
		
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = 'select a.Id, a.FirstName,a.LastName, b.MessageId  from ' 					
					. 'TravelAppUser.Customer a join '										
					. 'TravelAppUser.Notification b'
					. ' on a.Id=b.PacakgeId '
					. ' where b.Processed=0 and b.MessageType=' .  $fp('MessageType');

		

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(		    
		    'MessageType' => 'NewRegistration'
		);

		$results = $statement->execute($parameters);
		
		$notifications=[];
		foreach($results as $notification) {
		  $notifications[]=$notification;
		}

		return $notifications;
	}

	public function fetch($id)
	{		
		$rowset = $this->tableGateway->select(array('MessageId' => $id));
		$row = $rowset->current();
		if (!$row) {
		    throw new \Exception("Could not find row $id");
		}
		return $row;
	}

	public function createNotification(Notification $notification)
	{		
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 

		$data = array(
		    'MessageId' => $notification->messageId,
		    'PacakgeId'  => $notification->packageId,
		    'DateCreated'  => $notification->dateCreated,
		    'Processed'  => $notification->processed,
		    'MessageType' => $notification->messageType,
		 );
		 $this->tableGateway->insert($data);	     
	     $notification = $this->fetch($notification->messageId);
	     return $notification;
	}

	public function updateNotification(Notification $notification)
	{	
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 

		$data = array(
		    'MessageId' => $notification->messageId,		    
		    'DateProcessed'  => $notification->dateProcessed,		    		    		    
		 );
		$data['Processed']= ($notification->processed=='true');
		//var_dump($data[''])
		$this->tableGateway->update($data, array('MessageId' => $notification->messageId));
	    $notification = $this->fetch($notification->messageId);
	    return $notification;
	}

	public function deleteCustomer($id)
	{
	 	$this->tableGateway->delete(array('MessageId' => $id));
	}
}

 ?>
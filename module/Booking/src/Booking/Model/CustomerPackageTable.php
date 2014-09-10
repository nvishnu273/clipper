<?php
namespace Booking\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


use Booking\Model\CustomerPackage;

class CustomerPackageTable implements ServiceLocatorAwareInterface    
{

	protected $service_manager;

	protected $customerPackageTableGateway;

	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->service_manager = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->service_manager;
    }

	public function __construct(TableGateway $customerPackageTableGateway)
	{
		$this->customerPackageTableGateway = $customerPackageTableGateway;
	}
	
	public function fetchAll()
	{			
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "select a.CustomerId, a.CustomerPackageId, a.StartDate, b.FirstName, b.LastName, b.Email, 
			CONCAT(c.FirstName, ' ' , c.LastName) as AssignedTo, a.Status, a.DateCreated  
			from TravelAppUser.CustomerPackage a 
			join TravelAppUser.Customer b on a.CustomerId=b.Id 
			left join TravelAppUser.User c on a.AssignedTo=c.Id ORDER BY a.DateCreated DESC LIMIT " . $fp('Limit');

		

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
		    'Limit' => '20'
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

		$packages=[];
		foreach($resultSet as $result) {
			$package = new CustomerPackage();	
			$package->exchangeArray($result);		
			$packages[]=$package;
		}

		return $packages;
	}

	public function fetch($id)
	{			

		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "select a.Id, a.CustomerId, a.CustomerPackageId, a.StartDate, b.FirstName, b.LastName, b.Email, 
			CONCAT(c.FirstName, ' ' , c.LastName) as AssignedTo, a.Status  
			from TravelAppUser.CustomerPackage a 
			join TravelAppUser.Customer b on a.CustomerId=b.Id 
			left join TravelAppUser.User c on a.AssignedTo=c.Id where a.Id=" . $fp('BookingId');

		

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
		    'BookingId' => $id
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

		$packages=[];
		foreach($resultSet as $result) {
			$package = new CustomerPackage();	
			$package->exchangeArray($result);		
			$packages[]=$package;
		}

		return $packages;

	}

	//getCustomerBookings
	public function fetchAllByCustomer($customerId)
	{			
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "select a.Id, a.CustomerId, a.CustomerPackageId, a.StartDate, b.FirstName, b.LastName, b.Email, 
				CONCAT(c.FirstName, ' ' , c.LastName) as AssignedTo, a.Status  
					from TravelAppUser.CustomerPackage a 
					join TravelAppUser.Customer b on a.CustomerId=b.Id 
					left join TravelAppUser.User c on a.AssignedTo=c.Id where a.CustomerId=" . $fp($customerId);

		

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
		    'CustomerId' => $customerId
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

		$packages=[];
		foreach($resultSet as $result) {
			//$package = new CustomerPackage();	
			//$package->exchangeArray($result);		
			$packages[]=$result;
		}

		return $packages;
	}

	public function fetchAllByPackageId($packageId)
	{			
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "select a.Id, a.CustomerId, a.CustomerPackageId, a.StartDate, b.FirstName, b.LastName, b.Email, 
				CONCAT(c.FirstName, ' ' , c.LastName) as AssignedTo, a.Status  
					from TravelAppUser.CustomerPackage a 
					join TravelAppUser.Customer b on a.CustomerId=b.Id 
					left join TravelAppUser.User c on a.AssignedTo=c.Id 
					where a.CustomerPackageId=" . $fp($packageId);

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
		    'CustomerPackageId' => $packageId
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

		$packages=[];
		foreach($resultSet as $result) {
			$package = new CustomerPackage();	
			$package->exchangeArray($result);		
			$packages[]=$package;
		}

		return $packages;
	}

	public function fetchAllByStatus($status)
	{			
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "select Id, CustomerPackageId, CustomerId,StartDate 
				from TravelAppUser.CustomerPackage where Status=" . $fp($status);

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
		    'Status' => $status
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

		$packages=[];
		foreach($resultSet as $result) {
			$package = new CustomerPackage();	
			$package->exchangeArray($result);		
			$packages[]=$package;
		}

		return $packages;
	}

	public function fetchAllPendingByAgent($agentId,$status)
	{			
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "select a.Id, a.CustomerId, a.CustomerPackageId, a.StartDate, b.FirstName, b.LastName, b.Email, 
			CONCAT(c.FirstName, ' ' , c.LastName) as AssignedTo, a.Status 
				from TravelAppUser.CustomerPackage a 
				join TravelAppUser.Customer b on a.CustomerId=b.Id 
				join TravelAppUser.User c on a.AssignedTo=c.Id 
				where a.AssignedTo=" . $fp($agentId) . " and " . " a.Status= " . $fp($status);

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
    		'AgentId' => $agentId,
		    'Status' => $status
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

		$packages=[];
		foreach($resultSet as $result) {
			$package = new CustomerPackage();	
			$package->exchangeArray($result);		
			$packages[]=$package;
		}

		return $packages;
	}

	public function fetchAllByDateRange($start,$end,$status,$destination)
	{			
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "select a.Id, a.CustomerId, a.CustomerPackageId, a.StartDate, b.FirstName, b.LastName, b.Email, 
			CONCAT(c.FirstName, ' ' , c.LastName) as AssignedTo, a.Status 
				from TravelAppUser.CustomerPackage a 
				join TravelAppUser.Customer b on a.CustomerId=b.Id 
				left join TravelAppUser.User c on a.AssignedTo=c.Id 
				where a.StartDate between " . $fp($start) . " and " . $fp($end) . " and a.status=" . 
				$fp($status);
		
		$parameters = array(
    		'Start' => $start,
		    'End' => $end,
		    'Status' => $status,
		    
		);

		if ($destination != '') {
			
			$destination = $destination . '%';
			$sql = $sql . " and a.CustomerPackageId like " . $fp($destination);
			$parameters['CustomerPackageId'] = $destination;
		}    	

    	$statement = $dbAdapter->query($sql);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

		$packages=[];
		foreach($resultSet as $result) {
			//$package = new CustomerPackage();	
			//$package->exchangeArray($result);		
			$packages[]=$result;
		}

		return $packages;	
	}

	public function fetchByCompositeKey($customerId,$packageId,$startDate)
	{					

		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "select 
					a.Id, a.CustomerId, a.CustomerPackageId, 
					a.StartDate, b.FirstName, b.LastName, b.Email, 
					CONCAT(c.FirstName, ' ' , c.LastName) as AssignedTo , 
					a.Id
				from 
					TravelAppUser.CustomerPackage a 
					join TravelAppUser.Customer b on a.CustomerId=b.Id 
					left join TravelAppUser.User c on a.AssignedTo=c.Id 
				where 
					a.CustomerId = " . $fp($customerId) 
					. " and a.CustomerPackageId=" . $fp($packageId) 
					. " and a.StartDate=" . $fp($startDate);

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
    		'CustomerId' => $customerId,
		    'CustomerPackageId' => $packageId,
		    'StartDate' => $startDate,
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

		$packages=[];
		foreach($resultSet as $result) {
			return $result;
		}

		//return $packages[0];	
	}

	public function createPackage(CustomerPackage $package)
	{
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 

		$data = array(
		    'CustomerPackageId' => $package->CustomerPackageId,
		    'DateCreated'  => date("Y-m-d H:i:s"),
		    'CustomerId'  => $package->CustomerId,
		    'StartDate'  =>  $package->StartDate->format('Y-m-d'),
		    'Status' => $package->Status
		);

		$id = (int) $package->Id;
		if ($id == 0) {
			$this->customerPackageTableGateway->insert($data);
			$id = $this->customerPackageTableGateway->lastInsertValue;
			$package = $this->fetch($id);
			$package[0]->Id=$id;
			return $package;
		} 
		else {
			 if ($this->fetch($id)) 
			 {
			     $this->customerPackageTableGateway->update($data, array('Id' => $id));
			 } else {
			     throw new \Exception('Customer pacakge id does not exist');
			 }
		}
	}

	public function assignAgent($bookingId,$agentId)
	{					

		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "update TravelAppUser.CustomerPackage set AssignedTo=" . $fp($agentId) . " where Id=" . $fp($bookingId);

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
    		'AgentId' => $agentId,
    		'BookingId' => $bookingId,
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}

	}

	public function checkInAction($bookingId,$checkInTime)
	{					

		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };
		
		$sql = "update TravelAppUser.CustomerPackage set Status='Checked-In'" . " where Id=" . $fp($bookingId);

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(    		
    		'BookingId' => $bookingId,
		);

		$resultSet = $statement->execute($parameters);
		
		if (!$resultSet) {
			throw new \Exception("Could not find package $packageId");
		}		
	}
}

<?php
namespace Account\Model;

use Account\Model\User;

use Zend\Db\TableGateway\TableGateway;
use Account\Model\Customer;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomerTable implements ServiceLocatorAwareInterface    
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

	public function fetchAll()
	{
		$customers=[];
		$resultSet = $this->tableGateway->select();		
		foreach($resultSet as $customer) {
		  $customers[]=$customer;
		}		
		return $customers;
	}

	public function fetch($id)
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array('Id' => $id));
		$row = $rowset->current();
		if (!$row) {
		    throw new \Exception("Could not find row $id");
		}
		return $row;
	}

	public function getPaymentToken($id)
	{
		
	}

	public function fetchUser($id)
	{
		$customer = $this->fetch($id);
		$user = new User();


		$user->id=$customer->id;
		$user->firstname=$customer->firstname;
		$user->lastname=$customer->lastname;
		$user->userType=0;
		$user->userName=$customer->email;
		$user->role=array('customer');			

		return $user;
	}

	public function login($userType,$email,$password)
	{				

		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };

		$sql = 'SELECT Id,FirstName,LastName,0 as Type,Email,Password FROM ' . $qi('Customer') . 'WHERE Email = ' . $fp('email');

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
		    'email' => $email
		);

		$results = $statement->execute($parameters);

		$row = $results->current();
		$password_hash = $row['Password'];
		
		if (password_verify($password, $password_hash))
			return $row['Id'];
		else 
			return 0;
	}

	public function saveCustomer(Customer $customer,$password)
	{		
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 

		//$password=$dbAdapter->real_escape_string($password);			
		$pass_hash=password_hash($password,PASSWORD_DEFAULT);

		$data = array(
		    'Id' => $customer->id,
		    'FirstName'  => $customer->firstname,
		    'LastName'  => $customer->lastname,
		    'Email'  => $customer->email,
		    'Password' => $pass_hash
		 );

		 $id = (int) $customer->id;
		 if ($id == 0) {
		     $this->tableGateway->insert($data);
		     $id = $this->tableGateway->lastInsertValue;
		     $user = $this->fetchUser($id);
		     return $user;
		 } else {
		     if ($this->getCustomer($id)) {
		         $this->tableGateway->update($data, array('Id' => $id));
		     } else {
		         throw new \Exception('Customer id does not exist');
		     }
		 }

	}

	public function deleteCustomer($id)
	{
	 	$this->tableGateway->delete(array('Id' => (int) $id));
	}
}

 ?>
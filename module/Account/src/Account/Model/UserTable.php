<?php
namespace Account\Model;

use Zend\Db\TableGateway\TableGateway;
use Account\Model\User;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserTable implements ServiceLocatorAwareInterface    
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
		$resultSet = $this->tableGateway->select();
		return $resultSet;
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

	public function login($userType,$username,$password)
	{		
		
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 
		
		$qi = function($name) use ($dbAdapter) { return $dbAdapter->platform->quoteIdentifier($name); };
		$fp = function($name) use ($dbAdapter) { return $dbAdapter->driver->formatParameterName($name); };

		$sql = 'SELECT Id,FirstName,LastName,Type,UserName,Password FROM ' . $qi('User') . 'WHERE UserName = ' . $fp('username');

    	$statement = $dbAdapter->query($sql);

    	$parameters = array(
		    'username' => $username
		);

		$results = $statement->execute($parameters);

		$row = $results->current();
		$password_hash = $row['Password'];
		
		if (password_verify($password, $password_hash))
			return $row['Id'];
		else 
			return 0;
	}

	public function saveUser(User $user,$password)
	{

		$password=$travel_db_conn->real_escape_string($password);			
		$pass_hash=password_hash($password,PASSWORD_DEFAULT);

		$data = array(
		    'Id' => $user->id,
		    'FirstName'  => $user->firstname,
		    'LastName'  => $user->lastname,
		    'Email'  => $user->email,
		    'Password' => $pass_hash
		 );

		 $id = (int) $user->id;
		 if ($id == 0) {
		     $this->tableGateway->insert($data);
		 } else {
		     if ($this->getUser($id)) {
		         $this->tableGateway->update($data, array('Id' => $id));
		     } else {
		         throw new \Exception('User id does not exist');
		     }
		 }
	}

	public function deleteUser($id)
	{
	 	$this->tableGateway->delete(array('Id' => (int) $id));
	}
}

 ?>
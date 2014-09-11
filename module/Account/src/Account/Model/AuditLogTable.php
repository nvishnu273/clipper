<?php
namespace Account\Model;



use Zend\Db\TableGateway\TableGateway;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Account\Model\AuditLog;

class AuditLogTable implements ServiceLocatorAwareInterface    
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

	public function create($message)
	{		
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter'); 

		$data = array(
		    'Message' => $message,
		    'CreatedDate'  => date("Y-m-d H:i:s")
		 );
		 $this->tableGateway->insert($data);
	}

}

 ?>
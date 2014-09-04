<?php
namespace factory;

use Authentication\Adapter\HeaderAuthentication;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $request     = $sl->get('Request');
        $userrespository = $sl->get('Account\Model\UserTable');
        $customerrespository = $sl->get('Account\Model\CustomerTable');	  	
        $adapter = new HeaderAuthentication($request, $userrespository, $customerrespository);
        return $adapter;
    }
}
?>
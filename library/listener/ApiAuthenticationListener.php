<?php
namespace listener;

use Authentication\Adapter\HeaderAuthentication;
use Zend\Mvc\MvcEvent;

class ApiAuthenticationListener
{

    private $_noauth = array('module' => 'Account', 
                              'controller' => 'Account', 
                              'action' => 'login'); 

    protected $adapter;

    public function __construct(
        HeaderAuthentication $adapter
    ) {
        $this->adapter = $adapter;
    }


    public function __invoke(MvcEvent $e)
    {

        $controller = $e->getRouteMatch()->getParam('controller');
        $action = $e->getRouteMatch()->getParam('action');

        if($controller != 'Account\Controller\Account' && $action != 'login')
        {

            
            $result = $this->adapter->authenticate();  

            if (!$result->isValid()) {
                $response = $e->getResponse();

                // Set some response content
                $response->setStatusCode(401);
                return $response;
            }

            // Set Identity
            /*
            $event->setParam('user', $result->getIdentity());
            */   
        }   

                
       
    }
}
?>
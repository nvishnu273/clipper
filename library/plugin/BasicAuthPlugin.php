<?php

namespace plugin;


use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * HTTP Basic Authentication Plugin for My API
 */
class BasicAuthPlugin extends AbstractPlugin implements ServiceLocatorAwareInterface    
{
    private $_noauth = array('module' => 'Account', 
                              'controller' => 'Account', 
                              'action' => 'login'); 

    private $_noacl = array('module' => 'default', 
                             'controller' => 'error', 
                             'action' => 'privileges'); 

    protected $service_manager;

    protected $sesscontainer ;
 
    private function getSessContainer()
    {
        if (!$this->sesscontainer) {
            $this->sesscontainer = new SessionContainer('zftutorial');
        }
        return $this->sesscontainer;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->service_manager = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->service_manager;
    }

    public function doAuthorization(MvcEvent $e)
    {
        $controller = $e->getRouteMatch()->getParam('controller');
        $action = $e->getRouteMatch()->getParam('action');


        if($controller != 'Account\Controller\Account' && $action != 'login')
        {
            //if ($this->getAuthService()->hasIdentity()){
                //return $this->redirect()->toRoute('success');
                //var_dump('failure');
            //}            
        }                
    }
}

?>
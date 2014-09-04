<?php
namespace Account;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

use Account\Model\Customer;
use Account\Model\CustomerTable;
use Account\Model\User;
use Account\Model\UserTable;
use Account\Model\Notification;
use Account\Model\NotificationTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);


        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 0);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'onRenderError'), 0);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'loadConfiguration'), 2);
    }

    public function loadConfiguration(MvcEvent $e)
    {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $sharedManager = $application->getEventManager()->getSharedManager();        
        $router = $sm->get('router');
        $request = $sm->get('request');

        $matchedRoute = $router->match($request);
        if (null !== $matchedRoute) {
            $listener = $sm->get('Listener\ApiAuthenticationListener');
            $sharedManager->attach(
                'Zend\Mvc\Controller\AbstractRestfulController',
                MvcEvent::EVENT_DISPATCH, $listener);
            /*
            $sharedManager->attach(
                'Zend\Mvc\Controller\AbstractRestfulController',
                MvcEvent::EVENT_DISPATCH,array($this, 'onAuthentication'), 0);
            */
        }        
    }

    public function onAuthentication(MvcEvent $e)
    {
        $app = $e->getApplication();
        $sm  = $app->getServiceManager();
        //$sm->get('ControllerPluginManager')->get('authplugin')->doAuthorization($e);                     
    }

    public function onDispatchError($e)
    {        
        return $this->getJsonModelError($e);
    }

    public function onRenderError($e)
    {
        return $this->getJsonModelError($e);
    }

    public function getJsonModelError($e)
    {
        $error = $e->getError();
        if (!$error) {
            return;
        }

        $response = $e->getResponse();
        $exception = $e->getParam('exception');
        $exceptionJson = array();
        if ($exception) {
            $exceptionJson = array(
                'class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'stacktrace' => $exception->getTraceAsString()
            );
        }

        $errorJson = array(
            'message'   => 'An error occurred during execution; please try again later.',
            'error'     => $error,
            'exception' => $exceptionJson,
        );
        if ($error == 'error-router-no-match') {
            $errorJson['message'] = 'Resource not found.';
        }

        $model = new JsonModel(array('errors' => array($errorJson)));

        $e->setResult($model);

        return $model;
    }

    public function getConfig()
    {
        $config = array();
        $configFiles = array(
            include __DIR__ . '/config/module.config.php',
            include __DIR__ . '/config/module.account_config.php',
        );
        foreach ($configFiles as $file) {
            $config = \Zend\Stdlib\ArrayUtils::merge($config, $file);
        }
        
        return $config;

        //return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {        
        return array(
            /*'Zend\Loader\ClassMapAutoloader'=>array(
                __DIR__ . '/autoload_classmap.php',
            ),*/
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'common' => __DIR__ . '/../../library/common',
                    'plugin' => __DIR__ . '/../../library/plugin',
                    'listener' => __DIR__ . '/../../library/listener',
                    'factory' => __DIR__ . '/../../library/factory',
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(                
                'Account\Model\CustomerTable' => function($sm) {
                    $customerTableGateway = $sm->get('CustomerTableGateway');
                    $customerTable = new CustomerTable($customerTableGateway);
                    return $customerTable;
                },
                'CustomerTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');                                      
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Customer());
                    return new TableGateway('Customer',$dbAdapter,null,$resultSetPrototype);
                },
                'Account\Model\UserTable' => function($sm) {
                    $userTableGateway = $sm->get('UserTableGateway');
                    $userTable = new UserTable($userTableGateway);
                    return $userTable;
                },
                'UserTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');                                      
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('User',$dbAdapter,null,$resultSetPrototype);
                },
                'Account\Model\NotificationTable' => function($sm) {
                    $notificationTableGateway = $sm->get('NotificationTableGateway');
                    $notificationTable = new NotificationTable($notificationTableGateway);
                    return $notificationTable;
                },
                'NotificationTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');                                      
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Notification());
                    return new TableGateway('Notification',$dbAdapter,null,$resultSetPrototype);
                },
                'Authentication\Adapter\HeaderAuthentication' => 'factory\AuthenticationAdapterFactory',
                'Listener\ApiAuthenticationListener' => 'factory\AuthenticationListenerFactory',
            )
        );
    }
}

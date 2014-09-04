<?php
namespace Booking;

use Booking\Model\CustomerPackage;
use Booking\Model\CustomerPackageTable;
use Package\Model\DynamoDbPackageTableGateway;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Aws\Common\Aws;
use Aws\Common\Enum\Region;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Rds\RdsClient;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\ReturnValue;

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
        }        
    }

    public function onDispatchError($e)
    {
        //var_dump($e);
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
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Booking\Model\CustomerPackageTable' => function($sm) {
                    $customerPackageTableGateway = $sm->get('CustomerPackageTableGateway');                    
                    $customerPackageTable = new CustomerPackageTable($customerPackageTableGateway);
                    return $customerPackageTable;
                },
                'CustomerPackageTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');                                      
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CustomerPackage());
                    return new TableGateway('CustomerPackage',$dbAdapter,null,$resultSetPrototype);                                                          
                },
                'Booking\Model\PackageTable' => function($sm) {
                    $dynamoDbPackageTableGateway = $sm->get('DynamoDbPackageTableGateway');                    
                    $packageTable = new PackageTable($dynamoDbPackageTableGateway);
                    return $packageTable;
                },
                'DynamoDbPackageTableGateway' => function($sm) {
                    $dynamoDbClient = $sm->get('DynamoDbClient');                                      
                    return new DynamoDbPackageTableGateway('TravelPackage',$dynamoDbClient);
                },
                'DynamoDbClient' => function($sm) {                    
                    $aws = $sm->get('aws');                                   
                    $dynamoDbClient = $aws->get("dynamodb");                    
                    return $dynamoDbClient;
                }
            )
        );
    }
}

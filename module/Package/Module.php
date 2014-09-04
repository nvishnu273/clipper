<?php
namespace Package;

use Package\Model\Package;
use Package\Model\PackageTable;
use Package\Model\PackagePlanTable;
use Package\Model\PackagePlanDayLocationTable;
use Package\Model\DynamoDbPackageTableGateway;
use Package\Model\TravelPackageInstance;
use Package\Model\TravelPackageInstanceTable;
use Package\Model\DynamoDbTravelPackageInstanceTableGateway;

use Zend\Db\ResultSet\ResultSet;
use Zend\TableGateway\TableGateway;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

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
    
	public function getAutoloaderConfig()
	{        
		return array(
			'Zend\Loader\ClassMapAutoloader'=>array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
		);
	}
	
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';		
	}

    public function getServiceConfig()
    {
        return array(
            'factories' => array(                
                'Package\Model\PackageTable' => function($sm) {
                    $dynamoDbPackageTableGateway = $sm->get('DynamoDbPackageTableGateway');                    
                    $packageTable = new PackageTable($dynamoDbPackageTableGateway);
                    return $packageTable;
                },
                'Package\Model\PackagePlanTable' => function($sm) {
                    $dynamoDbPackageTableGateway = $sm->get('DynamoDbPackageTableGateway');                    
                    $packagePlanTable = new PackagePlanTable($dynamoDbPackageTableGateway);
                    return $packagePlanTable;
                },
                'Package\Model\PackagePlanDayLocationTable' => function($sm) {
                    $dynamoDbPackageTableGateway = $sm->get('DynamoDbPackageTableGateway');                    
                    $packagePlanDayLocationTable = new PackagePlanDayLocationTable($dynamoDbPackageTableGateway);
                    return $packagePlanDayLocationTable;
                },
                'DynamoDbPackageTableGateway' => function($sm) {
                    $dynamoDbClient = $sm->get('DynamoDbClient');                                      
                    return new DynamoDbPackageTableGateway('TravelPackage',$dynamoDbClient);
                },
                'Package\Model\TravelPackageInstanceTable' => function($sm) {
                    $dynamoDbPackageInstanceTableGateway = $sm->get('DynamoDbTravelPackageInstanceTableGateway');                    
                    $travelPackageInstanceTable = new TravelPackageInstanceTable($dynamoDbPackageInstanceTableGateway);
                    return $travelPackageInstanceTable;
                },
                'DynamoDbTravelPackageInstanceTableGateway' => function($sm) {
                    $dynamoDbClient = $sm->get('DynamoDbClient');                                      
                    return new DynamoDbTravelPackageInstanceTableGateway('CustomerTravelPackage',$dynamoDbClient);
                },
                'DynamoDbClient' => function($sm) {                    
                    $aws = $sm->get('aws');                                   
                    $dynamoDbClient = $aws->get("dynamodb");                    
                    return $dynamoDbClient;
                },
                'SnsClient' => function($sm) {                    
                    $aws = $sm->get('aws');                                   
                    $snsClient = $aws->get("Sns");                    
                    return $snsClient;
                },                
            )
        );
    }
}

?>

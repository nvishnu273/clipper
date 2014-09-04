<?php
namespace Trip;

use Booking\Model\CustomerPackage;
use Booking\Model\CustomerPackageTable;

use Package\Model\TravelPackageInstance;
use Package\Model\TravelPackageInstanceTable;
use Package\Model\DynamoDbTravelPackageInstanceTableGateway;

use Trip\Model\TripReviewTable;
use Trip\Model\DynamoDbTripReviewTableGateway;

use Trip\Model\TripPhotoTable;
use Trip\Model\S3TripPhotoTableGateway;
use Trip\Model\DynamoDbTripPhotoByPackageTableGateway;
use Trip\Model\DynamoDbTripPhotoByLocationTableGateway;


use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Aws\Common\Aws;
use Aws\Common\Enum\Region;

use Aws\S3\S3Client;

use Aws\Rds\RdsClient;
use Aws\DynamoDb\DynamoDbClient;
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

                'Package\Model\TravelPackageInstanceTable' => function($sm) {
                    $dynamoDbPackageInstanceTableGateway = $sm->get('DynamoDbTravelPackageInstanceTableGateway');                    
                    $travelPackageInstanceTable = new TravelPackageInstanceTable($dynamoDbPackageInstanceTableGateway);
                    return $travelPackageInstanceTable;
                },
                'DynamoDbTravelPackageInstanceTableGateway' => function($sm) {
                    $dynamoDbClient = $sm->get('DynamoDbClient');                                      
                    return new DynamoDbTravelPackageInstanceTableGateway('CustomerTravelPackage',$dynamoDbClient);
                },

                'Trip\Model\TripPhotoTable' => function($sm) {
                    $s3TripPhotoTableGateway = $sm->get('S3TripPhotoTableGateway');
                    $dynamoDbTripPhotoByLocationTableGateway = $sm->get('DynamoDbTripPhotoByLocationTableGateway');
                    $dynamoDbTripPhotoByPackageTableGateway = $sm->get('DynamoDbTripPhotoByPackageTableGateway');                    
                    $tripPhotoTable = new TripPhotoTable(
                        $s3TripPhotoTableGateway,
                        $dynamoDbTripPhotoByLocationTableGateway,
                        $dynamoDbTripPhotoByPackageTableGateway);
                    return $tripPhotoTable;
                },                
                'S3TripPhotoTableGateway' => function($sm) {                                                 
                    $s3Client = $sm->get('aws')->get('S3');                                      
                    return new S3TripPhotoTableGateway('clippertravels',$s3Client);
                },
                'DynamoDbTripPhotoByPackageTableGateway' => function($sm) {
                    $dynamoDbClient = $sm->get('DynamoDbClient');                                                         
                    return new DynamoDbTripPhotoByPackageTableGateway('TripPhotosByPackage',$dynamoDbClient);
                },
                'DynamoDbTripPhotoByLocationTableGateway' => function($sm) {
                    $dynamoDbClient = $sm->get('DynamoDbClient');                                      
                    return new DynamoDbTripPhotoByLocationTableGateway('TripPhotosByLocation',$dynamoDbClient);
                },

                'Trip\Model\TripReviewTable' => function($sm) {                    
                    $dynamoDbTripReviewTableGateway = $sm->get('DynamoDbTripReviewTableGateway');                    
                    $tripReviewTable = new TripReviewTable($dynamoDbTripReviewTableGateway);
                    return $tripReviewTable;
                }, 
                'DynamoDbTripReviewTableGateway' => function($sm) {
                    $dynamoDbClient = $sm->get('DynamoDbClient');                                      
                    return new DynamoDbTripReviewTableGateway('TripReviews',$dynamoDbClient);
                },
            )
        );
    }
}

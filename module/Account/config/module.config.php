<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Account\Controller\Account' => 'Account\Controller\AccountController',
            'Account\Controller\Customer' => 'Account\Controller\CustomerController',
            'Account\Controller\CustomerNotification' => 'Account\Controller\CustomerNotificationController',            
            'Account\Controller\NotificationHandler' => 'Account\Controller\NotificationHandlerController'
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'authplugin' => 'plugin\BasicAuthPlugin',
        ),
    ),
    'router' => array(
        'routes' => array(
            'account' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/account[/:action]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Account\Controller',
                        'controller'    => 'Account',
                        'action' => 'index' 
                    ),
                ),
            ),
            'customer' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Account\Controller',
                        'controller'    => 'Customer',                        
                    ),
                ),                 
                'may_terminate' => true,
                'child_routes' => array(
                    'notification' => array(
                        'type' => 'segment',
                            'options' => array(
                                'route' => '/notification',
                                'defaults' => array(
                                    'controller' => 'Account\Controller\CustomerNotification',                                    
                            ),
                        ),
                    ),                         
                ),                    
            ),
            'notification' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/notification',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Account\Controller',
                        'controller'    => 'NotificationHandler'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'customer' => array(
                        'type' => 'segment',
                            'options' => array(
                                'route' => '/[:action]',
                                'defaults' => array(                                     
                                    'action' => 'customer'                                   
                            ),
                        ),
                    ), 
                    'internal' => array(
                        'type' => 'segment',
                            'options' => array(
                                'route' => '/',
                                'defaults' => array(                                     
                                    'action' => 'internal'                                   
                            ),
                        ),
                    ),                         
                ),  
            ),
        ),                  
    ),        
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),    
);
?>
<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Account\Controller\Account' => 'Account\Controller\AccountController',
            'Account\Controller\Customer' => 'Account\Controller\CustomerController',
            'Account\Controller\CustomerNotification' => 'Account\Controller\CustomerNotificationController',            
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
        ),                  
    ),        
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),    
);
?>
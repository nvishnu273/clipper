<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Booking\Controller\Booking' => 'Booking\Controller\BookingController',
            'Booking\Controller\Search' => 'Booking\Controller\SearchController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'booking' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/booking[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Booking\Controller',
                        'controller'    => 'Booking',                        
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
	            	'search' => array(
	            		'type' => 'segment',
	            		'options' => array(
	            			'route' => '/search[/:action]',
	            			'defaults' => array(
	            				'controller' => 'Booking\Controller\Search',                                    
	            				'action' => 'index',
	            			),
	            		),
	            	),
                    'agent' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/agent[/:action]'
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
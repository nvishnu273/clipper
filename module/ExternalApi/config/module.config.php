<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'ExternalApi\Controller\ExternalApi' => 'ExternalApi\Controller\ExternalApiController',            
        ),
    ),
    'router' => array(
        'routes' => array(
            'autocomplete' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'ExternalApi\Controller',
                        'controller'    => 'ExternalApi'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
	            	'autocomplete' => array(
	            		'type' => 'segment',
	            		'options' => array(
	            			'route' => 'autocomplete[/:action]',	
	            			'defaults' => array(
	            				'controller' => 'ExternalApi\Controller\ExternalApi',  
	            				'action' => 'index',	            				                         	            				
	            			), 
	            			'constraints' => array(
	            				'action' => '(city|hotel)'
	            			),
	            		),
	            	),
                    'geocode' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'geocode[/:action]',    
                            'defaults' => array(
                                'controller' => 'ExternalApi\Controller\ExternalApi',  
                                'action' => 'index',                                                                                        
                            ), 
                            'constraints' => array(
                                'action' => '(latlng|address|nearby)'
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
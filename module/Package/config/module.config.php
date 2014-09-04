<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Package\Controller\Package' => 'Package\Controller\PackageController',            
            'Package\Controller\PackagePlan' => 'Package\Controller\PackagePlanController',
            'Package\Controller\PackagePlanDayLocation' => 'Package\Controller\PackagePlanDayLocationController',   
        ),
    ),
    'router' => array(
        'routes' => array(
            'package' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/package[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Package\Controller',
                        'controller'    => 'Package'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'location' => array(
                        'type'    => 'literal',
                        'options' => array(
                            'route'    => '/publish',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Package\Controller',
                                'controller'    => 'Package',
                                'action' => 'publish'
                            ),
                        ),
                        'may_terminate' => true
                    ),
                ),  
            ),
            'packagesearch' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/package/search[/][:action][/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Package\Controller',
                        'controller'    => 'Package',
                        'action' => 'filterByStatus'
                    ),
                ),
            ),
            'day' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/package/day[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Package\Controller',
                        'controller'    => 'PackagePlan',                        
                    ),
                    'constraints' => array(
                        'id' => '[0-999]*'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'location' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/location[/:latlng]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Package\Controller',
                                'controller'    => 'PackagePlanDayLocation'
                            ),
                        ),
                        'may_terminate' => true
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
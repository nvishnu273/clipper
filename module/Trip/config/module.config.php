<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Trip\Controller\Trip' => 'Trip\Controller\TripController',            
            'Trip\Controller\TripReview' => 'Trip\Controller\TripReviewController',
            'Trip\Controller\TripPhoto' => 'Trip\Controller\TripPhotoController',   
        ),
    ),
    'router' => array(
        'routes' => array(
            'trip' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/trip[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Trip\Controller',
                        'controller'    => 'Trip'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'review' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/review[/:action]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Trip\Controller',
                                'controller'    => 'TripReview',
                                'action' => 'search'
                            ),
                        ),
                        'may_terminate' => true
                    ),
                    'photo' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/photo[/:action]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Trip\Controller',
                                'controller'    => 'TripPhoto'
                            ),
                        ),
                        'may_terminate' => true
                    ),
                    'upload' => array(
                        'type'    => 'literal',
                        'options' => array(
                            'route'    => '/upload',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Trip\Controller',
                                'controller'    => 'TripPhoto'
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
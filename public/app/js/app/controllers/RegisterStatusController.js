'use strict';

travelManagerApp.controller('RegisterStatusController',
	function RegisterStatusController($scope,$location,$routeParams,authenticationService,Session,growl){		
		$scope.isActivated=false;
		authenticationService.getAccountInfo($routeParams.id).then(function(data) {		
			$scope.customer = data;		
			$scope.isActivated=$scope.customer.activated;
			console.log($scope.customer.activated);		
		});			
	});
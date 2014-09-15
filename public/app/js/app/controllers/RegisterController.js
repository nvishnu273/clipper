'use strict';

travelManagerApp.controller('RegisterController',
	function RegisterController($scope, $rootScope, AUTH_EVENTS, authenticationService, $location){
		
		$scope.customer = {			
			firstname: '',
			lastname: '',
			password: '',
			email: ''
		};
		
		$scope.registerNewCustomer = function(customer){									
			authenticationService.createNewCustomer(customer).then(function (d) {	
				if (typeof d.message != 'undefined') {
					console.log(d);
				}
				else {
					var url = '/register/' + d.User + '/status';			
					$location.path(url);
				}
				
			}, function (status) {
				console.log(status);
			  //$rootScope.$broadcast(AUTH_EVENTS.loginFailed);
			});

		};
		
	});
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
				console.log(d);		  			  
			  var credentials = {
				username: $scope.customer.email,
				password: $scope.customer.password,
				userType: 0,
				userTypeName: 'Customer',
			  };
			  authenticationService.login(credentials).then(function (d) {			  				  
				  $scope.$parent.currentUser=d.user;
				  $rootScope.$broadcast(AUTH_EVENTS.loginSuccess);
				}, function () {
				  $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
				});			  
			}, function () {
			  $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
			});
		};
		
	});
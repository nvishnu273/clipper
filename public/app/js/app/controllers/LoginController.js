'use strict';

travelManagerApp.controller('LoginController',
	function LoginController($scope,$rootScope, AUTH_EVENTS, authenticationService){
		
		$scope.userTypes = [{
				id: 0,
				name: 'Customer'
			}, {
			id: 1,
			name: 'Agent'
		}, {
			id: 2,
			name: 'Manager'
		}];
		
		$scope.credentials = {
			username: 'lester@rdacorp.com',
			password: 'P@ssword1',
			userType: 0,
			userTypeName: 'Customer',
		};
		
		/*
		
		$scope.credentials = {
			username: '',
			password: '',
			userType: 0,
			userTypeName: 'Customer',
		};
		
		$scope.credentials = {
			username: 'lester@rdacorp.com',
			password: 'P@ssword1',
			userType: 0,
			userTypeName: 'Customer',
		};
		
		$scope.credentials = {
			username: 'tadmin1',
			password: 'P@ssword1',
			userType: 2,
			userTypeName: 'Manager',
		};
		
		$scope.credentials = {
			username: 'tagent2',
			password: 'P@ssword1',
			userType: 1,
			userTypeName: 'Agent',
		};
		
		$scope.credentials = {
			username: 'tagent1',
			password: 'P@ssword1',
			userType: 1,
			userTypeName: 'Agent',
		};
		*/
		$scope.login = function (credentials) {
			authenticationService.login(credentials).then(function (d) {			  			  
			  $scope.$parent.currentUser=d.user;
			  $rootScope.$broadcast(AUTH_EVENTS.loginSuccess);
			}, function () {
			  $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
			});
		  };
		  
		$scope.onUserTypeSelected = function (userType) {
			$scope.credentials.userType=userType.id;
			$scope.credentials.userTypeName=userType.name;
		 };
	});
'use strict';

travelManagerApp.controller('ApplicationController', function ($scope,$location,
                                               USER_ROLES,
											   AUTH_EVENTS,
                                               authenticationService,
											   Session) {
  $scope.currentUser = null;
  $scope.userRoles = USER_ROLES;
  $scope.isAuthorized = authenticationService.isAuthorized;
    
  $scope.$on(AUTH_EVENTS.notAuthenticated, function($) {
		console.log('Login was not successful');
		$location.path("/login");
	});
  $scope.$on(AUTH_EVENTS.loginSuccess, function($) {			
		if (Session.userType == 0){
			$location.path("/mybookings");
		}
		else if (Session.userType == 1){
			$location.path("/myassignments");
		}
		else if (Session.userType == 2){
			$location.path("/bookings");
		}
		else {
			$location.path("/login");
		}
	});
  $scope.$on(AUTH_EVENTS.logoutSuccess, function($) {
		$scope.currentUser = null;
		console.log('Logout was successful');
		$location.path("/login");
	});
  
  
})
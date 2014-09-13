'use strict';

travelManagerApp.controller('MenuController',
	function MenuController($scope,$rootScope, AUTH_EVENTS, authenticationService, packageCatalogService, Session){
	
		$scope.isAuthenticated = authenticationService.isAuthenticated;
		
		$scope.notifications=[];	
		$scope.hasNotification=function(){
			return $scope.notifications.length > 0;
		};
		
		$scope.$on(AUTH_EVENTS.loginSuccess, function($) {	
			var userType = Session.userType || 0;								
			if (userType == 0 || userType==2){				
				packageCatalogService.getNotifications(Session.userType,Session.userId).then(function(data) {
					$scope.notifications=[];
					angular.forEach(data, function(value, key) {
						this.push(value);				   
					}, $scope.notifications);					
				});
			}
		});
								
		$scope.signOut = function(){
			Session.destroy();
			$rootScope.$broadcast(AUTH_EVENTS.logoutSuccess);
		};

		$scope.approveNewUserRequest = function(index){
			var notificationToApprove = $scope.notifications[index];
			authenticationService.approveNewUser(notificationToApprove).then(function(data) {
					console.log(data);
 					$scope.notifications.splice(index, 1);
 					console.log($scope.notifications);
				});
		};
	});
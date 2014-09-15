'use strict';

var travelManagerApp = angular.module('travelManagerApp', ['ngRoute','ngSanitize','ngResource','ngCookies','ui.bootstrap','google-maps',
			'angularFileUpload','angular-growl','uiSlider','kendo.directives'])
    .constant('AUTH_EVENTS', {
	  loginSuccess: 'auth-login-success',
	  loginFailed: 'auth-login-failed',
	  logoutSuccess: 'auth-logout-success',
	  sessionTimeout: 'auth-session-timeout',
	  notAuthenticated: 'auth-not-authenticated',
	  notAuthorized: 'auth-not-authorized'
	}).constant('USER_ROLES', {
	  all: '*',
	  manager: 'manager',
	  agent: 'agent',
	  customer: 'customer',
	  anonymous: 'anonymous'
	})
	.config(function($httpProvider){		
		
		$httpProvider.interceptors.push([
			'$injector',
			function($injector) {
				return $injector.get('AuthInterceptor');
			}
		]);

		$httpProvider.interceptors.push([
			'$injector',
			function($injector) {
				return $injector.get('authInterceptorService');
			}
		]);		
	})
	.factory('AuthInterceptor', function($rootScope, $q, AUTH_EVENTS) {		
		return {
			responseError: function(response){
				if (response.status === 401) {
					$rootScope.$broadcast(AUTH_EVENTS.notAuthenticated,
									  response);
				}
				if (response.status === 403) {
					$rootScope.$broadcast(AUTH_EVENTS.notAuthorized,
									  response);
				}
				if (response.status === 419 || response.status === 440) {
					$rootScope.$broadcast(AUTH_EVENTS.sessionTimeout,
									  response);
				}
				return $q.reject(response);
			}
		};
	})
	.factory('authInterceptorService', ['$q', '$location', 'Session', function ($q, $location, Session) {
 
	    var authInterceptorServiceFactory = {};
	 
	    var _request = function (config) {
	 
	        config.headers = config.headers || {};
	 
	        //var authData = localStorageService.get('authorizationData');
	        if (Session) {
	            config.headers.Authorization = 'Bearer ' + Session.id;
	            //config.headers.Authorization = 'Bearer ' + 'a74743a5b35f335551cce5a4180366f534677f0924de53abb4d4ab4e8a41e3adrbd7Hl9HeazhByWU2jAatf30XyCd4N6duzV88YVWUHE=';
	        }
	 
	        return config;
	    }
	 
	    var _responseError = function (rejection) {
	        if (rejection.status === 401) {
	            //$location.path('/login');
	            $rootScope.$broadcast(AUTH_EVENTS.notAuthenticated,response);
	        }
	        return $q.reject(rejection);
	    }
	 
	    authInterceptorServiceFactory.request = _request;
	    authInterceptorServiceFactory.responseError = _responseError;
	 
	    return authInterceptorServiceFactory;
	}])
	.config(function($routeProvider,$locationProvider,USER_ROLES){			
			$routeProvider
				.when('/login', {
									templateUrl:  'templates/Login.html', 
									controller: 'LoginController',
									data: {
										authorizedRoles: [USER_ROLES.anonymous]
									} 
								}
				)				
				.when('/register', {
									templateUrl:  'templates/Register.html', 
									controller: 'RegisterController',
									data: {
										authorizedRoles: [USER_ROLES.anonymous]
									} 
								}
				)
				.when('/register/:id/status', {
									templateUrl:  'templates/RegisterStatus.html', 
									controller: 'RegisterStatusController',
									data: {
										authorizedRoles: [USER_ROLES.anonymous]
									} 
								}
				)

				.when('/account', {
									templateUrl:  'templates/Account.html', 
									controller: 'AccountController',
									data: {
										authorizedRoles: [USER_ROLES.customer]
									} 
								}
				)
				.when('/account/creditcard', {
									templateUrl:  'templates/CreditCard.html', 
									controller: 'CreditCardController',
									data: {
										authorizedRoles: [USER_ROLES.customer]
									} 
								}
				)
				.when('/payment-process', {
									templateUrl:  'templates/Register.html', 
									controller: 'RegisterController',
									data: {
										authorizedRoles: [USER_ROLES.anonymous]
									} 
								}
				)
				.when('/mybookings', 
								{
									templateUrl: 'templates/MyBookingList.html', 
									controller: 'MyBookingListController', 
									data: {
										authorizedRoles: [USER_ROLES.customer]
									}
								}
				)
				.when('/myassignments', 
								{ 
									templateUrl: 'templates/MyAssignmentList.html', 
									controller: 'MyAssignmentListController',
									data: {
										authorizedRoles: [USER_ROLES.manager]
									}
								}
				)
				.when('/bookings', { 
										templateUrl: 'templates/BookingList.html', 
										controller: 'BookingListController',
										data: {
											authorizedRoles: [USER_ROLES.agent,USER_ROLES.manager]
										}
									}
				)
				.when('/booking/:customerId/:bookingId/:start?', 
									{ 
										templateUrl: 'templates/BookingDetails.html', 
										controller: 'ViewBookingController',
										data: {
											authorizedRoles: [USER_ROLES.all]
										}
									}
				)
				.when('/packages', 
									{ 
										templateUrl: 'templates/PackageList.html', 
										controller: 'PackageListController',
										data: {
											authorizedRoles: [USER_ROLES.all]
										}
									}
				)
				.when('/packages/reviews', 
									{ 
										templateUrl: 'templates/ReviewList.html', 
										controller: 'ReviewListController',
										data: {
											authorizedRoles: [USER_ROLES.all]
										}
									}
				)
				.when('/packages/pending', 
									{ 
										templateUrl: 'templates/PackageListPending.html', 
										controller: 'PackageListPendingController',
										data: {
											authorizedRoles: [USER_ROLES.manager]
										}
									}
				)
				.when('/packages/add', 
									{ 
										templateUrl: 'templates/AddPackage.html', 
										controller: 'AddPackageController',
										data: {
											authorizedRoles: [USER_ROLES.manager]
										}
									}
				)
				.when('/packages/:destination/:packageId', 
									{ 
										templateUrl: 'templates/ViewPackage.html', 
										controller: 'ViewPackageController',
										data: {
											authorizedRoles: [USER_ROLES.all]
										}
									}
				)
				.when('/packages/:destination/:packageId/edit', 
									{ templateUrl: 'templates/EditPackage.html', 
									controller: 'EditPackageController',
										data: {
											authorizedRoles: [USER_ROLES.manager]
										}
									}
				)
				.when('/packages/:destination/:packageId/:day', 
									{ 
										templateUrl: 'templates/ManageDayPlan.html', 
										controller: 'ManageDayPlanController',
										data: {
											authorizedRoles: [USER_ROLES.manager]
										}
									}
				)
				.when('/reports', 
									{ 
										templateUrl: 'templates/Report.html', 
										controller: 'ReportController',
										data: {
											authorizedRoles: [USER_ROLES.manager]
										}
									}
				)
				.when('/trip/:tripId/:start?', 
									{ 
										templateUrl: 'templates/TripDetails.html', 
										controller: 'TripDetailsController',
										data: {
											authorizedRoles: [USER_ROLES.all]
										}
									}
				)
				.otherwise({ 
								redirectTo: '/bookings',								
								data: {
									authorizedRoles: [USER_ROLES.agent,USER_ROLES.manager]
								}
				});
				$locationProvider.html5Mode(true); /* Uses HTML5 push/pop state instead of # (hasbang mode) anchor tag urls */  
		
	});

travelManagerApp.config(["growlProvider", "$httpProvider", function(growlProvider, $httpProvider) {
	growlProvider.globalTimeToLive(2000);	
	$httpProvider.responseInterceptors.push(growlProvider.serverMessagesInterceptor);
}]);

travelManagerApp.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});

	
travelManagerApp.run(function ($rootScope, USER_ROLES, AUTH_EVENTS, authenticationService) {
	$rootScope.$on('$routeChangeStart', function(event, next, current) {		
		var authorizedRoles = next.data.authorizedRoles;
		if (authorizedRoles.indexOf(USER_ROLES.anonymous) == -1) {
			if (!authenticationService.isAuthorized(authorizedRoles)) {
			  event.preventDefault();
			  if (authenticationService.isAuthenticated()) {
				// user is not allowed
				$rootScope.$broadcast(AUTH_EVENTS.notAuthorized);
			  } else {
				// user is not logged in				
				$rootScope.$broadcast(AUTH_EVENTS.notAuthenticated);
			  }
			}
		}						
	});
});	
'use strict';

var travelManagerApp = angular.module('travelManagerApp', ['ngRoute', 'ngSanitize', 'ngResource', 'ngCookies','ui.bootstrap','google-maps'])
	.config(function($routeProvider, $locationProvider){
			$routeProvider
				.when('/mybookings', { templateUrl: 'templates/MyBookingList.html', controller: 'MyBookingListController' })								
				.when('/booking/:customerId/:bookingId/:start?', { templateUrl: 'templates/BookingDetails.html', controller: 'ViewBookingController' })				
				.when('/trip/:tripId/:start?', { templateUrl: 'templates/TripDetails.html', controller: 'TripDetailsController' })
				.otherwise({ redirectTo: '/mybookings' });
				$locationProvider.html5Mode(true); /* Uses HTML5 push/pop state instead of # (hasbang mode) anchor tag urls */  
		
	}).constant('AUTH_EVENTS', {
	  loginSuccess: 'auth-login-success',
	  loginFailed: 'auth-login-failed',
	  logoutSuccess: 'auth-logout-success',
	  sessionTimeout: 'auth-session-timeout',
	  notAuthenticated: 'auth-not-authenticated',
	  notAuthorized: 'auth-not-authorized'
	}).constant('USER_ROLES', {
	  all: '*',
	  admin: 'manager',
	  editor: 'agent',
	  guest: 'customer'
	});
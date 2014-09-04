'use strict';

travelManagerApp.controller('MyBookingListController',
	function MyBookingListController($scope, bookingsService, Session){				
		$scope.orderByParam = "StartDate";
		bookingsService.getCustomerBookings(Session.userId).then(function(data) {					
			$scope.bookings = data;				
		});		
		$scope.isPendingCheckin=function(status){			
			return (status=="PendingCheckin");
		};
	});
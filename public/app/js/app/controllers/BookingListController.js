'use strict';

travelManagerApp.controller('BookingListController',
	function BookingListController($scope, bookingsService, growl){
		$scope.destination = "";
		$scope.startDate = "2014/06/01";
		$scope.endDate = "2014/12/31";
		$scope.orderByParam = "StartDate";
		$scope.seachBookings = function(){
			var startDate = moment($scope.startDate).format("YYYY/MM/DD");
			var endDate = $scope.endDate ? moment($scope.endDate).format("YYYY/MM/DD") : "";				
			bookingsService.getBookings(startDate,endDate,$scope.destination).then(function(data) {				
				$scope.bookings = data;
			});			
		};		
	});
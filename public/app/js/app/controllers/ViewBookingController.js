'use strict';

travelManagerApp.controller('ViewBookingController',
	function PackageController($scope,bookingsService,$routeParams,Session){
		
		bookingsService.getBookingDetail($routeParams.customerId,$routeParams.bookingId,$routeParams.start).then(function(data) {							
			$scope.booking = data;	
			$scope.showAssignAgent = function(){
				var userType = Session.userType || 0;
				var isManager = (Session.userType == 2);				
				var isAgentAssigned = $scope.booking.customer.AssignedTo || false;
				var showAgent = isManager && !isAgentAssigned;							
				return isManager && !isAgentAssigned;
			};			
		});	
		
		$scope.assignGuide = function(userId){			
			bookingsService.assignGuide(userId,$scope.booking.customer.Id).then(function(data) {													
				$scope.booking.customer.AssignedTo = data;					
			});
		};		
		
		
	});
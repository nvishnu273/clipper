'use strict';

travelManagerApp.controller('MyAssignmentListController',
	function MyAssignmentListController($scope,$modal,bookingsService, Session){				
		
		$scope.orderByParam = "StartDate";
		
		
		var CheckInCustomerModalInstanceCtrl = function ($scope, $modalInstance, booking) {			
			$scope.booking = booking;
			$scope.checkedIn=false;		  
			$scope.selected = {
				item: $scope.checkedIn
			};

			$scope.ok = function () {			
				
				bookingsService.checkInCustomer($scope.booking.CheckInTime,$scope.booking.Id).then(function(data) {							
					$scope.checkedIn=true;
					$modalInstance.close($scope.checkedIn);			
				});

				$modalInstance.close($scope.checkedIn);			
			};

			$scope.cancel = function () {
				$modalInstance.dismiss('cancel');
			};
		};

		bookingsService.getMyAssignments(Session.userId).then(function(data) {					
			$scope.bookings = data;				
		});
		
		$scope.checkInCustomer=function (booking) {
			
			var modalInstance = $modal.open({
			  templateUrl: 'templates/CheckInCustomer.html',
			  controller: CheckInCustomerModalInstanceCtrl,			  
			  resolve: {
				booking: function () {
				  return booking;
				}
			  }
			});

			modalInstance.result.then(function (checkedIn) {					
					booking.Status = 'Checked-In';					
				}, function () {					
			});
		};		

	});
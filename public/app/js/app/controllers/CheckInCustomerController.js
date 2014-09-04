'use strict';

travelManagerApp.controller('MyAssignmentListController',
	function MyAssignmentListController($scope,$modal,bookingsService,Session){				
		
		$scope.orderByParam = "StartDate";
		
		var CheckInCustomerModalInstanceCtrl = function ($scope, $modalInstance, booking) {
		  $scope.booking = booking;
		  $scope.selected = {
			item: $scope.items[0]
		  };

		  $scope.ok = function () {
			$modalInstance.close($scope.selected.item);
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
			  templateUrl: 'CheckInCustomer.html',
			  controller: CheckInCustomerModalInstanceCtrl,
			  size: 260,
			  resolve: {
				booking: function () {
				  return booking;
				}
			  }
			});
			modalInstance.result.then(function (selectedItem) {
				  $scope.selected = selectedItem;
				}, function () {
				  $log.info('Modal dismissed at: ' + new Date());
			});
		};
		
		$scope.checkInCustomer2=function(packageId,customerId,startDate){
			bookingsService.checkInCustomer(packageId,customerId,startDate).then(function(data) {		
						console.log(data);
						$scope.bookings = data;				
					});
		};
	});
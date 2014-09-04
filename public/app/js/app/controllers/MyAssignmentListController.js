'use strict';

travelManagerApp.controller('MyAssignmentListController',
	function MyAssignmentListController($scope,$modal,bookingsService, Session){				
		
		$scope.orderByParam = "StartDate";
		
		var t = '<div class="modal-dialog">' +
              '<div class="modal-content">' +
                '<div class="modal-header">' +
                 '<button type="button" class="close" ng-click="close()" aria-hidden="true">&times;</button>' +
                  '<h4 class="modal-title">Modal title</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                  '<p>One fine body&hellip;</p>' +
                '</div>' +
                '<div class="modal-footer">' +
                  '<button type="button" class="btn btn-default" ng-click="close()">Close</button>' +
                  '<button type="button" class="btn btn-primary" ng-click="close()">Save changes</button>' +
                '</div>' +
              '</div><!-- /.modal-content -->' +
            '</div><!-- /.modal-dialog -->';
			
		var CheckInCustomerModalInstanceCtrl = function ($scope, $modalInstance, booking) {
			console.log(booking);
			$scope.booking = booking;
			$scope.checkedIn=false;		  
			$scope.selected = {
				item: $scope.checkedIn
			};

			$scope.ok = function () {			
				bookingsService.checkInCustomer($scope.booking.CheckInTime,$scope.booking.Id).then(function(data) {							
					$scope.checkedIn=true;
					$modalInstance.close($scope.selected.item);			
				});
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
					console.log(checkedIn);
					$scope.checkedIn = checkedIn;
				}, function () {					
			});
		};		
	});
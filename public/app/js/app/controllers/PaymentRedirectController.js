'use strict';

travelManagerApp.controller('PaymentRedirectController',
	function PaymentRedirectController($scope,$location,$window,$routeParams,$interval,authenticationService){
		var customerId=$location.search()['customerID'];
		var token=$location.search()['token'];
		$scope.processedValue = 50;
		console.log(authenticationService);
		
		authenticationService.updatePaymentToken(customerId, token).then(function(data) {					
			if (data==1){					
				console.log("Successfully updated package");
				$interval(
				function(){
				   $scope.processedValue = 100;
				   var url = '/App.html';			
				   //$location.path(url);
				$window.location.href = url;
				},2000);
					
			}
			else {
				console.log("Error");
			}
		});	
		
	});
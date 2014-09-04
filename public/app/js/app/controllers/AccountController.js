'use strict';

travelManagerApp.controller('AccountController',
	function AccountController($scope,$location,$routeParams,authenticationService,Session,growl){		
				
		authenticationService.getAccountInfo(Session.userId).then(function(data) {		
			$scope.customer = data;				
		});	
		
		
		$scope.hasCCInfo = function(){
			if ($scope.customer) {
				var ccinfo = $scope.customer.lastfourcc || '';
				return ccinfo != '';
			}
			else {
				return false;
			}			
		};
		
		$scope.editCC = function(){
			var url = '/account/creditcard';			
			$location.path(url);
		};
		
	});
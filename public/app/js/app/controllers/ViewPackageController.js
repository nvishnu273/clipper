'use strict';

travelManagerApp.controller('ViewPackageController',
	function ViewPackageController($scope,$location,$routeParams,packageCatalogService,Session,growl){		
		$scope.manageDayPlan = function(day){
			var url = '/packages/' + $routeParams.destination + '/' + $routeParams.packageId + '/' + day;			
			$location.path(url);
		};		
		packageCatalogService.getPackage($routeParams.destination, $routeParams.packageId).then(function(data) {		
			$scope.package = data;				
		});	
		
		$scope.isManager = function(){
			var userType = Session.userType || 0;			
			return Session.userType == 2;
		};
		
		$scope.isPending = function(){			
			return $scope.package.status == 'submitted';
		};
		
		$scope.editPackage = function(){
			var url = '/packages/' + $routeParams.destination + '/' + $routeParams.packageId + '/edit';			
			$location.path(url);
		};
		
		$scope.publishPackage = function(){
			packageCatalogService.publishPackage($routeParams.destination, $routeParams.packageId).then(function(data) {		
				$scope.package.status = 'published';
				var config = {};
				config.ttl=1000;					
				growl.addSuccessMessage("Successfully published package. ", config);
			});	
		};
	});
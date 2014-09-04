'use strict';

travelManagerApp.controller('PackageListPendingController',
	function PackageListController($scope, packageCatalogService, bookingsService, Session, growl, $location, $routeParams){				
		$scope.destination = "";
		$scope.nights = 1;
		$scope.orderByParam = "StartDate";	
		$scope.minCost=1000;
		$scope.maxCost=20000;
		
		$scope.seachPackages = function(searchPackageForm){				
			if (searchPackageForm.$valid) {
				packageCatalogService.searchPackage($scope.destination,$scope.nights,$scope.minCost,$scope.maxCost,'pending').then(function(data) {		
					$scope.packages = data;
				});			
			}
		};
		
	});
'use strict';

travelManagerApp.controller('EditPackageController',
	function EditPackageController($scope, $routeParams, packageCatalogService, $location){
		
		packageCatalogService.getPackage($routeParams.destination, $routeParams.packageId).then(function(data) {		
			$scope.package = data;				
			console.log($scope.package);
		});
		
		$scope.savePackage = function(editPackageForm){			
			if (editPackageForm.$valid) {												
				packageCatalogService.editPackage($scope.package).then(function(data) {				
					var url = '/packages/' + $scope.package.destination + '/' + $scope.package.packagecode;	
					console.log(data);
					$location.path(url);
				});			
			}
		};
		
		$scope.cancelPackage = function(){			
			var url = '/packages/' + $routeParams.destination + '/' + $routeParams.packageId;			
			$location.path(url);
		};	
	});
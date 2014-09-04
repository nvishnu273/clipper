'use strict';

eventsApp.controller('ViewPackageController',
	function PackageController($scope, packageCatalogService,$routeParams){		
		$scope.package = packageCatalogService.getPackage($routeParams.destination, $routeParams.packageId);		
	});
'use strict';

travelManagerApp.controller('PackageListController',
	function PackageListController($scope, packageCatalogService, bookingsService, Session, growl, $location, $routeParams){				
		$scope.destination = "";
		$scope.nights = 1;
		$scope.orderByParam = "StartDate";	
		$scope.minCost=1000;
		$scope.maxCost=20000;
		
		
		$scope.seachPackages = function(searchPackageForm){				
			if (searchPackageForm.$valid) {
				packageCatalogService.searchByDestination($scope.destination,$scope.nights,$scope.minCost,$scope.maxCost).then(function(data) {		
					$scope.packages = data.Result;
				});			
			}
		};
		
		$scope.createBooking = function(selectedPackage){
			bookingsService.createBooking(Session.userId,selectedPackage.destination,selectedPackage.packagecode).then(function(data) {		
					
					var config = {};
					config.ttl=1000;					
					growl.addSuccessMessage("Successfully created booking.", config);

					var url = '/booking/' + data[0].CustomerId + '/' + data[0].CustomerPackageId 
						+ '/' + data[0].StartDate;			
					
					$location.path(url);				
				});
		};
		
		$scope.isCustomer = function(){
			var userType = Session.userType || 0;
			return Session.userType == 0;
		};
						
		$scope.seeReview = function(tripPackage){
			packageCatalogService.getPackageReview(tripPackage.destination+"-"+tripPackage.packagecode).then(function(data) {	
				tripPackage.reviews=[];
				angular.forEach(data, function(value, key) {
				    this.push(value);				   
				}, tripPackage.reviews);
			});
		};
		
		$scope.getReviewId = function(destination,code){
			var key=destination.replace(' ', '')+'-'+code.replace(' ', '');
			return key;
		};
		
		$scope.filterByCost = function(minCost, maxCost){
			$scope.minCost=minCost;
			$scope.maxCost=maxCost;			
			packageCatalogService.searchPackageByPrice($scope.destination,$scope.minCost,$scope.maxCost).then(function(data) {	
				$scope.packages = data.Result;
			});
		};
		
		$scope.filterByHotelbrand = function(hotel){			
			packageCatalogService.searchPackageByHotelBrand($scope.destination,hotel).then(function(data) {	
				$scope.packages = data.Result;
			});
		};
	});
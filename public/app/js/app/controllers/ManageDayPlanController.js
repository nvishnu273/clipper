'use strict';

travelManagerApp.controller('ManageDayPlanController',
	function ManageDayPlanController($scope, packageCatalogService, googleApiService, $location, $routeParams, $route,growl){
		
		/*
		  $scope.rate = 7;
		  $scope.max = 10;
		  $scope.isReadonly = false;

		  $scope.hoveringOver = function(value) {			
			$scope.overStar = value;
			$scope.percent = 100 * (value / $scope.max);
		  };
		*/
  
		packageCatalogService.getPackage($routeParams.destination, $routeParams.packageId).then(function(data) {		
			$scope.name=data.name;
			$scope.destination=data.destination;
			$scope.packagecode=data.packagecode;
			$scope.planday = data.packageplan[$routeParams.day-1];

			if ($routeParams.day == 1){				
				if (!$scope.planday.startlocation) {
					googleApiService.getLatLongFromAddress(data.checkInHotelAddress).then(function(data) {
						//var startlocation = {"formatted_address" : data.checkInHotelAddress};
						$scope.planday.startlocation=data;	
					});					
				}				
			}
			else {								
				$scope.planday.startlocation=data.packageplan[$routeParams.day-2].endlocation;
			}					
			$scope.searchNearByPlacesResult=[];
		});
		
		$scope.savePlanDay = function(plan, planForm){			
			if (planForm.$valid) {
				packageCatalogService.savePackageDayPlan($routeParams.destination, $routeParams.packageId, $scope.planday).then(function(data) {		
					var url = '/packages/' + $routeParams.destination + '/' + $routeParams.packageId;			
					console.log(data);
					$location.path(url);
				});
			}
		};
		
		$scope.cancelPlanDay = function(){			
			var url = '/packages/' + $routeParams.destination + '/' + $routeParams.packageId;			
			$location.path(url);
		};	
		
		$scope.isSearching=false;
		$scope.searchNearByPlaces = function(){					
			googleApiService.searchNearByPlaces($scope.planday.startlocation.lat, $scope.planday.startlocation.lng).then(function(data) {		
					console.log(data);
					$scope.isSearching=true;
					$scope.searchNearByPlacesResult=data;
				});
		};
		$scope.doneSearchNearByPlaces = function(){					
			$scope.isSearching=false;
		};
		
		$scope.addLocationToPlanDay = function(place){	
			console.log(place);
			packageCatalogService.addDayPlanVisitingPlace($routeParams.destination, $routeParams.packageId, $scope.planday.night, 
				place).then(function(packageplan) {		
					$scope.planday.visitingplaces=packageplan[$routeParams.day-1].visitingplaces;
					var config = {};
					config.ttl=1000;					
					growl.addSuccessMessage("Successfully added " + place.name, config);
				}, function(reason){
					console.log(reason);
				});
		};
		
		$scope.removeLocationFromPlanDay = function(place){			
			packageCatalogService.removeDayPlanVisitingPlace($routeParams.destination, $routeParams.packageId, $scope.planday.night, 
				place).then(function(packageplan) {		
					$scope.planday.visitingplaces=packageplan[$routeParams.day-1].visitingplaces;					
					var config = {};
					config.ttl=1000;					
					growl.addSuccessMessage("Successfully removed " + place.name, config);
				}, function(reason){
					console.log(reason);
				});
		};
		
	});
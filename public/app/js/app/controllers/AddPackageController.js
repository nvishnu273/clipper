'use strict';

travelManagerApp.controller('AddPackageController',
	function AddPackageController($scope, packageCatalogService, googleApiService, $location,growl){
		
		$scope.code="2014-SUMMER";
		$scope.customcode="";
		$scope.selectedHotel=null;
		
		var readHotelSource = new kendo.data.DataSource({
			serverFiltering: true,	
			transport: {
				read:'http://54.209.199.253/auto-complete.php',				
				parameterMap: function (data,action) {				
					return {
						type: 'hotel',
						name: data.filter.filters[0].value
					};
				}			   
			}
		});
		
		$scope.hotelDs = readHotelSource;
		
		var readAirportCodeSource = new kendo.data.DataSource({
			serverFiltering: true,			
			transport: {
				read:'http://54.209.199.253/auto-complete.php',				
				parameterMap: function (options) {
					return {
						type: 'airport',
						name: options.filter.filters[0].value 
					};
				}			   
			}
		});
		
		$scope.airportIataCodeDs = readAirportCodeSource;

			
		packageCatalogService.getNewPackage().then(function(data) {		
			$scope.package=data;
			$scope.package.status="submitted";
		});
		
		$scope.savePackage = function(newPackageForm){			
			if (newPackageForm.$valid) {						
				$scope.package.packagecode = $scope.code + "-" + $scope.customcode;					
				packageCatalogService.createPackage($scope.package).then(function(data) {					
					$location.path(data.resouceUrl);
				});			
			}
		};
		
	});
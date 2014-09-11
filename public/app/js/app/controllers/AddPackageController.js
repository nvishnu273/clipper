'use strict';

travelManagerApp.controller('AddPackageController',
	function AddPackageController($scope, packageCatalogService, googleApiService, $location, growl, Session){
		
		$scope.code="2014-SUMMER";
		$scope.customcode="";
		$scope.selectedHotel=null;
		
		$scope.datePickerConfig = {		  
		  format : "MM/dd/yyyy"
		};

		var readHotelSource = new kendo.data.DataSource({
			serverFiltering: true,	
			transport: {
				read:'/autocomplete/hotel',				
				parameterMap: function (data,action) {				
					return {						
						text: data.filter.filters[0].value						
					};
				}			   
			}
		});
		
		$scope.hotelDs = readHotelSource;
		
		var readAirportCodeSource = new kendo.data.DataSource({
			serverFiltering: true,			
			transport: {
				read:'/autocomplete/city',					
				parameterMap: function (options) {
					return {						
						text: options.filter.filters[0].value		
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
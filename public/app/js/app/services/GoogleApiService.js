'use strict';

travelManagerApp.factory('googleApiService',
	function ($http, $q){
		return {
				
				searchNearByPlaces : function(lat,lng) {							
					var deferered = $q.defer();
					var url='/geocode/nearby?val='+lat+','+lng;
					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				
				getLatLongFromAddress : function(address) {
					var deferered = $q.defer();
					var url='/geocode/address?val='+address;
					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},

				getAddressFromLatLong : function(lat,lng) {
					var deferered = $q.defer();
					var url='http://54.209.199.253/get-address-from-location.php?lat='+lat+'&lng='+lng;
					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				
				getAirportCodes : function(city) {
					var deferered = $q.defer();
					var url='http://54.209.199.253/get-airport-code.php?city='+city;
					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				}

				
			}
	});	
'use strict';

travelManagerApp.factory('packageCatalogService',
	function ($http, $q){
		return {
				
				getNewPackage : function() {							
					
					var deferered = $q.defer();
					var url='/package/search/new';
					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
						return deferered.promise;
				},
				
				createPackage : function(packageData) {							
					
					var deferered = $q.defer();					
					var stringPackageData = JSON.stringify(packageData);
					$http({
							method: 'POST', 
							url:'/package',
							data: stringPackageData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				
				getPackage : function(destination,packageCode) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/package/'+destination+'-'+packageCode})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},

				editPackage : function(packageData) {							
					
					var deferered = $q.defer();					
					var stringPackageData = JSON.stringify(packageData);
					$http({
							method: 'PUT', 
							url:'/package/'+packageData.destination+'-'+packageData.packagecode,
							data: packageData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				
				publishPackage : function(destination,packagecode) {							
					
					var deferered = $q.defer();					
					var data={};
					data.destination = destination;
					data.packagecode = packagecode;					
					var stringPostData = JSON.stringify(data);
					$http({
							method: 'PUT', 
							url:'/package/'+destination+'-'+packagecode+'/publish',
							data: stringPostData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				

				searchPackage : function(destination,nights,minCost,maxCost,status) {
					var deferered = $q.defer();
					var url='/package/search/filterByStatus?destination='+destination+'&minnights='+nights+'&minCost='+minCost+'&maxCost='+maxCost;
					if (status){
						url = url+'&status='+status;
					}
					
					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
						return deferered.promise;
				},

				searchByDestination : function(destination,nights,minCost,maxCost,status) {
					var deferered = $q.defer();
					var url='/package/search/filterByDestination?destination='+destination+'&minCost='+minCost+'&maxCost='+maxCost;
					if (status){
						url = url+'&status='+status;
					}
					
					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
						return deferered.promise;
				},

				searchPackageByHotelBrand : function(destination,brand) {
					var deferered = $q.defer();
					var url='/package/search/filterByBrand?destination='+destination+'&brand='+brand;

					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
						return deferered.promise;
				},

				searchPackageByPrice : function(destination,minCost,maxCost) {
					var deferered = $q.defer();
					var url='/package/search/filterByPrice?destination='+destination+'&minCost='+minCost+'&maxCost='+maxCost;

					$http({method: 'GET', url:url})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
						return deferered.promise;
				},

				savePackageDayPlan : function(destination,packageCode,plan) {												
					var deferered = $q.defer();	
					plan.destination = destination;
					plan.packagecode = packageCode;
					var stringPackageData = JSON.stringify(plan);
					console.log(plan.night);
					$http({
							method: 'PUT', 
							url:'/package/day/'+plan.night,
							data: stringPackageData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				
				addDayPlanVisitingPlace : function(destination,packageCode,night,place,action) {												
					var deferered = $q.defer();	
					var data={};
					data.destination = destination;
					data.packagecode = packageCode;
					data.night = night;
					data.place = place;
					data.action = action;
					var stringPostData = JSON.stringify(data);
					$http({
							method: 'POST', 
							url:'/package/day/'+night+'/location',
							data: stringPostData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},

				removeDayPlanVisitingPlace : function(destination,packageCode,night,place,action) {												
					var deferered = $q.defer();	
					var data={};
					data.destination = destination;
					data.packagecode = packageCode;
					data.night = night;
					data.place = place;
					data.action = action;
					var stringPostData = JSON.stringify(data);
					$http({
							method: 'DELETE', 
							url:'/package/day/'+night+'/location/'+place.map.center.latitude+','+place.map.center.longitude,
							data: stringPostData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},				
				
				getPackageReview: function(packageId) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/package/search/reviews?packageId='+packageId})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},
				
				getNotifications : function(userType, id) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/account/notification' + '?userType=' + userType + '&id=' + id})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				}
			}
	});	
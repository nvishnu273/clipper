'use strict';

travelManagerApp.factory('tripService',
	function ($http, $q, $resource){
		
		return {
				
				getTripDetail: function(tripId,startDate) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/trip/'+tripId+'?StartDate='+startDate})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},
				
				getTripPhoto: function(tripId,photoId) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/trip/' + tripId + '/photo/get?id='+photoId})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},
				
				addTripReview : function(location,tripId,firstname,lastname,customerID,rating,comment,placeName) {												
					var deferered = $q.defer();	
					var postData={};					
					postData.location=location;
					postData.tripId=tripId;
					postData.firstname=firstname;					
					postData.lastname=lastname;
					postData.customerID=customerID;
					postData.rating=rating;
					postData.comment=comment;
					postData.name=placeName;
					var stringPostData = JSON.stringify(postData);
					$http({
						method: 'POST', 
						url:'/trip/'+tripId+'/review/add',
						data: stringPostData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				searchReviews: function(address,radial) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/package/search/geoReview?address='+address+'&radial='+radial})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				}
			}
	});	
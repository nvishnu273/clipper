'use strict';

travelManagerApp.factory('bookingsService',
	function ($http, $q, $resource){
		
		return {
				
				createBooking : function(customerId, destination, packagecode) {
					var deferered = $q.defer();
					var postData={};					
					postData.customerid=customerId;
					postData.destination=destination;
					postData.packagecode=packagecode;					
					var stringPostData = JSON.stringify(postData);
					$http({
							method: 'POST', 
							url:'/booking',
							data: stringPostData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				
				getBookings : function(start, end) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/booking/all/search/date?start='+start+'&end='+end+'&status=' + 'PendingCheckin'})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},

				getCustomerBookings : function(customerId) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/booking/all/search/customer?id='+customerId})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},
				
				getCustomerBooking : function(bookingId) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/booking/'+bookingId})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},
				
				getMyAssignments : function(agentId) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/booking/all/search/agent?id='+agentId+'&status=PendingCheckIn'})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},
				
				getBookingDetail: function(customerId,packageCode,startDate) {
					var deferered = $q.defer();
					$http({method: 'GET', url:'/booking/all/search/key?customerId='+customerId
					+'&packageId='+packageCode+'&startDate='+startDate})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});					
					return deferered.promise;									
				},
				
				assignGuide : function(userId,bookingId) {							
					
					var deferered = $q.defer();	
					var postData={};																		
					postData.AgentId=userId;
					var stringPostData = JSON.stringify(postData);
					$http({
						method: 'POST', 
						url:'/booking/'+bookingId+'/agent/assign',
						data: stringPostData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				},
				
				checkInCustomer : function(checkInTime,bookingId) {												
					var deferered = $q.defer();	
					var postData={};					
					
					postData.CheckInTime=checkInTime;
					
					var stringPostData = JSON.stringify(postData);
					$http({
						method: 'POST', 
						url:'/booking/'+bookingId+'/agent/checkIn',
						data: stringPostData
						})
						.success(function(data,status,headers,config) {							
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;
				}
			}
	});	
'use strict';

	
travelManagerApp.factory('authenticationService',
	function ($http, $q, Session){
		return {		
				createNewCustomer: function (customer) {				  
					var deferered = $q.defer();														
					$http({
							method: 'POST', 
							url:'http://54.209.199.253/register-customer.php',
							data: customer
					})
					.success(function(data,status,headers,config) {	
						Session.create(data.id, data.user.id, data.user.role, data.user.userType);
						deferered.resolve(data);
					}).error(function(data,status,headers,config) {
						deferered.reject(status);
					});
					return deferered.promise;				  
				},
				updatePaymentToken: function (customerID,token) {				  
					var deferered = $q.defer();					
					var data={};
					data.customerID = customerID;
					data.token = token;					
					var stringPostData = JSON.stringify(data);	
					$http({
							method: 'POST', 
							url:'http://54.209.199.253/register-customer.php',
							data: customer
					})
					.success(function(data,status,headers,config) {							
						deferered.resolve(data);
					}).error(function(data,status,headers,config) {
						deferered.reject(status);
					});
					return deferered.promise;				  
				},
				login: function (credentials) {				  
					var deferered = $q.defer();														
					$http({
							method: 'POST', 
							url:'http://54.209.199.253/auth-service.php',
							data: credentials
						})
						.success(function(data,status,headers,config) {	
							Session.create(data.id, data.user.id, data.user.role, data.user.userType);
							deferered.resolve(data);
						}).error(function(data,status,headers,config) {
							deferered.reject(status);
						});
					return deferered.promise;				  
				},
				isAuthenticated: function () {					
				  return !!Session.userId;
				},
				isAuthorized: function (authorizedRoles) {				  
				  if (!angular.isArray(authorizedRoles)) {					
					authorizedRoles = [authorizedRoles];
				  }				  			 
				  return (this.isAuthenticated() &&
					authorizedRoles.indexOf(Session.userRole) !== -1);
				}
			}
	});	
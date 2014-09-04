'use strict';

travelManagerApp.controller('CreditCardController',
	function CreditCardController($scope,Session){
		$scope.redirectUrl="http://54.209.199.253/PaymentConfirm.html?customerID="+Session.userId;
	});
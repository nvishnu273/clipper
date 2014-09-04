'use strict';

eventsApp.factory('ExceptionHandler',
	function ($exceptionHandler){
		return function(exception){
			console.log("Exception Message: " + exception.message);
		}
	});	
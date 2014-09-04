'use strict';

/* Filters */
travelManagerApp.filter('durations', function(){
	return function(duration){
		switch(duration){
			case 1: 
				return "Half hour";
			case 2: 
				return "1 hour";
			case 3: 
				return "Half day";
			case 4: 
				return "Full hour";
		}
	}
});

travelManagerApp.filter('dateFormatter', function(){
	return function(inputDate){				
		var momentDate = moment(inputDate.date);		
		return momentDate.format("YYYY/MM/DD");
	}
});
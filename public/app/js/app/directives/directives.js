'use strict';

/* Directives */
travelManagerApp.directive('bootDate', function () {
	return {
        restrict: 'E',
		replace: true,
		transclude: true,
        scope: {        	
			date: '='
        },
        templateUrl: 'js/app/directives/bootstrap-datepicker-directive.html',
        link: function (scope, element, attribs) {
			$(element).bind('change', function() {				
                scope.$apply(read);
            });
			
			$(element).datepicker({
				format: "yyyy/mm/dd",
				startDate: "2012-01-01",
				endDate: "2015-01-01",
				todayBtn: "linked",
				autoclose: true,
				todayHighlight: true
			});
			var input = $(element).children()[0];
			
			function read() {
                scope.endDate = $(input).val();
				console.log(scope.endDate);
            }
        }
    }
});
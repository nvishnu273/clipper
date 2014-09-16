'use strict';

travelManagerApp.controller('ReviewListController',
	function ReviewListController($scope, tripService, bookingsService, Session, growl, $location, $routeParams){				
		$scope.address = "";
		$scope.location = 1;
		$scope.radial = false;
		$scope.seachReview = function(seachReviewForm){				
			if (seachReviewForm.$valid) {				
				tripService.searchReviews($scope.address,$scope.radial).then(function(data) {		
					$scope.reviews = data.Result;
				});			
			}
		};
		$scope.viewNearby = function(review){						
			tripService.searchReviews(review.Address,true).then(function(data) {		
				//$scope.reviews = data.Result;
				review.nearbyReviews = data.Result;
			});	
		};
	});
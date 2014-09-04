'use strict';

travelManagerApp.controller('TripDetailsController',
	function PackageController($scope,$upload,tripService,bookingsService,$routeParams){
		
		$scope.uploadData = {
			Address: '',
			LatLong: '',
			PackageId: ''
		};
		
		$scope.onFileSelect = function(place,$files) {
			$scope.uploadData.PackageId=$routeParams.tripId;
			$scope.uploadData.LatLong=place.map.center.latitude+","+place.map.center.longitude;
			$scope.uploadData.Address=place.vicinity;
			//$files: an array of files selected, each file has name, size, and type.
			for (var i = 0; i < $files.length; i++) {
			  var file = $files[i];
			  $scope.upload = $upload.upload({
				url: '/trip/' + $routeParams.tripId + '/upload', //upload.php script, node.js route, or servlet url
				// method: 'POST' or 'PUT',
				// headers: {'header-key': 'header-value'},
				// withCredentials: true,
				data: $scope.uploadData,
				file: file, // or list of files: $files for html5 only
				/* set the file formData name ('Content-Desposition'). Default is 'file' */
				//fileFormDataName: myFile, //or a list of names for multiple files (html5).
				/* customize how data is added to formData. See #40#issuecomment-28612000 for sample code */
				//formDataAppender: function(formData, key, val){}
			  }).progress(function(evt) {
				console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
			  }).success(function(photo, status, headers, config) {
				// file is uploaded successfully
				
				tripService.getTripPhoto($routeParams.tripId,photo.photoId).then(function(data) {					
					place.Photos.push(data);			
				});	
			  });
			  //.error(...)
			  //.then(success, error, progress); 
			  //.xhr(function(xhr){xhr.upload.addEventListener(...)})// access and attach any event listener to XMLHttpRequest.
			}
			/* alternative way of uploading, send the file binary with the file's content-type.
			   Could be used to upload files to CouchDB, imgur, etc... html5 FileReader is needed. 
			   It could also be used to monitor the progress of a normal http post/put request with large data*/
			// $scope.upload = $upload.http({...})  see 88#issuecomment-31366487 for sample code.
		};
  
		tripService.getTripDetail($routeParams.tripId,$routeParams.start).then(function(data) {				
			$scope.trip = data;			
		});	
		
		$scope.comment="";
		$scope.rating=4;
		$scope.addTripReview = function(place,parent) {	
			console.log(parent.comment);
			tripService.addTripReview(place.map.center.latitude+","+place.map.center.longitude,$routeParams.tripId,$scope.trip.customer.FirstName,
				$scope.trip.customer.LastName,
				$scope.trip.customer.CustomerId,
				$scope.rating,
				parent.comment).then(function(data) {	
					parent.comment="";
					place.Reviews.push(data);
			});	
		};
		
		$scope.getUploadAnchorUrl = function(place) {
			return $location.path() + "/#" + place.id;
		};
	});
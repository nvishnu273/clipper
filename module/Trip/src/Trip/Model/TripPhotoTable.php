<?php
namespace Trip\Model;


	class TripPhotoTable
	{
		protected $s3TripPhotoTableGateway;
		protected $dynamoDbTripPhotoByLocationTableGateway;
		protected $dynamoDbTripPhotoByPackageTableGateway;

		public function __construct(S3TripPhotoTableGateway $s3TripPhotoTableGateway,
			DynamoDbTripPhotoByLocationTableGateway $dynamoDbTripPhotoByLocationTableGateway,
			DynamoDbTripPhotoByPackageTableGateway $dynamoDbTripPhotoByPackageTableGateway)
		{
			$this->s3TripPhotoTableGateway = $s3TripPhotoTableGateway;
			$this->dynamoDbTripPhotoByLocationTableGateway = $dynamoDbTripPhotoByLocationTableGateway;
			$this->dynamoDbTripPhotoByPackageTableGateway = $dynamoDbTripPhotoByPackageTableGateway;
		}

		public function create($packageId,$location,$locationName,$uploadDate,$pathToFile,$photoname)
		{
			$fileName = $this->s3TripPhotoTableGateway->uploadPhoto($packageId,$locationName,$pathToFile,$photoname);
			$this->dynamoDbTripPhotoByLocationTableGateway->put_item($location,$uploadDate,$fileName);
			$this->dynamoDbTripPhotoByPackageTableGateway->put_item($packageId,$fileName,$location);
			return $fileName;
		}

		public function fetch($photoId)
		{			
			$photo = $this->s3TripPhotoTableGateway->getPhotoPlainUrl($photoId);						
			if (!$photo) {
			 throw new \Exception("Could not find photo $photoId");
			}
			return $photo;
		}

		public function fetchPreSigned($photoId)
		{			
			$photo = $this->s3TripPhotoTableGateway->getPhotoPreSignedUrl($packageId);						
			
			return $photo;
		}

		public function fetchPhotoObject($photoId)
		{			
			$photo = $this->s3TripPhotoTableGateway->getPhoto($photoId);						
			if (!$photo) {
			 throw new \Exception("Could not find photo $photoId");
			}
			return $photo;
		}		

		public function fetchAllByPackageId($packageId)
		{						
			$photo = $this->dynamoDbTripPhotoByPackageTableGateway->getTripPhotosByPacakgeId($packageId);						
			
			return $photo;
		}
	}
 ?>
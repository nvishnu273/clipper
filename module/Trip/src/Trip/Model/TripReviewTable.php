<?php
namespace Trip\Model;


	class TripReviewTable
	{
		
		protected $dynamoDbTripReviewTableGateway;

		public function __construct(DynamoDbTripReviewTableGateway $dynamoDbTripReviewTableGateway)
		{			
			$this->dynamoDbTripReviewTableGateway = $dynamoDbTripReviewTableGateway;
		}

		public function addReview($location,$reviewDate,$comment,$reviewBy,$reviewById,$packageId,$rating)
		{			
			$review = $this->dynamoDbTripReviewTableGateway->addReview($location,$reviewDate,$comment,$reviewBy,
				$reviewById,$packageId,$rating);						
			
			return $review;
		}

		public function getTripReview($locationValue,$reviewDate)
		{			
			$review = $this->dynamoDbTripReviewTableGateway->getTripReview($locationValue,$reviewDate);						
			
			return $review;
		}

		public function getTripReviewsByLocation($locationValue)
		{			
			$review = $this->dynamoDbTripReviewTableGateway->getTripReviewsByLocation($locationValue);						
			
			return $review;
		}

		public function getTripReviewsByLocation2($locationValue,$locationAddress)
		{			
			$review = $this->dynamoDbTripReviewTableGateway->getTripReviewsByLocation2($locationValue,$locationAddress);						
			
			return $review;
		}
	}
 ?>
<?php



namespace Trip\Model;

use Aws\Common\Enum\Region;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\ReturnValue;

class DynamoDbTripReviewTableGateway
{
	protected $tableName;
	protected $dynamoDbClient;
	
	public function __construct($tableName,$dynamoDbClient)
	{				
		$this->tableName = $tableName;
		$this->dynamoDbClient = $dynamoDbClient;				
	}
	
	public function addReview($location,$reviewDate,$comment,$reviewBy,$reviewById,$packageId,$rating){		
				
		$response=$this->dynamoDbClient->putItem(array(
			"TableName"=>$this->tableName,
			"Item"=>array(
				"Location"=>array(Type::STRING=>$location),				
				"ReviewDate"=>array(Type::STRING=>$reviewDate),		
				"Comment"=>array(Type::STRING=>$comment),		
				"ReviewBy"=>array(Type::STRING=>$reviewBy),
				"ReviewById"=>array(Type::STRING=>$reviewById),
				"PackageId"=>array(Type::STRING=>$packageId),
				"Rating"=>array(Type::STRING=>$rating)
			)
		));
				
		return $response;
	}
	
	
	public function getTripReviewsByLocation($location){
		
		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"KeyConditions"=>array(
				"Location"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$location)
					)
				)
			)
		));
		
		$items_iterator = $response['Items'];	
		$reviews=[];
		
		foreach($items_iterator as $item){		
			if ((isset($item["Location"]))) {								
				$location = $item["Location"]["S"];		
			}
			if ((isset($item["ReviewDate"]))) {	
				$reviewDate = $item["ReviewDate"]["S"];				
			}
			if ((isset($item["Comment"]))) {
				$comment = $item["Comment"]["S"];				
			}
			if ((isset($item["ReviewBy"]))) {
				$reviewBy = $item["ReviewBy"]["S"];				
			}
			if ((isset($item["ReviewById"]))) {	
				$reviewById = $item["ReviewById"]["S"];					
			}
			if ((isset($item["PackageId"]))) {	
				$packageId = $item["PackageId"]["S"];				
			}
			if ((isset($item["Rating"]))) {	
				$rating = $item["Rating"]["S"];				
			}
			$reviews[]=array(
							"Location"=>$location,
							"ReviewDate"=>$reviewDate,
							"Comment"=>$comment,
							"ReviewBy"=>$reviewBy,
							"ReviewById"=>$reviewById,
							"PackageId"=>$packageId,
							"Rating"=>$rating
						);
		}
		
		return $reviews;

	}
	
	public function getTripReviewsByLocation2($location,$locationAddress){
				
			

		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"KeyConditions"=>array(
				"Location"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$location)
					)
				)
			)
		));
		
		$items_iterator = $response['Items'];	
		$reviews=[];
		
		foreach($items_iterator as $item){	
			
			if ((isset($item["Location"]))) {								
				$location = $item["Location"]["S"];		
			}
			if ((isset($item["ReviewDate"]))) {	
				$reviewDate = $item["ReviewDate"]["S"];				
			}
			if ((isset($item["Comment"]))) {
				$comment = $item["Comment"]["S"];				
			}
			if ((isset($item["ReviewBy"]))) {
				$reviewBy = $item["ReviewBy"]["S"];				
			}
			if ((isset($item["ReviewById"]))) {	
				$reviewById = $item["ReviewById"]["S"];					
			}
			if ((isset($item["PackageId"]))) {	
				$packageId = $item["PackageId"]["S"];				
			}
			if ((isset($item["Rating"]))) {	
				$rating = $item["Rating"]["S"];				
			}
			$reviews[]=array(
							"Location"=>$location,
							"Address"=>$locationAddress,
							"ReviewDate"=>$reviewDate,
							"Comment"=>$comment,
							"ReviewBy"=>$reviewBy,
							"ReviewById"=>$reviewById,
							"PackageId"=>$packageId,
							"Rating"=>$rating
						);
		}
		
		return $reviews;

	}
	
	public function getTripReview($location,$reviewDate){		

		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"KeyConditions"=>array(
				"Location"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$location)
					)
				),
				"ReviewDate"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$reviewDate)
					)
				)
			)
		));
	
		$review=array(
					"Location"=>$response['Items'][0]["Location"]["S"],
					"ReviewDate"=>$response['Items'][0]["ReviewDate"]["S"],
					"Comment"=>$response['Items'][0]["Comment"]["S"],
					"ReviewBy"=>$response['Items'][0]["ReviewBy"]["S"],
					"ReviewById"=>$response['Items'][0]["ReviewById"]["S"],
					"PackageId"=>$response['Items'][0]["PackageId"]["S"],
					"Rating"=>$response['Items'][0]["Rating"]["S"]
				);
			
		return $review;		
	}			
	
	public function  getAllReviews(){

		$response=$this->dynamoDbClient->scan(array(
			"TableName"=>$this->tableName,
		));
		
		$items_iterator = $response['Items'];	
		$reviews=[];
		
		foreach($items_iterator as $item){		
			if ((isset($item["Location"]))) {								
				$location = $item["Location"]["S"];		
			}
			if ((isset($item["ReviewDate"]))) {	
				$reviewDate = $item["ReviewDate"]["S"];				
			}
			if ((isset($item["Comment"]))) {
				$comment = $item["Comment"]["S"];				
			}
			if ((isset($item["ReviewBy"]))) {
				$reviewBy = $item["ReviewBy"]["S"];				
			}
			if ((isset($item["ReviewById"]))) {	
				$reviewById = $item["ReviewById"]["S"];					
			}
			if ((isset($item["PackageId"]))) {	
				$packageId = $item["PackageId"]["S"];				
			}
			if ((isset($item["Rating"]))) {	
				$rating = $item["Rating"]["S"];				
			}
			$reviews[]=array(
							"Location"=>$location,							
							"ReviewDate"=>$reviewDate,
							"Comment"=>$comment,
							"ReviewBy"=>$reviewBy,
							"ReviewById"=>$reviewById,
							"PackageId"=>$packageId,
							"Rating"=>$rating
						);
		}
		
		return $reviews;		
	}
}


	
?>

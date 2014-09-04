<?php



namespace Trip\Model;

use Aws\Common\Enum\Region;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\ReturnValue;

class DynamoDbTripPhotoByPackageTableGateway
{
	protected $tableName;
	protected $dynamoDbClient;
	
	public function __construct($tableName,$dynamoDbClient)
	{				
		$this->tableName = $tableName;
		$this->dynamoDbClient = $dynamoDbClient;				
	}
	
	public function put_item($packageId,$fileName,$location){		
														
		$response=$this->dynamoDbClient->putItem(array(
			"TableName"=>$this->tableName,
			"Item"=>array(
				"CustomerPackageId"=>array(Type::STRING=>$packageId),
				"PhotoId"=>array(Type::STRING=>$fileName),			
				"Location"=>array(Type::STRING=>$location)
			)
		));
				
	}
	
	public function getTripPhotosByPacakgeId($customerPackageId){
		//var_dump($this->dynamoDbClient);
		
		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"KeyConditions"=>array(
				"CustomerPackageId"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$customerPackageId)
					)
				)
			)
		));

		
		$items_iterator = $response['Items'];	
		$photos=[];
		
		foreach($items_iterator as $item){		
			if ((isset($item["Location"]))) {								
				$photos[$item["PhotoId"]["S"]] = array("Location"=>$item["Location"]["S"]);		
			}
		}
		
		return $photos;
	
	}
}


	
?>

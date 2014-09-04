<?php



namespace Trip\Model;

use Aws\Common\Enum\Region;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\ReturnValue;

class DynamoDbTripPhotoByLocationTableGateway
{
	protected $tableName;
	protected $dynamoDbClient;
	
	public function __construct($tableName,$dynamoDbClient)
	{				
		$this->tableName = $tableName;
		$this->dynamoDbClient = $dynamoDbClient;
	}
	
	public function put_item($location,$uploadDate,$fileName){		
												
		$response=$this->dynamoDbClient->putItem(array(
			"TableName"=>$this->tableName,
			"Item"=>array(
				"Location"=>array(Type::STRING=>$location),
				"UploadDate"=>array(Type::STRING=>$uploadDate),			
				"PhotoId"=>array(Type::STRING=>$fileName)
			)
		));				
				
	}
	
}


	
?>

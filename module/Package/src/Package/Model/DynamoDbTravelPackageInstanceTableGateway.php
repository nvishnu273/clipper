<?php



namespace Package\Model;

use Aws\Common\Enum\Region;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\ReturnValue;

use Package\Model\TravelPackageInstance;

class DynamoDbTravelPackageInstanceTableGateway
{
	protected $tableName;
	protected $dynamoDbClient;
	
	public function __construct($tableName,$dynamoDbClient)
	{				
		$this->tableName = $tableName;
		$this->dynamoDbClient = $dynamoDbClient;
	}
	
	public function put_package_item($customerPackageId,$startDate,$assignedTo,$package)
	{		

		$packageplanjson=json_encode($package->packageplan);
		
		$response=$this->dynamoDbClient->putItem(array(
			"TableName"=>$this->tableName,
			"Item"=>array(
				"PackageId"=>array(Type::STRING=>$customerPackageId),
				"StartDate"=>array(Type::STRING=>$startDate),
				"Name"=>array(Type::STRING=>$package->name),
				"Destination"=>array(Type::STRING=>$package->destination),
				"Airport"=>array(Type::STRING=>$package->airport),
				"CheckInTime"=>array(Type::STRING=>$package->checkInTime),
				"CheckInHotel"=>array(Type::STRING=>$package->checkInHotel),
				"CheckInHotelAddress"=>array(Type::STRING=>$package->checkInHotelAddress),
				"Nights"=>array(Type::NUMBER=>$package->nights),
				"Cost"=>array(Type::NUMBER=>$package->cost),				
				"End"=>array(Type::STRING=>$package->end->format('Y-m-d')),
				"PackagePlan"=>array(Type::STRING=>$packageplanjson),				
				"AssignedTo"=>array(Type::STRING=>$assignedTo),
				"Status"=>array(Type::STRING=>$package->status)
			)
		));
		
		return $response;
	}
	
	public function query_package($packageId,$startDate)
	{		
		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"KeyConditions"=>array(
				"PackageId"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$packageId)
					)
				),
				"StartDate"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$startDate)
					)
				)
			)
		));
		
		$package=new TravelPackageInstance();


		if (isset($response['Items'][0])){	
			$package->packagecode=$response['Items'][0]["PackageId"]["S"];
			$package->name=$response['Items'][0]["Name"]["S"];
			$package->destination=$response['Items'][0]["Destination"]["S"];
			$package->airport=$response['Items'][0]["Airport"]["S"];
			$package->checkInTime=$response['Items'][0]["CheckInTime"]["S"];
			$package->checkInHotel=$response['Items'][0]["CheckInHotel"]["S"];
			$package->checkInHotelAddress=$response['Items'][0]["CheckInHotelAddress"]["S"];
			$package->cost=$response['Items'][0]["Cost"]["N"];
			$package->nights=$response['Items'][0]["Nights"]["N"];
			$package->start=new \DateTime($response['Items'][0]["StartDate"]["S"]);
			$package->end=new \DateTime($response['Items'][0]["End"]["S"]);
			if (isset($response['Items'][0]["Status"])){
				$package->status=$response['Items'][0]["Status"]["S"];
			}
			else {
				$package->status="Checked-In";
			}
			
			$package->packageplan=json_decode($response['Items'][0]["PackagePlan"]["S"]);
		}
		
		return $package;
	}
	

	
	function getCustomerPackagesBulk($customerPackageKeys){				
		
		foreach($customerPackageKeys as $key=>$value){
			$keys[] = array(
				'PackageId' => array('S' => $value['PackageId']),
				'StartDate' => array('S' => $value['StartDate'])
			);
		}
		$attributesToGet=array('PackageId','StartDate','PackagePlan');
		
		$items = $this->dynamoDbClient->getIterator('BatchGetItem',array(
			'RequestItems' => array(
				$this->tableName => array(
					'Keys' => $keys,
					'ConsistentRead' => true,
					'AttributesToGet' => $attributesToGet
				)
			)
		));
		
		$packages=[];
		
		foreach ($items as $item) {
			//var_dump($item);
			$package=new TravelPackageInstance();
			
			$package->packagecode=$item["PackageId"]["S"];
			$package->start=new \DateTime($item["StartDate"]["S"]);				
			$package->packageplan=json_decode($item["PackagePlan"]["S"]);
			$packages[] = $package;
		}
		
		return $packages;

	}	
    
}


	
?>

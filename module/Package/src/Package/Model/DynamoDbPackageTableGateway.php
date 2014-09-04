<?php



namespace Package\Model;

use Aws\Common\Enum\Region;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\ReturnValue;

use Package\Model\Package;

class DynamoDbPackageTableGateway
{
	protected $tableName;
	protected $dynamoDbClient;
	
	public function __construct($tableName,$dynamoDbClient)
	{				
		$this->tableName = $tableName;
		$this->dynamoDbClient = $dynamoDbClient;
	}
	
	public function put_package_item($package)
	{		
		$packageplanjson=json_encode($package->packageplan);
		
		$response=$this->dynamoDbClient->putItem(array(
			"TableName"=>$this->tableName,
			"Item"=>array(
				"PackageCode"=>array(Type::STRING=>$package->packagecode),
				"Name"=>array(Type::STRING=>$package->name),
				"Destination"=>array(Type::STRING=>$package->destination),
				"Airport"=>array(Type::STRING=>$package->airport),
				"CheckInTime"=>array(Type::STRING=>$package->checkInTime),
				"CheckInHotel"=>array(Type::STRING=>$package->checkInHotel),
				"CheckInHotelAddress"=>array(Type::STRING=>$package->checkInHotelAddress),
				"Nights"=>array(Type::NUMBER=>$package->nights),
				"Cost"=>array(Type::NUMBER=>$package->cost),
				"Start"=>array(Type::STRING=>$package->start->format('Y-m-d')),
				"End"=>array(Type::STRING=>$package->end->format('Y-m-d')),
				"PackagePlan"=>array(Type::STRING=>$packageplanjson),
				"Status"=>array(Type::STRING=>$package->status)
			)
		));
		
		return $response;
	}
	
	public function query_package($destination,$packagecode)
	{		
		
		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"KeyConditions"=>array(
				"Destination"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$destination)
					)
				),
				"PackageCode"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$packagecode)
					)
				)
			)
		));

		$package=new Package();		
		$package->packagecode=$packagecode;
		$package->name=$response['Items'][0]["Name"]["S"];
		$package->destination=$destination;

		$package->airport=$response['Items'][0]["Airport"]["S"];
		$package->checkInTime=$response['Items'][0]["CheckInTime"]["S"];
		$package->checkInHotel=$response['Items'][0]["CheckInHotel"]["S"];
		$package->checkInHotelAddress=$response['Items'][0]["CheckInHotelAddress"]["S"];
		$package->cost=$response['Items'][0]["Cost"]["N"];
		$package->nights=$response['Items'][0]["Nights"]["N"];			
		$package->start=new \DateTime($response['Items'][0]["Start"]["S"]);
		$package->end=new \DateTime($response['Items'][0]["End"]["S"]);
		$package->status=$response['Items'][0]["Status"]["S"];
		$package->packageplan=json_decode($response['Items'][0]["PackagePlan"]["S"]);
		
		return $package;
	}
	
	public function query_by_index_getMostPouplarPackages($page,$count)
	{	
		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"IndexName"=>"NightsIndex",
			"Select"=>"ALL_ATTRIBUTES",
			"KeyConditions"=>array(
				"Destination"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$destination)
					)
				),
				"Nights"=>array(
					"ComparisonOperator"=>ComparisonOperator::GT,
					"AttributeValueList"=>array(
						array(Type:: NUMBER=>$nights)
					)
				),
			)
		));
		$packages=[];
		$items_iterator = $response['Items'];	
		foreach($items_iterator as $item){
		
			if ((isset($item["Status"])) && ($item["Status"]["S"] === "submitted")) {
				
				$package=new Package();
				$package->packagecode=$item["PackageCode"]["S"];
				$package->name=$item["Name"]["S"];
				$package->destination=$item["Destination"]["S"];
				$package->airport=$item["Airport"]["S"];
				$package->checkInTime=$item["CheckInTime"]["S"];
				$package->checkInHotel=$item["CheckInHotel"]["S"];
				$package->checkInHotelAddress=$item["CheckInHotelAddress"]["S"];
				$package->cost=$item["Cost"]["N"];
				$package->nights=$item["Nights"]["N"];
				$package->start=new DateTime($item["Start"]["S"]);
				$package->end=new DateTime($item["End"]["S"]);
				$package->status=$item["Status"]["S"];

				$packages[] = $package;
				
			}
		}
		return $packages;		
	}


	/* search by local index */
	public function query_by_index_getpackagebydestinationnights($destination,$nights)
	{	
		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"IndexName"=>"NightsIndex",
			"Select"=>"ALL_ATTRIBUTES",
			"KeyConditions"=>array(
				"Destination"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$destination)
					)
				),
				"Nights"=>array(
					"ComparisonOperator"=>ComparisonOperator::GT,
					"AttributeValueList"=>array(
						array(Type:: NUMBER=>$nights)
					)
				),
			)
		));
		$packages=[];
		$items_iterator = $response['Items'];	
		foreach($items_iterator as $item){
		
			if ((isset($item["Status"])) && ($item["Status"]["S"] === "submitted")) {
				
				$package=new Package();
				$package->packagecode=$item["PackageCode"]["S"];
				$package->name=$item["Name"]["S"];
				$package->destination=$item["Destination"]["S"];
				$package->airport=$item["Airport"]["S"];
				$package->checkInTime=$item["CheckInTime"]["S"];
				$package->checkInHotel=$item["CheckInHotel"]["S"];
				$package->checkInHotelAddress=$item["CheckInHotelAddress"]["S"];
				$package->cost=$item["Cost"]["N"];
				$package->nights=$item["Nights"]["N"];
				$package->start=new \DateTime($item["Start"]["S"]);
				$package->end=new \DateTime($item["End"]["S"]);
				$package->status=$item["Status"]["S"];

				$packages[] = $package;
				
			}
		}
		return $packages;		
	}

	public function udpdate_package_item($destination,$packagecode,$package)
	{		
		$response=$this->dynamoDbClient->updateItem(array(
			"TableName"=>$this->tableName,
			"Key"=>array(
				"PackageCode"=>array(
					TYPE::STRING=>$packagecode
				),
				"Destination"=>array(
					TYPE::STRING=>$destination
				)
			),
			"AttributeUpdates"=>array(
				"Name"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"S"=>$package->name	
					)
				),
				"Airport"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"S"=>$package->airport	
					)
				),
				"CheckInTime"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"S"=>$package->checkInTime		
					)
				),
				"CheckInHotel"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"S"=>$package->checkInHotel
					)
				),
				"CheckInHotelAddress"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"S"=>$package->checkInHotelAddress
					)
				),
				"Nights"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"N"=>$package->nights
					)
				),
				"Cost"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"N"=>$package->cost
					)
				),
				"Start"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"S"=>$package->start->format('Y-m-d')
					)
				),
				"End"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"S"=>$package->end->format('Y-m-d')
					)
				),
				"Status"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"S"=>$package->status
					)
				)			
			)
		));
	}
	
	public function getAllRowsInPackage(){
		
		$response=$this->dynamoDbClient->scan(array(
			"TableName"=>$this->tableName,
		));
		
		$packages=[];
		$items_iterator = $response['Items'];	
		foreach($items_iterator as $item){
		
			if ((isset($item["Status"])) && ($item["Status"]["S"] === "published" || $item["Status"]["S"] === "submitted")) {
				
				$package=new Package();
				$package->packagecode=$item["PackageCode"]["S"];
				$package->name=$item["Name"]["S"];
				$package->destination=$item["Destination"]["S"];
				$package->airport=$item["Airport"]["S"];
				$package->checkInTime=$item["CheckInTime"]["S"];
				$package->checkInHotel=$item["CheckInHotel"]["S"];
				$package->checkInHotelAddress=$item["CheckInHotelAddress"]["S"];
				$package->cost=$item["Cost"]["N"];
				$package->nights=$item["Nights"]["N"];
				$package->start=new \DateTime($item["Start"]["S"]);
				$package->end=new \DateTime($item["End"]["S"]);
				$package->status=$item["Status"]["S"];

				$packages[] = $package;
				
			}
		}
		return $packages;
		
	}

	public function udpdate_package_plan_item($package)
	{
		$packageplanjson=json_encode($package->packageplan);
		
		$response=$this->dynamoDbClient->updateItem(array(
			"TableName"=>$this->tableName,
			"Key"=>array(
				"PackageCode"=>array(
					TYPE::STRING=>$package->packagecode
				),
				"Destination"=>array(
					TYPE::STRING=>$package->destination
				)
			),
			"AttributeUpdates"=>array(
				"PackagePlan"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						Type::STRING=>$packageplanjson			
					)
				)
			)
		));
	}
	
	/*
	function query_by_index_getpackagebydestinationstart($destination,$start,$end){
		
		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"IndexName"=>"DestinationByDateIndex",
			"Select"=>"ALL_ATTRIBUTES",
			"KeyConditions"=>array(
				"Destination"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$destination)
					)
				),
				"Start"=>array(
					"ComparisonOperator"=>ComparisonOperator::BETWEEN,
					"AttributeValueList"=>array(
						array(Type::STRING=>$start),
						array(Type::STRING=>$end)
					)
				),
			)
		));	
		
		$packages = [];
		$items_iterator = $response['Items'];	
		foreach($items_iterator as $item){
			
			if ((isset($item["Status"])) && ($item["Status"]["S"] === "Published")) {
				$package=new Package();
				$package->packagecode=$item["PackageCode"]["S"];
				$package->name=$item["Name"]["S"];
				$package->destination=$item["Destination"]["S"];
				$package->airport=$item["Airport"]["S"];
				$package->checkInTime=$item["CheckInTime"]["S"];
				$package->checkInHotel=$item["CheckInHotel"]["S"];
				$package->checkInHotelAddress=$item["CheckInHotelAddress"]["S"];
				$package->cost=$item["Cost"]["N"];
				$package->nights=$item["Nights"]["N"];
				$package->start=new DateTime($item["Start"]["S"]);
				$package->end=new DateTime($item["End"]["S"]);
				$package->status=$item["Status"]["S"];

				if (isset($item["PackagePlan"])) {
					$package->packageplan=json_decode($item["PackagePlan"]["S"]);			
				}
				
				$packages[] = $package;
			}
		}
		
		return $packages;
	}
	
	function query_by_index_getpackagedetail($packageid,$destination){
		
		$response=$this->dynamoDbClient->query(array(
			"TableName"=>$this->tableName,
			"IndexName"=>"PackageDetailIndex",
			"KeyConditions"=>array(
				"Id"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$packageid)
					)
				),
				"Destination"=>array(
					"ComparisonOperator"=>ComparisonOperator::EQ,
					"AttributeValueList"=>array(
						array(Type::STRING=>$destination)
					)
				)
			)
		));	
		foreach($items_iterator as $item){
			//echo $item["Name"]["S"] . "\n";
			print_r ($item["Name"]["S"]);
		}
		return $response['Items'];
	}
	

	
	
	public function udpdate_package_cost_item($destination,$packagecode,$newcost)
	{		
		$response=$this->dynamoDbClient->updateItem(array(
			"TableName"=>$this->tableName,
			"Key"=>array(
				"PackageCode"=>array(
					TYPE::STRING=>$packagecode
				),
				"Destination"=>array(
					TYPE::STRING=>$destination
				)
			),
			"AttributeUpdates"=>array(
				"Cost"=>array(
					"Action"=>AttributeAction::PUT,
					"Value"=>array(
						"N"=>$newcost		
					)
				)
			)
		));
	}
	
	
	
	public function delete_package_day_items_in_batch($packageid,$day)
	{		
		$response=$this->dynamoDbClient->batchWriteItem(
			array(	
				"RequestItems"=>array(
					$this->tableName=>array(
						array(
							"DeleteRequest"=>array(
								"Key"=>array(
									"Id"=>array(Type::STRING=>$packageid),
									"Day"=>array(Type::NUMBER=>$day)
								)
							)
						)
					)
				)
			)
		);
		return $response;	
	}
	
	public function deleteTable()
    {        
		$response=$this->dynamoDbClient->deleteTable(array("TableName"=>$this->tableName));
		return $response;
    }		
    */
}


	
?>

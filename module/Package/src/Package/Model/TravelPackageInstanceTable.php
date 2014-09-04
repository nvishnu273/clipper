<?php
namespace Package\Model;

use Package\Model\TravelPackageInstance;

	class TravelPackageInstanceTable
	{
		protected $travelPackageInstanceTableGateway;

		public function __construct(DynamoDbTravelPackageInstanceTableGateway $travelPackageInstanceTableGateway)
		{
			$this->travelPackageInstanceTableGateway = $travelPackageInstanceTableGateway;
		}

		public function createTravelPackageinstance($customerPackageId,$startDate,$assignedTo,$package)
		{
			$package = $this->travelPackageInstanceTableGateway->put_package_item($customerPackageId,$startDate,$assignedTo,$package);
			return $package;
		}


		public function getPackageInstance($packageId,$startDate)
		{			
			$package = $this->travelPackageInstanceTableGateway->query_package($packageId,$startDate);						
			if (!$package) {
			 throw new \Exception("Could not find package $id");
			}
			return $package;
		}

		public function getPackageInstances($customerPackageKeys)
		{			
			$package = $this->travelPackageInstanceTableGateway->query_package_bulk($customerPackageKeys);						
			if (!$package) {
			 throw new \Exception("Could not find package $id");
			}
			return $package;
		}

		public function getCustomerPackagesBulk($customerPackageKeys){
			return $this->travelPackageInstanceTableGateway->getCustomerPackagesBulk($customerPackageKeys);			
		}
	}
 ?>
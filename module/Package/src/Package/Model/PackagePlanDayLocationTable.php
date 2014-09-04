<?php
namespace Package\Model;


	class PackagePlanDayLocationTable
	{
		protected $packageTableGateway;

		public function __construct(DynamoDbPackageTableGateway $packageTableGateway)
		{
			$this->packageTableGateway = $packageTableGateway;
		}
	
		public function updatePlanPackage($destination,$packageCode,$packagedataobj,$package)
		{
			$day=$packagedataobj->night;
			$checkout=$packagedataobj->checkout;
			$checkin=$packagedataobj->checkin;

			$dayIndex = $day-1;
			$planItems=$package->packageplan;
			$planItems[$dayIndex]->checkout=$checkout;
			$planItems[$dayIndex]->checkin=$checkin;					
			$package->packageplan=$planItems;	

			$this->packageTableGateway->udpdate_package_plan_item($package);
			
			return $package;
		}

		public function deletePackagePlan($destination,$packagecode,$day)
		{
		 	$this->packageTableGateway->delete(array('Id' => (int) $id));
		}
	}
 ?>
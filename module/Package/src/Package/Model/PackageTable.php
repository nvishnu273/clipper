<?php
namespace Package\Model;

use Package\Model\Package;

	class PackageTable
	{
		protected $packageTableGateway;

		public function __construct(DynamoDbPackageTableGateway $packageTableGateway)
		{
			$this->packageTableGateway = $packageTableGateway;
		}

		public function getNewPackage()
		{			
			$package = new Package();
			$package->packageplan=[];
			return $package;
		}

		public function getPackage($destination,$packagecode)
		{			
					
			
			$package = $this->packageTableGateway->query_package($destination,$packagecode);	
				
			if (!$package) {
			 throw new \Exception("Could not find package $id");
			}
			return $package;
		}

		public function getMostRecent($page,$count)
		{
			$package = $this->packageTableGateway->query_by_index_getMostPouplarPackages($page,$count);
			return $package;
		}

		public function getPackageByNights($destination,$minNights)
		{			
			$package = $this->packageTableGateway->query_by_index_getpackagebydestinationnights($destination,$minNights);	
				
			if (!$package) {
			 throw new \Exception("Could not find package $id");
			}
			return $package;
		}

		public function getAllRowsInPackage()
		{								
			$package = $this->packageTableGateway->getAllRowsInPackage();	
				
			if (!$package) {
				throw new \Exception("Could not find package $id");
			}
			
			return $package;
		}

		public function createPackage($packagedataobj)
		{

			date_default_timezone_set('UTC');
			$newpackage=new Package();
			
			$airportParts = explode("-", $packagedataobj['airport']);
			$airportCode=trim($airportParts[0]);
			
			$checkInHotelAddressParts = explode(",", $packagedataobj['checkInHotel']);	
			$checkInHotel=trim($checkInHotelAddressParts[0]);
			$destination=trim($checkInHotelAddressParts[1]);
			
			$checkInHotelAddressArr=array_slice($checkInHotelAddressParts, 2);
			$checkInHotelAddress=trim(implode(",", $checkInHotelAddressArr));
			$packageCode=trim($destination.'-'.$airportCode.'-'.$packagedataobj['packagecode']);	
			
			$newpackage->packagecode=$packageCode;	
			$newpackage->name=$packagedataobj['name'];
			$newpackage->destination=$destination;	
			$newpackage->airport=$airportCode;
			$newpackage->checkInTime=$packagedataobj['checkInTime'];
			$newpackage->checkInHotel=$checkInHotelAddressParts[0];
			$newpackage->checkInHotelAddress=$checkInHotelAddress;
			$newpackage->cost=$packagedataobj['cost'];
			$newpackage->nights=$packagedataobj['nights'];		
			$newpackage->start=new \DateTime($packagedataobj['start']);
			$newpackage->end=new \DateTime($packagedataobj['end']);	
			$newpackage->status=$packagedataobj['status'];
			
			$planItems=[];
			for($dayIndex=1; $dayIndex<=$newpackage->nights;$dayIndex++){
				$plan=new PackagePlan();
				$plan->night=$dayIndex;		
				$planItems[]=$plan;
			}
			$newpackage->packageplan=$planItems;
			$this->packageTableGateway->put_package_item($newpackage);
			
			return $this->getPackage($destination,$packageCode);
		}		

		public function updatePackage($destination,$packageCode,$packagedataobj,$package)
		{
			$package->name=$packagedataobj->name;	
			$package->airport=$packagedataobj->airport;	
			$package->checkInTime=$packagedataobj->checkInTime;
			$package->checkInHotel=$packagedataobj->checkInHotelAddress;
			$package->checkInHotelAddress=$packagedataobj->checkInHotelAddress;
			$package->cost=$packagedataobj->cost;
			$package->nights=$packagedataobj->nights;		
			$package->start=new \DateTime($packagedataobj->start->date);
			$package->end=new \DateTime($packagedataobj->end->date);

			$this->packageTableGateway->udpdate_package_item($destination,$packageCode,$package);	
			return true;
		}

		public function publishPackage($destination,$packageCode)
		{
			$package=$this->getPackage($destination,$packageCode);
			$package->status='published';

			$this->packageTableGateway->udpdate_package_item($destination,$packageCode,$package);	
			return $package;
		}
	}
 ?>
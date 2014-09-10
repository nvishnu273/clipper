<?php

namespace Booking\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;
use common\Utility;

class SearchController extends AbstractRestfulController
{
	protected $customerPackageTable;
	protected $packageTable;
	protected $travelPackageInstanceTable;

	public function indexAction()
	{			
		return new JsonModel($this->getCustomerPackageTable()->fetchAll());		
	}

	public function keyAction()
	{	
		$allGetValues = $this->params()->fromQuery();
		$packageidparts = explode("-", $allGetValues['packageId'],2);
		$packageCatalog=$this->getPackageTable()->getPackage($packageidparts[0],$packageidparts[1]);
		$customerBooking=$this->getCustomerPackageTable()->fetchByCompositeKey(
			$allGetValues['customerId'], $allGetValues['packageId'], $allGetValues['startDate']);
		
		if (isset($packageCatalog) && isset($customerBooking)){
			if (!isset($packageCatalog->packageplan[0]->startlocation)){
				$startAddress=$packageCatalog->checkInHotelAddress;
				$url="https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyASi0J3CxTwf8wGsX60t6T24gPnwkTScgc&sensor=false&address=" . urlencode($startAddress);
				$geocodeinformation=Utility::GetLatLongFromAddress($url);
				$startLocationLatLong = $geocodeinformation['results'][0]['geometry']['location'];	
				$startLocationLatLong['formatted_address']=$startAddress;
				$packageCatalog->packageplan[0]->startlocation=$startLocationLatLong;
			}
			$endDate = new \DateTime($customerBooking['StartDate']);		
			if (isset($endDate)){
				$diffDay = new \DateInterval('P' . $packageCatalog->nights . 'D');
				$endDate->add($diffDay);
				$customerBooking['EndDate']=$endDate->format('Y-m-d');			
			}
			$booking['package']=$packageCatalog;
			$booking['customer']=$customerBooking;
			return new JsonModel($booking);		
		}		
	}

	public function packageAction()
	{	
		$allGetValues = $this->params()->fromQuery();		
		return new JsonModel($this->getCustomerPackageTable()->fetchAllByPackageId($allGetValues['id']));		    
	}

	public function customerAction()
	{			
		$allGetValues = $this->params()->fromQuery();


		$customerPackages=$this->getCustomerPackageTable()->fetchAllByCustomer($allGetValues['id']);

		foreach($customerPackages as &$customerPackage){
			$packageidparts = explode("-", $customerPackage['CustomerPackageId'],2);
			if ($customerPackage['Status']=='Checked-In'){			
				$customerPackageId=$customerPackage['CustomerPackageId'] . "-" . $customerPackage['CustomerId'];				
				$package=$this->getTravelPackageInstanceTable()->getPackageInstance($customerPackageId,$customerPackage['StartDate']);
				$customerPackage['PackageId']=$package->packagecode;
			}
			else{
				$package=$this->getPackageTable()->getPackage($packageidparts[0],$packageidparts[1]);
			}		
			$customerPackage['Name']=$package->name;
			$customerPackage['Airport']=$package->airport;
			$customerPackage['CheckInTime']=$package->checkInTime;
			$customerPackage['CheckInHotel']=$package->checkInHotel;
			$customerPackage['CheckInHotelAddress']=$package->checkInHotelAddress;
			$customerPackage['Nights']=$package->nights;		
		}
		return new JsonModel($customerPackages);
	}

	public function statusAction()
	{	
		$allGetValues = $this->params()->fromQuery();		
		return new JsonModel($this->getCustomerPackageTable()->fetchAllByStatus($allGetValues['status']));
	}

	public function agentAction()
	{	
		$allGetValues = $this->params()->fromQuery();		
		$customerPackages = $this->getCustomerPackageTable()->fetchAllPendingByAgent($allGetValues['id'],$allGetValues['status']);
		
		foreach($customerPackages as &$customerPackage){			
			$packageidparts = explode("-", $customerPackage->CustomerPackageId,2);
			$package=$this->getPackageTable()->getPackage($packageidparts[0],$packageidparts[1]);	
			$customerPackage->Name=$package->name;
			$customerPackage->Airport=$package->airport;
			$customerPackage->CheckInTime=$package->checkInTime;
			$customerPackage->CheckInHotel=$package->checkInHotel;
			$customerPackage->CheckInHotelAddress=$package->checkInHotelAddress;
			$customerPackage->Nights=$package->nights;		
		}

		return new JsonModel($customerPackages);
	}

	public function dateAction()
	{
		$allGetValues = $this->params()->fromQuery();	
		$destination = '';
		if ($allGetValues['destination']){
			$destination = $allGetValues['destination'];
		}
		$customerPackages = $this->getCustomerPackageTable()->fetchAllByDateRange($allGetValues['start'],
		        	$allGetValues['end'],$allGetValues['status'],$destination);

		//Refer to the array
		foreach($customerPackages as &$customerPackage){
			$packageidparts = explode("-", $customerPackage['CustomerPackageId'],2);		
			$package=$this->getPackageTable()->getPackage($packageidparts[0],$packageidparts[1]);			
			$customerPackage['Name']=$package->name;
			$customerPackage['Airport']=$package->airport;
			$customerPackage['CheckInTime']=$package->checkInTime;
			$customerPackage['CheckInHotel']=$package->checkInHotel;
			$customerPackage['CheckInHotelAddress']=$package->checkInHotelAddress;
			$customerPackage['Nights']=$package->nights;		
		}	
		return new JsonModel($customerPackages);		    
	}

	public function getCustomerPackageTable()
	{
		if (!$this->customerPackageTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerPackageTable = $sm->get('Booking\Model\CustomerPackageTable');	        
	    }
	    return $this->customerPackageTable;
	}

	public function getPackageTable()
	{
		if (!$this->packageTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->packageTable = $sm->get('Package\Model\PackageTable');	        
	    }
	    return $this->packageTable;
	}

	public function getTravelPackageInstanceTable()
	{
		if (!$this->travelPackageInstanceTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->travelPackageInstanceTable = $sm->get('Package\Model\TravelPackageInstanceTable');	        
	    }
	    return $this->travelPackageInstanceTable;
	}
}


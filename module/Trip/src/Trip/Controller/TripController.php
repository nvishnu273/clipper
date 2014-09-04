<?php

namespace Trip\Controller;


use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;

use common\Utility;

class TripController extends AbstractRestfulController
{	
	protected $customerPackageTable;
	protected $customerPackageInstanceTable;
	protected $tripPhotoTable;
	protected $tripReviewTable;

	public function get($tripId)
	{	
		$allGetValues=$this->params()->fromQuery();

		//Get customer id
		$idArray=explode('-', $tripId);
		$customerId=end($idArray);
		
		//removed customer id and get the packageid
		$tripIdParts = explode("-", $tripId);
		array_pop($tripIdParts);
		$customerPackageId=implode("-", $tripIdParts);
		$startDate=$allGetValues['StartDate'];
		
		//Get package from Dynamo Db
		$package=$this->getCustomerPackageInstanceTable()->getPackageInstance($tripId,
			$startDate);

		
		//Get the booking from RDS
		$customerPackageInfo=$this->getCustomerPackageTable()->fetchByCompositeKey($customerId,$customerPackageId,
			$startDate);

		if (!isset($package->packageplan[0]->startlocation)){
			$startAddress=$package->checkInHotelAddress;

			$url="https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyASi0J3CxTwf8wGsX60t6T24gPnwkTScgc&sensor=false&address=" . urlencode($startAddress);
			$geocodeinformation=Utility::GetLatLongFromAddress($url);
			$startLocationLatLong = $geocodeinformation['results'][0]['geometry']['location'];	
			$startLocationLatLong['formatted_address']=$startAddress;
			$package->packageplan[0]->startlocation=$startLocationLatLong;
		}
		$endDate = new \DateTime($customerPackageInfo['StartDate']);		
		if (isset($endDate)){
			$diffDay = new \DateInterval('P' . $package->nights . 'D');
			$endDate->add($diffDay);
			$customerPackageInfo['EndDate']=$endDate->format('Y-m-d');			
		}
		
		$trip['package']=$package;
		$trip['customer']=$customerPackageInfo;
		
		
		// /* Get a list of locations */
		$locations=[];
		foreach($package->packageplan as $planKey=>$planValue){			
			foreach($planValue->visitingplaces as $planItemVistingPlaceKey=>$planItemVistingPlaceValue){
				$locations[]=$planItemVistingPlaceValue->map->center->latitude . "," . 
				$planItemVistingPlaceValue->map->center->longitude;								
			}			
		}
		
		// /* Build the photo review for the locations */
		$tripPhotos=[];
		$photos=$this->getTripPhotoTable()->fetchAllByPackageId($tripId);
				
		// //GET PHOTOS TODO:Use Batch Here
		foreach($locations as $locationKey=>$locationValue){
			$photoInfos=[];
			foreach($photos as $photoKey=>$photoValue){
				if ($locationValue==$photoValue["Location"]) {		
					$photoUrl=$this->getTripPhotoTable()->fetch($photoKey);
					$photo=$this->getTripPhotoTable()->fetchPhotoObject($photoKey);
					//echo($photo);
					$photoInfos[]=array(
								"photoId" => $photoKey, 
								"photoUrl" => $photoUrl, 
								"photoName"=>$photo['Metadata']['photoname'],
								"photoUploadDt"=>$photo['LastModified'],
								"reviewComments"=>isset($photo['Metadata']['Review']) ? $photo['Metadata']['Review'] : null,
								"reviewedBy"=>isset($photo['Metadata']['ReviewedBy']) ? $photo['Metadata']['ReviewedBy'] : null
					);
				}
			}	
			$tripPhotos[$locationValue]=$photoInfos;
		}
		
		// //GET REVIEWS TODO:Use Batch Here
		$tripReviews=[];
		foreach($locations as $locationKey=>$locationValue){	
			$tripReviews[$locationValue]=$this->getTripReviewTable()->getTripReviewsByLocation($locationValue);			
		}
		
		/* Assign the photo and review back to the locations */
		foreach($package->packageplan as $planKey=>$planValue){			
			foreach($planValue->visitingplaces as $planItemVistingPlaceKey=>$planItemVistingPlaceValue){
				$reviewKey=$planItemVistingPlaceValue->map->center->latitude . "," . $planItemVistingPlaceValue->map->center->longitude;
				//echo($planItemVistingPlaceKey . PHP_EOL);
				
				if (is_object($planValue->visitingplaces)) {					
					$visitingplacesarr=get_object_vars($planValue->visitingplaces); //get an array
					//echo(json_encode($visitingplacesarr[$planItemVistingPlaceKey]) . PHP_EOL);
					$visitingplacesarr[$planItemVistingPlaceKey]->Photos=$tripPhotos[$reviewKey];
					$visitingplacesarr[$planItemVistingPlaceKey]->Reviews=$tripReviews[$reviewKey];
					$visitingplacesobject = json_decode(json_encode($visitingplacesarr), FALSE); //covert back to object
					$planValue->visitingplaces=$visitingplacesobject;
				}
				else {
					//echo("object" . PHP_EOL);
					$planValue->visitingplaces[$planItemVistingPlaceKey]->Photos=$tripPhotos[$reviewKey];
					$planValue->visitingplaces[$planItemVistingPlaceKey]->Reviews=$tripReviews[$reviewKey];		
				}					
			}			
		}

		return new JsonModel($trip);		    
	}
	
	/* returns customer package table from service locator */	
	public function getCustomerPackageInstanceTable()
	{
		if (!$this->customerPackageInstanceTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerPackageInstanceTable = $sm->get('Package\Model\TravelPackageInstanceTable');	        
	    }
	    return $this->customerPackageInstanceTable;
	}

	public function getCustomerPackageTable()
	{
		if (!$this->customerPackageTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerPackageTable = $sm->get('Booking\Model\CustomerPackageTable');	        
	    }
	    return $this->customerPackageTable;
	}

	public function getTripPhotoTable()
	{
		if (!$this->tripPhotoTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->tripPhotoTable = $sm->get('Trip\Model\TripPhotoTable');	        
	    }
	    return $this->tripPhotoTable;
	}

	public function getTripReviewTable()
	{
		if (!$this->tripReviewTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->tripReviewTable = $sm->get('Trip\Model\TripReviewTable');	        
	    }
	    return $this->tripReviewTable;
	}
}


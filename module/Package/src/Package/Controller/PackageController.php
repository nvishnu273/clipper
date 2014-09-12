<?php

namespace Package\Controller;

use Account\Model\Notification;

use Zend\Mvc\Controller\AbstractRestfulController;

use common\SearchManager;

use Zend\View\Model\JsonModel;

class PackageController extends AbstractRestfulController
{
	protected $packageTable;
	protected $customerNotificationTable;
	protected $customerPackageTable;
	protected $travelPackageInstanceTable;
	protected $tripReviewTable;
	
	protected $snsClient;

	public function getList()
	{		
		return new JsonModel($this->getPackageTable()->getMostRecent(1,20));
	}	

	public function get($id)
	{				
		$packageidparts = explode("-", $id,2);	
		$destination=$packageidparts[0];
		$code=$packageidparts[1];
		$package=$this->getPackageTable()->getPackage($destination,$code);
		return new JsonModel(get_object_vars($package));
	}
	
	public function create($data)
	{	

		if ($this->getRequest()->isPost()){				
			$input = $this->getRequest()->getContent();
			$postedData=json_decode($input,true);			
			$package=$this->getPackageTable()->createPackage($postedData);	

			$resourceUrl = '/packages/'.$package->destination.'/'.$package->packagecode;
			return new JsonModel(array('resouceUrl' => $resourceUrl));
		}
		
	}
	
	public function update($id,$data){
		date_default_timezone_set('UTC');
		
		if ($this->getRequest()->isPut()){					
			$postedData=json_encode($data);
			$packagedataobj=json_decode($postedData);
			$packageidparts = explode("-", $id,2);	
			$destination=$packageidparts[0];
			$code=$packageidparts[1];

			$package=$this->getPackageTable()->getPackage($destination,$code);
			$currentCost = $package->cost;			
			
			$packageUpdated=$this->getPackageTable()->updatePackage($destination,$code,$packagedataobj,$package);
			
			
			if ($packagedataobj->cost != $currentCost){			
				$packageId = $packagedataobj->destination . "-" . $packagedataobj->packagecode;
				
				$arn=$this->getServiceLocator()->get('config')['sns_config']['topic_arn'];
				$result = $this->getSnsClient()->publish(array(
					'TopicArn' => $arn,
					'Message' => $packageId,
					'Subject' => "There are changes to the package"
				));
				
				$newNotification=new Notification();
				$newNotification->messageId=$result['MessageId'];
				$newNotification->packageId=$packageId;
				$newNotification->dateCreated=date("Y-m-d H:i:s");
				$newNotification->processed=false;
				$newNotification->messageType='PackageModified';
				
				$this->getCustomerNotificationTable()->createNotification($newNotification);			
			}
			

			if (isset($packageUpdated)){
				return new JsonModel(array('Updated' => $packageUpdated));				
			}
			
		}	
	}

	public function indexAllAction()
	{		
		date_default_timezone_set('UTC');	
		$packages=$this->getPackageTable()->getAllRowsInPackage();
		
		foreach($packages as $package){		
			$packageId=$package->destination . "-" . $package->packagecode;
			$totalPackage=$this->getCustomerPackageTable()->fetchAllByPackageId($packageId);
			$review=rand(0, 5);
			
			SearchManager::IndexPackage($package,$packageId,count($totalPackage),$review);
		}

		return new JsonModel(array('Status' => 'OK'))		;
	}

	public function newAction()
	{			
		return new JsonModel(get_object_vars($this->getPackageTable()->getNewPackage()));
	}

	public function publishAction()
	{			
		if ($this->getRequest()->isPut()){

			$input = $this->getRequest()->getContent();
			$postedData=json_decode($input,true);

			$package=$this->getPackageTable()->publishPackage($postedData['destination'], $postedData['packagecode']);

			$rating=rand(0, 5);	
			$packageId=$package->destination . "-" . $package->packagecode;
			SearchManager::IndexPackage($package,$packageId,0,$rating);
			return new JsonModel(array('status' => 'Updated'));
		}
		
	}

	public function filterByStatusAction()
	{			
		$allGetValues = $this->params()->fromQuery();
		
		
		$destination = $allGetValues['destination'];
		$minnights = $allGetValues['minnights'];
		
		$packages=$this->getPackageTable()->getPackageByNights($destination,$minnights);
		foreach($packages as $key=>$value){
			$value->reviews=[];
		}			
		return new JsonModel($packages);	
						
	}

	public function filterByDestinationAction()
	{			
		$allGetValues = $this->params()->fromQuery();

		$destination = $allGetValues['destination'];		
		$minCost = $allGetValues['minCost'];
		$maxCost=$allGetValues['maxCost'];

		$result=SearchManager::SearchPackage($destination,$minCost,$maxCost);	
		foreach($result['Result'] as &$package){
			$package['reviews']=[];
		}	
		return new JsonModel($result);	
						
	}

	public function filterByBrandAction()
	{
		$allGetValues = $this->params()->fromQuery();

		$destination = $allGetValues['destination'];		
		$brand = $allGetValues['brand'];

		$result=SearchManager::FilterByBrand($destination,$brand);	
		foreach($result['Result'] as &$package){
			$package['reviews']=[];
		}	

		return new JsonModel($result);	
	}

	public function filterByPriceAction()
	{
		$allGetValues = $this->params()->fromQuery();

		$destination = $allGetValues['destination'];		
		$minCost = $allGetValues['minCost'];
		$maxCost=$allGetValues['maxCost'];

		$result=SearchManager::SearchPackage($destination,$minCost,$maxCost);	
		foreach($result['Result'] as &$package){
			$package['reviews']=[];
		}	
		return new JsonModel($result);	
	}

	public function reviewsAction()
	{
		$allGetValues = $this->params()->fromQuery();

		$packageId = $allGetValues['packageId'];		
		
		$packageBookings=$this->getCustomerPackageTable()->fetchAllByPackageId($packageId);
		
		//Get all the bookings for this package and build the key for TripReview table
		foreach($packageBookings as $key=>$value){
			$customerPackageKeys[] = array(
				'PackageId' => $value->CustomerPackageId . "-" . $value->CustomerId,
				'StartDate' => $value->StartDate
			);
		}

		if (!isset($customerPackageKeys)) {
			return new JsonModel([]);	
		}
		
		$packages=$this->getTravelPackageInstanceTable()->getCustomerPackagesBulk($customerPackageKeys);
	
		/* Get a list of locations */ 
		$locations=[];
		foreach($packages as $packageKey=>$packageValue){			
			foreach($packageValue->packageplan as $planKey=>$planValue){			
				foreach($planValue->visitingplaces as $planItemVistingPlaceKey=>$planItemVistingPlaceValue){
					$locaKey=$planItemVistingPlaceValue->map->center->latitude . "," . 
								$planItemVistingPlaceValue->map->center->longitude;					
					$locations[$locaKey]=$planItemVistingPlaceValue->name . ", " . $planItemVistingPlaceValue->vicinity;									
				}			
			}			
		}
		
		//GET REVIEWS TODO:Use Batch Here
		$tripReviews=[];
		foreach($locations as $locationKey=>$locationValue){				
			$review=$this->getTripReviewTable()->getTripReviewsByLocation2($locationKey,$locationValue);
			if (!empty($review)){			
				foreach($review as $reviewKey=>$reviewValue){					
					$tripReviews[]=$reviewValue;		
				}						
			}
		}

		return new JsonModel($tripReviews);	
	}

	/* returns package table from service locator */
	public function getPackageTable()
	{
		if (!$this->packageTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->packageTable = $sm->get('Package\Model\PackageTable');	        
	    }
	    return $this->packageTable;
	}

	public function getCustomerPackageTable()
	{
		if (!$this->customerPackageTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerPackageTable = $sm->get('Booking\Model\CustomerPackageTable');	        
	    }
	    return $this->customerPackageTable;
	}

	public function getTravelPackageInstanceTable()
	{
		if (!$this->travelPackageInstanceTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->travelPackageInstanceTable = $sm->get('Package\Model\TravelPackageInstanceTable');	        
	    }
	    return $this->travelPackageInstanceTable;
	}

	public function getCustomerNotificationTable()
	{
		if (!$this->customerNotificationTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerNotificationTable = $sm->get('Account\Model\NotificationTable');	        
	    }
	    return $this->customerNotificationTable;
	}

	public function getTripReviewTable()
	{
		if (!$this->tripReviewTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->tripReviewTable = $sm->get('Trip\Model\TripReviewTable');	        
	    }
	    return $this->tripReviewTable;
	}	

	public function getSnsClient()
	{
		if (!$this->snsClient) {
	        $sm = $this->getServiceLocator();	        
	        $this->snsClient = $sm->get('SnsClient');	        
	    }
	    return $this->snsClient;
	}
}


<?php

namespace Trip\Controller;


use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;

use common\Utility;
use common\SearchManager;

class TripReviewController extends AbstractRestfulController
{	
	protected $tripReviewTable;
	
	public function addAction()
	{			
		
		$input = $this->getRequest()->getContent();
		$postdataobj=json_decode($input,true);
		
		$reviewDate = \date('Y-m-d H:i:s');
		
		$reviewResponse = $this->getTripReviewTable()->addReview(
				$postdataobj['location'],
				$reviewDate,
				$postdataobj['comment'],			
				$postdataobj['firstname'] . " " . $postdataobj['lastname'],
				$postdataobj['customerID'],
				$postdataobj['tripId'],
				$postdataobj['rating']);
		
		$review=$this->getTripReviewTable()->getTripReview($postdataobj['location'],$reviewDate);
		
		$rating=rand(0, 5);
		$review['Rating']=$rating;
		$coords=explode(',',$review['Location']);
		$result=Utility::GetAddressFromLatLong($coords[0],$coords[1]);
		
		if ($result['status'] !=  'ZERO_RESULTS'){						
			$address=(string) $result['results'][0]['formatted_address'];
			$review['formatted_address']=$address;		
			$review['name']=$postdataobj['name'];				
		}
		SearchManager::IndexReview($review);
	
		return new JsonModel($review);
		
	}

	public function searchAction($id)
	{	
		return new JsonModel(array(
	        'Trip' => $this->getCustomerPackageInstanceTable()->fetch($id),	        
	    ));		    
	}
	
	

	/* returns customer package table from service locator */	
	public function getTripReviewTable()
	{
		if (!$this->tripReviewTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->tripReviewTable = $sm->get('Trip\Model\TripReviewTable');	        
	    }
	    return $this->tripReviewTable;
	}


}


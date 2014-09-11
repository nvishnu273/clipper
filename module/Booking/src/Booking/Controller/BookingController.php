<?php

namespace Booking\Controller;

use Package\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Booking\Model\CustomerPackage;
use Booking\Model\CustomerPackageTable;
use Package\Model\TravelPackageInstanceTable;
use Zend\View\Model\JsonModel;

class BookingController extends AbstractRestfulController
{
	protected $customerPackageTable;
	protected $packageTable;
	protected $customerPackageInstanceTable;

	public function getList()
	{		
		return new JsonModel(array(
	        'Booking' => $this->getCustomerPackageTable()->fetchAll(),	        
	    ));
	}	

	public function get($id)
	{	
		return new JsonModel(array(
	        'Booking' => $this->getCustomerPackageTable()->fetch($id),	        
	    ));		    
	}
	
	public function create($data)
	{			
		if ($this->getRequest()->isPost()){

			$input = $this->getRequest()->getContent();
			$postedData=json_decode($input,true);


			$package=$this->getPackageTable()->getPackage($postedData['destination'], $postedData['packagecode']);

			if( !empty($package) && isset($package))
			{				
				$packageid=$package->destination . "-" . $package->packagecode;
				$newBooking=new CustomerPackage();
				$newBooking->CustomerPackageId=$packageid;
				$newBooking->CustomerId=$postedData['customerid'];
				$newBooking->StartDate=new \DateTime($postedData['startDate']);//$package->start;
				$newBooking->Status='PendingCheckin';								
				return new JsonModel($this->getCustomerPackageTable()->createPackage($newBooking));
			}

		}

	}

	public function assignAction()
	{
			$allGetValues = $this->params()->fromRoute();
			$input = $this->getRequest()->getContent();
			$postedData=json_decode($input,true);
			return new JsonModel($this->getCustomerPackageTable()->assignAgent($allGetValues['id'],
				$postedData['AgentId']));		
	}

	public function checkInAction()
	{
		$allGetValues = $this->params()->fromRoute();
		$input = $this->getRequest()->getContent();
		$postedData=json_decode($input,true);
		$booking=$this->getCustomerPackageTable()->fetch($allGetValues['id']);
		
		//checkin the booking
		$this->getCustomerPackageTable()->checkInAction($allGetValues['id'],$postedData['CheckInTime']);

		//copy package
		$packageidparts = explode("-", $booking[0]->CustomerPackageId,2);	
	
		//Get Package		
		$package=$this->getPackageTable()->getPackage($packageidparts[0],$packageidparts[1]);
		$customerpackageid=$booking[0]->CustomerPackageId . "-" . $booking[0]->CustomerId;	
		$package->checkInTime=$postedData['CheckInTime'];
		$package->status="Checked-In";			

		$this->getCustomerPackageInstanceTable()->createTravelPackageinstance($customerpackageid,
		$booking[0]->StartDate,$booking[0]->AssignedTo,$package);
		return new JsonModel(array('TravelPackageInstance'=>$package));		
	}
	
	/* returns customer package table from service locator */
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

	public function getCustomerPackageInstanceTable()
	{
		if (!$this->customerPackageInstanceTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->customerPackageInstanceTable = $sm->get('Package\Model\TravelPackageInstanceTable');	        
	    }
	    return $this->customerPackageInstanceTable;
	}
}


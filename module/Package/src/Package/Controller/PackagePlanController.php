<?php

namespace Package\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;

class PackagePlanController extends AbstractRestfulController
{
	protected $packageTable;
	protected $packagePlanTable;	
		
	
	public function update($id,$data){
		date_default_timezone_set('UTC');
				
		if ($this->getRequest()->isPut()){					
			$postedData=json_encode($data);
			$packagedataobj=json_decode($postedData);
			
			$destination=$packagedataobj->destination;
			$packagecode=$packagedataobj->packagecode;			
			$package=$this->getPackageTable()->getPackage($destination,$packagecode);														
			$this->getPackagePlanTable()->updatePlanPackage($destination,$packagecode,$packagedataobj,$package);
			return new JsonModel(get_object_vars($package));		
		}	
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

	public function getPackagePlanTable()
	{
		if (!$this->packagePlanTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->packagePlanTable = $sm->get('Package\Model\PackagePlanTable');	        
	    }
	    return $this->packagePlanTable;
	}
}


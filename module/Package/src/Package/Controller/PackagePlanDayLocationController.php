<?php

namespace Package\Controller;

use common\Utility;

use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;

class PackagePlanDayLocationController extends AbstractRestfulController
{
	protected $packageTable;
	protected $packagePlanTable;	
	
	public function get($id){
		$allGetValues = $this->params()->fromRoute();

		if (isset($allGetValues['latlng'])) {
			$latlng=$allGetValues['latlng'];
			return new JsonModel(array('latlng' => $allGetValues['latlng'], 'id' => $id));
		}
		else {
			return new JsonModel(array('id' => $id));
		}
	}
	public function create($data){
		date_default_timezone_set('UTC');
				
			
		$postedData=json_encode($data);
		$locationDataObj=json_decode($postedData);
		
		$destination=$locationDataObj->destination;
		$packagecode=$locationDataObj->packagecode;
		$day=$locationDataObj->night;
		$place=$locationDataObj->place;
		

		$package=$this->getPackageTable()->getPackage($destination,$packagecode);
		$dayIndex = $day-1;
		$planItems=$package->packageplan;			
		$keyFound;		
		if (isset($planItems[$dayIndex]->visitingplaces)){			
			foreach($planItems[$dayIndex]->visitingplaces as $key=>$value){
				if (isset($value)){
					$lat=$value->map->center->latitude;
					$lng=$value->map->center->longitude;
					$name=$value->name;					
					if (
							($lat==$place->map->center->latitude) 
							&& ($lng==$place->map->center->longitude)
							&& ($name==$place->name)
						){							
						$keyFound = $key;
						break;
					}
				};				
			}
		}
		else {
			$planItems[$dayIndex]->visitingplaces = [];
		}	
		
		if (!isset($keyFound)){										
			$planItems[$dayIndex]->visitingplaces[]=$place;				
		}
		else {								
			return new JsonModel(array('Key_Exists' => "The item already exists in the day's plan..."));
		}

		if (isset($planItems[$dayIndex]->visitingplaces)){			
			if (is_object($planItems[$dayIndex]->visitingplaces)) {				
				$visitingplacesarr=get_object_vars($planItems[$dayIndex]->visitingplaces); //get an array				
			}
			else {				
				$visitingplacesarr=$planItems[$dayIndex]->visitingplaces;
			}				
			$lastVistingPlace=end($visitingplacesarr);//gives reference to the array
			if (isset($lastVistingPlace)){
				$formattedAddress=Utility::GetAddressFromLatLong($lastVistingPlace->map->center->latitude,$lastVistingPlace->map->center->longitude);	
				if ($formattedAddress['status'] !=  'ZERO_RESULTS'){		
					$planItems[$dayIndex]->endlocation=array("lat"=>$lastVistingPlace->map->center->latitude,"lng"=>$lastVistingPlace->map->center->longitude,
						"formatted_address"=>$formattedAddress['results'][0]['formatted_address']);					
				}
			}			
		}
	
		$package->packageplan=$planItems;		
		$this->getPackagePlanTable()->updatePlanPackageLocation($package);

		return new JsonModel($package->packageplan);		
					
	}
	
	public function delete($id){
		date_default_timezone_set('UTC');
		$allGetValues = $this->params()->fromRoute();
		$ltlngparts = explode(',',$allGetValues['latlng']);
		$input = $this->getRequest()->getContent();
		$locationDataObj=json_decode($input);
		
		
		$destination=$locationDataObj->destination;
		$packagecode=$locationDataObj->packagecode;
		$day=$locationDataObj->night;
		$place=$locationDataObj->place;
		

		$package=$this->getPackageTable()->getPackage($destination,$packagecode);
		$dayIndex = $day-1;
		$planItems=$package->packageplan;			
		$keyFound;		
		if (isset($planItems[$dayIndex]->visitingplaces)){			
			foreach($planItems[$dayIndex]->visitingplaces as $key=>$value){
				if (isset($value)){
					$lat=$value->map->center->latitude;
					$lng=$value->map->center->longitude;
					$name=$value->name;							
					if (($lat==$ltlngparts[0]) && ($lng==$ltlngparts[1]) && 
						($name==$place->name)){							
						$keyFound = $key;
						break;
					}
				};				
			}
		}
		else {
			$planItems[$dayIndex]->visitingplaces = [];
		}	


		if (isset($keyFound)){				
			if (is_object($planItems[$dayIndex]->visitingplaces)) {
				$visitingplacesarr=get_object_vars($planItems[$dayIndex]->visitingplaces); //get an array					
			}
			else {
				$visitingplacesarr=$planItems[$dayIndex]->visitingplaces;
			}				
			unset($visitingplacesarr[$keyFound]); //remove the object found				
			$visitingplacesobject = json_decode(json_encode($visitingplacesarr), FALSE); //covert back to object
			$planItems[$dayIndex]->visitingplaces=$visitingplacesobject;
		}
		else {						
			return new JsonModel(array('Key_Not_Found' => "The item was not found in day's plan..."));
		}
	
		if (isset($planItems[$dayIndex]->visitingplaces)){			
			if (is_object($planItems[$dayIndex]->visitingplaces)) {				
				$visitingplacesarr=get_object_vars($planItems[$dayIndex]->visitingplaces); //get an array				
			}
			else {				
				$visitingplacesarr=$planItems[$dayIndex]->visitingplaces;
			}				
			$lastVistingPlace=end($visitingplacesarr);//gives reference to the array
			if (isset($lastVistingPlace)){
				$formattedAddress=Utility::GetAddressFromLatLong($lastVistingPlace->map->center->latitude,$lastVistingPlace->map->center->longitude);	
				if ($formattedAddress['status'] !=  'ZERO_RESULTS'){		
					$planItems[$dayIndex]->endlocation=array("lat"=>$lastVistingPlace->map->center->latitude,"lng"=>$lastVistingPlace->map->center->longitude,
						"formatted_address"=>$formattedAddress['results'][0]['formatted_address']);					
				}
			}			
		}
	
		$package->packageplan=$planItems;		
		$this->getPackagePlanTable()->updatePlanPackageLocation($package);

		return new JsonModel($package->packageplan);		
		
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


<?php

namespace ExternalApi\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;
use common\Utility;

class ExternalApiController extends AbstractRestfulController
{
	public function getList()
	{
		return new JsonModel(array('all' => 'hit'));
	}
	public function cityAction()
	{			
		$allGetValues = $this->params()->fromQuery();
		$mql=array("type"=>"/aviation/airport","name"=>null,"name~="=>"*".$allGetValues['text']."*","/aviation/airport/iata"=>[],
			"limit"=>3);
		$mqlAirportCodeByCity[]=$mql;		
		$results=Utility::LookupAirPortCode(json_encode($mqlAirportCodeByCity));
		$airportCodes=[];
		foreach($results['result'] as $airports){
			if (!empty($airports["/aviation/airport/iata"])){
				$airportCodes[]=$airports["/aviation/airport/iata"][0] . " - " . $airports["name"];
			}
		}			
		return new JsonModel($airportCodes);		
	}	
	public function hotelAction()
	{	
		$allGetValues = $this->params()->fromQuery();				
		$namePredicate=array("\$search"=>$allGetValues['text']);					
		$filterQuery=array("name"=>$namePredicate);			
		$results=Utility::GetHotelInformation(json_encode($filterQuery));	
		if ($results['status']=='ok'){
			$hotelResults=[];				
			foreach($results['response']['data'] as $hotelValue){					
				$hotelResults[]=$hotelValue['name'].', '.$hotelValue['locality'].', '.$hotelValue['address'].', '.$hotelValue['postcode'];	
			}
			
			return new JsonModel($hotelResults);	
		}		
	}
	public function addressAction()
	{			
		$allGetValues = $this->params()->fromQuery();
		$address = $allGetValues['val'];
		$url="https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyASi0J3CxTwf8wGsX60t6T24gPnwkTScgc&sensor=false&address=" . urlencode($address);
		$geocodeinformation=Utility::GetLatLongFromAddress($url);
		$geocodeinformation['results'][0]['geometry']['location']['formatted_address'] = $address;	
		return new JsonModel($geocodeinformation['results'][0]['geometry']['location']);		
	}	
	public function nearbyAction()
	{			
		$allGetValues = $this->params()->fromQuery();
		$ltlngparts = explode(',',$allGetValues['val']);
		
		$places=Utility::GetNearbyPlaces2($ltlngparts[0],$ltlngparts[1]);
		$nextPageToken=$places['next_page_token'];
		
		$pageResult=array('nextPageToken'=>$nextPageToken,'places'=>[]);
		$nearbyLocations=[];
		
		foreach($places['results'] as $key=>$value){		
			$result=$value;
			$rating=0;
			if (isset($result["rating"])){
				$rating=$result["rating"];
			}
			$map=array("center"=>array("latitude"=>$result["geometry"]["location"]["lat"],"longitude"=>$result["geometry"]["location"]["lng"]), 
						"zoom"=>16);
			
			$nearbyLocation=array("map"=>$map,
								"icon"=>$result["icon"],
								"id"=>$result["id"],
								"name"=>$result["name"],
								"vicinity"=>$result["vicinity"],
								"rating"=>$rating);	
			$nearbyLocations[]=$nearbyLocation;			
		}
		$pageResult['places']=$nearbyLocations;

		return new JsonModel($pageResult);		
	}	
	public function latlngAction()
	{			
		$allGetValues = $this->params()->fromQuery();
		$mql=array("type"=>"/aviation/airport","name"=>null,"name~="=>"*".$allGetValues['text']."*","/aviation/airport/iata"=>[],
			"limit"=>3);
		$mqlAirportCodeByCity[]=$mql;		
		$results=Utility::LookupAirPortCode(json_encode($mqlAirportCodeByCity));
		$airportCodes=[];
		foreach($results['result'] as $airports){
			if (!empty($airports["/aviation/airport/iata"])){
				$airportCodes[]=$airports["/aviation/airport/iata"][0] . " - " . $airports["name"];
			}
		}			
		return new JsonModel($airportCodes);		
	}		
}


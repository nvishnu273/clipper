<?php

namespace common;

class SearchManager {

	private static $site_clipper_package=array("endpoint"=>
                        array(
                                "localhost"=>
                                        array(
                                                "host"=>"127.0.0.1",
                                                "port"=>"8080",
                                                "path"=>"/solr",
                                                "core"=>"core0",
                                        )
                        )
                );
	
	private static $site_clipper_location=array("endpoint"=>
                        array(
                                "localhost"=>
                                        array(
                                                "host"=>"127.0.0.1",
                                                "port"=>"8080",
                                                "path"=>"/solr",
                                                "core"=>"core1",
                                        )
                        )
                );
				
	public static function IndexPackage($package,$id,$totalPackage,$review){
		$client=new \Solarium\Client(SearchManager::$site_clipper_package);
		$updateQuery=$client->createUpdate();
		$doc1=$updateQuery->createDocument();

		$doc1->id=$id;
		$doc1->packagecode_s=$package->packagecode; //_s, _dt are dynamic fields. no need to add new fields
		$doc1->destination_t=$package->destination;
		$doc1->hotel_t=$package->checkInHotel;
		$doc1->start_dt=$package->start;
		$doc1->end_dt=$package->end;
		$doc1->nights_i=$package->nights;
		$doc1->cost_d=$package->cost;
		$doc1->total_i=$totalPackage;
		$doc1->review_i=$review;
		$doc1->name_t=$package->name;
		
		//echo $package->packageplan[0]->startlocation->lat .",".$customerPackage->package[0]->startlocation->lng . PHP_EOL;
		$updateQuery->addDocuments(array($doc1),true);
		$updateQuery->addCommit();
		
		$result=$client->update($updateQuery);
		return $result->getStatus();
	}
	
	public static function SearchPackage($destination,$minCost,$maxCost){
		$client=new \Solarium\Client(SearchManager::$site_clipper_package);
		$query=$client->createSelect();
		$query->setQuery('destination_t:'.$destination);
		
		if (isset($minCost)){
			$fquery=$query->createFilterQuery('priceRangeFilter');
			$fquerys='cost_d:['.$minCost.' TO ';
			if (isset($maxCost)){
				if ($minCost == $maxCost) {
					$fquerys=$fquerys.'*]';
				}
				else {
					$fquerys=$fquerys.$maxCost.']';
				}
			}
			else {
				$fquerys=$fquerys.'*]';
			}
			
			$fquery->setQuery($fquerys);
		}
		
		//$query->setRows(3);//3 results per page
		$query->setFields(['id','packagecode_s','destination_t','start_dt','hotel_t','cost_d','nights_i','end_dt','total_i','review_i','startLoc_p','name_t']); //$query->setFields(array('id','packagecode_s','start_dt','end_dt','startLoc_p'));
		$query->addSort('cost_d',$query::SORT_DESC);
		$resultSet=$client->select($query);
		$totalFound=$resultSet->getNumFound();
		$packages=[];
		foreach($resultSet as $doc){
			$packages[]=array(
				"packagecode"=>$doc->packagecode_s,
				"name"=>$doc->name_t,
				"destination"=>$doc->destination_t,
				"hotel"=>$doc->hotel_t,
				"cost"=>$doc->cost_d,
				"nights"=>$doc->nights_i,
				"start"=>new \DateTime($doc->start_dt),				
				"end"=>new \DateTime($doc->end_dt),
				"rating"=>$doc->review_i);			
		}
		return array('TotalRecords'=>$totalFound,'Result'=>$packages);
	}
	
	public static function FilterByBrand($destination,$hotelName){
		$client=new \Solarium\Client(SearchManager::$site_clipper_package);
		$query=$client->createSelect();
		$query->setQuery('destination_t:'.$destination);
		
		$fquery=$query->createFilterQuery('hotelBrandFilter');
		$fquerys='hotel_t:'.$hotelName;			
		$fquery->setQuery($fquerys);
		
		//$query->setRows(3);//3 results per page
		$query->setFields(['id','packagecode_s','destination_t','start_dt','cost_d','nights_i','end_dt','total_i','review_i','startLoc_p','name_t']); //$query->setFields(array('id','packagecode_s','start_dt','end_dt','startLoc_p'));
		//$query->addSort('start_dt',$query::SORT_ASC);
		$resultSet=$client->select($query);
		$totalFound=$resultSet->getNumFound();
		$packages=[];
		foreach($resultSet as $doc){
			$packages[]=array(
				"packagecode"=>$doc->packagecode_s,
				"name"=>$doc->name_t,
				"destination"=>$doc->destination_t,
				"cost"=>$doc->cost_d,
				"nights"=>$doc->nights_i,
				"start"=>new \DateTime($doc->start_dt),				
				"end"=>new \DateTime($doc->end_dt),
				"rating"=>$doc->review_i);			
		}
		return array('TotalRecords'=>$totalFound,'Result'=>$packages);
	}
	
	public static function MostPuopularPackage(){
		$client=new Solarium\Client(SearchManager::$site_clipper_package);
	}
	
	public static function DeleteAll(){
		$client=new Solarium\Client(SearchManager::$site_clipper_package);
		
		$update = $client->createUpdate();

		$update->addDeleteQuery('*:*');
		$update->addCommit();

		$result = $client->update($update);
		echo '<b>Update query executed</b><br/>';
		echo 'Query status: ' . $result->getStatus(). '<br/>';
		echo 'Query time: ' . $result->getQueryTime();
	}
	
	/*** Loaction/Review search ***/
	public static function IndexReview($review){
		$client=new \Solarium\Client(SearchManager::$site_clipper_location);
		$updateQuery=$client->createUpdate();
		$doc1=$updateQuery->createDocument();
		$doc1->id=$review['ReviewDate']."-".$review['ReviewById'];
		$doc1->packageid_t=$review['PackageId'];
		$doc1->comment_t=$review['Comment'];
		$doc1->reviewBy_t=$review['ReviewBy'];
		$doc1->rating_i=$review['Rating'];
		$doc1->address_t=$review['formatted_address'];
		$doc1->location_p=$review['Location'];
		
		$updateQuery->addDocuments(array($doc1),true);
		$updateQuery->addCommit();
		$result=$client->update($updateQuery);
		return $result->getStatus();
	}
	
	public static function SearchByAddress($address){
		$client=new \Solarium\Client(SearchManager::$site_clipper_location);
		$query=$client->createSelect();
		$query->setQuery('address_t:*'.$address.'*');
			
		$query->setFields(['id','packageid_t','comment_t','reviewBy_t','rating_i','address_t','location_p']);		
		$query->addSort('rating_i',$query::SORT_DESC);
		$resultSet=$client->select($query);
		$totalFound=$resultSet->getNumFound();
		$reviews=[];
		foreach($resultSet as $doc){
			$idParts=explode('-',$doc->id);
			$tripIdParts=explode('-',$doc->packageid_t);
			array_pop($tripIdParts);
			$customerPackageid=implode("-", $tripIdParts);			
			$customerPackageidParts=explode("-", $customerPackageid,2);
			$reviews[]=array(				
				"ReviewDate"=>$idParts[0],
				"Destination"=>$customerPackageidParts[0],
				"PackageCode"=>$customerPackageidParts[1],
				"Comment"=>$doc->comment_t,
				"ReviewBy"=>$doc->reviewBy_t,
				"Rating"=>$doc->rating_i,
				"Address"=>$doc->address_t,				
				"Location"=>$doc->location_p);			
		}
		return array('TotalRecords'=>$totalFound,'Result'=>$reviews);
	}
	
	public static function SearchNearbyPackages($location,$d){
		$client=new \Solarium\Client(SearchManager::$site_clipper_location);
		$query=$client->createSelect();
		
		$latlonparts=explode(",",$location);
		$query->setQuery('*:*');
		$query->createFilterQuery('distance')->setQuery($query->getHelper()->geofilt('location_p', $latlonparts[0], $latlonparts[1], $d, true));
		//$query->addSort('geodist()', $query::SORT_ASC);

		$resultSet=$client->select($query);
		$totalFound=$resultSet->getNumFound();
		$reviews=[];
		foreach($resultSet as $doc){
			$idParts=explode('-',$doc->id);
			$tripIdParts=explode('-',$doc->packageid_t);
			array_pop($tripIdParts);
			$customerPackageid=implode("-", $tripIdParts);			
			$customerPackageidParts=explode("-", $customerPackageid,2);
			$reviews[]=array(				
				"ReviewDate"=>$idParts[0],
				"Destination"=>$customerPackageidParts[0],
				"PackageCode"=>$customerPackageidParts[1],
				"Comment"=>$doc->comment_t,
				"ReviewBy"=>$doc->reviewBy_t,
				"Rating"=>$doc->rating_i,
				"Address"=>$doc->address_t,				
				"Location"=>$doc->location_p);			
		}
		return array('TotalRecords'=>$totalFound,'Result'=>$reviews);
	}
	
	
}

?>
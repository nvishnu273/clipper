<?php


// require_once('travel-package.php');
// require_once('createpackage.php');
// require_once('travel-package-instance.php');
// require_once('travel-db-repository.php');
// require_once('trip-photo-manager.php');
// require_once('trip-review-manager.php');
require_once ('search-manager.php');

date_default_timezone_set('UTC');

/* GET PACKAGE */	
//$customerPackages=travel_db_respository::getAllCustomerPackages();
//echo(json_encode($customerPackages));

$packages=getAllRowsInPackage();
foreach($packages as $package){		
	$packageId=$package->destination . "-" . $package->packagecode;
	$totalPackages=travel_db_respository::getCustomerPackageByPackageId($packageId);
	$review=rand(0, 5);
	
	SearchManager::IndexPackage($package,$packageId,count($totalPackages),$review);
}

?>

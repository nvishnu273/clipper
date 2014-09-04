<?php

// require_once('travel-package.php');
// require_once('createpackage.php');
// require_once('travel-package-instance.php');
// require_once('travel-db-repository.php');
// require_once('trip-photo-manager.php');
// require_once('trip-review-manager.php');
require_once ('search-manager.php');

date_default_timezone_set('UTC');

//$result=SearchManager::SearchPackage('chicago',4000,10000);
//$result=SearchManager::FilterByBrand('chicago','marriott');			
//$result=SearchManager::DeleteAll();		

//$result=SearchManager::FilterByBrand('chicago','marriott');

//$result=SearchManager::SearchNearbyPackages('28.438095,-81.470744');
//$result=SearchManager::SearchNearbyPackages('28.455317,-81.470998');
//$result=SearchManager::SearchByAddress('International');

$result=SearchManager::SearchNearbyPackages('35.228307,-80.84555',0.1);
echo json_encode($result);

?>

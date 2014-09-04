<?php

namespace Trip\Controller;


use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;

class TripPhotoController extends AbstractRestfulController
{	
	protected $tripPhotoTable;
	
	public function getAction()
	{	
		$data = $this->params()->fromQuery();
		$photoId = $data['id'];
		$photoUrl=$this->getTripTable()->fetch($data['id']);
		$photo=$this->getTripTable()->fetchPhotoObject($data['id']);
		$photoInfo=array(
					"photoId" => $photoId, 
					"photoUrl" => $photoUrl, 
					"photoName"=>$photo['Metadata']['photoname'],
					"photoUploadDt"=>$photo['LastModified'],
					"reviewComments"=>null, //$photo['Metadata']['Review']
					"reviewedBy"=>null, //$photo['Metadata']['ReviewedBy']
		);		

		return new JsonModel($photoInfo);	
	}

	public function create($data)
	{			
		
		$uploadDate = \date('Y-m-d H:i:s');

		$photoId = $this->getTripTable()->create(
				$data['PackageId'],
				$data['LatLong'],
				$data['Address'],
				$uploadDate,
				$_FILES["file"]["tmp_name"],
				$_FILES['file']['name']);
		
		$photo=$this->getTripTable()->fetchPhotoObject($photoId);

		$photoUrl=$this->getTripTable()->fetch($photoId);
		
		$photoInfo=array(
					"photoId" => $photoId, 
					"photoUrl" => $photoUrl, 
					"photoName"=>$photo['Metadata']['photoname'],
					"photoUploadDt"=>$photo['LastModified'],
					"reviewComments"=>null, //$photo['Metadata']['Review']
					"reviewedBy"=>null, //$photo['Metadata']['ReviewedBy']
		);	

		return new JsonModel($photoInfo);		    
	}
	
	/* returns customer package table from service locator */	
	public function getTripTable()
	{
		if (!$this->tripPhotoTable) {
	        $sm = $this->getServiceLocator();	        
	        $this->tripPhotoTable = $sm->get('Trip\Model\TripPhotoTable');	        
	    }
	    return $this->tripPhotoTable;
	}
}


<?php

namespace Trip\Model;

use Aws\Common\Enum\Region;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\ReturnValue;

use common\Utility;

class S3TripPhotoTableGateway
{
	protected $bucketName;
	protected $s3client;
	protected $prefix = 'photo';

	public function __construct($bucketName,$s3client)
	{				
		$this->bucketName = $bucketName;
		$this->s3client = $s3client;
	}
	
	public function uploadPhoto($packageId,$locationName,$pathToFile,$photoname){		
										
		$fileId=Utility::getGUID();
		$ext = pathinfo($photoname, PATHINFO_EXTENSION);
		$fileName=$fileId . "." . $ext;
		$key = $this->prefix . '/' . $fileName;
		
		$result = $this->s3client->putObject(array(
			'Bucket' => $this->bucketName,
			'Key'    => $key,
			'Body'   => fopen($pathToFile, 'r+'),
			'Metadata' => array(
				'LocationName' => $locationName,
				'PhotoName' => $photoname,
				'PackageId' => $packageId
			),			
		));
				
		return $fileName;
	}
	
	public function getPhotoPlainUrl($photoId){	
		$key = $this->prefix . '/' . $photoId;
		$plainUrl = $this->s3client->getObjectUrl($this->bucketName, $key);
		return $plainUrl;
	}	
	
	public function getPhotoPreSignedUrl($photoId){
		$key = $this->prefix . '/' . $photoId;		
		$signedUrl = $this->s3client->getObjectUrl($this->bucketName, $key, '+10 minutes');			
		return $signedUrl;
	}	
	
	public function getPhoto($photoId){		
		$key = $this->prefix . '/' . $photoId;		
		$result = $this->s3client->getObject(array(
			'Bucket' => $this->bucketName,
			'Key'    => $key
		));
		return $result;
	}		
	
}


	
?>

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
	
	public function __construct($bucketName,$s3client)
	{				
		$this->bucketName = $bucketName;
		$this->s3client = $s3client;
	}
	
	public function uploadPhoto($packageId,$locationName,$pathToFile,$photoname){		
										
		$fileId=Utility::getGUID();
		$ext = pathinfo($photoname, PATHINFO_EXTENSION);
		$fileName=$fileId . "." . $ext;
		
		$result = $this->s3client->putObject(array(
			'Bucket' => $this->bucketName,
			'Key'    => $fileName,
			'Body'   => fopen($pathToFile, 'r+'),
			'Metadata' => array(
				'LocationName' => $locationName,
				'PhotoName' => $photoname,
				'PackageId' => $packageId
			), 
			'ACL'        => 'public-read'
		));
				
		return $fileName;
	}
	
	public function getPhotoPlainUrl($photoId){		
		$plainUrl = $this->s3client->getObjectUrl($this->bucketName, $photoId);
		return $plainUrl;
	}	
	
	public function getPhotoPreSignedUrl($photoId){		
		$signedUrl = $this->s3client->getObjectUrl($this->bucketName, $photoId, '+10 minutes');		
		return $signedUrl;
	}	
	
	public function getPhoto($photoId){		
		$result = $this->s3client->getObject(array(
			'Bucket' => $this->bucketName,
			'Key'    => $photoId
		));
		return $result;
	}		
	
}


	
?>

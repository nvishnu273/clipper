<?php

namespace Account\Controller;

use Account\Form\Account;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use Zend\Crypt\BlockCipher;
use Zend\Crypt\Symmetric\Mcrypt;


use common\Utility;

class AccountController extends AbstractRestfulController
{	

	protected $accountTable;

	public function indexAction()
	{	
		
		return new JsonModel(array(
	        'Account' => 'This module is used for account management. Use <base_uri>/account/customer for customer and <base_uri>/account/agent for agent management',	        
	    ));
	    
	}	
	public function loginAction()
	{				      

		if ($this->getRequest()->isPost()){		
			$input = $this->getRequest()->getContent();
			$postedData=json_decode($input,true);
			$userName = $postedData['username'];
			$password = $postedData['password'];
			$userType = $postedData['userType'];
			$session=[];
			
			
			if (isset($userName) && isset($password)) {		

				$id=$this->getAccountTable($userType)->login($userType,$userName,$password);		
				if ($id > 0){						
					if ($userType>0){
						$session['user']=$this->getAccountTable($userType)->fetch($id);
					}
					else {						 
						$session['user'] = $this->getAccountTable($userType)->fetchUser($id);
					}					
					$dataToBeSigned=$id . ":" . $userType . ":" . time();
					
					$key=$this->getServiceLocator()->get('config')['auth_login']['key'];
					
					$session['id']=Utility::getSignedToken($dataToBeSigned,$key);
					return new JsonModel($session);					
				}
				else {
					$httpStatusCode = 400;
					$httpStatusMsg = "Login not successfull...";	
					echo($httpStatusMsg);
					http_response_code($httpStatusCode);
				}
			}			
		}
	}	

	/* account/updatepassword */
	public function updatePassword($userType,$userName,$password){	
		/*
		global $traveldb_hostname,$traveldb_database,$root_username,$root_password;
		$travel_db_conn=new travel_db_mysqli($traveldb_hostname,$root_username,$root_password,$traveldb_database);
		$password=$travel_db_conn->real_escape_string($password);	
		
		
		echo($password . PHP_EOL);		
		$pass_hash=password_hash($password,PASSWORD_DEFAULT);
		echo($pass_hash . PHP_EOL);
		//$pass_hash=password_hash($password,PASSWORD_DEFAULT);
		
		if (isset($userType) && $userType != 0){			
			$updatePasswordQuery="update TravelAppUser.User Set password='$pass_hash' Where UserName='$userName'";
		}
		else {			
			$updatePasswordQuery="update TravelAppUser.Customer Set password='$pass_hash' Where Email='$userName'";
		}		
		$travel_db_conn->query($updatePasswordQuery);		
		$affectedRows=mysql_affected_rows();
		$travel_db_conn->close();		
		return $affectedRows>0;
		*/
	}

	/* returns Customer table from service locator */
	public function getAccountTable($userType)
	{
		if (!$this->accountTable) {
			$sm = $this->getServiceLocator();	 
			if ($userType>0){					
				$this->accountTable = $sm->get('Account\Model\UserTable');	  	
			}	
	        else {	        	      
	        	$this->accountTable = $sm->get('Account\Model\CustomerTable');	  	
	        }      
	              
	    }
	    return $this->accountTable;
	}

	
}


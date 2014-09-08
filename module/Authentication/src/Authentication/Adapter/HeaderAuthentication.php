<?php
namespace Authentication\Adapter;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Http\Request;

use Account\Model\UserTable;
use Account\Model\CustomerTable;

use common\Utility;

class HeaderAuthentication implements AdapterInterface, ServiceLocatorAwareInterface  
{
    protected $request;
    protected $userrepository;
    protected $customerrepository;

    protected $accountTable;

    public function __construct(
        Request $request,
        UserTable $userrepository,
        CustomerTable $customerrepository
    ) {
        $this->request    = $request;
        $this->userrepository = $userrepository;
        $this->customerrepository = $customerrepository;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->service_manager = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->service_manager;
    }

    public function authenticate()
    {        
        $headers =  $this->request->getHeaders();
        if (!$headers->has('Authorization')) {
            return new Result(Result::FAILURE, null, array(
                'Authorization header missing'                
            ));
        }
        else { 
            $key=$this->getServiceLocator()->get('config')['auth_login']['key'];            
            $bearer_token=$headers->get('Authorization')->getFieldValue();            
            $auth_token=explode(' ',$bearer_token);            
            $data=Utility::decryptSignedToken($auth_token[1],$key);
            $auth_token_parts=explode(':',$data);             
            $user=$this->getAccountTable($auth_token_parts[1])->fetchUser($auth_token_parts[0]);
            //var_dump($user);
            return new Result(Result::SUCCESS, null, array(
                'Has Authorization header'
            ));
        }
    }

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
?>
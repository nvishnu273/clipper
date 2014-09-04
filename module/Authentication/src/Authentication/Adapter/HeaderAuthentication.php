<?php
namespace Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Http\Request;

use Account\Model\UserTable;
use Account\Model\CustomerTable;

class HeaderAuthentication implements AdapterInterface
{
    protected $request;
    protected $userrepository;
    protected $customerrepository;

    public function __construct(
        Request $request,
        UserTable $userrepository,
        CustomerTable $customerrepository
    ) {
        $this->request    = $request;
        $this->userrepository = $userrepository;
        $this->customerrepository = $customerrepository;
    }

    public function authenticate()
    {        
        $headers =  $this->request->getHeaders();
        if (!$headers->has('Authorization')) 
            return new Result(Result::FAILURE, null, array(
                'Authorization header missing'
            ));
        
        else 
            return new Result(Result::SUCCESS, null, array(
                'Has Authorization header'
            ));
        
    }
}
?>
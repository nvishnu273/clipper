<?php
use Authentication\Adapter;
use Zend\Authentication\Adapter\AdapterInterface;

class ClipperAuthAdapter implements AdapterInterface
{

    /**
     * Username
     *
     * @var string
     */
    protected $username = null;
 
    /**
     * Password
     *
     * @var string
     */
    protected $password = null;
 
    /**
     * Class constructor
     *
     * The constructor sets the username and password
     *
     * @param string $username
     * @param string $password
     */

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password, $usertype)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        // Try to fetch the user from the database using the model
        $users = new Users();
        $select = $users->select()
                        ->where('username = ?', $this->username),
                        ->where('password = ?', md5($this->password));
        $user = $users->fetchRow($select);
 
        // Initialize return values
        $code = Zend_Auth_Result::FAILURE;
        $identity = null;
        $messages = array();
 
        // Do we have a valid user?
        if ($user instanceof User) {
            $code = Zend_Auth_Result::SUCCESS;
            $identity = $user;
        } else {
            $messages[] = 'Authentication error';
        }
 
        return new Zend_Auth_Result($code, $identity, $messages);
    }
}
//http://cogo.wordpress.com/2009/02/24/custom-zend_auth_adapter-and-zend_auth_storage-classes/
//http://samsonasik.wordpress.com/2012/10/23/zend-framework-2-create-login-authentication-using-authenticationservice-with-rememberme/
//http://framework.zend.com/manual/2.0/en/modules/zend.authentication.intro.html
?>


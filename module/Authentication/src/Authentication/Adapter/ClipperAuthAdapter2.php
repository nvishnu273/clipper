<?php
class STM_Auth_Adapter implements Zend_Auth_Adapter_Interface {
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
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
 
    /**
     * Authenticate
     *
     * Authenticate the username and password
     *
     * @return Zend_Auth_Result
     */
    public function authenticate() {
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
?>
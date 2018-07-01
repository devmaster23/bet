<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authlibrary {

    /**
     * Mark a user as logged in and create autologin cookie if wanted
     * 
     * @param string $id
     * @param boolean $remember
     * @return boolean
     */

    private $ci;
    
    /**
     * Constructor, loads dependencies, initializes the library
     * and detects the autologin cookie
     */
    public function __construct($config = array()) {
        $this->ci = &get_instance();
        
        // load session library
        $this->ci->load->library('session');
        
        // initialize from config
        if (!empty($config)) {
            $this->initialize($config);
        }
        
        log_message('debug', 'Authentication library initialized');

    }

    public function login($id, $remember = TRUE) {
        if(!$this->loggedin()) {
            // mark user as logged in
            $this->ci->session->set_userdata(array('auth_user' => $id, 'logged_in' => TRUE));
            
            // if ($remember) {
            //     $this->create_autologin($id);
            // }
        }
    }
    
    /**
     * Logout the current user, destroys the current session and autologin key
     */
    public function logout() {
        // mark user as logged out
        $this->ci->session->set_userdata(array('auth_user' => FALSE, 'logged_in' => FALSE));
        
        // remove cookie and active key
        // $this->delete_autologin();
    }
    
    /**
     * Check if the current user is logged in or not
     * 
     * @return boolean
     */
    public function loggedin() {
        return $this->ci->session->userdata('logged_in');
    }
    
    /**
     * Returns the user id of the current user when logged in
     * 
     * @return int
     */
    public function userid() {
        return $this->loggedin() ? $this->ci->session->userdata('auth_user') : FALSE;
    }

    public function userInfo() {
        // load session library
        $this->ci->load->model('Users_model');
        $userId = self::userid();
        $result = array();
        if($userId)
        {
            $result = $this->ci->Users_model->getByID($userId);
        }
        return $result;
    }

}

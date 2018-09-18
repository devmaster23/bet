<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BaseController extends CI_Controller {

    public $userinfo;

    public function __construct() {
        parent::__construct();
        
        $this->load->library('authlibrary');    
        if (!$this->authlibrary->loggedin()) {
            redirect('login');
        }

        $this->userInfo = $this->authlibrary->userInfo();
    }
}
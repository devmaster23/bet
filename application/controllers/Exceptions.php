<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exceptions extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
    }
    
    public function no_orders()
    {
        $data['message'] = 'No Orders Opened';
        $this->load->view('exception/tpl1', $data);
    }
}

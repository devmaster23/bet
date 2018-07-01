<?php
 
class Index extends CI_Controller {
 
    public function __construct(){
        parent::__construct();
        $this->load->library('session');
     
    }

    public function index()
    {
        $logged_in = $this->session->userdata('logged_in');
        if($logged_in)
        {
            redirect('games', 'refresh');
        }
        else{
            redirect('login', 'refresh');
        }
    }
}

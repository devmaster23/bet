<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Screen\Capture;

class Userlogs extends CI_Controller {

    public function __construct() {
      parent::__construct();
      $this->load->library('authlibrary');
      if (!$this->authlibrary->loggedin()) {
          redirect('login');
      }
      
      $userInfo = $this->authlibrary->userInfo();
      if(!in_array($userInfo['user_type'], [0,1])){
          redirect('login');
      }

      $this->load->model('OrderLog_model', 'model');
    }
    public function index()
    {
      $data = array();
      $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
      $this->load->view('logs', $data);
    }

    public function loadData()
    {
      if(isset($_POST['betweek'])){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek; 
      }else{
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $betweek = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
      }
      
      $logs = $this->model->getData($betweek);
      $data['data'] = $logs;
      header('Content-Type: application/json');
      echo json_encode( $data );
      die;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allocations extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Investor_sportbooks_model', 'model');
        $this->load->model('Investor_model', 'investor_model');
        $this->load->library('session');
    }
    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $investors = $this->investor_model->getIdList();
        $investorId = isset($_GET['investorId'])? $_GET['investorId'] : $investors[0]['id'] ;

        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $data['investors'] = $investors;
        $data['investorId'] = $investorId;

        $this->load->view('allocations',$data);
    }

    public function loadSportbooks(){
        $investorId = $_POST['investorId'];
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        
        $investor_sportbooks = $this->investor_model->getInvestorSportboooksWithBets($investorId, $betweek);
        $data['data'] = $investor_sportbooks;
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function assign()
    {
        $investorId = $_POST['investorId'];
        $betweek = $_POST['betweek'];
        $result = $this->investor_model->assign($investorId, $betweek);
        header('Content-Type: application/json');
        echo json_encode( $result);
    }
}

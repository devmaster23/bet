<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worksheets extends CI_Controller {
    private $pageTitles = array(
        'bets'  => 'Bets',
        'bet_summary' =>'Summary', 
        'bet_sheet' =>'RR and Parlay', 
        'bets_pick' =>'Picks',
        'bets_custom' =>'Custom',
    );

    public function __construct() {
        parent::__construct();
        $this->load->model("WorkSheet_model","model");
        $this->load->model("Picks_model","pick_model");
        $this->load->model("CustomBet_model","custombet_model");
        $this->load->library('session');
    }
    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $pageType = isset($_GET['type'])? $_GET['type']: 'bets';
        $settingId = isset($_GET['id'])?$_GET['id']:-1;
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $setting = $this->model->getActiveSetting($data['betweek'],$settingId);
        $data['settingId'] = $settingId;
        $data['setting'] = $setting;
        $data['pageType'] = $pageType;
        $data['pageTitle'] = $this->pageTitles[$pageType];
        $this->load->view('worksheets/'.$pageType, $data);
    }

    public function loadCustomBet(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data['data'] = $this->custombet_model->getData($betweek);    
        $data['bets'] = $this->pick_model->getAllList($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function loadSummary(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data['summary'] = $this->model->getBetSummary($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function loadData(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data['games'] = $this->model->getGames($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function loadAllPickData(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data = $this->pick_model->getAll($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function loadPickData()
    {
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data = $this->pick_model->getIndividual($betweek, 'pick');
        header('Content-Type: application/json');
        echo json_encode( $data);      
    }

    public function loadBetSetting(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $settingId = $_POST['settingId'];
        $data = $this->model->getBetSetting($betweek,$settingId);
        header('Content-Type: application/json');
        echo json_encode( $data);   
    }

    public function loadBetSheet(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data = $this->model->getBetSheet($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);   
    }

    public function saveData(){
        $betweek = $_POST['betweek'];
        $data = $_POST['setting'];
        $this->model->saveData($betweek, $data);
        echo 'success';
        die;
    }

    public function savePickSelect(){
        $betweek = $_POST['betweek'];
        $data = $_POST['data'];
        $this->model->savePickSelect($betweek, $data);
        echo 'success';
        die;
    }

    public function saveCustomBet(){
        $betweek = $_POST['betweek'];
        $data = $_POST['data'];
        $this->custombet_model->saveCustomBet($betweek, $data);
        echo 'success';
        die;   
    }

    public function updateParlay(){
        $betweek = $_POST['betweek'];
        $data = $_POST['data'];
        $this->model->updateParlay($betweek, $data);
        echo 'success';
        die;   
    }
}


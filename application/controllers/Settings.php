<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('authlibrary');    
        if (!$this->authlibrary->loggedin()) {
            redirect('login');
        }
        $this->load->model('Settings_model', 'model');
        $this->load->model('Picks_model', 'pick_model');
        $this->load->model('SystemSettings_model', 'systemsettings_model');
        $this->load->library('session');
    }

    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $data['numberOfTeams'] = $this->model->getNumberOfTeams();
        $data['numberOfPicks'] = $this->model->getNumberOfPicks();
        
        $data['fomularData'] = $this->model->getFomular();

        $data['activeSetting'] = $this->model->getActiveSetting($data['betweek']);

        $openBetDay = $this->systemsettings_model->getBetDay();
        $data['isLocked'] = ($openBetDay == $data['betweek'] ? 0 : 1);

        $this->load->view('settings', $data);
    }

    public function loadGroupUser(){
        $categoryType = $_POST['categoryType'];
        $data = $this->model->getGroupUserList($categoryType);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function loadData(){
        $betweek            = $_POST['betweek'];
        $categoryType       = $_POST['categoryType'];
        $categoryGroupUser  = $_POST['categoryGroupUser'];

        $_SESSION['betday'] = $betweek;

        $data = $this->model->getSettings($betweek, $categoryType, $categoryGroupUser);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function saveData(){
        $betweek            = $_POST['betweek'];
        $categoryType       = $_POST['categoryType'];
        $categoryGroupUser  = $_POST['categoryGroupUser'];
        $data               = $_POST['data'];

        $data = $this->model->saveSettings($betweek, $categoryType, $categoryGroupUser,$data);
        echo 'success';
        die;
    }

    public function savelockstatus()
    {
        $betweek            = $_POST['betweek'];
        $locked             = $_POST['locked'];
        if($locked){
            $betweek = 0;
        }
        $this->systemsettings_model->updateBetDay($betweek);
        echo 'success';
        die;
    }
}

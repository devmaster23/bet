<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Settings_model', 'model');
        $this->load->model('Picks_model', 'pick_model');
    }

    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = $betweek;
        $data['numberOfTeams'] = $this->model->getNumberOfTeams();
        $data['numberOfPicks'] = $this->model->getNumberOfPicks();
        
        $data['fomularData'] = $this->model->getFomular();

        $data['activeSetting'] = $this->model->getActiveSetting($betweek);

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
}

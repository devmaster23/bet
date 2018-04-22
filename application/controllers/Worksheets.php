<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worksheets extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("WorkSheet_model","model");
        $this->load->model("Picks_model","pick_model");
    }
    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = $betweek;
        $this->load->view('worksheets', $data);
    }

    public function loadData(){
        $betweek = $_POST['betweek'];
        $data['games'] = $this->model->getGames($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function loadAllPickData(){
        $betweek = $_POST['betweek'];
        $data = $this->pick_model->getAll($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function loadPickData()
    {
        $betweek = $_POST['betweek'];
        $data = $this->pick_model->getIndividual($betweek, 'pick');
        header('Content-Type: application/json');
        echo json_encode( $data);      
    }

    public function loadBetSetting(){
        $betweek = $_POST['betweek'];
        $data = $this->model->getBetSetting($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);   
    }

    public function loadBetSheet(){
        $betweek = $_POST['betweek'];
        $data = $this->model->getBetSheet($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);   
    }

    public function saveData(){
        $betweek = $_POST['betweek'];
        $data = $_POST['setting'];
        $this->model->saveSetting($betweek, $data);
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
}

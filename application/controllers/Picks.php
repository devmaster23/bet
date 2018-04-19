<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Picks extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Picks_model', 'model');
    }
    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = $betweek;
        $this->load->view('picks', $data);
    }

    public function loadData(){
        $betweek = $_POST['betweek'];
        $data['picks'] = $this->model->get($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function loadAllPickData(){
        $betweek = $_POST['betweek'];
        $data = $this->model->getAll($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function saveData(){
        $betweek = $_POST['betweek'];
        $game_type = $_POST['game_type'];
        $picks = $_POST['picks'];

        $data['picks'] = $this->model->save($betweek, $game_type, $picks);
        echo 'success';
    }
}

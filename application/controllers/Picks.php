<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Picks extends CI_Controller {
    private $pageTitles = array(
        'all_picks'  => 'All Picks',
        'ncaa_m' =>'NCAA M', 
        'nba' =>'NBA', 
        'football' =>'NFL',
        'ncaa_f' =>'NCAA F',
        'soccer' =>'SOCCER',
        'mlb' =>'MLB'
    );

    private $pageTitleIcon = array(
        'all_picks'  => null,
        'ncaa_m' =>'icon_title_basketball.png', 
        'nba' =>'icon_title_basketball.png', 
        'football' =>'icon_title_basketball.png',
        'ncaa_f' =>'icon_title_basketball.png',
        'soccer' =>'icon_title_basketball.png',
        'mlb' =>'icon_title_basketball.png'
    );


    public function __construct() {
        parent::__construct();
        $this->load->library('authlibrary');    
        if (!$this->authlibrary->loggedin()) {
            redirect('login');
        }
        $this->load->model('Picks_model', 'model');
        $this->load->library('session');
    }
    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $pageType = isset($_GET['type'])? $_GET['type']: 'all_picks';
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $data['pageType'] = $pageType;
        $data['pageTitle'] = $this->pageTitles[$pageType];
        $data['pageTitleIcon'] = $this->pageTitleIcon[$pageType];
        $this->load->view('picks', $data);
    }

    public function loadData(){
        $betweek = $_POST['betweek'];
        $pageType = $_POST['type'];
        $_SESSION['betday'] = $betweek;
        $data['picks'] = $this->model->get($betweek, $pageType);
        $data['pageType'] = $pageType;
        $data['pageTitle'] = $this->pageTitles[$pageType];
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function loadAllPickData(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
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

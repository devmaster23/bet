<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include('BaseController.php');

class Games extends BaseController {
	private $pageTitles = array(
    'ncaa_m' =>'NCAA M', 
    'nba' =>'NBA', 
    'football' =>'NFL',
    'ncaa_f' =>'NCAA F',
    'soccer' =>'SOCCER',
    'mlb' =>'MLB'
	);

	private $pageTitleIcon = array(
		'ncaa_m' =>'icon_title_basketball.png', 
    'nba' =>'icon_title_nba.png', 
    'football' =>'icon_title_football.png',
    'ncaa_f' =>'icon_title_football.png',
    'soccer' =>'icon_title_soccer.png',
    'mlb' =>'icon_title_baseball.png'
	);

	public function __construct() {
		parent::__construct();

    if ($this->userInfo['user_type'] == 2) {
        redirect('orders');
    }

		$this->load->model('Games_model', 'model');
		$this->load->library('session');
	}
	public function index() // default ncaa_m
	{
		$date = new DateTime(date('Y-m-d'));
		$pageType = isset($_GET['type'])? $_GET['type']: 'football';
		$betweek = $date->format('W');
		$data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
		$data['pageType'] = $pageType;
		$data['pageTitle'] = $this->pageTitles[$pageType];
		$data['pageTitleIcon'] = $this->pageTitleIcon[$pageType];

		$this->load->view('games', $data);
	}

	public function loadData(){
		$betweek = $_POST['betweek'];
		$pageType = $_POST['pageType'];
		$_SESSION['betday'] = $betweek;
		$data['games'] = $this->model->getGames($betweek, $pageType);
		$data['pageType'] = $pageType;
		$data['pageTitle'] = $this->pageTitles[$pageType];

		header('Content-Type: application/json');
		echo json_encode( $data);
		die;
	}

	public function saveData(){
		$betweek = $_POST['betweek'];
		$game_type = $_POST['game_type'];
		$games = $_POST['games'];

		$data['games'] = $this->model->saveGames($betweek, $game_type, $games);
		echo 'success';
		die;
	}
}

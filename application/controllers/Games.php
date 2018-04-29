<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Games extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Games_model', 'model');
		$this->load->library('session');
	}
	public function index()
	{
		$date = new DateTime(date('Y-m-d'));
		$betweek = $date->format('W');
		$data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
		$this->load->view('games', $data);
	}

	public function loadData(){
		$betweek = $_POST['betweek'];
		$_SESSION['betday'] = $betweek;
		$data['games'] = $this->model->getGames($betweek);
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Investors extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Investor_model', 'model');
        $this->load->model('Sportbook_model', 'sportbook_model');
        $this->load->library('session');
    }
    public function index()
    {
        $this->load->view('investors');
    }

    public function add()
    {
        if(isset($_POST['add_submit']))
        {
            $this->model->addItem($_POST);
            redirect('/investors', 'refresh');
        }
        $sportbookList = $this->sportbook_model->getList();
        $data['sportbook_list'] = $sportbookList;
        $this->load->view('investors/add', $data);
    }

    public function edit()
    {
        if(isset($_POST['edit_submit']))
        {
            $id = $_POST['id'];
            $this->model->updateItem($id, $_POST);
        }
        $id = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        if(is_null($id))
        {
            redirect('/investors', 'refresh');
        }

        $investor = $this->model->getItem($id);
        $sportbookList = $this->sportbook_model->getList();
        if(is_null($investor))
            redirect('/investors', 'refresh');

        $data['investor'] = $investor;
        $data['sportbook_list'] = $sportbookList;

        $this->load->view('investors/edit', $data);
    }

    public function delete(){
        if(isset($_REQUEST['id']))
        {
            $id = $_REQUEST['id'];
            $this->model->deleteItem($id);
        }
        return true;
    }

    public function loadInvestors(){
        $data['data'] = $this->model->getList();
        header('Content-Type: application/json');
        echo json_encode( $data );
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

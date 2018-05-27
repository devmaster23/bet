<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sportbooks extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Sportbook_model', 'model');
        $this->load->library('session');
    }
    public function index()
    {
        $this->load->view('sportbooks');
    }

    public function add()
    {
        if(isset($_POST['add_submit']))
        {
            $this->model->addItem($_POST);
            redirect('/sportbooks', 'refresh');
        }

        $this->load->view('sportbooks/add');
    }

    public function edit()
    {
        if(isset($_POST['edit_submit']))
        {
            $id = $_POST['id'];
            $this->model->updateItem($id, $_POST);
            redirect('/sportbooks', 'refresh');
        }
        $id = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        if(is_null($id))
        {
            redirect('/sportbooks', 'refresh');
        }
        $sportbook_item = $this->model->getItem($id);
        if(is_null($sportbook_item))
            redirect('/sportbooks', 'refresh');
        $data['sportbook_item'] = $sportbook_item;

        $this->load->view('sportbooks/edit', $data);
    }

    public function delete(){
        if(isset($_REQUEST['id']))
        {
            $id = $_REQUEST['id'];
            $this->model->deleteItem($id);
        }
        return true;
    }

    public function loadSportbooks(){
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

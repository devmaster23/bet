<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('authlibrary');
        
        if (!$this->authlibrary->loggedin()) {
            redirect('login');
        }

        $this->load->model('Users_model', 'model');
        $this->load->library('session');
    }
    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $users = $this->model->getList();
        $data['users'] = $users;
        $this->load->view('users/list', $data);
    }

    public function user_list(){
        $data['data'] = $this->model->getList();
        header('Content-Type: application/json');
        echo json_encode( $data );
        die;
    }

    public function add()
    {
        if(isset($_POST['add_submit']))
        {
            $this->model->addItem($_POST);
            redirect('/users', 'refresh');
        }

        $this->load->view('users/add');
    }

    public function edit()
    {
        if(isset($_POST['edit_submit']))
        {
            $id = $_POST['id'];
            $this->model->updateItem($id, $_POST);
            redirect('/users', 'refresh');
        }

        $id = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        if(is_null($id))
        {
            redirect('/users', 'refresh');
        }

        $user_item = $this->model->getByID($id);
        if(is_null($user_item))
            redirect('/sportbooks', 'refresh');
        $data['user'] = $user_item;

        $this->load->view('users/edit', $data);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('authlibrary');
        
        if (!$this->authlibrary->loggedin()) {
            redirect('login');
        }

        $config = array(
        'upload_path' => "./uploads/",
        'allowed_types' => "gif|jpg|png|jpeg|pdf",
        'overwrite' => TRUE,
        'encrypt_name' => TRUE
        );

        $this->load->model('Users_model', 'model');
        $this->load->library('session');
        $this->load->library('upload', $config);
        //load form validation library
        $this->load->library('form_validation');
        
        //load file helper
        $this->load->helper('file');
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

    public function delete()
    {
        $id = $_POST['id'];
        $this->model->deleteItem($id);
        return true;
    }

    public function add()
    {
        if(isset($_POST['add_submit']))
        {
            $filename = null;
            $allowed_mime_type_arr = array('image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
            $mime = null;
            if(isset($_FILES['profile_img']))
                $mime = $_FILES['profile_img']['type'];

            if(in_array($mime, $allowed_mime_type_arr)){
                if($this->upload->do_upload('profile_img'))
                {
                    $upload_data = $this->upload->data();
                    $filename = $upload_data['file_name'];

                    try{
                        $data = array('upload_data' => $upload_data);
                    }catch(Exception $e){
                        $error = $e->getMessage();
                    }
                }else{
                    $error = 'Image can\'t be uploaded!';    
                }
            }else{
                $error = 'Please select only image file!';
            }
            if(!is_null( $filename ))
                $_POST['profile_img'] = $filename;

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
            $filename = null;
            $allowed_mime_type_arr = array('image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
            $mime = null;
            if(isset($_FILES['profile_img']) && $_FILES['profile_img']['size']){
                $mime = $_FILES['profile_img']['type'];
                if(in_array($mime, $allowed_mime_type_arr)){
                    if($this->upload->do_upload('profile_img'))
                    {
                        $upload_data = $this->upload->data();
                        $filename = $upload_data['file_name'];

                        try{
                            $data = array('upload_data' => $upload_data);
                        }catch(Exception $e){
                            $error = $e->getMessage();
                        }
                    }else{
                        $error = 'Image can\'t be uploaded!';    
                    }
                }else{
                    $error = 'Please select only image file!';
                }
            }
            if(!is_null( $filename ))
                $_POST['profile_img'] = $filename;

            $this->model->updateItem($id, $_POST);
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
        if(isset($error))
            $data['error'] = $error;

        $this->load->view('users/edit', $data);
    }
}

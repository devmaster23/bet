<?php
 
class Auth extends CI_Controller {
 
    public function __construct(){
        parent::__construct();
        $this->load->model('Users_model', 'model');
        $this->load->library('session');
        $this->load->library('authlibrary');
     
    }

    public function index()
    {
        $logged_in = $this->session->userdata('logged_in');
        if ($this->authlibrary->loggedin())
        {
            redirect('games', 'refresh');
        }
        else{
            redirect('login', 'refresh');
        }
    }

    public function user_login(){
        if(isset($_POST['login']))
        {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $user=$this->model->login_user($username,$password);
            if($user)
            {
                $this->authlibrary->login($user['id']);
                return redirect('games', 'refresh');
                exit();
            }
            else{
                $this->session->set_flashdata('error_msg', 'Invalid username and password.');
                
            }
        }
        $this->load->view('auth/login');
    }
    public function user_logout(){
      $this->authlibrary->logout();
      redirect('', 'refresh');
    }

}

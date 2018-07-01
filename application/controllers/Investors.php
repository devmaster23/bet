<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Investors extends CI_Controller {
    private $pageType = 'investors';
    public function __construct() {
        parent::__construct();
        $this->load->model('Investor_model', 'model');
        $this->load->model('Sportbook_model', 'sportbook_model');
        $this->load->model('WorkSheet_model', 'workSheet_model');
        $this->load->model('Settings_model', 'setting_model');
        $this->load->model('Groups_model', 'groups_model');
        $this->load->library('session');
    }
    public function index()
    {
        $data['pageType'] = $this->pageType;
        $data['pageTitle'] = 'Investors';
        $this->load->view('investors', $data);
    }

    public function add()
    {
        if(isset($_POST['add_submit']))
        {
            $this->model->addItem($_POST);
            redirect('/investors', 'refresh');
        }
        $sportbookList = $this->sportbook_model->getList();
        $groupList = $this->groups_model->getAll();
        $data['sportbook_list'] = $sportbookList;
        $data['group_list'] = $groupList;
        $data['pageType'] = $this->pageType;
        $this->load->view('investors/add', $data);
    }

    public function edit()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        if(isset($_POST['edit_submit']))
        {
            $id = $_POST['id'];
            $this->model->updateItem($id, $_POST);
            redirect('investors', 'refresh');
        }
        $id = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        if(is_null($id))
        {
            redirect('/investors', 'refresh');
        }

        $investor = $this->model->getItem($id,$data['betweek']);
        $sportbookList = $this->sportbook_model->getList();
        $groupList = $this->groups_model->getAll();
        if(is_null($investor))
            redirect('/investors', 'refresh');

        $data['investor'] = $investor;
        $data['sportbook_list'] = $sportbookList;
        $data['group_list'] = $groupList;
        $data['pageType'] = $this->pageType;

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

    public function sportbooks(){
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;

        $id = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        if(is_null($id))
        {
            redirect('/investors', 'refresh');
        }

        $investor = $this->model->getItem($id,$data['betweek']);

        $data['investor'] = $investor;
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $data['pageType'] = $this->pageType;

        $this->load->view('investors/sportbooks', $data);
    }

    public function loadsportbooks(){
        $investorId = $_POST['investorId'];
        $betweek = $_POST['betweek'];
        $investor_sportbooks = $this->model->getInvestorSportboooks($investorId, $betweek);
        $data['sportbook_list'] = $investor_sportbooks;
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function loadInvestors(){
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;

        $data['data'] = $this->model->getList($data['betweek']);
        header('Content-Type: application/json');
        echo json_encode( $data );
        die;
    }

    public function loadRules(){
        $betweek = $_POST['betweek'];
        $sportbookId = $_POST['sportbookId'];

        $rules = $this->sportbook_model->getItem($sportbookId);
        $activeSetting = $this->setting_model->getActiveSetting($betweek);
        $parlay = $this->workSheet_model->getParlay($betweek);
        $roundrobin = $this->workSheet_model->getRRCombination($betweek);

        $data['rules'] = $rules;
        $data['setting'] = $activeSetting;
        $data['parlay'] = $parlay;
        $data['parlay_outcome'] = $this->model->getOutcome($rules, $parlay);
        $data['roundrobin'] = $roundrobin;
        $data['rr_outcome'] = $this->model->getRROutcome($activeSetting, $rules, $roundrobin);
        header('Content-Type: application/json');
        echo json_encode( $data );
        die;
    }

    public function saveSportbookData(){
        $betweek = $_POST['betweek'];
        $data = $_POST['data'];

        $this->model->saveSportbook($betweek, $data);
        echo 'success';
        die;
    }
}

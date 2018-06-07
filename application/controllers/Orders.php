<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Order_model', 'model');
        $this->load->model('Investor_model', 'investor_model');
        $this->load->model('Sportbook_model', 'sportbook_model');
        $this->load->model('Picks_model', 'pick_model');
        $this->load->model('Settings_model', 'setting_model');
        $this->load->library('session');
    }
    public function index()
    {
        $this->load->view('orders');
    }

    public function loadInvestors(){
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;

        $data['data'] = $this->model->getInvestorList($data['betweek']);
        header('Content-Type: application/json');
        echo json_encode( $data );
        die;
    }

    public function enter_order(){
        $investorId = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        $betIndex = isset($_REQUEST['bet_id'])? $_REQUEST['bet_id'] : 1;
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $bets = $this->pick_model->getIndividual($data['betweek'], 'pick');

        if(isset($_POST['sportbookID']))
        {
            $sportbookID = $_POST['sportbookID'];
            if($sportbookID != "" && isset($bets[$betIndex-1]))
            {
                $selectedBet = $bets[$betIndex-1];
                $this->model->addOrder($data['betweek'], $investorId, $sportbookID, $selectedBet['select']);
            }
        }
        if(is_null($investorId))
        {
            redirect('/orders', 'refresh');
            exit();
        }

        $investor = $this->investor_model->getItem($investorId, $data['betweek']);

        $orders = $this->model->getOrders($data['betweek'], $investor['id']);

        $bet = isset($bets[$betIndex-1])? $bets[$betIndex-1]: null;
        if($bet)
        {
            $bet['amount'] = 100;
        }
        $data['investor'] = $investor;
        $data['bet'] = $bet;
        $data['sportbookList'] = $this->model->getSportbook($investor['sportbooks'], $data['betweek'], $investorId, $data['bet']);

        $data['sportbook'] = $this->model->getSelectedSportbook($data['sportbookList']);

        $data['setting'] = $this->setting_model->getActiveSetting($data['betweek']);
        $data['iframe_src'] = 'http://'.$data['sportbook']['siteurl'];

        $data['bet_count'] = count($bets);
        $data['bet_index'] = $betIndex;
        $this->load->view('orders/enter_order', $data);
    }

    public function balance(){
        $investorId = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        $sportbookIndex = isset($_REQUEST['sportbook_id'])? $_REQUEST['sportbook_id'] : 1;
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $setting = $this->setting_model->getActiveSetting($data['betweek']);

        if(is_null($investorId))
        {
            redirect('/orders', 'refresh');
            exit();
        }

        $investor = $this->investor_model->getItem($investorId, $data['betweek']);


        $data['investor'] = $investor;
        $sportbookList = $investor['sportbooks'];
        $total_bet = 0;
        foreach ($sportbookList as &$item) {
            $item['current_balance_bet'] = $item['current_balance'] * $setting['pick_allocation'] / 100;
            $total_bet += $item['current_balance_bet'];
        }
        $data['sportbookList'] = $sportbookList;
        $data['total_bet'] = $total_bet;
        $sportbook = isset($data['sportbookList'][$sportbookIndex-1]) ? $data['sportbookList'][$sportbookIndex-1] : null;
        $data['sportbook'] = $sportbook;
        $data['setting'] = $setting;

        $data['iframe_src'] = 'http://'.$data['sportbook']['siteurl'];

        $data['sportbook_count'] = count($investor['sportbooks']);
        $data['sportbook_id'] = $sportbookIndex;
        $this->load->view('orders/balance', $data);
    }
}

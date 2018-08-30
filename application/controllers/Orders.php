<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {
    
    private $userinfo;

    public function __construct() {
        parent::__construct();
        $this->load->library('authlibrary');    
        if (!$this->authlibrary->loggedin()) {
            redirect('login');
        }
        
        $this->userInfo = $this->authlibrary->userInfo();

        $this->load->model('Order_model', 'model');
        $this->load->model('Investor_model', 'investor_model');
        $this->load->model('Investor_sportbooks_model', 'investor_sportbooks_model');
        $this->load->model('Sportbook_model', 'sportbook_model');
        $this->load->model('Picks_model', 'pick_model');
        $this->load->model('Settings_model', 'setting_model');
        $this->load->model('WorkSheet_model', 'worksheet_model');
        $this->load->model('OrderLog_model', 'orderlog_model');
        $this->load->model('Users_model', 'users_model');
        $this->load->model('SystemSettings_model', 'systemsettings_model');
        $this->load->library('session');

        self::checkOrderOpen();
    }

    private function checkOrderOpen()
    {
        $user_type = $this->userInfo['user_type'];
        $isOpen = true;
        if($user_type == $this->users_model->ORDER_ENTRY)
        {
            $betday = $this->systemsettings_model->getBetDay();
            if($betday){
                $_SESSION['betday'] = $betday;
                $isOpen = true;
            }else{
                $isOpen = false;
            }
        }
        if(!$isOpen){
            redirect('no_orders');
        }
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

    private function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function enter_order(){
        $ip_source = $this->get_client_ip();
        $investorId = isset($_REQUEST['id'])? $_REQUEST['id'] : null;

        $betIndex = isset($_REQUEST['bet_id'])? $_REQUEST['bet_id'] : 1;
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        if(isset($_REQUEST['betday'])){
            $betweek = $_REQUEST['betday'];
            $_SESSION['betday'] = $betweek;
        }else{
            $betweek = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        }
        $data['betweek'] = $betweek;

        $this->setting_model->setActiveSetting($betweek,$investorId);

        $bets = $this->model->getOrders($data['betweek'],$investorId);
        if(isset($_POST['sportbookID']))
        {
            $sportbookID = $_POST['sportbookID'];
            $submit_type = $_POST['submit_type'];
            $betAmount = $_POST['betAmount'];

            $selectedBet = $bets[$betIndex-1];
            if($submit_type == 'reassign')
            {
                $this->model->reassignOrder($data['betweek'], $investorId, $sportbookID, $selectedBet, $betAmount);
            }else{
                if($sportbookID != "" && isset($bets[$betIndex-1]))
                {
                    $this->model->addOrder($data['betweek'], $investorId, $sportbookID, $selectedBet, $betAmount, $submit_type);
                }else{
                    $this->model->removeOrder($data['betweek'], $investorId, $selectedBet);
                }
            }
            redirect($_SERVER['HTTP_REFERER']);
        }

        if(isset($_REQUEST['bet_key']))
        {
            $bet_key = $_REQUEST['bet_key'];
            $tmpBetItem = array_filter($bets,function($item) use($bet_key){
                return $item['order_id'] == $bet_key;
            }); 
            $key_arr = array_keys($tmpBetItem);
            if(count($key_arr)){
                $betIndex = $key_arr[0]+1;
            }else{
                $betIndex = 1;
            }
        }

        if(is_null($investorId))
        {
            redirect('/orders', 'refresh');
            exit();
        }

        $investor = $this->investor_model->getItem($investorId, $data['betweek']);

        $bet = isset($bets[$betIndex-1])? $bets[$betIndex-1]: null;
        if($bet)
        {
            $filename = 'icon_NFL.png';
            switch ($bet['game_type']) {
                case 'NCAA M':
                    $filename = 'icon_NCAAM.png';
                    break;
                case 'NBA':
                    $filename = 'icon_NBA.png';
                    break;
                case 'NFL':
                    $filename = 'icon_NFL.png';
                    break;
                case 'NCAA F':
                    $filename = 'football_icon.png';
                    break;
                case 'SOC':
                    $filename = 'icon_soccer.png';
                    break;
                case 'MLB':
                default:
                    $filename = 'icon_MLB.png';
                    break;
            }
            $bet['logo'] = $filename;
        }

        $data['ip_source'] = $ip_source;
        $data['rr1'] = @$worksheet['rr1'];
        $data['investor'] = $investor;

        $data['bet'] = $bet;
        $data['sportbookList'] = $this->model->getSportbook($investor['sportbooks'], $data['betweek'], $investorId, $data['bet']);
        $data['sportbook'] = $this->model->getSelectedSportbook($data['sportbookList']);

        // $data['setting'] = $this->setting_model->getActiveSetting($data['betweek']);
        $data['iframe_src'] = 'http://'.$data['sportbook']['siteurl'];

        $data['bet_count'] = count($bets);
        $data['bet_index'] = $betIndex;
        $this->load->view('orders/enter_order', $data);
    }

    public function balance(){
        $ip_source = $this->get_client_ip();
        $investorId = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        $sportbookIndex = isset($_REQUEST['sportbook_id'])? $_REQUEST['sportbook_id'] : 1;

        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $betweek = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        if(isset($_POST['save_balance'])){
            $current_balance = $_POST['balance'];
            $investorSportbookId = $_POST['sportbookID'];
            $this->investor_sportbooks_model->setCurrentBalance($investorId, $investorSportbookId, $betweek, $current_balance);
            $this->orderlog_model->addLog($betweek, $investorId, $investorSportbookId, 'balance', $current_balance);
        }
        $data['betweek'] = $betweek;
        $setting = $this->setting_model->getActiveSetting($data['betweek']);

        if(is_null($investorId))
        {
            redirect('/orders', 'refresh');
            exit();
        }

        $investor = $this->investor_model->getItem($investorId, $data['betweek']);


        $data['ip_source'] = $ip_source;
        $data['investor'] = $investor;
        $sportbookList = $investor['sportbooks'];

        if(isset($_REQUEST['sportbook_key']))
        {
            $sportbook_key = $_REQUEST['sportbook_key'];
            $tmpBetItem = array_filter($sportbookList,function($item) use($sportbook_key){
                return $item['id'] == $sportbook_key;
            }); 
            $key_arr = array_keys($tmpBetItem);
            if(count($key_arr)){
                $sportbookIndex = $key_arr[0]+1;
            }else{
                $sportbookIndex = 1;
            }
        }

        $total_bet = 0;
        foreach ($sportbookList as &$item) {
            $total_bet += $item['current_balance'];
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include('BaseController.php');

class Allocations extends BaseController {

    public function __construct() {
        parent::__construct();

        if ($this->userInfo['user_type'] == 2) {
            redirect('orders');
        }
        
        $this->load->model('Investor_sportbooks_model', 'model');
        $this->load->model('Investor_model', 'investor_model');
        $this->load->model('Order_model', 'order_model');
        $this->load->model('WorkSheet_model', 'worksheet_model');
        $this->load->model('Settings_model', 'setting_model');
        $this->load->library('session');
    }
    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;

        $investors = $this->investor_model->getIdList($data['betweek']);
        if (count($investors)) {
            $investorId = isset($_GET['investorId'])? $_GET['investorId'] : $investors[0]['id'] ;
            $data['investors'] = $investors;
            $data['investorId'] = $investorId;
        } else {
            $data['investors'] = null;
            $data['investorId'] = null;
        }

        $data['pageType'] = 'allocations';
        $data['pageTitle'] = 'Allocation of Money';

        $this->load->view('allocations',$data);
    }

    public function reassign()
    {
        $betweek = $_POST['betweek'];
        $investorId = $_POST['investor_id'];
        $sportbookID= $_POST['reassign_sportbook_id'];
        $betAmount = $_POST['bet_amount'];
        $betIndex = $_POST['bet_id'];

        if ($betIndex) {
            $bets = $this->order_model->getOrders($betweek, $investorId);

            $selectedBet = $bets[$betIndex];
            $this->order_model->reassignOrder($betweek, $investorId, $sportbookID, $selectedBet, $betAmount);
        }

        
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function loadSportbooks(){
        $investorId = $_POST['investorId'];
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        
        /*
        $worksheet = $this->worksheet_model->getRROrders($betweek,$investorId);
        $bets = getBetArr($worksheet);
        foreach ($bets as &$bet) {
            unset($bet['data']);
        }
        */

        $bets = $this->order_model->getOrders($betweek, $investorId);
        foreach ($bets as & $bet) {
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

        // $setting = $this->setting_model->getActiveSettingByInvestor($betweek,$investorId);
        $investor_sportbooks = $this->investor_model->getInvestorSportboooksWithBets($investorId, $betweek, true);

        $data['data'] = $investor_sportbooks;
        $data['bets'] = $bets;
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function assign()
    {
        $investorId = $_POST['investorId'];
        $betweek = $_POST['betweek'];
        // $bet_amount = $_POST['bet_amount'];
        $result = $this->investor_model->assign($investorId, $betweek);
        header('Content-Type: application/json');
        echo json_encode( $result);
    }
}

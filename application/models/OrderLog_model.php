<?php
class OrderLog_model extends CI_Model {
    private $tableName = 'orderlog';
    private $pageURL = 'logs';
    private $CI = null;

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Users_model');
        $this->CI->load->model('Sportbook_model');
        $this->CI->load->model('Investor_model');
        $this->CI->load->model('WorkSheet_model');
    }

    public function addLog($betweek, $investorId, $investorSportbookId, $action , $current_balance, $bet_item = null)
    {
        $userInfo = $this->authlibrary->userInfo();
        $user_id = $userInfo['id'];
        date_default_timezone_set('America/Los_Angeles');
        $updated_at = date("Y-m-d H:i:s");
        $addDate = array(
            'betday'        => $betweek,
            'user_id'       => $user_id,
            'investor_id'   => $investorId,
            'sportbook_id'  => $investorSportbookId,
            'action'        => $action,
            'amount'        => $current_balance,
            'updated_at'    => $updated_at,
        );
        if($action != 'balance')
        {
            if(!is_null($bet_item))
            {
                $addDate['bet_id'] = $bet_item['title'];
                $addDate['bet_type'] = $bet_item['bet_type'];
                $addDate['data'] = json_encode($bet_item);
            }
        }
        
        $this->db->insert($this->tableName, $addDate);
    }

    public function getData($betweek)
    {
        $result = [];
        $rows = $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'betday' => $betweek
            ))
            ->order_by('updated_at','asc')
            ->get()->result_array();

        $user_list = $this->Users_model->getKeyValueList();
        $sportbook_list = $this->Sportbook_model->getKeyValueList();
        $investor_list = $this->Investor_model->getKeyValueList();

        $setting_arr = [];

        foreach ($rows as $key => $item) {

            $investor_id = $item['investor_id'];
            if(!array_key_exists($investor_id, $setting_arr))
            {
                $bet_setting = $this->WorkSheet_model->getRROrders($betweek,$investor_id);
                $setting_arr[$investor_id] = $bet_setting;
            }else{
                $bet_setting = $setting_arr[$investor_id];
            }

            $tmpArr = $item;
            $update_at = new DateTime($item['updated_at']);
            $tmpArr['date'] = date_format($update_at, 'm.d');
            $tmpArr['time'] = date_format($update_at, 'H:i A');
            $tmpArr['user_name'] = $user_list[$item['user_id']]['name'];
            $tmpArr['investor_name'] = $investor_list[$item['investor_id']]['first_name']. ' ' . $investor_list[$item['investor_id']]['first_name'];
            $tmpArr['sportbook_name'] = $sportbook_list[$item['sportbook_id']]['title'];
            $action_title = '';
            switch ($item['action']) {
                case 'no_bet':
                    $action_title = 'No Bet';
                    break;
                case 'reassign':
                    $action_title = 'Reassigned';
                    break;
                case 'placed':
                    $action_title = 'Placed';
                    break;
                case 'balance':
                default:
                    $action_title = 'Balance Updated';
                    break;
            }
            $tmpArr['action_title'] = $action_title;
            $tmpArr['setting'] = $bet_setting;

            $bet_data = json_decode($item['data']);
            $filename = 'icon_NFL.png';
            if($bet_data->game_type)
            {
                switch ($bet_data->game_type) {
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
            }
            $tmpArr['logo'] = $filename;

            if($item['action'] != 'balance')
               $tmpArr['amount'] = '';
            $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='edit' href='/".$this->pageURL."/edit?id=".$item['id']."'>Edit</i></a><a class='delete'>Delete</a></div>";
            $result[] = $tmpArr;
        }

        return $result;
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
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
    }

    public function addLog($betweek, $investorId, $investorSportbookId, $action , $current_balance, $bet_id = null)
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
            'bet_id'        => $bet_id,
            'amount'        => $current_balance,
            'updated_at'    => $updated_at
        );
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

        foreach ($rows as $key => $item) {
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
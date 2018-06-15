<?php
class Order_model extends CI_Model {
    private $tableName = 'orders';
    private $relationTableName = 'investor_sportbooks';
    private $pageURL = 'orders';
    private $CI = null;

    private $dbColumns = array(
        'first_name',
        'last_name',
        'address1',
        'address2',
        'state',
        'city',
        'zip_code',
        'country',
        'email',
        'phone_number',
        'starting_bankroll',
        'notes'
    );

    private $relationDbColumns = array(
        'sportbook_id',
        'date_opened',
        'opening_balance',
        'login_name',
        'password'
    );

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Investor_sportbooks_model');
        $this->CI->load->model('Order_model');
    }

    public function getInvestorList($betweek){

        $result = [];
        $rows = $this->db->select('*')
            ->from('investors')
            ->order_by('id','asc')
            ->get()->result_array();

        foreach ($rows as $key => $item) {
            $tmpArr = $item;
            $investorId = $item['id'];

            $tmpArr['sportbooks'] = $this->CI->Investor_sportbooks_model->getListByInvestorId($investorId,$betweek);
            $tmpArr['bets'] = count($this->CI->Order_model->getInvesterOrders($investorId,$betweek));
            $tmpArr['accounts'] = count($tmpArr['sportbooks']);
            $tmpArr['full_name'] = $tmpArr['first_name'] . ' ' . $tmpArr['last_name'];
            $tmpArr['current_balance'] = 0;
            foreach ($tmpArr['sportbooks'] as $sportbook_item) {
                $tmpArr['current_balance'] += $sportbook_item['current_balance_'.$betweek];
            }
            $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='' href='/".$this->pageURL."/enter_order?id=".$item['id']."'>Enter Order</a><a class='' href='/".$this->pageURL."/balance?id=".$item['id']."'>Balance</i></a></div>";

            $result[] = $tmpArr;
        }
        return $result;
    }

    public function addOrder($betweek, $investorId, $sportbookID, $betId)
    {
        $newData = array(
            'investor_id' => $investorId,
            'sportbook_id'  => $sportbookID,
            'betday'  => $betweek,
            'bet_id' => $betId,
            'bet_amount' => 100
        );

        $rows = $this->db->select('id')
        ->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'betday'  => $betweek,
            'bet_id'  => $betId
        ))->get()->result_array();

        if(count($rows)){
            $rowId = $rows[0]['id'];
            $this->db->where(array(
                'id' => $rowId
            ))->update($this->tableName, $newData);
        }else{
            $this->db->insert($this->tableName, $newData);
        }
        return true;
    }

    public function removeOrder($betweek, $investorId, $betId)
    {

        $rows = $this->db->where(array(
            'investor_id' => $investorId,
            'betday'  => $betweek,
            'bet_id'  => $betId
        ))->delete($this->tableName);

        return true;
    }

    public function getOrders($betweek, $investorId)
    {
        $result = [];

        $rows = $this->db->select('*')
        ->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'betday' => $betweek
        ))->get()->result_array();
        if(count($rows))
        {
            foreach ($rows as $item) {
                if(!isset($result[$item['sportbook_id']]))
                    $result[$item['sportbook_id']] = array();
                $result[$item['sportbook_id']][] = $item['bet_id'];
            }
        }
        return $result;
    }

    public function getSportbook($sportbooks, $betweek, $investorId, $bet)
    {
        $result = $sportbooks;
        usort($result, function($a,$b){
            if ($a['bet_count']==$b['bet_count']) return 0;
            return ($a['bet_count']<$b['bet_count'])?1:-1;
        });
        

        $rows = $this->db->select('*')
        ->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'betday' => $betweek,
            'bet_id' => $bet['title']
        ))->get()->result_array();
        foreach ($result as &$item) {
            $item['selected'] = false;
            if(count($rows) && ($item['sportbook_id'] == $rows[0]['sportbook_id']))
                $item['selected'] = true;
        }
        return $result;
    }

    public function getSelectedSportbook($sportbooks)
    {
        $result = null;
        if(count($sportbooks))
        {
            $result = $sportbooks[0];
            foreach ($sportbooks as $item) {
                if($item['selected'])
                {
                    $result = $item;
                    break;
                }
            }
        }
        return $result;
    }

    public function getInvesterOrders($investorId,$betweek)
    {
        $result = [];
        $rows = $this->db->select("*")
        ->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'betday' => $betweek
        ))->get()->result_array();
        if(count($rows))
        {
            $result = $rows;
        }
        return $result;
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
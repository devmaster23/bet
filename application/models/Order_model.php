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
            // $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='' href='/".$this->pageURL."/enter_order?id=".$item['id']."'>Enter Order</a><a class='' href='/".$this->pageURL."/balance?id=".$item['id']."'>Balance</i></a></div>";
            $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='' href='/".$this->pageURL."/enter_order?id=".$item['id']."'>Enter Order</a></div>";

            $result[] = $tmpArr;
        }
        return $result;
    }

    public function addOrder($betweek, $investorId, $sportbookID, $bet, $submit_type = null)
    {
        $betId = $bet['title'];
        $newData = array(
            'investor_id' => $investorId,
            'sportbook_id'  => $sportbookID,
            'betday'  => $betweek,
            'bet_id' => $betId
        );

        if($submit_type == 'reassign')
            $submit_type == null;
        if(!is_null($submit_type))
        {
            $newData['bet_status'] = $submit_type;
        }

        $rows = $this->db->select('*')
        ->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'betday'  => $betweek,
            'bet_id'  => $betId
        ))->get()->result_array();


        $prevSportbookId = null;
        $prevRowID = null;
        if(count($rows)){
            $prevRowID = $rows[0]['id'];
            $prevSportbookId = $rows[0]['sportbook_id'];
            $prevStatus = $rows[0]['bet_status'];
            $betTotalAmount = $rows[0]['bet_total_amount'];
        }

        $newBetTotalAmount = $bet['total_amount'];

        if(!is_null($prevSportbookId))
        {
            if($prevSportbookId != $sportbookID)
            {
                if($prevStatus == 'placed')
                {
                    $this->CI->Investor_sportbooks_model->addBalance($investorId, $prevSportbookId, $betweek, $betTotalAmount);
                }

                if($submit_type == 'placed')
                {
                    $newBetTotalAmount = $this->CI->Investor_sportbooks_model->removeBalance($investorId, $sportbookID, $betweek, $bet['total_amount']);
                }
            }else{
                if($prevStatus != 'placed' && $submit_type == 'placed')
                {
                    $newBetTotalAmount = $this->CI->Investor_sportbooks_model->removeBalance($investorId, $sportbookID, $betweek, $bet['total_amount']);
                }else if($prevStatus == 'placed' && $submit_type != 'placed'){
                    $this->CI->Investor_sportbooks_model->addBalance($investorId, $sportbookID, $betweek, $betTotalAmount);
                }                
            }
        }else{
            if($submit_type == 'placed')
            {
                $newBetTotalAmount = $this->CI->Investor_sportbooks_model->removeBalance($investorId, $sportbookID, $betweek, $bet['total_amount']);
            }
        }

        $newData['bet_total_amount'] = $newBetTotalAmount;

        if(!is_null($prevRowID)){
            $this->db->where(array(
                'id' => $prevRowID
            ))->update($this->tableName, $newData);

        }else{
            $this->db->insert($this->tableName, $newData);
        }

        return true;
    }

    public function removeOrder($betweek, $investorId, $bet)
    {
        $betId = $bet['title'];
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

        $rows = $this->db->select('*')
        ->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'betday' => $betweek,
            'bet_id' => $bet['title']
        ))->get()->result_array();

        $orders = $this->db->select('*')
        ->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'betday' => $betweek
        ))->get()->result_array();

        $bet_count_list = array();
        foreach ($orders as $order_item) {
            $sprotbook_id = $order_item['sportbook_id'];
            if(!isset($bet_count_list[$sprotbook_id]))
                $bet_count_list[$sprotbook_id] = array(
                    'bet_left' => 0,
                    'bet_placed' => 0
                );
            if($order_item['bet_status'] == 'placed')
                $bet_count_list[$sprotbook_id]['bet_placed'] ++;
            else 
                $bet_count_list[$sprotbook_id]['bet_left'] ++;
        }

        foreach ($result as &$item) {
            $item['selected'] = false;
            $item['status'] = null;
            if(count($rows) && ($item['sportbook_id'] == $rows[0]['sportbook_id']))
            {
                $order = $rows[0];
                $item['selected'] = true;
                $item['status'] = $order['bet_status'];
            }
            $item['bet_left'] = @$bet_count_list[$item['sportbook_id']]['bet_left'];
            $item['bet_placed'] = @$bet_count_list[$item['sportbook_id']]['bet_placed'];
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
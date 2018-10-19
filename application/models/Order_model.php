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

    private $sordOrder = array(
        'rr' => 1,
        'crr' => 2,
        'parlay' => 3,
        'cparlay' => 4,
        'single' => 5
    );

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Investor_sportbooks_model');
        $this->CI->load->model('OrderLog_model');
        $this->CI->load->model('Order_model');
        $this->CI->load->model('Settings_model');
    }

    public function getInvestorList($betweek){

        $result = [];
        $rows = $this->db->select('*')
            ->from('investors')
            ->order_by('id','asc')
            ->get()->result_array();

        $isOpenList = $this->CI->Settings_model->getOpenList($betweek);

        foreach ($rows as $key => $item) {
            $group_id = $item['group_id'];
            $investor_id = $item['id'];
            $allow = false;
            if (in_array($investor_id, $isOpenList['user'])) {
                $allow = true;
            }
            if (in_array($group_id, $isOpenList['group'])) {
                $allow = true;
            }
            if ($isOpenList['all']) {
                $allow = true;
            }
            if(!$allow)
                continue;
            $tmpArr = $item;
            $investorId = $item['id'];

            $tmpArr['sportbooks'] = $this->CI->Investor_sportbooks_model->getListByInvestorId($investorId,$betweek);
            $tmpArr['bets'] = count($this->CI->Order_model->getInvesterOrders($investorId,$betweek));
            $tmpArr['accounts'] = count($tmpArr['sportbooks']);
            $tmpArr['full_name'] = $tmpArr['first_name'] . ' ' . $tmpArr['last_name'];
            $tmpArr['current_balance'] = 0;

            $tmpArr['total_bets'] = $this->getTotalBetCount($betweek, $investorId);

            foreach ($tmpArr['sportbooks'] as $sportbook_item) {
                $tmpArr['current_balance'] += $sportbook_item['current_balance_'.$betweek];
            }
            $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='' href='/".$this->pageURL."/enter_order?id=".$item['id']."&showintro=1'>Enter Order</a><a class='' href='/".$this->pageURL."/balance?id=".$item['id']."'>Balance</i></a></div>";

            $result[] = $tmpArr;
        }
        return $result;
    }

    public function getTotalBetCount($betweek, $investorId = null)
    {
        $worksheet = $this->worksheet_model->getRROrders($betweek,$investorId);
        $bets = getBetArr($worksheet);
        return count($bets);
    }

    public function reassignOrder($betweek, $investorId, $sportbookID, $bet, $newBetAmount)
    {
        $submit_type = 'reassign';
        $orderId = $bet['order_id'];
        $betId = $bet['title'];
        $betAmount = $bet['bet_amount'];
        $m_number = $bet['m_number'];
        if($betAmount <= $newBetAmount){
            $rows = $this->db->where(array(
                'id'  => $orderId
            ))->update($this->tableName, array(
                'sportbook_id' => $sportbookID
            ));
            $newBetTotalAmount = $bet['total_amount'];
        }else{
            $newBetTotalAmount = $newBetAmount * $m_number;

            $leftBalance = $betAmount - $newBetAmount;
            $leftBetTotalAmount = $leftBalance * $m_number;
            
            $rows = $this->db->where(array(
                'id'  => $orderId
            ))->update($this->tableName, array(
                'bet_total_amount' => $leftBetTotalAmount,
                'bet_amount' => $leftBalance
            ));

            $newData = array(
                'investor_id' => $investorId,
                'sportbook_id'  => $sportbookID,
                'betday'  => $betweek,
                'bet_id' => $betId,
                'bet_type' => $bet['bet_type'],
                'bet_amount' => $newBetAmount,
                'bet_total_amount' => $newBetTotalAmount,
            );

            $this->db->insert($this->tableName, $newData);
            $newId = $this->db->insert_id();
            $bet['order_id'] = $newId;
        }

        $bet['amount'] = $newBetAmount;
        $bet['total_amount'] = $newBetTotalAmount;

        $this->OrderLog_model->addLog($betweek, $investorId, $sportbookID, $submit_type, $newBetAmount, $bet);
        return true;
    }
    public function addOrder($betweek, $investorId, $sportbookID, $bet, $betAmount, $submit_type = null)
    {
        $newData = array(
            'investor_id' => $investorId,
            'sportbook_id'  => $sportbookID,
            'betday'  => $betweek,
            'bet_type' => $bet['bet_type'],
            'bet_id' => $bet['title'],
            'bet_status' => $submit_type
        );

        $rows = $this->db->select('*')
        ->from($this->tableName)
        ->where(array(
            'id' => $bet['order_id']
        ))->get()->result_array();


        $prevSportbookId = null;
        $prevRowID = null;
        if(count($rows)){
            $prevRowID = $rows[0]['id'];
            $prevSportbookId = $rows[0]['sportbook_id'];
            $prevStatus = $rows[0]['bet_status'];
            $betTotalAmount = $rows[0]['bet_total_amount'];
        }
        if($prevSportbookId == $sportbookID && $prevStatus == $submit_type)
            return;
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
            $insert_id = $prevRowID;
        }else{
            $this->db->insert($this->tableName, $newData);
            $insert_id = $this->db->insert_id();
        }
        $bet['order_id'] = $insert_id;

        $this->OrderLog_model->addLog($betweek, $investorId, $sportbookID, $submit_type, $betTotalAmount, $bet);
        return true;
    }

    public function removeOrder($betweek, $investorId, $bet)
    {
        $rows = $this->db->where(array(
            'id' => $bet['order_id']
        ))->delete($this->tableName);

        return true;
    }

    public function getOrders($betweek, $investorId)
    {
        $result = [];
        $worksheet = $this->worksheet_model->getRROrders($betweek, $investorId);
        $bets = getBetArr($worksheet);

        $rows = $this->db->select('*')
        ->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'betday' => $betweek
        ))
        ->get()->result_array();

        if(count($rows))
        {
            usort($rows, function($a,$b){
                if ($this->sordOrder[$a['bet_type']] == $this->sordOrder[$b['bet_type']]) {
                    if($a['bet_id'] == $b['bet_id'])
                    {
                        return $a['id'] > $b['id'] ? 1 : -1;
                    }
                    return $a['bet_id'] > $b['bet_id'] ? 1 : -1;    
                }
                return ($this->sordOrder[$a['bet_type']] > $this->sordOrder[$b['bet_type']]) ? 1 : -1;
            });
            foreach ($rows as $item) {
                $bet_item = array_filter($bets, function($tmp) use($item){
                    return ($tmp['title'] == $item['bet_id']) && ($tmp['bet_type'] == $item['bet_type']);
                });
                if($bet_item){
                    $result_item = reset($bet_item);
                    $result_item['bet_amount'] = $item['bet_amount'];
                    $result_item['total_amount']  = $item['bet_total_amount'];
                    $result_item['order_id']  = $item['id'];
                    $result_item['sportbook_id']  = $item['sportbook_id'];
                    $result[] = $result_item;
                }else{
                    continue;
                }
            }
        }

        return $result;
    }

    public function getSportbook($sportbooks, $betweek, $investorId, $bet)
    {
        $result = $sportbooks;

        $rows = $this->db->select('*')
        ->from($this->tableName)
        ->where('id', $bet['order_id'])->get()->result_array();

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

        usort($result, function($a,$b){
            if ($a['current_balance'] == $b['current_balance']) {
                return 0;
            }
            return ($a['current_balance'] > $b['current_balance']) ? -1 : 1;
        });
        
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
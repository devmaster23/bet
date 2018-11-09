<?php
class Investor_model extends CI_Model {
    private $tableName = 'investors';
    private $relationTableName = 'investor_sportbooks';
    private $pageURL = 'investors';
    private $CI = null;
    private $bet_amounts = [];

    private $dbColumns = array(
        'group_id',
        'first_name',
        'last_name',
        'address1',
        'address2',
        'state',
        'city',
        'zip_code',
        'country',
        'email',
        'ip',
        'phone_number',
        'starting_bankroll',
        'note'
    );

    private $relationDbColumns = array(
        'sportbook_id',
        'date_opened',
        'opening_balance',
        'login_name',
        'password'
    );

    private $groups;

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Investor_sportbooks_model');
        $this->CI->load->model('WorkSheet_model');
        $this->CI->load->model('Groups_model');
        $this->CI->load->model('Settings_model');
        $this->CI->load->model('Order_model');

        $this->groups = $this->CI->Groups_model->getAll();
    }

    public function getAll(){
        $this->db->select("id, CONCAT(first_name, ' ' ,last_name)  AS name")
            ->from($this->tableName)
            ->order_by('name','asc');
        $result = $this->db->get()->result_array();;
        return $result;
    }

    public function getAllWithOpenStatus( $betday ){
        $this->db->select("G.id, CONCAT(G.first_name, ' ' ,G.last_name)  AS name, S.is_open")
            ->from($this->tableName.' as G')
            ->join('settings as S', 'S.betday = '.$betday.' AND S.type = 2 AND S.groupuser_id = G.id', 'left')
            ->order_by('name','asc');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function getKeyValueList()
    {
        $result = [];
        $this->db->select('*')
            ->from($this->tableName)
            ->order_by('id','asc');
        $rows = $this->db->get()->result_array();
        foreach ($rows as $item) {
            $result[$item['id']] = $item;
        }
        return $result;
    }

    public function getByID($id){
        $this->db->select("id, CONCAT(first_name, ' ', last_name)  AS name, group_id")
            ->from($this->tableName)
            ->where('id',$id);
        $rows = $this->db->get()->result_array();
        $result = null;
        if(count($rows))
            $result = $rows[0];
        return $result;   
    }

    public function getUserGroup($userId){
        $groupId = null;
        $this->db->select('group_id')
            ->from($this->tableName)
            ->where('id',$userId);

        $rows = $this->db->get()->result_array();
        if(count($rows))
            $groupId = $rows[0]['group_id'];
        return $groupId;
    }

    public function getGroupInvestors($groupId)
    {
        $investorIds = [];
        $this->db->select('id')->from($this->tableName)->where('group_id', $groupId);
        $rows = $this->db->get()->result_array();
        foreach ($rows as $row) {
            $investorIds[] = $row['id'];
        }

        return $investorIds;
    }

    private function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

        if(strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);

            $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
        }
        else if(strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
        }
        else if(strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);

            $phoneNumber = $nextThree.'-'.$lastFour;
        }

        return $phoneNumber;
    }

    public function getIdList( $betweek )
    {
        $result = [];
        $rows = $this->db->select('*')
            ->from($this->tableName)
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

            $result[] = array(
                'id' => $item['id'],
                'name' => $item['first_name'] . ' ' . $item['last_name']
            );
        }
        return $result;
    }

    private function groupName($id)
    {
        $result = '';
        foreach ($this->groups as $group) {
            if($group['id'] == $id)
                $result = $group['name'];
        }
        return $result;
    }

    public function getList($betweek){

        $result = [];
        $rows = $this->db->select('*')
            ->from($this->tableName)
            ->order_by('id','asc')
            ->get()->result_array();

        $settingOpenList = $this->CI->Settings_model->getOpenList($betweek);

        foreach ($rows as $key => $item) {
            $tmpArr = $item;
            $investorId = $item['id'];

            $tmpArr['sportbooks'] = $this->CI->Investor_sportbooks_model->getListByInvestorId($investorId,$betweek);
            $tmpArr['bets'] = 0;
            $tmpArr['accounts'] = count($tmpArr['sportbooks']);
            $tmpArr['full_name'] = $tmpArr['first_name'] . ' ' . $tmpArr['last_name'];
            $tmpArr['phone_number'] = $this->formatPhoneNumber($tmpArr['phone_number']);
            $tmpArr['current_balance'] = 0;
            $tmpArr['number_sportbooks'] = count($tmpArr['sportbooks']);
            $tmpArr['group_label'] = $this->groupName($item['group_id']);
            foreach ($tmpArr['sportbooks'] as $sportbook_item) {
                $tmpArr['current_balance'] += $sportbook_item['current_balance_'.$betweek];
            }
            $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='sportbooks' href='/".$this->pageURL."/sportbooks?id=".$item['id']."'>Sportbooks</i></a><a class='edit' href='/".$this->pageURL."/edit?id=".$item['id']."'>Edit</i></a><a class='delete'>Delete</a></div>";

            $result[] = $tmpArr;
        }
        return $result;
    }

    public function getItem($id=null, $betweek)
    {

        $result = null;
        $row = $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'id' => $id
            ))
            ->get()->result_array();
        if(count($row))
        {
            $result = $row[0];
            $investorId = $result['id'];
            $result['full_name'] = $result['first_name'] . ' ' . $result['last_name'];
            $result['sportbooks'] = $this->CI->Investor_sportbooks_model->getListByInvestorId($investorId,$betweek);
            $result['current_balance'] = 0;
            foreach ($result['sportbooks'] as $sportbook_item) {
                $result['current_balance'] += $sportbook_item['current_balance_'.$betweek];
            }
        }
        return $result;
    }

    public function getInvestorSportboooks($investorId, $betweek){
        $result = [];
        $sprotbookList = $this->CI->Investor_sportbooks_model->getListByInvestorId($investorId,$betweek);
        $total_opening_balance = 0;
        $total_current_balance = 0;
        foreach ($sprotbookList as $key => $sportbook_item) {
            $tmpArr = $sportbook_item;
            $total_current_balance += floatval($sportbook_item['current_balance_'.$betweek]);
            $total_opening_balance += floatval($sportbook_item['opening_balance']);
            $opening_balance = floatval($sportbook_item['opening_balance']);
            $current_balance = floatval($sportbook_item['current_balance_'.$betweek]);
            $current_change = $opening_balance != 0 ? $current_balance / $opening_balance : 0;
            $tmpArr['current_change'] = number_format($current_change * 100,2, '.', '');
            $tmpArr['current_balance'] = number_format($current_balance,2);
            $tmpArr['opening_balance'] = number_format($opening_balance,2);
            $result[] = $tmpArr;
        }
        $total_current_change = $total_opening_balance != 0 ? $total_current_balance / $total_opening_balance : 0;
        $result[] = array(
           'current_balance' =>  number_format($total_current_balance, 2),
           'opening_balance' =>  number_format($total_opening_balance, 2),
           'current_change' =>  number_format($total_current_change * 100,2, '.', ''),
        );
        return $result;
    }

    public function fetchBetAmount($betday, $inverstor_id)
    {
        // Load bet amounts for this investor
        $sql = "SELECT * FROM `bet_amounts` WHERE `betday`='$betday' AND `investor_id`='$inverstor_id'";
        $row = $this->db->query($sql)->row();
        if ($row) {
            $this->bet_amounts = json_decode($row->data, true);
        }
    }

    public function getAmountPerItem($item)
    {
        $bet_amount = 0;
        switch ($item['bet_type']) {
            case 'crr':
            case 'cparlay':
                // Remove 'c' from the beginning of the title
                $bet_amount = $this->bet_amounts[substr($item['title'], 1)] ?? 0;
                break;
            default:
                $bet_amount = $this->bet_amounts[$item['bet_type']] ?? 0;
                break;
        }

        return $bet_amount;
    }

    public function assign($investorId, $betweek, $bet_amount = 1)
    {
        $this->fetchBetAmount($betweek, $investorId);

        $result = [];
        $sprotbookList = $this->getInvestorSportboooksWithBets($investorId,$betweek);
        $totalBalance = 0;
        $totalValidBalance = 0;
        $worksheet = $this->WorkSheet_model->getRROrders($betweek, $investorId);
        $assignes = getBetArr($worksheet);

        foreach ($sprotbookList as $key => $sportbook_item)
        {
            if($sportbook_item['is_valid'])
                $totalBalance += floatval($sportbook_item['current_balance']);
        }

        $this->db->where(array(
            'betday' => $betweek,
            'investor_id' => $investorId,
        ))->delete('orders');

        $tmpBetCount = 0;
        usort($sprotbookList, function($a,$b){
            if ($a['current_balance'] == $b['current_balance']) {
                return 0;
            }
            return ($a['current_balance'] > $b['current_balance']) ? -1 : 1;
        });

        foreach ($sprotbookList as $key => $item) {
            if($item['is_valid'])
            {
                $betPercent = $item['valid_percent'];
                $betCount = $item['valid_bet_count'];
                $betAssign = array_slice($assignes, $tmpBetCount, $betCount);
                $tmpBetCount += $betCount;

                foreach ($betAssign as $betItem) {
                    $bet_amount = $this->getAmountPerItem($betItem);
                    $orderData = array(
                        'betday'        => $betweek,
                        'investor_id'   => $investorId,
                        'sportbook_id'  => $item['id'],
                        'bet_type'      => $betItem['bet_type'],
                        'bet_id'        => $betItem['title'],
                        'bet_amount'    => $bet_amount,
                        'bet_total_amount' => $bet_amount * $betItem['m_number']
                    );
                    $this->db->insert('orders', $orderData);
                }
            }

        }

        // $this->CI->Settings_model->setBetAmount($betweek, $investorId, $bet_amount);
        return array('status'=>'success');
    }

    /**
     * Description
     * @param type $investorId 
     * @param type $betweek 
     * @param type|bool $fromOrders 
     * $fromOrders == true => get Orders
     * $fromOrders == false => No orders yet, get bets instead
     * @return sportbook list with bets
     */
    public function getInvestorSportboooksWithBets($investorId, $betweek, $fromOrders = false){
        $result = [];
        $sprotbookList = $this->CI->Investor_sportbooks_model->getListByInvestorId($investorId,$betweek);
        $totalBalance = 0;
        $totalValidBalance = 0;
        $sportbookCount = count($sprotbookList);

        if ($fromOrders) {
            $bets = $this->order_model->getOrders($betweek, $investorId);
        }
        else {   
            $worksheet = $this->WorkSheet_model->getRROrders($betweek,$investorId);
            $bets = getBetArr($worksheet);
        }

        $totalBetCount = count($bets);
        foreach ($sprotbookList as $key => $sportbook_item)
            $totalBalance += floatval($sportbook_item['current_balance_'.$betweek]);

        // Get current betday setting
        $query = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betweek,
                'groupuser_id'  => $investorId
            ));
        $row = $query->get()->row_array();
        $total_bet_percent = $row ? $row['bet_allocation'] : 0;

        foreach ($sprotbookList as $key => $sportbook_item) {
            $tmpArr = array(
                'id' => $sportbook_item['id'],
                'title' => $sportbook_item['title']
            );
            $tmpArr['current_balance'] = floatval($sportbook_item['current_balance_'.$betweek]);
            $tmpArr['percent'] = $totalBalance == 0 ? 0 : $tmpArr['current_balance'] / $totalBalance * 100;
            $tmpArr['equal_percent'] = $sportbookCount == 0 ? 0 : 100 / $sportbookCount;
            $tmpArr['bet_count'] = $sportbook_item['bet_count'];
            $tmpArr['is_valid'] = false;
            if($tmpArr['percent'] >= $tmpArr['equal_percent'] / 2)
            {   
                $tmpArr['is_valid'] = true;
                $totalValidBalance += $tmpArr['current_balance'];
            }

            $tmpArr['percent'] = number_format((float)$tmpArr['percent'], 2, '.', '');
            $tmpArr['equal_percent'] = number_format((float)$tmpArr['equal_percent'], 2, '.', '');
            $tmpArr['valid_percent'] = 0;
            $tmpArr['valid_bet_count'] = 0;
            $tmpArr['balance_left'] = $tmpArr['current_balance'];
            $result[] = $tmpArr;
        }

        $tmpBetCount = 0;
        usort($result, function($a,$b){
            if ($a['current_balance'] == $b['current_balance']) {
                return 0;
            }
            return ($a['current_balance'] > $b['current_balance']) ? -1 : 1;
        });

        $tempKey = null;
        foreach ($result as $key => &$item) {
            if($item['is_valid'])
            {
                $tempKey = $key;
                $item['valid_bet_count'] = 0;
                $item['valid_percent'] = $totalValidBalance == 0 ? 0 : $item['current_balance'] / $totalValidBalance * 100;
                $item['valid_percent'] = number_format((float)$item['valid_percent'], 2, '.', '');
                if ($fromOrders) {
                    $item['valid_bet_count'] = $item['bet_count'];
                    // This is after orders are available or reassigned
                    foreach ($bets as $betItem) {
                        // filters balance for this sportbook only
                        if ($betItem['sportbook_id'] == $item['id']) {
                            $item['balance_left'] -= $betItem['total_amount'];
                        }
                    }
                }
                elseif($tmpBetCount <= $totalBetCount)
                {
                    // $item['valid_bet_count'] = round($totalBetCount * $item['valid_percent'] / 100);
                    $allowed_balance = intval($item['current_balance'] * $total_bet_percent / 100);
                    for ($i = $tmpBetCount; $i < count($bets); $i ++) {
                        $allowed_balance -= $bets[$i]['total_amount'];
                        if ($allowed_balance <= 0) break;
                        
                        $item['valid_bet_count'] += 1;
                    }
                    if($tmpBetCount + $item['valid_bet_count'] > $totalBetCount)
                        $item['valid_bet_count'] = $totalBetCount - $tmpBetCount;

                    $betAssign = array_slice($bets, $tmpBetCount, $item['bet_count']);
                    $tmpBetCount += $item['valid_bet_count'];
                    foreach ($betAssign as $betItem) {
                        $item['balance_left'] -= $betItem['total_amount'];
                    }
                    if($tmpBetCount > $totalBetCount)
                        break;
                }
            }
        }
        if($tmpBetCount < $totalBetCount && !is_null($tempKey)){
            $result[$tempKey]['valid_bet_count'] += $totalBetCount - $tmpBetCount;
        }

        return $result;
    }

    private function formatRelationItem($investor_id,$data){
        $result = array(
            'investor_id' => $investor_id,
        );

        foreach ($this->relationDbColumns as $column) {
            if(!isset($data->$column))
                continue;
            if($column == 'date_opened')
                $value = date_format(date_create($data->$column),"Y-m-d");
            else
                $value = $data->$column;
            $result[$column] = $value;
        }
        return $result;
    }

    public function addItem($data)
    {
        $addDate = [];
        foreach ($this->dbColumns as $dbColumn) {
            if(isset($data[$dbColumn]))
                $addDate[$dbColumn] = $data[$dbColumn];
        }
        $this->db->insert($this->tableName, $addDate);
        $investor_id = $this->db->insert_id();

        $addSportbookDate = [];
        $sportbook_data = json_decode($data['sportbook_data']);
        foreach ($sportbook_data as $sportbook_item) {
            $addSportbookDate[] = $this->formatRelationItem($investor_id, $sportbook_item);
        }
        foreach ($addSportbookDate as $newItem) {
            $this->db->insert($this->relationTableName, $newItem);
        }
        return true;
    }

    public function updateItem($id, $data)
    {
        $updateData = [];
        foreach ($this->dbColumns as $dbColumn) {
            $value = '';
            if($dbColumn == 'starting_bankroll'){
                $value = '';
            }else{
                if(isset($data[$dbColumn]))
                    $value = $data[$dbColumn];
            }
            $updateData[$dbColumn] = $value;
        }

        $addSportbookDate = array();
        $sportbook_data = json_decode($data['sportbook_data']);

        $validIds = [];
        $starting_bankroll = 0;
        foreach ($sportbook_data as $sportbook_item) {
            $rowData = $this->formatRelationItem($id, $sportbook_item);
            if($sportbook_item->relation_id == -1)
            {
                array_push($addSportbookDate, $rowData);
            }else{
                $validIds[] = $sportbook_item->relation_id;
                $updateSportbookData = $rowData;
                $this->db->where(array(
                    'id' => $sportbook_item->relation_id
                ))->update($this->relationTableName,$updateSportbookData);
            }
            $starting_bankroll += $sportbook_item->opening_balance;
        }
        $updateData['starting_bankroll'] = $starting_bankroll;
        $this->db->where(array(
            'id' => $id
        ))->update($this->tableName,$updateData);

        if(count($validIds))
        {
            $this->db->where('investor_id',$id)
            ->where_not_in('id', $validIds)
            ->delete($this->relationTableName);
        }else{
            $this->db->where('investor_id',$id)
            ->delete($this->relationTableName);
        }

        foreach ($addSportbookDate as $newItem) {
            $this->db->insert($this->relationTableName, $newItem);
        }

        return true;
    }
    
    public function deleteItem($id)
    {
       $this->db->where(array(
            'id' => $id
        ))->delete($this->tableName);
       return ture;
    } 

    public function saveSportbook($betweek,$data){
        $data = json_decode($data);
        $sportbookData = $data->data;
        date_default_timezone_set('America/Los_Angeles');
        $updated_at = date("Y-m-d H:i:s");
        foreach ($sportbookData as $sportbook_item) {
            if(!isset($sportbook_item->id))
                continue;
            $updateSportbookData = array(
                'current_balance_'.$betweek => $sportbook_item->current_balance
            );
            $rows = $this->db->select('current_balance_'.$betweek.' as current_balance')
                ->from($this->relationTableName)
                ->where(array(
                    'id' => $sportbook_item->id
                ))->get()->result_array();
            $current_balance = @$rows[0]['current_balance'];
            if($current_balance != $sportbook_item->current_balance)
            {
                $updateSportbookData['updated_at'] = $updated_at;
            }
            $this->db->where(array(
                'id' => $sportbook_item->id
            ))->update($this->relationTableName,$updateSportbookData);
        }
        return true;
    }   

    public function getOutcome($rules, $parlay)
    {
        $result = [];
        $initial_bet = 1000;
        if(count($parlay))
        {   
            $index = 1;
            foreach ($parlay[0] as $team) {
                $tmpArr = array(
                    'title' => 'After Bet '.$index,
                );
                if( $index == 1)
                    $before = intval($initial_bet);
                else
                    $before = intval($result[$index-2]['after']);
                if($team['line'] > 0)
                    $payout_win = $before * ($team['line']/100);
                else
                    $payout_win = $team['line'] == 0 ? 0: $before / ($team['line']/100*(-1));
                $after = $before + $payout_win;
                $tmpArr['before'] = number_format((float)$before, 2, '.', ',');
                $tmpArr['payout_win'] = number_format((float)$payout_win, 2, '.', ',');
                $tmpArr['after'] = number_format((float)$after, 2, '.', ',');

                $result[] = $tmpArr;
                $index ++;
            }
        }
        return $result;
    }

    public function getRROutcome($activeSetting, $rules, $teamList)
    {
        $result = array(
            'sheet1'=> [],
            'sheet2'=> [],
            'sheet3'=> []
        );

        $rr1 = $activeSetting['rr_number1'];
        $rr2 = $activeSetting['rr_number2'];
        $rr3 = $activeSetting['rr_number3'];
        $rr4 = $activeSetting['rr_number4'];

        // sheet 1
        $result['sheet1'] = $this->buildRuleSheet($rr1, $rr2, $teamList);
        $result['sheet2'] = $this->buildRuleSheet($rr1, $rr3, $teamList);
        $result['sheet3'] = $this->buildRuleSheet($rr1, $rr4, $teamList);
        return $result;
    }

    private function buildRuleSheet($rr1, $rr2, $teamList)
    {   
        $data = [];
        $startArray = range(1,$rr1);
        $result = [];
        if(is_array($startArray) && count($startArray) && $rr2 > 0){
            $keyList = self::getRRKey($startArray, $rr2);
            foreach ($keyList as $keyItem) {
                $teamArr = [];
                foreach ($keyItem as $key) {
                    if (isset($teamList[$key-1])) {
                        $teamArr[] = $teamList[$key-1];
                    }
                }
                $data[] = $teamArr;
            }

            $initial_bet = 100;
            $overall_bet = 0;
            $overall_outcome = 0;
            foreach ($data as $dataItem) {
                $row = array(
                    'team' => '',
                    'line' => '',
                    'bet'  => $initial_bet,
                    'outcome' => 0
                );
                if(count($dataItem))
                {
                    $teams = [];
                    $lines = [];
                    $outcome = $initial_bet;

                    foreach ($dataItem as $key => $item) {
                        $teams[] = $item['team'];
                        if($item['team'] == '' || $item['line'] == '')
                            continue;
                        $lines[] = $item['line'];
                        $outcome = $outcome + (($item['line'] > 0) ? $outcome * $item['line'] / 100 : @($outcome  / $item['line']) * 100 * (-1));
                    }
                    $row['team'] = join(', ',$teams);
                    $row['line'] = join(' / ',$lines);
                    $row['outcome'] = number_format((float)$outcome, 2, '.', '');

                    $overall_bet += $initial_bet;
                    $overall_outcome += $outcome;
                }
                $result[] = $row;
            }

            $result[] = array(
                'team' => 'A '.$rr1.'-'.$rr2.' Round Robin is',
                'line' => count($data).' Parlays',
                'bet'  => number_format($overall_bet,2),
                'outcome' => number_format((float)$overall_outcome, 2, '.', ',')
            );
        }

        return $result;
    }

    private function getRRKey($startArray, $size, $combinations = array()) {

        # if it's the first iteration, the first set 
        # of combinations is the same as the set of characters
        if (empty($combinations)) {
            $combinations = $startArray;
        }

        # we're done if we're at size 1
        if ($size == 1) {
            return $combinations;
        }

        # initialise array to put new values in
        $new_combinations = array();

        # loop through existing combinations and character set to create strings
        foreach ($combinations as $combination) {
            foreach ($startArray as $char) {
                if(is_array($combination))
                    $tmpArr = $combination;
                else
                    $tmpArr = [$combination];
                if($char <= end($tmpArr))
                    continue;
                $tmpArr[] = $char;
                $new_combinations[] = $tmpArr;
            }
        }
        # call same function again for the next iteration
        return self::getRRKey($startArray, $size - 1, $new_combinations);

    }
    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
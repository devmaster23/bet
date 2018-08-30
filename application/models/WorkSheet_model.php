<?php
class WorkSheet_model extends CI_Model {
    private $tableName = 'work_sheet';
    private $CI = null;
    private $hypoBetAmount;

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Investor_model');
        $this->CI->load->model('Settings_model');
        $this->CI->load->model('Picks_model');
        $this->CI->load->model('CustomBet_model');
    }

    private function getRobbinSetting($betday, $settingId = -1){
        if($settingId != -1)
            return $this->CI->Settings_model->getActiveSetting($betday, $settingId);
        else
            return $this->CI->Settings_model->getAppliedSetting($betday);
    }   

    public function getBetSummary($betday)
    {
        $settingList = $this->CI->Settings_model->getSettingList($betday);
        $result = array();
        $result = $settingList;
        return $result;
    }

    public function getSheetData($betday)
    {
        $this->CI->Settings_model->getActiveSetting($betday);
    }

    public function getActiveSetting($betday, $settingId = -1)
    {
        $activeSetting = $this->getRobbinSetting($betday, $settingId);
        return $activeSetting;
    }

    private function getHypoBetAmount()
    {
        $betday = isset($_SESSION['betday']) ? $_SESSION['betday'] : 0;
        $activeSetting = $this->CI->Settings_model->getAppliedSetting($betday);
        $this->hypoBetAmount = $activeSetting['bet_amount'];
    }

    public function getBetSetting($betday, $settingId = -1){
        
        $activeSetting = $this->getRobbinSetting($betday, $settingId);
        $_SESSION['settingType'] = isset($activeSetting['type'])? $activeSetting['type'] : 0;
        $_SESSION['settingGroupuserId'] = isset($activeSetting['groupuser_id'])? $activeSetting['groupuser_id'] : 0;

        $rows = $this->db->select('*')->from($this->tableName)
                ->where(array(
                    'betday' => $betday,
                    'type'  => $activeSetting['type'],
                    'groupuser_id'  => $activeSetting['groupuser_id']
                ))
                ->get()->result_array();

        $row = isset($rows[0]) ? $rows[0] : [];
        $settingData = json_decode(@$row['sheet_data']);
        
        $ret = array(
            'sheet_data'    => array(),
            'date_info'     => array(),
            'active_setting'=> $activeSetting
        );
        for($i = 0;$i < 7; $i++){
            $new_item = array();
            for ($j = 0; $j <= 6; $j++){
                $value = @$settingData[$i][$j];
                $value = $value == null ? '': $value;
                array_push($new_item, $value);
            }
            array_push($ret['sheet_data'], $new_item);
        }
        
        array_push($ret['sheet_data'], array("Round Robin Structure"));
        array_push($ret['sheet_data'], array(
            @$activeSetting['rr_number1'],
            @$activeSetting['rr_number2'],
            @$activeSetting['rr_number3'],
            @$activeSetting['rr_number4']
        ));

        array_push($ret['date_info'], date_format(date_create(@$row['date']),"m/d/y"));

        return $ret;
    }

    public function getParlay($betday)
    {
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $rows = $this->db->select('*')->from($this->tableName)
            ->where(array(
                'betday' => $betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ))
            ->get()->result_array();

        $result = array();

        if(count($rows))
        {
            $pick_data = $this->CI->Picks_model->getAll($betday);
            $activeSetting = $this->getRobbinSetting($betday);

            $row = $rows[0];
            $settingData = json_decode($row['sheet_data']);
            $robin_1 = @$activeSetting['rr_number1'];
            $robin_2 = @$activeSetting['rr_number2'];
            $robin_3 = @$activeSetting['rr_number3'];
            $parlayIds = empty($row['parlay_select'])? array() : json_decode($row['parlay_select']);

            foreach ($parlayIds as $selected_parlay) {
                $tmpArr = explode('_', $selected_parlay);
                $i = $tmpArr[0];
                $j = $tmpArr[1];

                $itemArr = [];
                $candy_item = $this->getTeamFromPick($pick_data, $i, 'candy');
                if(is_null($candy_item['team']))
                    continue;
                $candy_key = $this->getTeamKey($pick_data, $i, 'candy');

                $disableList = array();
                for($k=0; $k<$robin_1-1; $k++){
                    $team_row_id = $settingData[$k][$j];
                    $team_info = $this->getTeamFromPick($pick_data, $team_row_id-1);
                    $team_key = $this->getTeamKey($pick_data, $team_row_id-1);

                    array_push($itemArr,$team_info);    
                    if($candy_item['team'] != null && $team_info['team'] != null && ($candy_item['team'] == $team_info['team'] || $candy_key == $team_key))
                        $disableList[] = $k;
                }  
                array_push($itemArr,$candy_item);
                $result[] = $itemArr;
            }
        }
        return $result;
    }


    public function getRRCombination($betday){
        $result = [];
        $bets = $this->getParlay($betday);
        if(count($bets))
        {
            $result = $bets[0];

        }
        return $result;
    }

    public function getParlayCount($betday, $type = null, $groupuser_id = null){
        if(is_null($type))
            $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        if(is_null($groupuser_id))  
            $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $this->db->select('*')->from($this->tableName);
        $this->db->where(array(
            'betday' => $betday,
            'type' => $type,
            'groupuser_id' => $groupuser_id
        ));
        $rows = $this->db->get()->result_array();
        
        $result = 0;

        if(count($rows))
        {
            $row = $rows[0];
            $parlayIDs = json_decode($row['parlay_select']);
            $result = count($parlayIDs);
        }
        return $result;
    }

    public function getDisableCount($betday, $type = null , $groupuser_id = null){
        if(is_null($type))
            $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        if(is_null($groupuser_id))
            $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $pick_data = $this->CI->Picks_model->getAll($betday);

        $activeSetting = $this->getRobbinSetting($betday);

        $this->db->select('*')->from($this->tableName);
        $this->db->where(array(
            'betday' => $betday,
            'type' => $type,
            'groupuser_id' => $groupuser_id
        ));
        $rows = $this->db->get()->result_array();
        
        $result = 0;

        if(count($rows))
        {
            $row = $rows[0];
            $settingData = json_decode($row['sheet_data']);
            $robin_1 = @$activeSetting['rr_number1'];
            $robin_2 = @$activeSetting['rr_number2'];
            $robin_3 = @$activeSetting['rr_number3'];
            if(!empty($settingData))
            {
                $validColumnArr = array();
                for($i =0; $i < $robin_2; $i ++)
                {
                    $arrayItem = $settingData[$i];
                    foreach ($arrayItem as $key1 => $value) {
                        if(!is_null($value) && $value != '')
                            $validColumnArr[$key1] = true;
                    }
                }

                for($i=0; $i<60; $i++)
                {
                    $candy_item = $this->getTeamFromPick($pick_data, $i, 'candy');
                    $candy_key = $this->getTeamKey($pick_data, $i, 'candy');
                    for($j=0; $j<count($validColumnArr); $j++){
                        for($k=0; $k<$robin_1-1; $k++){
                            $team_row_id = $settingData[$k][$j];
                            $team_info = $this->getTeamFromPick($pick_data, $team_row_id-1);
                            $team_key = $this->getTeamKey($pick_data, $team_row_id-1);
                            if($candy_item['team'] != null && $team_info['team'] != null && ($candy_item['team'] == $team_info['team'] || $candy_key == $team_key))
                            {
                                $result++;
                                break;
                            }
                        }    
                    }
                }
            }
        }
        return $result;
    }

    public function getValidRRColumnCount($betday, $type = null, $groupuser_id = null)
    {
        if(is_null($type))
            $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        if(is_null($groupuser_id))
            $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $activeSetting = $this->getRobbinSetting($betday);

        $this->db->select('*')->from($this->tableName);
        $this->db->where(array(
            'betday' => $betday,
            'type' => $type,
            'groupuser_id' => $groupuser_id
        ));
        $rows = $this->db->get()->result_array();

        $result = 0;
        if(count($rows))
        {
            $row = $rows[0];
            $settingData = json_decode($row['sheet_data']);
            $robin_1 = @$activeSetting['rr_number1'];
            $robin_2 = @$activeSetting['rr_number2'];
            $robin_3 = @$activeSetting['rr_number3'];
            $parlayIds = empty($row['parlay_select'])? array() : json_decode($row['parlay_select']);
            if(!empty($settingData))
            {
                $validColumnArr = array();
                for($i =0; $i < $robin_2; $i ++)
                {
                    $arrayItem = $settingData[$i];
                    foreach ($arrayItem as $key1 => $value) {
                        if(!is_null($value) && $value != '')
                            $validColumnArr[$key1] = true;
                    }
                }
                $result = count($validColumnArr);
            }
        }
        return $result;
    }

    public function getBetSheet($betday)
    {

        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $pick_data = $this->CI->Picks_model->getAll($betday);
        $activeSetting = $this->getRobbinSetting($betday);

        $rows = $this->db->select('*')->from($this->tableName)
            ->where(array(
                'betday' => $betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ))
            ->get()->result_array();


        $result = array(
            'type'   => null,
            'betday' => $betday,
            'date'   => null,
            'data'   => array()
        );
        $ret = array();
        if(count($rows))
        {
            $row = $rows[0];
            $settingData = json_decode($row['sheet_data']);
            $robin_1 = @$activeSetting['rr_number1'];
            $robin_2 = @$activeSetting['rr_number2'];
            $robin_3 = @$activeSetting['rr_number3'];
            $robin_4 = @$activeSetting['rr_number4'];
            $parlayIds = empty($row['parlay_select'])? array() : json_decode($row['parlay_select']);

            $validColumnArr = array();
            for($i =0; $i < $robin_2; $i ++)
            {
                $arrayItem = $settingData[$i];
                foreach ($arrayItem as $key1 => $value) {
                    if(!is_null($value) && $value != '')
                        $validColumnArr[$key1] = true;
                }
            }

            for($i=0; $i<60; $i++)
            {
                $ret[$i] = array();
                $candy_item = $this->getTeamFromPick($pick_data, $i, 'candy');
                if(is_null($candy_item['team']))
                    continue;
                $candy_key = $this->getTeamKey($pick_data, $i, 'candy');
                for($j=0; $j<count($validColumnArr); $j++){
                    $ret[$i][$j] = array();
                    $disableList = array();
                    for($k=0; $k<$robin_1-1; $k++){
                        $team_row_id = $settingData[$k][$j];
                        $team_info = $this->getTeamFromPick($pick_data, $team_row_id-1);
                        $team_key = $this->getTeamKey($pick_data, $team_row_id-1);

                        array_push($ret[$i][$j],$team_info);    
                        if($candy_item['team'] != null && $team_info['team'] != null && ($candy_item['team'] == $team_info['team'] || $candy_key == $team_key))
                            $disableList[] = $k;
                    }  
                    array_push($ret[$i][$j],$candy_item);
                    $ret[$i][$j]['is_parlay'] = in_array($i."_".$j, $parlayIds) ? 1 : 0;
                    $ret[$i][$j]['title'] = chr(65+$j).($i+1);
                    $ret[$i][$j]['disabled'] = $disableList;
                }
            }

            $rrType = $robin_1 != '' && $robin_1 != '0' ? $robin_1: '';
            $rrType .= $robin_2 != '' && $robin_2 != '0'  ? '-'.$robin_2: '';
            $rrType .= $robin_3 != '' && $robin_3 != '0' ? '-'.$robin_3: '';
            $rrType .= $robin_4 != '' && $robin_4 != '0' ? '-'.$robin_4: '';

            $result['type'] = $rrType;
            $result['date'] = $row['date'];
            $result['data'] = $ret;
        }
        return $result;
    }

    private function formatCustomRR($rrArr, $pickData){  
        $ret = [];
        $data = [];
        $id = $rrArr['id'];
        $betArr = json_decode($rrArr['rr_bets']);
        if(!count($betArr)){
            return false;
        }else{
            foreach ($pickData as $key => $item) {
                if(in_array($item['title'], $betArr)){
                    $item['rush'] = $this->getRushValue($item);
                    $data[] = $item;
                }
            } 
        }
        $ret['data'] = $data;
        $ret['title'] = 'crr_'.$id;
        $ret['bet_title'] = 'Custom RR';
        $ret['game_type'] = $data[0]['game_type'];
        $ret['bet_type'] = 'crr';
        $ret['amount'] = $this->hypoBetAmount;
        $ret['is_group'] = 1;
        $ret['m_number'] = $this->CI->Settings_model->roundRobbinBetCounts($rrArr['rr_number1'], $rrArr['rr_number2'], null, null);
        $ret['total_amount'] = $this->getTotalAmount( $ret, $rrArr['rr_number1'], $rrArr['rr_number2'] );
        $ret['rrArr'] = array(
            'rr1' => $rrArr['rr_number1'],
            'rr2' => $rrArr['rr_number2'],
            'rr3' => null,
            'rr4' => null,
        );
        return $ret;
    }

    private function formatCustomParlay($rrArr, $pickData){
        $ret = [];
        $data = [];
        $id = $rrArr['id'];
        $betArr = json_decode($rrArr['parlay_bets']);
        if(!count($betArr)){
            return false;
        }else{
            foreach ($pickData as $key => $item) {
                if(in_array($item['title'], $betArr)){
                    $item['rush'] = $this->getRushValue($item);
                    $data[] = $item;
                }
            } 
        }
        $ret['data'] = $data;
        $ret['title'] = 'cparlay_'.$id;
        $ret['bet_title'] = 'Custom Parlay';
        $ret['game_type'] = $data[0]['game_type'];
        $ret['bet_type'] = 'cparlay';
        $ret['amount'] = $this->hypoBetAmount;
        $ret['is_group'] = 1;
        $ret['m_number'] = $rrArr['parlay_number'];
        $ret['total_amount'] = $this->getTotalAmount( $ret, $rrArr['parlay_number'] );
        $ret['rrArr'] = array(
            'rr1' => $rrArr['parlay_number'],
            'rr2' => null,
            'rr3' => null,
            'rr4' => null,
        );
        return $ret;
    }

    private function formatRR($settingData, $pick_data, $i, $j, $rrArr){
        $data = [];
        $rr_number = $rrArr['rr1'];
        $candy_item = $this->getTeamFromPick($pick_data, $i, 'candy');
        if(is_null($candy_item['team']))
            return false;
        $candy_key = $this->getTeamKey($pick_data, $i, 'candy');
        $tmpArr = array();
        $disableList = array();
        for($k=0; $k<$rr_number-1; $k++){
            $team_row_id = $settingData[$k][$j];
            $team_info = $this->getTeamFromPick($pick_data, $team_row_id-1);
            if(is_null($team_info['team']))
                return false;
            $team_key = $this->getTeamKey($pick_data, $team_row_id-1);

            $team_info['rush'] = $this->getRushValue($team_info);
            array_push($data,$team_info);
            if($candy_item['team'] != null && $team_info['team'] != null && ($candy_item['team'] == $team_info['team'] || $candy_key == $team_key))
                $disableList[] = $k;
        }
        if(count($disableList))
            return false;
        $candy_item['rush'] = $this->getRushValue($candy_item);
        array_push($data,$candy_item);
        $tmpArr['data'] = $data;
        $tmpArr['title'] = chr(65+$j).($i+1);
        $tmpArr['game_type'] = $candy_item['game_type'];
        $tmpArr['bet_type'] = 'rr';
        $tmpArr['bet_title'] = 'Round Robin';
        $tmpArr['is_group'] = 1;
        $tmpArr['amount'] = $this->hypoBetAmount;
        $tmpArr['m_number'] = $this->CI->Settings_model->roundRobbinBetCounts( $rrArr['rr1'], $rrArr['rr2'], $rrArr['rr3'], $rrArr['rr4'] );
        $tmpArr['total_amount'] = $this->getTotalAmount( $tmpArr, $rrArr['rr1'], $rrArr['rr2'], $rrArr['rr3'], $rrArr['rr4'] );
        $tmpArr['rrArr'] = $rrArr;
        return $tmpArr;
    }

    public function getRROrders($betday, $inverstor_id = null)
    {
        $this->getHypoBetAmount();

        $investor_setting = $this->CI->Settings_model->getActiveSettingByInvestor($betday, $inverstor_id);
        if($investor_setting){
            $type = $investor_setting['settingType'];
            $groupuser_id = $investor_setting['settingGroupuserId'];
        }else{
            $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
            $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;
        }
        
        
        $pick_data = $this->CI->Picks_model->getAll($betday, $type, $groupuser_id);
        $activeSetting = $investor_setting['data'];

        $rows = $this->db->select('*')->from($this->tableName)
            ->where(array(
                'betday' => $betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ))
            ->get()->result_array();

        $custom_rows = $this->CI->CustomBet_model->getData($betday, $type, $groupuser_id);

        $result = array(
            'betday' => $betday,
            'data'   => array()
        );

        $roundrobins = array();

        $ret = array(
            'rr' => array(),
            'crr' => array(),
            'parlay' => array(),
            'cparlay' => array(),
            'single' => array()
        );

        $singleBets = $this->CI->Picks_model->getIndividual($betday, 'pick', $type, $groupuser_id);
        $singleBets = array_filter($singleBets, function($arrItem){
            return $arrItem['selected'];
        });
        foreach ($singleBets as &$item) {
            $item['rush'] = $this->getRushValue($item);
            $item['bet_type'] = 'single';
            $item['is_group'] = 0;
            $item['bet_title'] = 'Single Bet';

            $item['amount'] = $this->hypoBetAmount;
            $item['m_number'] = 1;
            $item['total_amount'] = $this->getTotalAmount( $item );
        }
        $ret['single']  = $singleBets;
        
        if(count($custom_rows)){
            foreach ($custom_rows as $row_item) {
                $picklist_data = $this->CI->Picks_model->getAllList($betday, $type, $groupuser_id);
                $crr_item  = $this->formatCustomRR($row_item, $picklist_data);
                if($crr_item)
                    $ret['crr'][] = $crr_item;

                $cparlay_item  = $this->formatCustomParlay($row_item, $picklist_data);
                if($cparlay_item)
                    $ret['cparlay'][] = $cparlay_item;
            }
        }
        
        if(count($rows))
        {
            $row = $rows[0];
            $settingData = json_decode($row['sheet_data']);
            $robin_1 = @$activeSetting['rr_number1'];
            $robin_2 = @$activeSetting['rr_number2'];
            $robin_3 = @$activeSetting['rr_number3'];
            $robin_4 = @$activeSetting['rr_number4'];
            $robinArr = array(
                'rr1' => $robin_1,
                'rr2' => $robin_2,
                'rr3' => $robin_3,
                'rr4' => $robin_4,
            );
            $parlayIds = empty($row['parlay_select'])? array() : json_decode($row['parlay_select']);

            $validColumnArr = array();
            foreach ($settingData as $key => $arrayItem) {
                foreach ($arrayItem as $key1 => $value) {
                    if(!is_null($value) && $value != '')
                        $validColumnArr[$key1] = true;
                }
            }

            for($j=0; $j<count($validColumnArr); $j++){
                for($i=0; $i<60; $i++){

                    $tmpArr = $this->formatRR($settingData, $pick_data, $i, $j, $robinArr);
                    if(!$tmpArr) 
                        continue;
                    $ret['rr'][] = $tmpArr;

                    $is_parlay = in_array($i."_".$j, $parlayIds) ? 1 : 0;
                    if($is_parlay)
                    {
                        $tmpArr['bet_type'] = 'parlay';
                        $tmpArr['bet_title'] = 'Parlay';
                        $ret['parlay'][] = $tmpArr;
                    }
                }
            }

            $maxIndex = 0;
            foreach ($roundrobins as $subArr) {
                $maxIndex = $maxIndex > count($subArr) ? $maxIndex : count($subArr);
            }

            for($i = 0; $i < $maxIndex; $i ++)
            {
                for($j = 0; $j < count($roundrobins); $j ++)
                {
                    if(!isset($roundrobins[$j][$i]))
                        continue;
                    $ret['rr'][] = $roundrobins[$j][$i];
                }
            }

            $result['rr1'] = $robin_1;
            $result['rr2'] = $robin_2;
            $result['rr3'] = $robin_3;
            $result['rr4'] = $robin_4;
            $result['data'] = $ret;
        }
        return $result;
    }

    private function getRushValue($game){
        date_default_timezone_set('America/Los_Angeles');
        $west_time = date('Y-m-d h:i:s', strtotime('-30 minutes'));
        $gameTime = date('Y-m-d H:i:s', strtotime("{$game['date']} {$game['time']}"));
        if($gameTime > $west_time){
            return true;
        }else {
            return false;
        }
    }

    private function getTotalAmount($bet, $n=null, $r1=null, $r2=null, $r3=null){
        $bet_type = $bet['bet_type'];
        $total_amount = $bet['amount'];
        if($bet_type == 'rr' || $bet_type == 'crr'){
            $total_amount = $bet['amount'] * $this->CI->Settings_model->roundRobbinBetCounts($n, $r1, $r2, $r3);
        }else if($bet_type == 'parlay' || $bet_type == 'cparlay'){
            $total_amount = $bet['amount'] * $n;
        }
        return $total_amount;
    }

    public function getTeamKey($pickData, $id, $type='wrapper'){
        $result = null;
        if(isset($pickData[$id])){
            $result = $pickData[$id][$type.'_key'];
        }
        return $result;
    }

    public function getTeamFromPick($pickData, $id, $type='wrapper'){
        $result = null;
        if(isset($pickData[$id])){
            $result = array(
                'vrn' => $pickData[$id][$type.'_vrn'],
                'type' => $pickData[$id][$type.'_type'],
                'team' => $pickData[$id][$type.'_team'],
                'line' => $pickData[$id][$type.'_line'],
                'time' => $pickData[$id][$type.'_time'],
                'date' => $pickData[$id][$type.'_date'],
                'game_type' => $pickData[$id][$type.'_game_type'],
            );
        }
        return $result;
    }
    public function saveData($betday, $setting)
    {   
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $settingList = json_decode($setting);
        $data = array();
        
        $sheet_data = array();
        foreach($settingList->data as $key => $item)
        {
            array_push($sheet_data, $item);
        }

        $data['date'] = $value = date_format(date_create(@$settingList->betday),"Y-m-d");
        $data['sheet_data'] = json_encode($sheet_data);

        $this->db->select('*')->from($this->tableName);
        $this->db->where(array(
            'betday' =>$betday,
            'type'  => $type,
            'groupuser_id'  => $groupuser_id
        ));
        $rows = $this->db->get()->result_array();
        if(count($rows))
        {
            $this->db->where(array(
                'betday'    =>$betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ));
            $this->db->update('work_sheet', $data);
        }
        else{
            $this->db->insert('work_sheet', array_merge(array(
                'betday'=>$betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id

            ),$data));
        }

        $this->saveSetting($betday);
    }

    private function saveSetting($betday)
    {
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $candy_data = $this->CI->Picks_model->getIndividual($betday, 'candy', $type, $groupuser_id);
        $pick_data = $this->CI->Picks_model->getIndividual($betday, 'pick', $type, $groupuser_id);

        $parlayCnt = $this->getParlayCount($betday);

        $individualCnt = 0;
        foreach($pick_data as $key => $item)
        {
            if($item['selected'])
            {
                $individualCnt++;
            }
        }

        $newData = array(
            'parlay_number1'    => $parlayCnt,
            'pick_number1'      => $individualCnt
        );

        $this->db->select('*')->from('settings');
        $this->db->where(array(
            'betday' =>$betday,
            'type'  => $type,
            'groupuser_id'  => $groupuser_id
        ));

        $rows = $this->db->get()->result_array();
        if(count($rows))
        {
            $this->db->where(array(
                'betday'    =>$betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ));
            $this->db->update('settings', $newData);
        }
        else{
            $this->db->insert('settings', array_merge(array(
                'betday'=>$betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ),$newData));
        }

    }

    public function savePickSelect($betday, $data)
    {
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $data = json_decode($data);
        $newData = array(
            'pick_select' => json_encode($data->data)
        );

        $this->db->select('*')->from($this->tableName);
        $this->db->where(array(
            'betday' =>$betday,
            'type'  => $type,
            'groupuser_id'  => $groupuser_id
        ));
        $rows = $this->db->get()->result_array();
        
        if(count($rows))
        {
            $this->db->where(array(
                'betday'    =>$betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ));
            $this->db->update('work_sheet', $newData);
        }
        else{
            $this->db->insert('work_sheet', array_merge(array(
                'betday'=>$betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ),$newData));
        }

        $this->saveSetting($betday);
    }

    public function updateParlay($betday, $data){
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $this->db->where(array(
            'betday'    =>$betday,
            'type'  => $type,
            'groupuser_id'  => $groupuser_id
        ));
        $newData = array(
            'parlay_select' => $data
        );
        $this->db->update('work_sheet', $newData);
        return true;
    }
    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
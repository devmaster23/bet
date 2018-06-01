<?php
class WorkSheet_model extends CI_Model {
    private $tableName = 'work_sheet';
    private $CI = null;

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Settings_model');
        $this->CI->load->model('Picks_model');
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
            'date_info'     => array()
        );
        for($i = 0;$i < 7; $i++){
            $new_item = array();
            for ($j = 0; $j <= 6; $j++){
                array_push($new_item, @$settingData[$i][$j]);
            }
            array_push($ret['sheet_data'], $new_item);
        }
        
        array_push($ret['sheet_data'], array("Round Robbin Structure"));
        array_push($ret['sheet_data'], array(
            @$activeSetting['rr_number1'],
            @$activeSetting['rr_number2'],
            @$activeSetting['rr_number3'],
            @$activeSetting['rr_number4']
        ));
        array_push($ret['date_info'], array(
            date_format(date_create(@$row['date']),"M d, Y"),
            date_format(date_create(@$row['date']),"Y"),
            @$row['betday']
        ));

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

    public function getParlayCount($betday){

        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
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

    public function getDisableCount($betday){
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
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
        return $result;
    }

    public function getValidRRColumnCount($betday)
    {
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
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
            $result['type'] = $robin_1.'-'.$robin_2.'-'.$robin_3;
            $result['date'] = $row['date'];
            $result['data'] = $ret;
        }
        return $result;
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
                'time' => $pickData[$id][$type.'_time']
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
            if($key < 7)
            {
                array_push($sheet_data, $item);
                continue;
            }else if($key == 8){
                $data['robin_1'] = $item[0];
                $data['robin_2'] = $item[1];
                $data['robin_3'] = $item[2];
            }
            
        }
        $data['date'] = $value = date_format(date_create(@$settingList->data1[0][0]),"Y-m-d");
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

        $candy_data = $this->CI->Picks_model->getIndividual($betday, 'candy');
        $pick_data = $this->CI->Picks_model->getIndividual($betday, 'pick');

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
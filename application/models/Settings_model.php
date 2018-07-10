<?php
class Settings_model extends CI_Model {
    private $tableName = 'settings';
    private $headerName = array(
        'Bet Allocation %',
        'Round Robbin Structure',
        'Parlays',
        'Individual Bets(Picks)',
    );

    private $defaultSetting;
    private $defaultResult;
    private $fomularData;

    private $numberOfTeams;
    private $numberOfPicks;

    private $CI;

    private $dbColums = array(
        'rr_allocation',
        'rr_number1',
        'rr_number2',
        'rr_number3',
        'rr_number4',
        'parlay_allocation',
        'parlay_number1',
        'pick_allocation',
        'pick_number1',
        'description'
    );


    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Groups_model');
        $this->CI->load->model('Investor_model');
        $this->CI->load->model('Picks_model');
        $this->CI->load->model('WorkSheet_model');
        $this->CI->load->model('CustomBet_model');
        $this->CI->load->model('CustomBetAllocation_model');

        $this->numberOfTeams = array(
            'min' => 3,
            'max' => 8
        );

        $this->numberOfPicks = array(
            'min' => 2,
            'max' => 8
        );

        $this->defaultSetting = array(
            array(
                'title' => $this->headerName[0],
                'bet_percent'    => 0
            ),
            array(
                'title' => $this->headerName[1],
                'bet_percent'    => 0,
                'bet_number1'    => null,
                'bet_text'       => 'by',
                'bet_number2'    => null,
                'bet_number3'    => null,
                'bet_number4'    => null,
            ),
            array(
                'title' => $this->headerName[2],
                'bet_percent'    => 0,
                'bet_number1'    => null,
            ),
            array(
                'title' => $this->headerName[3],
                'bet_percent'    => 0,
                'bet_number1'    => null,
            )
        );

        $this->defaultResult = array(
            array(
                'title'     => '',
                'rr1'       => 'Structure',
                'rr2'       => '',
                'sheet'     => 'Sheets',
                'order'     => 'Orders',
                'bets'      => '# Bets'
            )
        );

        $fomularData = array();

        for($i = $this->numberOfTeams['min']; $i <= $this->numberOfTeams['max']; $i++)
        {
            for($j = $this->numberOfPicks['min']; $j <= $this->numberOfTeams['max']; $j++)
            {
                if($i >= $j)
                    $fomularData[$i][$j] = $this->combinationValue($i,$j);
                else
                    $fomularData[$i][$j] = '';
            }
        }
        $this->fomularData = $fomularData;
    }
    public function getNumberOfPicks(){
        return $this->numberOfPicks;
    }

    public function getNumberOfTeams(){
        return $this->numberOfTeams;
    }

    public function getFomular(){
        return $this->fomularData;
    }

    private function factorialize($num) {
        if ($num < 0) 
            return -1;
        else if ($num == 0) 
            return 1;
        else {
            return ($num * $this->factorialize($num - 1));
        }
    }

    private function combinationValue($n, $r){
        return $this->factorialize($n) / ( $this->factorialize($r) * $this->factorialize($n-$r));
    }

    public function roundRobbinBetCounts($n, $r1, $r2, $r3){
        $count = 0;
        if($r1)
            $count += $this->factorialize($n) / ( $this->factorialize($r1) * $this->factorialize($n-$r1));
        if($r2)
            $count += $this->factorialize($n) / ( $this->factorialize($r2) * $this->factorialize($n-$r2));
        if($r3)
            $count += $this->factorialize($n) / ( $this->factorialize($r3) * $this->factorialize($n-$r3));
        return $count;
    }

    public function getGroupUserList($categoryType){
        $result = array();
        switch ($categoryType) {
            case 1:
                $result = $this->CI->Groups_model->getAll();
                break;
            case 2:
                $result = $this->CI->Investor_model->getAll();
                break;
            case 0:
            default:
                # code...
                break;
        }
        return $result;
    }

    private function isEmptySetting($data)
    {
        $result = true;
        if(!is_null($data))
        {
            foreach ($this->dbColums as $column) {
                if($data[$column])
                {
                    $result = false;
                    break;
                }
            }
        }
        return $result;
    }

    private function getGroupSetting($group_id)
    {
        $result = null;
        $query = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betday,
                'type'  => 1,
                'groupuser_id' => $group_id
            ));
        $rows = $query->get()->result_array();
        if(count($rows))
            $result = $rows[0];
        return $result;
    }

    private function getAllSetting()
    {
        $result = null;
        $query = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betday,
                'type'  => 0,
            ));
        $rows = $query->get()->result_array();
        if(count($rows))
            $result = $rows[0];
        return $result;   
    }

    private function getSettingTitle($categoryType = 0, $groupuser_id = null)
    {
        $result = '';
        switch ($categoryType) {
            case 1:
                $group_item = $this->CI->Groups_model->getByID($groupuser_id);
                $result = $group_item['name'];
                break;
            case 2:
                $user_item = $this->CI->Investor_model->getByID($groupuser_id);
                $group_id = $user_item['group_id'];
                $group_item = $this->CI->Groups_model->getByID($group_id);

                $result = '( '. $group_item['name'] . ' ) '  . $user_item['name'];
                break;
            case 0:
            default:
                $result = 'All';
                # code...
                break;
        }
        return $result;
    }

    public function getAppliedSetting($betday)
    {
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $rows = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ))
            ->get()->result_array();

        $result = array(
            'type'          => 0,
            'groupuser_id'  => null,
            'rr_number1'    => 0,
            'rr_number2'    => 0,
            'rr_number3'    => 0,
            'rr_number4'    => 0,
            'parlay_allocation'    => 0,
            'parlay_number1'    => 0,
            'pick_allocation'    => 0,
            'pick_number1'    => 0,
            'description'   => '',
            'title'         => 'Default'
        );

        if(count($rows))
        {

            $setting = $rows[0];
            $type = $setting['type'];
            $groupuser_id= $setting['groupuser_id'];
            if($this->isEmptySetting($setting))
            {
                switch ($type) {
                    case '2':
                        $setting = $this->getGroupSetting($groupuser_id);
                        if($this->isEmptySetting($setting))
                            $setting = $this->getAllSetting();
                        break;
                    case '1':
                        $setting = $this->getAllSetting();
                        break;
                    case '0':
                    default:
                        break;
                }
            }

            $result['type'] = $setting['type'];
            $result['groupuser_id'] = $setting['groupuser_id'];
            $result['rr_number1'] = $setting['rr_number1'];
            $result['rr_number2'] = $setting['rr_number2'];
            $result['rr_number3'] = $setting['rr_number3'];
            $result['rr_number4'] = $setting['rr_number4'];
            $result['parlay_allocation'] = $setting['parlay_allocation'];
            $result['parlay_number1'] = $setting['parlay_number1'];
            $result['pick_allocation'] = $setting['pick_allocation'];
            $result['pick_number1'] = $setting['pick_number1'];
            $result['description'] = $setting['description'];
            $result['title'] = $this->getSettingTitle($setting['type'],$setting['groupuser_id']);
        }
        return $result;
    }

    public function getActiveSetting($betday, $settingId = -1){
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $query = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betday
            ));
        if($settingId != -1)
        {
            $query = $query->where('id', $settingId);
        }else{
            $query = $query->where(array(
                'type'  => $type,
                'groupuser_id'  => $groupuser_id
            ));
        }
        $rows = $query->get()->result_array();

        $result = array(
            'type'          => 0,
            'groupuser_id'  => null,
            'rr_number1'    => 0,
            'rr_number2'    => 0,
            'rr_number3'    => 0,
            'rr_number4'    => 0,
            'rr_number4'    => 0,
            'parlay_allocation'    => 0,
            'parlay_number1'    => 0,
            'pick_allocation'    => 0,
            'pick_number1'    => 0,
            'description'   => '',
            'title'         => ''
        );

        if(count($rows))
        {
            $data = $rows[0];
            $result['type'] = $data['type'];
            $result['groupuser_id'] = $data['groupuser_id'];
            $result['rr_number1'] = $data['rr_number1'];
            $result['rr_number2'] = $data['rr_number2'];
            $result['rr_number3'] = $data['rr_number3'];
            $result['rr_number4'] = $data['rr_number4'];
            $result['parlay_allocation'] = $data['parlay_allocation'];
            $result['parlay_number1'] = $data['parlay_number1'];
            $result['pick_allocation'] = $data['pick_allocation'];
            $result['pick_number1'] = $data['pick_number1'];
            $result['description'] = $data['description'];
            $result['title'] = $this->getSettingTitle($data['type'],$data['groupuser_id']);
        }
        return $result;
    }

    public function getSettingList($betday){
        $query = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betday
            ))->order_by('type','asc')
            ->order_by('groupuser_id','asc');


        $rows = $query->get()->result_array();
        foreach($rows as $key => &$item)
        {
            switch ($item['type']) {
                case '1':
                    $group = $this->CI->Groups_model->getByID($item['groupuser_id']);
                    $item['title'] = $group['name'];
                    break;
                case '2':
                    $user = $this->CI->Investor_model->getByID($item['groupuser_id']);
                    $item['title'] = $user['name'];
                    break;
                case '0':
                default:
                    $item['title'] = 'All';
                    break;
            }
            foreach($item as $column_name => &$value)
            {
                $value = (is_null($value) || empty($value)) ? '' : $value;
            }
        }
        return $rows;
    }

    public function getSettings($betday, $categoryType, $categoryGroupUser){

        $fomularData = $this->fomularData;
        $settings = $this->defaultSetting;
        $description = '';
        $bet_analysis = $this->defaultResult;

        $candy_data = $this->CI->Picks_model->getIndividual($betday, 'candy',$categoryType, $categoryGroupUser);
        $pick_data = $this->CI->Picks_model->getIndividual($betday, 'pick',$categoryType, $categoryGroupUser);

        $rr_disableCnt = $this->CI->WorkSheet_model->getDisableCount($betday);
        $rr_validColumnCnt = $this->CI->WorkSheet_model->getValidRRColumnCount($betday);

        $parlayCnt = $this->CI->WorkSheet_model->getParlayCount($betday);

        $custom_bets = $this->CI->CustomBet_model->getData($betday);

        $custom_bet_allocations = $this->CI->CustomBetAllocation_model->getByBetday($betday,$categoryType,$categoryGroupUser);

        $individualCnt = 0;
        foreach($pick_data as $key => $item)
        {
            if($item['selected'])
            {
                $individualCnt++;
            }
        }

        $query = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betday,
                'type'  => $categoryType
            ));
        if($categoryType != 0)
            $query->where('groupuser_id', $categoryGroupUser);

        $rows = $query->get()->result_array();

        if(count($rows))
        {
            $data = $rows[0];
            
            $settings[0]['bet_percent'] = floatval($data['bet_allocation']);

            $settings[1]['bet_percent'] = floatval($data['rr_allocation']);
            $settings[1]['bet_number1'] = $data['rr_number1'];
            $settings[1]['bet_number2'] = $data['rr_number2'];
            $settings[1]['bet_number3'] = $data['rr_number3'];
            $settings[1]['bet_number4'] = $data['rr_number4'];

            $settings[2] = array(
                'title' => $this->headerName[2],
                'bet_percent' => floatval($data['parlay_allocation']),
                'bet_number1' => $parlayCnt
            );
            
            $settings[3] = array(
                'title' => $this->headerName[3],
                'bet_percent' => floatval($data['pick_allocation']),
                'bet_number1' => $individualCnt
            );

            $bet_analysis_index = 1;
            $bet_analysis[$bet_analysis_index]['title'] = 'Round Robbin 1';
            $bet_analysis[$bet_analysis_index]['rr1'] = $data['rr_number1'];
            $bet_analysis[$bet_analysis_index]['rr2'] = $data['rr_number2'];
            $bet_analysis[$bet_analysis_index]['sheet'] = $rr_validColumnCnt * count($candy_data) - $rr_disableCnt;;
            $bet_analysis[$bet_analysis_index]['order'] = 1;
            $bet_analysis[$bet_analysis_index]['bets'] = @$fomularData[$data['rr_number1']][$data['rr_number2']];

            if(!is_null($data['rr_number3']) && $data['rr_number3'] != 0)
            {
                $bet_analysis_index ++;
                $bet_analysis[$bet_analysis_index]['title'] = 'Round Robbin 2';
                $bet_analysis[$bet_analysis_index]['rr1'] = $data['rr_number1'];
                $bet_analysis[$bet_analysis_index]['rr2'] = $data['rr_number3'];
                $bet_analysis[$bet_analysis_index]['sheet'] = $rr_validColumnCnt * count($candy_data) - $rr_disableCnt;;
                $bet_analysis[$bet_analysis_index]['order'] = 1;
                $bet_analysis[$bet_analysis_index]['bets'] = @$fomularData[$data['rr_number1']][$data['rr_number3']];                
            }

            if(!is_null($data['rr_number4']) && $data['rr_number4'] != 0)
            {
                $bet_analysis_index ++;
                $bet_analysis[$bet_analysis_index]['title'] = 'Round Robbin 3';
                $bet_analysis[$bet_analysis_index]['rr1'] = $data['rr_number1'];
                $bet_analysis[$bet_analysis_index]['rr2'] = $data['rr_number4'];
                $bet_analysis[$bet_analysis_index]['sheet'] = $rr_validColumnCnt * count($candy_data) - $rr_disableCnt;;
                $bet_analysis[$bet_analysis_index]['order'] = 1;
                $bet_analysis[$bet_analysis_index]['bets'] = @$fomularData[$data['rr_number1']][$data['rr_number4']];                
            }

            $bet_analysis_index ++;
            $bet_analysis[$bet_analysis_index]['title'] = 'Parlay';
            $bet_analysis[$bet_analysis_index]['rr1'] = '';
            $bet_analysis[$bet_analysis_index]['rr2'] = '';
            $bet_analysis[$bet_analysis_index]['sheet'] = $parlayCnt;
            $bet_analysis[$bet_analysis_index]['order'] = 1;
            $bet_analysis[$bet_analysis_index]['bets'] = 1;

            $bet_analysis_index ++;
            $bet_analysis[$bet_analysis_index]['title'] = 'Individual Bets(Picks)';
            $bet_analysis[$bet_analysis_index]['rr1'] = '';
            $bet_analysis[$bet_analysis_index]['rr2'] = '';
            $bet_analysis[$bet_analysis_index]['sheet'] = '';
            $bet_analysis[$bet_analysis_index]['order'] = $individualCnt;
            $bet_analysis[$bet_analysis_index]['bets'] = $individualCnt;

            foreach ($custom_bets as $key => $custom_bet_item) {
                $custom_bet_allocation = 0;

                $rr_allocation = "0";
                $parlay_allocation = "0";
                foreach($custom_bet_allocations as $allocation_item)
                {
                    if($allocation_item['bet_id'] == $custom_bet_item['id'])
                    {
                        $rr_allocation = $allocation_item['rr_allocation'];
                        $parlay_allocation = $allocation_item['parlay_allocation'];;
                    }
                }

                $settings[] = array(
                    'id'          => $custom_bet_item['id'],
                    'type'        => 'rr',
                    'title'     => "Custom RR ".($key+1),
                    'bet_percent' => floatval($rr_allocation),
                    'bet_text'    => 'by',
                    'bet_number1' => $custom_bet_item['rr_number1'],
                    'bet_number2' => $custom_bet_item['rr_number2'],
                    'bet_number3' => "0",
                    'bet_number4' => "0"
                );

                $settings[] = array(
                    'id'          => $custom_bet_item['id'],
                    'type'        => 'parlay',
                    'title' => "Custom Parlay ".($key+1),
                    'bet_percent' => floatval($parlay_allocation),
                    'bet_number1' => $custom_bet_item['parlay_number'],
                    'bet_number2' => "0",
                    'bet_number3' => "0",
                    'bet_number4' => "0"
                );

                $custom_bet_allocation += @$fomularData[$custom_bet_item['rr_number1']][$custom_bet_item['rr_number2']];

                $bet_analysis[] = array(
                    'title'     => "Custom RR ".($key+1),
                    'rr1'       => $custom_bet_item['rr_number1'],
                    'rr2'       => $custom_bet_item['rr_number2'],
                    'sheet'     => '',
                    'order'     => 1,
                    'bets'      => $custom_bet_allocation
                );

                $bet_analysis[] = array(
                    'title'     => "Custom RR ".($key+1),
                    'rr1'       => '',
                    'rr2'       => '',
                    'sheet'     => '',
                    'order'     => 1,
                    'bets'      => $custom_bet_item['parlay_number']
                );
            }   

            $description = $data['description'];
        }

        $result = array(
            'bet_allocation'    => $settings,
            'bet_analysis'      => $bet_analysis,
            'description'       => $description
        );

        return $result;
    }

    public function saveSettings($betday, $categoryType, $categoryGroupUser,$data, $description = ''){
        

        $jsonData = json_decode($data);
        $settingData = $jsonData->data;
        $description = $jsonData->description;

        $_SESSION['settingType'] = $categoryType;
        $_SESSION['settingGroupuserId'] = $categoryGroupUser;

        $query = $this->db->from('settings')
            ->where(array(
                'betday'    => $betday,
                'type'      => $categoryType,
            ));
        if($categoryType != 0)
            $query = $query->where('groupuser_id', $categoryGroupUser);
        $rows = $query->get();

        $custom_bet_allocations = $this->CI->CustomBetAllocation_model->getByBetday($betday,$categoryType, $categoryGroupUser);

        $newData = array(
            'bet_allocation' => @$settingData[0]['1'],
            'rr_allocation' => @$settingData[1]['1'],
            'rr_number1' => @$settingData[1]['2'],
            'rr_number2' => @$settingData[1]['4'],
            'rr_number3' => @$settingData[1]['5'],
            'rr_number4' => @$settingData[1]['6'],
            'parlay_allocation' => @$settingData[2]['1'],
            'parlay_number1' => @$settingData[2]['2'],
            'pick_allocation' => @$settingData[3]['1'],
            'pick_number1'  => @$settingData[3]['2'],
            'description'   => $description,
        );

        if ( $rows->num_rows() > 0 ) 
        {
            $updateQuery = $this->db->where(array(
                'betday'    => $betday,
                'type'      => $categoryType,
            ));
            if($categoryType != 0)
                $updateQuery = $updateQuery->where('groupuser_id', $categoryGroupUser);

            $updateQuery->update('settings', $newData);
        } else {
            $newData['betday'] = $betday;
            $newData['type'] = $categoryType;
            if($categoryType != 0)
                $newData['groupuser_id'] = $categoryGroupUser;
            else
                $newData['groupuser_id'] = 0;
            $this->db->insert('settings', $newData);
        }

        for($i=4; $i< count($settingData); $i = $i+2)
        {
            $bet_id = $settingData[$i][7];
            $custom_bet_rows = array_filter($custom_bet_allocations, function($item) use($bet_id, $categoryType, $categoryGroupUser){
                return ((($categoryType == 0 && $item['type'] == $categoryType) || 
                    ($item['type'] == $categoryType && $item['groupuser_id'] == $categoryGroupUser)) && 
                    ($item['bet_id'] == $bet_id));
            });
            $customData = array(
                'rr_allocation'     => $settingData[$i][1],
                'parlay_allocation' => $settingData[$i+1][1]
            );
            
            if(count($custom_bet_rows))
            {   
                $custom_bet_row = reset($custom_bet_rows);
                $custom_bet_row_id = $custom_bet_row['id'];
                $updateQuery = $this->db->where(array(
                    'id'      => $custom_bet_row_id,
                ));
                $updateQuery->update('custom_bet_allocations', $customData);
            }else{
                $this->db->insert('custom_bet_allocations', array_merge(array(
                    'betday'    => $betday,
                    'type'      => $categoryType,
                    'groupuser_id'  => $categoryGroupUser,
                    'bet_id'        => $bet_id
                ),$customData));
            }
        }
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
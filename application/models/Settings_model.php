<?php
class Settings_model extends CI_Model {
    private $tableName = 'settings';
    private $headerName = array(
        'Bet Allocation %',
        'Round Robin Structure',
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
        'description',
        'bet_amount'
    );

    private $actualType = 0;
    private $actualGroupUser = '';


    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Groups_model');
        $this->CI->load->model('Investor_model');
        $this->CI->load->model('Picks_model');
        $this->CI->load->model('WorkSheet_model');
        $this->CI->load->model('CustomBet_model');
        $this->CI->load->model('CustomBetAllocation_model');
        $this->CI->load->model('SystemSettings_model');

        $this->numberOfTeams = array(
            'min' => 2,
            'max' => 8
        );

        $this->numberOfPicks = array(
            'min' => 2,
            'max' => 8
        );

        $this->defaultSetting = array(
            array(
                'title' => $this->headerName[0],
                'bet_percent'    => 0,
                'recommend_bet_amount'     => 'Bet Amount'
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

    public function getGroupUserList($betday, $categoryType){
        $result = array();
        switch ($categoryType) {
            case 1:
                $result = $this->CI->Groups_model->getAllWithOpenStatus($betday);
                break;
            case 2:
                $result = $this->CI->Investor_model->getAllWithOpenStatus($betday);
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

    private function getInvestorSetting($betweek, $investor_id)
    {
        $rows = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betweek,
                'type'  => 2,
                'groupuser_id'  => $investor_id
            ))
            ->get()->result_array();
        if(count($rows))
            return $rows[0];
        return false;
    }

    private function getGroupSetting($betweek, $group_id)
    {
        $result = null;
        $query = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betweek,
                'type'  => 1,
                'groupuser_id' => $group_id
            ));
        $rows = $query->get()->result_array();
        if(count($rows))
            $result = $rows[0];
        return $result;
    }

    private function getAllSetting($betday)
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

    public function setActiveSetting($betweek, $investor_id)
    {
        $investor = $this->CI->Investor_model->getByID($investor_id);
        $group_id = isset($investor['group_id']) ? $investor['group_id'] : null;
        $investor_setting = $this->getInvestorSetting($betweek, $investor_id);

        if(!$this->isEmptySetting($investor_setting))
        {
            $_SESSION['settingType'] = 2;
            $_SESSION['settingGroupuserId'] = $investor_id;
        }else{
            $group_setting = $this->getGroupSetting($betweek, $group_id);
            if(!$this->isEmptySetting($group_setting))
            {
                $_SESSION['settingType'] = 1;
                $_SESSION['settingGroupuserId'] = $group_id;
            }else{
                $_SESSION['settingType'] = 0;
                $_SESSION['settingGroupuserId'] = 0;
            }
        }
        return true;
    }

    public function setBetAmount($betday, $investor_id, $bet_amount)
    {
        $investor = $this->CI->Investor_model->getByID($investor_id);
        $group_id = isset($investor['group_id']) ? $investor['group_id'] : null;
        $investor_setting = $this->getInvestorSetting($betday, $investor_id);
        if(!$this->isEmptySetting($investor_setting))
        {
            $setting_type = 2;
            $setting_id = $investor_id;
        }else{
            $group_setting = $this->getGroupSetting($betday, $group_id);
            if(!$this->isEmptySetting($group_setting))
            {
                $setting_type = 1;
                $setting_id = $group_id;
            }else{
                $setting_type = 0;
                $setting_id = 0;
            }
        }
        $updateQuery = $this->db->where(array(
            'betday'    => $betday,
            'type'      => $setting_type,
        ));
        if($setting_type != 0)
            $updateQuery = $updateQuery->where('groupuser_id', $setting_id);

        $updateQuery->update('settings', array(
            'bet_amount'    => $bet_amount
        ));

        return true;
    }
    public function getSettingByType($betday, $type, $groupuser_id)
    {
        $result = null;
        $query = $this->db->select('*')
            ->from('settings')
            ->where(array(
                'betday' => $betday,
                'type'  => $type,
                'groupuser_id'  => $groupuser_id,
            ));
        $rows = $query->get()->result_array();
        if(count($rows))
            $result = $this->formatSetting($rows[0]);
        return $result;   
    }

    public function formatSetting($item){
        if(!$item['rr_number1'])
            $item['rr_number1'] = '';
        if(!$item['rr_number2'])
            $item['rr_number2'] = '';
        if(!$item['rr_number3'])
            $item['rr_number3'] = '';
        if(!$item['rr_number4'])
            $item['rr_number4'] = '';
        return $item;
    }

    public function getActiveSettingByInvestor($betweek, $investor_id)
    {
        $result = false;
        $investor = $this->CI->Investor_model->getByID($investor_id);
        $group_id = isset($investor['group_id']) ? $investor['group_id'] : null;
        $investor_setting = $this->getInvestorSetting($betweek, $investor_id);

        if(!$this->isEmptySetting($investor_setting))
        {
            $result = array(
                'settingType' => 2,
                'settingGroupuserId' => $investor_id,
            );
        }else{
            $group_setting = $this->getGroupSetting($betweek, $group_id);
            if(!$this->isEmptySetting($group_setting))
            {
                $result = array(
                    'settingType' => 1,
                    'settingGroupuserId' => $group_id,
                );
            }else{
                $result = array(
                    'settingType' => 0,
                    'settingGroupuserId' => 0,
                );
            }
        }
        $result['data'] = $this->getSettingByType($betweek, $result['settingType'], $result['settingGroupuserId']);
        return $result;
    }

    public function getAppliedSetting($betday)
    {
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        if (!$this->settingExists($betday, $type, $groupuser_id)) {
            if ($type == 1) {
                $type = 0;
                $groupuser_id = '';
            }
            elseif ($type == 2) {
                $type = 1;
                $groupuser_id = $this->Investor_model->getUserGroup($groupuser_id);
                if (!$this->settingExists($betday, $type, $groupuser_id)) {
                    $type = 0;
                    $groupuser_id = '';
                }
            }
        }

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
            'bet_amount'   => 0,
            'title'         => 'Default'
        );



        if(count($rows))
        {

            $setting = $rows[0];
            $type = $setting['type'];
            $groupuser_id= $setting['groupuser_id'];
            // if($this->isEmptySetting($setting))
            // {
            //     switch ($type) {
            //         case '2':
            //             $setting = $this->getGroupSetting($betday,$groupuser_id);
            //             if($this->isEmptySetting($setting))
            //                 $setting = $this->getAllSetting($betday);
            //             break;
            //         case '1':
            //             $setting = $this->getAllSetting($betday);
            //             break;
            //         case '0':
            //         default:
            //             break;
            //     }
            // }

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
            $result['bet_amount'] = $setting['bet_amount'];
            $result['title'] = $this->getSettingTitle($setting['type'],$setting['groupuser_id']);
        }
        return $result;
    }

    public function getActiveSetting($betday, $settingId = -1){
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        if (!$this->settingExists($betday, $type, $groupuser_id)) {
            if ($type == 1) {
                $type = 0;
                $groupuser_id = '';
            }
            elseif ($type == 2) {
                $type = 1;
                $groupuser_id = $this->Investor_model->getUserGroup($groupuser_id);
                if (!$this->settingExists($betday, $type, $groupuser_id)) {
                    $type = 0;
                    $groupuser_id = '';
                }
            }
        }

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
            'bet_amount'   => 0,
            'title'         => '',
            'is_open'       => 0,
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
            $result['bet_amount'] = $data['bet_amount'];
            $result['title'] = $this->getSettingTitle($data['type'],$data['groupuser_id']);
            $result['is_open'] = $data['is_open'];
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

    public function getCascadingSettings($betday, $categoryType, $categoryGroupUser){

        $fomularData = $this->fomularData;
        $settings = $this->defaultSetting;
        $description = '';
        $bet_amount = 0;
        $bet_analysis = $this->defaultResult;

        $candy_data = $this->CI->Picks_model->getIndividual($betday, 'candy',$categoryType, $categoryGroupUser);
        $pick_data = $this->CI->Picks_model->getIndividual($betday, 'pick',$categoryType, $categoryGroupUser);

        $rr_disableCnt = $this->CI->WorkSheet_model->getDisableCount($betday,$categoryType, $categoryGroupUser);
        $rr_validColumnCnt = $this->CI->WorkSheet_model->getValidRRColumnCount($betday,$categoryType, $categoryGroupUser);

        $parlayCnt = $this->CI->WorkSheet_model->getParlayCount($betday,$categoryType, $categoryGroupUser);

        $custom_bets = $this->CI->CustomBet_model->getData($betday,$categoryType, $categoryGroupUser);

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
            ->from($this->tableName)
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
            $settings[1]['bet_number1'] = $data['rr_number1'] != 0 ? $data['rr_number1'] : '';
            $settings[1]['bet_number2'] = $data['rr_number2'] != 0 ? $data['rr_number2'] : '';
            $settings[1]['bet_number3'] = $data['rr_number3'] != 0 ? $data['rr_number3'] : '';
            $settings[1]['bet_number4'] = $data['rr_number4'] != 0 ? $data['rr_number4'] : '';

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

            $sheet_number = $rr_validColumnCnt * count($candy_data) - $rr_disableCnt;

            $bet_analysis_index = 1;
            $bet_analysis[$bet_analysis_index]['title'] = 'Round Robin 1';
            $bet_analysis[$bet_analysis_index]['rr1'] = $data['rr_number1'];
            $bet_analysis[$bet_analysis_index]['rr2'] = $data['rr_number2'];
            $bet_analysis[$bet_analysis_index]['sheet'] = $sheet_number;
            $bet_analysis[$bet_analysis_index]['order'] = $sheet_number;
            $bet_analysis[$bet_analysis_index]['bets'] = $sheet_number*@$fomularData[$data['rr_number1']][$data['rr_number2']];

            if(!is_null($data['rr_number3']) && $data['rr_number3'] != 0)
            {
                $bet_analysis_index ++;
                $bet_analysis[$bet_analysis_index]['title'] = 'Round Robin 2';
                $bet_analysis[$bet_analysis_index]['rr1'] = $data['rr_number1'];
                $bet_analysis[$bet_analysis_index]['rr2'] = $data['rr_number3'];
                $bet_analysis[$bet_analysis_index]['sheet'] = $sheet_number;
                $bet_analysis[$bet_analysis_index]['order'] = $sheet_number;
                $bet_analysis[$bet_analysis_index]['bets'] = ($sheet_number)*@$fomularData[$data['rr_number1']][$data['rr_number3']];
            }

            if(!is_null($data['rr_number4']) && $data['rr_number4'] != 0)
            {
                $bet_analysis_index ++;
                $bet_analysis[$bet_analysis_index]['title'] = 'Round Robin 3';
                $bet_analysis[$bet_analysis_index]['rr1'] = $data['rr_number1'];
                $bet_analysis[$bet_analysis_index]['rr2'] = $data['rr_number4'];
                $bet_analysis[$bet_analysis_index]['sheet'] = $sheet_number;
                $bet_analysis[$bet_analysis_index]['order'] = $sheet_number;
                $bet_analysis[$bet_analysis_index]['bets'] = ($sheet_number)*@$fomularData[$data['rr_number1']][$data['rr_number4']];
            }

            $bet_analysis_index ++;
            $bet_analysis[$bet_analysis_index]['title'] = 'Parlay';
            $bet_analysis[$bet_analysis_index]['rr1'] = '';
            $bet_analysis[$bet_analysis_index]['rr2'] = '';
            $bet_analysis[$bet_analysis_index]['sheet'] = (int)$parlayCnt > 0 ? $parlayCnt : '' ;
            $parlay_ordernumber = (int)$parlayCnt > 0 ? $parlayCnt : '';
            $bet_analysis[$bet_analysis_index]['order'] = $parlay_ordernumber;
            $bet_analysis[$bet_analysis_index]['bets'] = $parlay_ordernumber;

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

                if ($custom_bet_item['rr_number1'] && $custom_bet_item['rr_number2']) {
                    $settings[] = array(
                        'id'          => $custom_bet_item['id'],
                        'type'        => 'rr',
                        'title'     => "Custom RR ".($key+1),
                        'bet_percent' => floatval($rr_allocation),
                        'bet_text'    => 'by',
                        'bet_number1' => $custom_bet_item['rr_number1'],
                        'bet_number2' => $custom_bet_item['rr_number2'] ? $custom_bet_item['rr_number2'] : "",
                        'bet_number3' => "",
                        'bet_number4' => ""
                    );
                }

                if($custom_bet_item['parlay_number']) {
                    $settings[] = array(
                        'id'          => $custom_bet_item['id'],
                        'type'        => 'parlay',
                        'title' => "Custom Parlay ".($key+1),
                        'bet_percent' => floatval($parlay_allocation),
                        'bet_number1' => $custom_bet_item['parlay_number'] ? 1 : 0,
                        'bet_number2' => "",
                        'bet_number3' => "",
                        'bet_number4' => ""
                    );
                }

                $custom_bet_allocation += @$fomularData[$custom_bet_item['rr_number1']][$custom_bet_item['rr_number2']];
                if($custom_bet_item['rr_number1'] && $custom_bet_item['rr_number2']){
                    $bet_analysis[] = array(
                        'title'     => "Custom RR ".($key+1),
                        'rr1'       => $custom_bet_item['rr_number1'],
                        'rr2'       => $custom_bet_item['rr_number2'],
                        'sheet'     => 1,
                        'order'     => 1,
                        'bets'      => $custom_bet_allocation
                    );
                }

                if($custom_bet_item['parlay_number']){
                    $bet_analysis[] = array(
                        'title'     => "Custom Custom Parlay ".($key+1),
                        'rr1'       => '',
                        'rr2'       => '',
                        'sheet'     => '',
                        'order'     => 1,
                        'bets'      => 1
                    );
                }
            }   

            $description = $data['description'];
            if(!empty($data['bet_amount']))
                $bet_amount = $data['bet_amount'];
        }

        $betDayLock = $this->CI->SystemSettings_model->getBetDay();
        $isLock = 1;
        if($betday == $betDayLock)
            $isLock = 0;
        $result = array(
            'bet_allocation'    => $settings,
            'bet_analysis'      => $bet_analysis,
            'description'       => $description,
            'bet_amount'        => $bet_amount,
            'is_lock'           => $isLock
        );

        // Calculate Recommend Bet Amounts for each bet type
        $bet_percents = [];
        for ($i = 1; $i < count($settings); $i ++) {
            $percent = $settings[$i]['bet_percent'] ?? 0;
            $bet_percents[] = $percent;
        }

        $bets = getBetArr($this->CI->WorkSheet_model->getRROrders($betday, $categoryGroupUser));
        foreach ($bets as &$item) {
            unset($item['data']);
        }

        $orders_cnt = [0, 0, 0];
        $custom_orders_cnt = [];
        foreach ($bets as $item) {
            if ($item['bet_type'] == 'rr') {
                if (!isset($orders_cnt[0])) {
                    $orders_cnt[0] = 0;
                }
                $orders_cnt[0] += $item['m_number'];
            }
            elseif ($item['bet_type'] == 'parlay') {
                if (!isset($orders_cnt[1])) {
                    $orders_cnt[1] = 0;
                }
                $orders_cnt[1] += $item['m_number'];
            }
            elseif ($item['bet_type'] == 'single') {
                if (!isset($orders_cnt[2])) {
                    $orders_cnt[2] = 0;
                }
                $orders_cnt[2] += $item['m_number'];
            }
            elseif ($item['bet_type'] == 'crr' || $item['bet_type'] == 'cparlay') {
                $bet_id = intval(explode('_', $item['title'])[1]);
                if (!isset($custom_orders_cnt[$bet_id])) {
                    $custom_orders_cnt[$bet_id] = [
                        'crr'       => 0,
                        'cparlay'   => 0
                    ];
                }
                $custom_orders_cnt[$bet_id][$item['bet_type']] += $item['m_number'];
            }
        }

        // $recommend_bet_amounts = [];
        // $investor_sportbooks = $this->CI->Investor_model->getInvestorSportboooksWithBets($categoryGroupUser, $betday);
        $investor_sportbooks = [];
        if ($this->actualType == 2) {
            $investor_sportbooks = $this->CI->Investor_model->getInvestorSportboooksWithBets($this->actualGroupUser, $betday);
        }

        $total_balance = 0;
        foreach ($investor_sportbooks as $item) {
            $total_balance += $item['current_balance'];
        }

        for ($i = 0; $i < count($orders_cnt); $i ++) {
            $optimal_balance = $settings[$i+1]['bet_percent'] ? ($total_balance * $settings[$i+1]['bet_percent'] / 100) : 0;
            if ($orders_cnt[$i]) {
                $recommend_bet_amount = roundBetAmount($optimal_balance / $orders_cnt[$i]);
            }
            $settings[$i+1]['recommend_bet_amount'] = $recommend_bet_amount ?? '';
        }

        // For custom bets only
        foreach ($settings as &$item) {
            if (isset($item['id']) && isset($custom_orders_cnt[$item['id']])) {
                $optimal_balance = $item['bet_percent'] ? ($total_balance * $item['bet_percent'] / 100) : 0;
                if (!$optimal_balance || !$custom_orders_cnt[$item['id']]) {
                    $item['recommend_bet_amount'] = '';   
                }
                else {
                    $cnt = $custom_orders_cnt[$item['id']]['c' . $item['type']];
                    if (!$cnt) {
                        $item['recommend_bet_amount'] = '';
                    }
                    else {
                        $item['recommend_bet_amount'] = roundBetAmount($optimal_balance / $cnt);
                    }
                }
            }
        }

        $result['bet_allocation'] = $settings;

        return $result;
    }

    public function settingExists($betday, $categoryType, $categoryGroupUser) {
        $query = $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'betday' => $betday,
                'type'  => $categoryType
            ));
        if($categoryType != 0)
            $query->where('groupuser_id', $categoryGroupUser);

        $rows = $query->get()->result_array();

        return !empty($rows);
    }

    public function getSettings($betday, $categoryType, $categoryGroupUser) {
        $this->actualType = $categoryType;
        $this->actualGroupUser = $categoryGroupUser;

        $settings = $this->getCascadingSettings($betday, 0, '');

        if ($this->settingExists($betday, $categoryType, $categoryGroupUser)) {
            $settings = $this->getCascadingSettings($betday, $categoryType, $categoryGroupUser);
        }
        elseif ($categoryType == 2) {
            $groupId = $this->Investor_model->getUserGroup($categoryGroupUser);
            if ($this->settingExists($betday, 1, $groupId)) {
                $settings = $this->getCascadingSettings($betday, 1, $groupId);
            }
        }

        // Load bet amounts for each bet type
        if ($categoryType == 2) {
            $allocations = $settings['bet_allocation'];
            $sql = "SELECT * FROM `bet_amounts` WHERE `betday`='$betday' AND `investor_id`='$categoryGroupUser'";
            $row = $this->db->query($sql)->row();
            if ($row) {
                $bet_amounts = json_decode($row->data, true);
                for ($i = 1; $i < count($allocations); $i ++) {
                    if (strpos($allocations[$i]['title'], 'Round Robin') !== false) {
                        $allocations[$i]['bet_amount'] = $bet_amounts['rr'];
                    }
                    elseif (strpos($allocations[$i]['title'], 'Parlays') !== false) {
                        $allocations[$i]['bet_amount'] = $bet_amounts['parlay'];
                    }
                    elseif (strpos($allocations[$i]['title'], 'Individual') !== false) {
                        $allocations[$i]['bet_amount'] = $bet_amounts['single'];
                    }
                    elseif (strpos($allocations[$i]['title'], 'Custom') !== false) {
                        $key = $allocations[$i]['type'] . '_' . $allocations[$i]['id'];
                        if (isset($bet_amounts[$key])) {
                            $allocations[$i]['bet_amount'] = $bet_amounts[$key];
                        }
                    }
                }
            }
            $settings['bet_allocation'] = $allocations;
        }

        return $settings;
    }

    public function saveSettings($betday, $categoryType, $categoryGroupUser,$data, $description = ''){

        $jsonData = json_decode($data);
        $settingData = $jsonData->data;
        $description = $jsonData->description;

        $_SESSION['settingType'] = $categoryType;
        $_SESSION['settingGroupuserId'] = $categoryGroupUser;

        // Set settings for children when
        // $categoryType is All or Group selected
        if ($categoryType == 0) {
            $this->db->delete($this->tableName, array('betday' => $betday, 'type !=' => '0'));
            $this->db->delete('work_sheet', array('betday' => $betday, 'type !=' => '0'));
            $this->db->delete('custom_bets', array('betday' => $betday, 'type !=' => '0'));
            $this->db->delete('custom_bet_allocations', array('betday' => $betday, 'type !=' => '0'));
            $this->db->delete('bet_amounts', array('betday' => $betday));
        }
        elseif ($categoryType == 1) {
            $investorIds = $this->Investor_model->getGroupInvestors($categoryGroupUser);
            
            $this->db->where('betday', $betday);
            $this->db->where('type', 2);
            $this->db->where_in('groupuser_id', $investorIds);
            $this->db->delete($this->tableName);

            $this->db->where('betday', $betday);
            $this->db->where('type', 2);
            $this->db->where_in('groupuser_id', $investorIds);
            $this->db->delete('work_sheet');

            $this->db->where('betday', $betday);
            $this->db->where('type', 2);
            $this->db->where_in('groupuser_id', $investorIds);
            $this->db->delete('custom_bets');

            $this->db->where('betday', $betday);
            $this->db->where('type', 2);
            $this->db->where_in('groupuser_id', $investorIds);
            $this->db->delete('custom_bet_allocations');

            $this->db->where('betday', $betday);
            $this->db->where_in('investor_id', $investorIds);
            $this->db->delete('bet_amounts');
        }

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

            $updateQuery->update($this->tableName, $newData);
        } else {
            $newData['betday'] = $betday;
            $newData['type'] = $categoryType;
            if($categoryType != 0)
                $newData['groupuser_id'] = $categoryGroupUser;
            else
                $newData['groupuser_id'] = 0;
            $this->db->insert($this->tableName, $newData);
        }

        for($i=4; $i< count($settingData); $i ++)
        {
            $bet_id = $settingData[$i][9];
            $custom_bet_rows = array_filter($custom_bet_allocations, function($item) use($bet_id, $categoryType, $categoryGroupUser){
                return ((($categoryType == 0 && $item['type'] == $categoryType) || 
                    ($item['type'] == $categoryType && $item['groupuser_id'] == $categoryGroupUser)) && 
                    ($item['bet_id'] == $bet_id));
            });

            if (isset($settingData[$i+1]) && $bet_id == $settingData[$i+1][9]) {
                $customData = array(
                    'rr_allocation'     => $settingData[$i][1],
                    'parlay_allocation' => $settingData[$i+1][1]
                );
                $i ++;
            }
            else {
                $key1 = $settingData[$i][10] == 'rr' ? 'rr_allocation' : 'parlay_allocation';
                $key2 = $key1 == 'rr_allocation' ? 'parlay_allocation' : 'rr_allocation';
                $customData = array(
                    $key1   => $settingData[$i][1],
                    $key2   => 0
                );
            }
            
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

        // Save bet amounts for individuals
        if ($categoryType == 2 && $categoryGroupUser) {
            $bet_amounts = [
                'rr'        => $settingData[1][8] ?? 0,
                'parlay'    => $settingData[2][8] ?? 0,
                'single'    => $settingData[3][8] ?? 0,
            ];
            for ($i = 4; $i < count($settingData); $i ++) {
                $key = $settingData[$i][10] . '_' . $settingData[$i][9];
                $bet_amounts[$key] = $settingData[$i][8] ?? 0;
            }
            $this->db->where(array(
                'betday'        => $betday,
                'investor_id'   => $categoryGroupUser
            ));
            $query = $this->db->get('bet_amounts');
            if ($query->num_rows()) {
                $this->db->where(array(
                    'betday'        => $betday,
                    'investor_id'   => $categoryGroupUser
                ));
                $this->db->update('bet_amounts', array(
                    'data'  => json_encode($bet_amounts)
                ));
            }
            else {
                $this->db->insert('bet_amounts', array(
                    'betday'        => $betday,
                    'investor_id'   => $categoryGroupUser,
                    'data'          => json_encode($bet_amounts)
                ));
            }

            $this->CI->Investor_model->assign($categoryGroupUser, $betday);
        }
        else {
            $group = $categoryType == 0 ? 0 : $categoryGroupUser;
            $investorIds = $this->Investor_model->getGroupInvestors($group);
            foreach ($investorIds as $investor) {
                $settings = $this->getSettings($betday, 2, $investor);
                $allocations = $settings['bet_allocation'];
                $bet_amounts = [
                    'rr'    => $allocations[1]['recommend_bet_amount'],
                    'parlay'=> $allocations[2]['recommend_bet_amount'],
                    'single'=> $allocations[3]['recommend_bet_amount']
                ];
                for ($i = 4; $i < count($allocations); $i ++) {
                    $key = $allocations[$i]['type'] . '_' . $allocations[$i]['id'];
                    $val = $allocations[$i]['recommend_bet_amount'];
                    if ($val == '') {
                        $val = 0;
                    }
                    $bet_amounts[$key] = $val;
                }
                $this->db->insert('bet_amounts', array(
                    'betday'    => $betday,
                    'investor_id' => $investor,
                    'data'  => json_encode($bet_amounts)
                ));

                $this->CI->Investor_model->assign($investor, $betday);
            }
        }
    }

    public function updateIsOpen($betweek, $categoryType, $categoryGroupUser, $isChecked)
    {
        $isChecked = $isChecked == 'true' ? 1 : 0;
        $count = $this->db->from($this->tableName)->where(array(
            'betday'        => $betweek,
            'type'          => $categoryType,
            'groupuser_id'  => $categoryGroupUser,
        ))->get()->num_rows();
        if( $count ) {
            $this->db->where(array(
                'betday'        => $betweek,
                'type'          => $categoryType,
                'groupuser_id'  => $categoryGroupUser,
            ))->update($this->tableName, array(
                'is_open'       => $isChecked
            ));
        } else {
            // temporarily disable
            // $newData = array(
            //     'betday'        => $betweek,
            //     'type'          => $categoryType,
            //     'groupuser_id'  => $categoryGroupUser,
            //     'is_open'       => $isChecked
            // );
            // $this->db->insert('settings', $newData);
        }
        
        return true;
    }

    public function isActiveSetting($betweek, $categoryType, $categoryGroupUser)
    {
        $result = false;
        $rows = $this->db->from($this->tableName)->where(array(
            'betday'        => $betweek,
            'type'          => $categoryType,
            'groupuser_id'  => $categoryGroupUser,
        ))->get()->result_array();
        if(count($rows)) {
            $setting = $rows[0];
            if( $setting['is_open'] )
                $result = true;
        }
        return $result;
    }

    public function getOpenList( $betday )
    {
        $result = array(
            'all'       => false,
            'group'     => [],
            'user'      => []
        );
        $rows = $this->db->from($this->tableName)->where(array(
            'betday'        => $betday,
        ))->get()->result_array();
        foreach ($rows as $key => $item) {
            if( $item['is_open'] == '1') {
                switch ($item['type']) {
                    case 0:
                        $result['all'] = true;
                        break;
                    case 1:
                        $result['group'][] = $item['groupuser_id'];
                        break;
                    case 2:
                        $result['user'][] = $item['groupuser_id'];
                        break;
                }
            }
        }
        return $result;
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
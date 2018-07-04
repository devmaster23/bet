<?php
class Picks_model extends CI_Model {
    private $gameTypeList = array(
        'ncaa_m'=> 'NCAA M', 
        'nba'=> 'NBA', 
        'football'=> 'NFL',
        'ncaa_f'=> 'NCAA F',
        'soccer'=> 'SOC',
        'mlb'=> 'MLB'
    );

    private $gameJsonTpl = array(
        'id',
        'vrn',
        'date',
        'time',
        'team',
        'game_pts',
        'game_ml',
        'game_rl',
        'game_rrl',
        'game_total',
        'first_half_pts',
        'first_half_ml',
        'first_half_total',
    );
    private $pickJsonTpl = array(
        'game_type' => 'game_type',
        'vrn'       => 'vrn',
        'type'      => 'type',
        'team'      => 'team',
        'line'      => 'line',
        'time'      => 'time',
        'count'     => 'count'
    );

    private $typeJsonTpl = array(
        'pts'   => 'SP',
        'ml'    => 'ML',
        'total' => 'TO'
    );
    private $pickType = array(
        'pick',
        'wrapper',
        'candy'
    );

    private function ftime($time,$f) {
        if (gettype($time)=='string')   
          $time = strtotime($time);  
      
        return ($f==24) ? date("G:i", $time) : date("g:i A", $time);    
    }

    private function addPlusMinus($value,$revert = false){
        if($revert)
            $value = $value * -1;
        return $value > 0 ? '+'.$value : $value;
    }

    public function get($betday, $type=null) {
        $this->db->select('*')->from('games');
        $this->db->where(array('betday' => $betday));
        if(!is_null($type))
        {
            $this->db->where('game_type', $type);
        }
        $rows = $this->db->get()->result_array();
        
        $ret = array();
        foreach ($rows as $row) {
            $type = $row['game_type'];
            if (!array_key_exists($type, $ret)) {
                $ret[$type] = array(
                );
            }
            for($i=1; $i<=2; $i++)
            {
                $new_item = array();
                foreach($this->gameJsonTpl as $db_column){
                    $key = $db_column;
                    if($db_column == 'vrn'){
                        $db_column = $db_column.$i;
                    }else if($db_column == 'team'){
                        $db_column = $db_column.$i;
                    }else if(!in_array($db_column, array('id','date','time')))
                    {
                        if($type == 'mlb' && ($db_column == 'game_rl' || $db_column == 'game_rrl'))
                        {
                            $value_db_column = 'team'.$i.'_'.$db_column;
                            $pick_value  = $this->addPlusMinus($row[$value_db_column]). ' ' . $this->addPlusMinus($row[$value_db_column.'_ml']);
                        }else if($db_column == 'game_total' || $db_column == 'first_half_total')
                        {
                            $pick_value = $row[$db_column];
                        }else{
                            $value_db_column = 'team'.$i.'_'.$db_column;
                            $pick_value = $row[$value_db_column];
                        }
                        $new_item[$key.'_value'] = $pick_value;

                        $db_column = 'team'.$i.'_'.$db_column.'_sel';
                    }

                    $value = @$row[$db_column];
                    if($db_column == 'date')
                    {
                        $value = date_format(date_create($value),"M d, Y");
                    }
                    else if($db_column == 'time')
                    {
                        $value = $this->ftime($value,12);
                    }
                    $new_item[$key] = $value;
                }
                array_push($ret[$type], $new_item);
            }
        }
        return $ret;
    }

    public function getAllList($betday)
    {
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $this->db->select('*')->from('games');
        $this->db->where(array(
            'betday' => $betday
        ))->order_by('time', 'ASC')->order_by('game_type', 'ASC');
        $rows = $this->db->get()->result_array();

        $this->db->select('pick_select')->from('work_sheet');
        $this->db->where(array(
            'betday' => $betday,
            'type'  => $type,
            'groupuser_id'  => $groupuser_id
        ));
        $pickSelectList = $this->db->get()->result_array();
        $pickSelectList = count($pickSelectList) && $pickSelectList[0]['pick_select'] ? json_decode($pickSelectList[0]['pick_select']) : array();
        $ret = array();
        foreach($rows as $key => $item){
            $team1_game_pts = json_decode($item['team1_game_pts_sel']);
            $team1_game_ml = json_decode($item['team1_game_ml_sel']);
            $team1_game_total = json_decode($item['team1_game_total_sel']);
            $team1_first_half_pts = json_decode($item['team1_first_half_pts_sel']);
            $team1_first_half_ml = json_decode($item['team1_first_half_ml_sel']);
            $team1_first_half_total = json_decode($item['team1_first_half_total_sel']);
            
            $team2_game_pts = json_decode($item['team2_game_pts_sel']);
            $team2_game_ml = json_decode($item['team2_game_ml_sel']);
            $team2_game_total = json_decode($item['team2_game_total_sel']);
            $team2_first_half_pts = json_decode($item['team2_first_half_pts_sel']);
            $team2_first_half_ml = json_decode($item['team2_first_half_ml_sel']);
            $team2_first_half_total = json_decode($item['team2_first_half_total_sel']);


            if((isset($team1_game_pts->pick) && $team1_game_pts->pick) || (isset($team1_game_pts->wrapper) && $team1_game_pts->wrapper) || (isset($team1_game_pts->candy) && $team1_game_pts->candy))
                $ret[] = $this->getPickData($item, 1, 'pts', false, $pickSelectList);
            if((isset($team1_game_ml->pick) && $team1_game_ml->pick) || (isset($team1_game_ml->wrapper) && $team1_game_ml->wrapper) || (isset($team1_game_ml->candy) && $team1_game_ml->candy))
                $ret[] = $this->getPickData($item, 1, 'ml', false, $pickSelectList);
            if((isset($team1_game_total->pick) && $team1_game_total->pick) || (isset($team1_game_total->wrapper) && $team1_game_total->wrapper) || (isset($team1_game_total->candy) && $team1_game_total->candy))
                $ret[] = $this->getPickData($item, 1, 'total', false, $pickSelectList);

            if((isset($team1_first_half_pts->pick) && $team1_first_half_pts->pick) || (isset($team1_first_half_pts->wrapper) && $team1_first_half_pts->wrapper) || (isset($team1_first_half_pts->candy) && $team1_first_half_pts->candy))
                $ret[] = $this->getPickData($item, 1, 'pts',true, $pickSelectList);
            if((isset($team1_first_half_ml->pick) && $team1_first_half_ml->pick) || (isset($team1_first_half_ml->wrapper) && $team1_first_half_ml->wrapper) || (isset($team1_first_half_ml->candy) && $team1_first_half_ml->candy))
                $ret[] = $this->getPickData($item, 1, 'ml',true, $pickSelectList);
            if((isset($team1_first_half_total->pick) && $team1_first_half_total->pick) || (isset($team1_first_half_total->wrapper) && $team1_first_half_total->wrapper) || (isset($team1_first_half_total->candy) && $team1_first_half_total->candy))
                $ret[] = $this->getPickData($item, 1, 'total',true, $pickSelectList);

            if((isset($team2_game_pts->pick) && $team2_game_pts->pick) || (isset($team2_game_pts->wrapper) && $team2_game_pts->wrapper) || (isset($team2_game_pts->candy) && $team2_game_pts->candy))
                $ret[] = $this->getPickData($item, 2, 'pts', false, $pickSelectList);
            if((isset($team2_game_ml->pick) && $team2_game_ml->pick) || (isset($team2_game_ml->wrapper) && $team2_game_ml->wrapper) || (isset($team2_game_ml->candy) && $team2_game_ml->candy))
                $ret[] = $this->getPickData($item, 2, 'ml', false, $pickSelectList);
            if((isset($team2_game_total->pick) && $team2_game_total->pick) || (isset($team2_game_total->wrapper) && $team2_game_total->wrapper) || (isset($team2_game_total->candy) && $team2_game_total->candy))
                $ret[] = $this->getPickData($item, 2, 'total', false, $pickSelectList);

            if((isset($team2_first_half_pts->pick) && $team2_first_half_pts->pick) || (isset($team2_first_half_pts->wrapper) && $team2_first_half_pts->wrapper) || (isset($team2_first_half_pts->candy) && $team2_first_half_pts->candy))
                $ret[] = $this->getPickData($item, 2, 'pts',true, $pickSelectList);
            if((isset($team2_first_half_ml->pick) && $team2_first_half_ml->pick) || (isset($team2_first_half_ml->wrapper) && $team2_first_half_ml->wrapper) || (isset($team2_first_half_ml->candy) && $team2_first_half_ml->candy))
                $ret[] = $this->getPickData($item, 2, 'ml',true, $pickSelectList);
            if((isset($team2_first_half_total->pick) && $team2_first_half_total->pick) || (isset($team2_first_half_total->wrapper) && $team2_first_half_total->wrapper) || (isset($team2_first_half_total->candy) && $team2_first_half_total->candy))
                $ret[] = $this->getPickData($item, 2, 'total',true, $pickSelectList);
        }

        $result = $ret;
        return $result;
    }

    public function getAll($betday)
    {
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $this->db->select('*')->from('games');
        $this->db->where(array(
            'betday' => $betday
        ))->order_by('time', 'ASC')->order_by('game_type', 'ASC');
        $rows = $this->db->get()->result_array();

        $this->db->select('pick_select')->from('work_sheet');
        $this->db->where(array(
            'betday' => $betday,
            'type'  => $type,
            'groupuser_id'  => $groupuser_id
        ));
        $pickSelectList = $this->db->get()->result_array();
        $pickSelectList = count($pickSelectList) && $pickSelectList[0]['pick_select'] ? json_decode($pickSelectList[0]['pick_select']) : array();
        $ret = array();
        foreach($rows as $key => $item){
            $team1_game_pts = json_decode($item['team1_game_pts_sel']);
            $team1_game_ml = json_decode($item['team1_game_ml_sel']);
            $team1_game_rl = json_decode($item['team1_game_rl_sel']);
            $team1_game_rrl = json_decode($item['team1_game_rrl_sel']);
            $team1_game_total = json_decode($item['team1_game_total_sel']);
            
            $team1_first_half_pts = json_decode($item['team1_first_half_pts_sel']);
            $team1_first_half_ml = json_decode($item['team1_first_half_ml_sel']);
            $team1_first_half_total = json_decode($item['team1_first_half_total_sel']);
            
            $team2_game_pts = json_decode($item['team2_game_pts_sel']);
            $team2_game_ml = json_decode($item['team2_game_ml_sel']);
            $team2_game_rl = json_decode($item['team2_game_rl_sel']);
            $team2_game_rrl = json_decode($item['team2_game_rrl_sel']);
            $team2_game_total = json_decode($item['team2_game_total_sel']);

            $team2_first_half_pts = json_decode($item['team2_first_half_pts_sel']);
            $team2_first_half_ml = json_decode($item['team2_first_half_ml_sel']);
            $team2_first_half_total = json_decode($item['team2_first_half_total_sel']);

            foreach($this->pickType as $type_item)
            {   
                if (!array_key_exists($type_item, $ret)) {
                    $ret[$type_item] = array(
                    );
                }
                if(isset($team1_game_pts->$type_item) && $team1_game_pts->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 1, 'pts', false, $pickSelectList);
                if(isset($team1_game_ml->$type_item) && $team1_game_ml->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 1, 'ml', false, $pickSelectList);
                if(isset($team1_game_rl->$type_item) && $team1_game_rl->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 1, 'rl', false, $pickSelectList);
                if(isset($team1_game_rrl->$type_item) && $team1_game_rrl->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 1, 'rrl', false, $pickSelectList);
                if(isset($team1_game_total->$type_item) && $team1_game_total->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 1, 'total', false, $pickSelectList);

                if(isset($team1_first_half_pts->$type_item) && $team1_first_half_pts->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 1, 'pts',true, $pickSelectList);
                if(isset($team1_first_half_ml->$type_item) && $team1_first_half_ml->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 1, 'ml',true, $pickSelectList);
                if(isset($team1_first_half_total->$type_item) && $team1_first_half_total->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 1, 'total',true, $pickSelectList);

                if(isset($team2_game_pts->$type_item) && $team2_game_pts->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 2, 'pts', false, $pickSelectList);
                if(isset($team2_game_ml->$type_item) && $team2_game_ml->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 2, 'ml', false, $pickSelectList);
                if(isset($team2_game_rl->$type_item) && $team2_game_rl->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 2, 'rl', false, $pickSelectList);
                if(isset($team2_game_rrl->$type_item) && $team2_game_rrl->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 2, 'rrl', false, $pickSelectList);
                if(isset($team2_game_total->$type_item) && $team2_game_total->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 2, 'total', false, $pickSelectList);

                if(isset($team2_first_half_pts->$type_item) && $team2_first_half_pts->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 2, 'pts',true, $pickSelectList);
                if(isset($team2_first_half_ml->$type_item) && $team2_first_half_ml->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 2, 'ml',true, $pickSelectList);
                if(isset($team2_first_half_total->$type_item) && $team2_first_half_total->$type_item)
                    $ret[$type_item][] = $this->getPickData($item, 2, 'total',true, $pickSelectList);
            }
        }

        $result = array();
        $maxRowCount = 60;
        foreach($ret as $key => $arrayItem){
            $maxRowCount = count($arrayItem) > $maxRowCount ? count($arrayItem): $maxRowCount;
        }
        for($i=0; $i<$maxRowCount; $i++)
        {
            $row_item = array(
                'id' => $i+1
            );
            foreach($this->pickType as $type)
            {
                foreach($this->pickJsonTpl as $colum_index => $db_column)
                {
                    $new_colum_index = $type.'_'.$colum_index;
                    $value = null;
                    if(isset($ret[$type][$i]))
                    {
                        $value = $ret[$type][$i][$colum_index];
                    }
                    $row_item[$new_colum_index] = $value;
                }
                if($type == 'pick')
                {
                    $row_item['selected'] = @$ret[$type][$i]['selected'];   
                }
                $row_item[$type.'_key'] = @$ret[$type][$i]['key'];
            }
            $result[] = $row_item;
        }
        return $result;
    }

    public function getIndividual($betday, $individual_type = 'candy', $categoryType = null, $categoryGroupUser = null)
    {
        $type = !is_null($categoryType)? $categoryType : (isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0);
        $groupuser_id = !is_null($categoryGroupUser)? $categoryGroupUser : (isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0);

        $this->db->select('*')->from('games');
        $this->db->where(array(
            'betday' => $betday
        ))->order_by('time', 'ASC')->order_by('game_type', 'ASC');
        $rows = $this->db->get()->result_array();

        $this->db->select('pick_select')->from('work_sheet');
        $this->db->where(array(
            'betday' => $betday,
            'type'  => $type,
            'groupuser_id'  => $groupuser_id
        ));
        $pickSelectList = $this->db->get()->result_array();
        $pickSelectList = count($pickSelectList) && $pickSelectList[0]['pick_select'] ? json_decode($pickSelectList[0]['pick_select']) : array();

        $type_item = $individual_type;

        $ret = array();
        foreach($rows as $key => $item){
            $team1_game_pts = json_decode($item['team1_game_pts_sel']);
            $team1_game_ml = json_decode($item['team1_game_ml_sel']);
            $team1_game_total = json_decode($item['team1_game_total_sel']);
            $team1_first_half_pts = json_decode($item['team1_first_half_pts_sel']);
            $team1_first_half_ml = json_decode($item['team1_first_half_ml_sel']);
            $team1_first_half_total = json_decode($item['team1_first_half_total_sel']);
            
            $team2_game_pts = json_decode($item['team2_game_pts_sel']);
            $team2_game_ml = json_decode($item['team2_game_ml_sel']);
            $team2_game_total = json_decode($item['team2_game_total_sel']);
            $team2_first_half_pts = json_decode($item['team2_first_half_pts_sel']);
            $team2_first_half_ml = json_decode($item['team2_first_half_ml_sel']);
            $team2_first_half_total = json_decode($item['team2_first_half_total_sel']);

            if(isset($team1_game_pts->$type_item) && $team1_game_pts->$type_item)
                $ret[] = $this->getPickData($item, 1, 'pts', false, $pickSelectList);
            if(isset($team1_game_ml->$type_item) && $team1_game_ml->$type_item)
                $ret[] = $this->getPickData($item, 1, 'ml', false, $pickSelectList);
            if(isset($team1_game_total->$type_item) && $team1_game_total->$type_item)
                $ret[] = $this->getPickData($item, 1, 'total', false, $pickSelectList);

            if(isset($team1_first_half_pts->$type_item) && $team1_first_half_pts->$type_item)
                $ret[] = $this->getPickData($item, 1, 'pts',true, $pickSelectList);
            if(isset($team1_first_half_ml->$type_item) && $team1_first_half_ml->$type_item)
                $ret[] = $this->getPickData($item, 1, 'ml',true, $pickSelectList);
            if(isset($team1_first_half_total->$type_item) && $team1_first_half_total->$type_item)
                $ret[] = $this->getPickData($item, 1, 'total',true, $pickSelectList);

            if(isset($team2_game_pts->$type_item) && $team2_game_pts->$type_item)
                $ret[] = $this->getPickData($item, 2, 'pts', false, $pickSelectList);
            if(isset($team2_game_ml->$type_item) && $team2_game_ml->$type_item)
                $ret[] = $this->getPickData($item, 2, 'ml', false, $pickSelectList);
            if(isset($team2_game_total->$type_item) && $team2_game_total->$type_item)
                $ret[] = $this->getPickData($item, 2, 'total', false, $pickSelectList);

            if(isset($team2_first_half_pts->$type_item) && $team2_first_half_pts->$type_item)
                $ret[] = $this->getPickData($item, 2, 'pts',true, $pickSelectList);
            if(isset($team2_first_half_ml->$type_item) && $team2_first_half_ml->$type_item)
                $ret[] = $this->getPickData($item, 2, 'ml',true, $pickSelectList);
            if(isset($team2_first_half_total->$type_item) && $team2_first_half_total->$type_item)
                $ret[] = $this->getPickData($item, 2, 'total',true, $pickSelectList);
        }

        return $ret;
    }

    private function getPickData($row, $team_id=1, $type, $first_half = false, $pickSelectList){
        $item = array();
        $gameType = $row['game_type'];
        foreach($this->pickJsonTpl as $key => $db_column)
        {
            $value = '';

            if($db_column == 'game_type')
            {
                $value = $this->gameTypeList[$row[$db_column]];
            }
            else if($db_column == 'team' OR $db_column == 'vrn')
            {
                $value = $row[$db_column.$team_id];
            }else if($db_column == 'time')
            {
                $value = $this->ftime($row[$db_column],12);
            }
            else if($db_column == 'type')
            {   
                if($type == 'total')
                {
                    if($team_id == 1)
                        $value = 'OVER';
                    else
                        $value = 'UNDER';
                    if($first_half)
                        $value = substr($value,0,2);
                }else
                {
                    $value = strtoupper($type);
                }
                if($first_half)
                    $value = '1st '.$value;
            }
            else if($db_column == 'line')
            {
                if(!$first_half)
                    $value_db_column = 'game_';   
                else
                    $value_db_column = 'first_half_';

                if($type == 'total')
                {
                    $value_db_column = $value_db_column.$type;
                }else{
                    $value_db_column = 'team'.$team_id.'_'.$value_db_column.$type;
                }

                if($type == 'rl' || $type == 'rrl')
                {
                    $value_db_column = 'team'.$team_id.'_game_'.$type;
                    $value = $this->addPlusMinus($row[$value_db_column]). ' ' . $this->addPlusMinus($row[$value_db_column.'_ml']);
                }else{
                    $value = $row[$value_db_column];
                }
            }
            else if($db_column == 'count')
            {
                $value = 0;
            }
            else if(is_null($db_column))
            {   
                $value = '';
            }else
            {
                $value = $row[$db_column];
            }

            $item[$key] = $value;
        }

        $item['select'] = $row['id'].'_'.$team_id.'_'.$type.'_'.($first_half ? 1 : 0);
        $item['title'] = $item['select'];
        $item['bet_type'] = 'single';
        $item['selected'] = false;
        if(in_array($item['select'], $pickSelectList))
        {
            $item['selected'] = true;
        }
        $item['key'] = $row['id'];
        $item['team_id'] = $team_id;
        return $item;
    }

    public function save($betday, $game_type, $games)
    {

        $insertData = array();
        $gamesList = json_decode($games);
        foreach($gamesList->data as $key => $item){
            $team = 'team1';
            if($key % 2 == 1)
                $team = 'team2';

            $gameData = array();
            $row_id = $item->id;

            foreach($this->gameJsonTpl as $index){
                if(!in_array($index, array('game_pts','game_ml','game_rl','game_rrl','game_total','first_half_pts','first_half_ml','first_half_total')))
                {
                    continue;
                }
                $game_data_index = $team.'_'.$index.'_sel';
                $value = '';
                if(!is_null($item->$index))
                    $value = $item->$index;
                $gameData[$game_data_index] = $value;
            }

            $this->db->where(array(
                'id'        =>$row_id,
                'game_type' =>$game_type,
                'betday'    =>$betday
            ));
            $this->db->update('games', $gameData);
        }

    }
    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
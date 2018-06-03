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
        'game_total',
        'first_half_pts',
        'first_half_ml',
        'first_half_total'
    );
    private $pickJsonTpl = array(
        'game_type' => 'game_type',
        'vrn'       => 'vrn',
        'type'      => 'type',
        'team'      => 'team',
        'line'      => 'game_ml',
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

    public function get($betday) {
        $this->db->select('*')->from('games');
        $this->db->where(array('betday' => $betday));
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
                        $db_column = 'team'.$i.'_'.$db_column;
                    }
                    $value = $row[$db_column];
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
            $team1_game_pts = json_decode($item['team1_game_pts']);
            $team1_game_ml = json_decode($item['team1_game_ml']);
            $team1_game_total = json_decode($item['team1_game_total']);
            $team1_first_half_pts = json_decode($item['team1_first_half_pts']);
            $team1_first_half_ml = json_decode($item['team1_first_half_ml']);
            $team1_first_half_total = json_decode($item['team1_first_half_total']);
            
            $team2_game_pts = json_decode($item['team2_game_pts']);
            $team2_game_ml = json_decode($item['team2_game_ml']);
            $team2_game_total = json_decode($item['team2_game_total']);
            $team2_first_half_pts = json_decode($item['team2_first_half_pts']);
            $team2_first_half_ml = json_decode($item['team2_first_half_ml']);
            $team2_first_half_total = json_decode($item['team2_first_half_total']);

            foreach($this->pickType as $type_item)
            {   
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
            $team1_game_pts = json_decode($item['team1_game_pts']);
            $team1_game_ml = json_decode($item['team1_game_ml']);
            $team1_game_total = json_decode($item['team1_game_total']);
            $team1_first_half_pts = json_decode($item['team1_first_half_pts']);
            $team1_first_half_ml = json_decode($item['team1_first_half_ml']);
            $team1_first_half_total = json_decode($item['team1_first_half_total']);
            
            $team2_game_pts = json_decode($item['team2_game_pts']);
            $team2_game_ml = json_decode($item['team2_game_ml']);
            $team2_game_total = json_decode($item['team2_game_total']);
            $team2_first_half_pts = json_decode($item['team2_first_half_pts']);
            $team2_first_half_ml = json_decode($item['team2_first_half_ml']);
            $team2_first_half_total = json_decode($item['team2_first_half_total']);

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

    public function getIndividual($betday, $individual_type = 'candy')
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

        $type_item = $individual_type;

        $ret = array();
        foreach($rows as $key => $item){
            $team1_game_pts = json_decode($item['team1_game_pts']);
            $team1_game_ml = json_decode($item['team1_game_ml']);
            $team1_game_total = json_decode($item['team1_game_total']);
            $team1_first_half_pts = json_decode($item['team1_first_half_pts']);
            $team1_first_half_ml = json_decode($item['team1_first_half_ml']);
            $team1_first_half_total = json_decode($item['team1_first_half_total']);
            
            $team2_game_pts = json_decode($item['team2_game_pts']);
            $team2_game_ml = json_decode($item['team2_game_ml']);
            $team2_game_total = json_decode($item['team2_game_total']);
            $team2_first_half_pts = json_decode($item['team2_first_half_pts']);
            $team2_first_half_ml = json_decode($item['team2_first_half_ml']);
            $team2_first_half_total = json_decode($item['team2_first_half_total']);

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
                    if($gameType == 'mlb' && $type == 'pts')
                        $value = 'RL';
                    else
                        $value = strtoupper($type);
                }
                if($first_half)
                    $value = '1st '.$value;
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

        if ($gameType == 'mlb')
        {
            $value = 0;
            switch ($type) {
                case 'pts':
                    $value = (($row['game_rl'] > 0 ) ? '+' : ''). $row['game_rl']. ' ' .$row['game_rl_ml'];
                    break;
                case 'ml':
                    $value = $row['game_ml'];
                    break;
                case 'total':
                default:
                    $value = $row['game_total'];
                    break;
            }
            if($team_id == 2)
            {
                $value *= -1;
                if($type =='total')
                {
                    $value = $row['first_half_total'];
                }
            }

            $item['line'] = $value;

        }
        $item['select'] = $row['id'].'_'.$team_id.'_'.$type.'_'.($first_half ? 1 : 0);
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
            $row_id = $item[0];

            foreach($this->gameJsonTpl as $index => $game_data_index){
                if(!in_array($game_data_index, array('game_pts','game_ml','game_total','first_half_pts','first_half_ml','first_half_total')))
                {
                    continue;
                }
                $game_data_index = $team.'_'.$game_data_index;
                $value = '';
                if(!is_null($item[$index]))
                    $value = $item[$index];
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
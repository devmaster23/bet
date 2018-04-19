<?php
class WorkSheet_model extends CI_Model {
    private $tableName = 'work_sheet';

    public function getBetSetting($betday){
        $this->db->select('*')->from($this->tableName);
        $this->db->where(array('betday' => $betday));
        $rows = $this->db->get()->result_array();
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
        array_push($ret['sheet_data'], array(@$row['robin_1'],@$row['robin_2'],@$row['robin_3']));
        array_push($ret['date_info'], array(
            date_format(date_create(@$row['date']),"M d, Y"),
            date_format(date_create(@$row['date']),"Y"),
            @$row['betday']
        ));

        return $ret;
    }

    public function getBetSheet($betday)
    {
        $CI =& get_instance();
        $CI->load->model('picks_model');
        $pick_data = $CI->picks_model->getAll($betday);

        $this->db->select('*')->from($this->tableName);
        $this->db->where(array('betday' => $betday));
        $rows = $this->db->get()->result_array();


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
            $robin_1 = $row['robin_1'];

            for($i=0; $i<60; $i++)
            {
                $ret[$i] = array();
                $candy_item = $this->getTeamFromPick($pick_data, $i, 'candy');
                for($j=0; $j<7; $j++){
                    $ret[$i][$j] = array();
                    $disableList = array();
                    for($k=0; $k<$robin_1-1; $k++){
                        $team_row_id = $settingData[$k][$j];
                        $team_info = $this->getTeamFromPick($pick_data, $team_row_id-1);
                        array_push($ret[$i][$j],$team_info);    
                        if($candy_item['team'] != null && $candy_item['team'] == $team_info['team'])
                            $disableList[] = $k;
                    }    
                    array_push($ret[$i][$j],$candy_item);
                    $ret[$i][$j]['title'] = chr(65+$j).($i+1);
                    $ret[$i][$j]['disabled'] = $disableList;
                }
            }
            $result['type'] = @$row['robin_1'].'-'.@$row['robin_2'].'-'.@$row['robin_3'];
            $result['date'] = $row['date'];
            $result['data'] = $ret;
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
    public function saveSetting($betday, $setting)
    {
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
            'betday' =>$betday
        ));
        $rows = $this->db->get()->result_array();
        if(count($rows))
        {
            $this->db->where(array(
                'betday'    =>$betday
            ));
            $this->db->update('work_sheet', $data);
        }
        else{
            $this->db->insert('work_sheet', array_merge(array(
                'betday'=>$betday
            ),$data));
        }
    }
    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
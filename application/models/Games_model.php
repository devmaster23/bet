<?php
class Games_model extends CI_Model {
	private $gameJsonTpl = array(
		'id',
		'date',
		'time',
		'vrn1',
		'team2',
        'alpha',
        'team1',
        'team1_game_pts',
        'team2_game_pts',
        'team1_game_rl',
        'team2_game_rl',
        'team1_game_rl_ml',
        'team2_game_rl_ml',
        'team1_game_rrl',
        'team2_game_rrl',
        'team1_game_rrl_ml',
        'team2_game_rrl_ml',
        'team1_game_ml',
        'team2_game_ml',
        'game_total',
        'team1_first_half_pts',
        'team2_first_half_pts',
        'team1_first_half_ml',
        'team2_first_half_ml',
        'first_half_total'
	);
	private function ftime($time,$f) {
	    if (gettype($time)=='string')	
		  $time = strtotime($time);	 
	  
	    return ($f==24) ? date("G:i", $time) : date("g:i A", $time);	
  	}

	public function getGames($betday, $type = null) {
		$this->db->select('*')->from('games');
		$this->db->where(array('betday' => $betday));
		if(!is_null($type))
			$this->db->where('game_type', $type);
		$rows = $this->db->get()->result_array();
		
		$ret = array();
		foreach ($rows as $row) {
			$new_item = array();
			$type = $row['game_type'];
			if (!array_key_exists($type, $ret)) {
				$ret[$type] = array();
			}

			foreach($this->gameJsonTpl as $db_column_name){
				if($db_column_name == 'alpha')
					$new_item[$db_column_name] = '@';
				else if($db_column_name == 'date')
				{
					$new_item[$db_column_name] = date_format(date_create($row[$db_column_name]),"M d, Y");
				}
				else if($db_column_name == 'time')
				{
					$new_item[$db_column_name] = $this->ftime($row[$db_column_name],12);
				}
				else
					$new_item[$db_column_name] = $row[$db_column_name];
			}
			array_push($ret[$type], $new_item);
		}
		return $ret;
	}

	public function saveGames($betday, $game_type, $games)
	{

		$insertData = array();
		$gamesList = json_decode($games);
		$validIds = array();
		foreach($gamesList->data as $key => $item){
			$gameData = array();
			$row_id = $item->id;

			foreach($this->gameJsonTpl as $game_data_index){
				if($game_data_index =='id' or $game_data_index =='alpha')
					continue;
				$value = '';

				if(!is_null($item->$game_data_index))
					$value = $item->$game_data_index;

				if($game_data_index == 'date')
				{	
					$value = date_format(date_create($value),"Y-m-d");
				}else if($game_data_index == 'time')
				{
					$value = $this->ftime($value,24);
				}
				
				$gameData[$game_data_index] = $value;
			}
			$gameData['game_type'] =  $game_type;
			$gameData['betday'] =  $betday;
			$gameData['vrn2'] =  $gameData['vrn1']+1;
			if(is_null($row_id))  // new items
			{
				$insertData[] = $gameData;
			}else{
				$validIds[] = $row_id;
				$this->db->where(array(
	                'id'        =>$row_id,
	                'game_type' =>$game_type,
	                'betday'    =>$betday
	            ));
				$this->db->update('games', $gameData);
			}
		}
		if(count($validIds))
		{
			$this->db->where(array(
				'game_type' => $game_type,
				'betday' 	=> $betday
			))->where_not_in('id', $validIds);
			$this->db->delete('games');
		}
		foreach ($insertData as $newItem) {
			$this->db->insert('games', $newItem);
		}
	}
	public function q($sql) {
		$result = $this->db->query($sql);
	}
}
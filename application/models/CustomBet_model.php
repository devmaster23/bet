<?php
class CustomBet_model extends CI_Model {
    private $tableName = 'custom_bets';
    private $tableName1 = 'custom_bet_allocations';

    public function getData($betday, $type, $groupuser_id){
        $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'betday' => $betday,
                'type' => $type,
                'groupuser_id' => $groupuser_id,
            ))
            ->order_by('id','asc');
        $result = $this->db->get()->result_array();
        foreach ($result as &$row_item) {
            if(is_null(@json_decode($row_item['rr_bets']))){
                $row_item['rr_bets'] = json_encode(array());
            }

            if(is_null(@json_decode($row_item['parlay_bets']))){
                $row_item['parlay_bets'] = json_encode(array());
            }
        }
        return $result;
    }

    public function saveCustomBet($betday, $data, $type, $groupuser_id){
        $betData = json_decode($data);

        $this->db->where(array(
            'betday'        => $betday,
            'type'          => $type,
            'groupuser_id'  => $groupuser_id
        ));
        $this->db->delete($this->tableName);
        $this->db->where(array(
            'betday'        => $betday,
            'type'          => $type,
            'groupuser_id'  => $groupuser_id
        ));
        $this->db->delete($this->tableName1);

        foreach($betData->data as $key => $betItem){
            // $id = $betItem->id;
            $row_data = array(
                'rr_number1' => $betItem->rr_number1,
                'rr_number2' => $betItem->rr_number2,
                'parlay_number' => $betItem->parlay_number,
                'rr_bets' => json_encode($betItem->rr_bets),
                'parlay_bets' => json_encode($betItem->parlay_bets)
            );
            
            $this->db->insert($this->tableName, array_merge(array(
                'betday'        => $betday,
                'type'          => $type,
                'groupuser_id'  => $groupuser_id
            ),$row_data));
        }
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
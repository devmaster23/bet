<?php
class CustomBet_model extends CI_Model {
    private $tableName = 'custom_bets';

    public function getData($betday){
        $this->db->select('*')
            ->from($this->tableName)
            ->where('betday',$betday)
            ->order_by('id','asc');

        $result = $this->db->get()->result_array();;
        return $result;
    }

    public function saveCustomBet($betday, $data){
        $betData = json_decode($data);
        $validIds = array();
        foreach($betData->data as $key => $betItem){
            $id = $betItem->id;
            $row_data = array(
                'rr_number1' => $betItem->rr_number1,
                'rr_number2' => $betItem->rr_number2,
                'parlay_number' => $betItem->parlay_number,
                'rr_bets' => json_encode($betItem->rr_bets)
            );

            if($id != -1)
            {
                $validIds[] = $id;
                $this->db->where(array(
                    'betday'    =>$betday,
                    'id'        =>$id
                ));
                $this->db->update($this->tableName, $row_data);
            }else{
                $this->db->insert($this->tableName, array_merge(array(
                    'betday'    =>$betday
                ),$row_data));
                $validIds[] = $this->db->insert_id();
            }
        }
        if(count($validIds))
        {
            $this->db->where_not_in('id', $validIds);
            $this->db->delete($this->tableName);
        }

    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
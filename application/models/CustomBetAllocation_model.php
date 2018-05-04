<?php
class CustomBetAllocation_model extends CI_Model {
    private $tableName = 'custom_bet_allocations';

    public function getByBetday($betday,$categoryType,$categoryGroupUser){
        $query = $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'betday' => $betday,
                'type' => $categoryType
            ));
        if($categoryType != 0)
            $query->where('groupuser_id', $categoryGroupUser);

        $result = $query->get()->result_array();;
        return $result;
    }
    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
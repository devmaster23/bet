<?php
class CustomBetAllocation_model extends CI_Model {
    private $tableName = 'custom_bet_allocations';

    function __construct() {
        $this->load->model('Investor_model');
    }

    public function betExists($betday,$categoryType,$categoryGroupUser) {
        $query = $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'betday' => $betday,
                'type'  => $categoryType
            ));
        if($categoryGroupUser != 0)
            $query->where('groupuser_id', $categoryGroupUser);

        $rows = $query->get()->result_array();

        return !empty($rows);
    }

    public function getCascadingByBetday($betday,$categoryType,$categoryGroupUser){
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

    public function getByBetday($betday,$categoryType,$categoryGroupUser) {
        if ($this->betExists($betday,$categoryType,$categoryGroupUser)) {
            return $this->getCascadingByBetday($betday, $categoryType, $categoryGroupUser);
        }

        if ($categoryType == 2) {
            $groupId = $this->Investor_model->getUserGroup($categoryGroupUser);
            if ($this->betExists($betday, 1, $groupId)) {
                return $this->getCascadingByBetday($betday, 1, $groupId);
            }
        }

        return $this->getCascadingByBetday($betday, 0, '');
    }


    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
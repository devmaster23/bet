<?php
class Investor_sportbooks_model extends CI_Model {
    private $tableName = 'investor_sportbooks';
    private $relationTableName = 'sportsbooks';

    public function getListByInvestorId($investor_id)
    {
        $rows = $this->db->select("A.id as relation_id, DATE_FORMAT(A.date_opened, '%M %d, %Y') as date_opened, A.current_balance, A.opening_balance, A.login_name, A.password, B.*")
            ->from($this->tableName.' as A')
            ->join($this->relationTableName.' as B', 'A.sportbook_id = B.id')
            ->where(array(
                'A.investor_id' => $investor_id
            ))
            ->get()->result_array();
        return $rows;
    }   

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
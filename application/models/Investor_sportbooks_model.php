<?php
class Investor_sportbooks_model extends CI_Model {
    private $tableName = 'investor_sportbooks';
    private $relationTableName = 'sportsbooks';

    public function getListByInvestorId($investor_id,$betweek)
    {
        $result = [];
        $rows = $this->db->select("A.id as relation_id, A.*,  DATE_FORMAT(A.date_opened, '%M %d, %Y') as date_opened, B.*")
            ->from($this->tableName.' as A')
            ->join($this->relationTableName.' as B', 'A.sportbook_id = B.id')
            ->where(array(
                'A.investor_id' => $investor_id
            ))
            ->get()->result_array();
        foreach ($rows as $item) {
            $tmpArr = $item;
            if($betweek >=1 && $betweek <= 53)
                $tmpArr['current_balance'] = $item['current_balance_'.$betweek];
            $result[] = $tmpArr;
        }
        return $result;
    }   

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
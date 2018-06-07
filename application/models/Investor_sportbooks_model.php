<?php
class Investor_sportbooks_model extends CI_Model {
    private $tableName = 'investor_sportbooks';
    private $relationTableName = 'sportsbooks';

    public function getListByInvestorId($investor_id,$betweek)
    {
        $result = [];
        $rows = $this->db->select("A.id as relation_id, A.*,  DATE_FORMAT(A.date_opened, '%M %d, %Y') as date_opened, B.*, count(C.id) as bet_count")
            ->from($this->tableName.' as A')
            ->join($this->relationTableName.' as B', 'A.sportbook_id = B.id', 'left')
            ->join('orders as C', 'C.sportbook_id = A.sportbook_id AND C.investor_id = '.$investor_id.' AND betday = '.$betweek, 'left')
            ->where(array(
                'A.investor_id' => $investor_id
            ))
            ->group_by(array(
                'A.investor_id',
                'A.sportbook_id'
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
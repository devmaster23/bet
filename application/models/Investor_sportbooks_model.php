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

    public function addBalance($investorId, $sportbookId, $betweek, $betAmount)
    {

        $rows = $this->db->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'sportbook_id' => $sportbookId,
        ))->get()->result_array();

        $current_balance = 0;

        if(count($rows))
        {
            $current_balance = $rows[0]['current_balance_'.$betweek];
        }else{
            return false;
        }

        $date = new DateTime();
        $date_opened = $date->format('Y-m-d');

        $newBalance =  $current_balance + $betAmount;
        $newData = array(
            'current_balance_'.$betweek => $newBalance,
            'date_opened' => $date_opened
        );

        $this->db->where(array(
            'investor_id' => $investorId,
            'sportbook_id' => $sportbookId,
        ))->update($this->tableName, $newData);
        return true;
    }

    public function removeBalance($investorId, $sportbookId, $betweek, $betAmount)
    {
        $rows = $this->db->from($this->tableName)
        ->where(array(
            'investor_id' => $investorId,
            'sportbook_id' => $sportbookId,
        ))->get()->result_array();

        $current_balance = 0;

        if(count($rows))
        {
            $current_balance = $rows[0]['current_balance_'.$betweek];
        }else{
            return false;
        }

        $date = new DateTime();
        $date_opened = $date->format('Y-m-d');

        $newBalance = $current_balance - $betAmount > 0 ? $current_balance - $betAmount: 0;
        $removeBalance = $current_balance > $betAmount ? $betAmount : $current_balance;
        
        $newData = array(
            'current_balance_'.$betweek => $newBalance,
            'date_opened' => $date_opened
        );

        $this->db->where(array(
            'investor_id' => $investorId,
            'sportbook_id' => $sportbookId,
        ))->update($this->tableName, $newData);
        return $removeBalance;
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
<?php
class Sportbook_model extends CI_Model {
    private $tableName = 'sportsbooks';
    private $relationTableName = 'investor_sportbooks';
    private $pageURL = 'sportbooks';

    private $dbColumns = array(
        'title',
        'siteurl',
        'adress1',
        'address2',
        'city',
        'state',
        'country',
        'zip_code',
        'phone_number',
        'contact_name',
        'note',
        'singlebet_min',
        'singlebet_max',
        'parlay_min_team',
        'parlay_max_team',
        'parlay_min_bet',
        'parlay_max_bet',
        'rr_min_team',
        'rr_max_team',
        'rr_max_combination',
        'rr_min_bet',
        'rr_max_bet'
    );
    public function getList(){
        $result = [];
        $rows = $this->db->select('*')
            ->from($this->tableName)
            ->order_by('id','asc')
            ->get()->result_array();

        foreach ($rows as $key => $item) {
            $tmpArr = $item;
            $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='edit' href='/".$this->pageURL."/edit?id=".$item['id']."'>Edit</i></a><a class='delete'>Delete</a></div>";
            $result[] = $tmpArr;
        }
        return $result;
    }

    public function getItem($id=null)
    {
        $result = null;
        $row = $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'id' => $id
            ))
            ->get()->result_array();
        if(count($row))
            $result = $row[0];
        return $result;
    }

    public function addItem($data)
    {
        $addDate = [];
        foreach ($this->dbColumns as $dbColumn) {
            if(isset($data[$dbColumn]))
                $addDate[$dbColumn] = $data[$dbColumn];
        }
        $this->db->insert($this->tableName, $addDate);
        return true;
    }

    public function updateItem($id, $data)
    {
        $updateDate = [];
        foreach ($this->dbColumns as $dbColumn) {
            if(isset($data[$dbColumn]))
                $updateDate[$dbColumn] = $data[$dbColumn];
        }
        $this->db->where(array(
            'id' => $id
        ))->update($this->tableName,$updateDate);
        return true;
    }
    
    public function deleteItem($id)
    {
       $this->db->where(array(
            'id' => $id
        ))->delete($this->tableName);
       return ture;
    }    
    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
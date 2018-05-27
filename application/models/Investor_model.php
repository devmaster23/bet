<?php
class Investor_model extends CI_Model {
    private $tableName = 'investors';
    private $relationTableName = 'investor_sportbooks';
    private $pageURL = 'investors';

    private $dbColumns = array(
        'first_name',
        'last_name',
        'address1',
        'address2',
        'state',
        'city',
        'zip_code',
        'country',
        'email',
        'phone_number',
        'starting_bankroll',
        'notes'
    );

    private $relationDbColumns = array(
        'sportbook_id',
        'date_opened',
        'opening_balance',
        'current_balance',
        'login_name',
        'password'
    );

    public function getList(){
        $CI =& get_instance();
        $CI->load->model('Investor_sportbooks_model');

        $result = [];
        $rows = $this->db->select('*')
            ->from($this->tableName)
            ->order_by('id','asc')
            ->get()->result_array();

        foreach ($rows as $key => $item) {
            $tmpArr = $item;
            $investorId = $item['id'];

            $tmpArr['sportbooks'] = $CI->Investor_sportbooks_model->getListByInvestorId($investorId);
            $tmpArr['current_balance'] = 0;
            foreach ($tmpArr['sportbooks'] as $sportbook_item) {
                $tmpArr['current_balance'] += $sportbook_item['current_balance'];
            }
            $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='edit' href='/".$this->pageURL."/edit?id=".$item['id']."'>Edit</i></a><a class='delete'>Delete</a></div>";

            $result[] = $tmpArr;
        }
        return $result;
    }

    public function getItem($id=null)
    {
        $CI =& get_instance();
        $CI->load->model('Investor_sportbooks_model');

        $result = null;
        $row = $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'id' => $id
            ))
            ->get()->result_array();
        if(count($row))
        {
            $result = $row[0];
            $investorId = $result['id'];
            $result['sportbooks'] = $CI->Investor_sportbooks_model->getListByInvestorId($investorId);
            $result['current_balance'] = 0;
            foreach ($result['sportbooks'] as $sportbook_item) {
                $result['current_balance'] += $sportbook_item['current_balance'];
            }
        }
        return $result;
    }

    private function formatRelationItem($investor_id,$data){
        $result = array(
            'investor_id' => $investor_id,
        );

        foreach ($this->relationDbColumns as $column) {
            if(!isset($data->$column))
                continue;
            if($column == 'date_opened')
                $value = date_format(date_create($data->$column),"Y-m-d");
            else
                $value = $data->$column;
            $result[$column] = $value;
        }
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
        $investor_id = $this->db->insert_id();

        $addSportbookDate = [];
        $sportbook_data = json_decode($data['sportbook_data']);
        foreach ($sportbook_data as $sportbook_item) {
            $addSportbookDate[] = $this->formatRelationItem($investor_id, $sportbook_item);
        }
        if(count($addSportbookDate))
            $this->db->insert_batch($this->relationTableName, $addSportbookDate);
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

        $addSportbookDate = [];
        $sportbook_data = json_decode($data['sportbook_data']);

        $validIds = [];
        foreach ($sportbook_data as $sportbook_item) {
            $rowData = $this->formatRelationItem($id, $sportbook_item);
            if($sportbook_item->relation_id == -1)
            {
                $addSportbookDate[] = $rowData;
            }else{
                $validIds[] = $sportbook_item->relation_id;
                $updateSportbookData = $rowData;
                $this->db->where(array(
                    'id' => $sportbook_item->relation_id
                ))->update($this->relationTableName,$updateSportbookData);
            }
        }
        if(count($validIds))
        {
            $this->db->where('investor_id',$id)
            ->where_not_in('id', $validIds)
            ->delete($this->relationTableName);
        }else{
            $this->db->where('investor_id',$id)
            ->delete($this->relationTableName);
        }

        if(count($addSportbookDate)){
            $this->db->insert_batch($this->relationTableName, $addSportbookDate);
        }

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
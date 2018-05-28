<?php
class Users_model extends CI_Model {
    private $tableName = 'users';

    public function getAll(){
        $this->db->select('id, name')
            ->from($this->tableName)
            ->order_by('name','asc');
        $result = $this->db->get()->result_array();;
        return $result;
    }

    public function getByID($id){
        $this->db->select('id, name')
            ->from($this->tableName)
            ->where('id',$id);
        $rows = $this->db->get()->result_array();
        $result = null;
        if(count($rows))
            $result = $rows[0];
        return $result;   
    }

    public function getUserGroup($userId){
        $groupId = null;
        $this->db->select('group_id')
            ->from($this->tableName)
            ->where('id',$userId);

        $rows = $this->db->get()->result_array();
        if(count($rows))
            $groupId = $rows[0]['group_id'];
        return $groupId;
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
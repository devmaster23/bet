<?php
class Groups_model extends CI_Model {
    private $tableName = 'groups';

    public function getAll(){
        $this->db->select('id, name')
            ->from('groups')
            ->order_by('name','asc');
        $result = $this->db->get()->result_array();;
        return $result;
    }

    public function getByID($id){
        $this->db->select('id, name')
            ->from('groups')
            ->where('id',$id);
        $rows = $this->db->get()->result_array();
        $result = null;
        if(count($rows))
            $result = $rows[0];
        return $result;   
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
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

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
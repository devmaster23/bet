<?php
class Users_model extends CI_Model {
    private $tableName = 'users';

    public function getAll(){
        $this->db->select('id, name')
            ->from('users')
            ->order_by('name','asc');
        $result = $this->db->get()->result_array();;
        return $result;
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
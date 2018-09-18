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

    public function getAllWithOpenStatus($betday){
        $this->db->select('G.id, G.name, S.is_open')
            ->from('groups as G')
            ->join('settings as S', 'S.betday = '.$betday.' AND S.type = 1 AND S.groupuser_id = G.id', 'left')
            ->order_by('G.name','asc');
        $result = $this->db->get()->result_array();
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
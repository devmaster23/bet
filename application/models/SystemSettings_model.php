<?php
class SystemSettings_model extends CI_Model {
    private $tableName = 'system_settings';

    private $CI;

    private $dbColums = array(
        'key',
        'value'
    );


    function __construct()
    {
        $this->CI =& get_instance();
    }

    public function updateBetDay($betday){
        $data = array(
            'value'     => $betday
        );
        $this->db->where(array(
            'key'        => 'betday',
        ));
        $this->db->update($this->tableName, $data);
        return true;
    }

    public function getBetDay()
    {
        $result = 0;
        $query = $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'key'        => 'betday',
            ));
        $rows = $query->get()->result_array();
        if(count($rows))
            $result = $rows[0]['value'];
        return $result;
    }

    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
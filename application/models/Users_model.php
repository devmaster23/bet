<?php
class Users_model extends CI_Model {
    private $tableName = 'users';
    private $pageURL = 'users';

    private $dbColumns = array(
        'name',
        'username',
        'email',
        'password',
        'user_type',
    );

    public function getAll(){
        $this->db->select('id, name')
            ->from($this->tableName)
            ->order_by('name','asc');
        $result = $this->db->get()->result_array();;
        return $result;
    }

    public function getList($value='')
    {
        $result = [];
        $this->db->select('*')
            ->from($this->tableName)
            ->where('user_type !=', '0')
            ->order_by('name','asc');

        $rows = $this->db->get()->result_array();;

        foreach ($rows as $key => $item) {
            $tmpArr = $item;
            $tmpArr['custom_action'] = "<div class='action-div' data-id='".$item['id']."'><a class='edit' href='/".$this->pageURL."/edit?id=".$item['id']."'>Edit</i></a><a class='delete'>Delete</a></div>";
            $userType = 'Order Entry';
            switch ($item['user_type']) {
                case '1':
                    $userType = 'Order Entry';
                    break;
                case '2':
                default:
                    $userType = 'Game Entry';
                    break;
            }
            $tmpArr['type'] = $userType;
            $tmpArr['index'] = $key+1;
            $result[] = $tmpArr;
        }
        return $result;

        
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
            {
                if($dbColumn == 'password' && $data[$dbColumn] == '')
                    continue;
                if($dbColumn == 'password')
                    $updateDate[$dbColumn] = md5($data[$dbColumn]);
                else
                    $updateDate[$dbColumn] = $data[$dbColumn];
            }
        }
        $this->db->where(array(
            'id' => $id
        ))->update($this->tableName,$updateDate);

        return true;
    }

    public function getByID($id){
        $this->db->select('*')
            ->from($this->tableName)
            ->where('id',$id);
        $rows = $this->db->get()->result_array();
        $result = null;
        if(count($rows))
        {
            $result = $rows[0];
            $userRole = 'Administrator';
            switch ($result['user_type']) {
                case '0':
                    $userRole = 'Administrator';
                    break;
                case '1':
                    $userRole = 'Order Entry';
                    break;
                case '2':
                default:
                    $userRole = 'Game Entry';
                    # code...
                    break;
            }
            $result['user_role'] = $userRole;
        }
        return $result;   
    }

    
    public function register_user($user){
      $this->db->insert($this->tableName, $this->tableName); 
    }

    public function login_user($username,$pass){
 
        $this->db->select('*')
            ->from($this->tableName)
            ->where(array(
                'username' => $username,
                'password' => md5($pass)

            ));

        if($query=$this->db->get())
        {
          return $query->row_array();
        }
        else{
            return false;
        }
    }

    public function email_check($email){
 
        $this->db->select('*');
        $this->db->from($this->tableName);
        $this->db->where('user_email',$email);
        $query=$this->db->get();

        if($query->num_rows()>0){
            return false;
        }else{
            return true;
        }
     
    }


    public function q($sql) {
        $result = $this->db->query($sql);
    }
}
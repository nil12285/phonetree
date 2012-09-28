<?php

class User_model extends CI_Model {
    
    
    public $validation_rule = array(
                array(
                     'field'   => 'first_name',
                     'label'   => 'First Name',
                     'rules'   => 'required|alpha'
                  ),
                array(
                     'field'   => 'last_name',
                     'label'   => 'Last Name',
                     'rules'   => 'required|alpha'
                  ),
                array(
                     'field'   => 'password',
                     'label'   => 'Password',
                     'rules'   => 'required|matches[passconf]|md5'
                  ),
                array(
                     'field'   => 'passconf',
                     'label'   => 'Password Confirmation',
                     'rules'   => 'required'
                  ),   
                array(
                     'field'   => 'email',
                     'label'   => 'Email',
                     'rules'   => 'required|valid_email'
                  )
            );


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    
    
    /**
     * @name addUser
     * @param Array $userData;
     * @return Boolean;
     */
    function addUser($userData)
    {
        if(empty($userData))
            return false;
            
        $userData['user_created'] = date('Y-m-d H:i:s');
        $userData['last_login'] = date('Y-m-d H:i:s');
        $userData['password'] = md5($userData['password']);        

        $rs = $this->db->insert('Users', $userData);
        if($rs)
            return $this->db->insert_id();
        else
            return $rs;
    }
    
    
    /**
     * @name getUserByEmail
     * @param String $email;
     * @return Array $res | false;
     */
    function getUserByEmail($email)
    {
        if(empty($email))
            return false;
            
        $res = $this->db->get_where('Users',array('email'=>$email));
        if($res->num_rows() > 0) {
            $result = $res->result();
            return $result[0];
        }
        else 
            return false;
    }
    
    
    /**
     * @name getUserById
     * @param Integer $user_id;
     * @return Array $res | false;
     */
    function getUserById($user_id)
    {
        if(empty($user_id))
            return false;
            
        $res = $this->db->get_where('Users',array('user_id'=>$user_id));
        if($res->num_rows() > 0) {
            $result = $res->result();
            return $result[0];
        }
        else 
            return false;
    }
    
    
    /**
     * @name isUserIdExist
     * @param Integer $user_id;
     * @return Boolean;
     **/
    function isUserIdExist($user_id)
    {
        if(empty($user_id))
            return false;
        
        $this->db->select('user_id');
        $this->db->from('Users');
        $this->db->where('user_id',$user_id);        
        $res = $this->db->get();
        
        if($res->num_rows() > 0)
            return true;
        else 
            return false;
    }
    
    
    /**
     * @name getUserByEmail
     * @param String $email; String $pass
     * @return Array $user | false;
     */
    function authenticateUser($email,$password)
    {
        if(empty($email) || empty($password))
            return false;
            
        $res = $this->db->get_where('Users',array('email'=>$email,'password'=>md5($password)));
                
        if($res->num_rows() > 0) {
            $result = $res->result();
            return $result[0];
        }
        else 
            return false;
    }
    
}
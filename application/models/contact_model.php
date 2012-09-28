<?php

class Contact_model extends CI_Model {
    
    public $validation_rule = array(
                array(
                     'field'   => 'user_id',
                     'label'   => 'User Id',
                     'rules'   => 'required'
                  ),
                array(
                     'field'   => 'contact_name',
                     'label'   => 'Contact Name',
                     'rules'   => 'required'
                  ),
                array(
                     'field'   => 'phone',
                     'label'   => 'Phone  Number',
                     'rules'   => 'required'
                  )                
            );

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    
    /**
     * @name addContact
     * @param Array $contactData;
     * @return Boolean;
     **/
    function addContact($contactData)
    {
        if(empty($contactData) || empty($contactData['user_id']))
            return false;
            
        $contactData['created'] = date('Y-m-d H:i:s');

        $rs = $this->db->insert('Contacts', $contactData);
        if($rs)
            return $this->db->insert_id();
        else
            return $rs;
    }
    
    
    
    /**
     * @name updateContact
     * @param integer $contact_id;
     * @param Array $contactData;
     * @return Boolean;
     **/
    function updateContact($contact_id,$contactData)
    {
        if(empty($contactData) || empty($contactData['user_id']) || empty($contact_id))
            return false;
        
        $user_id = $contactData['user_id'];
        unset($contactData['user_id']);

        //echo $this->db->update_string('Contacts', $contactData, array('contact_id'=>$contact_id, 'user_id'=>$user_id));
        
        $rs = $this->db->update('Contacts', $contactData, array('contact_id'=>$contact_id, 'user_id'=>$user_id));
        return $rs;
    }
    
    
    /**
     * @name getUserContacts
     * @param Integer $user_id;
     * @return Array $contacts;
     **/
    function getUserContacts($user_id)
    {
        if(empty($user_id) || !is_numeric($user_id))
            return false;
            
        $contacts = array();
        
        $res = $this->db->get_where('Contacts',array('user_id'=>$user_id));
        if($res->num_rows() > 0)
            $contacts = $res->result("array");

        return $contacts;        
    }
    
    
    
    /**
     * @name getContactById
     * @param Integer $contact_id;
     * @return Array $contact;
     **/
    function getContactById($contact_id)
    {
        if(empty($contact_id) || !is_numeric($contact_id))
            return false;
            
        $contact = array();
        
        $res = $this->db->get_where('Contacts',array('contact_id'=>$contact_id));
        if($res->num_rows() > 0)
            $contact = $res->result("array");

        return $contact[0];        
    }
    
    
    /**
     * @name deleteContactById
     * @param Integer $contact_id;
     * @return Boolean;
     **/
    function deleteContactById($contact_id)
    {
        if(empty($contact_id) || !is_numeric($contact_id))
            return false;
            
        $res = $this->db->delete('Contacts',array('contact_id'=>$contact_id),1);
        return $res;
    }
    
    
    function syncContact(){}
    
}
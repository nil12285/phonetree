<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Users extends CI_Controller {
    
    protected $email;
    
    function __construct(){
        parent::__construct();
        $this->load->helper('url');
        $this->email = $this->session->userdata('email');
        if(empty($this->email)) {
            redirect('/welcome', 'refresh');
        }
    }
    
    
    function contacts($ajax=false)
    {
        $this->load->model('Contact_model');
        $contacts = $this->Contact_model->getUserContacts($this->session->userdata('user_id'));
        
        $this->load->library('jquerypager');
        #Define header
        $header = array(
                            'contact_id' => "Id",
                            'contact_name' => "Name",
                            'phone' => "Phone",
                            'extensions' => 'Extensions',
                    );
        
        #Define Link
        $link = array(
                        array(
                            'title' => 'View',
                            'text' => 'view',
                            'target' => 'overlay',
                            'link' => '/admin/users/viewContact',
                            'qstring' => array('contact_id')
                        ),
                        array(
                            'title' => 'Delete',
                            'text' => 'delete',
                            'target' => 'confirm',
                            'link' => '/admin/users/deleteContact',
                            'refresh-url' => '/admin/users/contacts/true',                            
                            'qstring' => array('contact_id')
                        )
                        
                    );
        
        #Set header and users data
        $this->jquerypager->setPager("idUsersContacts",10,$header,$contacts);
        
        #set Sort Order and some other gui param
        $this->jquerypager->setJqPagerParameter('false',array(0=>'desc'),array(),true,true);
        
        //$this->jquerypager->_setOverlayDialogParm();
        
        #set Link data
        $this->jquerypager->addLink($link);
        $jqpager = $this->jquerypager->getHtml();
        if($ajax == 'true') {
            echo $jqpager; exit;
        }
        
        $data['jqpager'] = $jqpager;
        $data['email'] = $this->email;
        
        $this->load->view('admin_sample',$data);
        
    }
            
    
    
    function viewContact($contact_id)
    {
        $this->load->model('Contact_model');
        $contact = $this->Contact_model->getContactById($contact_id);
        $this->load->view('contact_sample',$contact);
    }
    
    
    function deleteContact($contact_id)
    {
        echo 'true';
    }
    
    
    function phpinfo()
    {
        phpinfo();
    }
}

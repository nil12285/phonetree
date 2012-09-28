<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{	     
	   $this->load->view('welcome_message');
	}
    
    
    public function login()
    {
        //$this->print_r($this->input->post());
        $email      = $this->input->post('email');
        $password   = $this->input->post('password');
        
        $this->load->model('User_model');
        $user = $this->User_model->authenticateUser($email,$password);
        
        if(!empty($user)) {
            
            $this->session->set_userdata($user);
            sleep(2);
            $this->load->helper('url');
            redirect('/admin/users/contacts');
            
            //$this->response(array('user'=>$um),200,'Login Success');
            
        } else {
            $data['error'] = 'the email or password you entered is incorrect';
            $this->load->view('welcome_message',$data);            
            //$this->response(array('error_messages'=>$em),200,'the email or password you entered is incorrect',true);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
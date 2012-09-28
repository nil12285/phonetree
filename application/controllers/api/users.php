<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include BASEPATH.'core/REST_Controller.php';
class Users extends REST_Controller {
    
    
    public function register_post()
    {
        #Get User Object [$this->User_model]
        $this->load->model('User_model');
        
        if(!empty($this->_post_args) && !empty($this->_post_args['email'])) {
            
                #validate data
                $this->load->library('form_validation');
                
                #Get Validation Rules
                $uValodationRule = $this->User_model->validation_rule;
                
                #Set Validation Rule
                $this->form_validation->set_rules($uValodationRule);
                
                #validate
                $validate = $this->form_validation->run();
                if(!$validate) {
                    #get error messages
                    $error_messages = $this->form_validation->_error_array;
                    foreach($error_messages as $k=>$e) {
                        $em[] = array('field' => $k, 'error' => $e); 
                    }
                    
                    $this->response(array('error_messages'=>$em),200,'Registration Fail',true);
                    
                } else {
                    $isUser = $this->User_model->getUserByEmail($this->_post_args['email']);
                    
                    if(empty($isUser)) {
                        
                        unset($this->_post_args['passconf']);
                        
                        #add User                    
                        $user_id = $this->User_model->addUser($this->_post_args);
                                            
                        if($user_id) {
                            $this->response(array('user_id'=>$user_id),200,'Thanks you are registered');
                        } else {                    
                            $this->response(array(),200,'Registration Fail',true);
                        }
                    } else {
                        $em[] = array(
                                        'field'=>'email',
                                        'error'=>'This e-mail address already exists in system'
                                    );
                                        
                        $this->response(array('error_messages'=>$em),200,'Registration Fail',true);
                    }
                }            
                        
        } else {            
            $this->response(array(),200,'In valid param',true);
        }        
    }
    
    
    public function login_post()
    {
        $email      = $this->_post_args['email'];
        $password   = $this->_post_args['password'];
        
        $this->load->model('User_model');
        $user = $this->User_model->authenticateUser($email,$password);
        
        if(!empty($user)) {
            
            foreach($user as $k=>$v) {
                $um[] = array('field' => $k, 'value' => $v); 
            }
            
            $this->response(array('user'=>$um),200,'Login Success');
            
        } else {
            $em[] = array(
                            'field'=>'email',
                            'error'=>'the email or password you entered is incorrect'
                        );
            $em[] = array(
                            'field'=>'password',
                            'error'=>'the email or password you entered is incorrect'
                        );
                        
            $this->response(array('error_messages'=>$em),200,'the email or password you entered is incorrect',true);
        }
    }
    
    
}
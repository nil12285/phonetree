<?php 

include BASEPATH.'core/REST_Controller.php';
class Contacts extends REST_Controller {
    
    function user_contact_get()
    {
        $user_id = $this->_get_args['user_id'];
        
        $this->load->model('Contact_model');
        $contacts = $this->Contact_model->getUserContacts($user_id);

        if($contacts) {
            foreach($contacts as $key=>$val) {
                unset($contacts[$key]['user_id']);
                unset($contacts[$key]['created']);
                unset($contacts[$key]['updated']);
            }
            $this->response(array('contacts'=>$contacts),200,"Contacts for user_id : $user_id");
        } else {
            $this->response(array(),200,'No Contact found for user_id',true);
        }
    }
    
    
    function upsert_post()
    {
        $this->load->model('User_model');
        $this->load->model('Contact_model');
        $data = $this->_post_args;

        if(empty($data['user_id']) || 
            $this->User_model->isUserIdExist($data['user_id']) == false) {
                $this->response(array(),200,'Invalid Request',true);
        }
        
        #validate data
        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->Contact_model->validation_rule);
        
        $validate = $this->form_validation->run();
        if(!$validate) {
            #get error messages
            $error_messages = $this->form_validation->_error_array;
            foreach($error_messages as $k=>$e) {
                $em[] = array('field' => $k, 'error' => $e);
            }
            $this->response(array('error_messages'=>$em),200,'Fail to save contact',true);
        } else {            
            #check if contact_id passed
            $contact_id = $data['contact_id'];
            if(!empty($contact_id) && is_numeric($contact_id) && $contact_id != 0) {
                unset($data['contact_id']);                
                $rs = $this->Contact_model->updateContact($contact_id,$data);
                if(!$rs)
                    $contact_id = false;
            } else {
                $contact_id = $this->Contact_model->addContact($data);
            }

            if($contact_id) {
                $this->response(array('contact_id'=>$contact_id),200,'Contact saved successfully');
            } else {
                $this->response(array(),200,'Fail to save contact',true);
            }
        }
    }
    
    
    
    function delete_contacts_post()
    {
        $this->load->model('Contact_model');
        $data = $this->_post_args;
        $contact_id = $data['contact_id'];
        $user_id    = $data['user_id'];
        
        if(!empty($contact_id) || !empty($user_id)) {
            $rs = $this->Contact_model->deleteContactById($contact_id);
            if($rs) {
                $this->response(array(),200,'Contact deleted successfully');
            }
        }
    }
}    
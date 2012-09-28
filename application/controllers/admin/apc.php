<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Apc extends CI_Controller {
    
    protected $email;
    
    function __construct(){
        parent::__construct();
    }
    
    function index()
    {
        $this->load->helper('apc');
    }
    
}    
<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jul 16 2020
 *  File : login_proc.php
 *******************************************/
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login_proc extends SS_Controller
{

    public function __construct()
    {
        parent::__construct(0);
        $this->load->library('session');

        //Cek access
        if($_SESSION['userid'] == "") {
            show_error(lang('No access to this page'), 200);
        }
    }

    public function passwd_challenge() {
        $data = array();
        $data['api_url']    = $this->config->item('api');
        $data['UserId'] = $_SESSION['userid'];

        $this->load->view('common_header_front');
        $this->load->view('login/passwd_challenge', $data);
        $this->load->view('common_footer_front');
    }

    public function after_login_tor() {
        $data = array();
        $data['api_url'] = $this->config->item('api');
        $data['UserId'] = $_SESSION['userid'];

        $this->load->view('common_header_front');
        $this->load->view('login/after_login_tor', $data);
        $this->load->view('common_footer_front');
    }

}
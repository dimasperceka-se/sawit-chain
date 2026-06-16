<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Coop_print_statement extends CI_Controller {

    public function __construct() {
        parent::__construct();
    } 
    
    function index()
    {
        echo 'index';
    }
    
    function printout($memberID)
    {
       $service_url = $this->config->item('api').'/member/coop_member_balance_book';
       
       $curl = curl_init($service_url);
       $curl_post_data = array(
            "id" => $memberID
       );
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($curl, CURLOPT_POST, true);
       curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
       $curl_response = curl_exec($curl);
       curl_close($curl);
       // var_dump($curl_response);
       $dt = json_decode($curl_response);
       $curl_post_data['data'] = $dt->data;
       // $curl_post_data['memberData'] = $dt->memberData;
       
       $this->load->view('print_balance_statement',$dt);
    }
}
?>
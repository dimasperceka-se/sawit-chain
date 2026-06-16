<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MobileTest extends CIUnit_Framework_TestCase
{   

    public function testLoginOutput()
    {
        //test array hasil login
        $CI =& get_instance();
        $CI->load->model('mauth','_model');

        $login = $CI->_model->doLoginTraceability('trader1','trader1');

        //cek kelengkapan array
        $keys = array('Name','RealName','PartnerID','SupplyChainID','OrgID','OrgName','OrgType','Village','Area','Destination','token');

        foreach($keys as $val) {
            $this->assertArrayHasKey($val, $login); //cari berdasarkan key
            $this->assertNotNull($login[$val]); //output tidak boleh null
        }
    }
}
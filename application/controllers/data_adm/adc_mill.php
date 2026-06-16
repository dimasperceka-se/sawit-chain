<?php

/**
 * @Author: nikolius
 * @Date:   2017-10-11 16:33:18
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-11 16:33:46
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Adc_mill extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js']     = 'data_adm_adc_mill';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'sys_date' => date('Ymd')
        );
        $this->LoadView($data);
    }

}
?>
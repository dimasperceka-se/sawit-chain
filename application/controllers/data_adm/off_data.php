<?php
/**
 * @Author: nikolius
 * @Date:   2017-04-06 17:48:24
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Off_data extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js']     = 'data_adm_off_data';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'sys_date' => date('Ymd'),
            'act_index'   => !$this->system->CekAksi('index'),
            'act_offline_data_generate' => $this->system->CekAksi('offline_data_generate'),
            'act_offline_metadata_generate' => $this->system->CekAksi('offline_metadata_generate'),
            'act_offline_metadata_generate_devel' => $this->system->CekAksi('offline_metadata_generate_devel'),
            'act_offline_data_download' => $this->system->CekAksi('offline_data_download')
        );
        $this->LoadView($data);
    }

}
?>
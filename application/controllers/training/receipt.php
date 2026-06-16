<?php
/**
 * @Author: nikolius
 * @Date:   2017-01-12 13:59:32
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Receipt extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js']     = 'training/receipt';
        $api            = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';

        $data['action'] = array(
            'url_cetak' => $api.'/training_receipt/',
            'act_add'    => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
            'act_receipt_activity' => !$this->system->CekAksi('receipt_activity'),
            'act_receipt_participant' => !$this->system->CekAksi('receipt_participant')
        );
        $this->LoadView($data, 'common_content_region');
    }
}
?>
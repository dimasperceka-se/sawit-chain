<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-30 14:59:27
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Receipt_setting extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js']     = 'training/receipt_setting';
        $api            = $this->config->item('api');
        $prov = !empty($this->input->get('prov'))?$this->input->get('prov'):'';
        $dist = !empty($this->input->get('dist'))?$this->input->get('dist'):'';

        $data['action'] = array(
            'act_add'    => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
            'act_create_receipt' => !$this->system->CekAksi('tanda_terima_create')
        );
        $this->LoadView($data, 'common_content_region');
    }
}
?>
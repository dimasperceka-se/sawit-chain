<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-29 15:01:36
 */
class Goods extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js']     = 'basic/goods';
        $api            = $this->config->item('api');
        $data['action'] = array(
            'act_add'    => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'));
        $this->LoadView($data);
    }
}

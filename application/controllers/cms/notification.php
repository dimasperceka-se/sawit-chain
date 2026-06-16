<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notification extends SS_Controller
{

    public function __construct()
    {
        parent::__construct(1);
    }

    public function index($id = '')
    {
        $data['js'] = 'notification';        
        $api        = $this->config->item('api');

        $Keyword = $this->input->get('Keyword')!='' && $this->input->get('Keyword')!=false && $this->input->get('false') ? $this->input->get('Keyword') : '';

        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url'),
            'base_url'                              => base_url(),            
            'act_add'                               => !$this->system->CekAksi('add'),
            'act_update'                            => !$this->system->CekAksi('update'),
            'act_delete'                            => !$this->system->CekAksi('delete'),
            'keyword'                               => $Keyword,
        );
        $this->LoadView($data, 'common_content');
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Useracc_management extends SS_Controller {

    public function __construct() {
       parent::__construct();
    }
 
    public function index() {
        $data['js'] = 'farmcloud/useracc_management';
        $api = $this->config->item('api');
        $url_awss3 = $this->config->item('CTCDN');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            'base_url' => base_url(),
            'id_admin' => (int) $_SESSION['is_admin'],
            'act_add' => !$this->system->CekAksi('add'),
            'act_update' => !$this->system->CekAksi('update'),
            'act_delete' => !$this->system->CekAksi('delete'),
            'act_export' => !$this->system->CekAksi('export'),
            'sess_username' => $_SESSION['username']
        );

        $this->LoadView($data);
    }

}
?>
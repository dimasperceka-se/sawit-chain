<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download_kml extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'data_adm/download_kml';
        $api = $this->config->item('api');
        $data['action'] = array(
            'url'           => base_url(),
            'api_base_url'  => $this->config->item('api_base_url'),
            'userid'        => $_SESSION['userid'],
            'base_url'      => base_url(),
            'api' => $api,
            'show_partner' => $_SESSION['PartnerID']==1 ? true : false
        );

        $this->LoadView($data);
    }

}


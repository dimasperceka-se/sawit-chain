<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_farm_polygon extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'spatial_tools/upload_farm_polygon';
        $api = $this->config->item('api');
        $data['action'] = array(
            'url'           => base_url(),
            'api_base_url'  => $this->config->item('api_base_url'),
            'userid'        => $_SESSION['userid'],
            'base_url'      => base_url()
        );
        $data['style'] = "
        .error .x-grid-cell { 
            background-color: #F2DEDE;
            color: #333;
        }           
        .no-error .x-grid-cell { 
            background-color: #FFFFFF;
            color: #333;
        }
        #right-button {
            left: auto !important;
            right: 10px !important;           
                       
        }
        #info-button {
            background-color: #95130b;
            padding: 5px;
            border-radius: 50%;
            opacity: 0.3;
        }

        #info-button:hover {
            opacity: 1;
        }
        ";        
        $this->LoadView($data);
    }

}

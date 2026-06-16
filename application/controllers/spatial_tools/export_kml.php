<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Export_kml extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['js'] = 'spatial_tools/export_kml';
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
        .x-grid-row-selected .x-grid-td  {
            background-color: #95130b !important;
            box-shadow: none !important;                        
        }

        .x-grid-row-selected .x-grid-cell-inner{
            color: white !important;
        }
        
        #asaveButtonExcel{
            background-image:url(./images/icons/maps/xls-download.png);
            background-size: cover;
            border-style: none;
            opacity: 0.3;
        }
        #asaveButtonExcel:hover{            
            opacity: 1;
        }
        #buttonKML{
            background-image:url(./images/icons/maps/kml-download.png);
            background-size: cover;
            border-style: none;
            opacity: 0.3;
            width : 24px;
            height: 24px;
            margin-top: 3px;
        }
        #buttonKML:hover{            
            opacity: 1;
        }
        .x-btn-default-toolbar-small .x-btn-split-right {
            padding-right: 40px;
            display: none;
        }
        .custom-gridPerformance .x-column-header-inner .x-column-header-text {
            white-space: normal;
            
        }
        .x-column-header{
            border : none;
            background-color: #fff;
        }
        ";        
        $this->LoadView($data);
        
    }

}

<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile_Monitoring extends REST_Controller {

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct() {
        parent::__construct();
        $this->load->model('monitoring/mlocation','_location');
        $this->load->model('monitoring/mmonitoring','_monitoring');
    }

    public function get_by_district_get() {
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        
        $start = 0;
        $limit = 500;
        $district = false;
        $current = 1;
        $total = 0;
        $obj_type = false; //string ('farmer','cpg','demoplot','nursery','compost','cooperatives','warehouse','trader','sce','location')

        if($this->get('start')){
          $start = $this->get('start');
        }

        if($this->get('object_type') && strlen($this->get('object_type')) > 0){
          $obj_type = $this->get('object_type');
        }

        if($this->get('district')){
          $district = $this->get('district');
        }

        $objects = array();

        if($obj_type) {

          switch ($obj_type) {
            case 'location':
              $output = $this->_location->getLocationByUser($start,$this->user,$district);
              $results = array(
                'location_list' => array('location' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'farmer':
              $output = $this->_location->getFarmerByUser($start,$this->user,$district);
              $results = array(
                'farmer_list' => array('farmer' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'compost':
              $output = $this->_location->getCompostByUser($start,$this->user,$district);
              $results = array(
                'compost_list' => array('compost' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'nursery':
              $output = $this->_location->getNurseryByUser($start,$this->user,$district);
              $results = array(
                'nursery_list' => array('nusery' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'cpg':
              $output = $this->_location->getCpgByUser($start,$this->user,$district);
              $results = array(
                'cpg_list' => array('cpg' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'trader':
              $output = $this->_location->getTraderByUser($start,$this->user,$district);
              $results = array(
                'trader_list' => array('trader' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'demoplot':
              $output = $this->_location->getDemoplotByUser($start,$this->user,$district);
              $results = array(
                'demoplot_list' => array('demoplot' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'cooperatives':
              $output = $this->_location->getCooperativesByUser($start,$this->user,$district);
              $results = array(
                'cooperatives_list' => array('cooperatives' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'sce':
              $output = $this->_location->getSceByUser($start,$this->user,$district);
              $results = array(
                'sce_list' => array('sce' => $output['details'], 'total' => $output['total']),
              );
              break;
            case 'warehouse':
              $output = $this->_location->getWarehouseByUser($start,$this->user,$district);
              $results = array(
                'warehouse_list' => array('warehouse' => $output['details'], 'total' => $output['total']),
              );
              break;

          }
        }
        /*
        $results = array(
            'location_list'      => array('location'    => $objects,                'total' => count($objects)),
            'farmer_list'        => array('farmer'      => $farmers['details'],     'total' => $farmers['total']),
            'cpg_list'           => array('cpg'         => $cpgs['details'],        'total' => $cpgs['total']),
            'nursery_list'       => array('nursery'     => $nurseries['details'],   'total' => $nurseries['total']),
            'compost_list'       => array('compost'     => $composts['details'],    'total' => $composts['total']),
            'demoplot_list'      => array('demoplot'    => $demoplots['details'],   'total' => $demoplots['total']),
            'cooperatives_list'  => array('cooperatives'=> $coops['details'],       'total' => $coops['total']),
            'warehouse_list'     => array('warehouse'   => $warehouses['details'],  'total' => $warehouses['total']),
            'trader_list'        => array('trader'      => $traders['details'],     'total' => $traders['total']),
            'sce_list'           => array('sce'         => $sces['details'],        'total' => $sces['total']),
        );
        */
        $response = array('success' => true, 'message' => 'Berhasil', 'location_results' => $results);

        $data = json_encode($response);

        $name = 'location.json';

        $this->load->helper('download');

        force_download($name, $data);

    }

    function fetch_monitoring_get() {

        $monitoring = $this->_monitoring->getDataMonitoring($this->district);
        $results = array(
            'monitoring_list'   => array('monitoring'   => $monitoring['details'], 'total' => $monitoring['total'])
        );

        $response = array('success' => true, 'message' => 'Berhasil', 'monitoring_results' => $results);

        return $this->response($response,200);
    }

    function sync_monitoring_post() {

        $data = $this->post('monitoring_list');

        $this->load->helper('file');

        $this->_monitoring->syncMonitoring($data['monitoring']);

        return $this->response($data,200);
    }

    function sync_upload_post() {
        $headers = apache_request_headers();

        //$monitoring_id = $this->post('monitoringid');
        $name = $this->post('name');
        $path = 'images/photo_activity';
        //$files = $_FILES;
        $files = base64_decode($this->post('image'));

        $this->load->helper('file');
        if ( ! write_file("images/photo_activity/" . $name, $files))
        {
             echo 'Unable to write the file';die;
        }
        else
        {
             $this->response(array('success' => true));
        }
        //Allowed file type
        $allowed = array('image/png','image/jpeg','image/gif','image/x-png');
        $max_size = 1024000;

        /*
        foreach($files as $key => $values) {
            if(!$values['error']){
                if(in_array($values['type'], $allowed)){
                    if($values['size'] <= $max_size){
                        $name = $values["name"];
                        if (file_exists("images/photo_activity/" . $name)) {
                            die("{'success': false, 'error': '" . $name . " already exists.'}");
                        } else {

                            if(move_uploaded_file($values["tmp_name"], "images/photo_activity/" . $name)) {
                                $this->response(array('success' => true));
                            } else {
                                $this->response(array('error' => error_get_last()),301);die;
                            }

                            //$foto = $this->_monitoring->create_foto($monitoring_id,$title,$path,$name,$values['type'],$values['size']);
                            /*
                            if ($foto) {
                                $this->response($foto, 200);
                            } else {
                                $this->response(array('error' => 'Gagal koneksi ke database'), 401);
                            }

                        }
                    } else {
                        $this->response(array('error' => 'Ukuran file melebihi ketentuan (1MB).'),401);
                    }
                } else {
                    $this->response(array('error' => 'Jenis file tidak diijinkan (PNG,JPEG,GIF).'),401);
                }
            }
        } */
        return $this->response(array('success' => true),200);
    }

    function sync_upload_base64_post() {
        $monitoring_id = $this->post('monitoringid');
        $title = $this->post('title');
        $path = 'images/photo_activity';

        $files = $_FILES;

        //Allowed file type
        $allowed = array('image/png','image/jpeg','image/gif','image/x-png');
        $max_size = 1024000;

        foreach($files as $key => $values) {
            if(!$values['error']){
                if(in_array($values['type'], $allowed)){
                    if($values['size'] <= $max_size){
                        $name = date('Ymdhis').'_'.$values["name"];
                        if (file_exists("images/photo_activity/" . $name)) {
                            die("{'success': false, 'error': '" . $name . " already exists.'}");
                        } else {

                            if(move_uploaded_file($values["tmp_name"], "images/photo_activity/" . $name)) {

                            } else {
                                $this->response(array('error' => error_get_last()),301);die;
                            }

                            $foto = $this->_monitoring->create_foto($monitoring_id,$title,$path,$name,$values['type'],$values['size']);

                            if ($foto) {
                                $this->response($foto, 200);
                            } else {
                                $this->response(array('error' => 'Gagal koneksi ke database'), 401);
                            }
                        }
                    } else {
                        $this->response(array('error' => 'Ukuran file melebihi ketentuan (1MB).'),401);
                    }
                } else {
                    $this->response(array('error' => 'Jenis file tidak diijinkan (PNG,JPEG,GIF).'),401);
                }
            }
        }
        return $this->response(array('success' => true),200);
    }

}

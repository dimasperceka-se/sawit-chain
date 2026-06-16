<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dhis extends REST_Controller {

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct() {
        parent::__construct();
        $this->load->model('dhis/mdsync', '_dsync');
    }

    public function get_data_get() {

        $data = $this->_dsync->getData($this->get('start'), $this->get('limit'), false, $this->get('district'), $this->get('prod'), $this->get('landsize'), $this->get('ycert'), $this->get('name'), $this->get('nameop'), $this->get('prodop'), $this->get('landsizeop'));
        if ($data) {
            $this->response($data, 200);
        }

        $this->response(array('error' => 'Couldn\'t find any Data!'), 404);
    }

    public function sync_data_post($farmerID = false) {

        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $ids = json_decode($this->post('data'));

        if ($farmerID) {
            $ids = $farmerID;
        }

        $data = $this->_dsync->syncData($ids, 0);
        die;
        if ($data) {
            $this->response($data, 200);
        }

        $this->response(array('error' => 'Couldn\'t find any Data!'), 404);
    }

    public function sync_all_post() {

        $data = $this->_dsync->getData($this->post('start'), $this->post('limit'), true, $this->post('district'), $this->post('prod'), $this->post('landsize'), $this->post('ycert'), $this->post('name'), $this->post('nameop'), $this->post('prodop'), $this->post('landsizeop'));

        $ids = array();
        if ($data['total'] > 0) {

            foreach ($data['data'] as $keys => $values) {
                array_push($ids, $values['FarmerID']);
            }
            //var_dump(count($ids));die;
            //kirim per kloter 100
            $batches = ceil(count($ids) / 100);

            $loads = array();

            $ii = 1;
            $pp = array();

            for ($a = 0; $a < $batches; $a++) {
                $lists = array();
                for ($ii; $ii < 100 * ($a + 1); $ii++) {
                    if ($ii < count($ids)) {
                        array_push($lists, $ids[$ii]);
                    }
                }
                //var_dump(json_encode($lists));die;
                $this->_dsync->syncData($lists, $a);
                //array_push($pp,$lists);
            }
            //echo json_encode($pp);die;
            //if ($execute) {
            //    $this->response($execute, 200);
            //}
        }

        $this->response(array('error' => 'Couldn\'t find any Data!'), 404);
    }

    public function synccocoatracedhis_get() {

        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        
        $onlyNew = $this->get('onlyNew'); // skip yang udah punya uid
        $district = (int) $this->get('district'); // ambil farmer by district
        $program = $this->get('program'); // push by program
        $farmerid = $this->get('farmer'); // push by farmer
        $partner = $this->get('partner'); // push by partner
        if ($onlyNew === 'true') {
            $onlyNew = true;
        } else {
            $onlyNew = false;
        }

        $programs = $this->_dsync->getAllProgramWithView($program);
        
        if (strlen($farmerid) > 0) {
            if (count($programs) > 0) {
                foreach ($programs as $progkeys => $program) {
                    $district = substr($farmerid, 0, 4);
                    $farmers = $this->_dsync->getDataByDistrict('', $onlyNew, $program['uid'], $farmerid, $partner);
                    $this->_dsync->syncDataPerProgram($farmers, $program['uid'], $district, $partner);
                }
            }
        } else {

            $district = $this->_dsync->getAllDistrict($district);
            
            if ($district) {
                foreach ($district as $key => $value) {
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $farmers = $this->_dsync->getDataByDistrict($value['DistrictID'], $onlyNew, $program['uid'], $farmerid, $partner);
                            if ($farmers) {
                                $this->_dsync->syncDataPerProgram($farmers, $program['uid'], $value['DistrictID'], $partner);
                            }
                        }
                    }
                }
            }
        }
    }
    
    function notificationfarmerupdates_get() {
        
        $startdate = strtotime($this->get('startdate')) ? date('Y-m-d',strtotime($this->get('startdate'))):date('Y-m-d');
        $enddate = strtotime($this->get('enddate')) ? date('Y-m-d H:i:s',strtotime($this->get('enddate'))):date('Y-m-d H:i:s');
        $newfarmer = ($this->get('newonly') === 'true')?true:false;
        $tail = $newfarmer?'Baru':'Update';
        
        //select farmer berdasarkan DateUpdated per hari ini
        $farmers = $this->_dsync->getFarmerUpdates($startdate,$enddate,$newfarmer);
        
        //setelah udah dapet farmerlist nya di convert ke csv
        if($farmers) {
            $data = $farmers['data'];
            $csv = $farmers['csv'];
            
            //terus cari petugas FF berdasarkan district di ktv_access_staff
            $email_address = $this->_dsync->getOfficial($farmers['district']);
            
            $this->load->helper('file');
            
            //bikin attachment dulu yak
            if (write_file('data.csv', $csv) && count($email_address) > 0)
            {
                //kirim email dengan attachment ke masing-masing FF dan info@koltiva.com
                $this->load->library('email');
                
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = 'smtp.mandrillapp.com';
                $config['smtp_port'] = '587';
                $config['smtp_user'] = 'furqon17@gmail.com';
                $config['smtp_pass'] = '2quuIyzY5HMYToAUq3fEsg';
                $config['priority'] = '1';
                
                $this->email->initialize($config);

                $this->email->clear();

                $this->email->to(array('ardiantoro@koltiva.com','info@koltiva.com'));
                $this->email->from('noreply@koltiva.com');
                //$this->email->cc($email_address);
                $this->email->subject('Update Farmer');
                $this->email->message('Update Data Petani, ' . count($data) . 'Petani ' . $tail);
                $this->email->attach('data.csv');
                $this->email->send();
                
            } else {
                echo 'Unable to write';
            }
            
            
            
        }
        
        
        
        
        
        //done
        
    }
    
}

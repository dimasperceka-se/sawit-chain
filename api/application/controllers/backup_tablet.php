<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Backup_tablet extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('tools/mbackup_tablet');
        $this->load->model('mmiddleware');
    }

    public function upload_post() {
        $upload_path = './files/backup_tablet/';
        $config['allowed_types'] = 'zip';
        $config['max_size'] = '5120'; //10MB
        $this->load->library('upload');
        $config['upload_path'] = $upload_path;
        $config['encrypt_name'] = TRUE;

        $this->upload->initialize($config);
        if (!empty($_FILES['file']['tmp_name'])) {

            $result = array();

            $file = $this->file['file']['tmp_name'];
            $json = file_get_contents($file);
            $data = json_decode($json, true);
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    $result = $this->mmiddleware->ExecuteDhisData($value, $result);
                }
            }
            if (count($result) > 0)
//                $this->mmiddleware->sendEmailNotification($success, true);
                if (!$this->upload->do_upload('File')) {
                    $results['infos'] = "Warning";
                    $results['status'] = "false";
                    $results['message'] = $this->upload->display_errors();
                } else {
                    $file = $this->upload->data(); //detail FIle upoad
                    //**Next Proses klo mau di save ke database**//
                    $results['infos'] = "Success";
                    $results['status'] = "true";
                    $results['message'] = "Upload success.";
                }
        } else {
            $results['infos'] = "Warning";
            $results['status'] = "false";
            $results['message'] = "Upload failed. Please check the file. Max file size is 5MB.";
        }
        if ($results) {
            $this->response($results, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Training_farmer extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('training/mfarmer');
    }

    function datas_get_old() {
        $data = $this->mfarmer->readDatasOld($this->get('key'), $this->get('prov'), $this->get('dist'), $this->get('subdist'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function datas_get() {
        //get param
        $pSearch = array(
            'ArrFilter' => $this->get('ArrFilter'),
            'CmbFilterProvince' => (int) $this->get('CmbFilterProvince'),
            'CmbFilterDistrict' => (int) $this->get('CmbFilterDistrict'),
            'CmbFilterSubDistrict' => (int) $this->get('CmbFilterSubDistrict'),
            'CmbFilterVillage' => (int) $this->get('CmbFilterVillage'),
            'TextFilterID' => filter_var($this->get('TextFilterID'),FILTER_SANITIZE_STRING),
            'TextFilterName' => filter_var($this->get('TextFilterName'),FILTER_SANITIZE_STRING),
        );

        $data = $this->mfarmer->readDatas($this->get('IMSID'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_get() {
        $data = $this->mfarmer->readData($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_post() {
        if (!$this->post('training'))
            $this->response(NULL, 400);
        $add = $this->mfarmer->createData($_POST);
        if ($add)
            $this->response($add, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $update = $this->mfarmer->updateData($this->put());
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->mfarmer->deleteData($this->delete('id'));
        if ($delete)
            $this->response($delete, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function provinsi_label_get() {
        $data = $this->mfarmer->readLabelProvinsi($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function participants_get() {
        $data = $this->mfarmer->readParticipants($this->get('training'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_participant_post() {
        if (!$this->post('training')) {
            $this->response(NULL, 400);
        }
        $add = $this->mfarmer->createParticipant($this->post('training'), $this->post('farmer'), $this->post('participant'), $this->post('if_no'), $this->post('wstart'), $this->post('wend'), $this->post('bstart'), $this->post('bend'), $_SESSION['userid']);

        if ($cpg['success'] == true) {
            $this->load->model('farmer/mfarmer');
            $this->mfarmer->updateFarmerIsTraining($this->post('farmer'));
        }
        if ($add)
            $this->response($add, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }    

    public function fasilitator_scpp_get()
    {
        $data = $this->mfarmer->getFacilitatorSCPP();
        $this->response($data, 200);
    }

    public function fasilitator_mitra_get()
    {
        $data = $this->mfarmer->getFacilitatorMitra();
        $this->response($data, 200);
    }

    function data_participant_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $farmerid = is_numeric($this->put('farmer')) ? $this->put('farmer') : $this->put('farmer_id');
        $part = is_numeric($this->put('participant')) ? $this->put('participant') : $this->put('PetaniKakao');
        $ifno = is_numeric($this->put('if_no')) ? $this->put('if_no') : $this->put('FamilyID');
        $update = $this->mfarmer->updateParticipant($this->put('training'), $farmerid, $part, $ifno, $this->put('wstart'), $this->put('wend'), $this->put('bstart'), $this->put('bend'), $this->put('id'), $_SESSION['userid']);
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_participant_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->mfarmer->deleteParticipant($this->delete('id'));
        if ($delete)
            $this->response($delete, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function farmers_get() {
        $data = $this->mfarmer->readFarmers($this->get('prov'), $this->get('query'), $this->get('kab'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function families_get() {
        $data = $this->mfarmer->readFamilys($this->get('farmerid'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function check_get() {
        $data = $this->mfarmer->checkFarmer($this->get('trainingid'), $this->get('farmerid'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function cetak_get($id) {
        //set bahasa
        if ($_SESSION['language'] == "Indonesia") {
            $this->load->language('general', 'indonesia');
        } else {
            $this->load->language('general', 'english');
        }
        
        $data['data'] = $this->mfarmer->readTraining($id);
        // echo '<pre>'; print_r($data); echo '</pre>'; exit;
        if (empty($this->get('result'))) {
            $part = $this->mfarmer->readParticipantsTraining($id);
            $data['peserta'] = $part['data'];
        } else {
            $data['peserta'] = $this->mfarmer->getFarmerAttendanceDay($id, $this->get('DayNumber'));
        }
        // $data['logo'] = $this->mfarmer->readPartnerLogo($id);

        $this->load->model('project_process/mproject_process');
        $data['logo'] = $this->mproject_process->getPrintLogoHeader($data['data']['DistrictID']);
        // echo '<pre>'; print_r($data['logo']); echo '</pre>'; exit;

        $this->load->view('training_farmer_cetak_hadir', $data);
    }

    public function fasilitator_get()
    {
        $data = $this->mfarmer->getFacilitator();
        $this->response($data, 200);
    }

    public function participant_detail_get() {
        $FarmerTrainingsFarmerID = $this->get('FarmerTrainingsFarmerID');
        $result = $this->mfarmer->getParticipantDetail($FarmerTrainingsFarmerID);
        $this->response($result, 200);
    }

    public function participant_checklists_get() {
        $FarmerTrainingID   = $this->get('FarmerTrainingID');
        $FarmerID           = $this->get('FarmerID');

        $result = $this->mfarmer->getFarmerAttendance($FarmerTrainingID, $FarmerID);

        $this->response($result, 200);
    }

    public function participant_checklist_day_get() {
        $FarmerTrainingID     = $this->get('FarmerTrainingID');
        $DayNumber              = $this->get('DayNumber');

        $result = $this->mfarmer->getFarmerAttendanceDay($FarmerTrainingID, $DayNumber);

        $this->response($result, 200);
    }


    public function attendance_post() {
        $FarmerTrainingID = $this->post('FarmerTrainingID');
        $FarmerID = $this->post('FarmerID');
        $data = $this->post('data');
        //echo '<pre>'; print_r($data); exit;

        foreach ($data as $key => $value) {
            //$value['Attendance1'] = $value['Attendance1'] == 'true' ? 1 : 0;
            if ($value['Attendance1'] == 'true' || $value['Attendance1'] == 1) {
                $value['Attendance1'] = 1;
            } else {
                $value['Attendance1'] = 0;
            }

            //$value['Attendance2'] = $value['Attendance2'] == 'true' ? 1 : 0;
            if ($value['Attendance2'] == 'true' || $value['Attendance2'] == 1) {
                $value['Attendance2'] = 1;
            } else {
                $value['Attendance2'] = 0;
            }
            $result = $this->mfarmer->updateFarmerAttendance($FarmerTrainingID, $FarmerID, $value['DayNumber'], $value['Attendance1'], $value['Attendance2'], $value['TrainingDate'] ? date('Y-m-d', strtotime($value['TrainingDate'])) : null);
        }

        $this->response($result, 200);
    }

    public function attendance_day_post() {
        // echo '<pre>'; print_r($this->post(null)); echo '</pre>'; 
        $FarmerTrainingID = $this->post('FarmerTrainingID');
        $DayNumber = $this->post('DayNumber');
        $TrainingDate = $this->post('TrainingDate');
        $data = $this->post('data');
        //echo '<pre>'; print_r($data); exit;

        foreach ($data as $key => $value) {
            //$value['Attendance1'] = $value['Attendance1'] == 'true' ? 1 : 0;
            if ($value['Attendance1'] == 'true' || $value['Attendance1'] == 1) {
                $value['Attendance1'] = 1;
            } else {
                $value['Attendance1'] = 0;
            }

            //$value['Attendance2'] = $value['Attendance2'] == 'true' ? 1 : 0;
            if ($value['Attendance2'] == 'true' || $value['Attendance2'] == 1) {
                $value['Attendance2'] = 1;
            } else {
                $value['Attendance2'] = 0;
            }
            $FamilyID = 0;
            if ($value['AnggotaName']) {
                $FamilyID = $this->mfarmer->getFamilyID($value['FarmerID'], $value['AnggotaName']);
            }
            $result = $this->mfarmer->updateFarmerAttendance($FarmerTrainingID, $value['FarmerID'], $DayNumber, $value['Attendance1'], $value['Attendance2'], $TrainingDate ? date('Y-m-d', strtotime($TrainingDate)) : null, $FamilyID,$value['AnggotaName']);
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
        }
        // exit;
        $this->response($result, 200);
    }

    public function Provinsis_get() {
        $data = $this->mfarmer->readProvinsis($this->muserprofile->getUserProfile(), $this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Kabupatens_get() {
        $data = $this->mfarmer->readKabupatens($this->muserprofile->getUserProfile(),  $this->get('prov'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function cpgs_get() {
        $data = $this->mfarmer->readCPGs($this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        }
    }

    function participants_add_get() {
        $cpgs = $this->mfarmer->readParticipantsAdd($this->get('FarmerTrainingID'), $this->get('prov'), $this->get('kab'), $this->get('cpg'), $this->get('key'), $this->get('start'), $this->get('limit'));
        if ($cpgs)
            $this->response($cpgs, 200);
        else
            $this->response(array('error' => 'Couldn\'t find participants training!'), 404);
    }

    function participants_post() {
        if (!$this->post('FarmerTrainingID')) {
            $this->response(NULL, 400);
        }
        $participants = explode(',', $this->post('participants'));
        array_shift($participants);
        // $CpgBatchTrainingID,$participants,$PetaniKakao,$userid
        $result = $this->mfarmer->createParticipants(
                $this->post('FarmerTrainingID'), $participants, $_SESSION['userid']
        );
        // if ($result['success'] == true) {
        //     $this->load->model('farmer/mfarmer', 'farmermodel');
        //     foreach ($participants as $FarmerID) {
        //         $this->farmermodel->updateFarmerIsTraining($FarmerID);
        //     }
        // }
        if ($result)
            $this->response($result, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    // Berikut ini CPG training bukan farmer training
    public function download_get()
    {
        $query = $this->mfarmer->getFarmerTrainings($this->get('District'));
        // echo '<pre>'; print_r($query); echo '</pre>'; exit;
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {            
            $this->load->dbutil();
                
            // $data = $this->dbutil->csv_from_result($query);
            $data = ltrim(strstr($this->dbutil->csv_from_result($query, ',', "\r\n"), "\r\n")); // remove header row
            
            $this->load->helper('download');
            force_download("farmer_training {$this->get('District')}.csv", $data);
            // $writer = WriterFactory::create(Type::CSV); // for CSV files
            // //stream to browser
            // $writer->openToBrowser("farmer_training.csv");

            // $writer->addRows($rows); // add multiple rows at a time

            // $writer->close();
            # code...
        } else {
            $this->response(array('error' => lang('Data empty')), 404);
        }
    }

    public function absensi_post()
    {
        $is_upload = false;

        if (!empty($_FILES)) {
            $config['upload_path'] = './files/tmp/';
            $config['allowed_types'] = 'zip';
            $config['max_size'] = '8192';

            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('filezip')) {
                $error = array('error' => $this->upload->display_errors());
                $this->response(array('success' => false, 'message' => $error['error']), 400);
            } else {
                $is_upload = true;
                $data = array('upload_data' => $this->upload->data());
                
                $zip = new ZipArchive;
                if ($zip->open($data['upload_data']['full_path']) === TRUE) {
                    // delete_file($data['upload_data']['full_path']);
                    if (!file_exists($data['upload_data']['file_path'].$data['upload_data']['raw_name'])) {
                        mkdir($data['upload_data']['file_path'].$data['upload_data']['raw_name'], 0777, true);
                    }
                    $zip->extractTo($data['upload_data']['file_path'].$data['upload_data']['raw_name']);
                    $zip->close();
                    $files = array_slice(scandir($data['upload_data']['file_path'].$data['upload_data']['raw_name']), 2);

                    if (!empty($files)) {
                        $json_found = false;
                        $file_attachment = array();
                        foreach ($files as $file) {
                            if (strpos($file, '.json')) {
                                $json_found = true;
                                $data_post = json_decode(file_get_contents($data['upload_data']['file_path'].$data['upload_data']['raw_name'].'/'.$file), true);
                                // echo '<pre>'; print_r($data_post); echo '</pre>'; exit;
                            } else {
                                $tmp = explode('_', $file);
                                if (sizeof($tmp) > 4) {
                                    $session = substr($tmp[6], -1);
                                    $file_attachment[$tmp[1]]['farmer'][$tmp[3]][$session] = $data['upload_data']['file_path'].$data['upload_data']['raw_name'].'/'.$file;
                                } else {
                                    $file_attachment[$tmp[1]]['other'][] = $data['upload_data']['file_path'].$data['upload_data']['raw_name'].'/'.$file;
                                }
                            }
                        }
                        $data_post['file_attachment'] = $file_attachment;
                        if ($json_found === false) {
                            $this->response(array('success' => false, 'message' => 'Attendance file not found, please check your file!'), 400);
                        }
                    }
                } else {
                    $this->response(array('success' => false, 'message' => 'Can not read zip file, please check your file!'), 400);
                }
            }
        } else {
            $data_post = $this->post('null');
        }
        $debug = [];

        $training_id    = $data_post['training_id'];
        if ($this->mfarmer->checkFarmerTraining($training_id) !== false) {
            $participants   = $data_post['participants'];

            $this->db->trans_begin();

            //Hapus dulu attachment file jika ada data dari mobile
            if(!empty($data_post["file_attachment"][1]['other'])){
                $this->mfarmer->deleteAttachmentFile($training_id);
            }

            if (!empty($participants)){
                foreach ($participants as $key => $participant) {
                    $farmer_id      = $participant['farmer_id'];
                    if ($this->mfarmer->checkParticipant($training_id, $farmer_id) == false) {
                        $debug[] = $this->db->last_query();
                        $this->mfarmer->addParticipant($training_id, $farmer_id, $participant['w_start'], $participant['w_end'], $participant['b_start'], $participant['b_end']);
                    } else {
                        $this->mfarmer->editParticipant($training_id, $farmer_id, $participant['w_start'], $participant['w_end'], $participant['b_start'], $participant['b_end']);
                    }
                    $debug[] = $this->db->last_query();
                    // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                }
            }
            
            $attendances_list     = $data_post['attendances_list'];

            if (!empty($attendances_list)){
                foreach ($attendances_list as $key => $attendances) {
                    $day_number     = $attendances['day'];
                    $training_date  = (!isset($attendances['training_date']) || empty($attendances['training_date'])) ? date('Y-m-d') : date('Y-m-d', strtotime($attendances['training_date']));
                    $attendance     = $attendances['attendance'];
                    $file_attachment = ['farmer'=>[],'other'=>[]];

                    if (!empty($data_post['file_attachment'][$day_number])) {
                        $file_attachment = $data_post['file_attachment'][$day_number];
                        // echo '<pre>'; print_r($file_attachment); echo '</pre>'; exit;
                    }

                    $debug[] = $this->db->last_query();
                    foreach ($attendance as $att) {
                        if (isset($att['first_session']) || isset($att['second_session'])) {
                            $farmer_id      = $att['farmer_id'];
                            $family_id      = false;
                            if ($att['substitute_name']) {
                                $family_id = $this->mfarmer->getFamilyID($farmer_id, $att['substitute_name']);
                            }
                            $files = [];
                            if (!empty($file_attachment['farmer'][$farmer_id])) {
                                $files = $file_attachment['farmer'][$farmer_id];
                            }
                            // echo '<pre>'; print_r($file_attachment); echo '</pre>'; exit;
                            if ($this->mfarmer->checkFarmerAttendance($training_id, $farmer_id, $day_number) == false) {
                                $debug[] = $this->db->last_query();
                                $this->mfarmer->addFarmerAttendance($training_id, $farmer_id, $family_id, $att['substitute_name'], $day_number, $training_date, $att['first_session'], $att['second_session'], $files);
                            } else {
                                // echo '<pre>'; print_r($att); echo '</pre>'; 
                                // echo '<pre>'; print_r($files); echo '</pre>'; 
                                $this->mfarmer->editFarmerAttendance($training_id, $farmer_id, $family_id, $att['substitute_name'], $day_number, $training_date, $att['first_session'], $att['second_session'], $files);
                            }
                            $debug[] = $this->db->last_query();
                            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                        }
                    }


                    //Attachment List
                    if(!empty($file_attachment["other"])){
                        foreach($file_attachment["other"] as $key => $attfile){

                            $tmp        = explode("/", $attfile);
                            $filename   = end($tmp);

                            $this->mfarmer->MobileUpdateFileAttachment($filename, $attfile, $farmer_id, $training_id, $_SESSION['userid']);
                        }
                    }
                }
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $return['success']  = false;
                $return['message']  = "Failed to save data";
                $return['debug']    = $debug;
            } else {
                $this->db->trans_commit();
                $return['success'] = true;
                $return['message'] = "Data saved";
                $return['debug']    = $debug;
            }
            
        } else {
            $return = array('success' => false, 'message' => lang("Training {$training_id} doesn't exist"));
        }

        if ($is_upload == true) {
            $this->load->helper('file');
            // echo '<pre>'; print_r($data['upload_data']['full_path']); echo '</pre>'; exit;
            unlink($data['upload_data']['file_path'].$data['upload_data']['file_name']);
            // delete_files('/api/files/farmer_training_attendance_tmp/'.$data['upload_data']['file_name'], true);
            if (is_dir($data['upload_data']['file_path'].$data['upload_data']['raw_name'])) {
                $this->deleteDirectory($data['upload_data']['file_path'].$data['upload_data']['raw_name']);
            }
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($return, 200);
    }

    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
    
        if (!is_dir($dir)) {
            return unlink($dir);
        }
    
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
    
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
    
        }
    }

}

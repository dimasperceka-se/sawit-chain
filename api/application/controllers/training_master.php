<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Training_master extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('training/Mmaster');
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

        $data = $this->Mmaster->readDatas($pSearch, $this->get('start'), $this->get('limit'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_get() {
        $data = $this->Mmaster->readData($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_post() {
        if (!$this->post('training'))
            $this->response(NULL, 400);
        $add = $this->Mmaster->createData($this->post('training'), $this->post('fasilitator_scpp'), $this->post('TrainingStart'), $this->post('TrainingEnd'), $this->post('days'), $this->post('cpg'), $this->post('location'), $this->post('fasilitator_mitra'), $this->post('Provinsi'), $this->post('DistrictID'), $this->post('ServiceProvID'), !empty($this->post('ServiceProvID'))?$this->post('ServiceProvStaffName'):'', $_SESSION['userid'], $this->post('CpgTrainingsIDSubTopic'),$this->post('TrainingDayStatus'),$this->post('TrainingPurpose'));
        if ($add)
            $this->response($add, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $update = $this->Mmaster->updateData($this->put('training'), $this->put('fasilitator_scpp'), $this->put('TrainingStart'), $this->put('TrainingEnd'), $this->put('days'), $this->put('cpg'), $this->put('Provinsi'), $this->put('DistrictID'), $this->put('location'), $this->put('fasilitator_mitra'), $this->put('ServiceProvID'), !empty($this->put('ServiceProvID'))?$this->put('ServiceProvStaffName'):'', $this->put('id'), $_SESSION['userid'], $this->put('CpgTrainingsIDSubTopic'),$this->put('TrainingDayStatus'),$this->put('TrainingPurpose'));
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->Mmaster->deleteData($this->delete('id'));
        if ($delete)
            $this->response($delete, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function participants_get() {
        $data = $this->Mmaster->readParticipants($this->get('training'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_participant_post() {
        if (!$this->post('training'))
            $this->response(NULL, 400);
        $add = $this->Mmaster->createParticipant($this->post('training'), $this->post('staf'), $this->post('wstart'), $this->post('wend'), $this->post('bstart'), $this->post('bend'), $_SESSION['userid']);
        if ($add)
            $this->response($add, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_participant_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $staf = is_numeric($this->put('staf')) ? $this->put('staf') : $this->put('stafid');
        $update = $this->Mmaster->updateParticipant($this->put('training'), $staf, $this->put('wstart'), $this->put('wend'), $this->put('bstart'), $this->put('bend'), $this->put('id'), $_SESSION['userid']);
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_participant_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->Mmaster->deleteParticipant($this->delete('id'));
        if ($delete)
            $this->response($delete, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function staffs_get() {
        $data = $this->Mmaster->readStaffs($this->get('query'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function check_get() {
        $data = $this->Mmaster->check($this->get('trainingid'), $this->get('staffid'));
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
        
        $data['data']       = $this->Mmaster->readTraining($id);
        $part               = $this->Mmaster->readParticipantsTraining($id);
        $data['peserta']    = $part['data'];

        $this->load->model('project_process/mproject_process');
        $data['logos'] = $this->mproject_process->getPrintLogoHeader($data['data']['DistrictID'],null);

        $this->load->view('master_cetak_hadir', $data);
    }

    public function service_provider_get()
    {
        $data = $this->Mmaster->getServiceProvider();
        $this->response($data, 200);
    }

}

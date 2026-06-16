<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Training_kader extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('training/Mkader');
    }

    function datas_get() {
        $data = $this->Mkader->readDatas($this->get('key'), $this->get('prov'), $this->get('dist'), $this->get('subdist'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_get() {
        $data = $this->Mkader->readData($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_post() {
        if (!$this->post('training'))
            $this->response(NULL, 400);
        $add = $this->Mkader->createData($this->post('training'), $this->post('cpg'), $this->post('fasilitator_scpp'), $this->post('TrainingStart'), $this->post('TrainingEnd'), $this->post('location'), $this->post('fasilitator_mitra'), $this->post('days'), $this->post('Kabupaten'), $this->post('Provinsi'), $_SESSION['userid'], $this->post('CpgTrainingsIDSubTopic'),$this->post('TrainingDayStatus'));
        if ($add)
            $this->response($add, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $update = $this->Mkader->updateData($this->put('training'), $this->put('cpg'), $this->put('fasilitator_scpp'), $this->put('TrainingStart'), $this->put('TrainingEnd'), $this->put('location'), $this->put('fasilitator_mitra'), $this->put('days'), $this->put('Kabupaten'), $this->put('Provinsi'), $this->put('id'), $_SESSION['userid'], $this->put('CpgTrainingsIDSubTopic'),$this->put('TrainingDayStatus'));
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->Mkader->deleteData($this->delete('id'));
        if ($delete)
            $this->response($delete, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function provinsi_label_get() {
        $data = $this->Mkader->readLabelProvinsi($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function participants_get() {
        $data = $this->Mkader->readParticipants($this->get('training'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_participant_post() {
        if (!$this->post('training'))
            $this->response(NULL, 400);
        $add = $this->Mkader->createParticipant($this->post('training'), $this->post('farmer'), $this->post('participant'), $this->post('if_no'), $this->post('wstart'), $this->post('wend'), $this->post('bstart'), $this->post('bend'), $_SESSION['userid']);

        if ($cpg['success'] == true) {
            $this->load->model('farmer/mfarmer');
            $this->mfarmer->updateFarmerIsTraining($this->post('farmer'));
        }
        if ($add)
            $this->response($add, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_participant_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $farmerid = is_numeric($this->put('farmer')) ? $this->put('farmer') : $this->put('farmer_id');
        $part = is_numeric($this->put('participant')) ? $this->put('participant') : $this->put('PetaniKakao');
        $ifno = is_numeric($this->put('if_no')) ? $this->put('if_no') : $this->put('FamilyID');
        $update = $this->Mkader->updateParticipant($this->put('training'), $farmerid, $part, $ifno, $this->put('wstart'), $this->put('wend'), $this->put('bstart'), $this->put('bend'), $this->put('id'), $_SESSION['userid']);
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_participant_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->Mkader->deleteParticipant($this->delete('id'));
        if ($delete)
            $this->response($delete, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function farmers_get() {
        $data = $this->Mkader->readFarmers($this->get('prov'), $this->get('query'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function families_get() {
        $data = $this->Mkader->readFamilys($this->get('farmerid'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function check_get() {
        $data = $this->Mkader->checkFarmer($this->get('trainingid'), $this->get('farmerid'));
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
        
        $data['data'] = $this->Mkader->readTraining($id);
        $part = $this->Mkader->readParticipantsTraining($id);

        $data['peserta'] = $part['data'];

        $this->load->model('project_process/mproject_process');
        $data['logos'] = $this->mproject_process->getPrintLogoHeader($data['data']['DistrictID'],null);

        $this->load->view('kader_cetak_hadir', $data);
    }

}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Training_business extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('training/Mbusiness');
    }

    function datas_get() {
        $data = $this->Mbusiness->readDatas($this->get('key'), $this->get('prov'), $this->get('dist'), $this->get('subdist'), $this->get('start'), $this->get('limit'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_get() {
        $data = $this->Mbusiness->readData($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_post() {
        if (!$this->post('training'))
            $this->response(NULL, 400);
        $add = $this->Mbusiness->createData($this->post('training'), $this->post('fasilitator_scpp'), $this->post('TrainingStart'), $this->post('TrainingEnd'), $this->post('days'), $this->post('cpg'), $this->post('location'), $this->post('fasilitator_mitra'), $this->post('Provinsi'), $this->post('DistrictID'), $this->post('ServiceProvID'), !empty($this->post('ServiceProvID'))?$this->post('ServiceProvStaffName'):'', $_SESSION['userid'], $this->post('CpgTrainingsIDSubTopic'),$this->post('TrainingDayStatus'));
        if ($add)
            $this->response($add, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $update = $this->Mbusiness->updateData($this->put('training'), $this->put('fasilitator_scpp'), $this->put('TrainingStart'), $this->put('TrainingEnd'), $this->put('days'), $this->put('cpg'), $this->put('Provinsi'), $this->put('DistrictID'), $this->put('location'), $this->put('fasilitator_mitra'), $this->put('ServiceProvID'), !empty($this->put('ServiceProvID'))?$this->put('ServiceProvStaffName'):'', $this->put('id'), $_SESSION['userid'], $this->put('CpgTrainingsIDSubTopic'),$this->put('TrainingDayStatus'));
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->Mbusiness->deleteData($this->delete('id'));
        if ($delete)
            $this->response($delete, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function participants_get() {
        $data = $this->Mbusiness->readParticipants($this->get('training'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    public function Provinsis_get() {
        $data = $this->Mbusiness->readProvinsis();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Kabupatens_get() {
        $data = $this->Mbusiness->readKabupatens($this->get('ProvinceID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }

    public function Kecamatans_get() {
        $data = $this->Mbusiness->readKecamatans($this->get('DistrictID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any subdistrict!'), 404);
        }
    }

    public function cpgs_get() {
        $data = $this->Mbusiness->readCPGs($this->get('SubDistrictID'));
        if ($data) {
            $this->response($data, 200);
        }
    }

    function participants_add_farmer_get() {
        $data = $this->Mbusiness->readParticipantsAddFarmer($this->get('training'), $this->get('ProvinceID'), $this->get('DistrictID'), $this->get('SubDistrictID'), $this->get('CPGid'), $this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function participants_add_staff_get() {
        $data = $this->Mbusiness->readParticipantsAddStaff($this->get('training'), $this->get('role'), $this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function participants_post() {
        if (!$this->post('TrainingID')) {
            $this->response(NULL, 400);
        }
        $participants = explode(',', $this->post('participants'));
        // remove first empty data
        array_shift($participants);

        $result = $this->Mbusiness->createParticipants(
                $this->post('TrainingID'), $this->post('type'), $participants, $_SESSION['userid']
        );
        if ($result)
            $this->response($result, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function data_participant_post() {
        if (!$this->post('training'))
            $this->response(NULL, 400);
        $add = $this->Mbusiness->createParticipant($this->post('training'), $this->post('staf'), $this->post('wstart'), $this->post('wend'), $this->post('bstart'), $this->post('bend'), $_SESSION['userid']);
        if ($add)
            $this->response($add, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_participant_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        // $staf = is_numeric($this->put('staf')) ? $this->put('staf') : $this->put('stafid');
        $update = $this->Mbusiness->updateParticipant($this->put('wstart'), $this->put('wend'), $this->put('bstart'), $this->put('bend'), $this->put('id'), $_SESSION['userid']);
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function data_participant_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->Mbusiness->deleteParticipant($this->delete('id'));
        if ($delete)
            $this->response($delete, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function staffs_get() {
        $data = $this->Mbusiness->readStaffs($this->get('query'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function check_get() {
        $data = $this->Mbusiness->check($this->get('trainingid'), $this->get('staffid'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function cetak_get($id) {
        $data['data'] = $this->Mbusiness->readTraining($id);
        if (empty($this->get('result'))) {
            $part = $this->Mbusiness->readParticipantsTraining($id);
            $data['peserta'] = $part['data'];
        } else {
            $data['peserta'] = $this->Mbusiness->getFarmerAttendanceDay($id, $this->get('DayNumber'));
        }
        $data['logo'] = $this->Mbusiness->readPartnerLogo($id);
        $this->load->view('business_cetak_hadir', $data);
    }

    public function fasilitator_scpp_get()
    {
        $data = $this->Mbusiness->getFacilitatorSCPP();
        $this->response($data, 200);
    }

    public function fasilitator_mitra_get()
    {
        $data = $this->Mbusiness->getFacilitatorMitra();
        $this->response($data, 200);
    }

    public function participant_checklist_day_get() {
        $TrainingID     = $this->get('TrainingID');
        $DayNumber              = $this->get('DayNumber');

        $result = $this->Mbusiness->getFarmerAttendanceDay($TrainingID, $DayNumber);

        $this->response($result, 200);
    }

    public function attendance_day_post() {
        // echo '<pre>'; print_r($this->post(null)); echo '</pre>'; exit;
        $TrainingID     = $this->post('TrainingID');
        $DayNumber      = $this->post('DayNumber');
        $TrainingDate   = $this->post('TrainingDate');
        $data           = $this->post('data');
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
            $result = $this->Mbusiness->updateFarmerAttendance($TrainingID, $value['PartType'], $value['PartStaffID'], $value['PartFarmerID'], $DayNumber, $value['Attendance1'], $value['Attendance2'], $TrainingDate ? date('Y-m-d', strtotime($TrainingDate)) : null);
            echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
        }
        exit;
        $this->response($result, 200);
    }

}

<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-13 10:17:03
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Nursery extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('nursery/mnursery');
        $this->load->model('farmer/mfarmer');
    }

    public function cetak_nursery_summary_get($tipe,$ObjID,$NurseryNr,$bahasaCetak){
        if($bahasaCetak != ""){
            $this->load->language('general', $bahasaCetak);
            $data['bahasanya'] = $bahasaCetak;
        }else{
            if($_SESSION['language'] == "Indonesia"){
                $this->load->language('general', 'indonesia');
                $data['bahasanya'] = 'indonesia';
            }else{
                $this->load->language('general', 'english');
                $data['bahasanya'] = 'english';
            }
        }
        $data = array();

        //get data nursery
        $NurseryData = $this->mnursery->getNurseryByObject($tipe,$ObjID,$NurseryNr);
        $data['nursery'] = $NurseryData;
        $data['nursery_transaction'] = $this->mnursery->getNurseryTransactionByNurseryId($NurseryData['NurseryID']);
        $data['nursery_monitoring'] = $this->mnursery->getNurseryMonitoringByNurseryId($NurseryData['NurseryID']);
        $data['nursery']['countLastMonLog'] = count($data['nursery_monitoring']);
        $data['nursery']['countLastTrans'] = count($data['nursery_transaction']);
        if($data['nursery']['jumlahTransaksi'] == "") $data['nursery']['jumlahTransaksi'] = 0;

        switch ($tipe) {
            case 'cpg':
                $data['ObjTypeLabel'] = 'CPG';
                //nursery list
                $data['nursery_list'] = $this->mnursery->getNurseryListCpg($tipe,$ObjID,$NurseryNr);

                //data owner
                $data['owner'] = $this->mnursery->getDataOwnerNurseryCpg($NurseryData['ObjID']);
            break;
            case 'farmer':
                $data['ObjTypeLabel'] = 'SCE';
                //nursery list
                $data['nursery_list'] = $this->mnursery->getNurseryListCpg($tipe,$ObjID,$NurseryNr);

                //data owner
                $data['owner'] = $this->mnursery->getDataOwnerNurseryFarmer($NurseryData['ObjID']);
            break;
            case 'trader':
                $data['ObjTypeLabel'] = 'Trader';
                //$data['farmer'] = $this->mnursery->getProfileDataTrader($TraderID);
                //nursery list
                $data['nursery_list'] = $this->mnursery->getNurseryListTrader($tipe,$ObjID,$NurseryNr);

                //data owner
                $data['owner'] = $this->mnursery->getDataOwnerNurseryTrader($NurseryData['ObjID']);
            break;
            case 'koperasi':
                $data['ObjTypeLabel'] = 'Cooperative';
                //$data['farmer'] = $this->mnursery->getProfileDataCoop($CoopID);
                //nursery list
                $data['nursery_list'] = $this->mnursery->getNurseryListCoop($tipe,$ObjID,$NurseryNr);

                //data owner
                $data['owner'] = $this->mnursery->getDataOwnerNurseryCoop($NurseryData['ObjID']);
            break;
        }

        //ambil logo print
        $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($NurseryData['DistrictIDnya']);

        switch ($NurseryData['ResponsibleType']) {
            case 'farmer':
                $data['manager'] = $this->mnursery->getDataManagerNurseryFarmer($NurseryData['Responsible']);
            break;
            case 'staff':
                $data['manager'] = $this->mnursery->getDataManagerNurseryStaff($NurseryData['Responsible']);
            break;
            case 'other':
                $data['manager']['nama'] = $NurseryData['ResponsibleName'];
                $data['manager']['tgl_lahir'] = $NurseryData['ResponsibleBirthday'];
                $data['manager']['telp'] = $NurseryData['ResponsiblePhone'];
                $data['manager']['jk'] = $NurseryData['ResponsibleGender'];
                $data['manager']['foto'] = $NurseryData['ResponsiblePhoto'];
            break;
        }

        $this->load->view('cetak_nursery_summary_header');
        $this->load->view('cetak_nursery_summary', $data);
        $this->load->view('cetak_nursery_summary_footer');
    }

    public function cetak_nursery_form_get($tipe,$ObjID,$NurseryNr,$bahasaCetak,$printtype){
        if($bahasaCetak != "" && $bahasaCetak != "null"){
            $this->load->language('general', $bahasaCetak);
            $data['bahasanya'] = $bahasaCetak;
        }else{
            if($_SESSION['language'] == "Indonesia"){
                $this->load->language('general', 'indonesia');
                $data['bahasanya'] = 'indonesia';
            }else{
                $this->load->language('general', 'english');
                $data['bahasanya'] = 'english';
            }
        }
        $data = array();

        $dataNurseryOwner = $this->mnursery->getDataNurseryOwner($tipe,$ObjID);
        $data['dataNurseryOwner'] = $dataNurseryOwner;

        //ambil logo print
        $data['logos'] = $this->mnursery->getPartnerLogoByDistrict($dataNurseryOwner['DistrictID']);

        if($printtype == "empty"){
            $this->load->view('cetak_nursery_form', $data);
        }else{

            $dataNurseryFormPrint = $this->mnursery->getDataNurseryFormPrint($tipe,$ObjID,$NurseryNr);
            $data['dataNurseryFormPrint'] = $dataNurseryFormPrint;
            $data['nursery'] = $this->mnursery->getNurseryByObject($tipe,$ObjID,$NurseryNr);

            $data['nurseryPenjualan'] = $this->mnursery->getNurseryDataPenjualanPrint($data['nursery']['NurseryID']);
            $data['nurseryMonitoring'] = $this->mnursery->getNurseryDataMonitoringPrint($data['nursery']['NurseryID']);

            $this->load->view('cetak_nursery_form_result', $data);
        }
    }

    public function nursery_owner_get(){
        $data = $this->mnursery->getNurseryOwner($this->get('kabupaten'),$this->get('objType'),$this->get('printtype'));
        $this->response($data, 200);
    }

    public function nursery_nr_get(){
        $data = $this->mnursery->getNurseryNumber($this->get('objId'),$this->get('objType'));
        $this->response($data, 200);
    }
}
?>
<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-18 17:47:30
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trader_mem extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('trader_mem/mtrader_mem');
    }

    public function grid_main_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'roleSearch' => $this->get('roleSearch'),
            'AdvRowHandphone' => $this->get('AdvRowHandphone'),
            'AdvTextHandphone' => $this->get('AdvTextHandphone'),
            'AdvRowAge' => $this->get('AdvRowAge'),
            'AdvOpAge' => $this->get('AdvOpAge'),
            'AdvTextAge' => $this->get('AdvTextAge')
        );

        $data = $this->mtrader_mem->getGridMainTrader($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function member_basic_data_form_get(){
        $data = $this->mtrader_mem->getMemberBasicDataForm($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function image_member_post(){
        if($this->post('opsiDisplay') == "insert"){
            //ketika insert

            if ($this->file['Koltiva_view_Trader_FormMainTrader-MemberPhotoInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_Trader_FormMainTrader-MemberPhotoInput']['name'];
                $fileupload['Koltiva_view_Trader_FormMainTrader-MemberPhotoInput'] = $this->file['Koltiva_view_Trader_FormMainTrader-MemberPhotoInput'];
                $upload = move_upload($fileupload, 'images/trader/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false';
                    exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            //ketika update

            if ($this->file['Koltiva_view_Trader_FormMainTrader-MemberPhotoInput']['name'] != '') {
                $ProvinceID = $this->post('Koltiva_view_Trader_FormMainTrader-Province');

                //get ext nya..
                $arrTemp = explode(".", $this->file['Koltiva_view_Trader_FormMainTrader-MemberPhotoInput']['name']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];

                $gambar = $ProvinceID.'/'.$this->post('Koltiva_view_Trader_FormMainTrader-MemberDisplayID').'.'.$extNya;

                //cek folder propinsi itu sudah ada belum
                if(!file_exists('images/trader/'.$ProvinceID)){
                    mkdir('images/trader/'.$ProvinceID, 0777, true);
                }

                //hapus dl file lama
                @unlink('images/trader/'. $gambar);

                $fileupload['Koltiva_view_Trader_FormMainTrader-MemberPhotoInput'] = $this->file['Koltiva_view_Trader_FormMainTrader-MemberPhotoInput'];
                $upload = move_upload($fileupload, 'images/trader/'. $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = $gambar.'?'.rand(1,100);
                    $this->response($result, 200);
                } else {
                    echo  'false';
                    exit;
                }
            }

        }
    }

    public function image_member_business_photo_post(){
        if($this->post('opsiDisplay') == "insert"){
            //ketika insert
            if ($this->file['Koltiva_view_Trader_FormMainTrader-agBusinessLocationInput']['name'] != '') {
                $gambar = 'temp/'.$this->file['Koltiva_view_Trader_FormMainTrader-agBusinessLocationInput']['name'];
                $fileupload['Koltiva_view_Trader_FormMainTrader-agBusinessLocationInput'] = $this->file['Koltiva_view_Trader_FormMainTrader-agBusinessLocationInput'];
                $upload = move_upload($fileupload, 'images/trader_business/' . $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = $gambar;
                    $this->response($result, 200);
                } else {
                    echo  'false';
                    exit;
                }
            }
        }

        if($this->post('opsiDisplay') == "update"){
            //ketika update
            if ($this->file['Koltiva_view_Trader_FormMainTrader-agBusinessLocationInput']['name'] != '') {
                $ProvinceID = $this->post('Koltiva_view_Trader_FormMainTrader-Province');

                //get ext nya..
                $arrTemp = explode(".", $this->file['Koltiva_view_Trader_FormMainTrader-agBusinessLocationInput']['name']);
                $extNya = array_values(array_slice($arrTemp, -1))[0];

                $gambar = $ProvinceID.'/'.$this->post('Koltiva_view_Trader_FormMainTrader-MemberDisplayID').'.'.$extNya;

                //cek folder propinsi itu sudah ada belum
                if(!file_exists('images/trader_business/'.$ProvinceID)){
                    mkdir('images/trader_business/'.$ProvinceID, 0777, true);
                }

                //hapus dl file lama
                @unlink('images/trader_business/'. $gambar);

                $fileupload['Koltiva_view_Trader_FormMainTrader-agBusinessLocationInput'] = $this->file['Koltiva_view_Trader_FormMainTrader-agBusinessLocationInput'];
                $upload = move_upload($fileupload, 'images/trader_business/'. $gambar);
                if (isset($upload['upload_data'])) {
                    $result['success'] = true;
                    $result['file']    = $gambar.'?'.rand(1,100);
                    $this->response($result, 200);
                } else {
                    echo  'false';
                    exit;
                }
            }
        }
    }

    public function member_post(){
        if($this->post('Koltiva_view_Trader_FormMainTrader-MemberID') == ""){
            //insert
            $proses = $this->mtrader_mem->insertMember($this->post());
        }else{
            //update
            $proses = $this->mtrader_mem->updateMember($this->post());
        }
        $this->response($proses, 200);
    }

    public function member_delete(){
        $MemberID = (int) $this->delete('MemberID');
        $proses = $this->mtrader_mem->deleteMember($MemberID);
        $this->response($proses, 200);
    }

    public function grid_trader_staff_get(){
        $data = $this->mtrader_mem->getGridTraderStaff($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function grid_trader_vehicle_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        $data = $this->mtrader_mem->getGridTraderVehicle($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function cmb_brand_vehicle_get(){
        $data = $this->mtrader_mem->getCmbBrandVehicle();
        $this->response($data, 200);
    }

    public function cmb_staff_trader_get(){
        $data = $this->mtrader_mem->getCmbTraderStaff($this->get('MemberID'));
        $this->response($data, 200);
    }

    public function trader_vehicle_form_get(){
        $data = $this->mtrader_mem->getTraderVehicleFormData($this->get('VehID'));
        $this->response($data, 200);
    }

    public function trader_vehicle_post(){
        $varPost = $this->post();

        //prep variabel (begin)
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Trader_WinFormVehicle-Form-", '', $key);
            if ($value == "")
                $value = null;
            /*
            switch ($keyNew) {
                case 'VehCapacity':
                    $value = str_replace(",", "", $value);
                break;
            }
            */
            $paramPost[$keyNew] = $value;
        }
        //prep variabel (end)

        if($paramPost['VehID'] == ""){
            //insert
            $proses = $this->mtrader_mem->insertVehicle($paramPost);
        }else{
            //update
            $proses = $this->mtrader_mem->updateVehicle($paramPost);
        }
        $this->response($proses, 200);
    }

    public function trader_vehicle_delete(){
        $VehID = (int) $this->delete('VehID');
        $proses = $this->mtrader_mem->deleteVehicle($VehID);
        $this->response($proses, 200);
    }

}
?>
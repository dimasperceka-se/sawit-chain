<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Petani extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('tools/mpetani');
        $this->load->model('system/mlogupload');
    }

    function petanis_get() {
        $data = $this->mpetani->readDatas($_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function petani_cpg_get() {
        $data = $this->mpetani->readCpgs($this->get('prov'), $this->get('kab'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function petani_subdistrict_get() {
        $data = $this->mpetani->readSubdistricts($this->get('prov'), $this->get('kab'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function petani_village_get() {
        $data = $this->mpetani->readVillages($this->get('prov'), $this->get('kab'), $this->get('kec'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function checkRemoteFile($url) {
        $curl = curl_init($url);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);

        //do request
        $result = curl_exec($curl);
        //var_dumpt($result);exit;
        $ret = false;

        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode == 200) {
                $ret = true;
            }
        }

        curl_close($curl);

        return $ret;
    }

    function checkRemoteFile_($url) {
        echo $url;
        exit;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch))
            return true;
        else
            return false;
    }

    function process_photo_get() {
        $farmer = $this->mpetani->readDataFarmers($this->get('key'), $this->get('prov'), $this->get('kab'), $this->get('cpg'));
        for ($i = 0; $i < sizeof($farmer); $i++) {
            if ($farmer[$i]['Photo'] == '')
                $status = 'tidak ada';
            else {
                $path = explode('\\', $farmer[$i]['Photo']);
                $base = base_url(); //'http://app.cocoatrace.com/api/';
                if (sizeof($path) == 1) {
                    $file = str_replace(' ', '%20', str_replace('\\', '/', $base . 'images/Photo/' . $farmer[$i]['Photo']));
                    if ($this->checkRemoteFile($file))
                        $status = 'ada';
                    else
                        $status = 'ada path';
                } else {
                    $file = str_replace(' ', '%20', str_replace('\\', '/', $base . 'images/Photo/' . $farmer[$i]['Photo']));
                    if ($this->checkRemoteFile($file))
                        $status = 'ada';
                    else
                        $status = 'ada path';
                }
                $farmer[$i]['Photo'] = str_replace('%20', ' ', $file);
            }
            $this->mpetani->updateDataPetani($farmer[$i]['FarmerID'], $farmer[$i]['Photo'], $status);
        }
        return true;
    }

    function petani_photo_get() {
        $data = $this->mpetani->readDataPetanis(
                $this->get('key'), $this->get('prov'), $this->get('kab'), $this->get('kec'), $this->get('village'), $this->get('start'), $this->get('limit')
        );
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    public function petani_training_get() {
        $result = $this->mpetani->getPertaniTraining($this->get('CpgBatchTrainingID'), $this->get('start'), $this->get('limit'));
        $this->response($result, 200);
    }

    function petani_upload_post() {
        $file = $this->file['file']['tmp_name'];
        $name = $this->file['file']['name'];
        if ($name == 'tool.xls') {
            require_once 'application/libraries/excel_reader/excel_reader2.php';
            $data = new Spreadsheet_Excel_Reader($file);
            $err = $this->mpetani->tool($data);
            $result['success'] = true;
            $result['err'] = $err;
            $this->response($result, 200);
        }
        if (substr($name, strlen($name) - 3, 3) == 'xls') {
            require_once 'application/libraries/excel_reader/excel_reader2.php';
            $data = new Spreadsheet_Excel_Reader($file);
            $eData = $data->sheets[0]['cells'];
        } elseif (substr($name, strlen($name) - 4, 4) == 'xlsx') {
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/Shared/Date.php';
            $objectReader = PHPExcel_IOFactory::createReader('Excel2007');
            //$objectReader->setReadDataOnly(true);
            $objPHPExcel = $objectReader->load($file);
            $objectReader = $objPHPExcel->getActiveSheet();
            $eData = $objPHPExcel->getActiveSheet()->toArray();
            for ($i = 0; $i < sizeof($eData); $i++) {
                for ($j = 0; $j < sizeof($eData[0]); $j++) {
                    $data[$i + 1][$j + 1] = PHPExcel_Shared_Date::isDateTime($objectReader->getCellByColumnAndRow($j, $i)) ?
                            PHPExcel_Shared_Date::ExcelToPHPObject($eData[$i][$j])->format('Y-m-d') : $eData[$i][$j];
                }
            }
            $eData = $data;
        }
        $result = $this->mpetani->injectFarmer($eData, $_SESSION['userid']);
        if ($result && is_uploaded_file($file)) {
            $status = 'success';
        } else {
            $status = 'failed';
        }
        $this->mlogupload->insertLogUpload('farmer', $name, $status, $_SESSION['userid']);

        $return['success'] = true;
        // echo '<pre>'; print_r($return); echo '</pre>'; exit;
        $this->response($return, 200);
    }

    function petani_upload_foto_post() {
        $file = $this->file['file_foto']['tmp_name'];
        $name = $this->file['file_foto']['name'];

        $mem = ini_get('memory_limit');
        ini_set('memory_limit', '64M');
        $this->load->library('unzip');
        $tmp_dir = 'images/Photo/temp/' . $_SESSION['username'];
        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir);
        }
        $this->unzip->extract($file, $tmp_dir);
        $handle = opendir($tmp_dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if (is_file($tmp_dir . '/' . $entry)) {
                    $foto[] = "$entry";
                }
            }
        }
        // echo '<pre>'; print_r($foto); echo '</pre>'; exit;
        for ($i = 0; $i < sizeof($foto); $i++) {
            $fot = explode('.', $foto[$i]);
            //$result = $this->mpetani->getDataFoto($fot[0], $foto[$i]);
            $result = $this->mpetani->getDataFoto($fot[0]);
            // echo '<pre>'; print_r($result); echo '</pre>'; 
            if (@$result['FarmerID'] != "") {
                @mkdir('images/Photo/' . $result['Province']);
                $upload = copy($tmp_dir . '/' . $foto[$i], 'images/Photo/' . $result['Province'] . '/' . $result['File']);
                unlink($tmp_dir . '/' . $foto[$i]);
                //*Insert to photo history*//
                $this->mpetani->createPhotoHistory($result['FarmerID'], $result['Province'] . '/' . $result['File'], '1');
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $this->mpetani->updateDataPetani($result['FarmerID'], base_url() . 'images/Photo/' . $result['Province'] . '/' . $result['File'], 'ada');
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        // exit;

        if ($upload && is_uploaded_file($file)) {
            $status = 'success';
        } else {
            $status = 'failed';
        }
        $this->mlogupload->insertLogUpload('photo', $name, $status, $_SESSION['userid']);

        ini_set('memory_limit', $mem);
        $result['success'] = true;
        $this->response($result, 200);
    }

    function petani_upload_data_post() {
        $result = $this->mpetani->injectFarmerData($_SESSION['userid']);
        $return['success'] = $result;
        $this->response($return, 200);
    }

    function petani_upload_learning_contract_post() {
        $file = $this->file['file_learning_contract']['tmp_name'];
        $name = $this->file['file_learning_contract']['name'];

        $mem = ini_get('memory_limit');
        ini_set('memory_limit', '64M');
        $this->load->library('unzip');
        $this->unzip->extract($file, 'files/learning_contract_temp/');
        $handle = opendir('files/learning_contract_temp');
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $learning_contract[] = "$entry";
            }
        }
        for ($i = 0; $i < sizeof($learning_contract); $i++) {
            $lc = explode('.', $learning_contract[$i]);
            $check = $this->mpetani->getDataLearningContract($lc[0]);
            $pdf = date('Ymdhis') . '_' . $lc[0] . '_LearningContractFile.pdf';
            if ($check['LearningContractFile'] == "") {
                $upload = copy('files/learning_contract_temp/' . $learning_contract[$i], 'files/learning_contract/' . $pdf);
                if ($upload) {
                    unlink('files/learning_contract_temp/' . $learning_contract[$i]);
                    $this->mpetani->updateLearningContractFile($lc[0], $pdf);
                }
            }
        }
        if ($upload && is_uploaded_file($file)) {
            $status = 'success';
        } else {
            $status = 'failed';
        }
        $this->mlogupload->insertLogUpload('learning_contract', $name, $status, $_SESSION['userid']);
        ini_set('memory_limit', $mem);
        $result['success'] = true;
        $this->response($result, 200);
    }

    function photo_history_get() {
        if ($this->get('id') != "") {
            $FarmerID = $this->get('id');
        } else {
            $FarmerID = $this->get('id_default');
        }
        $photo = $this->mpetani->readHistorys($FarmerID, $this->get('start'), $this->get('limit'));
        if ($photo)
            $this->response($photo, 200);
        else
            $this->response(array('error' => 'Photo could not be found'), 404);
    }

    function photo_history_isactive_put() {
        $photo = $this->mpetani->updatePhotoFarmer($this->put('id'), $this->put('fid'), $this->put('photo'));
        if ($photo)
            $this->response($photo, 200);
        else
            $this->response(array('error' => 'Photo could not be found'), 404);
    }

    function photo_history_delete() {
        $photo = $this->mpetani->deletePhotoHistory($this->delete('id'), $this->delete('fid'), $this->delete('photo'));
        if ($photo)
            $this->response($photo, 200);
        else
            $this->response(array('error' => 'Photo could not be found'), 404);
    }

}

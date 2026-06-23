<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('tools/mgps');
        $this->load->model('tools/msyn');
        $this->load->model('system/mlogupload');
        $this->load->model('tools/mpetani');
        $this->load->model('tools/mtools');
        $this->load->model('tools/mfarmers');
        $this->load->model('tools/mgarden');
        $this->load->model('tools/mtoolscognito');        
    }

    function gpss_get() {
        $data = $this->mgps->readDatas($_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function import_existing_farmer_to_dhis_get(){
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $this->load->model('mmiddleware');
        $sql = "SELECT MemberID FROM ktv_members_tmp_2021 WHERE uid IS NULL AND StatusCode = 'active'";
        $query = $this->db->query($sql);

        $uid = "kclUA4bdLQU";
        
        if($query->num_rows()>0){
            $no = 0;
            foreach($query->result_array() as $row){
                if($uid != '' && $row['MemberID']) {
                    $mID = $row['MemberID'];
                    $onlyNew = true;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas  = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $row);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                            $no++;
                        }
                    }
                }
            }
            if($no > 0){
                $response = array("success"=>true,"message"=>$no." Data Inserted");
            }else{
                $response = array("success"=>false,"message"=>$no." Data Inserted");
            }
            $this->response($response, 200);
        }
    }

    function update_existing_farmer_to_dhis_get(){
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $this->load->model('mmiddleware');
        $sql = "SELECT MemberID FROM ktv_members_tmp_2021 WHERE uid IS NOT NULL AND StatusCode = 'active'";
        $query = $this->db->query($sql);

        $uid = "kclUA4bdLQU";
        
        if($query->num_rows()>0){
            $no = 0;
            foreach($query->result_array() as $row){
                if($uid != '' && $row['MemberID']) {
                    $mID = $row['MemberID'];
                    $onlyNew = true;
                    $programs = $this->mmiddleware->getAllProgramWithView($uid);
                    if (count($programs) > 0) {
                        foreach ($programs as $progkeys => $program) {
                            $datas  = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $row);
                            $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                            $no++;
                        }
                    }
                }
            }
            if($no > 0){
                $response = array("success"=>true,"message"=>$no." Data Inserted");
            }else{
                $response = array("success"=>false,"message"=>$no." Data Inserted");
            }
            $this->response($response, 200);
        }
    }

    /* function gps_upload_post() {
      $file = $this->file['file']['tmp_name'];
      $name = $this->file['file']['name'];
      if ($name == 'tool.xls') {
      require_once 'application/libraries/excel_reader/excel_reader2.php';
      $data = new Spreadsheet_Excel_Reader($file);
      $result = $this->mgps->tool($data, $_SESSION['userid']);
      $result['success'] = true;
      $this->response($result, 200);
      }
      if (substr($name, strlen($name) - 3, 3) == 'xls') {
      require_once 'application/libraries/excel_reader/excel_reader2.php';
      $data = new Spreadsheet_Excel_Reader($file);
      $eData = $data->sheets[0]['cells'];
      } elseif (substr($name, strlen($name) - 4, 4) == 'xlsx') {
      require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
      require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
      $objectReader = PHPExcel_IOFactory::createReader('Excel2007');
      $objectReader->setReadDataOnly(true);
      $objPHPExcel = $objectReader->load($file);
      $eData = $objPHPExcel->getActiveSheet()->toArray();
      for ($i = 0; $i < sizeof($eData); $i++) {
      for ($j = 0; $j < sizeof($eData[0]); $j++) {
      $data[$i + 1][$j + 1] = $eData[$i][$j];
      }
      }
      $eData = $data;
      }

      $result = $this->mgps->injectData($eData, $_SESSION['userid']);

      if ($result && is_uploaded_file($file)) {
      $status = 'success';
      } else {
      $status = 'failed';
      }
      $this->mlogupload->insertLogUpload('gps', $name, $status, $_SESSION['userid']);

      $result['success'] = true;
      $this->response($result, 200);
      } */

    public function gps_upload_post() {
        $file = $this->file['file']['tmp_name'];
        $name = $this->file['file']['name'];
        $nm = explode('.', $name);

        $this->load->library('Excel', null, 'PHPExcel');

        $excel_data = $this->PHPExcel->import($file, false);

        $result = $this->mgps->processUpload($excel_data, $_SESSION['userid']);
        if ($result['success'] == true) {
            $this->mlogupload->insertLogUpload('gps', $name, 'success', $_SESSION['userid']);
        }

        $this->response($result, 200);
    }

    function gps_upload_data_post() {
        $success = $this->mgps->updateData($_SESSION['userid']);
        if ($success) {
            $this->mgps->deleteUpload($_SESSION['userid']);
        }
        $result['success'] = $success;
        $result['msg'] = $this->db->affected_rows() . ' affected rows';

        $this->response($result, 200);
    }

    function merge_line($array, $a, $lines, $i) {
        if (sizeof($array[$a]) < sizeof($array[0])) {
            $i++;
            $arr = str_getcsv($lines[$i]);
            $arr[0] = $array[$a][sizeof($array[$a]) - 1] . PHP_EOL . $arr[0];
            unset($array[$a][sizeof($array[$a]) - 1]);
            $array[$a] = array_merge($array[$a], $arr);
            if (sizeof($array[$a]) < sizeof($array[0]))
                return $this->merge_line($array, $a, $lines, $i);
        }
        return array($array[$a], $i);
    }

    // function syn_upload_post() {
    //      $file = $this->file['file']['tmp_name'];
    //      $name = $this->file['file']['name'];
    //      $nm = explode('.',$name);
    //
    //      $csvData = file_get_contents($file);
    //      $lines = explode(PHP_EOL, $csvData);
    //      $array = array();
    //      $a = 0;
    //      for ($i=0;$i<sizeof($lines);$i++) {
    //         $array[$a] = str_getcsv($lines[$i]);
    //         if ($i>0 and trim($lines[$i])!='') {
    //            $ar = $this->merge_line($array,$a,$lines,$i);
    //            $array[$a] = $ar[0];
    //            $i = $ar[1];
    //         }
    //         $a++;
    //      }
    //      /*print_r($array);exit;
    //      foreach ($lines as $line) {
    //          $array[] = str_getcsv($line);
    //      }*/
    //      $this->msyn->replaceData($nm[0],$array,$_SESSION['userid']);
    //      $result['success'] = true;
    //      $this->response($result, 200);
    // }

    public function import_farmers_grid_main_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else
            $sortingDir = null;

        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $KeySearch = $this->get('KeySearch');

        $data = $this->mfarmers->ImportFarmersGridMain($KeySearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    private function convertDefault($valueNya) {
        $valueNya = trim($valueNya);
        if ($valueNya == "" || $valueNya == "0") {
            return null;
        } else {
            return trim($valueNya);
        }
    }

    public function import_farmer_upload_post() {
        //Ambil Ext
        $arrTemp = explode(".", $this->file['Koltiva_view_ImportFarmers_GridMainFarmers-MainGrid-Form-FileInput']['name']);
        $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
        $arrTempExt = explode("?", $tempExtNya);
        $extNya = $arrTempExt[0];

        if (in_array($extNya, array('xlsx'))) {
            if (file_exists($this->file['Koltiva_view_ImportFarmers_GridMainFarmers-MainGrid-Form-FileInput']['tmp_name'])) {
                require_once 'application/third_party/PHPExcel18/PHPExcel.php';
                require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

                $inputFileName = $this->file['Koltiva_view_ImportFarmers_GridMainFarmers-MainGrid-Form-FileInput']['tmp_name'];
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $excelResult = $objReader->load($inputFileName);
//                echo '<pre>'; print_r($excelResult->getActiveSheet()->getCell('A2')->getFormattedValue()); echo '</pre>'; exit;
                //data siap insert
                $dataInsert = array();

                $break = false;
                $error = false;
                $start = 1;
                $incre = 0;
                while (!$break) {
                    if ($start == 1) {
                        $start++;
                        continue;
                    }
                    // validate birthdate sesuai dengan format tanggal
                    $bd = trim($excelResult->getActiveSheet()->getCell('B' . $start)->getFormattedValue());
                    if ($bd != '') {
                        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $bd)) {
                            $birthdate = $bd;
//                            $break = false;
                        } else {
                            //keluar loop
                            $birthdate = NULL;
//                            $break = true;
                        }
                    }

                    if (trim($excelResult->getActiveSheet()->getCell('A' . $start)->getFormattedValue()) != '') {
                        $FarmerName = $this->convertDefault($excelResult->getActiveSheet()->getCell('A' . $start)->getFormattedValue());
                        $Birthdate = $birthdate;
                        $Gender = $this->convertDefault($excelResult->getActiveSheet()->getCell('C' . $start)->getFormattedValue());
                        $VillageID = $this->convertDefault($excelResult->getActiveSheet()->getCell('D' . $start)->getFormattedValue());
                        $PartnerID = $this->convertDefault($excelResult->getActiveSheet()->getCell('E' . $start)->getFormattedValue());

                        //masukkan variabel
                        $dataInsert[$incre]['FarmerName'] = $FarmerName;
                        $dataInsert[$incre]['Birthdate'] = $Birthdate;
                        $dataInsert[$incre]['Gender'] = $Gender;
                        $dataInsert[$incre]['VillageID'] = $VillageID;
                        $dataInsert[$incre]['PartnerID'] = $PartnerID;

                        $start++;
                        $incre++;
                    } else {
                        //keluar loop
                        $break = true;
                    }
                }
                $proses = $this->mfarmers->ImportDataFarmerTabelTemp($dataInsert);
                if ($proses['success'] == true) {
                    $this->response($proses, 200);
                } else {
                    $this->response($proses, 400);
                }
            } else {
                $proses['success'] = true;
                $proses['message'] = lang('File tidak ditemukan, pastikan file tidak terlalu besar');
                $this->response($proses, 400);
            }
        } else {
            $proses['success'] = true;
            $proses['message'] = lang('Invalid file type, file type must be .xlsx');
            $this->response($proses, 400);
        }
    }

    public function import_farmer_post() {
        $proses = $this->mfarmers->ImportMemberExcel();
        $proses['success'] = true;
        $proses['message'] = "Calon petani sudah terimport menjadi petani";
        if ($proses['success'] == true) {
            $this->load->model('mmiddleware');
            $uid = 'QxauNvjcpBw'; // push by program
            $onlyNew = 'true';
            if ($onlyNew === 'true') {
                $onlyNew = true;
            } else {
                $onlyNew = false;
            }
            foreach ($proses['mid'] as $k => $v) {
                $programs = $this->mmiddleware->getAllProgramWithView($uid);
                if (count($programs) > 0) {
                    foreach ($programs as $progkeys => $program) {
                        $datas = $this->mmiddleware->getDataBy($onlyNew, $program['uid'], $v);
                        $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                    }
                }
            }
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function syn_upload_post() {
        $file = $this->file['file']['tmp_name'];
        $name = $this->file['file']['name'];
        $nm = explode('.', $name);

        $this->load->library('Excel', null, 'PHPExcel');
        $excel_data = $this->PHPExcel->import($file, false);
        // $this->db->trans_start(FALSE);
        $result = $this->msyn->processSyn($excel_data, $nm[0]);
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // $this->db->trans_rollback();
        // exit;

        if ($result && is_uploaded_file($file)) {
            $status = 'success';
        } else {
            $status = 'failed';
        }
        $this->mlogupload->insertLogUpload('sync', $name, $status, $_SESSION['userid']);

        $this->response($result, 200);
    }

    public function farmer_post() {
        $post = $this->post(NULL);
        $table = 'ktv_farmer';
        $allowed_fields = array('FarmerID', 'OldFarmerID', 'CPGid', 'OldCPGid', 'FarmerName', 'DateCollection', 'Gender', 'VillageID', 'Address', 'HandPhone', 'MaritalStatus', 'Birthdate', 'Education', 'Photo', 'Photo_base64', 'Latitude', 'Longitude', 'KeyFarmer', 'DemoPlot', 'OtherTraining', 'CPGmembership', 'OtherTrainingSiapa', 'OtherTrainingTahun', 'OtherTrainingLama', 'DemoPlotLama', 'DemoPlotRehab', 'FarmerGroupFunctionsID', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy', 'StatusCode', 'DeleteReason', 'isValid', 'isValidGarden', 'isValidPostHarvest', 'isValidNutrition', 'isValidPPIScore', 'ApprovedByME', 'ApprovedByGO', 'ApprovedByDC', 'CommentValid', 'LahanKakao', 'LahanProduksiLain', 'TotalLahan', 'KebunKakao', 'Elevation', 'LahanKosong', 'Muge', 'ActiveMemberCooperation', 'DateSurvey', 'DateSynced', 'StatusFarmer', 'DeceasedStatus', 'FamilyMemberID', 'MovedLeftArea', 'SwitchOtherCrop',);
        $primary_key = array('FarmerID');
        $not_null = array('FarmerID');

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function garden_post() {
        $post = $this->post(NULL);
        $table = 'ktv_farmer_garden';
        $allowed_fields = array('FarmerID', 'OldFarmerID', 'GardenNr', 'SurveyNr', 'DateCollection', 'Latitude', 'LatDeg', 'LatMin', 'LatSec', 'Longitude', 'LongDeg', 'LongMin', 'LongSec', 'Elevation', 'OwnershipCocoa', 'TahunTanamanCocoa', 'GardenDistance', 'GardenHaUnCertified', 'GardenHaPolygon', 'Production', 'PanenBiasaMonths', 'PanenBiasaPanenMonth', 'PanenBiasaKg', 'PanenTrekMonths', 'PanenTrekPanenMonth', 'PanenTrekKg', 'PanenRayaMonths', 'PanenRayaPanenMonth', 'PanenRayaKg', 'TimeHarvestBiasa', 'TimeHarvestTrek', 'TimeHarvestRaya', 'LandOwner', 'LandCertificate', 'PohonTBM', 'PohonTM', 'PohonRehab', 'GraftedTrees', 'ReplantedTrees', 'RoadCondition', 'Comment', 'TSH858', 'RCC70', 'RCC71', 'RCC72', 'RCC73', 'Hybrid', 'S1', 'S2', 'S3', 'ICRRI3', 'ICRRI4', 'ICRRI5', 'M01', 'M06', 'THR', 'RCL', 'J45', 'CloneLain', 'TSH858Nr', 'RCC70Nr', 'RCC71Nr', 'RCC72Nr', 'RCC73Nr', 'LokalNr', 'S1Nr', 'S2Nr', 'S3Nr', 'ICRRI3Nr', 'ICRRI4Nr', 'ICRRI5Nr', 'M01Nr', 'M06Nr', 'THRNr', 'RCLNr', 'J45Nr', 'CloneLainNr', 'Gamal', 'Kelapa', 'Durian', 'Pinang', 'Karet', 'JackFruit', 'Lamtoro', 'Mahoni', 'Pisang', 'Rambutan', 'Sukun', 'Jengkol', 'Sengon', 'Petai', 'Jabon', 'Uru', 'Biti', 'Jati', 'Jeruk', 'Jambu', 'Kedondong', 'Cempedak', 'Manggis', 'Pepaya', 'Alpukat', 'Kemiri', 'JambuMente', 'Kapok', 'Pala', 'Aren', 'Sawit', 'Cengkeh', 'Mangga', 'Langsat', 'ShadeLain', 'GamalNr', 'KelapaNr', 'DurianNr', 'PinangNr', 'KaretNr', 'JackFruitNr', 'LamtoroNr', 'MahoniNr', 'PisangNr', 'RambutanNr', 'CengkehNr', 'SawitNr', 'ArenNr', 'ManggaNr', 'LangsatNr', 'PalaNr', 'KemiriNr', 'JambuMenteNr', 'KapokNr', 'AlpukatNr', 'SukunNr', 'PepayaNr', 'ManggisNr', 'JerukNr', 'JambuNr', 'KedondongNr', 'CempedakNr', 'JatiNr', 'BitiNr', 'UruNr', 'JabonNr', 'PetaiNr', 'JengkolNr', 'SengonNr', 'ShadeLainNr', 'ShadeTreesNr', 'TimeHarvest', 'HarvestAwal', 'HarvestMasak', 'HarvestHama', 'PruningPlants', 'FrequentPruning', 'HighPruning', 'PruningProtectPlants', 'FrequentPruningProtect', 'CleanSkin', 'HowToCleanSkin', 'OrganicKotoran', 'OrganicResidu', 'OrganicMembeli', 'TidakMemakaiOrganic', 'Urea', 'TSP', 'NPK', 'KCL', 'TidakMemakaiKimia', 'FrequentFertilizationOrganic', 'DoseFertilizerOrganic', 'FrequentFertilizationKimia', 'DoseFertilizerKimia', 'PakaiKompos', 'FrequentFertilizationKompos', 'DoseFertilizerKompos', 'FrKomposKandang', 'FrKomposCair', 'FrKomposGranula', 'DoseKomposKandang', 'DoseKomposCair', 'DoseKomposGranula', 'FrUrea', 'FrZa', 'FrTsp', 'FrNpk', 'FrKcl', 'DoUrea', 'DoZa', 'DoTsp', 'DoNpk', 'DoKcl', 'FrLain', 'DoLain', 'KomposTBM', 'KomposTM', 'KomposTR', 'PupukTBM', 'PupukTM', 'PupukTR', 'KimiaDana', 'KimiaSupplier', 'KimiaDilatih', 'KimiaTidakSuka', 'KimiaTidakTersedia', 'KimiaLain', 'HamaBPK', 'HamaHelopeltis', 'HamaBatang', 'PenyakitKanker', 'PenyakitBusuk', 'PenyakitUpas', 'PenyakitAkar', 'PenyakitVSD', 'PenyakitAntraknose', 'Herbisida', 'MerekHerbisida', 'FrequentHerbisida', 'DoseHerbisida', 'Herbisida1', 'Herbisida2', 'Herbisida3', 'Herbisida4', 'Herbisida5', 'Herbisida6', 'Herbisida7', 'Herbisida8', 'Herbisida9', 'Herbisida10', 'Herbisida11', 'Herbisida12', 'Herbisida13', 'Herbisida14', 'Herbisida15', 'Herbisida16', 'Herbisida17', 'Herbisida18', 'Herbisida19', 'Herbisida20', 'Herbisida21', 'Herbisida22', 'Herbisida23', 'Herbisida24', 'Herbisida25', 'Herbisida26', 'Herbisida27', 'Herbisida28', 'Herbisida29', 'Insectisida', 'MerekInsectisida', 'FrequentInsectisida', 'DoseInsectisida', 'Insectisida1', 'Insectisida2', 'Insectisida3', 'Insectisida4', 'Insectisida5', 'Insectisida6', 'Insectisida7', 'Insectisida8', 'Insectisida9', 'Insectisida10', 'Insectisida11', 'Insectisida12', 'Insectisida13', 'Insectisida14', 'Insectisida15', 'Insectisida16', 'Insectisida17', 'Insectisida18', 'Insectisida19', 'Insectisida20', 'Insectisida21', 'Insectisida22', 'Insectisida23', 'Fungisida', 'MerekFungisida', 'FrequentFungisida', 'DoseFungisida', 'Fungisida1', 'Fungisida2', 'Fungisida3', 'Fungisida4', 'Fungisida5', 'Fungisida6', 'Fungisida7', 'Fungisida8', 'Fungisida9', 'Fungisida10', 'Fungisida11', 'Fungisida12', 'Fungisida13', 'APD', 'TempatSimpanPestisida', 'BuangKemasanPestisida', 'DateCreated', 'CreatedBy', 'DateUpdated', 'DateSynced', 'LastModifiedBy', 'StatusCode', 'isValid', 'StatusGPS', 'ApprovedByME', 'ApprovedByGO', 'ApprovedByDC', 'CommentValid', 'TopGraftedTrees', 'GraftedTreesTahun', 'TopGraftedTreesTahun', 'ReplantedTreesTahun', 'isCertification', 'Certification', 'StatusAudit', 'DateRevisionAudit', 'CommentAudit', 'RecommendationAudit', 'RACertQuestion1', 'RACertQuestion2', 'RACertQuestion3', 'RACertQuestion4', 'RACertQuestion5', 'RACertQuestion6', 'RACertQuestion7', 'RACertQuestion8', 'RACertQuestion9', 'RACertQuestion10', 'RehabTrees', 'RehabTreesTahun', 'InsetTrees', 'InsetTreesTahun', 'FrFoliar', 'DoFoliar',);
        $primary_key = array('FarmerID', 'GardenNr', 'SurveyNr',);
        $not_null = array('FarmerID', 'GardenNr', 'SurveyNr', 'DateCollection', 'DateCreated', 'DateUpdated', 'LastModifiedBy',);

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function post_harvest_post() {
        $post = $this->post(NULL);
        $table = 'ktv_farmer_post_harvest';
        $allowed_fields = array('FarmerID', 'OldFarmerID', 'SurveyNr', 'DateCollection', 'AnggotaKerjaKebun', 'BuruhSeasonal', 'BuruhSeasonalRupiah', 'BuruhSeasonalPersen', 'BuruhFulltime', 'BuruhFulltimeRupiah', 'BuruhFulltimePersen', 'Fermentation', 'FermentationDays', 'SunDryingSemen', 'DryingAlat', 'DryingDays', 'CocoaBuyers', 'NoFermentation', 'Sortasi', 'NoSortasi', 'SunDryingAspal', 'JemurYesNo', 'TidakJemur', 'SunDryingAlas', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy', 'StatusCode', 'isValid', 'ApprovedByME', 'ApprovedByGO', 'ApprovedByDC', 'CommentValid', 'DateSynced', 'Distance', 'Comment', 'AdaProduksi', 'AntarSendiri',);
        $primary_key = array('FarmerID', 'SurveyNr',);
        $not_null = array('FarmerID', 'SurveyNr',);

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function ppiscore_post() {
        $post = $this->post(NULL);
        $table = 'ktv_ppiscore';
        $allowed_fields = array('FarmerID', 'OldFarmerID', 'SurveyNr', 'PrePostSurvey', 'InterviewDate', 'Householdmembers', 'Schooling', 'Working', 'DrinkingWater', 'ToiletFacility', 'HouseFloor', 'HouseCeiling', 'Refrigerator', 'Motorcycle', 'Television', 'Score', 'National', '1.25/day', '2.5/day', 'DateCreated', 'CreatedBy', 'DateUpdated', 'DateSynced', 'LastModifiedBy', 'StatusCode', 'isValid', 'ApprovedByME', 'ApprovedByGO', 'ApprovedByDC', 'CommentValid');
        $primary_key = array('FarmerID', 'SurveyNr',);
        $not_null = array('FarmerID', 'SurveyNr', 'InterviewDate', 'isValid', 'ApprovedByME', 'ApprovedByGO', 'ApprovedByDC', 'CommentValid',);

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function ppiscore2012_post() {
        $post = $this->post(NULL);
        $table = 'ktv_ppiscore2012';
        $allowed_fields = array('FarmerID', 'OldFarmerID', 'SurveyNr', 'InterviewDate', 'Householdmembers', 'Schooling', 'Education', 'Employment', 'HouseFloor', 'ToiletFacility', 'CookingFuel', 'GasCylinder', 'Refrigerator', 'Motorcycle', 'Score', 'National', '1.25/day', '2.5/day', 'DateCreated', 'CreatedBy', 'DateUpdated', 'DateSynced', 'LastModifiedBy', 'StatusCode', 'isValid', 'ApprovedByME', 'ApprovedByGO', 'ApprovedByDC', 'CommentValid',);
        $primary_key = array('FarmerID', 'SurveyNr',);
        $not_null = array('FarmerID', 'SurveyNr',);

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function nutrition_post() {
        $post = $this->post(NULL);
        $table = 'ktv_nutrition';
        $allowed_fields = array('FarmerID', 'OldFarmerID', 'InterviewDate', 'SurveyNr', 'KebunPanjang', 'KebunLebar', 'KbBayam', 'KbCabai', 'KbKacangPanjang', 'KbKangkung', 'KbSawi', 'KbTerong', 'KbTomat', 'KbKambing', 'KbSapi', 'KbBebek', 'KbAyam', 'KbIkan', 'aSagu', 'aNasi', 'aMie', 'aJagung', 'aRoti', 'bUbiJalarKuning', 'bSingkongKuning', 'bWortel', 'bLabu', 'cUbiJalarPutih', 'cSingkongPutih', 'cTalas', 'cKentang', 'dBayam', 'dDaunMelinjo', 'dDaunPepaya', 'dDaunSingkong', 'dKangkung', 'dSawi', 'eKacangPanjang', 'eTomat', 'eTerong', 'fJambuMerah', 'fMangga', 'fPepaya', 'gJambuAir', 'gKelapa', 'gPisang', 'gRambutan', 'gSemangka', 'gSalak', 'hJeroan', 'hHati', 'iAyam', 'iBebek', 'iKambing', 'iKerbau', 'iSapi', 'iLainnya', 'jAyam', 'jBebek', 'jEntok', 'jPuyuh', 'kCumiCumi', 'kIkan', 'kIkanTeri', 'kKepiting', 'kKerang', 'kUdang', 'lAirTahuSusuKedelai', 'lSausKacang', 'lTahu', 'lTempe', 'lKacang', 'lKwaci', 'mKeju', 'mSusu', 'nMinyakGoreng', 'nMentega', 'nSantan', 'Score', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy', 'DateSynced', 'StatusCode', 'isValid', 'ApprovedByME', 'ApprovedByGO', 'ApprovedByDC', 'CommentValid',);
        $primary_key = array('FarmerID', 'SurveyNr',);
        $not_null = array('FarmerID', 'SurveyNr', 'InterviewDate');

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function finance_post() {
        $post = $this->post(NULL);
        $table = 'ktv_farmer_financial';
        $allowed_fields = array('FarmerID', 'SurveyNr', 'InterviewDate', 'isValid', 'Account', 'AccountTypeTabungan', 'AccountTypeDeposito', 'AccountTypeKoran', 'AccountTypeLainnya', 'AccountHolderFarmer', 'AccountHolderName', 'AccountBankName', 'AccountBankBranch', 'AccountNumber', 'AccountNoDetails', 'DepositWithdrawnMoneyLast12m', 'AccountFeesToPay', 'AccountInterestRate', 'MoneyUsageHarian', 'MoneyUsageTabung', 'MoneyUsageInvestasi', 'MoneyUsageEmas', 'MoneyUsageKonsumsi', 'NotSavingJauh', 'NotSavingTidakBeruang', 'NotSavingBiayaTinggi', 'NotSavingTidakPercaya', 'NotSavingAdaMenabung', 'NotSavingLainnya', 'SavingUnitRumah', 'SavingUnitBank', 'SavingUnitKoperasi', 'SavingUnitPedagang', 'SavingUnitArisan', 'SavingUnitOrang', 'SavingUnitLembaga', 'SavingUnitMeminjamkan', 'DistanceSavingLocation', 'AmountSaving', 'FutureReasonSekolah', 'FutureReasonRumahTangga', 'FutureReasonSumbangan', 'FutureReasonDarurat', 'FutureReasonKesehatan', 'FutureReasonInvestasiKebun', 'FutureReasonInvestasiLain', 'FutureReasonRumah', 'FutureReasonLahan', 'FutureReasonKendaraan', 'FutureReasonHaji', 'FutureReasonPensiun', 'FutureReasonLain', 'ImportantFactorKemanan', 'ImportantFactorLikuiditas', 'ImportantFactorAksesibilitas', 'ImportantFactorKepercayaan', 'ImportantFactorBiaya', 'ImportantFactorBunga', 'ImportantFactorLain', 'LoanYesNo', 'AmountCurrentLoan', 'AmountOutsCurrentLoan', 'LoanUnitTengkulak', 'LoanUnitKeluarga', 'LoanUnitRentenir', 'LoanUnitBank', 'LoanUnitKoperasi', 'LoanUnitMasjid', 'LoanUnitLainnya', 'PreviousLoan', 'CollateralCurrentLoan', 'EasyCurrentLoan', 'DisburseIntervalCurrentLoan', 'RepaymentScheduleCurrentLoan', 'EasyGetNewLoan', 'UsageCurrentLoanHarian', 'UsageCurrentLoanSekolah', 'UsageCurrentLoanRumahTangga', 'UsageCurrentLoanSumbangan', 'UsageCurrentLoanHutang', 'UsageCurrentLoanDarurat', 'UsageCurrentLoanKesehatan', 'UsageCurrentLoanInvestasiKebun', 'UsageCurrentLoanInvestasiLain', 'UsageCurrentLoanRumah', 'UsageCurrentLoanLahan', 'UsageCurrentLoanKendaraan', 'UsageCurrentLoanHaji', 'UsageCurrentLoanPensiun', 'UsageCurrentLoanLainnya', 'TerminatedLoan', 'MoneyToRepayLoanPenghasilan', 'MoneyToRepayLoanPinjaman', 'MoneyToRepayLoanTanah', 'MoneyToRepayLoanTernak', 'MoneyToRepayLoanDeposito', 'MoneyToRepayLoanLainnya', 'ProfitSharingLoan', 'ResponsibilityLoan', 'WorryToRepayLoan', 'DifficultCoverExpenses', 'PostponeExpensesSewaRumah', 'PostponeExpensesKebun', 'PostponeExpensesMakanan', 'PostponeExpensesKesehatan', 'PostponeExpensesSosial', 'PostponeExpensesListrik', 'PostponeExpensesPendidikan', 'PostponeExpensesSandang', 'PostponeExpensesAngsuran', 'PostponeExpensesLainnya', 'DifficultSocialContributions', 'MoneyUrgentExpensesTabungan', 'MoneyUrgentExpensesMeminjamKeluarga', 'MoneyUrgentExpensesMeminjamTengkulak', 'MoneyUrgentExpensesMenjual', 'MoneyUrgentExpensesLainnya', 'CostUnsubsidizedFertilizer', 'OtherIncome', 'PensionPlan', 'OtherIncomeRegular', 'SourceOtherIncomeGajiTetap', 'SourceOtherIncomeGajiPasangan', 'SourceOtherIncomeUsaha', 'SourceOtherIncomeFamily', 'SourceOtherIncomeLainnya', 'AmountOtherIncome', 'CocoaProfitableBusiness', 'LoanBetterThanSaving', 'UnsubsidizedFertilizerProfitable', 'HighInterestRate', 'LoanWithTrader', 'BetterWetDriedBeans', 'GoodLoanClient', 'TrustGroupMembers', 'RepayLoanGroupMember', 'TrustBank', 'CocoaFarmPayExpenses', 'DiscipilinedSaveMoney', 'TradersRich', 'CollateralOfferedBank', 'ManyCocoaFarmSale', 'SatisfiedCocoaBusiness', 'PayCocoaBetterInterest', 'NeedLoan', 'MobilePhone', 'LoanAnalysisKnowledge', 'IslamicFinancialAwareness', 'LearnToSaveMoney', 'CocoaPriceToday', 'ReasonNotHavePhoneTidakButuh', 'ReasonNotHavePhoneMahal', 'ReasonNotHavePhoneSinyal', 'ReasonNotHavePhoneLainnya', 'ValueCocoaFarm', 'InsuranceKnowledge', 'PastNowInsurance', 'InsuranceTypeMotor', 'InsuranceTypePanen', 'InsuranceTypeBanjir', 'InsuranceTypeKemarau', 'InsuranceTypeMobil', 'InsuranceTypeKesehatan', 'InsuranceTypeJiwa', 'InsuranceTypeLainnya', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy',);
        $primary_key = array('FarmerID');
        $not_null = array('FarmerID');

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function family_post() {
        $post = $this->post(NULL);
        $table = 'ktv_family';
        $allowed_fields = array('FamilyID', 'FarmerID', 'OldFarmerID', 'AnggotaName', 'HubunganKeluarga', 'AnggotaAge', 'AnggotaGender', 'StatusSekolah', 'Photo', 'DateCreated', 'DateUpdated', 'LastModifiedBy', 'DateSynced', 'FamilyStatus', 'DeleteReason',);
        $primary_key = array(/* 'FamilyID', */'FarmerID', 'AnggotaName',);
        $not_null = array(/* 'FamilyID', */'FarmerID', 'AnggotaName',);

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function garden_status_post() {
        $post = $this->post(NULL);
        $table = 'ktv_farmer_garden_status';
        $allowed_fields = array('FarmerID', 'GardenNr', 'GardenStatus', 'Commodity', 'Remarks', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy',);
        $primary_key = array('FarmerID', 'GardenNr',);
        $not_null = array('FarmerID', 'GardenNr',);

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function other_land_post() {
        $post = $this->post(NULL);
        $table = 'ktv_farmer_other_land';
        $allowed_fields = array('FarmerID', 'Commodity', 'GardenHa', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy',);
        $primary_key = array('FarmerID', 'Commodity');
        $not_null = array('FarmerID', 'Commodity');

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function village_post() {
        $post = $this->post(NULL);
        $table = 'ktv_village';
        $allowed_fields = array('VillageID', 'Village', 'SubDistrictID', 'VillageHeadName', 'VillageHeadGender', 'VillageHeadLatitude', 'VillageHeadLongitude', 'LastModifiedBy', 'DateUpdated',);
        $primary_key = array('VillageID', 'SubDistrictID');
        $not_null = array('VillageID', 'Village', 'SubDistrictID');

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function village_crop_post() {
        $post = $this->post(NULL);
        $table = 'ktv_village_crop';
        $allowed_fields = array('VillageCropID', 'VillageID', 'CropName', 'CropYear', 'CropFarmers', 'CropHectares', 'CropProduction', 'CreatedBy', 'DateCreated', 'LastModifiedBy', 'DateUpdated',);
        $primary_key = array(/* 'VillageCropID', */'VillageID');
        $not_null = array(/* 'VillageCropID', */'VillageID', 'CropName');

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function village_infrastructure_post() {
        $post = $this->post(NULL);
        $table = 'ktv_village_infrastructure';
        $allowed_fields = array('InfrastructureID', 'VillageID', 'InfrastructureType', 'InfrastructureName', 'Latitude', 'Longitude', 'CreatedBy', 'DateCreated', 'LastModifiedBy', 'DateUpdated',);
        $primary_key = array(/* 'InfrastructureID', */'VillageID');
        $not_null = array(/* 'InfrastructureID', */'VillageID', 'InfrastructureType', 'InfrastructureName');

        $result = $this->msyn->process_data($post, $table, $primary_key, $allowed_fields, $not_null);
        $this->response($result, 200);
    }

    public function upload_farmer_photo_post() {
        $success = false;
        $message = '';

        // upload path after images/, with trailing slash
        $upload_path = 'Photo/';
        // echo "<pre>"; print_r($_FILES); echo "</pre>";
        // echo "<pre>"; print_r($this->post(NULL)); echo "</pre>"; exit;
        $FarmerID = $this->post('FarmerID');
        if (empty($FarmerID)) {
            $message = 'You must provide FarmerID to update photo';
        } else {
            $config['upload_path'] = 'images/' . $upload_path;
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '2048';
            // $config['max_width']        = '1024';
            // $config['max_height']       = '768';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                $message = $this->upload->display_errors('', '');
            } else {
                $upload_data = $this->upload->data();
                // echo "<pre>"; print_r($upload_data); echo "</pre>"; exit;
                $success = $this->msyn->updateFarmerPhoto($FarmerID, $upload_data, $upload_path);
                // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
            }
        }
        $this->response(array(
            'success' => $success,
            'message' => $message,
                ), 200);
    }

    public function fix_average_age_trees_get() {
        $result = $this->mtools->FixAverageAgeTrees();
        $this->response($result, 200);
    }

    public function assign_member_mill_by_fa_get() {
        $result = $this->mtools->AssignMemberMillByFa();
        $this->response($result, 200);
    }

    public function assign_member_mill_by_fa_2019_get() {
        $result = $this->mtools->AssignMemberMillByFa2019();
        $this->response($result, 200);
    }

    public function fix_name_member_dkk_get() {
        $result = $this->mtools->FixNameMemberDkk();
        $this->response($result, 200);
    }

    public function perbaiki_family_get() {
        $result = $this->msyn->perbaiki_family();
        $this->response($result, 200);
    }

    function syn_upload_get() {
        $path = 'files/upload';
        $path_to = 'files/backup/';
        $path_failed = 'files/failed/';
        $files = scandir($path);
        // echo "<pre>"; print_r($files); echo "</pre>"; exit;
        $this->load->library('Excel', null, 'PHPExcel');

        // for ($i = 2; $i < sizeof($files); $i++) {
        if (count($files) > 2) {
            for ($i = 2; $i < 12; $i++) {
                $nm = explode('.', $files[$i]);
                if ($nm[sizeof($nm) - 1] == 'csv') {

                    // validate file
                    $valid = true;
                    if (($handle = fopen($path . '/' . $files[$i], "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $num = count($data);
                            if ($num == 1) {
                                $valid = false;
                                break;
                            }
                        }
                        fclose($handle);
                    }

                    if (!$valid) {
                        rename($path . '/' . $files[$i], $path_failed . $files[$i]);
                        continue;
                    }
                    // end of validate file
                    $excel_data = $this->PHPExcel->import($path . '/' . $files[$i], false);
                    if (!empty($excel_data)) {
                        // $this->db->trans_start(FALSE);
                        $stat = $this->msyn->processSyn($excel_data, $nm[0]);
                        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
                        // $this->db->trans_rollback();
                    }

                    // // process file
                    // $csvData = file_get_contents($path . '/' . $files[$i]);
                    // //print_r($csvData);exit;
                    // $lines = explode(PHP_EOL, $csvData);
                    // $array = array();
                    // /* foreach ($lines as $line) {
                    //   $array[] = str_getcsv($line);
                    //   } */
                    // $a = 0;
                    // for ($j = 0; $j < sizeof($lines); $j++) {
                    //     $array[$a] = str_getcsv($lines[$j]);
                    //     if ($j > 0 and trim($lines[$j]) != '') {
                    //         $ar = $this->merge_line($array, $a, $lines, $j);
                    //         $array[$a] = $ar[0];
                    //         $j = $ar[1];
                    //     }
                    //     $a++;
                    // }
                    // $stat = $this->msyn->replaceData($nm[0], $array, $_SESSION['userid']);
                    // unset($array);
                    if ($stat) {
                        $stat = rename($path . '/' . $files[$i], $path_to . $files[$i]);
                    } else {
                        $stat = rename($path . '/' . $files[$i], $path_failed . $files[$i]);
                    }
                } else {
                    copy($path . '/' . $files[$i], $path_to . '/' . $name);
                    if (strpos(strtolower($files[$i]), 'frm_') !== false) {
                        $nmFile = explode('_', $files[$i]);
                        $foto = $this->msyn->getDataFoto(@$nmFile[1]);
                        @mkdir('images/Photo/' . @$foto['Province']);
                        //copy($path . '/' . $files[$i], $path_to . '/' . $name);
                        if ($nmFile[1] != "") {
                            $name = $files[$i];
                            copy($path . '/' . $files[$i], 'images/Photo/' . $foto['Province'] . '/' . $name);
                            $Photo = $foto['Province'] . '/' . $name;
                            $this->msyn->processSynPhoto($nmFile[1], $Photo);
                        }
                        unlink($path . '/' . $files[$i]);
                    } else if (strpos(strtolower($files[$i]), 'sigatt_') !== false) {
                        //copy($path . '/' . $files[$i], $path_to . '/' . $name);
                        $name = $files[$i];
                        copy($path . '/' . $files[$i], 'images/attendance_list_sign/' . $name);
                        unlink($path . '/' . $files[$i]);
                    } else if (strpos(strtolower($files[$i]), 'siglc_') !== false) {
                        //copy($path . '/' . $files[$i], $path_to . '/' . $name);
                        $nmFile = explode('_', $files[$i]);
                        $foto = $this->msyn->getDataFoto(@$nmFile[1]);
                        @mkdir('images/Photo/' . @$foto['Province']);
                        //copy($path . '/' . $files[$i], $path_to . '/' . $name);
                        if ($nmFile[1] != "") {
                            $name = $files[$i];
                            copy($path . '/' . $files[$i], 'images/learning_contract_sign/' . $name);
                            $this->msyn->processSynPhotoSign($nmFile[1], $name);
                            unlink($path . '/' . $files[$i]);
                        }
                    } else if (strpos(strtolower($files[$i]), 'sigcert_') !== false || strpos(strtolower($files[$i]), 'sigin_') !== false || strpos(strtolower($files[$i]), 'sigfm_') !== false || strpos(strtolower($files[$i]), 'sigim_') !== false || strpos(strtolower($files[$i]), 'sigco_') !== false) {
                        //copy($path . '/' . $files[$i], $path_to . '/' . $name);
                        $name = $files[$i];
                        copy($path . '/' . $files[$i], 'images/certification_sign/' . $name);
                        unlink($path . '/' . $files[$i]);
                    } else if (strpos(strtolower($files[$i]), 'att_') !== false) {
                        //copy($path . '/' . $files[$i], $path_to . '/' . $name);
                        $name = $files[$i];
                        copy($path . '/' . $files[$i], 'images/learning_contract_sign/' . $name);
                        unlink($path . '/' . $files[$i]);
                    } else {
                        $foto = $this->msyn->getDataFoto($nm[0]);
                        @mkdir('images/Photo/' . $foto['Province']);
                        if ($nm[0] != "") {
                            $name = 'frm_' . $nm[0] . '_' . date('YmdHis') . '.jpg';
                            copy($path . '/' . $files[$i], 'images/Photo/' . $foto['Province'] . '/' . $name);
                            $Photo = $foto['Province'] . '/' . $name;
                            $this->msyn->processSynPhoto($nm[0], $Photo);
                        }
                        unlink($path . '/' . $files[$i]);
                    }
                    //unlink($path . '/' . $files[$i]);
                }
            }
        }

        $result['success'] = $stat;
        $this->response($result, 200);
    }

    public function updateStatusPhotoPetani_get() {
        $result = $this->mpetani->updateStatusPhotoPetani();
        $this->response($result, 200);
    }

    public function information_grid_get() {
        echo $_SESSION['informationGrid'];
        exit;
    }

    public function survey_plot_polygon_upload_post() {
        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        if ($this->file['filezip']['name'] != '') {
            $upload = move_upload($this->file, 'files/survey_plot_polygon/' . $this->file['filezip']['name']);
            if (isset($upload['upload_data'])) {
                $statusProses = true;
                $msgProses = "File Uploaded";

                //insert ke tabel log
                $this->mlogupload->insertLogUploadPolygonSurveyPlot($this->file['filezip']['name']);
            } else {
                $statusProses = false;
                $msgProses = "File upload failed";
            }
        } else {
            $statusProses = false;
            $msgProses = "No file";
        }

        ini_set('memory_limit', $mem_ini);

        $result['success'] = $statusProses;
        $result['message'] = $msgProses;
        $this->response($result, 200);
    }

    public function survey_plot_polygon_post() {
        //ini_set('display_errors',true);
        //error_reporting(E_ALL);

        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        //tangkap nama file yg diproses
        if ($this->post('filename') != "") {

            //ambil namafile
            $namaFilefolder = str_replace('.zip', '', $this->post('filename'));

            $zipLib = new ZipArchive;
            $openZip = $zipLib->open('files/survey_plot_polygon/' . $this->post('filename'));
            if ($openZip == true) {
                $zipLib->extractTo('files/tmp/' . $namaFilefolder);
                $zipLib->close();

                //proses buka dan read file .json nya
                $jsonProsesString = file_get_contents('files/tmp/' . $namaFilefolder . '/geotrace.json');
                $jsonProses = json_decode($jsonProsesString, true);

                $prosesSave = $this->msyn->prosesUploadSurveyPlotPolygon($jsonProses, $this->post('filename'));
                if ($prosesSave['proses'] == true) {
                    $statusProses = true;
                    $msgProses = "Proses save polygon success, Detail : ".$prosesSave['labelProses'];
                    $this->msyn->moveFileToProcessedFolder($this->post('filename'), true);
                }else{
                    $statusProses = false;
                    $msgProses = "Proses save polygon failed";
                    $this->msyn->moveFileToProcessedFolder($this->post('filename'), false);
                }

                //hapus folder
                unlinkr("files/tmp/" . $namaFilefolder);
            } else {
                $statusProses = false;
                $msgProses = "No zip file found or cannot extract the file";
            }
        } else {
            $statusProses = false;
            $msgProses = "No filename";
        }

        ini_set('memory_limit', $mem_ini);
        $result['success'] = $statusProses;
        $result['message'] = $msgProses;
        $this->response($result, 200);
        exit;
    }

    public function auto_assign_data_control_get() {
        $result = $this->msyn->autoAssignDataControl();
        $this->response($result, 200);
    }

    public function kml_farmers_get() {
        $this->response($this->mgps->getKMLFarmers($this->get('start'), $this->get('limit')), 200);
    }

    public function upload_kml_post() {
        $config['upload_path'] = './files/upload/kml';
        $config['allowed_types'] = '*';
        $config['max_size'] = 8192;

        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        if ($ext !== 'kml') {
            $this->response(array('success' => false, 'msg' => lang('Invalid file type.')), 200);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $data = array('error' => $this->upload->display_errors());
            $this->response(array('success' => false, 'msg' => $this->upload->display_errors()), 200);
        } else {
            $data = $this->upload->data();
            $this->mgps->importKMLtmp($data['full_path']);
            @unlink($data['full_path']);
            $this->response(array('success' => true, 'msg' => ''), 200);
        }
    }

    public function update_kml_post() {
        $this->response($this->mgps->updateKML(), 200);
    }

    public function gps_status_get() {
        $this->response($this->mgps->getGPSStatus($this->get('start'), $this->get('limit')), 200);
    }

    public function gps_status_post() {
        $config['upload_path'] = './files/upload/gps_status';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = 8 * 1024;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $data = array('error' => $this->upload->display_errors());
            $this->response(array('success' => false, 'msg' => $this->upload->display_errors()), 200);
        } else {
            $data = $this->upload->data();
            $this->mgps->importGPSStatus($data['full_path']);
            $this->response(array('success' => true, 'msg' => ''), 200);
        }
    }

    public function update_gps_status_post() {
        $this->response($this->mgps->updateGPSStatus(), 200);
    }

    public function check_gps_position_farmer_garden_get() {
        $isSendEmail = false;
        $dataIndraHulu = $this->mgps->checkGPSFarmerGarden(1402);
        $dataIndraHilir = $this->mgps->checkGPSFarmerGarden(1404);
        $dataMerangin = $this->mgps->checkGPSFarmerGarden(1502);
        $dataMuaroJambi = $this->mgps->checkGPSFarmerGarden(1505);

        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';
        ini_set('memory_limit', '-1');

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
                ->setLastModifiedBy("PT Koltiva")
                ->setTitle("Out of Position Farmer Garden")
                ->setSubject("Out of Position Farmer Garden")
                ->setDescription("Out of Position Farmer Garden")
                ->setKeywords("Out of Position Farmer Garden")
                ->setCategory("Out of Position Farmer Garden");

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Data');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //set style  (begin)
        $styleFont = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            ),
        );

        $styleFontBold = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
        );

        $styleFontBoldMainTitle = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '11',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldTitle = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        //set style  (end)
        //tulis judul & informasi detail lainnya
        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'District Indragiri Hulu');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleFontBoldMainTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:E2');

        //tabel header
        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'MemberID');
        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'MemberDisplayID');
        $objPHPExcel->getActiveSheet()->setCellValue('D3', 'MemberName');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', 'PlotNr');
        $objPHPExcel->getActiveSheet()->setCellValue('F3', 'SurveyNr');
        $objPHPExcel->getActiveSheet()->setCellValue('G3', 'GPS Location');
        $objPHPExcel->getActiveSheet()->getStyle('B3:G3')->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B3:G3')->applyFromArray($styleBorderFull, false);

        $increRowExcel = 4;
        foreach ($dataIndraHulu as $val) {
            $isSendEmail = true;

            $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, $val['MemberID']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $increRowExcel, $val['MemberDisplayID']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $increRowExcel, $val['MemberName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $increRowExcel, $val['PlotNr']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $increRowExcel, $val['SurveyNr']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $increRowExcel, $val['GPSLocation']);

            $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleBorderFull, false);

            $increRowExcel++;
        }
        $increRowExcel += 2;


        //tulis judul & informasi detail lainnya
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, 'District Indragiri Hilir');
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel)->applyFromArray($styleFontBoldMainTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $increRowExcel . ':E' . $increRowExcel);
        $increRowExcel++;

        //tabel header
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, 'MemberID');
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $increRowExcel, 'MemberDisplayID');
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $increRowExcel, 'MemberName');
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $increRowExcel, 'PlotNr');
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $increRowExcel, 'SurveyNr');
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $increRowExcel, 'GPS Location');
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleBorderFull, false);
        $increRowExcel++;

        foreach ($dataIndraHilir as $val) {
            $isSendEmail = true;

            $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, $val['MemberID']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $increRowExcel, $val['MemberDisplayID']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $increRowExcel, $val['MemberName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $increRowExcel, $val['PlotNr']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $increRowExcel, $val['SurveyNr']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $increRowExcel, $val['GPSLocation']);

            $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleBorderFull, false);

            $increRowExcel++;
        }
        $increRowExcel += 2;


        //tulis judul & informasi detail lainnya
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, 'District Merangin');
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel)->applyFromArray($styleFontBoldMainTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $increRowExcel . ':E' . $increRowExcel);
        $increRowExcel++;

        //tabel header
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, 'MemberID');
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $increRowExcel, 'MemberDisplayID');
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $increRowExcel, 'MemberName');
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $increRowExcel, 'PlotNr');
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $increRowExcel, 'SurveyNr');
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $increRowExcel, 'GPS Location');
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleBorderFull, false);
        $increRowExcel++;

        foreach ($dataMerangin as $val) {
            $isSendEmail = true;

            $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, $val['MemberID']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $increRowExcel, $val['MemberDisplayID']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $increRowExcel, $val['MemberName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $increRowExcel, $val['PlotNr']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $increRowExcel, $val['SurveyNr']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $increRowExcel, $val['GPSLocation']);

            $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleBorderFull, false);

            $increRowExcel++;
        }
        $increRowExcel += 2;


        //tulis judul & informasi detail lainnya
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, 'District Muaro Jambi');
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel)->applyFromArray($styleFontBoldMainTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $increRowExcel . ':E' . $increRowExcel);
        $increRowExcel++;

        //tabel header
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, 'MemberID');
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $increRowExcel, 'MemberDisplayID');
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $increRowExcel, 'MemberName');
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $increRowExcel, 'PlotNr');
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $increRowExcel, 'SurveyNr');
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $increRowExcel, 'GPS Location');
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleBorderFull, false);
        $increRowExcel++;

        foreach ($dataMuaroJambi as $val) {
            $isSendEmail = true;

            $objPHPExcel->getActiveSheet()->setCellValue('B' . $increRowExcel, $val['MemberID']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $increRowExcel, $val['MemberDisplayID']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $increRowExcel, $val['MemberName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $increRowExcel, $val['PlotNr']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $increRowExcel, $val['SurveyNr']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $increRowExcel, $val['GPSLocation']);

            $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $increRowExcel . ':' . 'G' . $increRowExcel)->applyFromArray($styleBorderFull, false);

            $increRowExcel++;
        }
        $increRowExcel += 2;

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $namaFileExcelForAttach = date('YmdHis') . '_OutOfPosition.xlsx';
        $objWriter->save('files/tmp/' . $namaFileExcelForAttach);

        /*
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename="'.date('YmdHis').'_OutOfPosition.xlsx');
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
          $objWriter->save('php://output');
          exit;
         */

        if ($isSendEmail == true) {
            //Kirim emailnya
            require_once 'application/third_party/phpmailer-hr/class.phpmailer.php';
            $this->config->load('email');

            $ObjMail = new PHPMailer();
            $ObjMail->IsSMTP();
            $ObjMail->SMTPSecure = 'tls';
            $ObjMail->SMTPAuth = true;
            $ObjMail->Host = $this->config->item('smtp_host');
            $ObjMail->Port = $this->config->item('smtp_port');
            $ObjMail->Username = $this->config->item('smtp_user');
            $ObjMail->Password = $this->config->item('smtp_pass');

            $ObjMail->Priority = 0;
            $ObjMail->SetFrom($this->config->item('email_from'), 'Koltiva Support');

            $str = '';
            $str .= 'Yth. Palmoiltrace Admins,<br/><br/>';
            $str .= 'Berikut adalah data Petani yang titik koordinat kebunnya diluar distrik nya untuk tanggal ' . date('Y-m-d') . ' <br/>';

            $str .= "<br/>Salam Hangat,<br/>";
            $str .= "<br/>&copy; PalmoilTrace.<br/>";

            $ObjMail->Subject = 'Palmoiltrace - Out Of Position Farmer Garden (' . date('Y-m-d') . ')';
            $ObjMail->Body = $str;
            $ObjMail->IsHTML(true);

            $ObjMail->AddAddress('noersa.eka@koltiva.com', 'Eka');
            $ObjMail->AddCC('nikolius.lau@koltiva.com');
            $ObjMail->AddCC('furqonuddin.ramdhani@koltiva.com');
            $ObjMail->AddCC('wikha.jily@koltiva.com');
            $ObjMail->AddCC('ashadi.perwira@koltiva.com');
            $ObjMail->AddCC('sinta.ayu@koltiva.com');

            if (file_exists('files/tmp/' . $namaFileExcelForAttach)) {
                $ObjMail->AddAttachment('files/tmp/' . $namaFileExcelForAttach);
            }

            $result = $ObjMail->Send();
            $ObjMail->ClearAddresses();
            $ObjMail->ClearAllRecipients();
            $ObjMail->IsHTML(false);

            //hapus filenya
            if (file_exists('files/tmp/' . $namaFileExcelForAttach)) {
                @unlink('files/tmp/' . $namaFileExcelForAttach);
            }
        }

        $this->response(array('success' => true, 'msg' => 'Success'), 200);
    }

    function checkRestrictedArea_get($alldata = 'false') {

        $data = $this->mgps->runCheckProtectedForest($alldata);
        echo "<pre>";
        print_r('selesai');
        echo "</pre>";
        exit;
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    private function ConvertValueImportExcel($valueNya) {
        $valueNya = trim($valueNya);
        if ($valueNya == "" || $valueNya == "0") {
            return null;
        } else {
            return trim($valueNya);
        }
    }

    public function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public function import_region_data_get() {
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        ini_set('display_errors', true);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

        $inputFileName = 'files/region.xlsx';
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $ExcelResult = $objReader->load($inputFileName);
        //echo '<pre>'; print_r($ExcelResult); exit;
        //data siap insert
        $DataInsert = array();

        $break = false;
        $data = array();
        $start = 3;
        $incre = 0;
        while (!$break) {
            if ($start == 1) {
                $start++;
                continue;
            }

            if (trim($ExcelResult->getActiveSheet()->getCell('B' . $start)->getFormattedValue()) != '') {

                $ProvinceID = $this->ConvertValueImportExcel($ExcelResult->getActiveSheet()->getCell('B' . $start)->getFormattedValue());
                $Province = $this->ConvertValueImportExcel($ExcelResult->getActiveSheet()->getCell('C' . $start)->getFormattedValue());
                $District = $this->ConvertValueImportExcel($ExcelResult->getActiveSheet()->getCell('E' . $start)->getFormattedValue());
                $SubDistrict = $this->ConvertValueImportExcel($ExcelResult->getActiveSheet()->getCell('G' . $start)->getFormattedValue());
                $Village = $this->ConvertValueImportExcel($ExcelResult->getActiveSheet()->getCell('I' . $start)->getFormattedValue());

                //masukkan variabel
                $DataInsert[$incre]['ProvinceID'] = $ProvinceID;
                $DataInsert[$incre]['Province'] = $Province;
                $DataInsert[$incre]['District'] = $District;
                $DataInsert[$incre]['SubDistrict'] = $SubDistrict;
                $DataInsert[$incre]['Village'] = $Village;

                $start++;
                $incre++;
            } else {
                //keluar loop
                $break = true;
            }
        }
        //echo '<pre>'; print_r($DataInsert); exit;
        //remove data insert yg duplikat
        //$DataInsert = $this->unique_multidim_array($DataInsert, 'SubDistrictID');
        //$DataInsert = array_values($DataInsert); //reindex array
        //echo '<pre>'; print_r($DataInsert); exit;

        $proses = $this->mtools->InsertDataRegionImport($DataInsert);
        $this->response($proses, 200);
    }

    //============================== WIDGET (Begin) =================================================//
    public function widget_select_member_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';

        //get param
        $pSearch = array(
            'TextSearch' => $this->get('TextSearch'),
            'ExceptionID' => $this->get('ExceptionID'),
            'ListType' => $this->get('ListType'),
            'ProvinceID' => $this->get('ProvinceID'),
            'DistrictID' => $this->get('DistrictID'),
            'SubDistrictID' => $this->get('SubDistrictID'),
            'VillageID' => $this->get('VillageID')
        );

        $data = $this->mtools->GetGridSelectMember($pSearch, $this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($data, 200);
    }
    //============================== WIDGET (End)   =================================================//

    public function uploadzip_post() {
        ini_set('memory_limit', '-1');
        // var_dump($_FILES); die;
        if ($this->file['filezip']['name'] != '') {
            $upload = move_upload($this->file, 'files/uploadzip/' . $this->file['filezip']['name']);
            if (isset($upload['upload_data'])) {
                //Update ke tabel
                $proses = $this->mtools->InputUploadZip($this->file['filezip']['name']);
                if ($proses['success'] == false) {
                    //Hapus File
                    @unlink('files/uploadzip/' . $this->file['filezip']['name']);

                    $StatusProses = false;
                    $MsgProses = $proses['message'];
                } else {
                    $StatusProses = true;
                    $MsgProses = "File upload success";
                }
            } else {
                $StatusProses = false;
                $MsgProses = "File upload failed";
            }
        } else {
            $StatusProses = false;
            $MsgProses = "No file found, file parameter name is 'filezip'";
        }

        $result['success'] = $StatusProses;
        $result['message'] = $MsgProses;
        $this->response($result, 200);
    }

    public function process_uploadzip_get() {
        $proses = $this->mtools->ProcessUploadZip('CronNormal');
        $this->response($proses, 200);
    }

    public function process_uploadzip_daily_get() {
        $proses = $this->mtools->ProcessUploadZip('CronDaily');
        $this->response($proses, 200);
    }

    /** STAFF ACTIVITY ========================== (Begin) */
    public function api_staff_activity_data_post() {
        ini_set('memory_limit', '-1');

        if ($this->file['filezip']['name'] != '') {
            $upload = move_upload($this->file, 'files/uploadzip/' . $this->file['filezip']['name']);
            if (isset($upload['upload_data'])) {
                //Update ke tabel
                $proses = $this->mtools->ProcessStaffActData('files/uploadzip/' . $this->file['filezip']['name']);
                if ($proses['status'] == true) {
                    $statusProses = true;
                    $msgProses = $proses['message'];
                } else {
                    $statusProses = false;
                    $msgProses = $proses['message'];
                }
            } else {
                $statusProses = false;
                $msgProses = "File upload failed";
            }
        } else {
            $statusProses = false;
            $msgProses = "No file";
        }

        $result['success'] = $statusProses;
        $result['message'] = $msgProses;
        if ($statusProses == true) {
            $this->response($result, 200);
        } else {
            $this->response($result, 400);
        }
    }

    public function api_uploadzip_post() {
        //khusus file staff_activity
        ini_set('memory_limit', '-1');

        if ($this->file['filezip']['name'] != '') {
            $upload = move_upload($this->file, 'files/uploadzip/' . $this->file['filezip']['name']);
            if (isset($upload['upload_data'])) {
                //Update ke tabel
                $proses = $this->mtools->InputApiFileuploadStaffActivity('files/uploadzip/' . $this->file['filezip']['name']);
                if ($proses['status'] == true) {
                    $statusProses = true;
                    $msgProses = $proses['message'];
                } else {
                    $statusProses = false;
                    $msgProses = $proses['message'];
                }
            } else {
                $statusProses = false;
                $msgProses = "File upload failed";
            }
        } else {
            $statusProses = false;
            $msgProses = "No file";
        }

        $result['success'] = $statusProses;
        $result['message'] = $msgProses;
        if ($statusProses == true) {
            $this->response($result, 200);
        } else {
            $this->response($result, 400);
        }
    }

    /** STAFF ACTIVITY ========================== (End) */

    /** FIX DATA ========================== (Begin) */
    public function fix_harvest_months_data_get() {
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $proses = $this->mtools->FixDataHarvestMonthsData();
        $this->response($proses, 200);
    }

    public function recalculate_production_data_get() {
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $proses = $this->mtools->RecalculateProductionData();
        $this->response($proses, 200);
    }

    public function gen_new_member_display_id_get() {
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $proses = $this->mtools->GenNewMemberDisplayID();
        $this->response($proses, 200);
    }
    /** FIX DATA ========================== (End) */


    /*
    * Import Garden ================================================================ (Begin)
    */
    public function import_gardens_grid_get(){
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');
        $KeySearch = $this->get('KeySearch');

        $data = $this->mgarden->ImportGardenGrid($KeySearch, $start, $limit);
        $this->response($data, 200);
    }

    public function import_garden_upload_post() {
        require_once 'application/third_party/PHPExcel18/PHPExcel.php';
        require_once 'application/third_party/PHPExcel18/PHPExcel/IOFactory.php';

        //Ambil Ext
        $arrTemp = explode(".", $this->file['Koltiva_view_ImportGardens_MainGrid-MainGrid-Form-FileInput']['name']);
        $tempExtNya = strtolower(array_values(array_slice($arrTemp, -1))[0]);
        $arrTempExt = explode("?", $tempExtNya);
        $extNya = $arrTempExt[0];

        if (in_array($extNya, array('xlsx'))) {
            if (file_exists($this->file['Koltiva_view_ImportGardens_MainGrid-MainGrid-Form-FileInput']['tmp_name'])) {
                $inputFileName = $this->file['Koltiva_view_ImportGardens_MainGrid-MainGrid-Form-FileInput']['tmp_name'];
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $excelResult = $objReader->load($inputFileName);

                // Init data
                $data = array();

                $break = false;
                $error = false;
                $start = 1;
                $incre = 0;
                while (!$break) {
                    if ($start == 1) {
                        $start++;
                        continue;
                    }

                    if (trim($excelResult->getActiveSheet()->getCell('A' . $start)->getFormattedValue()) != '') {
                        $MemberID     = $this->convertDefault($excelResult->getActiveSheet()->getCell('A' . $start)->getFormattedValue());
                        $PlotNr       = $this->convertDefault($excelResult->getActiveSheet()->getCell('B' . $start)->getFormattedValue());
                        $SurveyNr     = $this->convertDefault($excelResult->getActiveSheet()->getCell('C' . $start)->getFormattedValue());
                        $Latitude     = $this->convertDefault($excelResult->getActiveSheet()->getCell('D' . $start)->getFormattedValue());
                        $Longitude    = $this->convertDefault($excelResult->getActiveSheet()->getCell('E' . $start)->getFormattedValue());
                        $GardenAreaHa = $this->convertDefault($excelResult->getActiveSheet()->getCell('F' . $start)->getFormattedValue());

                        //masukkan variabel
                        $data[$incre]['MemberID'] = $MemberID;
                        $data[$incre]['PlotNr'] = $PlotNr;
                        $data[$incre]['SurveyNr'] = $SurveyNr;
                        $data[$incre]['Latitude'] = $Latitude;
                        $data[$incre]['Longitude'] = $Longitude;
                        $data[$incre]['GardenAreaHa'] = $GardenAreaHa;

                        $start++;
                        $incre++;
                    } else {
                        //keluar loop
                        $break = true;
                    }
                }
                $proses = $this->mgarden->InsertGardenTmp($data);
                if ($proses['success'] == true) {
                    $this->response($proses, 200);
                } else {
                    $this->response($proses, 200);
                }
            } else {
                $proses['success'] = false;
                $proses['message'] = lang('File tidak ditemukan, pastikan file tidak terlalu besar');
                $this->response($proses, 400);
            }
        } else {
            $proses['success'] = false;
            $proses['message'] = lang('Invalid file type, file type must be .xlsx');
            $this->response($proses, 400);
        }
    }

    public function import_garden_post() {
        $this->load->model('mmiddleware');
        $callback = $this->mgarden->InsertGarden();
        if ($callback['success'] == true) {
            $uid = 'nQxNqbkCil1'; // push by program
            $onlyNew = true;
            foreach ($callback['mid'] as $key => $value) {
                $programs = $this->mmiddleware->getAllProgramWithView($uid);
                if (count($programs) > 0) {
                    foreach ($programs as $progkeys => $program) {
                        $datas = $this->mmiddleware->getDataBy(
                                                        $onlyNew, 
                                                        $program['uid'], 
                                                        array(
                                                            'MemberID' =>$value['MemberID'], 
                                                            'PlotNr'   =>$value['PlotNr'], 
                                                            'SurveyNr' =>$value['SurveyNr'], 
                                                            'long'     => $value['Longitude'], 
                                                            'lat'      => $value['Latitude']
                                                        )
                                                    );
                        $this->mmiddleware->syncDataPerProgram($datas, $program['uid']);
                    }
                }
            }
            $this->response($callback, 200);
        } else {
            $this->response($callback, 400);
        }
    }
    /*
    * Import Garden ================================================================= (End)
    */
    
    /*============================================================== Script migrasi user Palm Cognito (Begin) =================================================*/
    public function migrasi_cognito_prep_data_proses_get() {
        $this->load->model('tools/mtoolscognito');        

        //Input data dari excel dan samakan dengan yg ada di database
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $result = array();
        $result['success'] = true;
        $messageReturn = '';

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        $inputFileName = 'files/tmp/migrasicog.xlsx';
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $excelResult = $objReader->load($inputFileName);

        //data siap insert
        $dataInsert = array();

        $break = false;
        $data = array();
        $start = 1;
        $incre = 0;

        while (!$break) {
            if ($start == 1) {
                $start++;
                continue;
            }

            if (trim($excelResult->getActiveSheet()->getCell('A' . $start)->getFormattedValue()) != '') {
                $UserID = $this->convertDefault($excelResult->getActiveSheet()->getCell('A' . $start)->getFormattedValue());
                $Name = $this->convertDefault($excelResult->getActiveSheet()->getCell('B' . $start)->getFormattedValue());
                $Email = $this->convertDefault($excelResult->getActiveSheet()->getCell('C' . $start)->getFormattedValue());
                $Phonenumber = $this->convertDefault($excelResult->getActiveSheet()->getCell('D' . $start)->getFormattedValue());
                $PredefPass = $this->convertDefault($excelResult->getActiveSheet()->getCell('E' . $start)->getFormattedValue());
                $UpdateDhis = $this->convertDefault($excelResult->getActiveSheet()->getCell('F' . $start)->getFormattedValue());

                //masukkan variabel
                $dataInsert[$incre]['UserID'] = $UserID;
                $dataInsert[$incre]['Name'] = $Name;
                $dataInsert[$incre]['Email'] = $Email;
                $dataInsert[$incre]['Phonenumber'] = $Phonenumber;
                $dataInsert[$incre]['PredefPass'] = $PredefPass;
                $dataInsert[$incre]['UpdateDhis'] = $UpdateDhis;

                $start++;
                $incre++;
            }else{
                //keluar loop
                $break = true;
            }
        }

        //echo '<pre>'; print_r($dataInsert); exit;
        $prosesInsert = $this->mtoolscognito->ImportPrepDataProcess($dataInsert);

        $messageReturn = 'Import success, data bisa dilihat dan compare dengan menjalan query dibawah ini';
        $messageReturn = $messageReturn."<br><pre>SELECT
                                                    a.`UserIdExcel`
                                                    , a.`NameExcel`
                                                    , a.`EmailExcel`
                                                    , a.`PhonenumberExcel`
                                                    , b.`UserName` AS 'sys_user.UserName'
                                                    , b.`UserRealName` AS 'sys_user.UserRealName'
                                                    , b.`UserEmail` AS 'sys_user.UserEmail'
                                                    , c.`PersonNm` AS  'ktv_persons.PersonNm'
                                                    , c.`Email` AS 'ktv_persons.Email'
                                                    , c.`OfficialEmail` AS 'ktv_persons.OfficialEmail'
                                                    , c.`OfficialCellPhone` AS 'ktv_persons.OfficialCellPhone'
                                                    , d.`OfficialPhone` AS 'ktv_staffs.OfficialPhone'
                                                    , d.`WorkPhone` AS 'ktv_staffs.WorkPhone'
                                                    , d.`OfficialEmail` AS 'ktv_staffs.OfficialEmail'
                                                FROM
                                                    tmp_migcog_proses a
                                                    LEFT JOIN sys_user b ON a.`UserIdExcel` = b.`UserId`
                                                    LEFT JOIN ktv_persons c ON b.`UserId` = c.`UserID`
                                                    LEFT JOIN ktv_staffs d ON c.`PersonID` = d.`PersonID`</pre>";

        $result['message'] = $messageReturn;
        echo $messageReturn; exit;
        //$this->response($result, 200);
    }
    /*============================================================== Script migrasi user Palm Cognito (End) =================================================*/

    //proses all data excel ============================================================== (Begin)
    public function migrasi_cognito_make_active_status_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $this->load->model('staff/mtoolscognito');

        $result = $this->mtoolscognito->MakeStatusActiveAndGenPhoneNumber();
        $this->response($result, 200);
    }

    //Cek phonenumber duplikat disini, harus dibereskan dulu agar di tabel tmp_migcog_proses, tidak ada no telepon duplikat
    /*
    Querynya = SELECT
        a.`PhonenumberExcel`
        , COUNT(a.`UserIdExcel`) AS Jumlah
    FROM
        `tmp_migcog_proses` a
    WHERE 1=1
    GROUP BY a.`PhonenumberExcel`
    HAVING Jumlah > 1
    */

    public function migrasi_cognito_samakan_basic_data_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $this->load->model('staff/mtoolscognito');

        $result = $this->mtoolscognito->SamakanBasicData();
        $this->response($result, 200);
    }

    public function migrasi_cognito_clean_status_notactive_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $this->load->model('staff/mtoolscognito');

        $result = $this->mtoolscognito->CleanStatusNotActive();
        $this->response($result, 200);
    }
    //proses all data excel ============================================================== (Begin)

    //JALANkan DHIS dulu, baru migrasi cognito
    public function migrasi_cognito_update_pass_dhis_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $this->load->model('staff/mtoolscognito');

        $result = $this->mtoolscognito->MigCogUpdatePassDhis();
        $this->response($result, 200);
    }

    public function migrasi_cognito_staff_koltiva_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $this->load->model('staff/mtoolscognito');
        //echo passwordGenerator(8); exit;
        //echo usernameOfEmail('nikolius.lau@koltiva.com'); exit;

        //STAFF KOLTIVA, Bisa CREATE (SEND EMAIL CONFIRM - RANDOM PASSWORD) / LINK

        $result = $this->mtoolscognito->MigCogStaffKoltiva($this->get('userid'));
        $this->response($result, 200);
    }

    public function migrasi_cognito_noemailnfixed_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $this->load->model('staff/mtoolscognito');

        //BISA CREATE (NOTSEND EMAIL CONFIRM - FIXED PASSWORD) && LINK

        $result = $this->mtoolscognito->MigCogNoemailnfixed($this->get('userid'));
        $this->response($result, 200);
    }

    public function migrasi_cognito_emailconfnrandom_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $this->load->model('staff/mtoolscognito');

        //BISA CREATE (SEND EMAIL CONFIRM - RANDOM PASSWORD) && LINK

        $result = $this->mtoolscognito->MigCogEmailconfnrandom($this->get('userid'));
        $this->response($result, 200);
    }

    public function migrasi_cognito_predefinedpass_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $this->load->model('staff/mtoolscognito');

        //BISA CREATE (SEND EMAIL CONFIRM - RANDOM PASSWORD FROM EXCEL) && NO LINK (CREATE ONLY)

        $result = $this->mtoolscognito->MigCogPredefPass($this->get('userid'));
        $this->response($result, 200);
    }
    
    public function migrasi_cognito_fix_notelp_get() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $this->load->model('staff/mtoolscognito');

        $result = $this->mtoolscognito->FixNotelp();
        $this->response($result, 200);
    }

    public function migrasi_location_plot_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        //Select Semua Plot Yang Mempunyai Titik GPS dan Belum ada Spatial Fieldnya
        $sql = "SELECT
            sp.MemberID,
            sp.PlotNr,
            sp.SurveyNr,
            sp.Latitude,
            sp.Longitude
	    FROM
		    ktv_survey_plot sp 
	    WHERE
            sp.StatusCode = 'active' 
            AND ABS(sp.Latitude) > 0 
            AND ABS(sp.Longitude) > 0 
            AND sp.LatLong IS NULL";

        $query = $this->db->query($sql);

        $no = 0;
        if($query->num_rows()>0){
            foreach($query->result_array() as $row){
                //Koordinat Geometry ============= (Begin)
                if($row['Latitude'] != "" && $row['Longitude'] != "") {

                    $LatitudeProses = (float) $row['Latitude'];
                    $LongitudeProses = (float) $row['Longitude'];
                    
                    //Check Latitude
                    if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                        //Cek valid tidak koordinatnya
                        $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                        $DataCekKoordinat = $this->db->query($sql2)->row_array();
                        
                        if ($DataCekKoordinat['HasilCek'] == "1") {
                            $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                            $sql2 = "UPDATE ktv_survey_plot a SET
                                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                    WHERE
                                        a.`MemberID` = ?
                                        AND a.`PlotNr` = ?
                                        AND a.`SurveyNr` = ?
                                    LIMIT 1";
                            $p = array(
                                $row["MemberID"],
                                $row["PlotNr"],
                                $row["SurveyNr"]
                            );
                            $query = $this->db->query($sql2,$p);

                            if($query){
                                $sql2 = "UPDATE ktv_survey_plot_status a SET
                                            a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                        WHERE
                                            a.`MemberID` = ?
                                        AND a.`PlotNr` = ?
                                        LIMIT 1";
                                $p = array(
                                    $row["MemberID"],
                                    $row["PlotNr"]
                                );
                                $query = $this->db->query($sql2,$p);
                                $no++;
                            }
                        }

                    }
                }
            }
        }

        if($no > 0){
            $response = array("success"=>true,"Coordinate Converted"=>$no);
        }else{
            $response = array("success"=>false,"Coordinate Convertedted"=>$no);
        }

        $this->response($response, 200);
    }

    public function migrasi_location_plot_sme_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        //Select Semua Plot Yang Mempunyai Titik GPS dan Belum ada Spatial Fieldnya
        $sql = "SELECT
            sp.MemberID,
            sp.PlotNr,
            sp.SurveyNr,
            sp.Latitude,
            sp.Longitude
	    FROM
            ktv_survey_plot_sme sp 
	    WHERE
            sp.StatusCode = 'active' 
            AND ABS(sp.Latitude) > 0 
            AND ABS(sp.Longitude) > 0 
            AND sp.LatLong IS NULL";

        $query = $this->db->query($sql);

        $no = 0;
        if($query->num_rows()>0){
            foreach($query->result_array() as $row){
                //Koordinat Geometry ============= (Begin)
                if($row['Latitude'] != "" && $row['Longitude'] != "") {

                    $LatitudeProses = (float) $row['Latitude'];
                    $LongitudeProses = (float) $row['Longitude'];
                    
                    //Check Latitude
                    if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                        //Cek valid tidak koordinatnya
                        $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                        $DataCekKoordinat = $this->db->query($sql2)->row_array();
                        
                        if ($DataCekKoordinat['HasilCek'] == "1") {
                            $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                            $sql2 = "UPDATE ktv_survey_plot_sme a SET
                                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                    WHERE
                                        a.`MemberID` = ?
                                        AND a.`PlotNr` = ?
                                        AND a.`SurveyNr` = ?
                                    LIMIT 1";
                            $p = array(
                                $row["MemberID"],
                                $row["PlotNr"],
                                $row["SurveyNr"]
                            );
                            $query = $this->db->query($sql2,$p);

                            if($query){
                                $sql2 = "UPDATE ktv_survey_plot_status_sme a SET
                                            a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                        WHERE
                                            a.`MemberID` = ?
                                        AND a.`PlotNr` = ?
                                        LIMIT 1";
                                $p = array(
                                    $row["MemberID"],
                                    $row["PlotNr"]
                                );
                                $query = $this->db->query($sql2,$p);
                                $no++;
                            }
                        }

                    }
                }
            }
        }

        if($no > 0){
            $response = array("success"=>true,"Coordinate Converted"=>$no);
        }else{
            $response = array("success"=>false,"Coordinate Convertedted"=>$no);
        }

        $this->response($response, 200);
    }

    public function migrasi_location_member_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        //Select Semua Member Yang Mempunyai Titik GPS dan Belum ada Spatial Fieldnya
        $sql = "SELECT
                m.MemberID,
                m.Latitude,
                m.Longitude,
                m.LatLong 
            FROM
                ktv_members	m
            WHERE
                m.StatusCode = 'active' 
                AND ABS(m.Latitude) > 0 
                AND ABS(m.Longitude) > 0 
                AND m.LatLong IS NULL
            ORDER BY m.LatLong DESC";

        $query = $this->db->query($sql);

        $no = 0;
        if($query->num_rows()>0){
            foreach($query->result_array() as $row){
                //Koordinat Geometry ============= (Begin)
                if($row['Latitude'] != "" && $row['Longitude'] != "") {

                    $LatitudeProses = (float) $row['Latitude'];
                    $LongitudeProses = (float) $row['Longitude'];
                    
                    //Check Latitude
                    if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                        //Cek valid tidak koordinatnya
                        $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                        $DataCekKoordinat = $this->db->query($sql2)->row_array();
                        
                        if ($DataCekKoordinat['HasilCek'] == "1") {
                            $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                            $sql2 = "UPDATE ktv_members a SET
                                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                    WHERE
                                        a.`MemberID` = ?
                                    LIMIT 1";
                            $p = array(
                                $row["MemberID"]
                            );
                            $query = $this->db->query($sql2,$p);

                            if($query){
                                $no++;
                            }
                        }

                    }
                }
            }
        }

        if($no > 0){
            $response = array("success"=>true,"Coordinate Converted"=>$no);
        }else{
            $response = array("success"=>false,"Coordinate Convertedted"=>$no);
        }

        $this->response($response, 200);
    }

    public function migrasi_location_trader_warehouse_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        //Select Semua Member Yang Mempunyai Titik GPS dan Belum ada Spatial Fieldnya
        $sql = "SELECT
                m.MemberID,
                m.WarehousesNr,
                m.Latitude,
                m.Longitude,
                m.LatLong 
            FROM
                ktv_trader_warehouses m
            WHERE
                m.StatusCode = 'active' 
                AND ABS(m.Latitude) > 0 
                AND ABS(m.Longitude) > 0 
                AND m.LatLong IS NULL
            ORDER BY m.LatLong DESC";

        $query = $this->db->query($sql);

        $no = 0;
        if($query->num_rows()>0){
            foreach($query->result_array() as $row){
                //Koordinat Geometry ============= (Begin)
                if($row['Latitude'] != "" && $row['Longitude'] != "") {

                    $LatitudeProses = (float) $row['Latitude'];
                    $LongitudeProses = (float) $row['Longitude'];
                    
                    //Check Latitude
                    if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                        //Cek valid tidak koordinatnya
                        $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                        $DataCekKoordinat = $this->db->query($sql2)->row_array();
                        
                        if ($DataCekKoordinat['HasilCek'] == "1") {
                            $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                            $sql2 = "UPDATE ktv_trader_warehouses a SET
                                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                    WHERE
                                        a.`MemberID` = ?
                                        AND a.`WarehousesNr` = ?
                                    LIMIT 1";
                            $p = array(
                                $row["MemberID"],
                                $row["WarehousesNr"]
                            );
                            $query = $this->db->query($sql2,$p);

                            if($query){
                                $no++;
                            }
                        }

                    }
                }
            }
        }

        if($no > 0){
            $response = array("success"=>true,"Coordinate Converted"=>$no);
        }else{
            $response = array("success"=>false,"Coordinate Convertedted"=>$no);
        }

        $this->response($response, 200);
    }

    public function migrasi_location_mill_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        //Select Semua Mill Yang Mempunyai Titik GPS dan Belum ada Spatial Fieldnya
        $sql = "SELECT
                m.MillID,
                m.Latitude,
                m.Longitude,
                m.LatLong 
            FROM
                ktv_mill	m
            WHERE
                m.StatusCode = 'active' 
                AND ABS(m.Latitude) > 0 
                AND ABS(m.Longitude) > 0 
                AND m.LatLong IS NULL
            ORDER BY m.LatLong DESC";

        $query = $this->db->query($sql);

        $no = 0;
        if($query->num_rows()>0){
            foreach($query->result_array() as $row){
                //Koordinat Geometry ============= (Begin)
                if($row['Latitude'] != "" && $row['Longitude'] != "") {

                    $LatitudeProses = (float) $row['Latitude'];
                    $LongitudeProses = (float) $row['Longitude'];
                    
                    //Check Latitude
                    if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                        //Cek valid tidak koordinatnya
                        $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                        $DataCekKoordinat = $this->db->query($sql2)->row_array();
                        
                        if ($DataCekKoordinat['HasilCek'] == "1") {
                            $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                            $sql2 = "UPDATE ktv_mill a SET
                                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                    WHERE
                                        a.`MillID` = ?
                                    LIMIT 1";
                            $p = array(
                                $row["MillID"]
                            );
                            $query = $this->db->query($sql2,$p);

                            if($query){
                                $no++;
                            }
                        }

                    }
                }
            }
        }

        if($no > 0){
            $response = array("success"=>true,"Coordinate Converted"=>$no);
        }else{
            $response = array("success"=>false,"Coordinate Convertedted"=>$no);
        }

        $this->response($response, 200);
    }

    function migrasi_plot_polygon_spatial_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        
        $PolygonConverted = 0;

        //Select Semua Data Polygon yang active
        $sql = "SELECT
                a.`MemberID`,
                a.`PlotNr`,
                a.`SurveyNr`,
                a.`Revision`,
                a.`StatusCheck`,
                COUNT( a.`MemberID` ) AS JumlahTitik 
            FROM
                `ktv_survey_plot_polygon` a 
            WHERE
                1 = 1 
                AND a.`MemberID` > 0 
                AND a.`AfterMigration` = '1'
            GROUP BY
                a.`MemberID`,
                a.`PlotNr`,
                a.`SurveyNr`,
                a.`Revision`,
                a.`StatusCheck` 
            HAVING
                JumlahTitik >= 4";
        
        $DataPoly = $this->db->query($sql)->result_array();

        if(count($DataPoly)>0){
            for ($i=0; $i < count($DataPoly); $i++) {
                $sql = "SELECT
                            a.`MemberID`
                        FROM
                            `ktv_survey_plot_polygon_geo` a
                        WHERE 1=1
                            AND a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.`Revision` = ?
                        LIMIT 1
                        ";
                $p = array(
                    $DataPoly[$i]['MemberID'],
                    $DataPoly[$i]['PlotNr'],
                    $DataPoly[$i]['SurveyNr'],
                    $DataPoly[$i]['Revision']
                );
                $DataCekPoly = $this->db->query($sql,$p)->row_array();
                if($DataCekPoly['MemberID'] == "") {
                    //Mulai proses disini
                    $sql = "SELECT
                                a.*
                            FROM
                                `ktv_survey_plot_polygon` a
                            WHERE 1=1
                                AND a.`MemberID` = '{$DataPoly[$i]['MemberID']}'
                                AND a.`PlotNr` = '{$DataPoly[$i]['PlotNr']}'
                                AND a.`SurveyNr` = '{$DataPoly[$i]['SurveyNr']}'
                                AND a.`Revision` = '{$DataPoly[$i]['Revision']}'
                            ORDER BY a.OrderNr ASC
                            ";
                    $DataPolygon = $this->db->query($sql)->result_array();
                    $CountPolygon = count($DataPolygon);
                    
                    //TitikAwal
                    $TitikAwal = $DataPolygon[0]['latitude'] . '@' . $DataPolygon[0]['longitude'];
                    $TitikAkhir = $DataPolygon[$CountPolygon - 1]['latitude'] . '@' . $DataPolygon[$CountPolygon - 1]['longitude'];

                    if($TitikAwal != $TitikAkhir) { //SAMAKAN Titiknya
                        $DataPolygon[$CountPolygon]['MemberID'] = $DataPolygon[$CountPolygon - 1]['MemberID'];
                        $DataPolygon[$CountPolygon]['PlotNr'] = $DataPolygon[$CountPolygon - 1]['PlotNr'];
                        $DataPolygon[$CountPolygon]['SurveyNr'] = $DataPolygon[$CountPolygon - 1]['SurveyNr'];
                        $DataPolygon[$CountPolygon]['OrderNr'] = $DataPolygon[$CountPolygon - 1]['OrderNr'] + 1;
                        $DataPolygon[$CountPolygon]['latitude'] = $DataPolygon[0]['latitude'];
                        $DataPolygon[$CountPolygon]['longitude'] = $DataPolygon[0]['longitude'];
                        $DataPolygon[$CountPolygon]['Altitude'] = $DataPolygon[$CountPolygon - 1]['Altitude'];
                        $DataPolygon[$CountPolygon]['Accuracy'] = $DataPolygon[$CountPolygon - 1]['Accuracy'];
                        $DataPolygon[$CountPolygon]['CenterLatitude'] = $DataPolygon[$CountPolygon - 1]['CenterLatitude'];
                        $DataPolygon[$CountPolygon]['CenterLongitude'] = $DataPolygon[$CountPolygon - 1]['CenterLongitude'];
                        $DataPolygon[$CountPolygon]['Flag'] = $DataPolygon[$CountPolygon - 1]['Flag'];
                        $DataPolygon[$CountPolygon]['Revision'] = $DataPolygon[$CountPolygon - 1]['Revision'];
                        $DataPolygon[$CountPolygon]['StatusCheck'] = $DataPolygon[$CountPolygon - 1]['StatusCheck'];
                        $DataPolygon[$CountPolygon]['AfterMigration'] = $DataPolygon[$CountPolygon - 1]['AfterMigration'];
                        $DataPolygon[$CountPolygon]['DateCreated'] = $DataPolygon[$CountPolygon - 1]['DateCreated'];
                        $DataPolygon[$CountPolygon]['CreatedBy'] = $DataPolygon[$CountPolygon - 1]['CreatedBy'];
                        $DataPolygon[$CountPolygon]['DateUpdated'] = $DataPolygon[$CountPolygon - 1]['DateUpdated'];
                        $DataPolygon[$CountPolygon]['LastModifiedBy'] = $DataPolygon[$CountPolygon - 1]['LastModifiedBy'];
                        $DataPolygon[$CountPolygon]['DateSync'] = $DataPolygon[$CountPolygon - 1]['DateSync'];
    
                        $TitikAkhir = $TitikAwal;
                    }

                    if ($TitikAwal == $TitikAkhir) { //Proses Lanjut
                        $IsValidKoordinat = false;
    
                        if( ($DataPolygon[0]['latitude'] >= -90 && $DataPolygon[0]['latitude'] <= 90) && ($DataPolygon[0]['longitude'] >= -180 && $DataPolygon[0]['longitude'] <= 180) ) {
                            $IsValidKoordinat = true;
                        }
    
                        if($IsValidKoordinat == true) {
                            //Susun Text Polygon
                            $ArrSusunanPolygon = array();
                            $SusunanPolygon = "";
                            $TextPolgyon = "";
                            $TextPolgyonWithSrid = "";
    
                            //Susun disini
                            $DataSourceInsertCenterLatLong = "";
                            $DataSourceInsertAltitude = "";
                            $DataSourceInsertAccuracy = "";
                            $DataSourceInsertFlag = "";
                            $DataSourceInsertDateCreated = "";
                            $DataSourceInsertCreatedBy = "";
                            $DataSourceInsertDateUpdated = "";
                            $DataSourceInsertLastModifiedBy = "";
                            $DataSourceInsertDateSync = "";
    
                            for ($j = 0; $j < count($DataPolygon); $j++) {
                                if ($j == 0) {
                                    if ($DataPolygon[$j]['CenterLongitude'] != "" && $DataPolygon[$j]['CenterLatitude'] != "") {
                                        $DataSourceInsertCenterLatLong = " ST_GEOMFROMTEXT('POINT({$DataPolygon[$j]['CenterLatitude']} {$DataPolygon[$j]['CenterLongitude']})', 4326) ";
                                    }
                                    $DataSourceInsertAltitude = $DataPolygon[$j]['Altitude'];
                                    $DataSourceInsertAccuracy = $DataPolygon[$j]['Accuracy'];
                                    $DataSourceInsertFlag = $DataPolygon[$j]['Flag'];
                                    $DataSourceInsertDateCreated = $DataPolygon[$j]['DateCreated'];
                                    $DataSourceInsertCreatedBy = $DataPolygon[$j]['CreatedBy'];
                                    $DataSourceInsertDateUpdated = $DataPolygon[$j]['DateUpdated'];
                                    $DataSourceInsertLastModifiedBy = $DataPolygon[$j]['LastModifiedBy'];
                                    $DataSourceInsertDateSync = $DataPolygon[$j]['DateSync'];
                                }
        
                                $ArrSusunanPolygon[] = "{$DataPolygon[$j]['latitude']} {$DataPolygon[$j]['longitude']}";
                            }
    
                            $SusunanPolygon = implode(",", $ArrSusunanPolygon);
                            if ($SusunanPolygon != "") {
                                $TextPolgyon = " ST_GeomFromText('POLYGON(( $SusunanPolygon ))') ";
                                $TextPolgyonWithSrid = " ST_GEOMFROMTEXT('POLYGON(( $SusunanPolygon ))', 4326) ";
    
                                //Cek valid by MySQL
                                $sql = " SELECT ST_IsValid($TextPolgyon) AS HasilCek ";
                                $DataCekPolygon = $this->db->query($sql)->row_array();
    
                                if ($DataCekPolygon['HasilCek'] == "1") {
                                    //Koordinat CenterLatLong
                                    $SqlCenterLatLong = "";
                                    if ($DataSourceInsertCenterLatLong != "") $SqlCenterLatLong = " `CenterLatLong` = $DataSourceInsertCenterLatLong , ";
    
                                    $sql = "INSERT INTO `ktv_survey_plot_polygon_geo` SET
                                            `MemberID` = ?,
                                            `PlotNr` = ?,
                                            `SurveyNr` = ?,
                                            `Revision` = ?,
                                            `Polygon` = $TextPolgyonWithSrid ,
                                            $SqlCenterLatLong
                                            `Altitude` = ?,
                                            `Accuracy` = ?,
                                            `Flag` = ?,
                                            `StatusCheck` = ?,
                                            `DateCreated` = ?,
                                            `CreatedBy` = ?,
                                            `DateUpdated` = ?,
                                            `LastModifiedBy` = ?,
                                            `DateSync` = ?
                                    ";
                                    $p = array(
                                        $DataPoly[$i]['MemberID'],
                                        $DataPoly[$i]['PlotNr'],
                                        $DataPoly[$i]['SurveyNr'],
                                        $DataPoly[$i]['Revision'],
                                        ($DataSourceInsertAltitude != "" ? $DataSourceInsertAltitude : null),
                                        ($DataSourceInsertAccuracy != "" ? $DataSourceInsertAccuracy : null),
                                        ($DataSourceInsertFlag != "" ? $DataSourceInsertFlag : null),
                                        ($DataPoly[$i]['StatusCheck'] == null)?'':$DataPoly[$i]['StatusCheck'],
                                        ($DataSourceInsertDateCreated != "" ? $DataSourceInsertDateCreated : null),
                                        ($DataSourceInsertCreatedBy != "" ? $DataSourceInsertCreatedBy : null),
                                        ($DataSourceInsertDateUpdated != "" ? $DataSourceInsertDateUpdated : null),
                                        ($DataSourceInsertLastModifiedBy != "" ? $DataSourceInsertLastModifiedBy : null),
                                        ($DataSourceInsertDateSync != "" ? $DataSourceInsertDateSync : null)
                                    );
                                    $query = $this->db->query($sql, $p);
                                    //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
                                    if ($this->db->affected_rows() > 0) {
                                        $PolygonConverted++;
    
                                        //Update Status AfterMigration nya
                                        $sql = "UPDATE `ktv_survey_plot_polygon` a SET
                                                    a.`AfterMigration` = '2'
                                                WHERE 1=1
                                                    AND a.`MemberID` = ?
                                                    AND a.`PlotNr` = ?
                                                    AND a.`SurveyNr` = ?
                                                    AND a.`Revision` = ?
                                                ";
                                        $p = array(
                                            $DataPoly[$i]['MemberID'],
                                            $DataPoly[$i]['PlotNr'],
                                            $DataPoly[$i]['SurveyNr'],
                                            $DataPoly[$i]['Revision']
                                        );
                                        $query = $this->db->query($sql,$p);
    
                                    }
    
                                }
    
                            }
    
                        }
    
                    }
                }
            }
        }

        if($PolygonConverted > 0){
            $response = array("success"=>true,"Polygon Converted"=>$PolygonConverted);
        }else{
            $response = array("success"=>false,"Polygon Converted"=>$PolygonConverted);
        }

        $this->response($response, 200);
    }

    function migrasi_plot_polygon_sme_spatial_get(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        
        $PolygonConverted = 0;

        //Select Semua Data Polygon yang active
        $sql = "SELECT
                a.`MemberID`,
                a.`PlotNr`,
                a.`SurveyNr`,
                a.`Revision`,
                a.`StatusCheck`,
                COUNT( a.`MemberID` ) AS JumlahTitik 
            FROM
                `ktv_survey_plot_polygon_sme` a 
            WHERE
                1 = 1 
                AND a.`MemberID` > 0 
                AND a.`AfterMigration` = '1'
            GROUP BY
                a.`MemberID`,
                a.`PlotNr`,
                a.`SurveyNr`,
                a.`Revision`,
                a.`StatusCheck` 
            HAVING
                JumlahTitik >= 4";
        
        $DataPoly = $this->db->query($sql)->result_array();

        if(count($DataPoly)>0){
            for ($i=0; $i < count($DataPoly); $i++) {
                $sql = "SELECT
                            a.`MemberID`
                        FROM
                            `ktv_survey_plot_polygon_sme_geo` a
                        WHERE 1=1
                            AND a.`MemberID` = ?
                            AND a.`PlotNr` = ?
                            AND a.`SurveyNr` = ?
                            AND a.`Revision` = ?
                        LIMIT 1
                        ";
                $p = array(
                    $DataPoly[$i]['MemberID'],
                    $DataPoly[$i]['PlotNr'],
                    $DataPoly[$i]['SurveyNr'],
                    $DataPoly[$i]['Revision']
                );
                $DataCekPoly = $this->db->query($sql,$p)->row_array();
                if($DataCekPoly['MemberID'] == "") {
                    //Mulai proses disini
                    $sql = "SELECT
                                a.*
                            FROM
                                `ktv_survey_plot_polygon_sme` a
                            WHERE 1=1
                                AND a.`MemberID` = '{$DataPoly[$i]['MemberID']}'
                                AND a.`PlotNr` = '{$DataPoly[$i]['PlotNr']}'
                                AND a.`SurveyNr` = '{$DataPoly[$i]['SurveyNr']}'
                                AND a.`Revision` = '{$DataPoly[$i]['Revision']}'
                            ORDER BY a.OrderNr ASC
                            ";
                    $DataPolygon = $this->db->query($sql)->result_array();
                    $CountPolygon = count($DataPolygon);
                    
                    //TitikAwal
                    $TitikAwal = $DataPolygon[0]['latitude'] . '@' . $DataPolygon[0]['longitude'];
                    $TitikAkhir = $DataPolygon[$CountPolygon - 1]['latitude'] . '@' . $DataPolygon[$CountPolygon - 1]['longitude'];

                    if($TitikAwal != $TitikAkhir) { //SAMAKAN Titiknya
                        $DataPolygon[$CountPolygon]['MemberID'] = $DataPolygon[$CountPolygon - 1]['MemberID'];
                        $DataPolygon[$CountPolygon]['PlotNr'] = $DataPolygon[$CountPolygon - 1]['PlotNr'];
                        $DataPolygon[$CountPolygon]['SurveyNr'] = $DataPolygon[$CountPolygon - 1]['SurveyNr'];
                        $DataPolygon[$CountPolygon]['OrderNr'] = $DataPolygon[$CountPolygon - 1]['OrderNr'] + 1;
                        $DataPolygon[$CountPolygon]['latitude'] = $DataPolygon[0]['latitude'];
                        $DataPolygon[$CountPolygon]['longitude'] = $DataPolygon[0]['longitude'];
                        $DataPolygon[$CountPolygon]['Altitude'] = $DataPolygon[$CountPolygon - 1]['Altitude'];
                        $DataPolygon[$CountPolygon]['Accuracy'] = $DataPolygon[$CountPolygon - 1]['Accuracy'];
                        $DataPolygon[$CountPolygon]['CenterLatitude'] = $DataPolygon[$CountPolygon - 1]['CenterLatitude'];
                        $DataPolygon[$CountPolygon]['CenterLongitude'] = $DataPolygon[$CountPolygon - 1]['CenterLongitude'];
                        $DataPolygon[$CountPolygon]['Flag'] = $DataPolygon[$CountPolygon - 1]['Flag'];
                        $DataPolygon[$CountPolygon]['Revision'] = $DataPolygon[$CountPolygon - 1]['Revision'];
                        $DataPolygon[$CountPolygon]['StatusCheck'] = $DataPolygon[$CountPolygon - 1]['StatusCheck'];
                        $DataPolygon[$CountPolygon]['AfterMigration'] = $DataPolygon[$CountPolygon - 1]['AfterMigration'];
                        $DataPolygon[$CountPolygon]['DateCreated'] = $DataPolygon[$CountPolygon - 1]['DateCreated'];
                        $DataPolygon[$CountPolygon]['CreatedBy'] = $DataPolygon[$CountPolygon - 1]['CreatedBy'];
                        $DataPolygon[$CountPolygon]['DateUpdated'] = $DataPolygon[$CountPolygon - 1]['DateUpdated'];
                        $DataPolygon[$CountPolygon]['LastModifiedBy'] = $DataPolygon[$CountPolygon - 1]['LastModifiedBy'];
                        $DataPolygon[$CountPolygon]['DateSync'] = $DataPolygon[$CountPolygon - 1]['DateSync'];
    
                        $TitikAkhir = $TitikAwal;
                    }

                    if ($TitikAwal == $TitikAkhir) { //Proses Lanjut
                        $IsValidKoordinat = false;
    
                        if( ($DataPolygon[0]['latitude'] >= -90 && $DataPolygon[0]['latitude'] <= 90) && ($DataPolygon[0]['longitude'] >= -180 && $DataPolygon[0]['longitude'] <= 180) ) {
                            $IsValidKoordinat = true;
                        }
    
                        if($IsValidKoordinat == true) {
                            //Susun Text Polygon
                            $ArrSusunanPolygon = array();
                            $SusunanPolygon = "";
                            $TextPolgyon = "";
                            $TextPolgyonWithSrid = "";
    
                            //Susun disini
                            $DataSourceInsertCenterLatLong = "";
                            $DataSourceInsertAltitude = "";
                            $DataSourceInsertAccuracy = "";
                            $DataSourceInsertFlag = "";
                            $DataSourceInsertDateCreated = "";
                            $DataSourceInsertCreatedBy = "";
                            $DataSourceInsertDateUpdated = "";
                            $DataSourceInsertLastModifiedBy = "";
                            $DataSourceInsertDateSync = "";
    
                            for ($j = 0; $j < count($DataPolygon); $j++) {
                                if ($j == 0) {
                                    if ($DataPolygon[$j]['CenterLongitude'] != "" && $DataPolygon[$j]['CenterLatitude'] != "") {
                                        $DataSourceInsertCenterLatLong = " ST_GEOMFROMTEXT('POINT({$DataPolygon[$j]['CenterLatitude']} {$DataPolygon[$j]['CenterLongitude']})', 4326) ";
                                    }
                                    $DataSourceInsertAltitude = $DataPolygon[$j]['Altitude'];
                                    $DataSourceInsertAccuracy = $DataPolygon[$j]['Accuracy'];
                                    $DataSourceInsertFlag = $DataPolygon[$j]['Flag'];
                                    $DataSourceInsertDateCreated = $DataPolygon[$j]['DateCreated'];
                                    $DataSourceInsertCreatedBy = $DataPolygon[$j]['CreatedBy'];
                                    $DataSourceInsertDateUpdated = $DataPolygon[$j]['DateUpdated'];
                                    $DataSourceInsertLastModifiedBy = $DataPolygon[$j]['LastModifiedBy'];
                                    $DataSourceInsertDateSync = $DataPolygon[$j]['DateSync'];
                                }
        
                                $ArrSusunanPolygon[] = "{$DataPolygon[$j]['latitude']} {$DataPolygon[$j]['longitude']}";
                            }
    
                            $SusunanPolygon = implode(",", $ArrSusunanPolygon);
                            if ($SusunanPolygon != "") {
                                $TextPolgyon = " ST_GeomFromText('POLYGON(( $SusunanPolygon ))') ";
                                $TextPolgyonWithSrid = " ST_GEOMFROMTEXT('POLYGON(( $SusunanPolygon ))', 4326) ";
    
                                //Cek valid by MySQL
                                $sql = " SELECT ST_IsValid($TextPolgyon) AS HasilCek ";
                                $DataCekPolygon = $this->db->query($sql)->row_array();
    
                                if ($DataCekPolygon['HasilCek'] == "1") {
                                    //Koordinat CenterLatLong
                                    $SqlCenterLatLong = "";
                                    if ($DataSourceInsertCenterLatLong != "") $SqlCenterLatLong = " `CenterLatLong` = $DataSourceInsertCenterLatLong , ";
    
                                    $sql = "INSERT INTO `ktv_survey_plot_polygon_sme_geo` SET
                                            `MemberID` = ?,
                                            `PlotNr` = ?,
                                            `SurveyNr` = ?,
                                            `Revision` = ?,
                                            `Polygon` = $TextPolgyonWithSrid ,
                                            $SqlCenterLatLong
                                            `Altitude` = ?,
                                            `Accuracy` = ?,
                                            `Flag` = ?,
                                            `StatusCheck` = ?,
                                            `DateCreated` = ?,
                                            `CreatedBy` = ?,
                                            `DateUpdated` = ?,
                                            `LastModifiedBy` = ?,
                                            `DateSync` = ?
                                    ";
                                    $p = array(
                                        $DataPoly[$i]['MemberID'],
                                        $DataPoly[$i]['PlotNr'],
                                        $DataPoly[$i]['SurveyNr'],
                                        $DataPoly[$i]['Revision'],
                                        ($DataSourceInsertAltitude != "" ? $DataSourceInsertAltitude : null),
                                        ($DataSourceInsertAccuracy != "" ? $DataSourceInsertAccuracy : null),
                                        ($DataSourceInsertFlag != "" ? $DataSourceInsertFlag : null),
                                        ($DataPoly[$i]['StatusCheck'] == null)?'':$DataPoly[$i]['StatusCheck'],
                                        ($DataSourceInsertDateCreated != "" ? $DataSourceInsertDateCreated : null),
                                        ($DataSourceInsertCreatedBy != "" ? $DataSourceInsertCreatedBy : null),
                                        ($DataSourceInsertDateUpdated != "" ? $DataSourceInsertDateUpdated : null),
                                        ($DataSourceInsertLastModifiedBy != "" ? $DataSourceInsertLastModifiedBy : null),
                                        ($DataSourceInsertDateSync != "" ? $DataSourceInsertDateSync : null)
                                    );
                                    $query = $this->db->query($sql, $p);
                                    //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
                                    if ($this->db->affected_rows() > 0) {
                                        $PolygonConverted++;
    
                                        //Update Status AfterMigration nya
                                        $sql = "UPDATE `ktv_survey_plot_polygon_sme` a SET
                                                    a.`AfterMigration` = '2'
                                                WHERE 1=1
                                                    AND a.`MemberID` = ?
                                                    AND a.`PlotNr` = ?
                                                    AND a.`SurveyNr` = ?
                                                    AND a.`Revision` = ?
                                                ";
                                        $p = array(
                                            $DataPoly[$i]['MemberID'],
                                            $DataPoly[$i]['PlotNr'],
                                            $DataPoly[$i]['SurveyNr'],
                                            $DataPoly[$i]['Revision']
                                        );
                                        $query = $this->db->query($sql,$p);
    
                                    }
    
                                }
    
                            }
    
                        }
    
                    }
                }
            }
        }

        if($PolygonConverted > 0){
            $response = array("success"=>true,"Polygon Converted"=>$PolygonConverted);
        }else{
            $response = array("success"=>false,"Polygon Converted"=>$PolygonConverted);
        }

        $this->response($response, 200);
    }

    public function init_farmer_nc_v2_get()
    {
        //init bahasa indonesia
        $this->load->language('general', 'indonesia');

        $Username = filter_var($this->get('username'), FILTER_SANITIZE_STRING);
        $StatusProses = false;
        $MsgProses = '';

        //Get List Farmer dari mappingan
        $Mapping = $this->mtools->FaFarmerMapping($Username);
        if (isset($Mapping['DataFarmer'][0]['FarmerID'])) {
            $DataWriteJsonZip = $this->mtools->InitFarmerNcV2($Mapping['DataFarmer'], $Mapping['IMSID'], $Mapping['SurveyNr'], $Mapping['Certification']);
            $JsonWrite = json_encode($DataWriteJsonZip);
            //echo '<pre>'; print_r($JsonWrite); exit;

            //write json
            $NamaFile = 'init_farmer_nc_' . date('YmdHis');
            file_put_contents('files/export/' . $NamaFile . '.json', $JsonWrite);

            $zip = new ZipArchive();
            $zip->open('files/export/' . $NamaFile . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $zip->addFile('files/export/' . $NamaFile . '.json', $NamaFile . '.json');
            $zip->close();

            //Force download zip
            $ZipFile = 'files/export/' . $NamaFile . '.zip';
            $FileNameZip = basename($ZipFile);
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=$FileNameZip");
            header("Content-Length: " . filesize($ZipFile));
            readfile($ZipFile);
            exit;
        } else {
            $StatusProses = false;
            $MsgProses = lang('Username ini belum ada farmer yang terassign');

            $result['success'] = $StatusProses;
            $result['message'] = $MsgProses;
            $this->response($result, 400);
        }
    }

    public function api_coaching_activity_download_v2_get()
    {
        $Username = filter_var($this->get('username'), FILTER_SANITIZE_STRING);
        $IMSID = (int) $this->get('ims_id');

        $DataCoaching = $this->mtools->GetDataCoaching($Username, $IMSID);
        if (isset($DataCoaching[0]['FarmerID'])) {
            $DataWriteJsonZip = $this->mtools->CoachingDownloadV2($DataCoaching, $Username);
            $JsonWrite = json_encode($DataWriteJsonZip);
            //echo '<pre>'; print_r($JsonWrite); exit;

            //write json
            $NamaFile = 'coaching_nc_' . date('YmdHis');
            file_put_contents('files/export/' . $NamaFile . '.json', $JsonWrite);

            $zip = new ZipArchive();
            $zip->open('files/export/' . $NamaFile . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $zip->addFile('files/export/' . $NamaFile . '.json', $NamaFile . '.json');
            $zip->close();

            //Force download zip
            $ZipFile = 'files/export/' . $NamaFile . '.zip';
            $FileNameZip = basename($ZipFile);
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=$FileNameZip");
            header("Content-Length: " . filesize($ZipFile));
            readfile($ZipFile);
            exit;
        } else {
            $result['success'] = true;
            $result['message'] = "No Coaching Data for $Username with IMSID=$IMSID";
            $this->response($result, 200);
        }
    }

    public function api_coaching_activity_data_v2_post() {
        //GetParam
        $JsonAct = $this->post('activity');
        /*
         * Jika upload zip disini juga pakai ini
        $is_upload = false;
        $data_img = array();
        $folder = '';
        if (!empty($_FILES)) {
            $config['upload_path'] = './files/tmp/';
            $config['allowed_types'] = 'zip';
            $config['max_size'] = '8192';
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('file')) {
                $error = array('error' => $this->upload->display_errors());
                $this->response(array('success' => false, 'message' => $error['error']), 400);
            } else {
                $is_upload = true;
                $data = array('upload_data' => $this->upload->data());
                $zip = new ZipArchive;
                if ($zip->open($data['upload_data']['full_path']) === TRUE) {
                    $folder = $data['upload_data']['raw_name'];
                    $pathidr = $data['upload_data']['file_path'] . '/' . $data['upload_data']['raw_name'];
                    if (!file_exists($pathidr)) {
                        make_directory($pathidr);
                    }
                    $zip->extractTo($pathidr);
                    $zip->close();
                    delete_file($data['upload_data']['full_path']);
                    $files_upload = array_slice(scandir($pathidr), 2);
                    if (!empty($files_upload)) {
                        $json_found = false;
                        $file_attachment = array();
                        foreach ($files_upload as $file) {
                            $tmp = explode('_', $file);
                            $data_img[$tmp[0]] = $pathidr . '/' . $file;
                        }
                    }
                } else {
                    $this->response(array('success' => false, 'message' => 'Can not read zip file, please check your file!'), 400);
                }
            }
        }
        */
        if(isJson($JsonAct) == true) {
            $Act = json_decode($JsonAct, true);
            //echo '<pre>'; print_r($Act); exit;

//          Jika upload zip disini juga pakai ini
//            $proses = $this->mapi->CoachingSubmitV2($Act, $data_img, $folder);
            $proses = $this->mtools->CoachingSubmitV2($Act);
            if($proses['success'] == true) {
                $this->response($proses, 200);
            } else {
                $this->response($proses, 400);
            }
        } else {
            $result['success'] = 'false';
            $result['message'] = 'Not valid json parameter on \'activity\'';
            $this->response($result, 400);
        }
    }
    
    public function download_farmer_training_get() {
        $return = array();
        $this->load->model('training/mfarmer');

        $query = $this->mfarmer->getFarmerTrainingByUsername($this->get('username'));
        if (count($query) > 0) {
            $return['success']  = true;
            $return['total']    = count($query);
            $return['data']     = $query;
        } else {
            $return['success'] = false;
            $return['sql'] = $this->db->last_query();
            $return['message'] = 'No Training Data for this username';
        }

        if($return['success'] == true) {
            $this->response($return, 200);
        } else {
            $this->response($return, 400);
        }
    }

    public function upload_farmer_training_post() {
        // ini_set('display_errors',true); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $return = array();
        $is_upload = false;
        $this->load->model('training/mfarmer', 'mfarmer');
        $this->load->library('awsfileupload');
        $status = $this->post('status'); 
        
        if (!empty($_FILES)) {
            $config['upload_path'] = './files/tmp/';
            $config['allowed_types'] = 'zip';
            $config['max_size'] = '8192';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file')) {
                $return['success'] = false;
                $return['message'] = 'Upload file to temp folder failed';
                $this->response($return, 400);
            } else {
                $is_upload = true;
                $data = array('upload_data' => $this->upload->data());
                $DirProcess = $data['upload_data']['file_path'] . $data['upload_data']['raw_name'];
                
                if ($status == '2') {
                    $UploadFile = $this->awsfileupload->upload($data['upload_data']['full_path'], $data['upload_data']['file_name'], AWSS3_UPLOAD_FARMER_TRAINING_PATH, 'documents');
                    if ($UploadFile['success'] == true) {
                        $logId = $this->mfarmer->AddLogUploadFarmerTraining($UploadFile);
                    }

                    $return['success'] = true;
                    $return['message'] = 'Cek upload log';
                    $return['UploadFile'] = $UploadFile;
                    $return['DirProcess'] = $DirProcess;
                    $this->response($return, 200);
                }
                //echo '<pre>'; print_r($data); exit;

                //Proses Extrace File =========================== (Begin)
                $zip = new ZipArchive;
                if ($zip->open($data['upload_data']['full_path']) === TRUE) {
                    //create temp directory
                    $pathidr = $data['upload_data']['file_path'] . $data['upload_data']['raw_name'];
                    if (!file_exists($pathidr)){
                        make_directory($pathidr);  
                    }

                    //extract
                    $zip->extractTo($pathidr);
                    $zip->close();
                    delete_file($data['upload_data']['full_path']);

                    $files_upload = array_slice(scandir($pathidr), 2);

                    if (!empty($files_upload)) {
                        $json_found = false;
                        $file_attachment = array();
                        $data_post = array();

                        foreach ($files_upload as $key => $file) {
                            if (strpos($file, '.json')) {
                                $json_found = true;
                                $data_post = json_decode(file_get_contents($pathidr . '/' . $file), true);
                            } else {
                                $tmp = explode('_', $file);
                                // if($tmp[2] == "attendance") {
                                //     $file_attachment['farmer'][$tmp[1]][$tmp[4]] = $pathidr . '/' . $file;
                                // } else {
                                //     $file_attachment['other'][$tmp[1]][$tmp[4]] = $pathidr . '/' . $file;
                                // }
                                if($tmp[4] == ""){
                                    $file_attachment['files'][$key] = $pathidr . '/' . $file;
                                }
                            }
                        }
                        $data_post['file_attachment'] = $file_attachment;
                        if ($json_found === false) {
                            $return['success'] = false;
                            $return['message'] = 'Attendance file not found, please check your file!';
                        }
                    }
                } else {
                    $return['success'] = false;
                    $return['message'] = 'Can not read zip file, please check your file!';
                }
                //Proses Extrace File =========================== (End)
            }
            
            if ($status == '1') {
                $return['success'] = true;
                $return['message'] = 'Trace Problem';
                $return['files_upload'] = $files_upload;
                $return['pathidr'] = $pathidr;
                $return['data_post'] = $data_post;
                $this->response($return, 200);
            }

            $TrainFarmerID = (int) $data_post['training_id'];
            $username = (isset($data_post['username']) ? $data_post['username'] : '');
            $User = $this->mfarmer->getUser($username);
            if (!empty($User)) {
                $UserID = $User['UserId'];
            } else {
                $UserID = null;
            }
            $FarTrain = $this->mfarmer->getFarmerTrain($TrainFarmerID);
            if (!empty($FarTrain)) {
                $attendances_list = $data_post['attendances_list'];
                $TrainDates = GetDatesFromRange($FarTrain['TrainingStart'], $FarTrain['TrainingStart']);
                
                //Proses status kehadiran
                if (!empty($attendances_list)) {
                    foreach ($attendances_list as $key => $attlist) {
                        $DayTrain = $attlist['day'];
                        $TrainDateProcess = $TrainDates[$DayTrain-1];
                        $ParAttendances = $attlist['attendance'];
                        
                        $UpdateAtt = $this->mfarmer->MobileUpdateAttendances($TrainFarmerID, $DayTrain, $TrainDateProcess, $ParAttendances, $UserID, $DirProcess);
                    }
                }

                // //Attachment File
                $attchement_file = $data_post['file_attachment']['files'];

                //Proses file attachment
                if (!empty($attchement_file)) {
                    foreach ($attchement_file as $key => $attfile) {

                        $tmpdata = explode($pathidr."/", $attfile);
                        $filename = $tmpdata[1];
                        
                        $UpdateAtt = $this->mfarmer->MobileUpdateFileAttachment($filename, $attfile, $FarmerID, $TrainFarmerID, $UserID);
                    }
                }

                //Kalau sampai disini berarti success
                $return['success'] = true;
                $return['message'] = 'Data Saved';
            } else {
                $return['success'] = false;
                $return['message'] = 'Training ID not registered';
                $return['data_post'] = $data_post;
                $return['FarTrain'] = $FarTrain;
            }

        } else {
            $return['success'] = false;
            $return['message'] = 'Training file not found';
        }

        //hapus file tmp
        if ($is_upload == true) {
            $this->load->helper('file');
            $path_upload = 'files/tmp/' . $data['upload_data']['raw_name'];
            $files_upload = array_slice(scandir($path_upload), 2);
            @delete_files(@$path_upload, true);
            if (is_dir($path_upload)) {
                @rmdir(@$path_upload);
            }
        }
        if (isset($logId)) {
            $this->mfarmer->UpdateLogUpload($logId);
        }

        //echo '<pre>'; print_r($return); exit;
        if($return['success'] == true) {
            $this->response($return, 200);
        } else {
            $this->response($return, 400);
        }
    }

    public function gps_clear_data_get() {
        $data = $this->mgps->clearData($_SESSION['userid']);
        if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }
    public function kml_clear_data_get() {
        $data = $this->mgps->clearDataKML($_SESSION['userid']);
        if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }
}

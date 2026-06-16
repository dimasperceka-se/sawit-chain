<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'application/third_party/Spout3/Autoloader/autoload.php';

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Geospatial extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('geospatial/mmaps');
        // $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'dummy'));
    }

    function maps_get() {

        $data = $this->mmaps->readMaps($_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function map_get() {

        $ProvinceID     = $this->get('ProvinceID');
        $DistrictID     = $this->get('DistrictID');
        $Keyword        = $this->get('Keyword');
        $Status         = $this->get('Status');

        $data = $this->mmaps->readMap($ProvinceID, $DistrictID, $Keyword, $Status);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function bank_map_get($param1,$value1,$param2,$value2,$param3,$value3='',$param4,$value4='') {
        if ($value2=='undefined') $value2='';
        if ($value3=='skop') {
            $value3='';
            $value4 = $param4;
        }
        // $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'dummy'));
        // $key = md5($value1.$value2.$value3.$value4);
        // if ( ! $data = $this->cache->get($key))
        // {
            $data = $this->mmaps->readBankMap($value1,$value2,$value3,$value4);
             // Save into the cache for 5 minutes
        //     $this->cache->save($key, $data, 300);
        // }
        
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function districtmap_get() {
        $ProvinceID     = $this->get('ProvinceID');
        $DistrictID     = $this->get('DistrictID');
        $Keyword        = $this->get('Keyword');
        $Status         = $this->get('Status');

        $data = $this->mmaps->getDistrictCount($ProvinceID, $DistrictID, $Keyword, $Status);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function bank_districtmap_get($param1,$value1,$param2,$value2,$param3,$value3='',$param4,$value4='') {
        if ($value2=='undefined') $value2='';
        if ($value3=='skop') {
            $value3='';
            $value4 = $param4;
        }
        
        // $key = md5($value1.$value2.$value3.$value4);
        // if ( ! $data = $this->cache->get($key))
        // {
            $data = $this->mmaps->getBankDistrictCount($value1,$value2,$value3,$value4);

             // Save into the cache for 5 minutes
        //     $this->cache->save($key, $data, 300);
        // }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function polygon_put()
    {
        $MemberID   = $this->put('MemberID');
        $PlotNr     = $this->put('PlotNr');
        $SurveyNr   = $this->put('SurveyNr');
        if(empty($MemberID) || empty($PlotNr) || !isset($SurveyNr)) {
            $this->response(NULL, 400);
        }
        $data = $this->mmaps->updatePlotPolygon($this->put('area'),$this->put('MemberID'),$this->put('PlotNr'),$this->put('SurveyNr'));
        // $data = $this->mmaps->updateGarden($this->put('area_hectare'),$this->put('farmer_id'),$this->put('PlotNr'),$this->put('SurveyNr'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function map_post() {
        if(!$this->post('name')) $this->response(NULL, 400);
        $data = $this->mmaps->createTraining($this->post('name'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function map_put() {
        if(!$this->put('id')) $this->response(NULL, 400);
        $data = $this->mmaps->updateTraining($this->put('name'),$this->put('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function map_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mmaps->deleteTraining($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

    function district_get() {
        $district = $this->mmaps->readDistrict($this->get('ProvinceID'),$_SESSION['userid']);
        if($district) $this->response($district, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    function province_get() {
        $province = $this->mmaps->readProvince();
        $this->response($province, 200);
    }
    public function polygon_get()
    {
        $MemberID   = $this->get('MemberID');
        $PlotNr     = $this->get('PlotNr');
        $SurveyNr   = $this->get('SurveyNr');

        $area = array();
        $result = $this->mmaps->getPolygon($MemberID, $PlotNr, $SurveyNr);
        if (!empty($result)) {
            foreach ($result as $val) {
                $area[] = array(floatval($val['Latitude']),floatval($val['Longitude']));
            }
        }
        if($area) $this->response($area, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);

    }
    public function clonal_area_get()
    {
        $id = $this->get('id');
        $result = array();
        $result['data'] = $this->mmaps->readClonal($id);
        $result['area'] = $this->mmaps->readClonalArea($id);
        
        if($result) $this->response($result, 200);

    }
    public function area_calc_get()
    {
        set_time_limit(0);
        $farmer = $this->get('farmer');
        $garden = $this->get('garden');
        // if (empty($farmer)) {
        //     exit('Please provide farmer id');
        // }
        // if (empty($garden)) {
        //     $garden = 1;
        // }
        $count = $this->area_calc($farmer, $garden);
        exit;
        $this->response($count, 200);
    }

    private function area_calc($farmer='', $garden='', $survey='')
    {
        echo '<pre>'; print_r('Calc Area'); echo '</pre>'; 
        // get all gardens
        $gardens    = $this->mmaps->readGarden($farmer, $garden, $survey);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // echo "<pre>"; print_r($gardens); echo "</pre>"; 
        $count      = 0;
        if (!empty($gardens)) {
            foreach ($gardens as $key => $garden) {
                // get garden area
                $points = $this->mmaps->readArea($garden['FarmerID'], $garden['GardenNr'], $garden['SurveyNr']);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
                if (!empty($points)) {
                    // calc area in meters square
                    $area = $this->PlanarPolygonAreaMeters($points);
                    // area in hectare
                    $area /= 10000; 
                    // update area
                    if ($this->mmaps->updateGarden($area, $garden['FarmerID'], $garden['GardenNr'], $garden['SurveyNr'])) {
                        $count++;
                    }
                    echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";
                } else {
                    echo '<pre>Area Emty : '; print_r($this->db->last_query()); echo '</pre>'; 
                }
            }
            // $this->mmaps->updateGardenHaPolygon();
        } else {
            echo '<pre>'; print_r('Garden Not Found'); echo '</pre>'; 
        }
        return $count;
    }

    private function PlanarPolygonAreaMeters($points) {

        $earthRadiusMeters   = 6367460.0;
        $metersPerDegree     = 2.0*pi()*$earthRadiusMeters/360.0;
        $radiansPerDegree    = pi()/180.0;

        $a = 0;
        for ($i=0; $i < count($points); $i++) { 
            $j = ($i+1)%count($points);
            $xi  = $points[$i]['Longitude']*$metersPerDegree*cos($points[$i]['Latitude']*$radiansPerDegree);
            $yi  = $points[$i]['Latitude']*$metersPerDegree;
            $xj  = $points[$j]['Longitude']*$metersPerDegree*cos($points[$j]['Latitude']*$radiansPerDegree);
            $yj  = $points[$j]['Latitude']*$metersPerDegree;
            $a += $xi*$yj-$xj*$yi;
        }

        return abs($a/2);
    }     
   
    public function import_get()
    {
        $this->load->helper('file');
        $kml = read_file('polygon.kml');

        if (!empty($kml)) {
            $query = "INSERT INTO `ktv_farmer_garden_area` (
    `FarmerID`
    , `GardenNr`
    , `SurveyNr`
    , `OrderNr`
    , `Latitude`
    , `Longitude`
    , `Revision`
    , `Status`
    , `DateCreated`
    , `CreatedBy`
) 
VALUES
";
            $places_xml = simplexml_load_string($kml);
            // echo '<pre>'; var_dump($places_xml); echo '</pre>'; exit;
            // echo '<pre>'; print_r($places_xml); echo '</pre>';exit;
            // $values     = array();
            // $inserted   = array();
            $errors     = array();
            $success    = array();
            if ($places_xml) {
                // $this->db->trans_start(FALSE);
                foreach ($places_xml->Document->Folder->Placemark as $key => $value) {      
                    
                    // echo '<pre>'; var_dump($value->ExtendedData->SchemaData); echo '</pre>'; exit;
                    // if (in_array($farmer_id, array_keys($inserted))) {
                    //  $inserted["$farmer_id"] = $inserted["$farmer_id"]+1;
                    // } else {
                    //  $inserted["$farmer_id"] = 1;
                    // }
                    $coordinates = $value
                        // ->MultiGeometry
                        ->Polygon
                        ->outerBoundaryIs
                        ->LinearRing
                        ->coordinates;
                    echo '<pre>'; print_r($coordinates); echo '</pre>'; 
                    if (!empty($coordinates)) {

                        // $farmer_id = intval($value->ExtendedData->SchemaData->FARMERID);
                        // $plot_nr = intval($value->ExtendedData->SchemaData->GARDEN);
                        // $survey_nr = intval($value->ExtendedData->SchemaData->SURVEY);
                        
                        $FarmerID_key = null;
                        $GardenNr_key = null;
                        $SurveyNr_key = null;
                        for ($i=0; $i < count($value->ExtendedData->SchemaData->SimpleData); $i++) { 
                            $v = reset($value->ExtendedData->SchemaData->SimpleData[$i]);
                            if (strtoupper($v['name']) == 'FARMERID') {
                                $FarmerID_key = $i;
                            }
                            if (strtoupper($v['name']) == 'GARDEN') {
                                $GardenNr_key = $i;
                            }
                            if (strtoupper($v['name']) == 'SURVEY') {
                                $SurveyNr_key = $i;
                            }
                        }
                        $farmer_id = intval($value->ExtendedData->SchemaData->SimpleData[$FarmerID_key]);
                        $plot_nr = intval($value->ExtendedData->SchemaData->SimpleData[$GardenNr_key]);
                        $survey_nr = intval($value->ExtendedData->SchemaData->SimpleData[$SurveyNr]);
                        echo '<pre>'; print_r(compact('farmer_id','plot_nr','survey_nr')); echo '</pre>'; 

                        /*$exist = $this->mmaps->isExistGardenArea($farmer_id, $plot_nr, $survey_nr);
                        echo '<pre> IS EXIST : '; var_dump($exist); echo '</pre>'; 
                        if ($this->get('replace') == 1) {
                            $this->mmaps->deleteGardenArea($farmer_id, $plot_nr, $survey_nr);
                            echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                            $exist = false;
                        }
                        if (empty($farmer_id) OR empty($plot_nr) OR $exist) {
                            echo '<pre>'; print_r('continue'); echo '</pre>'; 
                            $errors[] = array('FarmerID' => $farmer_id, 'GardenNr' => $plot_nr, 'SurveyNr' => $survey_nr);
                            continue;
                        }*/
                        
                        $lastRevision = $this->mmaps->checkLastRevision($farmer_id, $plot_nr, $survey_nr);
                        echo '<pre>Latest Rev : '; print_r($lastRevision); echo '</pre>'; 
                        $revision = $lastRevision+1;
                        $coordinates = explode(' ', $coordinates);
                        $order = 1;
                        // $this->db->trans_start(TRUE);

                        $sql = $query;
                        $values = array();
                        foreach ($coordinates as $coord) {
                            $latlng = explode(',', $coord);
                            $value = "({$farmer_id}, {$plot_nr}, {$survey_nr}, {$order}, {$latlng[1]}, {$latlng[0]}, {$revision}, 'verified', NOW(), {$_SESSION['userid']})\n";
                            $values[] = $value;
                            // $data = array(
                            //     'FarmerID'      => $farmer_id,
                            //     'GardenNr'      => $plot_nr,
                            //     'SurveyNr'      => $survey_nr,
                            //     'OrderNr'       => $order,
                            //     'Latitude'      => $latlng[1],
                            //     'Longitude'     => $latlng[0]
                            //     );
                            // $this->mmaps->insertGardenArea($data);
                            $order++;
                        }
                        $sql .= implode(', ', $values);
                        echo '<pre>'; print_r($sql); echo '</pre>';
                        $this->db->query($sql);
                        $this->area_calc($farmer_id,$plot_nr,$survey_nr);
                        // $this->db->trans_complete();
                        // exit;
                        $success[] = array('FarmerID' => $farmer_id, 'GardenNr' => $plot_nr, 'SurveyNr' => $survey_nr);
                    }
                }
                // if ($this->get('commit') == '1') {
                //     $this->db->trans_complete();
                // } else {
                //     $this->db->trans_rollback();
                // }
            }
            // $values = implode(',', $values);
            // echo '<pre>'; print_r($query.$values); echo '</pre>';
            // var_dump(implode(',', $values));
            // exit;
            $this->load->view('gps_import', compact('errors', 'success'));
            exit;
            echo '<pre>'; print_r($errors); echo '</pre>';
        }       
    }

    public function supplychain_get()
    {
        $province   = $this->get('province');
        $partner    = $this->get('partner');
        $certification    = $this->get('certification');
        $start      = $this->get('start');
        $end        = $this->get('end');

        $data = $this->mmaps->getSupplyChain($start, $end, $province, $partner, $certification);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function supplychain_cpg_get()
    {
        $traderid   = $this->get('traderid');
        $start      = $this->get('start');
        $end        = $this->get('end');

        $data = $this->mmaps->getSupplyChainCPG($traderid, $start, $end);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function supplychain_farmer_get()
    {
        // $traderid    = $this->get('traderid');
        $supply_id  = $this->get('supply_id');
        $partner    = $this->get('partner');
        $certification    = $this->get('certification');
        $start      = $this->get('start');
        $end        = $this->get('end');

        $data = $this->mmaps->getSupplyChainFarmer($supply_id, $start, $end, $partner, $certification);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function weather_get()
    {
        $result = $this->mmaps->getWeather(date('Y-m-d'));
        $this->response($result);
    }

    public function weather_fetch_get()
    {
        $currentDate    = date('Y-m-d');
        $districts      = $this->mmaps->getDistricts();
        $values         = array();
        if (!empty($districts)) {
            foreach ($districts as $key => $district) {
                $data       = $this->mmaps->getDistrictWeather($district['District']);
                $dataObj    = json_decode($data, true);
                $value      = array(
                    $district['DistrictID'], $district['District'], $currentDate, $data,
                    $dataObj['coord']['lon'],
                    $dataObj['coord']['lat'],
                    $dataObj['weather'][0]['main'],
                    $dataObj['weather'][0]['description'],
                    'http://openweathermap.org/img/w/'.$dataObj['weather'][0]['icon'].'.png',
                    $dataObj['main']['temp'],
                    $dataObj['main']['pressure'],
                    $dataObj['main']['humidity'],
                    $dataObj['main']['temp_min'],
                    $dataObj['main']['temp_max'],
                    $dataObj['wind']['speed'],
                    $dataObj['wind']['deg'],
                    $dataObj['name'],
                );

                // $this->mmaps->insertWeatherLog($district['District'], $currentDate, $data);
                $values[] = "
                ('" . implode("','", $value) . "')";
            }
        }
        $values = implode(', ', $values);
        $result = $this->mmaps->batchInsertWeatherLog($values);
        echo "Fetch weather run at ".date('Y-m-d H:i:s');
        exit;
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
    }

    public function bank_get()
    {
        $DistrictID     = $this->get('DistrictID');
        $BankID         = $this->get('BankID');
        $data = $this->mmaps->getBank($DistrictID, $BankID);
        $this->response($data, 200);
    }

    public function partner_get()
    {
        $ProvinceID     = $this->get('ProvinceID');
        $data = $this->mmaps->getProvincePartner($ProvinceID);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($data, 200);
    }

    public function plot_detail_get()
    {
        $MemberID = $this->get('MemberID');
        $PlotNr = $this->get('PlotNr');
        $SurveyNr = $this->get('SurveyNr');
        $data = $this->mmaps->getPlotDetail($MemberID, $PlotNr, $SurveyNr);
        $this->response($data, 200);
    }

    public function supply_profile_farmer_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyProfileFarmer($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_transaction_farmer_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyTransactionFarmer($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_profile_koperasi_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyProfileKoperasi($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_transaction_koperasi_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyTransactionKoperasi($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_profile_sce_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyProfileSCE($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_transaction_sce_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyTransactionSCE($id, $start, $end, $wh);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($data, 200);
    }

    public function supply_profile_pedagang_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyProfilePedagang($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_transaction_pedagang_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyTransactionPedagang($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_profile_warehouse_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyProfileWarehouse($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function supply_transaction_warehouse_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $wh     = $this->mmaps->getWarehouseID($this->get('partner'));
        $data = $this->mmaps->getSupplyTransactionWarehouse($id, $start, $end, $wh);

        $this->response($data, 200);
    }

    public function kml_farmer_list_get()
    {
        $DistrictID    = $this->get('DistrictID');
        $FarmerGroupID    = $this->get('FarmerGroupID');
        $data   = $this->mmaps->getKMLFarmerList($DistrictID, $FarmerGroupID);

        if (!empty($data)) {
            $all[0] = array('id' => 'all', 'label' => 'All');
            $this->response(array_merge($all, $data), 200);
        }
    }

    public function kml_provinsi_get($value='')
    {
        $data = $this->mmaps->getKMLProvinsi();
        $this->response($data, 200);
    }

    public function kml_province_get($value='')
    {
        $data = $this->mmaps->getKMLProvince();
        $this->response($data, 200);
    }

    public function kml_district_get()
    {
        $ProvinceID = $this->get('ProvinceID');
        if (!empty($ProvinceID)) {
            $data = $this->mmaps->getKMLDistrict($ProvinceID);
        } else {
            $data = [];
        }
        $this->response($data,200);
    }

    public function kml_subdistrict_get()
    {
        $DistrictID = $this->get('DistrictID');
        if (!empty($DistrictID)) {
            $data = $this->mmaps->getKMLSubDistrict($DistrictID);
            // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
        } else {
            $data = [];
        }
        $this->response($data,200);
    }

    public function kml_village_get()
    {
        $SubDistrictID = $this->get('SubDistrictID');
        if (!empty($SubDistrictID)) {
            $data = $this->mmaps->getKMLVillage($SubDistrictID);
        } else {
            $data = [];
        }
        $this->response($data,200);
    }

    public function kml_partner_get()
    {
        $this->response($this->mmaps->getKMLPartner($this->get('DistrictID')),200);
    }

    public function kml_kabupaten_get()
    {
        $this->response($this->mmaps->getKMLKabupaten($this->get('ProvinceID')),200);
    }

    public function kml_farmer_group_get()
    {
        $this->response($this->mmaps->getKMLFarmerGroup($this->get('DistrictID')),200);
    }

    public function download_kml_post()
    {
        // set time limit for large files
        set_time_limit(0);
        ini_set('memory_limit', -1);
        // echo '<pre>'; print_r($this->post(null)); echo '</pre>'; exit;
        $ProvinceID    = $this->post('ProvinceID');
        $DistrictID    = $this->post('DistrictID');
        $SubDistrictID = $this->post('SubDistrictID');
        $VillageID     = $this->post('VillageID');
        $year          = $this->post('year');
        $FarmerID      = strlen($this->post('FarmerID') > 0)?$this->post('FarmerID'):'all'; //edited by Ardi
        $PartnerID     = $_SESSION['PartnerID'] == 37 ? $this->post('PartnerID') : $_SESSION['PartnerID']; # partner user
        $status        = $this->post('status'); # polygon status

        $type          = $this->post('type');
        $withFarmerID  = $this->post('withFarmerID') == 'true' ? true : false;

        if ($FarmerID != 'all') {
            $farmer_ids = explode('::', $FarmerID);
        } else {
            $data   = $this->mmaps->getKMLFarmerList($ProvinceID, $DistrictID, $SubDistrictID, $VillageID, $PartnerID, $status);
            // echo "<pre>";print_r($this->db->last_query());echo "</pre>";exit;
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $farmer_ids[] = $value['id'];
                }
            }
        }

        if (!empty($farmer_ids)) {
            $this->load->helper('file');
            $timestamp  = date('YmdHis');
            $file_name       = ($CPGid?$CPGid:($District?$District:($Province?$Province:'polygon')));
            $NamaFileZip = date('YmdHis').'_'.$file_name.'_kml.zip';
            
            $zip = new ZipArchive();
            $zip->open('files/export/'.$NamaFileZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            //Make Temp Dir
            $NamaFolderTemp = $timestamp.'_kmltemp_'.$_SESSION['userid'];
            if (!file_exists('files/export/' . $NamaFolderTemp)) {
                make_directory('files/export/' . $NamaFolderTemp, 0777, true);
            }

            if ($type == 'one') {
                foreach ($farmer_ids as $farmer_id) {
                    if ($farmer_id == 'all') {
                        continue;
                    }

                    $kmlOutput = $this->getKML($farmer_id, null, null, $withFarmerID, $status);
                    // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;

                    if ($kmlOutput !== false) {
                        $name = "KML_{$farmer_id}.kml";
                        $data = $kmlOutput;

                        $filenamepath = 'files/export/'.$NamaFolderTemp.'/'.$name;
                        $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
                        file_put_contents($filenamepath,$data);

                        $zip->addFile($filenamepath,$name);
                    }
                }
            } elseif ($type == 'all') {
                $kmlOutput = $this->getKMLs($farmer_ids, $withFarmerID, $status);
                // echo '<pre>'; print_r($kmlOutput); echo '</pre>'; exit;
                if ($kmlOutput !== false) {
                    $name = 'KML_'.$timestamp.".kml";
                    $data = $kmlOutput;

                    $filenamepath = 'files/export/'.$NamaFolderTemp.'/'.$name;
                    $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
                    file_put_contents($filenamepath,$data);

                    $zip->addFile($filenamepath,$name);
                }
            }
            // remove_directory('files/export/' . $NamaFolderTemp);
            //Finalize Zip
            $zip->close();
            // exit;

            $proses['success'] = true;
            $proses['filedl'] = '/api/files/export/'.$NamaFileZip;
            $this->response($proses, 200);
        } else {
            $this->response([
                'success' => false,
                'message' => 'No KML available'
            ], 400);
        }

        /*if (!empty($farmer_ids)) {
            $this->load->library('zip');
            $timestamp  = date('YmdHis');
            $file_name       = ($FarmerGroupID?$FarmerGroupID:($DistrictID?$DistrictID:($ProvinceID?$ProvinceID:'polygon')));
            if ($type == 'one') {
                foreach ($farmer_ids as $farmer_id) {
                    if ($farmer_id == 'all') {
                        continue;
                    }
                    $kmlOutput = $this->getKML($farmer_id, null, null, $withFarmerID, $rev);
                    // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
                    if ($kmlOutput !== false) {
                        $name = "{$farmer_id}.kml";
                        $data = $kmlOutput;
                        $this->zip->add_data($name, $data);
                    }
                }
            } elseif ($type == 'all') {
                $kmlOutput = $this->getKMLs($farmer_ids, $withFarmerID, $rev);
                // echo '<pre>'; print_r($kmlOutput); echo '</pre>'; exit;
                if ($kmlOutput !== false) {
                    $name = ($FarmerGroupID?$FarmerGroupID:($DistrictID?$DistrictID:'polygon')).".kml";
                    $data = $kmlOutput;
                    $this->zip->add_data($name, $data);
                }
            }
            $this->zip->download("{$file_name}-{$timestamp}.zip");
        }*/

        // $this->response('KML Not Found', 200);
    }

    /*
     * generate KML
     */
    private function getKML($farmer_id, $withFarmerID = false, $status = null)
    {
        $farmer = $this->mmaps->getFarmData($farmer_id, $status);

        if ($farmer) {
            $kml = array('<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document id="root_doc">
<Schema name="Polygon" id="Polygon_FarmerID">
    <SimpleField name="FARMERID" type="float"></SimpleField>
    <SimpleField name="FARMERNAME" type="string"></SimpleField>
    <SimpleField name="GARDENNR" type="int"></SimpleField>
    <SimpleField name="SURVEYNR" type="float"></SimpleField>
    <SimpleField name="REVISION" type="int"></SimpleField>
    <SimpleField name="X" type="float"></SimpleField>
    <SimpleField name="Y" type="float"></SimpleField>
    <SimpleField name="TGLMASUK" type="string"></SimpleField>
    <SimpleField name="AREA_HA" type="float"></SimpleField>
    <SimpleField name="AREA_HA_POLYGON" type="float"></SimpleField>
    <SimpleField name="KETERANGAN" type="string"></SimpleField>
    <SimpleField name="PROVINCE" type="string"></SimpleField>
    <SimpleField name="DISTRICT" type="string"></SimpleField>
    <SimpleField name="SUBDISTRICT" type="string"></SimpleField>
    <SimpleField name="VILLAGE" type="string"></SimpleField>
    <SimpleField name="GROUPNAME" type="string"></SimpleField>
    <SimpleField name="STATUSCHECK" type="string"></SimpleField>
    <SimpleField name="ENUMERATOR_PLANTATION" type="string"></SimpleField>
    <SimpleField name="ENUMERATOR_POLYGON" type="string"></SimpleField>
    <SimpleField name="PARTNER" type="string"></SimpleField>
</Schema>
<Folder><name>Polygon</name>');

            foreach ($farmer as $k => $v) {
                $kml[] = '
    <Placemark>';
                // if ($withFarmerID) { # always use FarmerID
                    $kml[] = "
        <name>{$v['MemberDisplayID']}_{$v['PlotNr']}</name>";
                // }
                $kml[] = '
        <Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
            <ExtendedData><SchemaData schemaUrl="#Polygon_FarmerID">
                <SimpleData name="MEMBERID">'.$v['MemberDisplayID'].'</SimpleData>
                <SimpleData name="MEMBERNAME">' . $v['MemberName'] . '</SimpleData>
                <SimpleData name="PLOTNR">' . $v['PlotNr'] . '</SimpleData>
                <SimpleData name="SURVEYNR">' . $v['SurveyNr'] . '</SimpleData>
                <SimpleData name="REVISION">'.$v['Revision'].'</SimpleData>
                <SimpleData name="X">' . $v['Longitude'] . '</SimpleData>
                <SimpleData name="Y">' . $v['Latitude'] . '</SimpleData>
                <SimpleData name="TGLMASUK">' . $v['DateCreated'] . '</SimpleData>
                <SimpleData name="AREA_HA">' . $v['GardenAreaHa'] . '</SimpleData>
                <SimpleData name="AREA_HA_POLYGON">' . $v['GardenHaPolygon'] . '</SimpleData>
                <SimpleData name="KETERANGAN" type="string"></SimpleData>
                <SimpleData name="PROVINCE" type="string">' . $v['Province'] . '</SimpleData>
                <SimpleData name="DISTRICT" type="string">' . $v['District'] . '</SimpleData>
                <SimpleData name="SUBDISTRICT" type="string">' . $v['SubDistrict'] . '</SimpleData>
                <SimpleData name="VILLAGE" type="string">' . $v['Village'] . '</SimpleData>
                <SimpleData name="GROUPNAME" type="string">' . $v['GroupName'] . '</SimpleData>
                <SimpleData name="STATUSCHECK">' . $v['StatusCheck'] . '</SimpleData>
                <SimpleData name="ENUMERATOR_PLANTATION">' . $v['EnumeratorGarden'] . '</SimpleData>
                <SimpleData name="ENUMERATOR_POLYGON">' . $v['EnumeratorPolygon'] . '</SimpleData>
                <SimpleData name="PARTNER">' . $v['Partner'] . '</SimpleData>
            </SchemaData></ExtendedData>
        <Polygon><altitudeMode>relativeToGround</altitudeMode><outerBoundaryIs><LinearRing><altitudeMode>relativeToGround</altitudeMode><coordinates>';

                // Iterates through the rows, printing a node for each row.
                $coordinates = $this->mmaps->getCoordinates($v['FarmerID'], $v['PlotNr'], $v['SurveyNr'], $v['Revision']);
                if ($coordinates) {
                    foreach ($coordinates as $key => $val) {
                        $kml[] = $val['Longitude'] . ',' . $val['Latitude'];
                    }
                }
                // End XML file
                $kml[] = '</coordinates></LinearRing></outerBoundaryIs></Polygon>
    </Placemark>';
            }

            $kml[] = '
</Folder>
</Document></kml>';
            $kmlOutput = join(" ", $kml);
            return $kmlOutput;
        }
        return false;
    }
    /*
     * generate KML
     */
    private function getKMLs($farmer_ids, $withFarmerID = true, $status = null)
    {
        $kml_start = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document id="root_doc">
<Schema name="Polygon" id="Polygon_FarmerID">
    <SimpleField name="FARMERID" type="float"></SimpleField>
    <SimpleField name="FARMERNAME" type="string"></SimpleField>
    <SimpleField name="GARDENNR" type="int"></SimpleField>
    <SimpleField name="SURVEYNR" type="float"></SimpleField>
    <SimpleField name="REVISION" type="int"></SimpleField>
    <SimpleField name="X" type="float"></SimpleField>
    <SimpleField name="Y" type="float"></SimpleField>
    <SimpleField name="TGLMASUK" type="string"></SimpleField>
    <SimpleField name="AREA_HA" type="float"></SimpleField>
    <SimpleField name="AREA_HA_POLYGON" type="float"></SimpleField>
    <SimpleField name="KETERANGAN" type="string"></SimpleField>
    <SimpleField name="PROVINCE" type="string"></SimpleField>
    <SimpleField name="DISTRICT" type="string"></SimpleField>
    <SimpleField name="SUBDISTRICT" type="string"></SimpleField>
    <SimpleField name="VILLAGE" type="string"></SimpleField>
    <SimpleField name="GROUPNAME" type="string"></SimpleField>
    <SimpleField name="STATUSCHECK" type="string"></SimpleField>
    <SimpleField name="ENUMERATOR_PLANTATION" type="string"></SimpleField>
    <SimpleField name="ENUMERATOR_POLYGON" type="string"></SimpleField>
    <SimpleField name="PARTNER" type="string"></SimpleField>
</Schema>
<Folder><name>Polygon</name>';

        $kmls = array();
        foreach ($farmer_ids as $farmer_id) {
            if ($farmer_id == 'all') {
                continue;
            }

            $farmer = $this->mmaps->getFarmData($farmer_id, $status);
            // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";

            if ($farmer) {
                $kml = '';
                foreach ($farmer as $k => $v) {
                    $kml .= '
        <Placemark>';
                    // if ($withFarmerID) {
                        $kml .= "
            <name>{$v['MemberDisplayID']}_{$v['PlotNr']}</name>";
                    // }
                    $kml .= '
            <Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
                <ExtendedData><SchemaData schemaUrl="#Polygon_FarmerID">
                <SimpleData name="MEMBERID">'.$v['MemberDisplayID'].'</SimpleData>
                <SimpleData name="MEMBERNAME">' . $v['MemberName'] . '</SimpleData>
                <SimpleData name="PLOTNR">' . $v['PlotNr'] . '</SimpleData>
                <SimpleData name="SURVEYNR">' . $v['SurveyNr'] . '</SimpleData>
                <SimpleData name="REVISION">'.$v['Revision'].'</SimpleData>
                <SimpleData name="X">' . $v['Longitude'] . '</SimpleData>
                <SimpleData name="Y">' . $v['Latitude'] . '</SimpleData>
                <SimpleData name="TGLMASUK">' . $v['DateCreated'] . '</SimpleData>
                <SimpleData name="AREA_HA">' . $v['GardenAreaHa'] . '</SimpleData>
                <SimpleData name="AREA_HA_POLYGON">' . $v['GardenHaPolygon'] . '</SimpleData>
                <SimpleData name="KETERANGAN" type="string"></SimpleData>
                <SimpleData name="PROVINCE" type="string">' . $v['Province'] . '</SimpleData>
                <SimpleData name="DISTRICT" type="string">' . $v['District'] . '</SimpleData>
                <SimpleData name="SUBDISTRICT" type="string">' . $v['SubDistrict'] . '</SimpleData>
                <SimpleData name="VILLAGE" type="string">' . $v['Village'] . '</SimpleData>
                <SimpleData name="GROUPNAME" type="string">' . $v['GroupName'] . '</SimpleData>
                <SimpleData name="STATUSCHECK">' . $v['StatusCheck'] . '</SimpleData>
                <SimpleData name="ENUMERATOR_PLANTATION">' . $v['EnumeratorGarden'] . '</SimpleData>
                <SimpleData name="ENUMERATOR_POLYGON">' . $v['EnumeratorPolygon'] . '</SimpleData>
                <SimpleData name="PARTNER">' . $v['Partner'] . '</SimpleData>
                </SchemaData></ExtendedData>
            <Polygon><altitudeMode>relativeToGround</altitudeMode><outerBoundaryIs><LinearRing><altitudeMode>relativeToGround</altitudeMode><coordinates>';

                    // Iterates through the rows, printing a node for each row.
                    $coordinates = $this->mmaps->getCoordinates($v['FarmerID'], $v['PlotNr'], $v['SurveyNr'], $v['Revision']);
                    // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";exit;
                    if ($coordinates) {
                        foreach ($coordinates as $key => $val) {
                            $kml .= $val['Longitude'] . ',' . $val['Latitude'] . ' ';
                        }
                    }
                    // End XML file
                    $kml .= '</coordinates></LinearRing></outerBoundaryIs></Polygon>
        </Placemark>';
                    // echo '<pre>'; print_r($kml); echo '</pre>'; exit;
                }
                $kmls[] = $kml;
            }
        }
        // echo '<pre>'; var_dump($kmls); echo '</pre>'; exit;

        $kml_end = '
</Folder>
</Document></kml>';
        $kmlOutput = $kml_start.join(" ", $kmls).$kml_end;

        return $kmlOutput;

    }

    public function get_datatable_kml_post()
    {
        // set time limit for large files
        set_time_limit(0);
        ini_set('memory_limit', -1);        
        $DataReturn = array();
        
        $ProvinceID    = $this->post('ProvinceID');
        $DistrictID    = $this->post('DistrictID');
        $SubDistrictID = $this->post('SubDistrictID');
        $VillageID     = $this->post('VillageID');
        
        $PartnerID     = $_SESSION['PartnerID'] == 37 ? $this->post('PartnerID') : $_SESSION['PartnerID']; # partner user
        $StatusPolygon = $this->post('StatusPolygon'); # polygon status
        $FarmerStatus  = $this->post('FarmerStatus'); # farmer status
        
        $data = $this->mmaps->getFarmersGroup($ProvinceID, $DistrictID, '', '', $PartnerID, $FarmerStatus);
        
        //Ambil data polygon
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				//Ambil informasi garden terlebih dahulu (Begin)
				$InfoGarden = $this->mmaps->GetInfoGardenPolygonNEWUI($value['ID'], $StatusPolygon);
				if (!empty($InfoGarden)) {
					foreach ($InfoGarden as $k => $v) {
						$InfoGarden[$k]['Name'] = $value['Name'];
						$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
                        $InfoGarden[$k]['Partner'] = $value['Partner'];
                        $InfoGarden[$k]['StatusMember'] = $value['StatusCode'];
					}
				}
				
				$DataReturn = array_merge($DataReturn, $InfoGarden);
				//Ambil informasi garden terlebih dahulu (End)
			}
		}
                
        $a=0;      
        $datatable = array();
        foreach ($DataReturn as $k => $v) { 
            $table_data = array();
            $a++;
            $table_data[] = $a;
            $table_data[] = $v['MemberDisplayID'];
            $table_data[] = $v['PlotNr'];
            $table_data[] = $v['Name'];
            $table_data[] = $v['StatusCheck'];
            $table_data[] = $v['GardenAreaHa'];
            $table_data[] = $v['Partner'];
            $table_data[] = $v['StatusMember'];                        
            $table_data[] = $v['Province'];
            $table_data[] = $v['District'];
            $table_data[] = $v['SubDistrict'];
            $table_data[] = $v['Village'];            
            $table_data[] = $v['CenterLon'];
            $table_data[] = $v['CenterLat'];
            $table_data[] = $v['MemberDisplayID'].'_'.$v['PlotNr'];
            
            $datatable[] = $table_data;               
        }

        $array_data = array (            
            "data"=> $datatable
        );

        echo json_encode($array_data);
        exit();        
    }

    public function get_map_kml_post()
    {
        // set time limit for large files
        set_time_limit(0);
        ini_set('memory_limit', -1);        
        $DataReturn = array();

        $ProvinceID    = $this->post('ProvinceID');
        $DistrictID    = $this->post('DistrictID');
        $SubDistrictID = $this->post('SubDistrictID');
        $VillageID     = $this->post('VillageID');
        
        $PartnerID     = $_SESSION['PartnerID'] == 37 ? $this->post('PartnerID') : $_SESSION['PartnerID']; # partner user
        $StatusPolygon = $this->post('StatusPolygon'); # polygon status
        $FarmerStatus  = $this->post('FarmerStatus'); # farmer status
        
        $data = $this->mmaps->getFarmersGroup($ProvinceID, $DistrictID, '', '', $PartnerID, $FarmerStatus);
        
        //Ambil data polygon
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				//Ambil informasi garden terlebih dahulu (Begin)
				$InfoGarden = $this->mmaps->GetInfoGardenPolygonNEWUI($value['ID'], $StatusPolygon);
				if (!empty($InfoGarden)) {
					foreach ($InfoGarden as $k => $v) {
						$InfoGarden[$k]['Name'] = $value['Name'];
						$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
                        $InfoGarden[$k]['Partner'] = $value['Partner'];
                        $InfoGarden[$k]['StatusMember'] = $value['StatusCode'];
					}
				}
				
				$DataReturn = array_merge($DataReturn, $InfoGarden);
				//Ambil informasi garden terlebih dahulu (End)
			}
		}   

        foreach ($DataReturn as $k => $v) { 
            //Tentukan ambil revisi yang mana
            $points = array();
            $polygon = json_decode($v['Polygon']);            
            foreach ($polygon->coordinates[0] as $key => $val) {                
                $points[$key] = array( 'lat' => $val[1], 'lng' => $val[0]);
            }
            
            $response[] = array(
                'MemberDisplayID' => $v['MemberDisplayID'],
                'PlotNr' => $v['PlotNr'],
                'MemberName' => $v['Name'],
                'StatusCheck' => $v['StatusCheck'],
                'GardenAreaHa' => $v['GardenAreaHa'],
                'Partner' => $v['Partner'],
                'StatusMember' => $v['StatusMember'],                    
                'Province' => $v['Province'],
                'District'=> $v['District'],
                'SubDistrict' => $v['SubDistrict'],
                'Village' => $v['Village'],
                'CenterLat' => $v['CenterLat'],
                'CenterLon' => $v['CenterLon'],                
                'coordinates' => $points                    
            );              
        }
        
        $this->response($response, 200);
    }

    public function export_excel_post() {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $DataReturn = array();

        $ProvinceID    = $this->post('ProvinceID');
        $DistrictID    = $this->post('DistrictID');
        $SubDistrictID = $this->post('SubDistrictID');
        $VillageID     = $this->post('VillageID');
       
        $PartnerID     = $_SESSION['PartnerID'] == 37 ? $this->post('PartnerID') : $_SESSION['PartnerID']; # partner user
        $StatusPolygon = $this->post('StatusPolygon'); # polygon status
        $FarmerStatus  = $this->post('FarmerStatus'); # farmer status

        //GENERATE EXCEL FILE
        $SqlvName = "List_Polygon_PalmOil ";

        $sqlViewName = str_replace(' ','_',$SqlvName);
        $path = './files/tmp/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        //Strip character spesial
        $sqlViewName = preg_replace('/[^A-Za-z0-9\-\_]/', '', $sqlViewName);
        $filePath = 'files/tmp/'.$sqlViewName.date('Y_m_d').'.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();        

        $writer->openToFile($filePath);

        $defaultStyle = (new StyleBuilder())
            ->setFontName('Arial')
            ->setFontSize(11)
            ->setShouldWrapText(false)
            ->build();

        $writer->setDefaultRowStyle($defaultStyle)
            ->openToFile($filePath);

        $borderDefa = (new BorderBuilder())
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->build();


        $styleHeader = (new StyleBuilder())
            ->setFontColor(Color::WHITE)
            ->setBorder($borderDefa)
            ->setBackgroundColor(Color::GREEN)
            ->build();
        $styleData = (new StyleBuilder())
            ->setBorder($borderDefa)
            ->build();

        $styleFormatAngka = (new StyleBuilder())
            ->setBorder($borderDefa)
            ->setFormat('0')
            ->build();
        $styleFormatTanggal = (new StyleBuilder())
            ->setBorder($borderDefa)
            ->setFormat('d-mmm-YY')
            ->build();

        // FARM LOCATION (fl)
        $fl_sheet = $writer->getCurrentSheet();	
        $fl_sheet->setName('Farm Location');

        //GET DATA
        $data = $this->mmaps->getFarmersGroup($ProvinceID, $DistrictID, '', '', $PartnerID, $FarmerStatus); 

        //Ambil data polygon
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				//Ambil informasi garden terlebih dahulu (Begin)
				$InfoGarden = $this->mmaps->GetInfoGardenPolygonNEWUI($value['ID'], $StatusPolygon);
				if (!empty($InfoGarden)) {
					foreach ($InfoGarden as $k => $v) {
						$InfoGarden[$k]['Name'] = $value['Name'];
						$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
                        $InfoGarden[$k]['Partner'] = $value['Partner'];
                        $InfoGarden[$k]['StatusMember'] = $value['StatusCode'];
					}
				}
				
				$DataReturn = array_merge($DataReturn, $InfoGarden);
				//Ambil informasi garden terlebih dahulu (End)
			}
		}

        //GENERATE HEADER
        $dataHeader = array(
            'No.', 'FarmerID', 'FarmNr', 'Farmer Name', 'Status Polygon', 'Ha Polygon', 'Partner', 'Farmer Status'
            ,'Province','District', 'Sub District','Village'
        );

        $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
        $writer->addRow($rowHeader);

        // WRITE ROW DATA
        $no = 1;
        foreach ($DataReturn as $k => $row) {
            $cells = array();                                    

            $cells = [
                WriterEntityFactory::createCell((float) $no, $styleFormatAngka),
                WriterEntityFactory::createCell( $row['MemberDisplayID'], $styleData),
                WriterEntityFactory::createCell( $row['PlotNr'], $styleData),
                WriterEntityFactory::createCell( $row['Name'], $styleData),
                WriterEntityFactory::createCell( $row['StatusCheck'], $styleFormatAngka),
                WriterEntityFactory::createCell( $row['GardenAreaHa'], $styleFormatAngka),
                WriterEntityFactory::createCell( $row['Partner'], $styleData),
                WriterEntityFactory::createCell( $row['StatusMember'], $styleData),
                WriterEntityFactory::createCell( $row['Province'], $styleData),
                WriterEntityFactory::createCell( $row['District'], $styleData),
                WriterEntityFactory::createCell( $row['SubDistrict'], $styleData), 
                WriterEntityFactory::createCell( $row['Village'], $styleData),                       
            ];
            $rowData = WriterEntityFactory::createRow($cells);
            $writer->addRow($rowData);
            $no++;
        }
        
        // CLOSE EXCEL WRITER & DOWNLOAD
        $writer->close();

        $this->response(array('success' => TRUE, 'filenya' => base_url() . $filePath), 200);
        exit;
    }

    public function download_kml_polygon_post()
    {
        // set time limit for large files
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $DataReturn = array();

        $ProvinceID    = $this->post('ProvinceID');
        $DistrictID    = $this->post('DistrictID');
        $SubDistrictID = $this->post('SubDistrictID');
        $VillageID     = $this->post('VillageID');
        
        $FarmerID      = strlen($this->post('FarmerID') > 0)?$this->post('FarmerID'):'all'; //edited by Ardi
        $PartnerID     = $_SESSION['PartnerID'] == 37 ? $this->post('PartnerID') : $_SESSION['PartnerID']; # partner user
        $StatusPolygon = $this->post('StatusPolygon'); # polygon status
        $FarmerStatus  = $this->post('FarmerStatus'); # farmer status

        $withFarmerID  = $this->post('withFarmerID') == 'true' ? true : false;

        //GET DATA
        $data = $this->mmaps->getFarmersGroup($ProvinceID, $DistrictID, '', '', $PartnerID, $FarmerStatus);

         //Ambil data polygon
		if (!empty($data)) {
            $this->load->helper('file');
            $timestamp   = date('YmdHis');
            $file_name   = 'Polygon_PalmOil_'.date('Y_m_d');
            $NamaFileZip = $file_name.'_kml.zip';
            
            $zip = new ZipArchive();
            $zip->open('files/export/'.$NamaFileZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            //Make Temp Dir
            $NamaFolderTemp = $timestamp.'_kmltemp_'.$_SESSION['userid'];
            if (!file_exists('files/export/' . $NamaFolderTemp)) {
                make_directory('files/export/' . $NamaFolderTemp, 0777, true);
            }

            $kml_start = '<?xml version="1.0" encoding="UTF-8"?>
                <kml xmlns="http://www.opengis.net/kml/2.2">
                <Document id="root_doc">
                <Schema name="Polygon" id="Polygon_FarmerID">
                    <SimpleField name="FarmerID" type="float"></SimpleField>
                    <SimpleField name="FarmerName" type="string"></SimpleField>
                    <SimpleField name="FarmNr" type="int"></SimpleField>
                    <SimpleField name="SurveyNr" type="float"></SimpleField>
                    <SimpleField name="Revision" type="int"></SimpleField>
                    <SimpleField name="HaPolygon" type="float"></SimpleField>                    
                    <SimpleField name="Province" type="string"></SimpleField>
                    <SimpleField name="District" type="string"></SimpleField>
                    <SimpleField name="SubDistrict" type="string"></SimpleField>
                    <SimpleField name="Village" type="string"></SimpleField>                    
                    <SimpleField name="StatusPolygon" type="string"></SimpleField>
                    <SimpleField name="StatusFarmer" type="string"></SimpleField>
                    <SimpleField name="EnumPlantation" type="string"></SimpleField>
                    <SimpleField name="EnumPolygon" type="string"></SimpleField>
                    <SimpleField name="Partner" type="string"></SimpleField>
                </Schema>
                <Folder><name>Polygon</name>';

			foreach ($data as $key => $value) {
				//Ambil informasi garden terlebih dahulu (Begin)
				$InfoGarden = $this->mmaps->GetInfoGardenPolygonNEWUI($value['ID'], $StatusPolygon);
				if (!empty($InfoGarden)) {
					foreach ($InfoGarden as $k => $v) {
						$InfoGarden[$k]['Name'] = $value['Name'];
						$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
                        $InfoGarden[$k]['Partner'] = $value['Partner'];
                        $InfoGarden[$k]['StatusMember'] = $value['StatusCode'];                        
					}
				}
				
				$DataReturn = array_merge($DataReturn, $InfoGarden);
				//Ambil informasi garden terlebih dahulu (End)
			}

            $kmls = array();
            if ($DataReturn) {
                $kml = '';
                foreach ($DataReturn as $k => $v) {
                    $kml .= '<Placemark>';                    
                    $kml .= "<name>{$v['MemberDisplayID']}_{$v['PlotNr']}</name>";                    
                    $kml .= '
                    <Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
                        <ExtendedData>
                            <SchemaData schemaUrl="#Polygon_FarmerID">
                                <SimpleData name="FarmerID">'.$v['MemberDisplayID'].'</SimpleData>
                                <SimpleData name="FarmerName">' . $v['Name'] . '</SimpleData>
                                <SimpleData name="FarmNr">' . $v['PlotNr'] . '</SimpleData>
                                <SimpleData name="SurveyNr">' . $v['SurveyNr'] . '</SimpleData>
                                <SimpleData name="Revision">'.$v['Revision'].'</SimpleData>
                                <SimpleData name="HaPolygon ">' . $v['GardenAreaPolygon'] . '</SimpleData>                                
                                <SimpleData name="Province" type="string">' . $v['Province'] . '</SimpleData>
                                <SimpleData name="District" type="string">' . $v['District'] . '</SimpleData>
                                <SimpleData name="SubDistrict" type="string">' . $v['SubDistrict'] . '</SimpleData>
                                <SimpleData name="Village" type="string">' . $v['Village'] . '</SimpleData>
                                <SimpleData name="StatusPolygon" type="string">' . $v['StatusCheck'] . '</SimpleData>
                                <SimpleData name="StatusFarmer">' . $v['StatusMember'] . '</SimpleData>
                                <SimpleData name="EnumPlantation">' . $v['EnumeratorGarden'] . '</SimpleData>
                                <SimpleData name="EnumPolygon">' . $v['EnumeratorPolygon'] . '</SimpleData>
                                <SimpleData name="Partner">' . htmlspecialchars($v['Partner']) . '</SimpleData>
                            </SchemaData>
                        </ExtendedData>
                        <Polygon><altitudeMode>relativeToGround</altitudeMode><outerBoundaryIs><LinearRing><altitudeMode>relativeToGround</altitudeMode><coordinates>';

                    $polygon = json_decode($v['Polygon']);            
                    foreach ($polygon->coordinates[0] as $key => $val) {
                        $kml .= $val[0] . ',' . $val[1] . ' ';
                    }
                    
                    // End XML file
                    $kml .= '</coordinates></LinearRing></outerBoundaryIs></Polygon>
                        </Placemark>';
                }
                $kmls[] = $kml;
            }
            $kml_end = '
                </Folder>
                </Document></kml>';
            $kmlOutput = $kml_start.join(" ", $kmls).$kml_end;
		    
            if ($kmlOutput !== false) {
                $name = 'Polygon_PalmOil_'.date('Y_m_d').".kml";
                $data = $kmlOutput;

                $filenamepath = 'files/export/'.$NamaFolderTemp.'/'.$name;
                $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
                file_put_contents($filenamepath,$data);

                $zip->addFile($filenamepath,$name);
            }
           
            //Finalize Zip
            $zip->close();
            $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/export/'.$NamaFileZip), 200);
        }else {
            $this->response([
                'success' => false,
                'message' => 'No KML available'
            ], 400);
        }
    }

    public function download_kml_point_post()
    {
        // set time limit for large files
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $DataReturn = array();

        $ProvinceID    = $this->post('ProvinceID');
        $DistrictID    = $this->post('DistrictID');
        $SubDistrictID = $this->post('SubDistrictID');
        $VillageID     = $this->post('VillageID');
        
        $FarmerID      = strlen($this->post('FarmerID') > 0)?$this->post('FarmerID'):'all'; //edited by Ardi
        $PartnerID     = $_SESSION['PartnerID'] == 37 ? $this->post('PartnerID') : $_SESSION['PartnerID']; # partner user
        $StatusPolygon = $this->post('StatusPolygon'); # polygon status
        $FarmerStatus  = $this->post('FarmerStatus'); # farmer status

        $withFarmerID  = $this->post('withFarmerID') == 'true' ? true : false;

        //GET DATA
        $data = $this->mmaps->getFarmersGroup($ProvinceID, $DistrictID, '', '', $PartnerID, $FarmerStatus);

         //Ambil data polygon
		if (!empty($data)) {
            $this->load->helper('file');
            $timestamp  = date('YmdHis');
            $file_name   = 'Center_Polygon_PalmOil_'.date('Y_m_d');
            $NamaFileZip = $file_name.'_kml.zip';
            
            $zip = new ZipArchive();
            $zip->open('files/export/'.$NamaFileZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            //Make Temp Dir
            $NamaFolderTemp = $timestamp.'_kmltemp_'.$_SESSION['userid'];
            if (!file_exists('files/export/' . $NamaFolderTemp)) {
                make_directory('files/export/' . $NamaFolderTemp, 0777, true);
            }

            $kml_start = '<?xml version="1.0" encoding="UTF-8"?>
                <kml xmlns="http://www.opengis.net/kml/2.2">
                <Document id="root_doc">
                <Schema name="Polygon" id="Polygon_FarmerID">
                    <SimpleField name="FarmerID" type="float"></SimpleField>
                    <SimpleField name="FarmerName" type="string"></SimpleField>
                    <SimpleField name="FarmNr" type="int"></SimpleField>                     
                    <SimpleField name="Latitude" type="float"></SimpleField> 
                    <SimpleField name="Longitude" type="float"></SimpleField>               
                    <SimpleField name="HaPolygon" type="float"></SimpleField>                    
                    <SimpleField name="Province" type="string"></SimpleField>
                    <SimpleField name="District" type="string"></SimpleField>
                    <SimpleField name="SubDistrict" type="string"></SimpleField>
                    <SimpleField name="Village" type="string"></SimpleField>                    
                    <SimpleField name="StatusPolygon" type="string"></SimpleField>
                    <SimpleField name="StatusFarmer" type="string"></SimpleField>
                    <SimpleField name="EnumPlantation" type="string"></SimpleField>
                    <SimpleField name="EnumPolygon" type="string"></SimpleField>
                    <SimpleField name="Partner" type="string"></SimpleField>
                </Schema>
                <Folder><name>Point</name>';

			foreach ($data as $key => $value) {
				//Ambil informasi garden terlebih dahulu (Begin)
				$InfoGarden = $this->mmaps->GetInfoGardenPolygonNEWUI($value['ID'], $StatusPolygon);
				if (!empty($InfoGarden)) {
					foreach ($InfoGarden as $k => $v) {
						$InfoGarden[$k]['Name'] = $value['Name'];
						$InfoGarden[$k]['MemberDisplayID'] = $value['MemberDisplayID'];
                        $InfoGarden[$k]['Partner'] = $value['Partner'];
                        $InfoGarden[$k]['StatusMember'] = $value['StatusCode'];                        
					}
				}
				
				$DataReturn = array_merge($DataReturn, $InfoGarden);
				//Ambil informasi garden terlebih dahulu (End)
			}
        
            $kmls = array();
            if ($DataReturn) {
                $kml = '';
                foreach ($DataReturn as $k => $v) {
                    $kml .= '<Placemark>';                    
                    $kml .= "<name>{$v['MemberDisplayID']}_{$v['PlotNr']}</name>";                    
                    $kml .= '
                    <Style><LineStyle><color>ff0000ff</color><width>3</width></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
                        <ExtendedData>
                            <SchemaData schemaUrl="#Polygon_FarmerID">
                                <SimpleData name="FarmerID">'.$v['MemberDisplayID'].'</SimpleData>
                                <SimpleData name="FarmerName">' . $v['Name'] . '</SimpleData>
                                <SimpleData name="FarmNr">' . $v['PlotNr'] . '</SimpleData>
                                <SimpleData name="Latitude">' . $v['CenterLat'] . '</SimpleData> 
                                <SimpleData name="Longitude">' . $v['CenterLon'] . '</SimpleData>                          
                                <SimpleData name="HaPolygon ">' . $v['GardenAreaPolygon'] . '</SimpleData>                                
                                <SimpleData name="Province" type="string">' . $v['Province'] . '</SimpleData>
                                <SimpleData name="District" type="string">' . $v['District'] . '</SimpleData>
                                <SimpleData name="SubDistrict" type="string">' . $v['SubDistrict'] . '</SimpleData>
                                <SimpleData name="Village" type="string">' . $v['Village'] . '</SimpleData>
                                <SimpleData name="StatusPolygon" type="string">' . $v['StatusCheck'] . '</SimpleData>
                                <SimpleData name="StatusFarmer">' . $v['StatusMember'] . '</SimpleData>
                                <SimpleData name="EnumPlantation">' . $v['EnumeratorGarden'] . '</SimpleData>
                                <SimpleData name="EnumPolygon">' . $v['EnumeratorPolygon'] . '</SimpleData>
                                <SimpleData name="Partner" type="string">' . htmlspecialchars($v['Partner']) . '</SimpleData>
                            </SchemaData>
                        </ExtendedData>
                        <Point>
                            <coordinates>'.$v['CenterLat'].','.$v['CenterLon'].',0</coordinates>
                        </Point>';                                        
                    $kml .= '</Placemark>';
                }
                $kmls[] = $kml;
            }
            $kml_end = '
            </Folder>
            </Document></kml>';
            $kmlOutput = $kml_start.join(" ", $kmls).$kml_end;
		    
            if ($kmlOutput !== false) {
                $name = 'Center_Polygon_PalmOil_'.date('Y_m_d').".kml";
                $data = $kmlOutput;

                $filenamepath = 'files/export/'.$NamaFolderTemp.'/'.$name;
                $filenamepath = filter_var($filenamepath,FILTER_SANITIZE_STRING);
                file_put_contents($filenamepath,$data);

                $zip->addFile($filenamepath,$name);
            }
           
            //Finalize Zip
            $zip->close();
            $this->response(array('success' => TRUE, 'filenya' => base_url() . 'files/export/'.$NamaFileZip), 200);
        }else {
            $this->response([
                'success' => false,
                'message' => 'No KML available'
            ], 400);
        }
    }

}

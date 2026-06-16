<?php defined('BASEPATH') OR exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Geospatial_new extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('mmaps_new');
        // $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'dummy'));
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    function maps_get() {

        $data = $this->mmaps_new->readMaps($_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function map_get($param1,$value1,$param2,$value2,$param3,$value3='',$param4,$value4='') {
        if ($value2=='undefined') $value2='';
        if ($value3=='skop') {
            $value3='';
            $value4 = $param4;
        }
        $data = $this->mmaps_new->readMap($value1,$value2,urldecode($value3),$value4);
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
            $data = $this->mmaps_new->readBankMap($value1,$value2,$value3,$value4);
             // Save into the cache for 5 minutes
        //     $this->cache->save($key, $data, 300);
        // }
        
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function districtmap_get($param1,$value1,$param2,$value2,$param3,$value3='',$param4,$value4='') {
        if ($value2=='undefined') $value2='';
        if ($value3=='skop') {
            $value3='';
            $value4 = $param4;
        }
        $data = $this->mmaps_new->getDistrictCount($value1,$value2,$value3,$value4);
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
            $data = $this->mmaps_new->getBankDistrictCount($value1,$value2,$value3,$value4);

             // Save into the cache for 5 minutes
        //     $this->cache->save($key, $data, 300);
        // }
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function area_put()
    {
        $farmer_id = $this->put('farmer_id');
        $garden_nr = $this->put('garden_nr');
        $survey_nr = $this->put('survey_nr');
        if(empty($farmer_id) || empty($garden_nr) || !isset($survey_nr)) {
            $this->response(NULL, 400);
        }
        $data = $this->mmaps_new->updateGardenArea($this->put('area'),$this->put('farmer_id'),$this->put('garden_nr'),$this->put('survey_nr'));
        $data = $this->mmaps_new->updateGarden($this->put('area_hectare'),$this->put('farmer_id'),$this->put('garden_nr'),$this->put('survey_nr'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function map_post() {
        if(!$this->post('name')) $this->response(NULL, 400);
        $data = $this->mmaps_new->createTraining($this->post('name'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function map_put() {
        if(!$this->put('id')) $this->response(NULL, 400);
        $data = $this->mmaps_new->updateTraining($this->put('name'),$this->put('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function map_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mmaps_new->deleteTraining($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

    function district_get() {
        $district = $this->mmaps_new->readDistrict($this->get('ProvinceID'),$this->muserprofile->getUserProfile());
        if($district) $this->response($district, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    function province_get() {
        $province = $this->mmaps_new->readProvince($this->muserprofile->getUserProfile());
        // if($province) 
            $this->response($province, 200);
        // else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    public function area_get()
    {
        $farmer = $this->get('farmer');
        $garden = $this->get('garden');
        $survey = $this->get('survey');

        $area = array();
        $result = $this->mmaps_new->readArea($farmer, $garden, $survey);
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
        $result['data'] = $this->mmaps_new->readClonal($id);
        $result['area'] = $this->mmaps_new->readClonalArea($id);
        
        if($result) $this->response($result, 200);

    }
    public function area_calc_get()
    {
        set_time_limit(0);
        $farmer = $this->get('farmer');
        $garden = $this->get('garden');
        $survey = $this->get('survey');
        // if (empty($farmer)) {
        //     exit('Please provide farmer id');
        // }
        // if (empty($garden)) {
        //     $garden = 1;
        // }
        $count = $this->area_calc($farmer, $garden, $survey);
        exit;
        $this->response($count, 200);
    }

    public function updateEmptyHaPolygon_get()
    {
        $gardens = $this->mmaps_new->listEmptyGardenHaPolygon();
        if (!empty($gardens)) {
            foreach ($gardens as $key => $garden) {
                $this->area_calc($garden['FarmerID'], $garden['GardenNr'], $garden['SurveyNr']);
            }
        }
    }

    private function area_calc($farmer='', $garden='', $survey='')
    {
        echo '<pre>'; print_r('Calc Area'); echo '</pre>'; 
        // get all gardens
        $gardens    = $this->mmaps_new->readGarden($farmer, $garden, $survey);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // echo "<pre>"; print_r($gardens); echo "</pre>"; 
        $count      = 0;
        if (!empty($gardens)) {
            foreach ($gardens as $key => $garden) {
                // get garden area
                $points = $this->mmaps_new->readArea($garden['FarmerID'], $garden['GardenNr'], $garden['SurveyNr']);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
                if (!empty($points)) {
                    // calc area in meters square
                    $area = $this->PlanarPolygonAreaMeters($points);
                    // area in hectare
                    $area /= 10000; 
                    // update area
                    if ($this->mmaps_new->updateGarden($area, $points[0]['Revision'], $garden['FarmerID'], $garden['GardenNr'], $garden['SurveyNr'])) {
                        $count++;
                    }
                    echo "<pre>"; print_r($this->db->last_query()); echo "</pre>";
                } else {
                    echo '<pre>Area Emty : '; print_r($this->db->last_query()); echo '</pre>'; 
                }
            }
            // $this->mmaps_new->updateGardenHaPolygon();
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
            $query = "INSERT INTO `ktv_cocoa_farmer_garden_area` (
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
                        // $garden_nr = intval($value->ExtendedData->SchemaData->GARDEN);
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
                        $garden_nr = intval($value->ExtendedData->SchemaData->SimpleData[$GardenNr_key]);
                        $survey_nr = intval($value->ExtendedData->SchemaData->SimpleData[$SurveyNr]);
                        echo '<pre>'; print_r(compact('farmer_id','garden_nr','survey_nr')); echo '</pre>'; 

                        /*$exist = $this->mmaps_new->isExistGardenArea($farmer_id, $garden_nr, $survey_nr);
                        echo '<pre> IS EXIST : '; var_dump($exist); echo '</pre>'; 
                        if ($this->get('replace') == 1) {
                            $this->mmaps_new->deleteGardenArea($farmer_id, $garden_nr, $survey_nr);
                            echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                            $exist = false;
                        }
                        if (empty($farmer_id) OR empty($garden_nr) OR $exist) {
                            echo '<pre>'; print_r('continue'); echo '</pre>'; 
                            $errors[] = array('FarmerID' => $farmer_id, 'GardenNr' => $garden_nr, 'SurveyNr' => $survey_nr);
                            continue;
                        }*/
                        
                        $lastRevision = $this->mmaps_new->checkLastRevision($farmer_id, $garden_nr, $survey_nr);
                        echo '<pre>Latest Rev : '; print_r($lastRevision); echo '</pre>'; 
                        $revision = $lastRevision+1;
                        $coordinates = explode(' ', $coordinates);
                        $order = 1;
                        // $this->db->trans_start(TRUE);

                        $sql = $query;
                        $values = array();
                        foreach ($coordinates as $coord) {
                            $latlng = explode(',', $coord);
                            $value = "({$farmer_id}, {$garden_nr}, {$survey_nr}, {$order}, {$latlng[1]}, {$latlng[0]}, {$revision}, 'verified', NOW(), {$_SESSION['userid']})\n";
                            $values[] = $value;
                            // $data = array(
                            //     'FarmerID'      => $farmer_id,
                            //     'GardenNr'      => $garden_nr,
                            //     'SurveyNr'      => $survey_nr,
                            //     'OrderNr'       => $order,
                            //     'Latitude'      => $latlng[1],
                            //     'Longitude'     => $latlng[0]
                            //     );
                            // $this->mmaps_new->insertGardenArea($data);
                            $order++;
                        }
                        $sql .= implode(', ', $values);
                        echo '<pre>'; print_r($sql); echo '</pre>';
                        $this->db->query($sql);
                        $this->area_calc($farmer_id,$garden_nr,$survey_nr);
                        // $this->db->trans_complete();
                        // exit;
                        $success[] = array('FarmerID' => $farmer_id, 'GardenNr' => $garden_nr, 'SurveyNr' => $survey_nr);
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
        $province       = $this->get('province');
        $partner        = $this->get('partner');
        $warehouse      = $this->get('warehouse');
        $certification  = $this->get('certification');
        $start          = $this->get('start');
        $end            = $this->get('end');

        //$data = $this->mmaps_new->getSupplyChain($start, $end, $province, $partner, $certification, $warehouse);
        $data = $this->mmaps_new->getSupplyChainNew($start, $end, $province, $partner, $certification, $warehouse);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function supplychain_cpg_get()
    {
        $traderid   = $this->get('traderid');
        $start      = $this->get('start');
        $end        = $this->get('end');

        $data = $this->mmaps_new->getSupplyChainCPG($traderid, $start, $end);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function supplychain_farmer_get()
    {
        $id         = $this->get('id');
        $supply_id  = $this->get('supply_id');
        $partner    = $this->get('partner');
        $certification    = $this->get('certification');
        $start      = $this->get('start');
        $end        = $this->get('end');
        $wh         = $this->get('warehouse');

        $data = $this->mmaps_new->getSupplyChainFarmer($id, $supply_id, $start, $end, $partner, $certification, $wh);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function supplychain_refinery_get()
    {
        $id         = $this->get('id');
        $supply_id  = $this->get('supply_id');
        $partner    = $this->get('partner');
        $certification    = $this->get('certification');
        $start      = $this->get('start');
        $end        = $this->get('end');
        $wh         = $this->get('warehouse');

        $data = $this->mmaps_new->getSupplyChainRefinery($id, $supply_id, $start, $end, $partner, $certification, $wh);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
        
        $this->response($data, 200);
    }

    public function weather_get()
    {
        $result = $this->mmaps_new->getWeather(date('Y-m-d'));
        $this->response($result);
    }

    public function weather_fetch_get()
    {
        $currentDate    = date('Y-m-d');
        $districts      = $this->mmaps_new->getDistricts();
        $values         = array();
        if (!empty($districts)) {
            foreach ($districts as $key => $district) {
                $data       = $this->mmaps_new->getDistrictWeather($district['District']);
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

                // $this->mmaps_new->insertWeatherLog($district['District'], $currentDate, $data);
                $values[] = "
                ('" . implode("','", $value) . "')";
            }
        }
        $values = implode(', ', $values);
        $result = $this->mmaps_new->batchInsertWeatherLog($values);
        echo "Fetch weather run at ".date('Y-m-d H:i:s');
        exit;
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
    }

    public function bank_get()
    {
        $DistrictID     = $this->get('DistrictID');
        $BankID         = $this->get('BankID');
        $data = $this->mmaps_new->getBank($DistrictID, $BankID);
        $this->response($data, 200);
    }

    public function partner_get()
    {
        $ProvinceID     = $this->get('ProvinceID');
        $data = $this->mmaps_new->getProvincePartner($ProvinceID);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($data, 200);
    }
    
    public function new_partner_get()
    {
        //$ProvinceID     = $this->get('ProvinceID');
        $data = $this->mmaps_new->getNewProvincePartner($_SESSION['PartnerID']);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($data, 200);
    }

    public function warehouse_get()
    {
        $PartnerID     = $this->get('PartnerID');
        $data = $this->mmaps_new->getPartnerWarehouse($PartnerID);
        $this->response($data, 200);
    }

    public function garden_detail_get()
    {
        $FarmerID = $this->get('FarmerID');
        $GardenNr = $this->get('GardenNr');
        $data = $this->mmaps_new->getGardenDetail($FarmerID, $GardenNr);
        $this->response($data, 200);
    }

    public function supply_profile_farmer_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent     = $this->get('parent');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyProfileFarmer($id, $start, $end, $wh, $cert, $parent);

        $this->response($data, 200);
    }

    public function supply_profile_refinery_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent     = $this->get('parent');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyProfileRefinery($id, $start, $end, $wh, $cert, $parent);

        $this->response($data, 200);
    }

    public function supply_transaction_farmer_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent     = $this->get('parent');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyTransactionFarmerNew($id, $start, $end, $wh, $cert, $parent);

        $this->response($data, 200);
    }

    public function export_supply_transaction_farmer_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent     = $this->get('parent');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $dataList = $this->mmaps_new->getSupplyTransactionFarmerNew($id, $start, $end, $wh, $cert, $parent);

        if(count($dataList)){

            //Kolom Header Transaction
            $dataHeader = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Transaction

            //Kolom Body Transaction
            $dataListExcel = array();
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Transaction
            

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_transaction_farmer.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
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

            //style
            $styleHeader = (new StyleBuilder())
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            
            $writer->getCurrentSheet()->setName('Transaction Data');
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = 25569 + (strtotime($dataRows[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
    
            $writer->close();
    
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename(base_url() . $filePath) . "\""); 
            readfile(base_url() . $filePath); 
            die;
        }
    }

    public function supply_transaction_refinery_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent     = $this->get('parent');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyTransactionRefineryNew($id, $start, $end, $wh, $cert, $parent);

        $this->response($data, 200);
    }

    public function export_supply_transaction_refinery_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        $parent     = $this->get('parent');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $dataList = $this->mmaps_new->getSupplyTransactionRefineryNew($id, $start, $end, $wh, $cert, $parent);

        if(count($dataList)){

            //Kolom Header Transaction
            $dataHeader = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Transaction

            //Kolom Body Transaction
            $dataListExcel = array();
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Transaction
            

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_transaction_refinery.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
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

            //style
            $styleHeader = (new StyleBuilder())
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            
            $writer->getCurrentSheet()->setName('Transaction Data');
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = 25569 + (strtotime($dataRows[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
    
            $writer->close();
    
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename(base_url() . $filePath) . "\""); 
            readfile(base_url() . $filePath); 
            die;
        }
    }

    public function supply_profile_koperasi_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyProfileKoperasi($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_transaction_koperasi_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyTransactionKoperasi($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_profile_sce_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyProfileSCE($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_transaction_sce_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyTransactionSCE($id, $start, $end, $wh, $cert);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $this->response($data, 200);
    }

    public function supply_profile_trader_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        //$data = $this->mmaps_new->getSupplyProfilePedagang($id, $start, $end, $wh, $cert);
        $data = $this->mmaps_new->getSupplyProfilePedagangNew($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_transaction_trader_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyTransactionPedagangNew($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function export_supply_transaction_trader_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $dataList = $this->mmaps_new->getSupplyTransactionPedagangNew($id, $start, $end, $wh, $cert);
        
        if(count($dataList) > 0){

            //Kolom Header Transaction
            $dataHeader = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Transaction

            //Kolom Body Transaction
            $dataListExcel = array();
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Transaction
            

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_transaction_trader.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
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

            //style
            $styleHeader = (new StyleBuilder())
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            
            $writer->getCurrentSheet()->setName('Transaction Data');
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = 25569 + (strtotime($dataRows[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
    
            $writer->close();
    
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename(base_url() . $filePath) . "\""); 
            readfile(base_url() . $filePath); 
            die;
        }

        // $this->response($filePath);
        
    }

    public function supply_profile_warehouse_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        //$data = $this->mmaps_new->getSupplyProfileWarehouse($id, $start, $end, $wh, $cert);
        $data = $this->mmaps_new->getSupplyProfileWarehouseNew($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function supply_transaction_warehouse_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $data = $this->mmaps_new->getSupplyTransactionWarehouseNew($id, $start, $end, $wh, $cert);

        $this->response($data, 200);
    }

    public function export_supply_transaction_warehouse_get()
    {
        $id     = $this->get('id');
        $start  = $this->get('start');
        $end    = $this->get('end');
        $cert   = $this->get('certification');
        $wh     = $this->get('warehouse');
        //$wh     = $this->mmaps_new->getWarehouseID($this->get('partner'));
        $dataList = $this->mmaps_new->getSupplyTransactionWarehouseNew($id, $start, $end, $wh, $cert);

        if(count($dataList)){

            //Kolom Header Transaction
            $dataHeader = array('No');
            foreach($dataList[0] as $key => $value){
                array_push($dataHeader,lang($key));
            }
            //Kolom Header Transaction

            //Kolom Body Transaction
            $dataListExcel = array();
            $no = 1;
            foreach ($dataList as $key => $value) {
                $data = array();
                array_push($data,$no);
                foreach($value as $keyx => $valuex){
                    array_push($data,$valuex);
                }
                $dataListExcel[$key] = $data;
                $no++;
            }
            //Kolom Body Transaction
            

            $writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files// 
            $namaFile = date('YmdHis') . '_export_excel_transaction_warehouse.xlsx';
            $filePath = 'files/tmp/' . $namaFile;
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

            //style
            $styleHeader = (new StyleBuilder())
                ->setFontColor(Color::WHITE)
                ->setBorder($borderDefa)
                ->setBackgroundColor(Color::GREEN)
                ->build();

            //row header
            $rowHeader = WriterEntityFactory::createRowFromArray($dataHeader, $styleHeader);
            
            $writer->getCurrentSheet()->setName('Transaction Data');
            $writer->addRow($rowHeader);

            $styleData = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->build();

            $styleFormatAngka = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('0')
                ->build();

            $styleFormatTanggal = (new StyleBuilder())
                ->setBorder($borderDefa)
                ->setFormat('YYYY-mm-dd')
                ->build();

            for ($i=0; $i < count($dataListExcel); $i++) {
                $dataRows = $dataListExcel[$i];
                $cells = array();
    
                for ($j=0; $j < count($dataRows); $j++) {
                    $styleRow = null;
                    $dataRow = null;
    
                    //cek apakah numeric
                    if(is_numeric($dataRows[$j])){
                        $styleRow = $styleFormatAngka;
                        $dataRow = $dataRows[$j];
                    } else {
                        //cek apakah tanggal
                        if($this->validateDate($dataRows[$j]) == true) {
                            $styleRow = $styleFormatTanggal;
                            $dataRow = 25569 + (strtotime($dataRows[$j]) / 86400);
                        } else {
                            $styleRow = $styleData;
                            $dataRow = $dataRows[$j];
                        }
                    }
    
                    $cells[$j] = WriterEntityFactory::createCell($dataRow, $styleRow);
                }
                /*$cells = [
                    WriterEntityFactory::createCell($dataRows[0], $styleData),
                    WriterEntityFactory::createCell((float) $dataRows[1], $styleFormatAngka),
                    WriterEntityFactory::createCell($dataRows[2], $styleData),
                    WriterEntityFactory::createCell(25569 + (time() / 86400), $styleFormatTanggal),
                    WriterEntityFactory::createCell($dataRows[4], $styleFormatTanggal)
                ];*/
    
                $rowData = WriterEntityFactory::createRow($cells);
                $writer->addRow($rowData);
            }
    
            $writer->close();
    
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename(base_url() . $filePath) . "\""); 
            readfile(base_url() . $filePath); 
            die;
        }
    }

    public function kml_farmer_list_get()
    {
        $Province   = $this->get('Province');
        $District   = $this->get('District');
        $cpg        = $this->get('cpg');
        $data       = $this->mmaps_new->getKMLFarmerList($Province, $District, $cpg);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        // echo '<pre>'; print_r($data); echo '</pre>'; exit;
        if (!empty($data)) {
            $all[0] = array('id' => 'all', 'label' => 'All');
            $this->response(array_merge($all, $data), 200);
        }
        // $this->response($data, 200);
    }

    public function kml_kabupaten_get()
    {
        $this->response($this->mmaps_new->getKMLKabupaten($this->get('key')),200);
    }

    public function kml_cpg_get()
    {
        $this->response($this->mmaps_new->getKMLCPG($this->get('kab')),200);
    }

}

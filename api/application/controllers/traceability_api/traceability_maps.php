<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Traceability_maps extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('traceability/api/mtraceability_maps', '_model');

		// dummy data---------------------------------------------------------------------------------------------------------------------------------------------
			// Entity / Actor
			// Detail Actor
			$actor[1] = array("SupplierID" => "1", "LocationID" => "1", "Latitude"=> -4.613263, "Longitude" => 119.981748, "Tipe"=>"Warehouse");
			$actor[2] = array("SupplierID" => "2", "LocationID" => "2", "Latitude"=> -4.513263, "Longitude" => 119.981748, "Tipe"=>"Tier 1 Supplier");
			$actor[3] = array("SupplierID" => "3", "LocationID" => "3", "Latitude"=> -4.413263, "Longitude" => 119.981748, "Tipe"=>"Tier 2 Supplier");
			$actor[4] = array("SupplierID" => "4", "LocationID" => "4", "Latitude"=> -4.313263, "Longitude" => 119.781748, "Tipe"=>"Farmer With Transactions");
			$actor[5] = array("SupplierID" => "5", "LocationID" => "5", "Latitude"=> -4.313263, "Longitude" => 120.081748, "Tipe"=>"Farmer With Transactions");
			$actor[6] = array("SupplierID" => "6", "LocationID" => "5", "Latitude"=> -4.313263, "Longitude" => 120.081748, "Tipe"=>"Farmer With Transactions");
			$actor[7] = array("SupplierID" => "6", "LocationID" => "6", "Latitude"=> -4.313263, "Longitude" => 120.081748, "Tipe"=>"Farmer With Transactions");

			// Total Actor
			$total["Warehouse"] 				= 99;
			$total["Tier1Supplier"] 			= 99;
			$total["Tier2Supplier"] 			= 99;
			$total["FarmerWithTransactions"]	= 99;
			

		// transaction
			$transaction[0]= array("ID_Trans" => "0", "From" => $actor[2], "To" => $actor[1]); 
			$transaction[1]= array("ID_Trans" => "1", "From" => $actor[3], "To" => $actor[2]); 
			$transaction[2]= array("ID_Trans" => "2", "From" => $actor[4], "To" => $actor[3]); 
			$transaction[3]= array("ID_Trans" => "3", "From" => $actor[5], "To" => $actor[3]); 
			$transaction[4]= array("ID_Trans" => "4", "From" => $actor[6], "To" => $actor[3]); 
			$transaction[5]= array("ID_Trans" => "5", "From" => $actor[7], "To" => $actor[3]); 
		// -----------------------------------------------

		$this->dummy_data["transaction"] = $transaction;
		$this->dummy_data["actor"] = $actor;
		$this->dummy_data["total"] = $total;
		// dummy data---------------------------------------------------------------------------------------------------------------------------------------------


	}

    public function get_combo_warehouse_get()
    {
        $data = $this->_model->getComboWarehouse();
        $this->response($data, 200);
    }
	
	public function get_combo_tier_1_get()
    {
        $data = $this->_model->getComboTier1($this->get());
        $this->response($data, 200);
    }

    public function get_combo_tier_2_get()
    {
        $data = $this->_model->getComboTier2($this->get());
        $this->response($data, 200);
    }

    public function get_relation_get($defaultGet = array())
	{
		if(count($defaultGet) == 0){
			$get = $this->get();
		}else{
			$get = $defaultGet;
		}
		$showFarmer = '';
		$get['showFarmer'] = $showFarmer;
		$total["Warehouse"] 				= 0;
		$total["Tier1Supplier"] 			= 0;
		$total["Tier2Supplier"] 			= 0;
		$total["FarmerWithTransactions"]	= 0;

		$query = $this->_model->getMarkerRelationPalm($get);
		
		$i = 0;
		$j = 0;
		if(count($query['data']) > 0){
			foreach ($query['data'] as $key => $value) {

				$i++;
				$actor[$i]['SupplierID'] = $value['wh_supplychainid'];
				$actor[$i]['LocationID'] = $i;
				$actor[$i]['Latitude'] = $value['lat_1'];
				$actor[$i]['Longitude'] = $value['long_1'];
				$actor[$i]['Tipe'] = 'Mill';
				$actor[$i]['LatitudeParent'] = '';
				$actor[$i]['LongitudeParent'] = '';
				if($showFarmer!=''){
					$transaction[$j]= array("ID_Trans" => $j, "From" => $actor[$i-1], "To" => $actor[$i-1]);
					$j++;
				}
				if($value['2_latitude']!=''){
					$i++;
					$actor[$i]['SupplierID'] = $value['2_supplychainid'];
					$actor[$i]['LocationID'] = $i;
					$actor[$i]['Latitude'] = $value['2_latitude'];
					$actor[$i]['Longitude'] = $value['2_longitude'];
					$actor[$i]['Tipe'] = $value['2_orgtype'];
					$actor[$i]['LatitudeParent'] = '';
					$actor[$i]['LongitudeParent'] = '';

					$FromActor = array_reverse($actor);
					
					$transaction[$j]= array("ID_Trans" => $j, "From" => $FromActor[0], "To" => $actor[$i-1]);
					$j++;
				}
				if($value['1_latitude']!=''){
					$i++;
					$actor[$i]['SupplierID'] = $value['1_supplychainid'];
					$actor[$i]['LocationID'] = $i;
					$actor[$i]['Latitude'] = $value['1_latitude'];
					$actor[$i]['Longitude'] = $value['1_longitude'];
					$actor[$i]['Tipe'] = $value['1_orgtype'];
					$actor[$i]['LatitudeParent'] = '';
					$actor[$i]['LongitudeParent'] = '';

					$FromActor = array_reverse($actor);
					
					$transaction[$j] = array("ID_Trans" => $j, "From" => $FromActor[0], "To" => $actor[$i-1]);
					$j++;
				}

				if($value['3_latitude']!=''){
					$i++;
					$actor[$i]['SupplierID'] = $value['3_supplychainid'].'_'.$value['3_supplychainid'];
					$actor[$i]['LocationID'] = $i;
					$actor[$i]['Latitude'] = $value['3_latitude'];
					$actor[$i]['Longitude'] = $value['3_longitude'];
					$actor[$i]['Tipe'] = 'Farmer With Transactions';
					$actor[$i]['LatitudeParent'] ='';
					$actor[$i]['LongitudeParent'] = '';
					
					$FromActor = array_reverse($actor);

					$transaction[$j]= array("ID_Trans" => $j, "From" => $FromActor[0], "To" => $actor[$i-1]);
					$j++;
				}
				
			}
			$total = @$query['total'];
		}

		$data["transaction"] = $transaction;
		$data["actor"] = $actor;
		$data["total"] = $total;	
		
		if(count($defaultGet) == 0){
			$this->response($data, 200);	
		}else{
			return $data;
		}
	}

	public function get_relation_farmer_get()
	{
		//echo "<pre>".print_r($this->get(), 1);die;
		$data["transaction"] = array();
		$data["actor"] = array();

		$get = $this->get();
		$data = $this->get_relation_get($get);

		$i = intval(count($data['actor']));
		$j = intval(count($data['transaction']));

		
		$kode = $this->get('to_id').rand(pow(10, 2-1), pow(10, 2)-1);
		$query = $this->_model->getMarkerRelationFarmer($this->get());
		if(count($query) > 0){
			foreach ($query as $key => $value) {
				$i++;
				$data["actor"][$i]['SupplierID'] = $value['supplyorgid_1'];
				$data["actor"][$i]['LocationID'] = $kode.$i;
				$data["actor"][$i]['Latitude'] = $value['lat_1'];
				$data["actor"][$i]['Longitude'] = $value['long_1'];
				$data["actor"][$i]['Tipe'] = $value['type_1'];
				$data["actor"][$i]['LatitudeParent'] = '';
				$data["actor"][$i]['LongitudeParent'] = '';
				
				$i++;
				$data["actor"][$i]['SupplierID'] = $value['FarmerID'].'_'.$value['supplyorgid_1'];
				$data["actor"][$i]['LocationID'] = $kode.$i;
				$data["actor"][$i]['Latitude'] = $value['lat_0'];
				$data["actor"][$i]['Longitude'] = $value['long_0'];
				$data["actor"][$i]['Tipe'] = $value['type_0'];
				$data["actor"][$i]['LatitudeParent'] = $value['lat_1'];
				$data["actor"][$i]['LongitudeParent'] = $value['long_1'];
				$data["transaction"][$j]= array("ID_Trans" => $kode.$j, "From" => $data["actor"][$i], "To" => $data["actor"][$i-1]);
				$j++;
				
			}
		}

		$this->response($data, 200);
	}

	public function get_relation_farmer_not_sales_get()
	{
		//echo "<pre>".print_r($this->get(), 1);die;
		$actor = $this->_model->getMarkerRelationFarmerNotSales($this->get());
		$transaction = array();
		$data["transaction"] = $transaction;
		$data["actor"] = $actor;
		$this->response($data, 200);
	}

    //============================================================================================================//

    public function district_get()
    {
        $data = $this->_model->getDistrict($this->get('ProvinceID'));
        $this->response($data, 200);
    }

    public function certificate_holders_get()
    {
        $data = $this->_model->getCertificateHolders($this->get());
        $this->response($data, 200);
    }

	public function farmer_organization_get()
	{
		$data = $this->_model->getFarmerOrg($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('date'), $this->get('show_all'));

		//echo '<pre>'; print_r($data); exit;
		if (!empty($this->get('export'))) {
			$this->load->library('excel');
			$excel_data = [[
				'title'  => 'FarmerOrg',
				'header' => ['Farmer Organizations'],
				'cols'   => [
					['name'=> 'ID', 'data' => 'ID', 'size' => 12],
					['name'=> 'Name', 'data' => 'Name', 'size' => 25],
					['name'=> 'Staff Name', 'data' => 'StaffName', 'size' => 15],
					['name'=> 'Village', 'data' => 'Village', 'size' => 15],
					['name'=> 'Sub District', 'data' => 'SubDistrict', 'size' => 15],
				],
				'data'	=> $data
			]];

			$path = './files/export/';
			$this->load->helper('file');
			delete_files($path);

			$filename = 'Farmer_Organization_'.date('Ymdhis').'.xlsx';
			$this->excel->filename($filename);
			$this->excel->create($excel_data, $path);
			$data = [
				'success' => true,
				'path' => ltrim($path,'.').$filename,
			];
		}
		$this->response($data, 200);
	}

	

	public function sce_get()
	{
		$data = $this->_model->getSCE($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('CertHolderID'), $this->get('key'), $this->get('date'), $this->get('show_all'));

		//echo '<pre>'; print_r($data); exit;
		if (!empty($this->get('export'))) {
			$this->load->library('excel');
			$excel_data = [[
				'title'  => 'SCE',
				'header' => ['SCE Data'],
				'cols'   => [
					['name'=> 'ID', 'data' => 'ID', 'size' => 12],
					['name'=> 'FarmerID', 'data' => 'FarmerID', 'size' => 12],
					['name'=> 'Name', 'data' => 'Name', 'size' => 20],
					['name'=> 'Village', 'data' => 'Village', 'size' => 15],
					['name'=> 'Sub District', 'data' => 'SubDistrict', 'size' => 15],
					['name'=> 'District', 'data' => 'District', 'size' => 15],
					['name'=> 'Province', 'data' => 'Province', 'size' => 15],
				],
				'data'	=> $data
			]];

			$path = './files/export/';
			$this->load->helper('file');
			delete_files($path);

			$filename = 'SCE_'.date('Ymdhis').'.xlsx';
			$this->excel->filename($filename);
			$this->excel->create($excel_data, $path);
			$data = [
				'success' => true,
				'path' => ltrim($path,'.').$filename,
			];
		}
		$this->response($data, 200);
	}

	public function warehouse_get()
	{
		$data = $this->_model->getWarehouse($this->get('ProvinceID'), $this->get('DistrictID'), $this->get('key'), $this->get('date'), $this->get('show_all'));

		//echo '<pre>'; print_r($data); exit;
		if (!empty($this->get('export'))) {
			$this->load->library('excel');
			$excel_data = [[
				'title'  => 'Warehouse',
				'header' => ['Warehouse'],
				'cols'   => [
					['name'=> 'ID', 'data' => 'ID', 'size' => 12],
					['name'=> 'Name', 'data' => 'Name', 'size' => 25],
					['name'=> 'Staff Name', 'data' => 'StaffName', 'size' => 15],
					['name'=> 'Village', 'data' => 'Village', 'size' => 15],
					['name'=> 'Sub District', 'data' => 'SubDistrict', 'size' => 15],
				],
				'data'	=> $data
			]];

			$path = './files/export/';
			$this->load->helper('file');
			delete_files($path);

			$filename = 'Warehouse_'.date('Ymdhis').'.xlsx';
			$this->excel->filename($filename);
			$this->excel->create($excel_data, $path);
			$data = [
				'success' => true,
				'path' => ltrim($path,'.').$filename,
			];
		}
		$this->response($data, 200);
	}

	public function info_by_id_get() {
		// $org = $this->_model->getDetailActor($this->get());
		$org = $this->_model->getDetailActorPalm($this->get());
		// var_dump($org);exit;
		$id 	= $this->get('id');
		$type 	= $this->get('type');
		$lat 	= $this->get('lat');
		$long	= $this->get('long');

		$transaction_count = $org['transaction'];
		//$data
		
		$data['transaction'] = array(
			'id' => $id,
			'type' => $type,
			'uniqueChild' => "3"
		);
		// $data['transaction']['data'] = $this->_model->getDetailTransaction($this->get());
		$data['transaction']['data'] = $this->_model->getDetailTransactionPalm($this->get());
		// var_dump($data['transaction']);exit;
		$bruto = 0;
		$netto = 0;
		$transaction = 0;
		foreach ($data['transaction']['data'] as $key => $value) {
			if($value['bruto']!=''){
				// var_dump($value);exit;
				$transaction++;
				$bruto = $bruto + floatval($value['bruto']);
				$netto = $netto + floatval($value['netto']);
			}
		}
		if($bruto > 0){
			$bruto = round(floatval($bruto)/1000, 3).' Ton';
		}else{
			$bruto = '-';
		}
		if($netto > 0){
			$netto = round(floatval($netto)/1000, 3).' Ton';
		}else{
			$netto = '-';
		}

		$org['transaction'] = $transaction_count;
		// var_dump($org['transaction']);exit;
		$org['Gross'] = $bruto;
		$org['Net Weight'] = $netto;
		$data['detail'] = $org;
		// var_dump($data['detail']);exit;
        $this->response($data, 200);
    }

    public function trans_data_by_id_get() {
    	// $data = $this->_model->getTransactionDetails($this->get());
		// $this->response($data, 200);


        $data = json_decode('{"id":"7","type":"trader","uniqueChild":"3","data":[{"date":"2021-06-16 10:00:00","transNumber":"00002-17","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00003-17","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00001-27","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00002-27","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00003-27","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00004-27","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00001-37","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00002-37","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00002-37","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00001-79","bruto":"120.00","netto":"100.00"},{"date":"2021-06-16 10:00:00","transNumber":"00001-108","bruto":"120.00","netto":"100.00"}]}');
        $this->response($data, 200);
    }

    public function transaction_location_get()
	{
		$data = $this->_model->getTransactionLocation($this->get());
		$this->response($data, 200);
	}

	public function buying_stations_get()
    {
        $data = $this->_model->getBuyingStations($this->get());
        $this->response($data, 200);
    }

}
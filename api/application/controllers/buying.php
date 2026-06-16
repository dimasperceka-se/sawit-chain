<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Buying extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('buying/mbuying');
        $this->load->model('traceability/mtransaction');
    }

    function datas_get() {
        $data = $this->mbuying->readDatas($_SESSION['userid'],$this->get('key'),$this->get('prov'),$this->get('kab'),
            $this->get('start'),$this->get('limit'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function data_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mbuying->readData($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function data_post() {
        if(!$this->post('FarmerID')) $this->response(NULL, 400);
        $data = $this->mbuying->createData($this->post('FarmerID'),$this->post('DateTransaction'),$this->post('VolumeBruto'),
            $this->post('VolumeNetto'),$this->post('BagType'),$this->post('BagQty'),$this->post('BeanType'),
            $this->post('QualityKA'),$this->post('QualityBC'),$this->post('QualityWaste'),$this->post('QualityMouldy'),
            $this->post('QualityInsect'),$this->post('QualitySlaty'),$this->post('Reward'),$this->post('ContractPrice'),
            $this->post('NetPrice'),$this->post('TotalPayment'),$this->post('DpPercent'),$this->post('StatusCode'),
            $this->post('WeightBy'),$this->post('SupervisorID'),$this->post('AdminID'),$this->post('VehicleNo'),
            $this->post('WarehouseTraderID'),$_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function data_put() {
        if(!$this->put('TransactionID')) $this->response(NULL, 400);
        $data = $this->mbuying->updateData($this->put('FarmerID'),$this->put('DateTransaction'),$this->put('VolumeBruto'),
            $this->put('VolumeNetto'),$this->put('BagType'),$this->put('BagQty'),   $this->put('BeanType'),
            $this->put('QualityKA'),$this->put('QualityBC'),$this->put('QualityWaste'),$this->put('QualityMouldy'),
            $this->put('QualityInsect'),$this->put('QualitySlaty'),   $this->put('Reward'),$this->put('ContractPrice'),
            $this->put('NetPrice'),$this->put('TotalPayment'),$this->put('DpPercent'),$this->put('StatusCode'),
            $this->put('WeightBy'),$this->put('SupervisorID'),$this->put('AdminID'),$this->put('VehicleNo'),
            $this->put('WarehouseTraderID'),$_SESSION['userid'],
         	$this->put('TRMoisture'),$this->put('TRBeanCount'),$this->put('TRMouldy'),$this->put('TRInsect'),
            $this->put('TRSalty'),$this->put('TRWaste'), 
         	$this->put('ClaimMoisture'),$this->put('ClaimBeanCount'),$this->put('ClaimMouldy'),$this->put('ClaimInsect'),
            $this->put('ClaimSalty'),$this->put('ClaimWaste'), 
         	$this->put('RewardMoisture'),$this->put('RewardBeanCount'),$this->put('RewardMouldy'),$this->put('RewardInsect'),
            $this->put('RewardSalty'),$this->put('RewardWaste'),             
            $this->put('TransactionID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function data_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mbuying->deleteData($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
    function data_farmer_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mbuying->readDataFarmer($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
   
    function datas_detail_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $data = $this->mbuying->readDatasDetail($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function data_detail_post() {
        if(!$this->post('PackageType')) $this->response(NULL, 400);
        $packageId = explode('-',$this->post('PackageType'));
        $data = $this->mbuying->createDataDetail($this->post('TransactionID'),$packageId[0],$this->post('Weight'),
            $this->post('Moisture'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function data_detail_put() {
        if(!$this->put('DetailID')) $this->response(NULL, 400);
        $packageId = explode('-',$this->put('PackageType'));
        if ($packageId[1]!='') $PackageID = $packageId[0]; else $PackageID = $this->put('PackageID');
        $data = $this->mbuying->updateDataDetail($this->put('TransactionID'),$PackageID,$this->put('Weight'),
            $this->put('Moisture'),$this->put('DetailID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function data_detail_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mbuying->deleteDataDetail($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
    function data_ff_get() {
        $data = $this->mbuying->readDataFf($_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function data_package_get() {
        $data = $this->mbuying->readDataPackage($_SESSION['userid']);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function cetak_kuitansi_get_($id) {
         $data['data'] = $this->mbuying->readData($id);
         $data['detail'] = $this->mbuying->readDatasDetail($id);
         $this->load->view('kuitansi_cetak', $data);
    }

    function cetak_kuitansi_get($id) {
         //$data['koperasi'] = $this->mbuying->readDataKoperasi($id);
         $data['data'] = $this->mbuying->readData($id);
         $data['detail'] = $this->mbuying->readDatasDetail($id);
		 $data['quality'] = $this->mbuying->readDatasQuality($id);
         //print_r($data['data']);exit;
         if ($data['data']['PartnerID']=='8') $view = 'kuitansi_farmer_cetak_cargill';
         elseif ($data['data']['PartnerID']=='7') $view = 'kuitansi_farmer_cetak_adm';
         elseif ($data['data']['PartnerID']=='19') $view = 'kuitansi_farmer_cetak_bt';
         elseif ($data['data']['PartnerID']=='21') $view = 'kuitansi_farmer_cetak_cargill';
         elseif ($data['data']['PartnerID']=='22') $view = 'kuitansi_farmer_cetak_jbbk';
         else $view = 'kuitansi_farmer_cetak_cargill';
         //$view = 'kuitansi_farmer_cetak_cargill';
         $this->load->view($view, $data);
    }
	
	function report_cetak_kuitansi_get($SupplyBatchID){
		$data = $this->mbuying->getTransaksi($SupplyBatchID);
		if($data->num_rows() > 0){
			foreach($data->result() as $row){
				$this->cetak_kuitansi_get($row->SupplyTransID);
			}
		}else{
			echo "No transaction found!";
		}
	}

    function cetak_kuitansi_batch_get($id,$orgid,$batchid,$transid='') {
         $data['data'] = $this->mtransaction->readDataBatch($id, $orgid, '%%');
         $data['detail'] = $this->mtransaction->readDatasDetail($data['data']['SupplyBatchID']);
         $data['deta'] = $this->mbuying->readDatasDetail($transid);
         //print_r($data['deta']);exit;
//         print_r($data['detail']);exit;
         if ($data['data']['PartnerID']=='7') $view = 'kuitansi_cetak_batch_adm';
         elseif ($data['data']['PartnerID']=='8') $view = 'kuitansi_cetak_batch_cargill';
         elseif ($data['data']['PartnerID']=='19') $view = 'kuitansi_cetak_batch_bt';
         //$view = 'kuitansi_cetak_batch_cargill';
         $this->load->view($view, $data);
    }
    
    /**
     * @author <ardi@koltiva.com>
     * @param type $batch <batch number>
     */
    function getPOFromBatch($batch) {
        
        $this->db->select('SupplyBatchID');
        $this->db->from('ktv_supplychain_batch');
        $this->db->where('SupplyBatchNumber',$batch);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            
            $batchid = $row->SupplyBatchID;
            
            $this->db->select('SupplyBatchID,DestPO,VolumeNetto');
            $this->db->from('ktv_supplychain_batch');
            $this->db->where('SupplyBatchID',$batchid);
            $Q2 = $this->db->get();
            if($Q2->num_rows() > 0){
                return $Q2->row_array();
            }
        }
        
        return false;
    }
    
    function cetak_packing_list_get($id) {
        
        $po = array();
        foreach($this->mbuying->readDataTrans($id) as $keys => $trans){
            if($trans['SupplyType'] == 'Batch'){
                $data_packing = $this->getPOFromBatch($trans['SupplyID']);
                array_push($po, $data_packing);
            }
        }
       
        $data['po'] = $po;
        
        $data['batch'] = $this->mbuying->readDataBatch($id);
        $data['trans'] = $this->mbuying->readDataTrans($id);
        $data['detail'] = $this->mbuying->readDataDetail($id);
        $data['transaksi'] = $this->mbuying->readDataTransaksi($id);
        
        if ($data['batch']['PartnerID']=='7') $view = 'kuitansi_packing_list_cetak_adm';
        elseif ($data['batch']['PartnerID']=='8') $view = 'kuitansi_packing_list_cetak_cargill';
        elseif ($data['batch']['PartnerID']=='21') $view = 'kuitansi_packing_list_cetak_bt';
        elseif ($data['batch']['PartnerID']=='22') $view = 'kuitansi_packing_list_cetak_jbkl';
        else $view = 'kuitansi_packing_list_cetak_adm';
        
        //echo "<pre>".print_r($data['detail'],1);exit;
        //$view = "kuitansi_packing_list_cetak_adm";
        $this->load->view($view, $data);
    }

}

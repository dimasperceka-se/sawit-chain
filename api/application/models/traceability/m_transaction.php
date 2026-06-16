<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_transaction extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->helper('common_helper');
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper('file');
    }
    
    public function get_data_transaction($SIP, $PID, $STID){
        $data = array('data' => array(), 'total' => 0);

        $this->db->select('
                            a.`SupplyTransID`,
                            a.`SupplychainID`,
                            a.`SupplyBatchID`,
                            a.`TransNumber`,
                            a.`InvoiceNumber`,
                            a.`DateTransaction`,
                            a.`SupplyType`,
                            a.`SupplyID`,
                            a.`PlantationNr`,
                            a.`VolumeBruto`,
                            a.`VolumeNetto`,
                            a.`VolumeCutting`,
                            a.`PackageID`,
                            a.`PackageNumber`,
                            a.`PackageWeight`,
                            a.`DetailTypeID`,
                            a.`TransStatusID`,
                            a.ContractPrice,
                            a.NetPrice,
                            a.DiscountPrice,
                            a.TotalPayment,
                            a.`Notes`,
                            a.`ChangeLog`,
                            a.`ChangeBy`,
                            a.`DateCreated`,
                            a.`CreatedBy`,
                            a.`DateUpdated`,
                            a.`LastModifiedBy`
                        ');
        $this->db->from('ktv_tc_supplychain_transaction a');
        $this->db->join('ktv_trace_package c', 'a.PackageID=c.PackageID', 'left');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where('a.SupplychainID', $SIP);
        //$this->db->where('a.SupplyBatchID', 0);
        $this->db->where(array('a.SupplyBatchID' => NULL));

        if($STID){
            $this->db->where('SupplyTransID', $STID);
        }

        $this->db->order_by('a.TransNumber', 'DESC');

        $Q = $this->db->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
                $val->quality = array();

                /*$getQuality = $this->db->select(" 
                                            a.TransQualityID,
                                            b.QualityID,
                                            b.SupplychainID,
                                            b.Name,
                                            IFNULL(b.Formula,'') AS Formula,
                                            IFNULL(b.Order,'') AS Order,
                                            IFNULL(b.Type,'') AS Type,
                                            IFNULL(b.MinValue,'') AS MinValue,
                                            IFNULL(b.MaxValue,'') AS MaxValue,
                                            IFNULL(b.StandardValue,'') AS StandardValue,
                                            IFNULL(b.IsPrintVisible,'') AS IsPrintVisible,
                                            IFNULL(a.Value,'') AS InputValue,
                                            a.CreatedBy,
                                            a.DateCreated,
                                            a.DateUpdated,
                                            IFNULL(a.LastModifiedBy,'') AS LastModifiedBy
                                        ")
                ->from('ktv_tc_supplychain_transaction_quality a')
                ->join('ktv_tc_supplychain_quality b', 'a.QualityID=b.QualityID', 'left')
                ->where('a.StatusCode', 'active')
                ->where('a.SupplyTransID', $val->SupplyTransID)
                ->get();*/
                $sql = "SELECT 
                            a.TransQualityID,
                            b.QualityID,
                            b.SupplychainID,
                            b.Name,
                            IFNULL(b.Formula,'') AS Formula,
                            IFNULL(b.Order,'') AS `Order`,
                            IFNULL(b.Type,'') AS `Type`,
                            IFNULL(b.MinValue,'') AS `MinValue`,
                            IFNULL(b.MaxValue,'') AS `MaxValue`,
                            IFNULL(b.StandardValue,'') AS StandardValue,
                            IFNULL(b.IsPrintVisible,'') AS IsPrintVisible,
                            IFNULL(a.Value,'') AS InputValue,
                            a.CreatedBy,
                            a.DateCreated,
                            a.DateUpdated,
                            IFNULL(a.LastModifiedBy,'') AS LastModifiedBy
                        FROM 
                            ktv_tc_supplychain_transaction_quality a
                            LEFT JOIN ktv_tc_supplychain_quality b ON a.QualityID=b.QualityID
                        WHERE a.StatusCode='active' AND a.SupplyTransID=?";
                $getQuality = $this->db->query($sql, array($val->SupplyTransID));
                if($getQuality->num_rows()){
                    $d_quality = $getQuality->result_array();
                    foreach($d_quality as $kq => $vq){
                        //$vq = $this->check_isNull($vq);
                        $query_value = $this->db->select('ValueQualityID, Value')
                        ->from('ktv_tc_supplychain_quality_value')
                        ->where('QualityID', $vq['QualityID'])
                        ->where('StatusCode', 'active')
                        ->get();
                        $d_quality[$kq]['Value'] = array();
                        if($query_value->num_rows()){
                            $d_quality[$kq]['Value'] = $query_value->result();
                        }
                    }
                    $val->quality = $d_quality;
                }
                
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }

        return $data;
    }
    private function check_isNull($v){
        foreach($v as $key => $value){
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }

    public function submit($data){
        $result = false;
        $insid = 0;
        $error = '';

        try{
            $this->db->trans_begin();
            $content = array(
                "SupplychainID" => $data["SupplychainID"],
                "DateTransaction" => $data["DateTransaction"].' '.date('H:i:s'),
                "SupplyType" => $data["SupplyType"], //Farmer, Batch
                "SupplyID" => $data["SupplyID"],
                "PlantationNr" => $data["PlantationNr"],
                "VolumeBruto" => $data["VolumeBruto"],
                "VolumeNetto" => $data["VolumeNetto"],
                "VolumeCutting" => $data["VolumeCutting"],
                //"FarmingTypeID" => $data["FarmingTypeID"],
                "PackageID" => $data["PackageID"],
                "PackageNumber" => $data["PackageNumber"],
                "PackageWeight" => $data["PackageWeight"],
                //"DetailTypeID" => $data["DetailTypeID"],
                "TransStatusID" => $data["TransStatusID"],
                "ContractPrice" => $data['ContractPrice'],
                "NetPrice" => $data['NetPrice'],
                "DiscountPrice" => $data['DiscountPrice'],
                "TotalPayment" => $data['TotalPayment'],
                "Notes" => $data["Notes"],
                "StatusCode" => 'active'
            );

            $cekSIP = $this->db->select('*')
                ->from('ktv_tc_supplychain_transaction')
                ->where('StatusCode', 'active')
                ->where('SupplyTransID', $data['SupplyTransID'])
                ->get(); 

            if($cekSIP->num_rows()){
                $dataSIP = $cekSIP->row();

                /* SupplyBatchID not null */
                if($dataSIP->SupplyBatchID){
                    return array('success' => false, 'message' => 'Save data failed', 'error' => 'SupplyBatchID Not NULL');
                }
                 
                /* DateUpdated < DateUpdated WEB */
                if(strtotime($data['DateUpdated']) < strtotime($dataSIP->DateUpdated)){
                    return array('success' => false, 'message' => 'Save data failed', 'error' => 'DateUpdated < DateUpdated WEB');
                }
                /* Update data Transaction */
                $this->db->where('SupplyTransID', $data['SupplyTransID']);
                $content['DateUpdated'] = $data['DateUpdated'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
                $this->db->update('ktv_tc_supplychain_transaction', $content);
                $insid = $data['SupplyTransID'];
            }else{
                //generateTransTraceabilityNumber($content['SupplychainID']);
                //getInvoiceNumber($content['SupplychainID']);
                $content['TransNumber'] = generateTransTraceabilityNumber($data["SupplychainID"]); 
                $content['InvoiceNumber'] = $data['InvoiceNumber']; 
                $content['DateCreated'] = $data['DateCreated'];
                $content['DateUpdated'] = $data['DateUpdated'];
                $content['CreatedBy'] = $data['CreatedBy'];
                $this->db->insert('ktv_tc_supplychain_transaction', $content);
                $insid = $this->db->insert_id();
            }
			// backup dulu ke file, siapa tau ada masalah di datanya
			$name = 'TRANS'.'-ID-'.$insid . '-STYPE-'.$data["SupplyType"].'-SupplyID'. $data['SupplyID'] . '-' . $data['DateTransaction'] . '-' . strtotime(date('Y-m-d H:i:s'));
			$dir = FCPATH . 'backup_traceability_sync';

			if(!is_dir($dir)) {
				mkdir($dir, 0777, true);
			}

			if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}

            /* Quality */
            if(count($data['quality'])){
                $this->db->where('SupplyTransID', $insid);
                $this->db->delete('ktv_tc_supplychain_transaction_quality');
                foreach($data['quality'] as $quality){
                    $content_quality = array(
                        'SupplyTransID' => $insid,
                        'QualityID' => $quality['QualityID'],
                        'Value' => $quality['Value'],
                        'StatusCode' => 'active'
                    );
                    $content_quality['DateCreated'] = $data['DateCreated'];
                    $content_quality['DateUpdated'] = $data['DateUpdated'];
                    $content_quality['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                    $this->db->insert('ktv_tc_supplychain_transaction_quality', $content_quality);
                }
            }

            /*SupplyType = 'Batch' */
            if($data["SupplyType"]=='Batch'){
                $this->db->where('SupplyBatchNumber', $data["SupplyID"]);
                $update_status = array('SupplyBatchStatus' => 'Delivered');
                $this->db->update('ktv_tc_supplychain_batch', $update_status);
            }

            if (($this->db->trans_status() == false)) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
                $result = false;
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result, 'SupplyTransID' => $insid, 'SupplyTransIDApp' => $data['SupplyTransIDApp']);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
}

?>

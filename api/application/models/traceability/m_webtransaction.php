<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf Sutana 
 */
class m_webtransaction extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->helper('common_helper');
        date_default_timezone_set('UTC');
    }
    
    public function get_data_transaction($SID){ 
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        /* pencarian */
        $SupplyType = $this->input->get('SupplyType');
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');

        if($SupplyType){
            $this->db->where('a.`SupplyType`', $SupplyType);
        }

        if($SupplyStatus){
            //$this->db->where('a.StatusCode', 'active');
        }

        if($SupplyKey){
            $this->db->like('b.`MemberName`', $SupplyKey);
            $this->db->or_like('e.Name', $SupplyKey);
            $this->db->or_like('b.`MemberDisplayID`', $SupplyKey);
            $this->db->or_like('e.ObjID', $SupplyKey);
        }



        /* Filter core */
        $PID = $this->input->get('PID');
        $STID = $this->input->get('STID');
        $SBID = $this->input->get('SBID');

        $data = array('data' => array(), 'total' => 0);

        $this->db->select(" a.`SupplyTransID`,
                            a.`SupplychainID`,
                            a.`SupplyBatchID`,
                            a.`TransNumber`,
                            a.`InvoiceNumber`,
                            a.`DateTransaction`,
                            a.`SupplyType`,
                            a.`PlantationNr`,
                            a.`VolumeBruto`,
                            a.`VolumeNetto`,
                            a.`VolumeCutting`,
                            a.`PackageID`,
                            a.`PackageNumber`,
                            a.`PackageWeight`,
                            a.`DetailTypeID`,
                            a.`TransStatusID`,
                            a.`FarmingTypeID`,
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
                            a.`LastModifiedBy`,
                            c.PackageType,
                            b.`MemberName`,
                            e.Name,
                            b.`MemberDisplayID`,
                            b.MemberID,
                            e.ObjID,
							IF(a.SupplyBatchID IS NULL, 'Open', 'Sent') SupplyStatus
                        ", false); 
        $this->db->from('ktv_tc_supplychain_transaction a');
        $this->db->join('ktv_members b', 'a.SupplyID=b.MemberID AND a.SupplyType != "Batch"', 'left');
        $this->db->join('ktv_tc_supplychain_batch d', 'a.SupplyID=d.SupplyBatchID AND a.SupplyType = "Batch"', 'left');
        $this->db->join('view_tc_supplychain_org e', 'd.SupplyOrgID=e.SupplychainID', 'left');
        $this->db->join('ktv_trace_package c', 'a.PackageID=c.PackageID', 'left');
		$this->db->where('a.SupplyType != ', 'Batch');
        $this->db->where('a.StatusCode', 'active');
        $this->db->where('a.SupplychainID', $SID);

        if($STID){
            $this->db->where('a.SupplyTransID', $STID);
        }

        $this->db->order_by('a.SupplyTransID', 'DESC');
        $this->db->limit($limit, $start);

        $Q = $this->db->get();


        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $val){
                $val = $this->check_isNull($val);
                $val->FarmingTypeName = $this->getFarming($val->FarmingTypeID);
                $val->SupplierName = $val->MemberName == '' ? $val->Name : $val->MemberName;
                $val->SupplyID = $val->MemberDisplayID == '' ? $val->ObjID : $val->MemberID;
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
    public function submit($params){
        $result = false;
        $insid = 0;
        $error = ''; 
		
		$data=array();
		foreach ($params as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Transaction_FormTransaction-", '', $key);
            if($value == "") $value = null;
			$data[$keyNew] = $value;
		}

        try{
            $this->db->trans_begin();
             
            $PlantationNr = explode('-', $data["PlantationNr"]);
            $PlantationNr = $PlantationNr[0];
            $content = array(
                "SupplychainID"=> $this->input->request_headers()['Sid'],
                "DateTransaction"=> $data["DateTransaction"],
                "SupplyType"=> 'Farmer', //Farmer, Batch
                "SupplyID"=> $data["FarmerID"],
                "PlantationNr"=> $PlantationNr,
                "VolumeBruto"=> $this->replacestr($data["VolumeBruto"]),
                "VolumeNetto"=> $this->replacestr($data["VolumeNetto"]),
                "VolumeCutting"=> $this->replacestr($data["VolumeCutting"]),
                "PackageID"=> $data["PackageID"], 
                "FarmingTypeID"=> $data["FarmingTypeID"],
                "PackageNumber"=> $this->replacestr($data["PackageNumber"]),
				"PackageWeight"=> @$data['PackageWeight'],
                "NetPrice"=> $this->replacestr($data['NetPrice']), 
                "TotalPayment"=> $this->replacestr($data['TotalPayment']),
				"StatusCode" => 'active' 
            );
            //print_r($content);die;
            if($data['STID'] !='' ){
                //$dataSIP = $cekSIP->row();
                /* SupplyBatchID not null */
               // if($dataSIP->SupplyBatchID){
                   // return array('success' => false, 'message' => 'Save data failed', 'error' => 'SupplyBatchID Not NULL');
               // }
                
                /* Update data Transaction */
                $this->db->where('SupplyTransID', @$data['STID']);
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->update('ktv_tc_supplychain_transaction', $content);
                $insid = $this->input->request_headers()['Sid'];

            }else{
                $content['TransNumber'] = generateTransTraceabilityNumber($content['SupplychainID']); 
                $content['InvoiceNumber'] = ""; 
                $content['DateCreated'] = date('Y-m-d H:i:s');
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                $this->db->insert('ktv_tc_supplychain_transaction', $content);
				//echo $this->db->last_query();die;
                $insid = $this->db->insert_id();
            }

            /* Quality */
            if($data['quality']){
                $this->db->where('SupplyTransID', $insid);
                $this->db->delete('ktv_tc_supplychain_transaction_quality');

                $quality = $data['quality'];
				 
				$dt = json_decode($quality);
				
                foreach($dt as $k => $quality){
                   
					$QS = $this->db->select('ValueQualityID')
							  ->from('ktv_tc_supplychain_quality_value')
							  ->where('QualityID', $quality->QualityID)
							  ->where('Value', $quality->Value)
							  ->where('StatusCode', 'active')
							  ->get()->row();
				   
					$content_quality = array(
                        'SupplyTransID' => $insid,
                        'QualityID' => $quality->QualityID,
                        'Value' => @$QS->ValueQualityID,
                        'StatusCode' => 'active'
                    );
					
                    $content_quality['DateCreated'] = date('Y-m-d H:i:s');
                    $content_quality['DateUpdated'] = date('Y-m-d H:i:s');
                    $content_quality['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                    $this->db->insert('ktv_tc_supplychain_transaction_quality', $content_quality);
                }
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
            return array('success' => $result, 'SupplyTransID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
    
	function replacestr($form)
	{
		return str_replace(',','',$form);
	}
    public function get_data_farmer($PartnerID, $SupplychainID){ 
        $start =  $this->input->get('start');
        $limit =  $this->input->get('limit');
		$query = $this->input->get('query');

		
		$return = array('data' => array(), 'total' => 0);

        $this->db->select(' b.MemberID, b.MemberDisplayID, b.MemberName, b.Nin, b.DateOfBirth, b.Gender, b.Address, b.Handphone, b.Photo, b.GapoktanID, g.GapoktanName, b.FarmerGroupID, h.GroupName, e.ProvinceID, f.Province, e.DistrictID, e.District, d.SubDistrictID, d.SubDistrict, b.VillageID, c.Village ');
        $this->db->from('ktv_access_partner_member a');
        $this->db->join('ktv_members b', 'a.apmMemberID=b.MemberID');
        $this->db->join('ktv_village c', 'b.VillageID=c.VillageID', 'left');
        $this->db->join('ktv_subdistrict d', 'c.SubDistrictID=d.SubDistrictID', 'left');
        $this->db->join('ktv_district e', 'e.DistrictID=d.DistrictID', 'left');
        $this->db->join('ktv_province f', 'f.ProvinceID=e.ProvinceID', 'left');
        $this->db->join('ktv_gapoktan g', 'b.GapoktanID=g.GapoktanID', 'left');
        $this->db->join('ktv_farmer_group h', 'b.FarmerGroupID=h.FarmerGroupID', 'left');
        $this->db->join('ktv_survey_plot i', 'b.MemberID=i.MemberID');
        $this->db->where('a.apmPartnerID', $PartnerID);
        $this->db->where('b.StatusCode', 'active');
        $this->db->group_by('b.MemberID');		
		if($query){
			$this->db->like('MemberName', $query); 
		}
		
	    $this->db->limit($limit, $start); 
		$this->db->order_by('b.MemberID', 'DESC');  
        $Q = $this->db->get();
		
	    /**Paging total data*/
        $this->db->from('ktv_members');
		$this->db->where('StatusCode', 'active'); 
		$jmldata = $this->db->get()->num_rows(); 
		/*end*/
		
	    //echo $this->db->last_query();die; 
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
                $val->MemberNames = $val->MemberName.'|'.$val->MemberDisplayID;
            }
			$data['data'] = $result; 
            $data['total'] = $jmldata; 
			
            return $data;
        }
        return $data;
    }
    public function get_data_plantation($MemberID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select(' a.PlotNr as PlantationNr, 
                                               a.SurveyNr, 
                                               a.VillageID, 
                                               a.Latitude, 
                                               a.Longitude, 
                                               c.Village ')
                ->from('ktv_survey_plot a')
                //->join('ref_tc_farming_type b', 'a.FarmingType=b.FarmingTypeID', 'left')
                ->join('ktv_village c', 'a.VillageID=c.VillageID', 'left')
                ->where('a.StatusCode', 'active')
                ->where('a.MemberID', $MemberID)
                ->get();


        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
                //$val->PlantationName = $val->PlantationNr.'|'.$val->FarmingTypeName;
                $val->PlantationName = $val->PlantationNr;
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        //echo $this->db->last_query(); die;

        return $return;
    }
    public function get_data_package_type($SupplychainID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('PackageID, PackageType, PackageWeight, PackageCapacity')
          ->from('ktv_tc_supplychain_package')
          ->where('SupplychainID', $SupplychainID)
          ->where('StatusCode', 'active')
          ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        return $data;
    }
    public function get_data_quality($SupplychainID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('`QualityID`, `SupplychainID`, `Name`, `Formula`, `Order`, `Type`, `MinValue`, `MaxValue`, `StandardValue`, `IsPrintVisible`')
          ->from('ktv_tc_supplychain_quality')
          ->where('SupplychainID', $SupplychainID)
          ->where('StatusCode', 'active')
          ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        return $data;
    }
    public function get_data_quality_value($QualityID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('`ValueQualityID`, `Value`') 
          ->from('ktv_tc_supplychain_quality_value')
          ->where('QualityID', $QualityID)
          ->where('StatusCode', 'active')
          ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }
            $return['data'] = $result;
            $return['total'] = $Q->num_rows();
            return $return;
        }
        return $return;
    }

    private function getFarming($id){
        $Q = $this->db->get_where('ref_tc_farming_type', array('FarmingTypeID' => $id));

        if($Q->num_rows()){
            return $Q->row()->FarmingTypeName;
        }else{
            return '';
        }
    }
	
	function  getTransaksi($SupplyTransID,$SupplychainID)
	{
		$SQL ='select B.Name,  C.MemberName as Namapetani,  D.GroupName, A.*  from 
				ktv_tc_supplychain_transaction A 
				JOIN view_tc_supplychain_org B ON A.SupplychainID = B.SupplychainID
				JOIN ktv_members C ON A.SupplyID = C.MemberID
				LEFT JOIN ktv_farmer_group D ON D.FarmerGroupID = C.FarmerGroupID
				where A.SupplyTransID = ? and A.SupplychainID = ? ';
		$t = $this->db->query($SQL, array($SupplyTransID, $SupplychainID));   
		//echo $this->db->last_query();die;
		return $t->row_array();
	}
	
	function getTransaksiQuality($SupplyTransID)
	{
		$SQL ='select B.Name, A.Value from ktv_tc_supplychain_transaction_quality A, ktv_tc_supplychain_quality B
			   where A.QualityID = B.QualityID and A.SupplyTransID = ?';
		$t = $this->db->query($SQL, array($SupplyTransID));
		//echo $this->db->last_query();die;
		return $t->result();
	}
}

?>

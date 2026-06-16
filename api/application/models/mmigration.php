<?php

/**
 * Authentication Model for API
 *
 * @author koltiva
 */
class Mmigration extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_transaction($batchid,$batchstartdate,$batchenddate) {

        $this->db->select('SupplyBatchNumber,SupplyOrgID,SupplyTransID,FFQualityKA,FFQualityBC,FFQualityWaste,FFQualityMouldy,FFQualityInsect,FFQualitySlaty,FAQQualityKA,FAQQualityBC,FAQQualityWaste,FAQQualityMouldy,FAQQualityInsect,FAQQualitySlaty');
        $this->db->from('ktv_supplychain_transaction');
        $this->db->join('ktv_supplychain_batch','ktv_supplychain_batch.SupplyBatchID = ktv_supplychain_transaction.SupplyBatchID','left');
        if($batchid){
            $this->db->where('ktv_supplychain_transaction.SupplyBatchID',$batchid);
        } else {
            //$this->db->where('`ktv_supplychain_transaction`.`FFQualityKA` > 0 OR `ktv_supplychain_transaction`.`FFQualityBC` > 0 OR `ktv_supplychain_transaction`.`FFQualityWaste` > 0 OR `ktv_supplychain_transaction`.`FFQualityMouldy` > 0 OR `ktv_supplychain_transaction`.`FFQualityInsect` > 0 OR `ktv_supplychain_transaction`.`FFQualitySlaty` > 0 OR `ktv_supplychain_transaction`.`FAQQualityKA` > 0 OR `ktv_supplychain_transaction`.`FAQQualityBC` > 0 OR `ktv_supplychain_transaction`.`FAQQualityWaste` > 0 OR `ktv_supplychain_transaction`.`FAQQualityMouldy` > 0 OR `ktv_supplychain_transaction`.`FAQQualityInsect` > 0 OR `ktv_supplychain_transaction`.`FAQQualitySlaty` > 0 ',NULL,FALSE);
            //$this->db->where('`ktv_supplychain_transaction`.`FFQualityKA` > 0',NULL,FALSE);
            //$this->db->where('(`ktv_supplychain_transaction`.`FFQualityKA` IS NOT NULL AND `ktv_supplychain_transaction`.`FFQualityBC` IS NOT NULL AND `ktv_supplychain_transaction`.`FFQualityWaste` IS NOT NULL AND `ktv_supplychain_transaction`.`FFQualityMouldy` IS NOT NULL AND `ktv_supplychain_transaction`.`FFQualityInsect` IS NOT NULL AND `ktv_supplychain_transaction`.`FFQualitySlaty` IS NOT NULL AND `ktv_supplychain_transaction`.`FAQQualityKA` IS NOT NULL AND `ktv_supplychain_transaction`.`FAQQualityBC` IS NOT NULL AND `ktv_supplychain_transaction`.`FAQQualityWaste` IS NOT NULL AND `ktv_supplychain_transaction`.`FAQQualityMouldy` IS NOT NULL AND `ktv_supplychain_transaction`.`FAQQualityInsect` IS NOT NULL AND `ktv_supplychain_transaction`.`FAQQualitySlaty` IS NOT NULL)',NULL,FALSE);
            if($batchstartdate){
                $this->db->where('(SupplyBatchDate => "'.date('Y-m-d',$batchstartdate).'"','',FALSE);
            }

            if($batchenddate){
                $this->db->where('(SupplyBatchDate =< "'.date('Y-m-d',$batchenddate).'"','',FALSE);
            }
        }
        $this->db->where('ktv_supplychain_transaction.SupplyTransID NOT IN(SELECT SupplyTransID FROM ktv_supplychain_transaction_quality)',null,false);
        //$this->db->limit(1000);
        //var_dump($this->db->_compile_select());die;
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            return $Q->result_array();
        }

        return false;
    }

    function migrate_transaction($data) {
        error_reporting(E_ALL);
        $stka = array('KA','Kadar Air','Moisture','Moisture (%)');
        $stbc = array('BC','BeanCount','Bean Count','Bean Count (%)','Bean Count (/100gr)');
        $stmo = array('Mouldy','Mouldy (%)');
        $stin = array('Insect','Insect (%)');
        $stsl = array('Slaty','Slaty (%)');
        $stwa = array('Waste','Waste (%)');

        $standardid = $this->getStandardID($data['SupplyOrgID']);
        echo json_encode($data);
        echo '<hr>';
        if($standardid){
            foreach($standardid['Detail'] as $stv => $stval){
                $name = '';
                if(in_array($stval['Name'],$stka)){
                    $name = 'KA';
                } elseif(in_array($stval['Name'],$stbc)){
                    $name = 'BC';
                } elseif(in_array($stval['Name'],$stmo)){
                    $name = 'Mouldy';
                } elseif(in_array($stval['Name'],$stin)){
                    $name = 'Insect';
                } elseif(in_array($stval['Name'],$stsl)){
                    $name = 'Slaty';
                } elseif(in_array($stval['Name'],$stwa)){
                    $name = 'Waste';
                }

                //echo json_encode($data);
                $resultFF = $data['FFQuality'.$name];
                $resultFAQ = $data['FAQQuality'.$name];
                if($resultFF > 0 || $resultFAQ > 0){
                    /**
                     * ini untuk ngecek dump ya... jgn dihapus ^_^
                    echo '<br>';
                    echo 'FFQuality'.$stval['Name'] . ': ' . $resultFF . '; ';
                    echo 'FFStandard'.$stval['Name'] . ': ' . $stval['FFValue'] . '; ';
                    echo 'FFFormula'.$stval['Name'] . ': ' . $stval['FFFormula'] . '; ';
                    echo 'FFReward'.$stval['Name'] . ': ' . $this->calculateReward($stval['FFFormula'],$stval['FFValue'],$resultFF) . ';';
                    echo '<br>';
                    echo 'FAQQuality'.$stval['Name'] . ': ' . $resultFAQ . '; ';
                    echo 'FAQStandard'.$stval['Name'] . ': ' . $stval['FAQValue'] . ' ;';
                    echo 'FAQFormula'.$stval['Name'] . ': ' . $stval['FAQFormula'] . '; ';
                    echo 'FAQReward'.$stval['Name'] . ': ' . $this->calculateReward($stval['FAQFormula'],$stval['FAQValue'],$resultFAQ) . ';';
                    echo '<br>';
                    */
                }
                //die;
                //if($this->_checkTransIDExist($data['SupplyTransID'])){
                  $insert = array(
                      'DetailID' => $stval['DetailID'],
                      'StandardID' => $stval['StandardID'],
                      'SupplyTransID' => $data['SupplyTransID'],
                      'FFStandard' => $stval['FFValue'],
                      'FAQStandard' => $stval['FAQValue'],
                      'FFResult' => $resultFF,
                      'FAQResult' => $resultFAQ,
                      'FFReward' => $this->calculateReward($stval['FFFormula'],$stval['FFValue'],$resultFF),
                      'FAQReward' => $this->calculateReward($stval['FAQFormula'],$stval['FAQValue'],$resultFAQ),
                  );

                  $this->db->insert('ktv_supplychain_transaction_quality',$insert);
                  $ins = $this->db->insert_id();
                  if($ins){
                      echo '<p>Success: <'.$ins.'> : ' . json_encode($insert) . '</p>';
                  } else {
                      echo 'Failed : ' . json_encode($insert);
                  }
                //} else {
                  //echo '<p>Already exist..</p>';
                //}
            }
            echo '<hr>';
            //die;
        }

    }

    private function _checkTransIDExist($transid) {

      $this->db->select('SupplyTransID');
      $this->db->from('ktv_supplychain_transaction_quality');
      $this->db->where('SupplyTransID',$transid);
      $Q = $this->db->get();
      if($Q->num_rows() > 0){
          return false;
      }
      return true;
    }


    /**
     * Fungsi-fungsi untuk migrasi data traceability yang lama
     * @author Ardi <ardiantoro@koltiva.com>
     */

    /**
     * ini fungsi sementara aja ya
     * @param type $id
     * @return boolean
     */
    function getStandardID($id) {
        $output = false;

        $this->db->select('StandardID');
        $this->db->from('ktv_supplychain_quality_standard');
        //$this->db->join('ktv_supplychain_quality_standard','ktv_supplychain_quality_standard.StandardID = ktv_supplychain_quality_standard_detail.StandardID','left');
        $this->db->where('StandardSupplychainID',$id);
        //$this->db->where('StandardSupplychainID',56);
        //$this->db->group_by('StandardSupplychainID');
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            $output['StandardID'] = $result[0]['StandardID'];
            foreach($result as $key => $value) {
                $this->db->select('*');
                $this->db->from('ktv_supplychain_quality_standard_detail');
                $this->db->where('StandardID',$value['StandardID']);
                $Q2 = $this->db->get();
                $details = $Q2->result_array();
                foreach($details as $kdet => $detail) {
                    echo '<pre>';
                    //var_dump($detail);
                    $output['Detail'][] = $detail;
                }
            }
        }
        //var_dump($output);die;
        return $output;
    }

    function calculateReward($formula,$standard,$result) {
            //echo '<hr>';
        $output = 0;
        if($result != NULL && $result > 0 && strlen($formula) > 0) {
            //echo 'formula: ' . $formula . '; ';
            //echo 'standard: ' . $standard . '; ';
            //echo 'result: ' . $result . '; ';

            $reward = false;
            $claim = false;
            $find = array('[R]','[S]');
            $replace = array('$result','$standard');
            $result = floatval($result);
            $standard = floatval($standard);
            $formula = str_replace($find,$replace,$formula); //echo '<br>formula: ' . $result . '-' . $standard;
            eval("\$hasil = $formula;"); //dangerous but, nevermind~
            //echo '<br>reward: ' . $hasil . '; ';

            $output = $hasil;
        }

        return $output;
    }

    /**
     * Fungsi-fungsi untuk copy setting buying unit
     */

    // Tab Setting
    public function getSourceDataSetting($supplychainsource) {

        $this->db->select('PerwakilanKoperasi,
            MekanismeReward,
            MekanismeMoisture,
            NonTraceableFarmer,
            PembelianFarmer,
            PembelianFarmerCert,
            TanpaKualitas,
            LabelKarung,
            PemisahanBatch,
            PembelianNonFarmer,
            PembelianBatch,
            IstilahFarmer,
            KalkulasiPremium,
            IsFF,
            IsFAQ,
            IsMoistureKarung,
            IsCalculateMoistureKarung,
            FormulaNettoKarung,
            FormulaNettoPrice,
            FormulaNettoAkhir,
            LabelFarmerCertified,
            LabelFarmerNonCertified,
            LabelNonFarmer,
            IsFakturNumber,
            IsDriver,
            IsPoliceNumber,
            IsSuratJalan,
            IsGeneratePacking,
            EqualNetWeight,
            StatusCode,
            IsGenerateBuyingUnitBatch,
            GenerateWeightOrPackage,
            StandardMoistureKarung');
        $this->db->from('ktv_supplychain_org');
        $this->db->where('SupplychainID',$supplychainsource);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row_array();
            return $row;
        }

        return false;
    }

    public function migrateSettingTab($source_setting,$supplychaintarget,$districtid) {
        $this->db->where_in('SupplychainID',$supplychaintarget);
        $this->db->update('ktv_supplychain_org',$source_setting);
        if($this->db->_error_number() > 0){
            return true;
        }
        return false;
    }

    // Tab Relation
    public function getSourceDataRelation($supplychainsource) {
        $this->db->select('ParentOrgId,
            ChildOrgId,
            NoKontrak,
            StartDate,
            EndDate,
            Description');
        $this->db->from('ktv_supplychain_org_rel');
        $this->db->where('ParentOrgId',$supplychainsource);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            return $result;
        }
        return false;
    }

    public function migrateRelationTab($source_relation,$supplychaintarget,$districtid) {

        foreach($source_relation as $keys => $value){
            $insert = array(
                'ParentOrgId' => $supplychaintarget,
                'ChildOrgId' => $value['ChildOrgId'],
                'NoKontrak' => $value['NoKontrak'],
                'StartDate' => $value['StartDate'],
                'EndDate' => $value['EndDate'],
                'Description' => $value['Description']
            );

            $this->db->insert('ktv_supplychain_org_rel',$insert);
        }
    }

    // Tab Quality Std
    public function getSourceDataStd($supplychainsource) {
        $this->db->select('StandardID,StandardSupplychainID,StandardName,IsClaim,IsReward');
        $this->db->from('ktv_supplychain_quality_standard');
        $this->db->where('StandardSupplychainID',$supplychainsource);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            return $result;
        }
        return false;
    }

    public function migrateStdTab($source_std,$supplychainsource,$supplychaintarget,$districtid) {

        foreach ($source_std as $key => $value) {

            foreach($supplychaintarget as $keys => $values) {
                $insert = array(
                    'StandardName' => $value['StandardName'],
                    'IsClaim' => $value['IsClaim'],
                    'IsReward' => $value['IsReward'],
                    'StandardSupplychainID' => $values
                );
                $this->db->insert('ktv_supplychain_quality_standard',$insert);
                $std = $this->db->insert_id();
                $source_quality = $this->getSourceDataQuality($value['StandardID']);
                if($source_quality){
                    $this->migrateQualityDetail($source_quality,$std);
                    $this->migrateQualityTab($supplychainsource, $values, $std, $districtid);
                }
            }

        }



    }

    // Tab Quality
    public function getSourceDataQuality($stdId) {
        $this->db->select('Name,Formula,Order,FAQFormula,FFFormula,FAQValue,FFValue,Value');
        $this->db->from('ktv_supplychain_quality_standard_detail');
        $this->db->where('StandardID',$stdId);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            return $result;
        }
        return false;
    }

    // Std Quality Details
    public function migrateQualityDetail($source_quality,$std) {
        foreach ($source_quality as $key => $value) {
            $insert = array(
                'StandardID' => $std,
                'Name' => $value['Name'],
                'Formula' => $value['Formula'],
                'Order' => $value['Order'],
                'FAQFormula' => $value['FAQFormula'],
                'FFFormula' => $value['FFFormula'],
                'FAQValue' => $value['FAQValue'],
                'FFValue' => $value['FFValue'],
                'Value' => $value['Value']
            );
            $this->db->insert('ktv_supplychain_quality_standard_detail',$insert);
        }
    }

    public function migrateQualityTab($supplychainsource, $supplychaintarget, $std, $districtid){

        $this->db->select('QualityDateStart,QualityDateEnd');
        $this->db->from('ktv_supplychain_quality');
        $this->db->where('QualitySupplychainID',$supplychainsource);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();

            foreach ($result as $key => $value) {
                $insert = array(
                    'QualitySupplychainID' => $supplychaintarget,
                    'QualityDateStart' => $value['QualityDateStart'],
                    'QualityDateEnd' => $value['QualityDateEnd'],
                    'StandardID' => $std
                );
                $this->db->insert('ktv_supplychain_quality',$insert);
            }
        }

        return false;
    }

    // Tab Price
    public function getSourceDataPrice($supplychainsource) {
        $this->db->select('PriceDateStart,PriceDateEnd,FFPrice,FAQPrice');
        $this->db->from('ktv_supplychain_price');
        $this->db->where('PriceSupplychainID',$supplychainsource);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            return $result;
        }
        return false;
    }

    public function migratePriceTab($source_price,$supplychaintarget,$districtid) {

        foreach ($source_price as $key => $value) {
            foreach($supplychaintarget as $keys => $values) {
                $insert = array(
                    'PriceSupplychainID' => $values,
                    'PriceDateStart' => $value['PriceDateStart'],
                    'PriceDateEnd' => $value['PriceDateEnd'],
                    'FFPrice' => $value['FFPrice'],
                    'FAQPrice' => $value['FAQPrice'],
                    'DistrictID' => $districtid
                );
                $this->db->insert('ktv_supplychain_price',$insert);
            }

        }
    }

    // Tab Packaging
    public function getSourceDataPackaging($supplychainsource) {
        $this->db->select('PackageType,PackageWeight,PackageCapasity');
        $this->db->from('ktv_supplychain_package');
        $this->db->where('PackageSupplychainID',$supplychainsource);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            return $result;
        }
        return false;
    }

    public function migratePackagingTab($source_packaging,$supplychaintarget,$districtid) {
        foreach ($source_packaging as $key => $value) {
            foreach($supplychaintarget as $keys => $values) {
                $insert = array(
                    'PackageSupplychainID' => $values,
                    'PackageType' => $value['PackageType'],
                    'PackageWeight' => $value['PackageWeight'],
                    'PackageCapasity' => $value['PackageCapasity'],
                );
                $this->db->insert('ktv_supplychain_package',$insert);
            }

        }
    }

    // Tab Kurs
    public function getSourceDataKurs($supplychainsource) {
        $this->db->select('KursDateStart,KursDateEnd,KursNominal');
        $this->db->from('ktv_supplychain_kurs');
        $this->db->where('KursSupplychainID',$supplychainsource);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            return $result;
        }
        return false;
    }

    public function migrateKursTab($source_kurs,$supplychaintarget,$districtid) {
        foreach ($source_price as $key => $value) {
            foreach($supplychaintarget as $keys => $values) {
                $insert = array(
                    'KursSupplychainID' => $values,
                    'KursDateStart' => $value['KursDateStart'],
                    'KursDateEnd' => $value['KursDateEnd'],
                    'KursNominal' => $value['KursNominal'],
                );
                $this->db->insert('ktv_supplychain_kurs',$insert);
            }

        }
    }

	public function getDataMember($member = false){

		$this->db->select('memberID');
		$this->db->from('coop_member');
		$this->db->where('status',4);
		if($member){
			$this->db->where('memberID',$member);
		}
		$Q = $this->db->get();
		if($Q->num_rows() > 0){
			return $Q->result_array();
		}

		return false;
	}

  public function migrasiCreateBatchForCargill($batch = false) {

    $org = $this->getSupplyOrgByDistrict(7312); //var_dump($org); die;

    $this->db->select('SupplyBatchID,SupplyBatchNumber,SupplyDestOrgID');
    $this->db->from('ktv_supplychain_batch');
    $this->db->where_in('SupplyOrgID',$org);
    //$this->db->limit(1);
    $this->db->where('SupplyDestStatus','Sent');//var_dump($this->db->_compile_select());die;
    //$this->db->where('SupplyBatchDate > "2015-12-31"',null,false);
    $Q = $this->db->get();
    if($Q->num_rows() > 0) { //var_dump($Q->num_rows());die;
      $result = $Q->result_array();
      foreach($result as $keys => $values) {
        $this->load->model('sync/msync');
        echo $this->msync->_createBatchForCargill($values['SupplyBatchID'],$values['SupplyDestOrgID']) . '<br />';
      }
    }
    die;
    return false;
  }

  public function getSupplyOrgByDistrict($district = false) {

    $this->db->select('SupplyChainID');
    $this->db->from('ktv_supplychain_org_view');
    $this->db->join('ktv_village','ktv_village.VillageID = ktv_supplychain_org_view.VillageID','left');
    $this->db->join('ktv_subdistrict','ktv_subdistrict.SubDistrictID = ktv_village.SubDistrictID','left');

    if($district) {
        $this->db->where('DistrictID',$district);
    }

    $Q = $this->db->get();
    $output = array();
    if($Q->num_rows() > 0) {
      $result = $Q->result_array();
      foreach($result as $keys => $values) {
        array_push($output,$values['SupplychainID']);
      }
      return $output;
    }

    return false;

  }

    // Tab Premium
    public function getSourceDataPremium($supplychainsource) {}
    public function migratePremiumTab($source_premium,$supplychaintarget,$districtid) {}
}

?>

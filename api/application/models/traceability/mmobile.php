<?php

/**
 * Authentication Model for Mobile Traceability
 *
 * @author ardiantoro <ardiantoro@koltiva.com>
 */
class Mmobile extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function doLogin($username = false, $password = false) {
        
        $sql = "SELECT su.UserId, su.UserRealName, su.UserName,( IFNULL( ( SELECT PartnerID FROM ktv_private_staff WHERE UserId = su.UserId), IFNULL( ( SELECT ktv_warehouse.PartnerID FROM ktv_warehouse_staff LEFT JOIN ktv_warehouse ON ktv_warehouse.WarehouseID = ktv_warehouse_staff.WarehouseID WHERE ktv_warehouse_staff.UserId = su.UserId ), IFNULL( (SELECT PartnerID FROM ktv_supplychain_org_partner op LEFT JOIN ktv_supplychain_org org ON org.SupplychainID = op.SupplychainID LEFT JOIN ktv_traders traders ON traders.TraderID = org.OrgID LEFT JOIN ktv_trader_staff tstaff ON tstaff.TraderID = traders.TraderID LEFT JOIN sys_user user ON user.UserId = tstaff.UserId WHERE user.UserName = ?), IFNULL( ( SELECT ktv_district_partner.PartnerID FROM ktv_district_partner WHERE ktv_district_partner.DistrictID = ( SELECT DistrictID FROM ktv_subdistrict INNER JOIN ktv_village ON ktv_village.SubDistrictID = ktv_subdistrict.SubDistrictID INNER JOIN ktv_farmer ON ktv_farmer.VillageID = ktv_village.VillageID INNER JOIN sce_farmer_staff ON sce_farmer_staff.FarmerID = ktv_farmer.FarmerID WHERE sce_farmer_staff.UserId = su.UserId ) ), IFNULL( ( SELECT ktv_district_partner.PartnerID FROM ktv_district_partner WHERE ktv_district_partner.DistrictID = ( SELECT DistrictID FROM ktv_cooperative_staff JOIN ktv_cooperatives ON ktv_cooperatives.CoopID = ktv_cooperative_staff.CoopID JOIN ktv_village ON ktv_village.VillageID = ktv_cooperatives.VillageID JOIN ktv_subdistrict ON ktv_subdistrict.SubDistrictID = ktv_village.SubDistrictID WHERE ktv_cooperative_staff.UserId = su.UserId ) ), 'Unknown' ) ) ) ) ) ) AS PartnerID, ( IFNULL( ( SELECT PartnerID FROM ktv_private_staff WHERE UserId = su.UserId ), IFNULL( ( SELECT ktv_warehouse.WarehouseID FROM ktv_warehouse_staff LEFT JOIN ktv_warehouse ON ktv_warehouse.WarehouseID = ktv_warehouse_staff.WarehouseID WHERE ktv_warehouse_staff.UserId = su.UserId ), IFNULL( ( SELECT ktv_trader_staff.TraderID FROM ktv_trader_staff WHERE ktv_trader_staff.UserId = su.UserId ), IFNULL( ( SELECT sce_farmer_staff.SceID FROM sce_farmer_staff WHERE sce_farmer_staff.UserId = su.UserId ), IFNULL( ( SELECT ktv_cooperative_staff.CoopID FROM ktv_cooperative_staff WHERE ktv_cooperative_staff.UserId = su.UserId ), 'Unknown' ) ) ) ) ) ) AS UnitID, ( IFNULL( ( SELECT PartnerID FROM ktv_private_staff WHERE UserId = su.UserId ), IFNULL( ( SELECT ktv_warehouse.WarehouseName FROM ktv_warehouse_staff LEFT JOIN ktv_warehouse ON ktv_warehouse.WarehouseID = ktv_warehouse_staff.WarehouseID WHERE ktv_warehouse_staff.UserId = su.UserId ), IFNULL( ( SELECT ktv_traders.TraderName FROM ktv_trader_staff LEFT JOIN ktv_traders ON ktv_traders.TraderID = ktv_trader_staff.TraderID WHERE ktv_trader_staff.UserId = su.UserId ), IFNULL( ( SELECT sce_farmer_staff.StaffName FROM sce_farmer_staff WHERE sce_farmer_staff.UserId = su.UserId ), IFNULL( ( SELECT ktv_cooperative_staff.StaffName FROM ktv_cooperative_staff WHERE ktv_cooperative_staff.UserId = su.UserId ), 'Unknown' ) ) ) ) ) ) AS UnitName, ( IFNULL( ( SELECT 'Partner' FROM ktv_private_staff WHERE UserId = su.UserId ), IFNULL( ( SELECT 'Warehouse' FROM ktv_warehouse_staff LEFT JOIN ktv_warehouse ON ktv_warehouse.WarehouseID = ktv_warehouse_staff.WarehouseID WHERE ktv_warehouse_staff.UserId = su.UserId ), IFNULL( ( SELECT 'Trader' FROM ktv_trader_staff LEFT JOIN ktv_traders ON ktv_traders.TraderID = ktv_trader_staff.TraderID WHERE ktv_trader_staff.UserId = su.UserId ), IFNULL( ( SELECT 'SCE' FROM sce_farmer_staff WHERE sce_farmer_staff.UserId = su.UserId ), IFNULL( ( SELECT 'Cooperative' FROM ktv_cooperative_staff WHERE ktv_cooperative_staff.UserId = su.UserId ), 'Unknown' ) ) ) ) ) ) AS OrgType FROM sys_user su WHERE su.UserActive = 'Yes' AND su.UserName = ? AND su.UserPassword = md5(?)";
        
        $Q = $this->db->query($sql,array($username,$username,$password));
        
        if($Q->num_rows()){
            $row = $Q->row();
            
            $token = $this->getToken($username);

            $update = array(
                'UserMobileToken' => $token
            );

            $this->db->where('UserName',$username);
            $this->db->update('sys_user',$update);
            
            if(!$this->db->_error_number()){

                return array(
                    'Name'=> $row->UserName, 
                    'RealName'=> $row->UserRealName, 
                    'UserID' => $row->UserId,
                    'PartnerID' => $row->PartnerID,
                    'SupplyChainID' => $row->SupplychainID,
                    'OrgName'=> $row->UnitName,
                    'OrgID'=> $row->UnitID,
                    'OrgType'=> $row->OrgType,
                    'Area' => $this->getUserDistrict($username,true),
                    'Token' => $token
                );
            }
            
        }
        
        return false;
    }
    
    public function doLogout($token = FALSE) {
        
        $salt = 'bismillah';
        $username = (string) $this->encrypt->decode($token,$salt);
        
        $update = array(
            'UserMobileToken' => NULL
        );

        $this->db->where('UserName',$username);
        $this->db->where('UserMobileToken',$token);
        $this->db->update('sys_user',$update);
        
        if(!$this->db->_error_number()){
            return true;
        }
        
        return false;
    }
    
    public function getToken($data) {
        
        $salt = 'bismillah';
        $this->load->library('encrypt');
        
        return $this->encrypt->encode($data,$salt);
    }
    
    public function checkToken($token,$user) {
        
        $this->db->select('StatusCode');
        $this->db->where('UserName',$user);
        $this->db->where('UserMobileToken',$token);
        $Q = $this->db->get('sys_user'); 
        if($Q->num_rows() > 0){
            $row = $Q->row();
            if($row->StatusCode === 'active'){
                return true;
            }
        }
        
        return false;
    }
    
    public function getUserDistrict($user,$return = FALSE) {
        $output = array();
        
        $this->db->select('ktv_access_staff.DistrictID,District');
        $this->db->from('ktv_access_staff');
        $this->db->where('`UserId` = (SELECT `UserId` FROM `sys_user` WHERE `UserName` = "'.$user.'")',NULL,FALSE);
        $this->db->join('ktv_district','ktv_district.DistrictID = ktv_access_staff.DistrictID','LEFT');
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            if($return){
                return $result;
            }
            
            foreach($result as $key => $value) {
                array_push($output, $value['DistrictID']);
            }
        }
        
        return $output;
    }
    
    public function getAuthSession($id = false, $user = false, $pwd = false) {
        
        $this->db->select('StatusCode');
        
        if($id){
            $this->db->where('UserID',$id);
        } else {
            $this->db->where('UserName',$user);
            $this->db->where('UserPassword',md5($pwd));
        }

        $Q = $this->db->get('sys_user');
        if($Q->num_rows() > 0){
            $row = $Q->row();
            if($row->StatusCode === 'active'){
                return true;
            }
        }
        
        return false;
    }

    /**
     * Fungsi untuk mengambil data petani yang di assign ke supplychain berdasarkan data district
     * 
     * @author Ardi <ardiantoro@koltiva.com>
     * @param boolean $sid SupplychainID
     * @param boolean $district DistrictID (optional)
     * @return void
     */
    public function getFarmerBySupplychainID($sid = false, $district = false) {
        
        if($sid && strlen($sid) > 0){ 
            
            $this->db->select('a.MemberID memberid, a.MemberDisplayID farmerid, a.MemberName farmername, "" groupid, "" groupname, a.VillageID villageid, v.Village village, "false" certification, "" certificationlabel, "" certificationstart, "" certificationend, 0 quota,"" extid',false);
            $this->db->from('ktv_members a');
            $this->db->join('ktv_village v','v.VillageID=a.VillageID','LEFT');
            $this->db->join('ktv_subdistrict sd','sd.SubDistrictID=v.SubDistrictID','LEFT');
            $this->db->join('ktv_district ds','ds.DistrictID=sd.DistrictID','LEFT');
            $this->db->join('ktv_member_role mr','mr.MemberID=a.MemberID','LEFT');
            $this->db->join('ktv_supplychain_area area','area.DistrictID=sd.DistrictID','LEFT');
            $this->db->where('area.SupplychainID',$sid);
            $this->db->where('mr.MRoleID',1);
            
            if($district) {
                $this->db->where('sd.DistrictID',$district); //optional, kalo mau ambil per district yak
            }
            
            //$this->db->limit(10); //untuk sekarang pake limit ya, biar ga berat
            //echo($this->db->_compile_select());die;
            /*
            $sql = 'SELECT a.MemberUID farmerid, a.MemberName farmername, "" groupid, "" groupname, a.VillageID villageid, v.Village village, "false" certification, "" certificationlabel, "" certificationstart, "" certificationend, 0 quota, "" extid
            FROM (`ktv_members` a)
            LEFT JOIN `ktv_village` v ON `v`.`VillageID`=`a`.`VillageID`
            LEFT JOIN `ktv_subdistrict` sd ON `sd`.`SubDistrictID`=`v`.`SubDistrictID`
            LEFT JOIN `ktv_district` ds ON `ds`.`DistrictID`=`v`.`DistrictID`
            LEFT JOIN `ktv_member_role` mr ON `mr`.`MemberID`=`a`.`MemberID`
            LEFT JOIN `ktv_supplychain_area` area ON `area`.`DistrictID`=`sd`.`DistrictID`
            WHERE `area`.`SupplychainID` =  "'.$sid.'"
            AND `mr`.`MRoleID` =  1';
            */
            $Q = $this->db->get(); 
            if($Q->num_rows() > 0) {
                $results = $Q->result_array();
                return array_values($results);
            }

        }

        return array();
    }

    /**
     * Fungsi untuk mengambil harga yang di assign ke supplychain
     * 
     * @author Ardi <ardiantoro@koltiva.com>
     * @param boolean $sid SupplychainID
     * @param boolean $pricedate Price Date (optional)
     * @return void
     */
    public function getPriceBySupplychainID($sid = false, $pricedate = false) {

        $pricedate = strtotime($pricedate)?date('Y-m-d',strtotime($pricedate)):date('Y-m-d');

        if($sid && strlen($sid) > 0){
            $sql_price = "SELECT Price price FROM ktv_supplychain_price WHERE PriceSupplychainID=? AND ? BETWEEN PriceDateStart AND PriceDateEnd ORDER BY PriceID DESC LIMIT 1";
            $Q = $this->db->query($sql_price, array($sid, $pricedate)); 

            if($Q->num_rows() > 0) { 
                $results = $Q->row();
                return $results->price;
            }

        }

        return 0;
    }

    /**
     * Fungsi untuk mengambil sisa kuota petani terkait
     * 
     * @author Ardi <ardiantoro@koltiva.com>
     * @param boolean $fid FarmerID
     * @return void
     */
    public function getQuotaByFarmerID($fid = false) {
        
                
        if($fid && strlen($fid) > 0){
            $sql_quota = "SELECT
            SupplyID,
            (
                (t.CertNextHarvest * 1.1) - SUM(VolumeBruto)
            ) sisa
        FROM
            ktv_supplychain_transaction a
        INNER JOIN (
            SELECT
                FarmerID,
                CertNextHarvest,
                CertificationStart,
                CertificationEnd
            FROM
                ktv_cocoa_certification_afl_farmer afl
            LEFT JOIN ktv_ims ims ON ims.IMSID = afl.IMSID
            WHERE afl.FarmerID =?
        ) t ON t.FarmerID = a.SupplyID
        WHERE
            a.SupplyType = 'Farmer'
        AND (
            DateTransaction >= t.CertificationStart
            AND DateTransaction <= t.CertificationEnd
        ) AND a.SupplyID = ?";
            $Q = $this->db->query($sql_quota, array($fid, $fid)); 

            if($Q->num_rows() > 0) { 
                $results = $Q->row();
                return $results->sisa;
            }

        }

        return 0;
    }

    public function postBatch($data) {

        if(!is_array($data)){
            $data = json_decode($data,true);
        }
        
        $this->load->helper('file');
        
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $success = false;
        $return = array();
        
        // backup dulu ke file, siapa tau ada masalah di datanya
        $name = $data['batchnumber'] . '-' . $data['createdby'] . '-' . $data['supplychainid'] . '-' . strtotime(date('Y-m-d H:i:s'));
        $dir = FCPATH . 'backup_traceability_sync';

        if(!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        
        // check if batch number is exist
        $this->db->select('SupplyBatchID,SupplyBatchNumber');
        $this->db->from('ktv_supplychain_batch');
        $this->db->where('SupplyBatchNumber', $data['batchnumber']);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            
            //if((int) $data['destsupplychainid'] == 1) { $data['destsupplychainid'] = NULL; }
            
            $update = array(
                'SupplyOrgID' => $data['supplychainid'],
                'SupplyDestOrgID' => $data['destsupplychainid'],
                'SupplyDestStatus' => $data['status'],
                'SupplyBatchNumber' => $data['batchnumber'],
                'VolumeBruto' => $data['bruto'],
                'VolumeNetto' => $data['netto'],
                'SupplyBatchDate' => $data['date'],
                'DeliveryDate' => $data['deliverydate'],
                'DestPO' => $data['ponumber'],
                'DestDriver' => $data['drivername'],
                'DestNoPolisi' => $data['policenumber'],
                'NoPO' => $data['ponumber'],
                'CreatedBy' => $data['createdby'],
                'DateCreated' => $data['createddate']
            );

            $this->db->where('SupplyBatchID',$row->SupplyBatchID);
            $this->db->update('ktv_supplychain_batch', $update);
            
            $return['SupplyBatchID'] = $row->SupplyBatchID;
            $return['SupplyBatchNumber'] = $data['batchnumber'];

            $return['trans'] = array();
            $return['quality'] = array();

            //insert trans
            foreach ($data['trans'] as $key => $value) {
                $value['batchid'] = $row->SupplyBatchID;
                $available = $this->_checkTransaction($value['transid'],$row->SupplyBatchID,$value['supplyid'],$value['invoicenumber']);
                
                if($available){
                    //update trans
                    $this->_updateTrans($available,$value,$return);
                } else {
                    //insert trans
                    $this->_insertTrans($data['supplychainid'],$value,$return);
                }

            }

        } else {

            $insert = array(
                'SupplyOrgID' => $data['supplychainid'],
                'SupplyDestOrgID' => $data['destsupplychainid'],
                'SupplyDestStatus' => $data['status'],
                'SupplyBatchNumber' => $data['batchnumber'],
                'VolumeBruto' => $data['bruto'],
                'VolumeNetto' => $data['netto'],
                'SupplyBatchDate' => $data['date'],
                'DeliveryDate' => $data['deliverydate'],
                'DestPO' => $data['ponumber'],
                'DestDriver' => $data['drivername'],
                'DestNoPolisi' => $data['policenumber'],
                'NoPO' => $data['ponumber'],
                'CreatedBy' => $data['createdby'],
                'DateCreated' => $data['createddate']
            );

            $this->db->insert('ktv_supplychain_batch', $insert);
            
            $id = $this->db->insert_id();
            
            if ($id) {

                $return['OldSupplyBatchID']  = $data['batchid'];
                $return['NewSupplyBatchID']  = $id;
                $return['SupplyBatchID']     = $id;
                $return['SupplyBatchNumber'] = $data['batchnumber'];

                $return['trans'] = array();
                
                $return['dtl'] = array();
                
                $return['quality'] = array();

                //insert trans
                foreach ($data['trans'] as $key => $value) {
                    $value['batchid'] = $id;
                    $available = $this->_checkTransaction($value['transid'],$id,$value['supplyid'],$value['invoicenumber']);
                    
                    if($available){
                        //update trans
                        $this->_updateTrans($available,$value,$return);
                    } else {
                        //insert trans
                        $this->_insertTrans($data['supplychainid'],$value,$return);
                    }
    
                }

                $success = true;

                $tujuan = $data['destsupplychainid'];

                //autobatch
                $check = $this->db->query("SELECT IsAutoBatch, SentEmail FROM ktv_supplychain_org WHERE SupplychainID=?",array($tujuan))->row();
                if($check && $data['status'] == 'sent') {
                    $this->load->model('traceability/mtransaction','_transaction');
                
                    if($check->IsAutoBatch=='1' && $data['status'] == 'Sent'){
                        //$this->_createBatchForCargill($id,$tujuan);
                        $this->_transaction->autoCreateBatch($id,$tujuan,@$data['deliverydate'],@$data['bruto'],@$data['netto'],$value['createdby'],$data['createddate']);
                    }

                    //email konfirmasi
                    if($check->SentEmail!=''){
                        $sentEmail = $this->_transaction->sentEmailBatch($id);
                    }
                }
            }
        }
        //end sync
        
        return $return;
    }

    public function postTransaction($data) {

        if(!is_array($data)){
            $data = json_decode($data,true);
        }
        
        $this->load->helper('file');
        
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $success = false;
        $return = array(
            'trans' => array(),
            'quality' => array()
        );
        
        // backup dulu ke file, siapa tau ada masalah di datanya
        $name = $data['invoicenumber'] . '-' . $data['createdby'] . '-' . $data['invoicenumber'] . '-' . strtotime(date('Y-m-d H:i:s'));
        $dir = FCPATH . 'backup_traceability_sync';

        if(!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $data['batchid'] = (int)$data['batchid'] > 0?$data['batchid']:NULL;

        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        
            $available = $this->_checkTransaction($data['transid'],$data['batchid'],$data['supplyid'],$data['invoicenumber']);
            
            if($available){
                //update trans
                $this->_updateTrans($available,$data,$return);
            } else {
                //insert trans
                $this->_insertTrans($data['supplychainid'],$data,$return);
            }

        return $return;
    }

    /**
     * @author Ardi <ardiantoro@koltiva.com>
     * fungsi untuk ambil latest quota by farmerid yang digunakan di tablet
     */
    private function _getQuota($farmerid) {
        
        $output = 0;
        $sql = "SELECT CAST(IFNULL(dt.AFLProduction, IFNULL(dt.AFLHarvest,IFNULL(dt.GardenHarvest,dt.GardenProduction))) + (IFNULL(dt.AFLProduction, IFNULL(dt.AFLHarvest,IFNULL(dt.GardenHarvest,dt.GardenProduction))) * 10/100) - (SUM(IFNULL(VolumeBruto,IFNULL(VolumeNetto,0)))) as decimal(10,2)) Quota
                FROM
                (
                SELECT
                                a.FarmerID id, a.FarmerName name, IF(a.ExtFarmerID!='' && a.ExtFarmerID IS NOT NULL, a.ExtFarmerID, '-') ExtFarmerID,
                                SUM(IFNULL(c.Production,0)) GardenProduction,
                                SUM(IFNULL(((IFNULL(c.PanenBiasaMonths,0) * IFNULL(c.PanenBiasaPanenMonth,0) * IFNULL(c.PanenBiasaKg,0)) + (IFNULL(c.PanenTrekMonths,0) * IFNULL(c.PanenTrekPanenMonth,0) * IFNULL(c.PanenTrekKg,0)) + (IFNULL(c.PanenRayaMonths,0) * IFNULL(c.PanenRayaPanenMonth,0) * IFNULL(c.PanenRayaKg,0))),0)) GardenHarvest,
                                d.CertNextHarvest AFLHarvest,
                                e.Production AFLProduction,
                                b.CertificationStart,
                                b.CertificationEnd
                                
                FROM ktv_cocoa_farmer a
                                LEFT JOIN ktv_cocoa_certification b ON a.FarmerID=b.FarmerID AND b.Certification!=0 AND b.GardenNr!=0 AND NOW() BETWEEN b.CertificationStart AND b.CertificationEnd AND b.ExternalDate!='0000-00-00'
                                LEFT JOIN ktv_cocoa_farmer_garden c ON b.FarmerID=c.FarmerID AND b.GardenNr=c.GardenNr AND b.SurveyNr=c.SurveyNr
                                LEFT JOIN ktv_cocoa_certification_afl_farmer d ON d.FarmerID=a.FarmerID AND d.CertSurveyNr=c.SurveyNr
                                LEFT JOIN ktv_cocoa_certification_pre_afl e ON d.FarmerID=e.FarmerID AND d.IMSID=e.IMSID
                                
                WHERE a.FarmerID='".$farmerid."'
                ) dt
                LEFT JOIN ktv_supplychain_transaction t ON t.SupplyType='Farmer' AND t.SupplyID=dt.id AND t.DateTransaction BETWEEN dt.CertificationStart AND dt.CertificationEnd AND t.IsQuota='1'";
        $Q = $this->db->query($sql);
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            $output = $row->Quota;
        }

        return $output;
    }

    private function _insertTrans($sid,$value,&$return) {

        //check farmer quota
        //if($this->_getQuota($value['supplyid']) < $value['bruto']) {
        //    $value['type'] = 'FarmerNonCert';
        //}
        
        $value['batchid'] = $value['batchid'] > 0? $value['batchid'] : NULL;
        
        /* Ga pake quality dulu ya
        if(count($value['quality']) > 0) {
            
            foreach($value['quality'] as $qua => $vq) {
                
                $value['FAQInsentifWaste'] = $this->checkWaste($vq['id'],$vq['incentive']);
                $value['FAQInsentifBrix'] = $this->checkBrix($vq['id'],$vq['incentive']);
                
            }
        }
        */
        if($value['type'] != 'NonFarmer' && !$this->getMemberID($value['supplyid'])){
            //return false;
        }

        $trans = array(
            'SupplyBatchID' => $value['batchid'],
            'SupplychainID' => $sid,
            'DateTransaction' => date('Y-m-d',strtotime($value['date'])),
            'SupplyType' => $value['type'],
            'SupplyID' => $value['supplyid'],
            //'SupplyID' => ($value['type'] == 'NonFarmer')? $value['supplyid'] : $this->getMemberID($value['supplyid']),
            'VolumeBruto1' => $value['bruto'],
            'VolumeNetto' => $value['netto'],
            'NetPrice' => $value['netprice'],
            'NumberPackage' => $value['cluster'],
            'Bjr' => $value['bjr'],
            'PlotNr' => $value['plotnr'],
            'TotalPayment' => $value['totalpayment'],
            'FakturNumber' => $value['fakturnumber'],
            'InvoiceNumber' => $value['invoicenumber']
        );

        $this->db->insert('ktv_supplychain_transaction', $trans);

        $transid = $this->db->insert_id();
        
        //add new farmer
        if($transid && $value['type'] == 'NonFarmer') {
            $this->addNewFarmer($value);
        }

        //quality
        /*
        if(count($value['quality']) > 0) {
            
            foreach($value['quality'] as $qua => $vq) {
                
                $quality = array(
                    'DetailID' => $vq['id'],
                    'SupplyTransID' => $transid,
                    'FAQResult' => $vq['result'],
                    'FAQReward' => $vq['reward']
                );

                $this->db->insert('ktv_supplychain_transaction_quality', $quality);
                $qualityid = $this->db->insert_id();
                if($qualityid){
                    array_push($return['quality'], array(
                        'NewQualityID' => $qualityid
                    ));
                }
            }
        }
        */
        //update quota petani di tablet
        $quota = 0;//$this->_getQuota($value['supplyid']);
        
        array_push($return['trans'], array(
            'OldTransID' => $value['transid'],
            'NewTransID' => $transid,
            'Type' => $value['type'],
            'TransID' => $transid,
            'InvoiceNumber' => $value['invoicenumber'],
            'SupplyID' => $value['supplyid'],
            'SupplyBatchID' => $value['batchid'],
            'Quota' => $quota
        ));
        
        $detail = array(
            'FromBatchID' => NULL,
            'SupplyTransID' => $transid,
            //'PackageID' => $value['packageid'],
            'Type' => $value['type'],
            'Weight' => $value['bruto'],
            //'WeightPackage' => $value['packagesize'],
            'DateCreated' => $value['createddate'],
            'CreatedBy' => $value['createdby'],
        );

        $this->db->insert('ktv_supplychain_transaction_dtl', $detail);

        $detailid = $this->db->insert_id();
    }

    private function _updateTrans($available,$value,&$return) {

        //check farmer quota
        //if($this->_getQuota($value['supplyid']) < $value['bruto']) {
        //    $value['type'] = 'FarmerNonCert';
        //}
        
        if($value['type'] != 'NonFarmer' && !$this->getMemberID($value['supplyid'])){
            //return false;
        }

        $trans = array(
            'SupplyBatchID' => $value['batchid'],
            'DateTransaction' => date('Y-m-d',strtotime($value['date'])),
            'SupplyType' => $value['type'],
            //'SupplyID' => ($value['type'] == 'NonFarmer')? $value['supplyid'] : $this->getMemberID($value['supplyid']),
            'SupplyID' => $value['supplyid'],
            'VolumeBruto1' => $value['bruto'],
            'VolumeNetto' => $value['netto'],
            'NetPrice' => $value['netprice'],
            'NumberPackage' => $value['cluster'],
            'Bjr' => $value['bjr'],
            'PlotNr' => $value['plotnr'],
            'TotalPayment' => $value['totalpayment'],
            'FakturNumber' => $value['fakturnumber'],
            'InvoiceNumber' => $value['invoicenumber']
        );

        $this->db->where('SupplyTransID',$available->SupplyTransID);
        $this->db->update('ktv_supplychain_transaction', $trans);

        $transid = $available->SupplyTransID;

        //update quota petani di tablet
        $quota = 0;//$this->_getQuota($value['supplyid']);
        
        array_push($return['trans'], array(
            'TransID' => $transid,
            'Type' => $value['type'],
            'InvoiceNumber' => $value['invoicenumber'],
            'SupplyBatchID' => $value['batchid'],
            'Quota' => $quota
        ));
        
        //add new farmer
        if($transid && $value['type'] == 'NonFarmer') {
            $this->addNewFarmer($value);
        }
        //delete all quality
        //$this->db->where('SupplyTransID',$transid);
        //$this->db->delete('ktv_supplychain_transaction_quality');

        //quality
        /*
        if(count($value['quality']) > 0) {
            
            foreach($value['quality'] as $qua => $vq) {

                $quality = array(
                    'DetailID' => $vq['id'],
                    'SupplyTransID' => $transid,
                    'FAQResult' => $vq['result'],
                    'FAQReward' => $vq['reward']
                );

                $this->db->insert('ktv_supplychain_transaction_quality', $quality);
                $qualityid = $this->db->insert_id();
                if($qualityid){
                    array_push($return['quality'], array(
                        'NewQualityID' => $qualityid
                    ));
                }
            }
        }
        */
        $detail = array(
            'FromBatchID' => NULL,
            'SupplyTransID' => $transid,
            //'PackageID' => $value['packageid'],
            'Type' => $value['type'],
            'Weight' => $value['bruto'],
            //'WeightPackage' => $value['packagesize']
        );
        
        $this->db->where('SupplyTransID',$available->SupplyTransID);
        $this->db->update('ktv_supplychain_transaction_dtl', $detail);

        $detailid = $this->db->insert_id();
        
    }

    private function _checkTransaction($transid = false,$bid,$supplyid,$invoicenumber) {
        $this->db->select('SupplyTransID,InvoiceNumber,SupplyBatchID');
        $this->db->from('ktv_supplychain_transaction');
        if($transid > 0){
            $this->db->where('SupplyTransID',$transid);
        } else {
            //$this->db->where('SupplyBatchID',$bid);
            //$this->db->where('SupplyID',$supplyid);
            $this->db->where('InvoiceNumber',$invoicenumber);
        }
        $Q = $this->db->get(); 
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row;
        }

        return false;
    }
    
    private function checkWaste($id,$insentif) {

        $Q = $this->db->query('SELECT DetailID FROM ktv_supplychain_quality_standard_detail WHERE DetailID = "'.$id.'" AND Name LIKE "%Waste%"');

        if($Q->num_rows()) {
            $row = $Q->row();
            if($row->DetailID > 0) {
                return $insentif;
            }
        }

        return 0;
    }

    private function checkBrix($id,$insentif) {

        $Q = $this->db->query('SELECT DetailID FROM ktv_supplychain_quality_standard_detail WHERE DetailID = "'.$id.'" AND Name LIKE "%Brix%"');
        
        if($Q->num_rows()) {
            $row = $Q->row();
            if($row->DetailID > 0) {
                return $insentif;
            }
        }

        return 0;
    }

    private function getMemberID($id) {

        $sql = 'SELECT MemberID FROM ktv_members WHERE MemberDisplayID = "'.$id.'"';
        $Q = $this->db->query($sql);
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->MemberID;
        }

        return false;
    }

    private function addNewFarmer($data) {

        $cek = $this->checkNewFarmer($data['supplyid'],$data['name']);
        if(!$cek) {
            $insert = array(
                'FarmerID' => $data['supplyid'],
                'TempID' => $data['supplyid'],
                'FarmerName' => $data['name'],
                'GroupName' => $data['groupname'],
                'HandPhone' => $data['handphone'],
                'Village' => $data['village']
            );

            $this->db->insert('ktv_supplychain_non_farmer',$insert);
            
            $insert = array(
                'MemberDisplayID' => $data['supplyid'],
                'Latitude' => $data['lat'],
                'Longitude' => $data['long'],
                'PlotNr' => $data['plotnr']
            );

            $this->db->insert('ktv_temp_member_plot',$insert);
            
        }
    }

    private function checkNewFarmer($farmerid,$farmername) {

        $sql = "SELECT * FROM ktv_supplychain_non_farmer WHERE TempID = ?";
        $Q = $this->db->query($sql, array($farmerid,$farmername));

        if($Q->num_rows() > 0) { return true; }

        return false;
    }

    public function checkBatchNumber($batchnumber) {
        $dest = false;
        $sql1 = "SELECT SupplyDestOrgID FROM ktv_supplychain_batch WHERE SupplyBatchNumber = ?";
        $Q2 = $this->db->query($sql1, array($batchnumber));

        if($Q2->num_rows() > 0) { 
            $dest = $Q2->row();
            $dest = $dest->SupplyDestOrgID; 
        }

        if($dest){
            $sql = "SELECT * FROM ktv_supplychain_transaction WHERE SupplyID = ? AND SupplychainID = ?";
            $Q = $this->db->query($sql, array($batchnumber,$dest));

            if($Q->num_rows() > 0) { return true; }
        }
        
        return false;
    }

    public function getGardenByFarmer($farmer) {

        $this->db->select('PlotNr plotnr,Village village,IFNULL(Longitude,0) longitude,IFNULL(Latitude,0) latitude,GardenAreaHa gardenha, TreeTBM Tbm, TreeTM Tm, TreeTR Tr',false);
        $this->db->from('ktv_survey_plot');
        $this->db->join('ktv_village','ktv_village.VillageID = ktv_survey_plot.VillageID','left');
        $this->db->where('MemberID',$farmer);
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            return $Q->result_array();
        }

        return array();
    }

    public function postGarden($farmerid, $number, $long = NULL,$lat = NULL) {
        $output = false;

        $insert = array(
            'MemberDisplayID'   => $farmerid,
            'PlotNr'            => $number,
            'Longitude'         => $long,
            'Latitude'          => $lat
        );

        $this->db->insert('ktv_temp_member_plot',$insert);
        
        if(!$this->db->_error_number()){
            $output = $number;
        } elseif($this->db->_error_number() == 1062) {
            $output = -1;
        }

        return $output;
    }
}

?>

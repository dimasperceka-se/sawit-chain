<?php

class Mbuying extends CI_Model {

    function readDatas($userid,$key,$prov,$kab,$start,$limit){
        $sql = "
            select %s
            from ktv_buying_transaction kbt
            left join ktv_traders kt on kt.TraderID=kbt.WarehouseTraderID
            left join ktv_trader_staff kts on kts.TraderID=kt.TraderID
            left join ktv_farmer kcf on kbt.FarmerID=kcf.FarmerID
            left join ktv_village kv on kcf.VillageID=kv.VillageID
            left join ktv_subdistrict ks on ks.SubDistrictID=kv.SubDistrictID
            left join ktv_district kd on kd.DistrictID=ks.DistrictID
            left join ktv_province kp on kp.ProvinceID=kd.ProvinceID
            WHERE (TransactionID like ? OR kcf.FarmerID like ?) %s
            ORDER BY FarmerName %s";
        $sql_cek = "select * from ktv_trader_staff where UserId=?";
        if ($prov!='') $add = "AND kp.ProvinceID like '$prov' ";
        if ($kab!='') $add .= "AND District like '$kab' ";
        $query = $this->db->query($sql_cek, array($userid));
        $cek = $query->result_array();
        if ($cek[0]['UserId']!='') $add .= "AND kts.UserId = $userid ";
        $query = $this->db->query(sprintf($sql,'kbt.WarehouseTraderID,kbt.TransactionID,kbt.FarmerID,FarmerName,
            VolumeNetto,ContractPrice,NetPrice,TotalPayment,CompanyAlias',$add,'LIMIT ?,?'),
            array($key,"%$key%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',$add,''), array($key,"%$key%",));
        $total = $query->result_array();

        if ($total[0]['total'])
        $result['total'] = $total[0]['total'];
        return $result;
    }

    function readDataBatch($id){
        /*$sql = "
            select CONCAT('coop/',kcoop.Photo) AS logo_koperasi, CONCAT('certification_provider/',kcp.Photo) AS logo_sertifikasi,
               ktq.*,ksb.*,ksov_perwakilan.Name perwakilan,kv.Village,ks.SubDistrict,kd.District,IF(ksov_pembeli.SupplychainID=176,ksov_pembeli.Alias,ksov_pembeli.Name) pembeli,
               ksov_pembeli.Address pembeli_alamat,ksov_pembeli.Phone pembeli_telepon,
               IF(kcs.PersonID='0',IF(kcs.FarmerID='0',kcs.StaffName,kcf.FarmerName),persons.PersonNm) koperasi_nama,
               ksov.Address koperasi_alamat, LabelKarung, TraderName pedagang,
               a.Village desa,a.SubDistrict kec,a.District kab,ksov.Name,
               IFNULL(kww.PartnerID,IFNULL(kw.PartnerID,kwwb.PartnerID)) PartnerID,
               kvb.Village Desa,ksd.SubDistrict Kec, kdb.District Kab,ksov.OrgID,
               count(distinct kcf.FarmerName) jumlah_petani,GroupName,
               ksovv.OrgType type_parent,ksovv.Name name_parent,ksov.Address
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_org kso on ksov.SupplychainID=kso.SupplychainID
            left join ktv_village kv on kv.VillageID=ksov.VillageID
            left join ktv_subdistrict ks on ks.SubDistrictID=substr(ksov.VillageID,1,7)
            left join ktv_district kd on kd.DistrictID=substr(ksov.VillageID,1,4)
            left join ktv_supplychain_org_view ksov_pembeli on ksov_pembeli.SupplychainID=ksb.SupplyDestOrgID
            left join ktv_supplychain_org_view ksov_perwakilan on ksov_perwakilan.SupplychainID=ksb.PerwakilanOrgID
            left join ktv_cooperative_staff kcs on kcs.CoopID=ksov.OrgID and ksov.OrgType='Organisasi Petani' and Position='Ketua' AND kcs.Status!='nullified'
            left join ktv_persons persons on kcs.PersonID=persons.PersonID
            left join ktv_traders kt on kt.TraderID=ksov.OrgID and ksov.OrgType='Pedagang'
            left join ktv_farmer kcf on kcs.FarmerID=kcf.FarmerID
            left join ktv_cpg kc on kc.CPGid=kcf.CPGid

            left join ktv_warehouse kw on kw.WarehouseID=ksov.OrgID and ksov.OrgType='Gudang'
            left join ktv_supplychain_org_rel ksor on ksov.SupplychainID=ksor.ChildOrgId
            left join ktv_supplychain_org_view ksovv on ksovv.SupplychainID=ksor.ParentOrgId
            left join ktv_warehouse kww on kww.WarehouseID=ksovv.OrgID
            left join ktv_supplychain_org_rel ksorb on ksovv.SupplychainID=ksorb.ChildOrgId
            left join ktv_supplychain_org_view ksovvb on ksovvb.SupplychainID=ksorb.ParentOrgId
            left join ktv_warehouse kwwb on kwwb.WarehouseID=ksovvb.OrgID

            left join ktv_village kvb ON kvb.VillageID=ksov.VillageID
            left join ktv_subdistrict ksd ON ksd.SubDistrictID=kvb.SubDistrictID
            left join ktv_district kdb ON kdb.DistrictID=ksd.DistrictID

            LEFT JOIN (
      		    SELECT Village,District,SubDistrict,OrgID
      		    FROM ktv_supplychain_org_view ksovp
      		    LEFT JOIN ktv_village kvp ON kvp.VillageID=ksovp.VillageID
      		    LEFT JOIN ktv_subdistrict ksp ON ksp.SubDistrictID=SUBSTR(ksovp.VillageID,1,7)
      		    LEFT JOIN ktv_district kdp ON kdp.DistrictID=SUBSTR(ksovp.VillageID,1,4)
      		    WHERE ksovp.OrgType='Pedagang'
      	   ) a ON a.OrgID=ksb.PerwakilanOrgID

            left join (select * from ktv_supplychain_quality group by QualitySupplychainID order by QualityID desc) ktq on
               ktq.QualitySupplychainID=ksb.SupplyOrgID
            LEFT JOIN ktv_cooperatives kcoop ON kcoop.CoopID=IF(ksov.OrgType='Organisasi Petani', ksov.OrgID, IF(ksovv.OrgType='Organisasi Petani',ksovv.OrgID, ksovvb.OrgID))
            LEFT JOIN ktv_certification_provider_contract kcp ON kcp.ObjType='koperasi' AND kcp.ObjID=kcoop.CoopID AND ksb.SupplyBatchDate BETWEEN kcp.CertificationStart AND kcp.CertificationEnd AND kcp.StatusCode='active'

            WHERE ksb.SupplyBatchID=?";*/
            $sql = "
            SELECT
                ksb.SupplyBatchID,
                ksov_pembeli.`Name` pembeli,
                district.District District,
                CONCAT('coop/',kcoop.Photo) AS logo_koperasi,
                CONCAT('certification_provider/',kcp.Photo) AS logo_sertifikasi,
                IF(ksb.SupplyBatchResponsible IS NULL OR ksb.SupplyBatchResponsible='',ksov.`Name`,ksb.SupplyBatchResponsible) pedagang,
                ksov.`Name` koperasi_nama,
                ksov.`Name`,
                ksov.Address koperasi_alamat,
                ksb.DestDriver,
                ksb.DestDriverJabatan,
                ksb.DestDriverAddress,
                kso.LabelKarung,
                ksb.DestKarungStart,
                ksb.DestKarungEnd,
                kw.PartnerID,
                ksb.DestWeight,
                ksb.VolumeBruto,
                ksb.DeliveryDate,
                ksb.DestICS,
                ksov_perwakilan.`Name` perwakilan,
                ksb.DestTransport,
                ksb.DestNoPolisi,
                ksov_pembeli.Address pembeli_alamat,
                kv.Village,
                subdistrict.SubDistrict,
                coop.PersonNm koperasi_ketua,
                kw.Phone pembeli_telepon,
                kcp.ICSTraceability

            FROM
                ktv_supplychain_batch ksb
                LEFT JOIN ktv_supplychain_org_view ksov ON ksov.SupplychainID = ksb.SupplyOrgID
                LEFT JOIN ktv_supplychain_org_view ksov_pembeli ON ksov_pembeli.SupplychainID = ksb.SupplyDestOrgID
                LEFT JOIN ktv_village kv ON kv.VillageID = ksov.VillageID
                LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID
                LEFT JOIN ktv_cooperatives kcoop ON kcoop.CoopID=ksov.OrgID
                LEFT JOIN ktv_certification_provider_contract kcp ON kcp.ObjType='koperasi' AND kcp.ObjID=kcoop.CoopID AND ksb.SupplyBatchDate BETWEEN kcp.CertificationStart AND kcp.CertificationEnd AND kcp.StatusCode='active'
                LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=ksb.SupplyOrgID
                LEFT JOIN ktv_warehouse kw ON kw.WarehouseID=ksov_pembeli.OrgID
                LEFT JOIN ktv_supplychain_org_view ksov_perwakilan ON ksov_perwakilan.SupplychainID=ksb.PerwakilanOrgID
                LEFT JOIN (
                        SELECT
                                staff.StaffID, staff.PersonID, staff.ObjID, staff.ObjType, kp.PersonNm
                        FROM
                                ktv_staff_positions staff_pos
                                LEFT JOIN ktv_staffs staff ON staff.StaffID=staff_pos.StaffPosStaffID
                                LEFT JOIN ktv_persons kp ON kp.PersonID=staff.PersonID
                        WHERE
                                StaffPosPositionID = 82 AND NOW() BETWEEN staff_pos.StaffPostStart AND staff_pos.StaffPostEnd
                                AND staff_pos.StatusCode = 'active'
                ) coop ON coop.ObjID=ksov.OrgID
            WHERE ksb.SupplyBatchID=?";
        $query = $this->db->query($sql, array($id));
        //echo "<pre>".$this->db->last_query();exit;
        $result = $query->result_array();
        return $result[0];
    }

    function readDataTrans($id){
        $sql = "
            select kst.*,kcf.FarmerID,kcf.FarmerName,kc.CPGid,kc.GroupName,kv.Village,ks.SubDistrict,kd.District,
               date(kst.DateTransaction) tgl,
               ksov.*,count(kstb.SupplyTransID) jumlah_karung,count(distinct IFNULL(kcfb.FarmerID,kcf.FarmerID)) jumlah_petani,
               kcb.GroupName cpg_nama
            from ktv_supplychain_transaction kst
            left join ktv_farmer kcf on kst.SupplyID=kcf.FarmerID and (kst.SupplyType='Farmer' OR kst.SupplyType='FarmerNonCert')
            left join ktv_supplychain_batch ksb on kst.SupplyID=ksb.SupplyBatchNumber and kst.SupplyType='Batch'
            left join ktv_supplychain_transaction kstb on kstb.SupplyBatchID=ksb.SupplyBatchID
            left join ktv_farmer kcfb on kstb.SupplyID=kcfb.FarmerID and (kstb.SupplyType='Farmer' OR kstb.SupplyType='FarmerNonCert')
            left join ktv_cpg kcb on kcb.CPGid=IFNULL(kcfb.CPGid,kcf.CPGid)
            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
            left join ktv_cpg kc on kc.CPGid=kcf.CPGid
            left join ktv_village kv on kv.VillageID=kcf.VillageID
            left join ktv_subdistrict ks on ks.SubDistrictID=kv.SubDistrictID
            left join ktv_district kd on kd.DistrictID=ks.DistrictID
            WHERE kst.SupplyBatchID=?
            GROUP BY kst.SupplyTransID";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result;
    }

    function readData($id){
        $sql = "
            select
               CONCAT('coop/',kcoop.Photo) AS logo_koperasi, CONCAT('certification_provider/',kcp.Photo) AS logo_sertifikasi,
               kst.*, ktq.*,date(DateTransaction) as DateTransaction,kd.District,kc.CPGid,GroupName,
               IFNULL(kcf.FarmerName,IFNULL(ksnf.FarmerName,ksb.SupplyBatchNumber)) farnam,
               IFNULL(concat('[',SupplyID,'] ',kcf.FarmerName),IFNULL(ksb.SupplyBatchNumber,ksnf.FarmerName)) FarmerName,
               IFNULL(kcf.FarmerID,ksnf.FarmerIdentity) FarmerID,
               ksov.Name,IFNULL(kww.PartnerID,IFNULL(kw.PartnerID,kwwb.PartnerID)) PartnerID,
               kvb.Village Desa,ksd.SubDistrict Kec, kdb.District Kab,ksov.OrgID,
               ksovv.OrgType type_parent,ksovv.Name name_parent,ksov.Address
               #GroupName,kt.Photo
            from ktv_supplychain_transaction kst
            left join ktv_supplychain_batch ksb on kst.SupplyBatchID=ksb.SupplyBatchID
            left join ktv_farmer kcf on kst.SupplyID=kcf.FarmerID and (SupplyType='Farmer' OR SupplyType='FarmerNonCert')
            left join ktv_cpg kc on kc.CPGid=kcf.CPGid
            left join ktv_supplychain_non_farmer ksnf on kst.SupplyID=ksnf.FarmerID and SupplyType='NonFarmer'
            left join ktv_district kd on substr(IF(kcf.FarmerID is null,ksnf.FarmerVillageID,kcf.VillageID),1,4)=kd.DistrictID

            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
            left join ktv_warehouse kw on kw.WarehouseID=ksov.OrgID and ksov.OrgType='Gudang'
            left join ktv_supplychain_org_rel ksor on ksov.SupplychainID=ksor.ChildOrgId
            left join ktv_supplychain_org_view ksovv on ksovv.SupplychainID=ksor.ParentOrgId
            left join ktv_warehouse kww on kww.WarehouseID=ksovv.OrgID
            left join ktv_supplychain_org_rel ksorb on ksovv.SupplychainID=ksorb.ChildOrgId
            left join ktv_supplychain_org_view ksovvb on ksovvb.SupplychainID=ksorb.ParentOrgId
            left join ktv_warehouse kwwb on kwwb.WarehouseID=ksovvb.OrgID
            left join ktv_supplychain_org_view ksovbu ON ksovbu.SupplychainID=ksb.SupplyDestOrgID

            left join ktv_village kvb ON kvb.VillageID=ksov.VillageID
            left join ktv_subdistrict ksd ON ksd.SubDistrictID=kvb.SubDistrictID
            left join ktv_district kdb ON kdb.DistrictID=ksd.DistrictID

            #left join ktv_traders kt on kbt.WarehouseTraderID=kt.TraderID
            left join (select * from ktv_supplychain_quality group by QualitySupplychainID order by QualityID desc) ktq on ktq.QualitySupplychainID=ksb.SupplyOrgID
            LEFT JOIN ktv_cooperatives kcoop ON kcoop.CoopID=IF(ksov.OrgType='Organisasi Petani',ksov.OrgID, IF(ksovv.OrgType='Organisasi Petani', ksovv.OrgID,IF(ksovvb.OrgID IS NULL,ksovbu.OrgID,ksovvb.OrgID)))
            LEFT JOIN ktv_certification_provider_contract kcp ON kcp.ObjType='koperasi' AND kcp.ObjID=kcoop.CoopID AND kst.DateTransaction BETWEEN kcp.CertificationStart AND kcp.CertificationEnd AND kcp.StatusCode='active'
            WHERE kst.SupplyTransID=?";
        /*$sql_detail = "
            select *
            from ktv_buying_transaction_detail
            WHERE TransactionID=?";
        $queryd = $this->db->query($sql_detail, array($id));
        $resultd = $query->result_array();*/
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
        //return array($result[0],$resultd[0]);
    }
    function createData($FarmerID,$DateTransaction,$VolumeBruto,$VolumeNetto,$BagType,$BagQty,
            $BeanType,$QualityKA,$QualityBC,$QualityWaste,$QualityMouldy,$QualityInsect,$QualitySlaty,
            $Reward,$ContractPrice,$NetPrice,$TotalPayment,$DpPercent,$StatusCode,$WeightBy,$SupervisorID,
            $AdminID,$VehicleNo,$WarehouseTraderID, $userid){
        $sql = "
            insert into ktv_buying_transaction (FarmerID, DateTransaction, VolumeBruto, VolumeNetto, BagType, BagQty,
            	BeanType, QualityKA, QualityBC, QualityWaste, QualityMouldy, QualityInsect, QualitySlaty,
            	Reward, ContractPrice, NetPrice, TotalPayment, DpPercent, StatusCode, WeightBy, SupervisorID,
            	AdminID, VehicleNo, DateCreated, CreatedBy,WarehouseTraderID)
            VALUES (?,?,?,?,?,?,   ?,?,?,?,?,?,?,   ?,?,?,?,?,?,?,?,   ?,?,now(),?,?)";
        $query = $this->db->query($sql, array($FarmerID,$DateTransaction,$VolumeBruto,$VolumeNetto,$BagType,$BagQty,
            $BeanType,$QualityKA,$QualityBC,$QualityWaste,$QualityMouldy,$QualityInsect,$QualitySlaty,
            $Reward,$ContractPrice,$NetPrice,$TotalPayment,$DpPercent,$StatusCode,$WeightBy,$SupervisorID,
            $AdminID,$VehicleNo,$userid,$WarehouseTraderID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
            $results['id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateData($FarmerID,$DateTransaction,$VolumeBruto,$VolumeNetto,$BagType,$BagQty,
            $BeanType,$QualityKA,$QualityBC,$QualityWaste,$QualityMouldy,$QualityInsect,$QualitySlaty,
            $Reward,$ContractPrice,$NetPrice,$TotalPayment,$DpPercent,$StatusCode,$WeightBy,$SupervisorID,
            $AdminID,$VehicleNo,$WarehouseTraderID,$userid,
            $TRMoisture,$TRBeanCount,$TRMouldy,$TRInsect,$TRSalty,$TRWaste,
         	$ClaimMoisture,$ClaimBeanCount,$ClaimMouldy,$ClaimInsect,$ClaimSalty,$ClaimWaste,
         	$RewardMoisture,$RewardBeanCount,$RewardMouldy,$RewardInsect,$RewardSalty,$RewardWaste,
            $id){
        $sql = "
            update ktv_buying_transaction
            set FarmerID=?,DateTransaction=?,VolumeBruto=?,VolumeNetto=?,BagType=?,BagQty=?,
            	BeanType=?,QualityKA=?,QualityBC=?,QualityWaste=?,QualityMouldy=?,QualityInsect=?,QualitySlaty=?,
            	Reward=?,ContractPrice=?,NetPrice=?,TotalPayment=?,DpPercent=?,StatusCode=?,WeightBy=?,SupervisorID=?,
            	AdminID=?,VehicleNo=?,WarehouseTraderID=?,DateUpdated=now(), LastModifiedBy=?,
            	TRMoisture=?,TRBeanCount=?,TRMouldy=?,TRInsect=?,TRSalty=?,TRWaste=?,
            	ClaimMoisture=?,ClaimBeanCount=?,ClaimMouldy=?,ClaimInsect=?,ClaimSalty=?,ClaimWaste=?,
            	RewardMoisture=?,RewardBeanCount=?,RewardMouldy=?,RewardInsect=?,RewardSalty=?,RewardWaste=?
            where TransactionID=?";
        $query = $this->db->query($sql, array($FarmerID,$DateTransaction,$VolumeBruto,$VolumeNetto,$BagType,$BagQty,
            $BeanType,$QualityKA,$QualityBC,$QualityWaste,$QualityMouldy,$QualityInsect,$QualitySlaty,
            $Reward,$ContractPrice,$NetPrice,$TotalPayment,$DpPercent,$StatusCode,$WeightBy,$SupervisorID,
            $AdminID,$VehicleNo,$WarehouseTraderID,$userid,
            $TRMoisture,$TRBeanCount,$TRMouldy,$TRInsect,$TRSalty,$TRWaste,
         	$ClaimMoisture,$ClaimBeanCount,$ClaimMouldy,$ClaimInsect,$ClaimSalty,$ClaimWaste,
         	$RewardMoisture,$RewardBeanCount,$RewardMouldy,$RewardInsect,$RewardSalty,$RewardWaste,
            $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteData($id){
        $sql = "
            DELETE FROM ktv_buying_transaction WHERE TransactionID=?";
        $sql_detail = "
            DELETE FROM ktv_buying_transaction_detail WHERE TransactionID=?";
        $this->db->trans_start();
        $this->db->query($sql_detail, array($id));
        $this->db->query($sql, array($id));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    function readDataFarmer($id){
        $sql = "
            select FarmerID,FarmerName,GroupName,District
            from ktv_farmer kcf
            left join ktv_cpg kc on kc.CPGid=kcf.CPGid
            LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
     }
    function readDatasDetail($id){
        $sql = "
            select kbtd.*,PackageType,PackageWeight
            from ktv_supplychain_transaction_dtl kbtd
            left join ktv_supplychain_package ktp on kbtd.PackageID=ktp.PackageID
            WHERE SupplyTransID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        $sql_update = "
            UPDATE ktv_supplychain_transaction SET IsCetak='1' WHERE SupplyTransID=?";
        $query = $this->db->query($sql_update, array($id));
        return $result;
    }
    function readDataDetail($id){
        $sql = "
            select kbtd.*,PackageType,PackageWeight,group_concat(kbtd.DetailID,'|',FarmerID,'|',Berat separator '#') detil
            from ktv_supplychain_transaction_dtl kbtd
            left join ktv_supplychain_package ktp on kbtd.PackageID=ktp.PackageID
            left join ktv_supplychain_transaction_dtl_farmer kbtdf on kbtd.DetailID=kbtdf.DetailID
            WHERE FromBatchID=?
            GROUP BY kbtd.DetailID";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }
    function createDataDetail($TransactionID, $PackageID, $Weight, $Moisture){
        $sql = "
            insert into ktv_buying_transaction_detail (TransactionID, PackageID, Weight, Moisture)
            VALUES (?,?,?,?)";
        $query = $this->db->query($sql, array($TransactionID, $PackageID, $Weight, $Moisture));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateDataDetail($TransactionID, $PackageID, $Weight, $Moisture,$id){
        $sql = "
            update ktv_buying_transaction_detail
            set TransactionID=?, PackageID=?, Weight=?, Moisture=?
            where DetailID=?";
        $query = $this->db->query($sql, array($TransactionID, $PackageID, $Weight, $Moisture,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteDataDetail($id){
        $sql_detail = "
            DELETE FROM ktv_buying_transaction_detail WHERE DetailID=?";
        $query = $this->db->query($sql_detail, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    function readDataFf($id){
        $sql = "
            select *
            from ktv_trace_quality ktq
            left join ktv_trader_staff kts ON ktq.QualityTraderID=kts.TraderID
            left join ktv_trace_price ktp ON ktp.PriceTraderID=kts.TraderID
            where UserId=?
            order by QualityDate desc,PriceDate desc
            limit 1";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
     }
    function readDataPackage($id){
        $sql = "
            select concat(PackageID,'-',PackageWeight) as id,PackageType as label
            from ktv_trace_package ktp
            left join ktv_trader_staff kts ON ktp.PackageTraderID=kts.TraderID
            where UserId=?
            order by PackageType asc";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result;
     }

    function readDataKoperasi($id){
        $sql = "
            select *
            from ktv_cooperatives
            where CoopId=(select)";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result;
     }

	function readDataTransaksi($id){
		$sql = "
			SELECT
                b.SupplyType,
                IF(b.SupplyType!='Batch',a.DestPO,d.DestPO) AS nopo,
                IF(b.SupplyType!='Batch',
                        CONCAT('[',c.FarmerID,'] ',c.FarmerName),
                        CONCAT('[',f.FarmerID,'] ',f.FarmerName)
                ) AS label,
                IF(b.SupplyType!='Batch',
                        '',
                        CONCAT('[',g.OrgType,'] ',g.`Name`)
                ) AS unit,
                SUM(IF(IFNULL(e.FAQVolumeBruto,b.FAQVolumeBruto)=0.00,IFNULL(e.FFVolumeBruto,b.FFVolumeBruto),IFNULL(e.FAQVolumeBruto,b.FAQVolumeBruto))) berat
            FROM
                ktv_supplychain_batch a
                LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
                LEFT JOIN ktv_farmer c ON c.FarmerID=b.SupplyID
                LEFT JOIN ktv_supplychain_batch d ON d.SupplyBatchNumber=b.SupplyID
                LEFT JOIN ktv_supplychain_transaction e ON e.SupplyBatchID=d.SupplyBatchID
                LEFT JOIN ktv_farmer_view f ON f.FarmerID=e.SupplyID
                LEFT JOIN ktv_supplychain_org_view g ON d.SupplyOrgID=g.SupplychainID
            WHERE
                a.SupplyBatchID = ?
            GROUP BY IFNULL(e.SupplyID,b.SupplyID)
		";
		$query = $this->db->query($sql, array($id));
        return $query;
	}

	function getTransaksi($SupplyBatchID){
		$sql = "SELECT SupplyTransID FROM ktv_supplychain_transaction WHERE SupplyBatchID=?";
		$query = $this->db->query($sql, array($SupplyBatchID));
        return $query;
	}

	function readDatasQuality($id){
        $sql = "SELECT b.`Name`, a.*
				FROM
					ktv_supplychain_transaction_quality a
					LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.DetailID=b.DetailID
				WHERE a.SupplyTransID=?";
        $query = $this->db->query($sql, array($id));
        return $query;
    }


}
?>

<?php

class Mtransaction extends CI_Model { 

    function readDatas($userid,$key,$tgl,$start,$limit){
        if ($tgl=='') $tgl = '%%';
		$tgl = substr($tgl,0,10);
        $sql = "
            select %s
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_staff kss on kss.StaffSupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_org_view kso on ksb.SupplyOrgID=kso.SupplychainID
            left join ktv_supplychain_org_view ksod on ksb.SupplyDestOrgID=ksod.SupplychainID
            LEFT JOIN ktv_supplychain_org c ON c.SupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
            LEFT JOIN ktv_supplychain_transaction_dtl kstd ON kstd.SupplyTransID=kst.SupplyTransID
            WHERE ((SupplyBatchNumber like ? OR DestPO like ?) and SupplyBatchDate like ?) %s
            ORDER BY SupplyBatchDate desc %s";
//               and IF(SupplyDestOrgID=50008 OR SupplyOrgID=50008,true,false)

        $sql_cek = "select UserId,ChildOrgId,StaffSupplychainID
            from ktv_supplychain_staff
            left join ktv_supplychain_org_rel on StaffSupplychainID=ParentOrgId
            where UserId=?";
        $query = $this->db->query($sql_cek, array($userid));
        $cek = $query->result_array();
        $sql_district = "
         select group_concat(DistrictID) district
         from ktv_private_staff kps
         left join ktv_district_partner kdp on kps.PartnerID=kdp.PartnerID
         where UserId=?";
        $query = $this->db->query($sql_district, array($_SESSION['userid']));
        $result = $query->result_array();
        $add = '';
        $addf = '';
        if ($result[0]['district']!='') $add .= 'and (substr(kso.VillageID,1,4) in ('.$result[0]['district'].') OR kss.UserId='.$userid.')';
        elseif (!empty($cek) and $cek[0]['ChildOrgId']=='') $add .= "and kss.UserId=$userid";
        elseif ($cek[0]['ChildOrgId']!='') $add .= "and (kss.UserId=$userid OR SupplyOrgID in (select ChildOrgId from
            ktv_supplychain_org_rel where ParentOrgId=".$cek[0]['StaffSupplychainID']."))";
        if ($cek[0]['StaffSupplychainID']=='47') {
            $addf = "IF(SupplyDestOrgID=47 AND SupplyOrgID!=47,'2','1') no_edit,";
            $add .= ' and IF(SupplyDestOrgID=47 OR SupplyOrgID=47,true,false)';
        }
		//**//
		if ($cek[0]['StaffSupplychainID']!='') {
			//$add .= " AND (ksod.SupplychainID=".$cek[0]['StaffSupplychainID']." OR ksod.SupplychainID IS NULL) ";
			$add .= " AND ksb.SupplyOrgID=".$cek[0]['StaffSupplychainID']." ";
		}
		//**//
        $quer = $this->db->query(sprintf($sql,$addf.'ksb.SupplyBatchID,ksb.SupplyBatchNumber,ksb.DestPO,kso.Name,ksb.SupplyDestStatus,
            (CASE SupplyType
			WHEN "Farmer" THEN IF(c.LabelFarmerCertified IS NULL OR c.LabelFarmerCertified="",SupplyType,c.LabelFarmerCertified)
			WHEN "NonFarmer" THEN IF(c.LabelNonFarmer IS NULL OR c.LabelNonFarmer="",SupplyType,c.LabelNonFarmer)
			WHEN "FarmerNonCert" THEN IF(c.LabelFarmerNonCertified IS NULL OR c.LabelFarmerNonCertified="",SupplyType,c.LabelFarmerNonCertified)
			ELSE SupplyType END) AS jenis,
            ksod.Name NameDest,SupplyBatchDate,VolumeBruto,VolumeNetto,kso.PembelianNonFarmer,kso.MekanismeReward,ksb.DestWeight, SUM(kstd.Tandan) Tandan, SUM(kstd.Brondol) Brondol',$add.' GROUP BY ksb.SupplyBatchID ','LIMIT ?,?'),
            array("%$key%","%$key%",$tgl,(int)$start,(int)$limit));
        $result['data'] = $quer->result_array();
        //printf($sql,'count(distinct ksb.SupplyBatchID) as total',$add,'');print_r(array("%$key%","%$key%",$tgl));exit;
        $query = $this->db->query(sprintf($sql,'count(distinct ksb.SupplyBatchID) as total',$add,''), array("%$key%","%$key%",$tgl));
        $total = $query->result_array();

        if ($total[0]['total'])
        $result['total'] = $total[0]['total'];
        return $result;
    }
    function readDatasDetail($id){
        $sql = "
            SELECT
				kst.SupplyBatchID,
				SupplyTransID,
				(CASE SupplyType
				WHEN 'Farmer' THEN IF(kso.LabelFarmerCertified IS NULL OR kso.LabelFarmerCertified='',SupplyType,kso.LabelFarmerCertified)
				WHEN 'NonFarmer' THEN IF(kso.LabelNonFarmer IS NULL OR kso.LabelNonFarmer='',SupplyType,kso.LabelNonFarmer)
				WHEN 'FarmerNonCert' THEN IF(kso.LabelFarmerNonCertified IS NULL OR kso.LabelFarmerNonCertified='',SupplyType,kso.LabelFarmerNonCertified)
				ELSE SupplyType END) AS SupplyTypeLabel,
				SupplyType,
				CONCAT(
					IFNULL(concat('[',SupplyID,'] ',kcf.FarmerName),IFNULL(IF(po.SupplyBatchNumber IS NULL,ksb.SupplyBatchNumber,po.SupplyBatchNumber),ksnf.FarmerName)),
					IF(po.SupplyOrgID IS NULL OR po.SupplyOrgID='','',' | '),
					IF(po.SupplyOrgID IS NULL OR po.SupplyOrgID='','',poname.`Name`),
					IF(po.DestPO IS NULL OR po.DestPO='','',' | '),
					IF(po.DestPO IS NULL OR po.DestPO='','',po.DestPO)
				) `Name`,
				date(DateTransaction) DateTransaction,
				(IFNULL(kst.FFVolumeBruto,0) + IFNULL(kst.FAQVolumeBruto,0)) VolumeBruto,
				(IFNULL(kst.FFVolumeNetto,0) + IFNULL(kst.FAQVolumeNetto,0)) VolumeNetto,
				(FFNetPrice + FAQNetPrice) NetPrice,
				kst.FFVolumeBruto,
				kst.FAQVolumeBruto,
				kst.FFVolumeNetto,
				kst.FAQVolumeNetto,

				IF(po.DestJumlahKarung!=0,
						(SELECT CONCAT('Karung : ',po.DestJumlahKarung,IF(COUNT(*)=po.DestJumlahKarung,' | Detail','')) AS karung FROM ktv_supplychain_transaction_dtl WHERE FromBatchID=po.SupplyBatchID),
						(SELECT CONCAT('Berat : ',MAX(Weight)) AS karung FROM ktv_supplychain_transaction_dtl WHERE FromBatchID=po.SupplyBatchID)
				) AS packing
			FROM
				ktv_supplychain_transaction kst
			LEFT JOIN ktv_supplychain_batch ksb ON kst.SupplyBatchID = ksb.SupplyBatchID
			LEFT JOIN ktv_farmer kcf ON kst.SupplyID = kcf.FarmerID
			LEFT JOIN ktv_supplychain_org kso ON kso.SupplychainID=ksb.SupplyOrgID
			AND (
				SupplyType = 'Farmer'
				OR SupplyType = 'FarmerNonCert'
			)
			LEFT JOIN ktv_supplychain_non_farmer ksnf ON kst.SupplyID = ksnf.FarmerID
			AND SupplyType = 'NonFarmer'
			LEFT JOIN ktv_supplychain_batch po ON kst.SupplyID=po.SupplyBatchNumber
			LEFT JOIN ktv_supplychain_org_view poname ON po.SupplyOrgID=poname.SupplychainID
            WHERE kst.SupplyBatchID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        //echo $id;print_r($result);exit;
        return $result;
    }

   function karung($i,$sisa=0,$trans,$batch,$cpg='',$cpg_berat='') {
      $berat = ($trans[$i]['FAQVolumeBruto']/$batch['VolumeBruto']*$batch['DestWeight']) + $sisa;
      $data['cpg'] = $cpg;
      $data['cpg_berat'] = $cpg_berat;
      if ($berat<62.5 and $trans[$i+1]['FAQVolumeBruto']>0) {
         $data['cpg'][] = $trans[$i]['SupplyID'];
         $data['cpg_berat'][] = $berat-$sisa;
         return $this->karung($i+1,$berat,$trans,$batch,$data['cpg'],$data['cpg_berat']);
      } else {
         $data['cpg'][] = $trans[$i]['SupplyID'];
         $data['cpg_berat'][] = 62.5-$sisa;
         if ($berat>62.5) {
            $data['cpg_a'][0] = $trans[$i]['SupplyID'];
            $data['cpg_berat_a'][0] = $berat-62.5;
         }
         $data['i'] = $i+1;
         $data['sisa'] = $berat-62.5;
      }
      return $data;
   }
    function readDataBatch($id,$orgId,$status){
         if ($status!='%%') $add = ' and SupplyDestStatus in (%s)';
        $sql = "
            select ksb.SupplyBatchID,SupplyBatchDate,ksb.VolumeBruto,ksb.VolumeNetto,a.Name name,SupplyDestStatus,
               SupplyBatchNumber,   DestWeight,PackageCapasity,round(DestWeight/PackageCapasity) karung,PackageID,
               DestWeight-(FLOOR(DestWeight/PackageCapasity)*PackageCapasity) sisa,PackageWeight,kst.SupplyBatchID frombatchid,
               IFNULL(kww.PartnerID,kw.PartnerID) PartnerID
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_org kso on ksb.SupplyOrgID=kso.SupplychainID
            left join ktv_supplychain_package ksp on ksp.PackageSupplychainID=SupplyDestOrgID
            left join ktv_supplychain_transaction kst on kst.SupplyBatchID=ksb.SupplyBatchID and SupplyType='Farmer'
            left join (
               select 'warehouse' Type,WarehouseID OrgID,WarehouseName Name
               from ktv_warehouse
               UNION ALL
               select 'trader' Type,TraderID OrgID,TraderName Name
               from ktv_traders
               UNION ALL
               select 'sce' Type,SceID OrgID,concat('[',kcf.FarmerID,'] ',FarmerName) Name
               from sce_farmer sf
               left join ktv_farmer kcf on sf.FarmerID=kcf.FarmerID
               UNION ALL
               select 'koperasi' Type, CoopID OrgID,CoopName Name
               from ktv_cooperatives
               UNION ALL
               select 'cpg' Type, CPGid OrgID,GroupName Name
               from ktv_cpg
            ) a on OrgType=Type COLLATE utf8_unicode_ci and kso.OrgID=a.OrgID

            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
            left join ktv_warehouse kw on kw.WarehouseID=ksov.OrgID and ksov.OrgType='Gudang'
            left join ktv_supplychain_org_rel ksor on ksov.SupplychainID=ksor.ChildOrgId
            left join ktv_supplychain_org_view ksovv on ksovv.SupplychainID=ksor.ParentOrgId
            left join ktv_warehouse kww on kww.WarehouseID=ksovv.OrgID
            left join ktv_supplychain_transaction kstb on kstb.SupplyID=ksb.SupplyBatchNumber and kstb.SupplyType='Batch'

            WHERE ksb.SupplyBatchNumber=? and kstb.SupplyID is null $add";
         //printf($sql,$status);exit;
        $query = $this->db->query(sprintf($sql,$status), array($id));
        $result = $query->result_array();
        //echo $id.','.$orgId;
        //print_r($result);exit;
        //karung
        $sql_karung = "
         SELECT * FROM ktv_supplychain_transaction WHERE SupplyBatchID=?";
        $query_karung = $this->db->query($sql_karung, array($result[0]['SupplyBatchID']));
        $result_karung = $query_karung->result_array();
        $a = 0;
        for ($i=0;$i<$result[0]['karung'];$i++){
            $data = $this->karung($a,$data['sisa'],$result_karung,$result[0],$data['cpg_a'],$data['cpg_berat_a']);
            $FarmerID[] = $data['cpg'];
            $berat[] = $data['cpg_berat'];
            $a = $data['i'];
        }
        for ($i=0;$i<sizeof($FarmerID);$i++){
            $farmer[] = implode('|',$FarmerID[$i]);
            $ber[] = implode('|',$berat[$i]);
        }

        $result[0]['FarmerID'] = implode('#',$farmer);
        $result[0]['berat'] = implode('#',$ber);
//         print_r($FarmerID);print_r($berat);exit;
        return $result[0];
    }
    function readDataAddBatch($userid,$orgid=''){
        if ($orgid=='') $wh = "UserId=$userid";
        else $wh = "ksov.SupplychainID=$orgid";
        $sql = "
            select *, IF(LabelFAQ!='' AND LabelFAQ IS NOT NULL,LabelFAQ,'FAQ') LabelFAQ, IF(LabelFF!='' AND LabelFF IS NOT NULL,LabelFF,'FF') LabelFF
            from ktv_supplychain_org_view ksov
            left join ktv_supplychain_staff on ksov.SupplychainID=StaffSupplychainID
            left join ktv_supplychain_org kso on kso.SupplychainID=StaffSupplychainID
            left join ktv_supplychain_org_partner ksop on ksop.SupplychainID=ksov.SupplychainID
            where $wh";
            //echo $sql;exit;
        $query = $this->db->query($sql, array());
        $result = $query->result_array();
        if ($result[0]['PembelianBatch']=='1') $return['hidden'] = '0';
        else $return['hidden'] = '1';
        $return['setting'] = $result[0];
        return $return;
    }
    function deleteDataDetail($id){
        $sql = "
            DELETE FROM ktv_supplychain_transaction WHERE SupplyTransID=?";
        $sql_dtl = "
            DELETE FROM ktv_supplychain_transaction_dtl WHERE SupplyTransID=?";
        $sql_quality = "
            DELETE FROM ktv_supplychain_transaction_quality WHERE SupplyTransID=?";
		$sql_cek = "SELECT SupplyType,SupplyID FROM ktv_supplychain_transaction WHERE SupplyTransID=?";

		$check = $this->db->query($sql_cek, array($id))->row();
		$this->db->trans_start();
        if($check->SupplyType=='Batch'){
			$batchNumber = $check->SupplyID;
			$sql_update = "UPDATE ktv_supplychain_batch SET SupplyDestStatus='Sent' WHERE SupplyBatchNumber=?";
			$this->db->query($sql_update, array($batchNumber));
		}
		$this->db->query($sql_quality, array($id));
        $this->db->query($sql_dtl, array($id));
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
    function readBatchSent($id,$userid,$start,$limit){
        $sql = "
            select SupplyBatchNumber id,CONCAT('[',SupplyBatchNumber,'] ',IF(DestPO!='',a.DestPO,c.`Name`))  label, a.DestPO, c.Name AS nama,
			a.DestWeight VolumeNetto, SUM(d.FAQTotalPayment) + SUM(FFTotalPayment) AS TotalPayment
            from ktv_supplychain_batch a
			LEFT JOIN ktv_supplychain_staff ON SupplyDestOrgID = StaffSupplychainID
			LEFT JOIN ktv_supplychain_org_view c ON a.SupplyOrgID=c.SupplychainID
			LEFT JOIN ktv_supplychain_transaction d ON a.SupplyBatchID=d.SupplyBatchID
            where SupplyDestStatus='Sent' and UserId=? and (SupplyBatchNumber like ? OR a.DestPO LIKE ? OR c.`Name` LIKE ?) GROUP BY a.SupplyBatchID LIMIT ?,?";
        $query = $this->db->query($sql, array($userid,"%$id%","%$id%","%$id%",intval($start),intval($limit)));
        $result = $query->result_array();
        return $result;
    }
    function readDatasFarmer($id,$frombatchid='',$tipe=''){
        if ($id!='' OR $frombatchid!='') {
            if($tipe==""){
                $sql = "select DetailID,PackageType,IFNULL(Weight,NettoDelivery) Weight, IFNULL(Weight,NettoDelivery) defaultWeight,IFNULL(WeightPackage,PackageWeight) PackageWeight,ksp.PackageID,Type,MoistureStandard,Moisture,Netto
                        from ktv_supplychain_transaction_dtl kstd
                        left join ktv_supplychain_package ksp on kstd.PackageID=ksp.PackageID
                        WHERE SupplyTransID=? /*OR (FromBatchID=? AND SupplyTransID IS NULL)*/
                        ORDER BY DetailID";
                $query = $this->db->query($sql, array($id,$frombatchid));
            }else{
                $sql = "SELECT b.DetailID, c.PackageType, IFNULL(Weight,NettoDelivery) Weight, IFNULL(Weight,NettoDelivery) defaultWeight,IFNULL(WeightPackage,PackageWeight) PackageWeight,c.PackageID,Type,MoistureStandard,Moisture,IFNULL(Weight,NettoDelivery) Netto
                        FROM ktv_supplychain_transaction_dtl b
                        LEFT JOIN ktv_supplychain_transaction a ON b.SupplyTransID=a.SupplyTransID
                        LEFT JOIN ktv_supplychain_package c on c.PackageID=b.PackageID
                        WHERE b.FromBatchID=?";
                $query = $this->db->query($sql, array($frombatchid));
            }
            $result['data'] = $query->result_array();
        }else{
            $result['data'] = '';
	}
        return $result;
    }
    function readPackages($id){
        $sql = "
            select concat(PackageID,'-',PackageWeight) as id,PackageType as label
            from ktv_supplychain_package ktp
            #left join ktv_supplychain_staff kts ON ktp.PackageSupplychainID=kts.StaffSupplychainID
            where PackageSupplychainID=?
            order by PackageType asc";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result;
    }
//transaction
    function readDataFf($id){
        $sql = "
            SELECT *
            FROM ktv_supplychain_staff kss
            LEFT JOIN ktv_supplychain_price ksp ON kss.StaffSupplychainID=ksp.PriceSupplychainID AND
               (NOW() BETWEEN PriceDateStart AND PriceDateEnd)
            LEFT JOIN ktv_supplychain_quality ksq ON ksp.PriceSupplychainID=ksq.QualitySupplychainID AND
		         (NOW() BETWEEN QualityDateStart AND QualityDateEnd)
            WHERE kss.StaffSupplychainID=? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
     }
    function readDataReward($id){
        $sql = "
            select *
            from ktv_supplychain_reward
            where RewardSupplychainID=?
            order by RewardDate desc
            limit 1";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
     }
    function createNonFarmer($FarmerName, $FarmerIdentity, $FarmerVillageID, $FarmerBirthdate, $FarmerDescription){
        $sql = "
            insert into ktv_supplychain_non_farmer(FarmerName, FarmerIdentity, FarmerVillageID, FarmerBirthdate,
               FarmerDescription, CreatedBy, DateCreated)
            values (?,?,?,?,   ?,".$_SESSION['userid'].",now())";
        $query = $this->db->query($sql, array($FarmerName, $FarmerIdentity, $FarmerVillageID, $FarmerBirthdate, $FarmerDescription));
        return $this->db->insert_id();
     }
    function updateNonFarmer($FarmerName, $FarmerIdentity, $FarmerVillageID, $FarmerBirthdate, $FarmerDescription,$id){
        $sql = "
            update ktv_supplychain_non_farmer set FarmerName=?, FarmerIdentity=?, FarmerVillageID=?, FarmerBirthdate=?,
               FarmerDescription=?, LastModifiedBy=".$_SESSION['userid'].", DateUpdated=now()
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($FarmerName, $FarmerIdentity, $FarmerVillageID, $FarmerBirthdate, $FarmerDescription,$id));
        return $query;
     }
    function createTransaction($SupplyBatchID, $DateTransaction, $SupplyType, $SupplyID,
         $FFVolumeBruto, $FFVolumePackageTmp,$FFVolumeMoistureTmp,$FFVolumeNetto,
         $FAQVolumeBruto, $FFVolumePackage,$FFVolumeMoisture,$FAQVolumeNetto,
         $FFRewardBruto,$FFRewardBonus,$FFReward, $FFContractPrice, $FFNetPrice, $FFTotalPayment,
         $FAQRewardBruto,$FAQRewardBonus,$FAQReward, $FAQContractPrice, $FAQNetPrice, $FAQTotalPayment, $DpPercent, $StatusCode,
      	$WeightBy, $SupervisorID, $AdminID, $VehicleNo,
      	$userid,$FakturNumber,$FFBeratBersihSetara,$FFVerifikasi,$FAQBeratBersihSetara,$FAQVerifikasi,
         $standardStandardNamaTmp,$detail,$standardStandardNama,$resultStandardNama,$rewardStandardNama,$frombatchid){
           $FFReward = $FFReward == ''?NULL:$FFReward;
           $FAQReward = $FAQReward == ''?NULL:$FAQReward;
           $FAQContractPrice = $FAQContractPrice == ''?NULL:$FAQContractPrice;
           $FFContractPrice = $FFContractPrice == ''?NULL:$FFContractPrice;
           $DpPercent = $DpPercent == ''?NULL:$DpPercent;

           $SupplyBatchID = $SupplyBatchID == ''?NULL:$SupplyBatchID;
           $DateTransaction = $DateTransaction == ''?NULL:$DateTransaction;
           $SupplyType = $SupplyType == ''?NULL:$SupplyType;
           $SupplyID = $SupplyID == ''?NULL:$SupplyID;
           $FFVolumeBruto = $FFVolumeBruto == ''?NULL:$FFVolumeBruto;
           $FFVolumePackageTmp = $FFVolumePackageTmp == ''?NULL:$FFVolumePackageTmp;
           $FFVolumeMoistureTmp = $FFVolumeMoistureTmp == ''?NULL:$FFVolumeMoistureTmp;
           $FFVolumeNetto = $FFVolumeNetto == ''?NULL:$FFVolumeNetto;
           $FAQVolumeBruto = $FAQVolumeBruto == ''?NULL:$FAQVolumeBruto;
           $FFVolumePackage = $FFVolumePackage == ''?NULL:$FFVolumePackage;
           $FFVolumeMoisture = $FFVolumeMoisture == ''?NULL:$FFVolumeMoisture;
           $FAQVolumeNetto = $FAQVolumeNetto == ''?NULL:$FAQVolumeNetto;
           $FFRewardBruto = $FFRewardBruto == ''?NULL:$FFRewardBruto;
           $FFRewardBonus = $FFRewardBonus == ''?NULL:$FFRewardBonus;
           $FFReward = $FFReward == ''?NULL:$FFReward;
           $FFContractPrice = $FFContractPrice == ''?NULL:$FFContractPrice;
           $FFNetPrice = $FFNetPrice == ''?NULL:$FFNetPrice;
           $FFTotalPayment = $FFTotalPayment == ''?NULL:$FFTotalPayment;
           $FAQRewardBruto = $FAQRewardBruto == ''?NULL:$FAQRewardBruto;
           $FAQRewardBonus = $FAQRewardBonus == ''?NULL:$FAQRewardBonus;
           $FAQReward = $FAQReward == ''?NULL:$FAQReward;
           $FAQContractPrice = $FAQContractPrice == ''?NULL:$FAQContractPrice;
           $FAQNetPrice = $FAQNetPrice == ''?NULL:$FAQNetPrice;
           $FAQTotalPayment = $FAQTotalPayment == ''?NULL:$FAQTotalPayment;
           $DpPercent = $DpPercent == ''?NULL:$DpPercent;
           $StatusCode = $StatusCode == ''?NULL:$StatusCode;
           $WeightBy = $WeightBy == ''?NULL:$WeightBy;
           $SupervisorID = $SupervisorID == ''?NULL:$SupervisorID;
           $AdminID = $AdminID == ''?NULL:$AdminID;
           $VehicleNo = $VehicleNo == ''?NULL:$VehicleNo;
           $userid = $userid == ''?NULL:$userid;
           $FakturNumber = $FakturNumber == ''?NULL:$FakturNumber;
           $FFBeratBersihSetara = $FFBeratBersihSetara == ''?NULL:$FFBeratBersihSetara;
           $FFVerifikasi = $FFVerifikasi == ''?NULL:$FFVerifikasi;
           $FAQBeratBersihSetara = $FAQBeratBersihSetara == ''?NULL:$FAQBeratBersihSetara;
           $FAQVerifikasi = $FAQVerifikasi == ''?NULL:$FAQVerifikasi;
           $standardStandardNamaTmp = $standardStandardNamaTmp == ''?NULL:$standardStandardNamaTmp;
           $detail = $detail == ''?NULL:$detail;
           $standardStandardNama = $standardStandardNama == ''?NULL:$standardStandardNama;
           $resultStandardNama = $resultStandardNama == ''?NULL:$resultStandardNama;
           $rewardStandardNama = $rewardStandardNama == ''?NULL:$rewardStandardNama;

        if ($SupplyType=='Batch') {
            $sql = "update ktv_supplychain_batch set SupplyDestStatus='Delivered' where SupplyBatchNumber=?";
            $query = $this->db->query($sql, array($SupplyID));

            $DestPO = $this->db->query("SELECT DestPO FROM ktv_supplychain_batch WHERE SupplyBatchNumber=?", array($SupplyID))->row()->DestPO;
            $sql = "UPDATE ktv_supplychain_batch set DestPO=? WHERE SupplyBatchID=?";
            $query = $this->db->query($sql, array($DestPO, $SupplyBatchID));
        }
        
        $OrgID = $this->db->query("SELECT SupplyOrgID FROM ktv_supplychain_batch WHERE SupplyBatchID=?",array($SupplyBatchID))->row()->SupplyOrgID;
        $InvoiceNumber = getInvoiceNumber($OrgID);
        $sql_quota = "SELECT IF(b.IsQuota='0','0','1') AS IsQuota FROM ktv_supplychain_batch a LEFT JOIN ktv_supplychain_org b ON a.SupplyDestOrgID=b.SupplychainID WHERE a.SupplyBatchID=?";
        $quota = $this->db->query($sql_quota,array($SupplyBatchID))->row()->IsQuota;
        if($quota=='0'){
            $IsQuota = '0';    
        }else{
            $IsQuota = '1';    
        }
        
        $sql = "
            insert into ktv_supplychain_transaction (SupplyBatchID, DateTransaction, SupplyType, SupplyID,
               FFVolumeBruto, FFVolumePackage,FFVolumeMoisture,FFVolumeNetto,
               FAQVolumeBruto, FAQVolumePackage,FAQVolumeMoisture,FAQVolumeNetto,
               FFRewardBruto,FFRewardBonus,FFReward, FFContractPrice, FFNetPrice, FFTotalPayment,
               FAQRewardBruto,FAQRewardBonus,FAQReward, FAQContractPrice, FAQNetPrice, FAQTotalPayment, Dp, StatusCode,
            	WeightBy, SupervisorID, AdminID, VehicleNo,DateCreated, CreatedBy,
               FakturNumber,InvoiceNumber,FFBeratBersihSetara,FFVerifikasi,FAQBeratBersihSetara,FAQVerifikasi,IsQuota)
            VALUES (?,?,?,?,   ?,?,?,?,   ?,?,?,?,   ?,?,?,?,?,?,   ?,?,?,?,?,?,?,?,
               ?,?,?,?,now(),?,   ?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($SupplyBatchID, $DateTransaction, $SupplyType, $SupplyID,
            $FFVolumeBruto, $FFVolumePackage,$FFVolumeMoisture,$FFVolumeNetto,
            $FAQVolumeBruto, $FFVolumePackage,$FFVolumeMoisture,$FAQVolumeNetto,
            $FFRewardBruto,$FFRewardBonus,$FFReward,$FFContractPrice, $FFNetPrice, $FFTotalPayment,
            $FAQRewardBruto,$FAQRewardBonus,$FAQReward,$FAQContractPrice, $FAQNetPrice, $FAQTotalPayment, $DpPercent, $StatusCode,
            $WeightBy, $SupervisorID, $AdminID, $VehicleNo,$userid,
            $FakturNumber,$InvoiceNumber,$FFBeratBersihSetara,$FFVerifikasi,$FAQBeratBersihSetara,$FAQVerifikasi,$IsQuota));
        $SupplyTransID = $this->db->insert_id();
        //echo "batch : ".$frombatchid;exit;
        if($frombatchid!=""){
            $sql = "
            INSERT INTO ktv_supplychain_transaction_dtl (SupplyTransID, PackageID, Type, Weight, WeightPackage,
               MoistureStandard,Moisture,NettoDelivery,Netto,DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,?,?,?,now(),?)";
            $dtl = $this->readDatasFarmer('',$frombatchid,'batch');
            $weight = $FFVolumeBruto + $FAQVolumeBruto;
            $netto = $FFVolumeNetto + $FAQVolumeNetto;
            if($dtl['data'][0]['DetailID'] != ""){
                $trans_dtl = $this->db->query($sql, array($SupplyTransID, $dtl['data'][0]['PackageID'], $dtl['data'][0]['Type'], $weight, NULL, NULL, NULL, $netto, $netto, $_SESSION['userid']));
            }
        }
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
            $results['id'] = $SupplyTransID;
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateTransaction($SupplyBatchID,$DateTransaction,$SupplyType,$SupplyID,
         $FFVolumeBruto, $FFVolumePackage,$FFVolumeMoisture,$FFVolumeNetto,
         $FAQVolumeBruto, $FAQVolumePackage,$FAQVolumeMoisture,$FAQVolumeNetto,
         $FFRewardBruto,$FFRewardBonus,$FFReward, $FFContractPrice, $FFNetPrice, $FFTotalPayment,
         $FAQRewardBruto,$FAQRewardBonus,$FAQReward, $FAQContractPrice, $FAQNetPrice, $FAQTotalPayment, $DpPercent, $StatusCode,
      	$WeightBy, $SupervisorID, $AdminID, $VehicleNo, $userid,$id,
         $FakturNumber,$FFBeratBersihSetara,$FFVerifikasi,$FAQBeratBersihSetara,$FAQVerifikasi,
         $standardStandardNamaTmp,$detail,$standardStandardNama,$resultStandardNama,$rewardStandardNama,
		$QDetailID,$QStandardID,
		$QFAQResult,$QFAQStandard,$QFAQReward,
		$QFFResult,$QFFStandard,$QFFReward){

      $SupplyBatchID = $SupplyBatchID == ''?NULL:$SupplyBatchID;
      $DateTransaction = $DateTransaction == ''?NULL:$DateTransaction;
      $SupplyType = $SupplyType == ''?NULL:$SupplyType;
      $SupplyID = $SupplyID == ''?NULL:$SupplyID;
      $FFVolumeBruto = $FFVolumeBruto == ''?NULL:$FFVolumeBruto;
      $FFVolumePackage = $FFVolumePackage == ''?NULL:$FFVolumePackage;
      $FFVolumeMoisture = $FFVolumeMoisture == ''?NULL:$FFVolumeMoisture;
      $FFVolumeNetto = $FFVolumeNetto == ''?NULL:$FFVolumeNetto;
      $FAQVolumeBruto = $FAQVolumeBruto = ''?NULL:$FAQVolumeBruto;
      $FAQVolumePackage = $FAQVolumePackage == ''?NULL:$FAQVolumePackage;
      $FAQVolumeMoisture = $FAQVolumeMoisture == ''?NULL:$FAQVolumeMoisture;
      $FAQVolumeNetto = $FAQVolumeNetto == ''?NULL:$FAQVolumeNetto;
      $FFRewardBruto = $FFRewardBruto == ''?NULL:$FFRewardBruto;
      $FFRewardBonus = $FFRewardBonus == ''?NULL:$FFRewardBonus;
      $FFReward = $FFReward == ''?NULL:$FFReward;
      $FFContractPrice = $FFContractPrice == ''?NULL:$FFContractPrice;
      $FFNetPrice = $FFNetPrice == ''?NULL:$FFNetPrice;
      $FFTotalPayment = $FFTotalPayment == ''?NULL:$FFTotalPayment;
      $FAQRewardBruto = $FAQRewardBruto == ''?NULL:$FAQRewardBruto;
      $FAQRewardBonus = $FAQRewardBonus == ''?NULL:$FAQRewardBonus;
      $FAQReward = $FAQReward == ''?NULL:$FAQReward;
      $FAQContractPrice = $FAQContractPrice == ''?NULL:$FAQContractPrice;
      $FAQNetPrice = $FAQNetPrice == ''?NULL:$FAQNetPrice;
      $FAQTotalPayment = $FAQTotalPayment == ''?NULL:$FAQTotalPayment;
      $DpPercent = $DpPercent == ''?NULL:$DpPercent;
      $StatusCode = $StatusCode == ''?NULL:$StatusCode;
      $WeightBy = $WeightBy == ''?NULL:$WeightBy;
      $SupervisorID = $SupervisorID == ''?NULL:$SupervisorID;
      $AdminID = $AdminID == ''?NULL:$AdminID;
      $VehicleNo = $VehicleNo == ''?NULL:$VehicleNo;
      $userid = $userid == ''?NULL:$userid;
      $id = $id == ''?NULL:$id;
      $FakturNumber = $FakturNumber == ''?NULL:$FakturNumber;
      $FFBeratBersihSetara = $FFBeratBersihSetara == ''?NULL:$FFBeratBersihSetara;
      $FFVerifikasi = $FFVerifikasi ==''?NULL:$FFVerifikasi;
      $FAQBeratBersihSetara = $FAQBeratBersihSetara == ''?NULL:$FAQBeratBersihSetara;
      $FAQVerifikasi = $FAQVerifikasi == ''?NULL:$FAQVerifikasi;
      $standardStandardNamaTmp = $standardStandardNamaTmp == ''?NULL:$standardStandardNamaTmp;
      $detail = $detail = ''?NULL:$detail;
      $standardStandardNama = $standardStandardNama == ''?NULL:$standardStandardNama;
      $resultStandardNama = $resultStandardNama == ''?NULL:$resultStandardNama;
      $rewardStandardNama = $rewardStandardNama == ''?NULL:$rewardStandardNama;
  		$QDetailID = $QDetailID == ''?NULL:$QDetailID;
      $QStandardID = $QStandardID == ''?NULL:$QStandardID;
  		$QFAQResult = $QFAQResult == ''?NULL:$QFAQResult;
      $QFAQStandard = $QFAQStandard == ''?NULL:$QFAQStandard;
      $QFAQReward = $QFAQReward == ''?NULL:$QFAQReward;
  		$QFFResult = $QFFResult == ''?NULL:$QFFResult;
      $QFFStandard = $QFFStandard == ''?NULL:$QFFStandard;
      $QFFReward = $QFFReward == ''?NULL:$QFFReward;

        $sql = "
            update ktv_supplychain_transaction
            set SupplyBatchID=?,DateTransaction=?,SupplyType=?,SupplyID=?,
               FFVolumeBruto=?, FFVolumePackage=?, FFVolumeMoisture=?, FFVolumeNetto=?,
               FAQVolumeBruto=?, FAQVolumePackage=?, FAQVolumeMoisture=?, FAQVolumeNetto=?,
               FFRewardBruto=?,FFRewardBonus=?,FFReward=?, FFContractPrice=?, FFNetPrice=?, FFTotalPayment=?,
               FAQRewardBruto=?,FAQRewardBonus=?,FAQReward=?, FAQContractPrice=?, FAQNetPrice=?, FAQTotalPayment=?, Dp=?, StatusCode=?,
            	WeightBy=?, SupervisorID=?, AdminID=?, VehicleNo=?, DateUpdated=now(),LastModifiedBy=?,
               FakturNumber=?,FFBeratBersihSetara=?,FFVerifikasi=?,FAQBeratBersihSetara=?,FAQVerifikasi=?
            where SupplyTransID=?";
        $query = $this->db->query($sql, array($SupplyBatchID,$DateTransaction,$SupplyType,$SupplyID,
            $FFVolumeBruto, $FFVolumePackage,$FFVolumeMoisture,$FFVolumeNetto,
            $FAQVolumeBruto,$FAQVolumePackage,$FAQVolumeMoisture, $FAQVolumeNetto,
            $FFRewardBruto,$FFRewardBonus,$FFReward, $FFContractPrice, $FFNetPrice, $FFTotalPayment,
            $FAQRewardBruto,$FAQRewardBonus,$FAQReward, $FAQContractPrice, $FAQNetPrice, $FAQTotalPayment, $DpPercent, $StatusCode,
         	$WeightBy, $SupervisorID, $AdminID, $VehicleNo, $userid,
            $FakturNumber,$FFBeratBersihSetara,$FFVerifikasi,$FAQBeratBersihSetara,$FAQVerifikasi,$id));
        //**//
		/*$sql = "
            DELETE FROM ktv_supplychain_transaction_quality WHERE SupplyTransID=?";
         $this->db->query($sql, array($id));
         if(!empty($standard)) {
            $sql = "
               INSERT INTO ktv_supplychain_transaction_quality(DetailID,SupplyTransID,FFValue,FAQValue,FFResult,FAQResult,
                  FFReward,FAQReward)
               VALUES (?,?,?,?,?,?,?,?)";
            for ($i=0;$i<sizeof($standard);$i++) {
               $this->db->query($sql, array($detail[$i],$id,$ffstandard[$i],$faqstandard[$i],$ffresult[$i],$faqresult[$i],
                  $ffreward[$i],$faqreward[$i]));
            }
         }*/
		//**//

		if(@$QDetailID[0]!=""){
			$sql = "DELETE FROM ktv_supplychain_transaction_quality WHERE SupplyTransID=?";
			$this->db->query($sql, array($id));
			$total_quality = count($QDetailID);
			$sql = "INSERT INTO ktv_supplychain_transaction_quality(SupplyTransID,DetailID,StandardID,FFStandard,FAQStandard,FFResult,FAQResult,
                  FFReward,FAQReward)
               VALUES (?,?,?,?,?,?,?,?,?)";
			for($i=0;$i<$total_quality;$i++){
                $this->db->query($sql, array($id, $QDetailID[$i], $QStandardID[$i], $QFFStandard[$i], $QFAQStandard[$i], $QFFResult[$i], $QFAQResult[$i], $QFFReward[$i], $QFAQReward[$i]));
			}
		}

        if ($query) {
            if ($SupplyType=='Batch') {
               $sql = "
                  UPDATE ktv_supplychain_batch SET SupplyDestStatus=?
                  WHERE SupplyBatchNumber=?";
               $query = $this->db->query($sql, array('Delivered', $SupplyID));
            }
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function readDest($id,$userid){
      $sql = "
         SELECT substr(kso.VillageID,1,4) as id,OrgType Type
         FROM ktv_supplychain_staff kss
         LEFT JOIN ktv_supplychain_org_view kso ON kss.StaffSupplychainID=kso.SupplychainID
         WHERE UserId=?";
        $query = $this->db->query($sql, array($userid));
        $district = $query->result_array();
      /*  if ($district[0]['Type']=='buyingunit') $add = "Type in('warehouse','cooperation','trader')";
        elseif ($district[0]['Type']=='trader') $add = "Type in('warehouse','cooperation')";
        elseif ($district[0]['Type']=='cooperation') $add = "Type in('warehouse')";
        elseif ($district[0]['Type']=='warehouse') $add = "Type in('ware')";*/

        $sql = "
            select kso.SupplychainID id,IF(kso.OrgType='cpg',concat('[Kelompok Petani] ',GroupName),IF(kso.OrgType='trader',
               concat('[Pedagang] ',concat(TraderName,IF(Company!='',concat(' (',Company,')'),''))),
               IF(kso.OrgType='koperasi',concat('[Organisasi Petani] ',CoopName),IF(kso.OrgType='warehouse',concat('[Gudang] ',WarehouseName),'')))) as label
            from ktv_supplychain_org_rel ksor
            left join ktv_supplychain_org kso on ksor.ParentOrgID=kso.SupplychainID
            left join ktv_traders kt on kso.OrgType='trader' and TraderID=kso.OrgID
            left join ktv_cooperatives kc on kso.OrgType='koperasi' and CoopID=kso.OrgID
            left join ktv_cpg kcpg on kso.OrgType='cpg' and CPGid=kso.OrgID
            left join ktv_warehouse kw on kso.OrgType='warehouse' and Warehouseid=kso.OrgID
            where ChildOrgId=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        if ($district[0]['Type']=='Gudang') {
            $result[0]['id'] = $result[0]['label'] = 'Closed';
        } else $result = array_merge($result,array(array('id'=>'Other','label'=>'Other')));
        return $result;
    }
    function readTransData($id){
        $sql = "
            select *,date(DateTransaction) DateTransaction,kst.SupplyBatchID,ksb.SupplyBatchID frombatchid,
               ksnf.FarmerID NonFarmerID,kcf.FarmerID FarmerID,IFNULL(kcf.FarmerName,ksnf.FarmerName) FarmerName,
               IFNULL(batas_atas_cert,batas_atas_non) batas_atas,jual
            from ktv_supplychain_transaction kst
            left join ktv_farmer kcf on kcf.FarmerID=kst.SupplyID and (SupplyType='Farmer' OR SupplyType='FarmerNonCert')
            left join ktv_supplychain_non_farmer ksnf on ksnf.FarmerID=kst.SupplyID and SupplyType='NonFarmer'
            left join ktv_cpg kc on kc.CPGid=kcf.CPGid
            left join ktv_district kd on substr(IF(kcf.FarmerID is null,ksnf.FarmerVillageID,kcf.VillageID),1,4)=kd.DistrictID

            left join ktv_supplychain_batch ksb on kst.SupplyID=ksb.SupplyBatchNumber
            left join ktv_supplychain_org kso on ksb.SupplyOrgID=kso.SupplychainID
            left join ktv_supplychain_org_view ksov on ksb.SupplyOrgID=ksov.SupplychainID

            LEFT JOIN (
            	SELECT z.FarmerID,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) batas_atas_non
               FROM
            	(SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z
            	LEFT JOIN ktv_farmer_garden kcfg ON kcfg.GardenNr=z.GardenNr AND kcfg.SurveyNr=z.LatestSurveyNr AND z.FarmerID=kcfg.FarmerID
            	GROUP BY z.FarmerID) kcfg ON kcf.FarmerID=kcfg.FarmerID
            LEFT JOIN (
            	SELECT z.FarmerID,sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) batas_atas_cert
               FROM
            	(SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr FROM ktv_certification GROUP BY FarmerID,GardenNr) z
            	LEFT JOIN ktv_farmer_garden kcfg ON kcfg.GardenNr=z.GardenNr AND kcfg.SurveyNr=z.LatestSurveyNr AND z.FarmerID=kcfg.FarmerID
            	WHERE z.FarmerID is not null
               GROUP BY z.FarmerID) kcfgb ON kcf.FarmerID=kcfgb.FarmerID
            LEFT JOIN (
               SELECT SupplyID supp,YEAR(DateTransaction) datet,SUM(Weight) jual FROM ktv_supplychain_transaction a
               LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
            	WHERE (SupplyType='Farmer' OR SupplyType='FarmerNonCert') GROUP BY SupplyID,YEAR(DateTransaction)) kstb
               ON Supp=kcf.farmerID AND Datet=YEAR(DateTransaction)
            WHERE kst.SupplyTransID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }
    function deleteDataFarmer($id,$SupplyTransID=''){
        if ($id!='') {
           $sql = "DELETE FROM ktv_supplychain_transaction_dtl WHERE DetailID=?";
           $del = $this->db->query($sql, array($id));
        } elseif ($SupplyTransID!='') {
           $sql = "DELETE FROM ktv_supplychain_transaction_dtl WHERE SupplyTransID=?";
           $del = $this->db->query($sql, array($SupplyTransID));
        }

        if ($del) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    function createDataFarmer($SupplyTransID, $PackageID, $type, $Weight,$MoistureStd,$Moisture,$netto,$userid,
         $FarmerID='',$berat=''){
        $sql_type = $this->db->query("SELECT IF(FFVolumeNetto > 0,'FF','FAQ') AS Type FROM ktv_supplychain_transaction WHERE SupplyTransID=?",array($SupplyTransID))->row()->Type;
        $sql = "
            INSERT INTO ktv_supplychain_transaction_dtl (SupplyTransID, PackageID, Type, Weight, WeightPackage,
               MoistureStandard,Moisture,Netto,DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,?,?,now(),?)";
         $sql_karung = "
            insert into ktv_supplychain_transaction_dtl_farmer(DetailID,FarmerID,Berat)
            values (?,?,?)";
        $query = $this->db->query($sql, array($SupplyTransID, $PackageID,$sql_type, $Weight,($Weight-$netto),
            $MoistureStd,$Moisture,$netto,$userid));
         $id = $this->db->insert_id();
        if ($FarmerID=='') {
            $farmer = explode('|',$FarmerID);
            $bera = explode('|',$berat);
            for ($i=0;$i<sizeof($farmer);$i++) {
              if($farmer[$i]!='') $this->db->query($sql_karung, array($id,$farmer[$i],$bera[$i]));
            }
        }
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
    function updateDataFarmer($SupplyTransID, $PackageID, $type,$Weight,$MoistureStd,$Moisture,$netto,$userid, $id){
        $sql = "
            UPDATE ktv_supplychain_transaction_dtl SET SupplyTransID=?, PackageID=?,Type=?, Weight=?, WeightPackage=?,
               MoistureStandard=?,Moisture=?,Netto=?,DateUpdated=now(), LastModifiedBy=?
            WHERE DetailID=?";
        $query = $this->db->query($sql, array($SupplyTransID, $PackageID, $type,$Weight,($Weight-$netto),
            $MoistureStd,$Moisture,$netto,$userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
            $results['id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteDataBatch($id){
        $sql_trans_dtl = "
            DELETE FROM ktv_supplychain_transaction_dtl WHERE SupplyTransID in (
            SELECT SupplyTransID FROM ktv_supplychain_transaction WHERE SupplyBatchID=?)";
        $sql_trans = "
            DELETE FROM ktv_supplychain_transaction WHERE SupplyBatchID=?";
        $sql = "
            DELETE FROM ktv_supplychain_batch WHERE SupplyBatchID=?";
        $this->db->trans_start();
        $this->db->query($sql_trans_dtl, array($id));
        $this->db->query($sql_trans, array($id));
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
    function createDataBatch($SupplyOrgID, $SupplyDestOrgID, $SupplyDestStatus, $SupplyBatchNumber,
               $SupplyBatchDate, $VolumeBruto, $VolumeNetto,$PerwakilanOrgID,$userid){
        if ($SupplyDestOrgID=='') $SupplyDestOrgID = null;
        if ($PerwakilanOrgID=='' || $PerwakilanOrgID=='0') $PerwakilanOrgID = null;
        $sql = "
            INSERT INTO ktv_supplychain_batch (SupplyOrgID, SupplyDestOrgID, SupplyDestStatus, SupplyBatchNumber,
               SupplyBatchDate, VolumeBruto, VolumeNetto, PerwakilanOrgID,DateCreated, CreatedBy)
            VALUES (?,?,?,?,   ?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($SupplyOrgID, $SupplyDestOrgID, $SupplyDestStatus, $SupplyBatchNumber,
               $SupplyBatchDate, $VolumeBruto, $VolumeNetto,$PerwakilanOrgID,$userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
            $results['id'] = $this->db->insert_id();
            $results['batch_number'] = $SupplyBatchNumber;
            $sql = "
               SELECT PembelianNonFarmer nonf from ktv_supplychain_org WHERE SupplychainID=?";
            $query = $this->db->query($sql, array($SupplyOrgID));
            $result = $query->result_array();
            $results['nonf'] = $result[0]['nonf'];
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateDataBatch($SupplyOrgID, $SupplyDestOrgID, $SupplyDestStatus, $SupplyBatchNumber,
               $SupplyBatchDate, $VolumeBruto, $VolumeNetto,$userid, $id,
               $po,$wieght,$karung,$ics,$driver,$DestDriverJabatan,$DestDriverAddress,$nopol,$DestTransport,$perwakilan,$dikirim,$SupplyBatchResponsible){
        if ($perwakilan=='' || $perwakilan=='0') $perwakilan = null;
		$this->db->trans_start();
        if ($SupplyDestOrgID=='') $SupplyDestOrgID = null;
        else $SupplyDestStatus = 'Sent';
        if ($SupplyDestOrgID == 'Closed') {
            $SupplyDestOrgID = null;
            $SupplyDestStatus = 'Closed';
           $sql = "
               UPDATE ktv_supplychain_batch SET SupplyDestStatus=?
               WHERE SupplyBatchNumber in (select SupplyID from ktv_supplychain_transaction where SupplyBatchID=?)";
            $query = $this->db->query($sql, array('Delivered', $id));
        } elseif ($SupplyDestOrgID == 'Other') {
            $SupplyDestOrgID = null;
            $SupplyDestStatus = 'Other';
         }
        $driver = implode( "|", $driver);
        $DestDriverAddress = implode( "|", $DestDriverAddress);
        $DestDriverJabatan = implode( "|", $DestDriverJabatan);
        $nopol = implode( "|", $nopol);
        $DestTransport = implode( "|", $DestTransport);
        $sql = "
            UPDATE ktv_supplychain_batch SET SupplyOrgID=?, SupplyDestOrgID=?, SupplyDestStatus=?, SupplyBatchNumber=?,
               SupplyBatchDate=?, VolumeBruto=?, VolumeNetto=?, DateUpdated=now(), LastModifiedBy=?,
               DestPO=?,DestWeight=?,DestJumlahKarung=?,DestICS=?,DestDriver=?,DestDriverJabatan=?,DestDriverAddress=?,DestNoPolisi=?,DestTransport=?,PerwakilanOrgID=?,DeliveryDate=?,SupplyBatchResponsible=?
            WHERE SupplyBatchID=?";
        $query = $this->db->query($sql, array($SupplyOrgID, $SupplyDestOrgID, $SupplyDestStatus, $SupplyBatchNumber,
               $SupplyBatchDate, $VolumeBruto, $VolumeNetto,$userid,
               $po,$wieght,$karung,$ics,$driver,$DestDriverJabatan,$DestDriverAddress,$nopol,$DestTransport,$perwakilan,$dikirim,$SupplyBatchResponsible,$id));
         $sql_cek = "SELECT DestWeight,DestKarungStart FROM ktv_supplychain_batch WHERE SupplyBatchID=?";
         $query = $this->db->query($sql_cek, array($id));
         $result = $query->result_array();
         if ($result[0]['DestWeight']>0 and $result[0]['DestKarungStart']=='') {
            $sql = "
               select IFNULL(max(DestKarungEnd)+1,1) start,IFNULL(max(DestKarungEnd)+1,1)+ceil(?/62.5)-1 end,
                  IFNULL(max(NoPO)+1,1) po
               from ktv_supplychain_batch where SupplyOrgID=?";
            $query = $this->db->query($sql, array($wieght,$SupplyOrgID));
            $result = $query->result_array();
         //print_r($result);exit;
            $sql = "
               UPDATE ktv_supplychain_batch SET DestKarungStart=?,DestKarungEnd=?,NoPO=? WHERE SupplyBatchID=?";
            $this->db->query($sql, array($result[0]['start'],$result[0]['end'],$result[0]['po'],$id));
         }
		//*Ceck jika belum dibuat Packing list maka otomatis dibuatkan (untuk glondongan)*//
		$sql_cek = "
            select DetailID id,FromBatchID,NettoDelivery jumlah
            from ktv_supplychain_transaction_dtl
            WHERE FromBatchID=?";
        $query_cek = $this->db->query($sql_cek, array($id));
		if($query_cek->num_rows() == 0){
			//$this->createDataPackingList($this->post('BatchID'),$VolumeNetto, $_SESSION['userid'], );
			$sql_insert = "
					INSERT INTO ktv_supplychain_transaction_dtl (FromBatchID,Weight,NettoDelivery,Type,PackageID,WeightPackage,
					   DateCreated,CreatedBy)
					select kst.SupplyBatchID,?,?,IF(sum(IFNULL(FFVolumeNetto,0))>sum(IFNULL(FAQVolumeNetto,0)),'FF','FAQ') jenis,PackageID,0,
					   now(),?
					from ktv_supplychain_transaction kst
					left join ktv_supplychain_batch ksb on kst.SupplyBatchID=ksb.SupplyBatchID
					left join ktv_supplychain_package on (PackageSupplychainID=SupplyDestOrgID or PackageSupplychainID=?) and PackageWeight=0
					where kst.SupplyBatchID=?";
			$query_insert = $this->db->query($sql_insert, array($VolumeNetto,$VolumeNetto,$_SESSION['userid'],'',$id));
            $return_id = $this->db->insert_id();
		}else{
            $return_id = '';
        }
        //Auto Insert Khusus Pipiltin
        /*if($SupplyDestOrgID=='179'){
            $sql_batch = "SELECT * FROM ktv_supplychain_batch WHERE SupplyBatchID=?";
            $data_batch = $this->db->query($sql_batch,array($id))->row();
            $no['number'] = getBatchNumber(179);//$this->readNumber(0,$SupplyOrgID);
            $sql_insert_batch = "INSERT INTO ktv_supplychain_batch(SupplyOrgID,SupplyDestStatus,SupplyBatchNumber,VolumeBruto,VolumeNetto,SupplyBatchDate,PerwakilanOrgID,DeliveryDate,SupplyBatchResponsible,DestPO,DestWeight,DestICS,DestDriver,DestDriverJabatan,DestDriverAddress,DestNoPolisi,DestTransport,DestKarungStart,DestKarungEnd,NoPO,DestJumlahKarung,DateCreated,CreatedBy)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?)";
            $insert_batch = $this->db->query($sql_insert_batch,array($data_batch->SupplyDestOrgID, 'Open', $no['number'], $data_batch->VolumeBruto, $data_batch->VolumeNetto, $data_batch->SupplyBatchDate, $data_batch->PerwakilanOrgID, $data_batch->DeliveryDate, $data_batch->SupplyBatchResponsible, $data_batch->DestPO, $data_batch->DestWeight, $data_batch->DestICS, $data_batch->DestDriver, $data_batch->DestDriverJabatan, $data_batch->DestDriverAddress, $data_batch->DestNoPolisi, $data_batch->DestTransport, $data_batch->DestKarungStart, $data_batch->DestKarungEnd, $data_batch->NoPO, $data_batch->DestJumlahKarung, $_SESSION['userid']));
            $batch_id = $this->db->insert_id();
            //insert ke trasaction dan transaction dtl
            $sql_transaction = "SELECT SUM(FFVolumeBruto) FFVolumeBruto, SUM(FFVolumeNetto) FFVolumeNetto, SUM(FAQVolumeBruto) FAQVolumeBruto, SUM(FAQVolumeNetto) FAQVolumeNetto, FFContractPrice, FFNetPrice, SUM(FFTotalPayment) FFTotalPayment, FAQContractPrice, FAQNetPrice, SUM(FAQTotalPayment) FAQTotalPayment, IsQuota FROM ktv_supplychain_transaction WHERE SupplyBatchID=?";
            $data_transaction = $this->db->query($sql_transaction,array($id))->row();
            $sql_insert_transaction = "INSERT INTO ktv_supplychain_transaction(SupplyBatchID,DateTransaction,SupplyType,SupplyID,FFVolumeBruto,FFVolumeNetto,FAQVolumeBruto,FAQVolumeNetto,FFContractPrice,FFNetPrice,FFTotalPayment,FAQContractPrice,FAQNetPrice,FAQTotalPayment,DateCreated,CreatedBy,IsQuota) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,?)";
            $insert_transaction = $this->db->query($sql_insert_transaction, array($batch_id, $data_batch->SupplyBatchDate, 'Batch', $data_batch->SupplyBatchNumber, $data_transaction->FFVolumeBruto, $data_transaction->FFVolumeNetto, $data_transaction->FAQVolumeBruto, $data_transaction->FAQVolumeNetto, $data_transaction->FFContractPrice, $data_transaction->FFNetPrice, $data_transaction->FFTotalPayment, $data_transaction->FAQContractPrice, $data_transaction->FAQNetPrice, $data_transaction->FAQTotalPayment, $_SESSION['userid'], $data_transaction->IsQuota));
            $transaction_id = $this->db->insert_id();
            //
            $sql_update_batch = "UPDATE ktv_supplychain_batch SET SupplyDestStatus='Delivered' WHERE SupplyBatchID=?";
            $this->db->query($sql_update_batch,array($id));
        }*/

        $check = $this->db->query("SELECT IsAutoBatch, SentEmail FROM ktv_supplychain_org WHERE SUpplychainID=?",array($SupplyOrgID))->row();
        if($check->IsAutoBatch=='1'){
            $batch_number = $this->readNumber(0, $SupplyDestOrgID);
            $batch = $this->createDataBatch($SupplyDestOrgID, NULL, 'Open', $batch_number['number'], $dikirim, $VolumeBruto, $VolumeNetto, NULL, $userid);

            $sql_transaction = "SELECT SUM(FFVolumeBruto) FFVolumeBruto, SUM(FFVolumeNetto) FFVolumeNetto, SUM(FAQVolumeBruto) FAQVolumeBruto, SUM(FAQVolumeNetto) FAQVolumeNetto, FFContractPrice, FFNetPrice, SUM(FFTotalPayment) FFTotalPayment, FAQContractPrice, FAQNetPrice, SUM(FAQTotalPayment) FAQTotalPayment, IsQuota, FFVolumeMoisture, SUM(FFBeratBersihSetara) FFBeratBersihSetara, SUM(FAQBeratBersihSetara) FAQBeratBersihSetara FROM ktv_supplychain_transaction WHERE SupplyBatchID=?";
            $dtrans = $this->db->query($sql_transaction,array($id))->row();
            $trans = $this->createTransaction($batch['id'], $dikirim, 'Batch', $SupplyBatchNumber,
            $dtrans->FFVolumeBruto, NULL, NULL, $dtrans->FFVolumeNetto,
            $dtrans->FAQVolumeBruto, NULL,$dtrans->FFVolumeMoisture,$dtrans->FAQVolumeNetto,
            NULL, NULL, NULL, $dtrans->FFContractPrice, $dtrans->FFNetPrice, $dtrans->FFTotalPayment,
            NULL, NULL, NULL, $dtrans->FAQContractPrice, $dtrans->FAQNetPrice, $dtrans->FAQTotalPayment, NULL, NULL,
            NULL, NULL, NULL, NULL,
            $userid, NULL, $dtrans->FFBeratBersihSetara, NULL, $dtrans->FAQBeratBersihSetara, NULL,
            NULL, NULL, NULL, NULL, NULL, $id);
        }

        if($check->SentEmail==''){
            $str .= "<br/>Harap tidak membalas email ini karena terkirim secara otomatis oleh sistem<br/>";
            $str .= "<br/>Demikian disampaikan, atas perhatian $gender kami ucapkan terima kasih.<br/>";
            $str .= "<br/>Salam Hangat,<br/>";
            $str .= "<br/><br/>";
            $str .= "<br/>&copy; Cocoatrace.<br/>";
            
            if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL) === false) {
                $this->load->library('email');
                $this->email->initialize($this->config->load('email'));
                $this->email->from('support@koltiva.com', 'Koltiva Support');
                $this->email->to($user['email']);
                $this->email->cc('info@koltiva.com');
                $this->email->subject($user['name'] . ' - Sync : ' . $date);
                $this->email->message($str);
                $this->email->send();
            }
        }

		$this->db->trans_complete();
		//**//

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
            $results['id'] = $return_id;
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function closeBatch($VolumeBruto,$VolumeNetto,$id,$IsGeneratePo,$SupplychainID){
        $this->db->trans_start();
        if($IsGeneratePo=='1'){
            $nopo = date('dmY').'-'.$SupplychainID;
            $sql = "UPDATE ktv_supplychain_batch SET DestPO=? WHERE SupplyBatchID=?";
            $query = $this->db->query($sql, array($nopo,$id));
        }else{
            $nopo = "";
        }
        $sql = "UPDATE ktv_supplychain_batch SET VolumeBruto=?,VolumeNetto=?,SupplyDestStatus='Close Batch', DestWeight=? WHERE SupplyBatchID=?";
        $query = $this->db->query($sql, array($VolumeBruto,$VolumeNetto,$VolumeNetto,$id));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record updated.";
            $results['id'] = $this->db->insert_id();
            $results['nopo'] = $nopo;
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function readNumber($id,$orgid=''){
        if ($id==0){
            $where = "kso.SupplychainID=$orgid";
            $orgid = "";
        }else{
            $where = "UserId=$id";
            if ($orgid!='') $whare .= " OR kso.SupplychainID=$orgid";
        }
      
      $sql = "
         SELECT SupplychainID id,NAME nama,IFNULL(MAX(SupplyBatchNumber)+1,CONCAT(YEAR(NOW()),'0001')) number_,
            CONCAT(LPAD(kso.SupplychainID, 4, '0'),LPAD(IFNULL(MAX(SUBSTR(SupplyBatchNumber,5,5)-0)+1,1), 5, '0')) number, kso.OrgID
         FROM ktv_supplychain_org_view kso
         LEFT JOIN ktv_supplychain_staff kss ON kss.StaffSupplychainID=kso.SupplychainID
         LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyOrgID=kso.SupplychainID
         where  $where";
         //echo $id;exit;
        $query = $this->db->query($sql, array($id));
        //echo "<pre>".$this->db->last_query();exit;

        $result = $query->result_array();
        $result[0]['number'] = getBatchNumber($result[0]['id']);
        return $result[0];
    }
    function readData($id){
        $sql = "
            select ksb.SupplyBatchID,ksb.SupplyOrgID,Name,ksb.SupplyDestOrgID,ksb.SupplyDestStatus, ksb.DestJumlahKarung,
               ksb.SupplyBatchNumber,ksb.SupplyBatchDate,ksb.VolumeBruto,ksb.VolumeNetto,kso.SupplychainID id,
               DestWeight,DestICS,DestDriver,DestDriverJabatan,DestDriverAddress,DestNoPolisi,DestTransport,DestPO,PerwakilanOrgID,DeliveryDate,SupplyBatchResponsible,
               ksov.*,
               if(ksb.SupplyDestStatus in ('Closed','Other'),SupplyDestStatus,SupplyDestOrgID) SupplyDestOrgID
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_org_view kso on ksb.SupplyOrgID=kso.SupplychainID
            left join ktv_supplychain_org ksov on ksov.SupplychainID=kso.SupplychainID
            left join ktv_supplychain_staff kss on kss.StaffSupplychainID=kso.SupplychainID
            WHERE ksb.SupplyBatchID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }



    function readDataFarmer($id){
        $sql = "
            select FarmerID,FarmerName,GroupName,District
            from ktv_farmer kcf
            left join ktv_cpg kc on kc.CPGid=kcf.CPGid
            left join ktv_district kd on substr(kcf.VillageID,1,4)=kd.DistrictID
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
     }

    function readBatch($destid){
      $sql = "
         select SupplychainID,concat(SupplychainID,' - ',Name) nama,IFNULL(max(SupplyBatchNumber)+1,concat(year(now()),'0001')) number
         from ktv_supplychain_batch ksb
         left join ktv_supplychain_staff kss on kss.StaffSupplychainID=ksb.SupplyOrgID
         left join ktv_supplychain_org kso on kss.StaffSupplychainID=kso.SupplychainID
         where SupplyDestOrgID=? and SupplyDestStatus='Sent'";
        $query = $this->db->query($sql, array($destid));
        $result = $query->result_array();
        return $result[0];
    }



    function deleteData($id){
        $sql_trans_dtl = "
            DELETE FROM ktv_supplychain_transaction_dtl WHERE SupplyTransID in (
            SELECT SupplyTransID FROM ktv_supplychain_transaction WHERE SupplyBatchID=?)";
        $sql_trans = "
            DELETE FROM ktv_supplychain_transaction WHERE SupplyBatchID=?";
        $sql = "
            DELETE FROM ktv_supplychain_batch WHERE SupplyBatchID=?";
        $this->db->trans_start();
        $this->db->query($sql_trans_dtl, array($id));
        $this->db->query($sql_trans, array($id));
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
    function readFarmers($userid,$key,$start,$limit,$noncert=''){
      if ($key=='') return;
      $sql = "
         SELECT GROUP_CONCAT(DistrictID) AS id,kso.OrgType,kso.OrgID,
            IFNULL(ksorb.ParentOrgId,IFNULL(ksor.ParentOrgId,kso.SupplychainID)) BatasSupplychainID,PerwakilanKoperasi,
            StaffSupplychainID
         FROM ktv_supplychain_staff kss
         LEFT JOIN ktv_supplychain_org_view kso ON kso.SupplychainID=StaffSupplychainID
         LEFT JOIN ktv_supplychain_org ksoa ON ksoa.SupplychainID=StaffSupplychainID
         LEFT JOIN ktv_supplychain_org_rel ksor ON ksor.ChildOrgId=StaffSupplychainID
         LEFT JOIN ktv_supplychain_org_rel ksorb ON ksorb.ChildOrgId=ksor.ParentOrgId
         LEFT JOIN ktv_warehouse kw ON kso.OrgType='Gudang' AND WarehouseID=kso.OrgID
         #LEFT JOIN ktv_district_partner kdp ON kdp.PartnerID=kw.PartnerID
         LEFT JOIN ktv_supplychain_area ksa ON StaffSupplychainID=ksa.SupplychainID
         WHERE UserId=?
         GROUP BY UserId";
        $query = $this->db->query($sql, array($userid));
        $district = $query->result_array();
        //echo $userid;
        //print_r($district);exit;
        if ($district[0]['OrgType']=='Organisasi Petani') {
//            $wh = 'substr(kcf.FarmerID,1,2)=?';
  //          $wh_n = $district[0]['OrgID'];
    //        if ($district[0]['PerwakilanKoperasi']=='1') {
               $wh = 'substr(kcf.VillageID,1,4) in (SELECT DistrictID FROM ktv_supplychain_area where SupplychainID=?)';
               $wh_n = $district[0]['StaffSupplychainID'];
      //      }
        } elseif ($district[0]['OrgType']=='Gudang') {
            $wh = 'substr(kcf.FarmerID,1,4) in ('.$district[0]['id'].') and ""=?';
            $wh_n = '';
        } else {
            $wh = 'substr(kcf.VillageID,1,4) in ('.$district[0]['id'].') and ""=?';
            $wh_n = '';
        }
        $BatasSupplychainID = $district[0]['BatasSupplychainID'];
        $sql_cert = "
         SELECT a.*,SUM(IFNULL(FAQVolumeBruto,FFVolumeBruto)) jual FROM (
            select sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) batas_atas,
               CertificationStart,CertificationEnd, %s
            from ktv_farmer kcf
            left join ktv_cpg kc ON kcf.CPGid=kc.CPGid
            left join ktv_district kd ON substr(kcf.VillageID,1,4)=kd.DistrictID
            LEFT JOIN (select FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr,CertificationStart,CertificationEnd
               FROM ktv_certification
               WHERE (date(now()) between CertificationStart and CertificationEnd) AND ExternalDate != '0000-00-00' AND ExternalDate IS NOT NULL
               GROUP BY FarmerID,GardenNr) z on
               kcf.FarmerID=z.FarmerID
            left join ktv_farmer_garden kcfg ON kcf.FarmerID=kcfg.FarmerID and kcfg.GardenNr=z.GardenNr and kcfg.SurveyNr=z.LatestSurveyNr
            WHERE (kcf.FarmerID=? OR FarmerName like ?) and kcf.StatusCode!='nullified' and z.FarmerID is not null and $wh
            GROUP BY kcf.FarmerID
            ORDER BY FarmerName
         ) a
         LEFT JOIN ktv_supplychain_transaction ON SupplyID=a.id AND (DateTransaction BETWEEN CertificationStart AND CertificationEnd) AND ktv_supplychain_transaction.IsQuota='1' 
         GROUP BY a.id
         %s";
        $sql_non = "
            select sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) batas_atas,jual, %s
            from ktv_farmer kcf
            left join ktv_cpg kc ON kcf.CPGid=kc.CPGid
            left join ktv_district kd ON substr(kcf.VillageID,1,4)=kd.DistrictID
            LEFT JOIN (select FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_certification
               WHERE (date(now()) between CertificationStart and CertificationEnd) AND ExternalDate != '0000-00-00' AND ExternalDate IS NOT NULL
               GROUP BY FarmerID,GardenNr) zz on
               kcf.FarmerID=zz.FarmerID
            LEFT JOIN (select FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               kcf.FarmerID=z.FarmerID
            left join ktv_farmer_garden kcfg ON kcf.FarmerID=kcfg.FarmerID and kcfg.GardenNr=z.GardenNr and kcfg.SurveyNr=z.LatestSurveyNr
            LEFT JOIN (
         		SELECT SupplyID,SUM(IFNULL(FAQVolumeBruto,FFVolumeBruto)) jual FROM ktv_supplychain_transaction
         		WHERE YEAR(DateTransaction)=YEAR(NOW()) AND SupplyType='FarmerNonCert'
         		GROUP BY SupplyID) kst ON SupplyID=kcf.farmerID
            WHERE (kcf.FarmerID=? OR FarmerName like ?) and kcf.StatusCode!='nullified' and zz.FarmerID is null and $wh
            GROUP BY kcf.FarmerID
            ORDER BY FarmerName
            %s";
//            echo 'select *,batas_atas+(10/100*batas_atas) batas_atas from ('.
//            printf($sql_cert,"kcf.FarmerID id,FarmerName name, GroupName grup,
  //          District district",'LIMIT ?,?').') a where batas_atas>0';
    //        print_r(array($key,"%$key%", $wh_n, (int)$start,(int)$limit));exit;
        //exit;
        if ($noncert=='') {
           $query = $this->db->query('select *,cast(batas_atas+(10/100*batas_atas) as decimal(10,2)) batas_atas, cast((batas_atas+(10/100*batas_atas) - jual) as decimal(10,2)) sisa from ('.
               sprintf($sql_cert,"kcf.FarmerID id,FarmerName name, GroupName grup,
               District district",'LIMIT ?,?').') a where batas_atas>0',
               array($key,"%$key%", $wh_n, (int)$start,(int)$limit));
           $result['data'] = $query->result_array();
           //echo "<pre>".$this->db->last_query();exit;
           $query = $this->db->query('select count(*) total from ('.sprintf($sql_cert,'kcf.FarmerID id','').') a',
               array($key,"%$key%",$wh_n));
           $total = $query->result_array();
           $result['total'] = $total[0]['total'];
        } else {
            $query = $this->db->query('select *,cast(batas_atas+(10/100*batas_atas) as decimal(10,2)) batas_atas, cast((batas_atas+(10/100*batas_atas) - jual) as decimal(10,2)) sisa from ('.
               sprintf($sql_non,"kcf.FarmerID id,FarmerName name, GroupName grup,
               District district",'LIMIT ?,?').') a where batas_atas>0',
               array($key,"%$key%", $wh_n, (int)$start,(int)$limit));
            $result['data'] = $query->result_array();
           $query = $this->db->query('select count(*) total from ('.sprintf($sql_cert,'kcf.FarmerID id','').') a',
               array($key,"%$key%",$wh_n));
           $total = $query->result_array();
           $result['total'] = $total[0]['total'];
        }
        return $result;
    }

    function readNonFarmers($userid,$key,$start,$limit){
      $sql = "
         SELECT substr(kss.VillageID,1,4) as id
         FROM (
            select 'warehouse' Type,kws.WarehouseID OrgID,UserId,WarehouseName Name,kw.VillageID
            from ktv_warehouse_staff kws
            left join ktv_warehouse kw on kws.WarehouseID=kw.WarehouseID
            UNION ALL
            select 'trader' Type,kts.TraderID OrgID,UserId,TraderName Name,kt.VillageID
            from ktv_trader_staff kts
            left join ktv_traders kt on kts.TraderID=kt.TraderID
            UNION ALL
            select 'koperasi' Type, kcs.CoopID OrgID,UserId,CoopName Name,kc.VillageID
            from ktv_cooperative_staff kcs
            left join ktv_cooperatives kc on kcs.CoopID=kc.CoopID
         ) kss
         LEFT JOIN ktv_supplychain_org kso ON kss.OrgID=kso.OrgID
         WHERE UserId=?";
        $query = $this->db->query($sql, array($userid));
        $district = $query->result_array();

        $sql = "
            select %s
            from ktv_supplychain_non_farmer kcf
            left join ktv_district kd ON substr(kcf.FarmerVillageID,1,4)=kd.DistrictID
            WHERE (FarmerID=? OR FarmerName like ?) and substr(kcf.FarmerVillageID,1,4)=?
            ORDER BY FarmerName
            %s";
        $query = $this->db->query(sprintf($sql,"FarmerID id,FarmerName name, FarmerIdentity, FarmerVillageID, FarmerBirthdate",'LIMIT ?,?'),
            array($key,"%$key%", $district[0]['id'], (int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array($key,"%$key%",$district[0]['id']));
        $result['total'] = $query->row()->total;
        return $result;
    }
    function readNonFarmerVillage($userid){
      $sql = "
         SELECT SUBSTR(a.VillageID,1,4) AS id
         FROM ktv_supplychain_org_view a
         LEFT JOIN ktv_supplychain_staff ON StaffSupplychainID=SupplychainID
         WHERE UserId=?";
        $query = $this->db->query($sql, array($userid));
        $district = $query->result_array();

        $sql = "
            select %s
            from ktv_village
            WHERE substr(VillageID,1,4)=?
            ORDER BY Village
            %s";
        $query = $this->db->query(sprintf($sql,"VillageID id,Village label",''),
            array($district[0]['id']));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''), array($key,"%$key%",$district[0]['id']));
        $result['total'] = $query->row()->total;
        return $result;
    }
    function readUnitFrom($id,$userid){
        $sql = "
            select kso.SupplychainID id,ksor.ParentOrgID,
               IF(kso.OrgType='cpg',concat('[Kelompok Petani] ',GroupName),
               IF(kso.OrgType='trader',concat('[Pedagang] ',concat(TraderName,IF(Company!='',concat(' (',Company,')'),''))),
               IF(kso.OrgType='koperasi',concat('[Organisasi Petani] ',CoopName),
               IF(kso.OrgType='warehouse',concat('[Gudang] ',WarehouseName),
               IF(kso.OrgType='sce',concat('[SCE] ',FarmerName),''))))) as label
            from ktv_supplychain_org_rel ksor
            left join ktv_supplychain_staff kss on kss.StaffSupplychainID=ksor.ParentOrgID
            left join ktv_supplychain_org kso on ksor.ChildOrgID=kso.SupplychainID
            left join ktv_traders kt on kso.OrgType='trader' and TraderID=kso.OrgID
            left join sce_farmer sf on kso.OrgType='sce' and SceID=kso.OrgID
            left join ktv_farmer kcf on kcf.FarmerID=sf.FarmerID
            left join ktv_cooperatives kc on kso.OrgType='koperasi' and CoopID=kso.OrgID
            left join ktv_cpg kcpg on kso.OrgType='cpg' and kcpg.CPGid=kso.OrgID
            left join ktv_warehouse kw on kso.OrgType='warehouse' and Warehouseid=kso.OrgID
            where UserId=? AND ksor.EndDate >= NOW()";
        $query = $this->db->query($sql, array($userid));

        /*$sql = "
            select SupplychainID id, Name label
            from ktv_supplychain_org
            WHERE (SupplychainID=? OR Name like ?) and substr(VillageID,1,4)=? and $add";
        $query = $this->db->query($sql, array($id,"%$id%",$district[0]['id']));*/
        $result = $query->result_array();
        return $result;
    }
    function readDatasPackingList($id){
        $sql = "
            select DetailID id,FromBatchID,NettoDelivery jumlah
            from ktv_supplychain_transaction_dtl
            WHERE FromBatchID=?";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }
    function createDataPackingList($batchid,$jumlah,$user,$destid='',$unitto=''){
        if($destid==''){
			$unit = explode(' - ',$unitto);
			$destid = @$unit[0];
		}
		$sql = "
            INSERT INTO ktv_supplychain_transaction_dtl (FromBatchID,Weight,NettoDelivery,Type,PackageID,WeightPackage,
               DateCreated,CreatedBy)
            select kst.SupplyBatchID,?,?,IF(sum(IFNULL(FFVolumeNetto,0))>sum(IFNULL(FAQVolumeNetto,0)),'FF','FAQ') jenis,PackageID,0,
               now(),?
            from ktv_supplychain_transaction kst
            left join ktv_supplychain_batch ksb on kst.SupplyBatchID=ksb.SupplyBatchID
            left join ktv_supplychain_package on (PackageSupplychainID=SupplyDestOrgID or PackageSupplychainID=?) and PackageWeight=0
            where kst.SupplyBatchID=?";
//            values (?,?,?,'FAQ',now(),?)";
//        $query = $this->db->query($sql, array($batchid,$jumlah,$jumlah,$user));
        $query = $this->db->query($sql, array($jumlah,$jumlah,$user,$destid,$batchid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Packing list generated";
            $results['id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    /**
     * Membuat packinglist per transaksi, khusus untuk mamuju ^_^
     * @author  <ardiantoro@koltiva.com>
     */
    function createDataPackingListPerTransaction($batchid,$user,$destid='',$unitto=''){

        ini_set('display_errors',true);
        error_reporting(E_ALL);
        //kumpulin dulu transaksinya per item yak,
        /*$this->db->select('IF(FFVolumeNetto = 0,FAQVolumeNetto,FFVolumeNetto) AS Netto,Type,PackageID',false);
        $this->db->from('ktv_supplychain_transaction');
        $this->db->join('ktv_supplychain_transaction_dtl','ktv_supplychain_transaction_dtl.SupplyTransID = ktv_supplychain_transaction.SupplyTransID');
        $this->db->where('SupplyBatchID',$batchid);
        $transaksi = $this->db->get();*/
        $sql = "SELECT IF ( a.FFVolumeNetto = 0, a.FAQVolumeNetto, a.FFVolumeNetto ) AS Netto, IF(b.Type='',IF(a.FFVolumeNetto = 0,'FAQ','FF'),b.Type) Type, b.PackageID, c.SupplyBatchID
                FROM
                    ktv_supplychain_transaction a
                    LEFT JOIN ktv_supplychain_transaction_dtl b ON b.SupplyTransID = a.SupplyTransID
                    LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber = a.SupplyID
                WHERE a.SupplyBatchID = ?";

        $transaksi = $this->db->query($sql, array($batchid));
        
        if($transaksi->num_rows() > 0 && @$transaksi->row()->PackageID != NULL){
            $transaksi = $transaksi->result_array();

            //ikutin bawah aja dah, hapus packing list sebelumnya -> indikator FromBatchID = batchid
            $this->db->where('FromBatchID',$batchid);
            $this->db->delete('ktv_supplychain_transaction_dtl');
        
            //insert packing list yang baru sesuai berat trans nya
            foreach($transaksi as $key => $trans){
                $insert = array(
                    'FromBatchID' => $batchid,
                    'Weight' => $trans['Netto'],
                    'NettoDelivery' => $trans['Netto'],
                    'Type' => $trans['Type'],
                    'PackageID' => $trans['PackageID'],
                    'WeightPackage' => 0,
                    'DateCreated' => date('Y-m-d H:i:s'),
                    'CreatedBy' => $user
                );
            $this->db->insert('ktv_supplychain_transaction_dtl',$insert);
            }

            if($this->db->insert_id()){
                $results['success'] = true;
                $results['message'] = "Packing list generated";
                $results['id'] = $this->db->insert_id();
            }

            return $results;
        }else{
            $sql = "SELECT IF ( a.FFVolumeNetto = 0, a.FAQVolumeNetto, a.FFVolumeNetto ) AS Netto, IF(b.Type='',IF(a.FFVolumeNetto = 0,'FAQ','FF'),b.Type) Type, b.PackageID, c.SupplyBatchID
                    FROM
                        ktv_supplychain_transaction a
                        LEFT JOIN ktv_supplychain_transaction_dtl b ON b.SupplyTransID = a.SupplyTransID
                        LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber = a.SupplyID
                    WHERE a.SupplyBatchID = ?";

            $query = $this->db->query($sql, array(@$transaksi->row()->SupplyBatchID));
            
            if($query->num_rows() > 0 && @$query->row()->PackageID != NULL){
                $transaksi = $query->result_array();

                //ikutin bawah aja dah, hapus packing list sebelumnya -> indikator FromBatchID = batchid
                $this->db->where('FromBatchID',$batchid);
                $this->db->delete('ktv_supplychain_transaction_dtl');
            
                //insert packing list yang baru sesuai berat trans nya
                foreach($transaksi as $key => $trans){
                    $insert = array(
                        'FromBatchID' => $batchid,
                        'Weight' => $trans['Netto'],
                        'NettoDelivery' => $trans['Netto'],
                        'Type' => $trans['Type'],
                        'PackageID' => $trans['PackageID'],
                        'WeightPackage' => 0,
                        'DateCreated' => date('Y-m-d H:i:s'),
                        'CreatedBy' => $user
                    );
                $this->db->insert('ktv_supplychain_transaction_dtl',$insert);
                }

                if($this->db->insert_id()){
                    $results['success'] = true;
                    $results['message'] = "Packing list generated";
                    $results['id'] = $this->db->insert_id();
                }

                return $results;
            }
        }



    }

    function createDataPackingListKarung($batchid,$weight,$karung,$user,$destid='',$unitto=''){
		if($destid==''){
			$unit = explode(' - ',$unitto);
			$destid = @$unit[0];
		}
      $sql_delete = "
         DELETE FROM ktv_supplychain_transaction_dtl WHERE FromBatchID=?";
      $this->db->query($sql_delete, array($batchid));
        $sql = "
            INSERT INTO ktv_supplychain_transaction_dtl (FromBatchID,Weight,NettoDelivery,Type,PackageID,WeightPackage,
               DateCreated,CreatedBy)
            select kst.SupplyBatchID,?,?,IF(sum(IFNULL(FFVolumeNetto,0))>sum(IFNULL(FAQVolumeNetto,0)),'FF','FAQ') jenis,PackageID,0,
               now(),?
            from ktv_supplychain_transaction kst
            left join ktv_supplychain_batch ksb on kst.SupplyBatchID=ksb.SupplyBatchID
            left join ktv_supplychain_package on (PackageSupplychainID=SupplyDestOrgID or PackageSupplychainID=?) and PackageWeight=0
            where kst.SupplyBatchID=?";
//            values (?,?,?,'FAQ',now(),?)";
//        $query = $this->db->query($sql, array($batchid,$jumlah,$jumlah,$user));
        for ($i=0;$i<ceil($weight/$karung);$i++) {
           if ($i+1==ceil($weight/$karung)) $jumlah = $weight - ($i*$karung);
           else $jumlah = $karung;
           $query = $this->db->query($sql, array($jumlah,$jumlah,$user,$destid,$batchid));
        }
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Packing list generated";
            $results['id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function createDataPackingListByKarung($batchid,$weight,$karung,$user,$destid='',$unitto='',$Gelondongan){
		if($destid==''){
			$unit = explode(' - ',$unitto);
			$destid = @$unit[0];
		}
	  $sql_delete = "
         DELETE FROM ktv_supplychain_transaction_dtl WHERE FromBatchID=?";
      $this->db->query($sql_delete, array($batchid));
        $sql = "
            INSERT INTO ktv_supplychain_transaction_dtl (FromBatchID,Weight,NettoDelivery,Type,PackageID,WeightPackage,
               DateCreated,CreatedBy)
            select kst.SupplyBatchID,?,?,IF(sum(IFNULL(FFVolumeNetto,0))>sum(IFNULL(FAQVolumeNetto,0)),'FF','FAQ') jenis,PackageID,0,
               now(),?
            from ktv_supplychain_transaction kst
            left join ktv_supplychain_batch ksb on kst.SupplyBatchID=ksb.SupplyBatchID
            left join ktv_supplychain_package on (PackageSupplychainID=SupplyDestOrgID or PackageSupplychainID=?) and PackageWeight=0
            where kst.SupplyBatchID=?";
		if($Gelondongan=="false"){
			$query = $this->db->query($sql, array($weight,$weight,$user,$destid,$batchid));
		}else{
			for($i=0;$i<$karung;$i++){
				$jumlah = $weight/$karung;
				$query = $this->db->query($sql, array($jumlah,$jumlah,$user,$destid,$batchid));
			}
		}
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Packing list generated";
            $results['id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateDataPackingList($jumlah,$id,$user){
        $sql = "
            UPDATE ktv_supplychain_transaction_dtl
            SET NettoDelivery=?,Weight=?,DateUpdated=now(),LastModifiedBy=?
            WHERE DetailID=?";
        $query = $this->db->query($sql, array($jumlah,$jumlah,$user,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
            $results['id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function deleteDataPackingList($id){
        $sql = "
            DELETE FROM ktv_supplychain_transaction_dtl
            WHERE DetailID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
            $results['id'] = $this->db->insert_id();
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
	function checkDatasPackingList($id,$SupplychainID){
        $sql = "
            select SUM(NettoDelivery) jumlah
            from ktv_supplychain_transaction_dtl
            WHERE FromBatchID=?";
        $query = $this->db->query($sql, array($id));

		$sql2 = "SELECT IsGeneratePacking FROM ktv_supplychain_org WHERE SupplychainID=?";
		$query2 = $this->db->query($sql2, array($SupplychainID));

		return array('jumlah'=>$query->row()->jumlah,'IsGeneratePacking'=>$query2->row()->IsGeneratePacking);
    }
	function readDetailTransaction($SupplyTransID){
		$sql = "
            select SUM(Weight) jumlah
            from ktv_supplychain_transaction_dtl
            WHERE SupplyTransID=?";
        $query = $this->db->query($sql, array($SupplyTransID));
		return $query->row()->jumlah;
	}
	function checkDataBatch($SupplyBatchNumber,$SupplyID,$SupplychainID){ //supplyID berupa supplybatch number awal.
		$sql = "
			SELECT
				#IF(a.SupplyType='Farmer',a.SupplyID,IF(d.SupplyType='Farmer',d.SupplyID,f.SupplyID)) AS FarmerID,
				#SUBSTR(IF(a.SupplyType='Farmer',a.DateTransaction,IF(d.SupplyType='Farmer',d.DateTransaction,f.DateTransaction)),1,10) AS DateTransactions,
				IF(g.FarmerID IS NULL, 'NonCert', 'Cert') AS hasil, h.PemisahanBatch

			FROM
				ktv_supplychain_transaction a
				LEFT JOIN ktv_supplychain_batch b ON a.SupplyBatchID=b.SupplyBatchID
				LEFT JOIN ktv_supplychain_batch c ON a.SupplyID=c.SupplyBatchNumber
				LEFT JOIN ktv_supplychain_transaction d ON c.SupplyBatchID=d.SupplyBatchID
				LEFT JOIN ktv_supplychain_batch e ON d.SupplyID=e.SupplyBatchNumber
				LEFT JOIN ktv_supplychain_transaction f ON e.SupplyBatchID=f.SupplyBatchID
				LEFT JOIN (
					SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
						 (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
						 (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
						 d.FarmerID,CertificationStart,CertificationEnd,ExternalDate
					FROM (
						 SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr,CertificationStart,CertificationEnd, ExternalDate
						 FROM ktv_certification
						 WHERE
							ExternalDate != '0000-00-00' AND
								ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
					INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
						 z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
					GROUP BY d.FarmerID
				) g ON
				#(g.CertificationStart<=SUBSTR(IF(a.SupplyType='Farmer',a.DateTransaction,IF(d.SupplyType='Farmer',d.DateTransaction,f.DateTransaction)),1,10) AND g.CertificationEnd>=SUBSTR(IF(a.SupplyType='Farmer',a.DateTransaction,IF(d.SupplyType='Farmer',d.DateTransaction,f.DateTransaction)),1,10)) AND
				g.FarmerID=IF(a.SupplyType='Farmer',a.SupplyID,IF(d.SupplyType='Farmer',d.SupplyID,f.SupplyID))
                LEFT JOIN ktv_supplychain_org h ON h.SupplychainID=?
			WHERE
				b.SupplyBatchNumber=? OR b.SupplyBatchNumber=?
		";
		$query = $this->db->query($sql, array($SupplyBatchNumber,$SupplyID,$SupplychainID))->result_array();
        //echo "<pre>".$this->db->last_query();exit;
		return $query;
	}
    function readBatchQuota($SupplyBatchID){
        $sql = "SELECT b.IsQuota AS SupplyDestOrgQuota, c.IsQuota AS SupplyOrgQuota FROM ktv_supplychain_batch a LEFT JOIN ktv_supplychain_org b ON a.SupplyDestOrgID=b.SupplychainID LEFT JOIN ktv_supplychain_org c ON a.SupplyOrgID=c.SupplychainID WHERE a.SupplyBatchID=?";
        $query = $this->db->query($sql,array($SupplyBatchID))->result_array();
        return $query[0];
    }
    
    function readUploadFiles($SupplyBatchID){
        $sql = "SELECT SupplyBatchFileID, FileName name, FilePath path, DateCreated date, FileType tipe FROM ktv_supplychain_batch_files WHERE SupplyBatchID=? ORDER BY SupplyBatchFileID DESC";
        $query = $this->db->query($sql, array($SupplyBatchID));
        $result= $query->result_array();
        return $result;
    }
    
    function InsertUploadFiles($SupplyBatchID,$FilePath,$FileName,$FileType,$UserID){
        $sql = "INSERT INTO ktv_supplychain_batch_files(SupplyBatchID,FilePath,FileName,FileType,CreatedBy,DateCreated) VALUES(?,?,?,?,?,NOW())";
        $query = $this->db->query($sql,array($SupplyBatchID,$FilePath,$FileName,$FileType,$UserID));
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
    
    function deleteUploadFiles($id){
        $sql_file = "SELECT FilePath FROM ktv_supplychain_batch_files WHERE SupplyBatchFileID=?";
        @$files = @$this->db->query($sql_file, array($id))->row()->File;
        $sql = "DELETE FROM ktv_supplychain_batch_files WHERE SupplyBatchFileID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
            if (!empty($files) && file_exists('./files/supplychain/'.$files)) {
                @unlink('./files/supplychain/'.$files);
            }
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

}
?>

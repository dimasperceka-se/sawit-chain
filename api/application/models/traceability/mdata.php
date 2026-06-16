<?php

class Mdata extends CI_Model
{

    function readDataComboWarehouse()
    {
        $sql = "
            SELECT distinct ksov.SupplychainID, ksov.Name
            FROM ktv_supplychain_org_view ksov
            LEFT JOIN ktv_supplychain_org_rel ksor ON ksor.ParentOrgId=ksov.SupplychainID
            LEFT JOIN ktv_supplychain_staff kss ON ksor.ChildOrgId=kss.StaffSupplychainID
            LEFT JOIN ktv_supplychain_staff kssb ON ksor.ParentOrgId=kssb.StaffSupplychainID
            WHERE ksov.OrgType='Gudang' AND (kss.UserId=? OR kssb.UserID=?)";
        $query = $this->db->query($sql, array($_SESSION['userid'],$_SESSION['userid']));
        $data = $query->result_array();
        if (empty($data)) {
           $sql = "
               select SupplychainID, Name
               from ktv_supplychain_org_view
               where OrgType='Gudang'";
           $query = $this->db->query($sql, array());
           return $query->result_array();
        } else return $data;
    }

    function readDataComboBu($id)
    {
        $sql = "
            select ksovb.SupplychainID, IF(kt.TraderID is null,ksovb.Name,concat(TraderName,' (',Company,')')) Name
            from ktv_supplychain_org_view ksov
            left join ktv_supplychain_org_rel ksor on ksor.ParentOrgId=ksov.SupplychainID
            left join ktv_supplychain_org_view ksovb on ksor.ChildOrgId=ksovb.SupplychainID
            left join ktv_traders kt on ksovb.OrgType='Pedagang' and ksovb.OrgID=kt.TraderID
            LEFT JOIN ktv_supplychain_staff kss ON ksor.ChildOrgId=kss.StaffSupplychainID
            where ksov.SupplychainID=? and kss.UserId=?";
        $query = $this->db->query($sql, array($id,$_SESSION['userid']));
        $data = $query->result_array();
        if (empty($data)) {
           $sql = "
               select ksovb.SupplychainID, IF(kt.TraderID is null,ksovb.Name,concat(TraderName,' (',Company,')')) Name
               from ktv_supplychain_org_view ksov
               left join ktv_supplychain_org_rel ksor on ksor.ParentOrgId=ksov.SupplychainID
               left join ktv_supplychain_org_view ksovb on ksor.ChildOrgId=ksovb.SupplychainID
               left join ktv_traders kt on ksovb.OrgType='Pedagang' and ksovb.OrgID=kt.TraderID
               where ksov.SupplychainID=?";
           $query = $this->db->query($sql, array($id));
           return $query->result_array();
        } else return $data;
    }

    function readDatas($userid, $type, $key, $prov, $kab, $start, $limit)
    {
        if ($type == '') $type = '%%';
        $sql = "
            select %s
            from ktv_supplychain_org_view kso
            left join ktv_supplychain_org ksov on ksov.SupplychainID=kso.SupplychainID
            left join ktv_supplychain_staff kss on kss.StaffSupplychainID=kso.SupplychainID
            left join ktv_district kd on substr(kso.VillageID,1,4)=kd.DistrictID
            left join ktv_province kp on substr(kso.VillageID,1,2)=kp.ProvinceID
            WHERE ksov.StatusCode = 'active' AND ksov.OrgType like ? and Name like ? %s";
        if ($prov != '') $add = "AND Province like '$prov' ";
        if ($kab != '') $add .= "AND District like '$kab'";

        //type
        $sql_cek = "select ksov.OrgType,kso.PerwakilanKoperasi
            from ktv_supplychain_staff  kss
            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=kss.StaffSupplychainID
            left join ktv_supplychain_org kso on ksov.SupplychainID=kso.SupplychainID
            where kss.UserId=?";
        $query = $this->db->query($sql_cek, array($userid));
        $cek = $query->result_array();

        //child
        $sql_child = "select ChildOrgId,PerwakilanKoperasi
            from ktv_supplychain_staff
            left join ktv_supplychain_org_rel on StaffSupplychainID=ParentOrgId
            left join ktv_supplychain_org on SupplychainID=ChildOrgId
            where UserId=?";
        $query = $this->db->query($sql_child, array($userid));
        $child = $query->result_array();
        for ($i = 0; $i < sizeof($child); $i++) {
            $imp[] = $child[$i]['ChildOrgId'];
        }

        //district
        $sql_district = "
         select group_concat(DistrictID) district
         from ktv_private_staff kps
         left join ktv_district_partner kdp on kps.PartnerID=kdp.PartnerID
         where UserId=?";
        $query = $this->db->query($sql_district, array($_SESSION['userid']));
        $result = $query->result_array();


        if ($cek[0]['OrgType'] == 'Gudang' and $result[0]['district'] != '')
            $add .= 'and substr(kso.VillageID,1,4) in (' . $result[0]['district'] . ')';

        if ( ! empty($cek)) {
            if ($imp[0] != '') $orr = " OR StaffSupplychainID in (" . implode(',', $imp) . ")";
            $add .= " and (kss.UserId=$userid $orr)";
        }

        if ($cek[0]['PerwakilanKoperasi'] == '1') $add .= " and kso.OrgType!='Pedagang'";
        //if ($cek[0]['OrgType']=='Gudang') $add .= "and substr(kso.VillageID,1,2) in ()";
        // printf($sql,"SupplychainID, OrgType,OrgID,Name, kso.Address,
        // District",$add.' GROUP BY OrgID ORDER BY Name LIMIT ?,?');exit;
        $query = $this->db->query(sprintf($sql, "kso.SupplychainID, kso.OrgType,kso.OrgID,Name, kso.Address,
          District", $add . ' GROUP BY kso.SupplychainID ORDER BY Name LIMIT ?,?'),
            array($type, "%$key%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(distinct kso.SupplychainID) as total', $add, ''), array($type, "%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readDatasWarehouse($userid, $type, $key, $prov, $kab, $start, $limit)
    {
        $this->user = $this->muserprofile->getUserProfile();
        $wer = "";
        $prov = $this->input->get('prov');
        if ($prov != '') {
            $wer .= " AND g.ProvinceID='$prov'";
        }
        $kab = $this->input->get('kab');
        if ($kab != '') {
            $wer .= " AND f.DistrictID='$kab'";
        }
        $kec = $this->input->get('kec');
        if ($kec != '') {
            $wer .= " AND e.SubDistrictID='$kec'";
        }
        if (!empty($this->user['district_access'])) {
            $wer .= " AND f.DistrictID IN ({$this->user['district_access']})";
        }
        $key = $this->input->get('key');
        if ($key != '') {
            $wer .= " AND (kt.WarehouseName LIKE ('%%$key%%') OR kt.WarehouseID LIKE ('%%$key%%'))";
        }

        $sql_cek = "select WarehouseID from ktv_warehouse_staff where UserId=?";
        $query = $this->db->query($sql_cek, array($_SESSION['userid']));
        $cek = $query->result_array();
        if ( ! empty($cek)) $wer .= " and kt.WarehouseID=" . $cek[0]['WarehouseID'];

        $sql = "SELECT WarehouseID,WarehouseName,Address,d.VillageID,d.Village,d.SubDistrictID,e.SubDistrict,
              e.DistrictID,f.District,f.ProvinceID,f.PartnerID,g.Province,Status,Year,Alias,Phone,PermanentEmployeeMale,
              PermanentEmployeeFemale,TemporaryEmployeeMale,TemporaryEmployeeFemale,Latitude,Longitude,Elevation,
              kt.DateCreated,kt.CreatedBy
            from ktv_warehouse kt
            left join ktv_village d on kt.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            left join ktv_province g on f.ProvinceID=g.ProvinceID
            WHERE TRUE AND kt.StatusCode != 'nullified' $wer %s
            ORDER BY WarehouseName";

        $query = $this->db->query(sprintf($sql, 'group by kt.WarehouseID'));
        
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'group by kt.WarehouseID'));
        $result['total'] = $query->num_rows();
        return $result;
    }

    function readDataObjIds($type, $id = '')
    {
        $sql ="";
        $sql_district = "
         select group_concat(DistrictID) district
         from ktv_private_staff kps
         left join ktv_district_partner kdp on kps.PartnerID=kdp.PartnerID
         where UserId=?";
        $query = $this->db->query($sql_district, array($_SESSION['userid']));
        if($query->num_rows() > 0){
            $result = $query->result_array();
            if ($result[0]['district'] != '') $add = 'and substr(VillageID,1,4) in (' . $result[0]['district'] . ')';
            if ($type == 'warehouse')
                $sql = "
                select WarehouseID id, concat(WarehouseID,' - ',WarehouseName) label
                from ktv_warehouse a
                LEFT JOIN ktv_supplychain_org ON OrgType='warehouse' AND OrgID=WarehouseID
                WHERE a.StatusCode='active' AND SupplychainID IS NULL $add";
            elseif ($type == 'trader')
                $sql = "
                select TraderID id, concat(TraderID,' - ',TraderName,IF(Company!='',concat(' (',Company,')'),'')) label
                from ktv_traders
                LEFT JOIN ktv_supplychain_org ON OrgType='trader' AND OrgID=TraderID
                WHERE SupplychainID IS NULL $add";
            elseif ($type == 'koperasi')
                $sql = "
                select CoopID id, concat(CoopID,' - ',CoopName) label
                from ktv_cooperatives
                LEFT JOIN ktv_supplychain_org ON OrgType='koperasi' AND OrgID=CoopID
                WHERE SupplychainID IS NULL $add";
            elseif ($type == 'cpg')
                $sql = "
                select CPGid id, concat(CPGid,' - ',GroupName) label
                from ktv_cpg
                LEFT JOIN ktv_supplychain_org ON OrgType='cpg' AND OrgID=CPGid
                WHERE SupplychainID IS NULL $add";
          elseif ($type=='sce')
             $sql = "
                select SceID id, concat('[',kcf.FarmerID,'] ',FarmerName) label
                from sce_farmer sf
                LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=sf.FarmerID
                LEFT JOIN ktv_supplychain_org ON OrgType='sce' AND OrgID=SceID
                WHERE SupplychainID IS NULL $add";
            if(strlen($sql) > 0){
                $query = $this->db->query($sql, array());

                $result = $query->result_array();
                if ($id != '') {
                    $sql = "
                       SELECT OrgID id, concat(OrgID,' - ',Name) label
                       FROM ktv_supplychain_org_view
                       WHERE SupplychainID=?";
                    $query = $this->db->query($sql, array($id));
                    $results = $query->result_array();
                    $result = array_merge($result, $results);
                }
            }
        }

        return $result;
    }

    function readData($id)
    {
        $sql = "
            select kso.*,kd.District,ksov.Name
            from ktv_supplychain_org kso
            left join ktv_supplychain_org_view ksov on kso.SupplychainID=ksov.SupplychainID
            left join ktv_district kd on substr(ksov.VillageID,1,4)=kd.DistrictID
            where kso.SupplychainID=?
            GROUP BY kso.SupplychainID";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        $sql_kab = "
            select kdb.District kab
            from ktv_supplychain_area ksa
            left join ktv_district kdb on ksa.DistrictID=kdb.DistrictID
            where SupplychainID=?";
        $query_kab = $this->db->query($sql_kab, array($id));
        $result_kab = $query_kab->result_array();
        for ($i=0;$i<sizeof($result_kab);$i++) $kk[] = $result_kab[$i]['kab'];
        $result[0]['kab'] = $kk;
        return $result[0];
    }

    function readDataWarehouse($type, $id)
    {
//        $sql = "
//            select distinct a.*, SupplychainID as id,e.SubDistrict as Kecamatan, f.District as Kabupaten,g.Province as Provinsi,
//               (a.LatDeg+(a.LatMin/60.0)+(a.LatSec/3600.0))*IF(a.LatDir='s',-1,1) LatSec,
//               (a.LongDeg+(a.LongMin/60.0)+(a.LongSec/3600.0))*IF(a.LongDir='w',-1,1) LongSec
//            from ktv_supplychain_org a
//            left join ktv_village d on a.VillageID=d.VillageID
//            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
//            left join ktv_district f on e.DistrictID=f.DistrictID
//            left join ktv_province g on f.ProvinceID=g.ProvinceID
//            WHERE Type=? and SupplychainID=?";
//        $query = $this->db->query($sql, array($type,$id));
//        $result = $query->result_array();
//        return $result[0];

        $sql = "select kt.PartnerID,h.PartnerName,WarehouseID,WarehouseName,Address,d.VillageID,d.Village,d.SubDistrictID,e.SubDistrict,
              e.DistrictID,f.District,f.ProvinceID,g.Province,Status,Year,Alias,Phone,PermanentEmployeeMale,
              PermanentEmployeeFemale,TemporaryEmployeeMale,TemporaryEmployeeFemale,Latitude,Longitude,Elevation,
              kt.DateCreated,kt.CreatedBy
            from ktv_warehouse kt
            left join ktv_village d on kt.VillageID=d.VillageID
            left join ktv_subdistrict e on d.SubDistrictID=e.SubDistrictID
            left join ktv_district f on e.DistrictID=f.DistrictID
            left join ktv_province g on f.ProvinceID=g.ProvinceID
            left join ktv_program_partner h ON kt.PartnerID = h.PartnerID
            where WarehouseID=$id";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result[0];
    }

    function createData($id, $type, $userid)
    {
        $sql = "
            insert into ktv_supplychain_org (OrgType, OrgID, DateCreated, CreatedBy)
            VALUES (?,?,   now(),?)";
        $query = $this->db->query($sql, array($type, $id, $userid));
        if ($query) {
            $results['id'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function createDataWarehouse($type, $LatSec, $LongSec, $Elevation, $userid)
    {
//
//        $d = array(
//                'PartnerID'=>$this->input->post('PartnerID'),
//                'WarehouseName'=> $this->input->post('WarehouseName'),
//                'Address'=> $this->input->post('Address'),
//                'VillageID'=> $this->input->post('Desa'),
//                'Status'=> $this->input->post('Status'),
//                'Year'=> $this->input->post('Year'),
//                'Alias'=> $this->input->post('Alias'),
//                'Phone'=> $this->input->post('Phone'),
//                'PermanentEmployeeMale'=> $this->input->post('PermanentEmployeeMale'),
//                'PermanentEmployeeFemale'=> $this->input->post('PermanentEmployeeFemale'),
//                'TemporaryEmployeeMale'=> $this->input->post('TemporaryEmployeeMale'),
//                'TemporaryEmployeeFemale'=> $this->input->post('TemporaryEmployeeFemale'),
//                'Latitude'=> $LatSec,
//                'Longitude'=> $LongSec,
//                'Elevation' => $Elevation,
////                'Photo'=>$Photo,
//                'DateCreated' => date('Y-m-d H:m:s'),
//                'CreatedBy' => $userid
//        );
//        $q = $this->db->insert('ktv_warehouse',$d);
//        $id = $this->db->insert_id();
//        if ($this->db->affected_rows()>0) {
//            $results['id'] = $id;
//            $results['success'] = true;
//            $results['message'] = "record created.";
//        } else {
//            $results['success'] = false;
//            $results['id'] = null;
//            $results['message'] = "Failed to create record";
//        }
//        return $results;
        $d = array(
            'PartnerID' => $this->getID('ktv_program_partner', 'PartnerName', 'PartnerID', $this->input->post('PartnerID')),
            'WarehouseName' => $this->input->post('WarehouseName'),
            'Address' => $this->input->post('Address'),
            'VillageID' => $this->getID('ktv_village', 'Village', 'VillageID', $this->input->post('Desa')),
            'Status' => $this->input->post('Status'),
            'Year' => $this->input->post('Year'),
            'Alias' => $this->input->post('Alias'),
            'Phone' => $this->input->post('Phone'),
            'PermanentEmployeeMale' => $this->input->post('PermanentEmployeeMale'),
            'PermanentEmployeeFemale' => $this->input->post('PermanentEmployeeFemale'),
            'TemporaryEmployeeMale' => $this->input->post('TemporaryEmployeeMale'),
            'TemporaryEmployeeFemale' => $this->input->post('TemporaryEmployeeFemale'),
            'Latitude' => $LatSec,
            'Longitude' => $LongSec,
            'Elevation' => $Elevation,
//                'Photo'=>$Photo,
            'DateCreated' => date('Y-m-d H:m:s'),
            'CreatedBy' => $userid
        );
        $q = $this->db->insert('ktv_warehouse', $d);
        $id = $this->db->insert_id();

        if ($this->db->affected_rows() > 0) {
            $results['success'] = true;
            $results['id'] = $id;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['id'] = null;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function updateData($id, $batchbu, $type, $PerwakilanKoperasi, $MekanismeMoisture, $MekanismeReward, $PembelianNonFarmer,
      $PembelianFarmer,$PembelianFarmerCert, $PembelianBatch, $TanpaKualitas, $KalkulasiPremium, $LabelKarung, $IsFakturNumber,
      $IstilahFarmer, $PemisahanBatch, $userid, $SupplychainID, $Kab,
      $isFF,$isFAQ,$IsMoistureKarung,$IsGeneratePacking,$GenerateWeightOrPackage,$IsSuratJalan,$FormulaNettoKarung,
      $StandardMoistureKarung,$FormulaNettoPrice,$FormulaNettoAkhir,
      $LabelFarmerCertified,$LabelFarmerNonCertified,$LabelNonFarmer,$LabelFAQ,$LabelFF,$IsDriver,$IsVehicleType,$IsDriverPosition,$IsDriverAddress,$IsPoliceNumber,$IsGeneratePo,$IsLockDestWeigh,$IsAutoBatch,$SentEmail) {
        $sql = "
            update ktv_supplychain_org
            set IsGenerateBuyingUnitBatch=?,OrgType=?, OrgID=?, PerwakilanKoperasi=?,MekanismeMoisture=?, MekanismeReward=?,PembelianNonFarmer=?,
               PembelianFarmer=?,PembelianFarmerCert=?,PembelianBatch=?,TanpaKualitas=?,KalkulasiPremium=?,LabelKarung=?,
               IsFakturNumber=?,IstilahFarmer=?,PemisahanBatch=?,DateUpdated=now(), LastModifiedBy=?,
               IsFF=?,IsFAQ=?,IsMoistureKarung=?,IsGeneratePacking=?,GenerateWeightOrPackage=?,IsSuratJalan=?,
               FormulaNettoKarung=?,StandardMoistureKarung=?,FormulaNettoPrice=?,FormulaNettoAkhir=?,
               LabelFarmerCertified=?,LabelFarmerNonCertified=?,LabelNonFarmer=?,LabelFAQ=?,LabelFF=?,IsDriver=?,IsVehicleType=?,IsDriverPosition=?,IsDriverAddress=?,IsPoliceNumber=?,IsGeneratePo=?,IsLockDestWeigh=?,IsAutoBatch=?,SentEmail=?
            where SupplychainID=?";
         $sql_delete_area = "DELETE FROM ktv_supplychain_area WHERE SupplychainID=?";
         $sql_add_area = "INSERT INTO ktv_supplychain_area(SupplychainID,DistrictID)
            SELECT ?,DistrictID FROM ktv_district WHERE District=?";
         $query = $this->db->query($sql, array($batchbu,$type, $id, $PerwakilanKoperasi, $MekanismeMoisture, $MekanismeReward,
            $PembelianNonFarmer, $PembelianFarmer,$PembelianFarmerCert, $PembelianBatch, $TanpaKualitas, $KalkulasiPremium,
            $LabelKarung,$IsFakturNumber,
            $IstilahFarmer, $PemisahanBatch, $userid,
            $isFF=='FF'?'1':'0',$isFAQ=='FAQ'?'1':'0',$IsMoistureKarung,$IsGeneratePacking,$GenerateWeightOrPackage,
            $IsSuratJalan,$FormulaNettoKarung,$StandardMoistureKarung,$FormulaNettoPrice,
            $FormulaNettoAkhir,$LabelFarmerCertified,$LabelFarmerNonCertified,$LabelNonFarmer,$LabelFAQ,$LabelFF,$IsDriver,$IsVehicleType,$IsDriverPosition,$IsDriverAddress,$IsPoliceNumber,$IsGeneratePo,$IsLockDestWeigh,$IsAutoBatch,$SentEmail,$SupplychainID));
            //echo $this->db->last_query();exit;
         //dipindah ke tab Baru
         //$this->db->query($sql_delete_area, array($SupplychainID));
         /*for ($i=0;$i<sizeof($Kab);$i++) {
            $this->db->query($sql_add_area, array($SupplychainID,$Kab[$i]));
         }*/
        if ($query) {
            $results['id'] = $id;
            $results['sid'] = $SupplychainID;
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function getID($table, $kolom, $vkolom, $id)
    {
        if ($id == '0') {
            return null;
        } else {
            $q = $this->db->get_where($table, array($kolom => $id));
//             echo $this->db->last_query();
            if ($q->num_rows() > 0) {
                $r = $q->row();
                return $r->$vkolom;
            } else {
                $q = $this->db->get_where($table, array($vkolom => $id));
                if ($q->num_rows() > 0) {
                    $r = $q->row();
                    return $r->$vkolom;
                } else {
                    return null;
                }
                return null;
            }
        }
        $q->free_result();
    }


    function updateDataWarehouse($type, $LatSec, $LongSec, $Elevation, $userid)
    {
        $d = array(
            'PartnerID' => $this->getID('ktv_program_partner', 'PartnerName', 'PartnerID', $this->input->post('PartnerID')),
            'WarehouseName' => $this->input->post('WarehouseName'),
            'Address' => $this->input->post('Address'),
            'VillageID' => $this->getID('ktv_village', 'Village', 'VillageID', $this->input->post('Desa')),
            'Status' => $this->input->post('Status'),
            'Year' => $this->input->post('Year'),
            'Alias' => $this->input->post('Alias'),
            'Phone' => $this->input->post('Phone'),
            'PermanentEmployeeMale' => $this->input->post('PermanentEmployeeMale'),
            'PermanentEmployeeFemale' => $this->input->post('PermanentEmployeeFemale'),
            'TemporaryEmployeeMale' => $this->input->post('TemporaryEmployeeMale'),
            'TemporaryEmployeeFemale' => $this->input->post('TemporaryEmployeeFemale'),
            'Latitude' => $LatSec,
            'Longitude' => $LongSec,
            'Elevation' => $Elevation,
//                'Photo'=>$Photo,
            'DateCreated' => date('Y-m-d H:m:s'),
            'CreatedBy' => $userid
        );
        $this->db->where('WarehouseID', $this->input->post('WarehouseID'));
        $query = $this->db->update('ktv_warehouse', $d);

        if ($this->db->affected_rows() > 0) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteData($id)
    {
        //$sql = "DELETE FROM ktv_supplychain_org WHERE SupplychainID=?";
        $sql="UPDATE ktv_warehouse SET StatusCode = 'nullified', LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW() WHERE WarehouseID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function readPartners()
    {

        $sql = "select PartnerID,PartnerName
                    from ktv_program_partner";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        $result['total'] = $query->num_rows();
        return $result;

    }

    //staff
    function readStaffs($id)
    {
//        $sql = "
//            select %s
//            from ktv_supplychain_staff kss
//            left join sys_user su on UserName=PrivateStaffEmail
//            left join ktv_farmer kcf on kss.FarmerID=kcf.FarmerID
//            WHERE StaffSupplychainID=? and Status!='nullified'
//            ORDER BY StaffName";
//        $select = 'kss.StaffID,su.UserId,StaffSupplychainID,IF(kss.FarmerID is null,"Non Farmer","Farmer") Status,
//            IF(kss.FarmerID is null,StaffName,FarmerName) FarmerID,kss.FarmerID FarmID,
//            IF(kss.FarmerID is null,StaffName,FarmerName) StaffName,Position,
//            IF(kss.FarmerID is null,PrivateCellphone,HandPhone) PrivateCellphone,
//            IF(kss.FarmerID is null,PrivateStaffEmail,"") PrivateStaffEmail,
//            IF(kss.FarmerID is null,date(StaffBirth),Birthdate) StaffBirth,
//            IF(StaffGender="1","Laki-laki",IF(StaffGender="2",
//            "Perempuan","")) as StaffGende,IF(kss.Education="1","Belum pernah sekolah",IF(kss.Education="2","Tidak tamat SD",
//            IF(kss.Education="3","Tamat SD, tidak melanjutkan",IF(kss.Education="4","Tamat SMP",IF(kss.Education="5","Tamat SMA/SMK",
//            IF(kss.Education="6","Tamat perguruan tinggi","")))))) as Educatio';
//        $query = $this->db->query(sprintf($sql,$select), array($id));
//        $result['data'] = $query->result_array();
//        $query = $this->db->query(sprintf($sql,'count(*) as total'), array($id));
//        $result['total'] = $query->row()->total;
//        return $result;

        $sql = "select StaffID,WarehouseID,StaffName,Phone,Email,date(StaffBirth) StaffBirth,Position,
                    IF(StaffGender='1','Laki-laki',IF(StaffGender='2','Perempuan','')) as StaffGender
                    from ktv_warehouse_staff
                    where WarehouseID=?";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
//        $query = $this->db->query(sprintf($sql,'count(*) as total'), array($id));
        $result['total'] = $query->num_rows();
        return $result;

    }

    function readStaffFarmers($districtId, $key, $start, $limit)
    {
        $sql = "
            select %s
            from ktv_farmer
            WHERE (FarmerID=? OR FarmerName like ?) and StatusCode!='nullified' and substr(VillageID,1,4)=?
            ORDER BY FarmerName
            %s";
        $query = $this->db->query(sprintf($sql, "FarmerID id,FarmerName name, HandPhone handphone, '' email, Birthdate birthday,
            Gender kelamin", 'LIMIT ?,?'), array($key, "%$key%", $districtId, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($key, "%$key%", $districtId));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createStaff($userid)
    {


        $this->db->trans_start();

        $StaffName = $this->input->post('Email');

        //cek
        $q = $this->db->get_where('sys_user', array('UserName' => $StaffName));
        if ($q->num_rows() > 0) {
            $results['success'] = false;
            $results['message'] = "User Name sudah ada";
        } else {
            //user
            $sql_user = "
                   INSERT INTO sys_user(UserRealName,UserName,UserActive)
                   VALUES (?,?,?)";
            $username = str_replace(' ', '_', $StaffName);
            $username = strtolower($username);
            $query = $this->db->query($sql_user, array($this->input->post('StaffName'), $username, 'No'));
            $user = $this->db->insert_id();
            $sql_user_group = "
                   INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
                   values (?,?,'1')";
            $query = $this->db->query($sql_user_group, array($user, null));
            //end user

            $d = array(
//                   'StaffID'=>$th,
                'UserId' => $user,
                'WarehouseID' => $this->input->post('WarehouseID'),
                'StaffName' => $this->input->post('StaffName'),
                'Phone' => $this->input->post('Phone'),
                'Email' => $this->input->post('Email'),
                'StaffBirth' => str_replace("T00:00:00", "", $this->input->post('StaffBirth')),
                'StaffGender' => ($this->input->post('StaffGender') == 'Laki-laki' ? '1' : '2'),
//                   'Photo',
                'IdentityNumber' => $this->input->post('IdentityNumber'),
                'VillageID' => $this->input->post('VillageID'),
                'Education' => $this->input->post('Education'),
                'FamilyMembers' => $this->input->post('FamilyMembers'),
                'Address' => $this->input->post('Address'),
                'Position' => $this->input->post('Position')
            );

            $query = $this->db->insert('ktv_warehouse_staff', $d);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $results['success'] = true;
                $results['message'] = "record created.";
            } else {
                $results['success'] = false;
                $results['message'] = "Failed to create record";
            }
        }


        return $results;
    }

    function updateStaff($WarehouseID, $StaffName, $Email, $Phone, $Position, $StaffBirthTmp, $StaffGender, $StaffBirth,
                         $userid, $StaffID)
    {

        $d = array(
//                   'StaffID'=>$th,
//                   'UserId'=>$userid,
            'WarehouseID' => $WarehouseID,
            'StaffName' => $StaffName,
            'Phone' => $Phone,
            'Email' => $Email,
            'StaffBirth' => str_replace("T00:00:00", "", $StaffBirth),
            'StaffGender' => ($StaffGender == 'Laki-laki' ? '1' : '2'),
//                   'Photo',
//                   'IdentityNumber'=> $this->input->post('IdentityNumber'),
//                   'VillageID'=> $this->input->post('VillageID'),
//                   'Education'=> $this->input->post('Education'),
//                   'FamilyMembers'=> $this->input->post('FamilyMembers'),
//                   'Address'=> $this->input->post('Address'),
            'Position' => $Position,
            'LastModifiedBy' => $userid,
            'DateUpdated' => date('Y-m-d H:m:s')
        );

        $this->db->where('StaffID', $StaffID);
        $query = $this->db->update('ktv_warehouse_staff', $d);
//        $sql = "
//            update ktv_supplychain_staff
//            set StaffSupplychainID=?,StaffName=?,FarmerID=?,PrivateCellphone=?,PrivateStaffEmail=?,OfficialCellphone=?,
//               OfficialStaffEmail=?,StaffBirth=?,StaffGender=?,
//               IdentityNumber=?,Education=?,FamilyMembers=?,Address=?, Position=?,DateUpdated=now(), LastModifiedBy=?
//            where StaffID=?";
//        $query = $this->db->query($sql, array($SupplychainID,$StaffName,$FarmerID,$PrivateCellphone,$PrivateStaffEmail,
//            $OfficialCellphone,$OfficialStaffEmail,$StaffBirth,$StaffGender,
//            $IdentityNumber,$Education,$FamilyMembers,$Address,$Position,$userid,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteStaff($id, $userId)
    {
        $sql = "
            DELETE FROM ktv_warehouse_staff WHERE StaffID=?";
        $sql_user_group = "
            DELETE FROM sys_user_group WHERE UserGroupUserId=?";
        $sql_user = "
            DELETE FROM sys_user WHERE UserId=?";
        $this->db->trans_start();
        $query = $this->db->query($sql, array($id));
//        $query = $this->db->query($sql_user_group, array($userId));
        //      $query = $this->db->query($sql_user, array($userId));
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
    //end staff

    //Quality Standard
    function readQualityStandards($id)
    {
        $sql = "
            select %s
            from ktv_supplychain_quality_standard
            WHERE StandardSupplychainID=?
            ORDER BY StandardName";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readQualityStandard($id)
    {
        $sql = "
            select *
            from ktv_supplychain_quality_standard
            WHERE StandardID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function readQualityStandardCombos($id)
    {
        $sql = "
            select StandardID as id,StandardName as label
            from ktv_supplychain_quality_standard
            WHERE StandardSupplychainID=?
            ORDER BY StandardName";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createQualityStandard($SupplychainID, $StandardName,$IsReward,$IsClaim,$userid)
    {
        $sql = "
            insert into ktv_supplychain_quality_standard (StandardSupplychainID, StandardName,IsReward,IsClaim,DateCreated, CreatedBy)
            VALUES (?,?,?,?,   now(),?)";
        $query = $this->db->query($sql, array($SupplychainID, $StandardName,$IsReward,$IsClaim,$userid));
        if ($query) {
            $results['id'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateQualityStandard($StandardName,$IsReward,$IsClaim,$userid, $id)
    {
        $sql = "
            update ktv_supplychain_quality_standard
            set StandardName=?, IsReward=?,IsClaim=?,DateUpdated=now(), LastModifiedBy=?
            where StandardID=?";
        $query = $this->db->query($sql, array($StandardName,$IsReward,$IsClaim,$userid, $id));//var_dump($this->db->last_query());die;
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteQualityStandard($id)
    {
        $sql = "
            DELETE FROM ktv_supplychain_quality_standard_detail WHERE StandardID=?";
        $query = $this->db->query($sql, array($id));
        $sql = "
            DELETE FROM ktv_supplychain_quality_standard WHERE StandardID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //end Quality

    //Premium
    function readPremiums($id)
    {
        $sql = "
            select %s
            from ktv_supplychain_premium
            WHERE PremiumSupplychainID=?
            ORDER BY PremiumDateStart desc";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readPremium($id)
    {
        $sql = "
            select *
            from ktv_supplychain_premium
            WHERE PremiumID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createPremium($PremiumSupplychainID, $PremiumDateStart, $PremiumDateEnd, $PersenPetani, $PersenBuyinUnit, $PersenPerwakilan, $USD,
                           $Kurs, $Rupiah, $userid)
    {
        $sql = "
            insert into ktv_supplychain_premium (PremiumSupplychainID,PremiumDateStart,PremiumDateEnd,PersenPetani,
            PersenBuyinUnit,PersenPerwakilan,USD,Kurs,Rupiah, DateCreated, CreatedBy)
            VALUES (?,?,?,?,   ?,?,?,?,?,   now(),?)";
        $query = $this->db->query($sql, array($PremiumSupplychainID, $PremiumDateStart, $PremiumDateEnd, $PersenPetani,
            $PersenBuyinUnit, $PersenPerwakilan, $USD, $Kurs, $Rupiah, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updatePremium($PremiumSupplychainID, $PremiumDateStart, $PremiumDateEnd, $PersenPetani, $PersenBuyinUnit, $PersenPerwakilan, $USD,
                           $Kurs, $Rupiah, $userid, $id)
    {
        $sql = "
            update ktv_supplychain_premium
            set PremiumSupplychainID=?,PremiumDateStart=?,PremiumDateEnd=?,PersenPetani=?,
               PersenBuyinUnit=?,PersenPerwakilan=?,USD=?,Kurs=?,Rupiah=?, DateUpdated=now(), LastModifiedBy=?
            where PremiumID=?";
        $query = $this->db->query($sql, array($PremiumSupplychainID, $PremiumDateStart, $PremiumDateEnd, $PersenPetani,
            $PersenBuyinUnit, $PersenPerwakilan, $USD, $Kurs, $Rupiah, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletePremium($id)
    {
        $sql = "
            DELETE FROM ktv_supplychain_premium WHERE PremiumID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //end Premium

    //Quality
    function readQualitys($id)
    {
        $sql = "
            select %s
            from ktv_supplychain_quality a
            LEFT JOIN ktv_supplychain_quality_standard b on a.StandardID=b.StandardID
            WHERE QualitySupplychainID=?
            ORDER BY QualityDateStart desc";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createQuality($QualitySupplychainID, $QualityDateStart,$QualityDateEnd, $StandardID,$userid)
    {
        $sql = "
            insert into ktv_supplychain_quality (QualitySupplychainID, QualityDateStart,QualityDateEnd, StandardID,
               DateCreated, CreatedBy)
            VALUES (?,?,?,?,   now(),?)";
        $query = $this->db->query($sql, array($QualitySupplychainID, $QualityDateStart,$QualityDateEnd, $StandardID,$userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateQuality($QualitySupplychainID, $QualityDateStart,$QualityDateEnd, $StandardID,$userid, $id)
    {
        $sql = "
            update ktv_supplychain_quality
            set QualitySupplychainID=?, QualityDateStart=?, QualityDateEnd=?,StandardID=?,
               DateUpdated=now(), LastModifiedBy=?
            where QualityID=?";
        $query = $this->db->query($sql, array($QualitySupplychainID, $QualityDateStart,$QualityDateEnd, $StandardID,
            $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteQuality($id)
    {
        $sql = "
            DELETE FROM ktv_supplychain_quality WHERE QualityID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //end Quality
    //Quality
    function readQualityVars($id) {
        $sql = "
            select %s
            from ktv_supplychain_var_quality a
            WHERE SupplychainID=?
            ORDER BY `Order`";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createQualityVar($VariableID, $SupplychainID, $Name, $Formula, $Code, $Order,$userid) {
        $sql = "
            insert into ktv_supplychain_var_quality (VariableID, SupplychainID, Name, Formula, Code, Order,
               DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,   now(),?)";
        $query = $this->db->query($sql, array($VariableID, $SupplychainID, $Name, $Formula, $Code, $Order,$userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateQualityVar($Name, $Formula, $Code, $Order,$userid,$VariableID) {
        $sql = "
            update ktv_supplychain_var_quality
            set Name=?, Formula=?, Code=?, Order=?, DateUpdated=now(), LastModifiedBy=?
            where VariableID=?";
        $query = $this->db->query($sql, array($Name, $Formula, $Code, $Order, $userid, $VariableID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteQualityVar($id) {
        $sql = "
            DELETE FROM ktv_supplychain_var_quality WHERE VariableID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //end Quality
    //Reward
    function readRewards($id)
    {
        $sql = "
            select %s
            from ktv_supplychain_reward
            WHERE RewardSupplychainID=?
            ORDER BY RewardDate desc";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createReward($RewardSupplychainID, $RewardDate,
                          $FFMoisture, $FFBeanCount, $FFWaste, $FFMouldy, $FFInsect, $FFSlaty,
                          $FAQMoisture, $FAQBeanCount, $FAQWaste, $FAQMouldy, $FAQInsect, $FAQSlaty,
                          $userid)
    {
        $sql = "
            insert into ktv_supplychain_reward (RewardSupplychainID, RewardDate,
               FFMoisture, FFBeanCount, FFWaste, FFMouldy, FFInsect, FFSlaty,
               FAQMoisture, FAQBeanCount, FAQWaste, FAQMouldy, FAQInsect, FAQSlaty,
               DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,   ?,now(),?)";
        $query = $this->db->query($sql, array($RewardSupplychainID, $RewardDate,
            $FFMoisture, $FFBeanCount, $FFWaste, $FFMouldy, $FFInsect, $FFSlaty,
            $FAQMoisture, $FAQBeanCount, $FAQWaste, $FAQMouldy, $FAQInsect, $FAQSlaty,
            $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateReward($RewardSupplychainID, $RewardDate,
                          $FFMoisture, $FFBeanCount, $FFWaste, $FFMouldy, $FFInsect, $FFSlaty,
                          $FAQMoisture, $FAQBeanCount, $FAQWaste, $FAQMouldy, $FAQInsect, $FAQSlaty,
                          $userid, $id)
    {
        $sql = "
            update ktv_supplychain_reward
            set RewardSupplychainID=?, RewardDate=?,
               FFMoisture=?, FFBeanCount=?, FFWaste=?, FFMouldy=?, FFInsect=?, FFSlaty=?,
               FAQMoisture=?, FAQBeanCount=?, FAQWaste=?, FAQMouldy=?, FAQInsect=?, FAQSlaty=?,
               DateUpdated=now(), LastModifiedBy=?
            where RewardID=?";
        $query = $this->db->query($sql, array($RewardSupplychainID, $RewardDate,
            $FFMoisture, $FFBeanCount, $FFWaste, $FFMouldy, $FFInsect, $FFSlaty,
            $FAQMoisture, $FAQBeanCount, $FAQWaste, $FAQMouldy, $FAQInsect, $FAQSlaty,
            $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteReward($id)
    {
        $sql = "
            DELETE FROM ktv_supplychain_reward WHERE RewardID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //end Reward

    //Kurs
    function readKurss($id){
        $sql = "
            select %s
            from ktv_supplychain_kurs
            WHERE KursSupplychainID=?
            ORDER BY KursDateStart desc";
        $query = $this->db->query(sprintf($sql,'*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }
    function createKurs($KursSupplychainID, $KursDateStart, $KursDateEnd, $kursNominal,$userid){
        $sql = "
            insert into ktv_supplychain_kurs (KursSupplychainID, KursDateStart,KursDateEnd, KursNominal,
               DateCreated, CreatedBy)
            VALUES (?,?,?,?,   now(),?)";
        $query = $this->db->query($sql, array($KursSupplychainID, $KursDateStart,$KursDateEnd,$kursNominal, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data berhasil disimpan.";
        } else {
            $results['success'] = false;
            $results['message'] = "Data gagal disimpan";
        }
        return $results;
    }

    function updateKurs($KursSupplychainID, $KursDateStart,$KursDateEnd, $kursNominal, $userid,$id){
        $sql = "
            update ktv_supplychain_kurs
            set KursSupplychainID=?, KursDateStart=?,KursDateEnd=?,KursNominal=?,DateUpdated=now(), LastModifiedBy=?
            where KursID=?";
        $query = $this->db->query($sql, array($KursSupplychainID, $KursDateStart,$KursDateEnd, $kursNominal, $userid,$id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data berhasil disimpan";
        } else {
            $results['success'] = false;
            $results['message'] = "Data gagal disimpan";
        }
        return $results;
    }

    function deleteKurs($id){
        $sql = "
            DELETE FROM ktv_supplychain_kurs WHERE KursID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data berhasil dihapus";
        } else {
            $results['success'] = false;
            $results['message'] = "Data gagal dihapus";
        }
        return $results;
    }
    //end Kurs

    //Price
    function readPrices($id)
    {
        $sql = "
            select %s
            from ktv_supplychain_price ktp
            left join ktv_district kd on ktp.DistrictID=kd.DistrictID
            WHERE PriceSupplychainID=?
            ORDER BY PriceDateStart desc";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createPrice($PriceSupplychainID, $PriceDateStart,$PriceDateEnd, $FFPrice, $FAQPrice, $districtID, $userid)
    {
        $sql = "
            insert into ktv_supplychain_price (PriceSupplychainID,PriceDateStart,PriceDateEnd,FFPrice, FAQPrice,
                  DateCreated, CreatedBy,DistrictID)
            VALUES (?,?,?,?,?,now(),?,((SELECT DistrictID from ktv_district where District=?)))";
        $query = $this->db->query($sql, array($PriceSupplychainID, $PriceDateStart,$PriceDateEnd, $FFPrice, $FAQPrice, $userid, $districtID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updatePrice($PriceSupplychainID, $PriceDateStart,$PriceDateEnd,$FFPrice, $FAQPrice, $districtID, $userid, $id)
    {
        $sql = "
            update ktv_supplychain_price
            set PriceSupplychainID=?,PriceDateStart=?,PriceDateEnd=?,FFPrice=?,FAQPrice=?,  DateUpdated=now(), LastModifiedBy=?,DistrictID=(SELECT DistrictID from ktv_district where District=?)
            where PriceID=?";
        $query = $this->db->query($sql, array($PriceSupplychainID, $PriceDateStart,$PriceDateEnd,$FFPrice, $FAQPrice, $userid, $districtID, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletePrice($id)
    {
        $sql = "
            DELETE FROM ktv_supplychain_price WHERE PriceID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //end Price

    //Package
    function readPackages($id)
    {
        $sql = "
            select %s
            from ktv_supplychain_package
            WHERE PackageSupplychainID=?
            ORDER BY PackageType";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function createPackage($PackageSupplychainID, $PackageType, $PackageWeight, $PackageCapasity, $userid)
    {
        $sql = "
            insert into ktv_supplychain_package (PackageSupplychainID,PackageType,PackageWeight, PackageCapasity,
               DateCreated, CreatedBy)
            VALUES (?,?,?,?,now(),?)";
        $query = $this->db->query($sql, array($PackageSupplychainID, $PackageType, $PackageWeight, $PackageCapasity, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updatePackage($PackageSupplychainID, $PackageType, $PackageWeight, $PackageCapasity, $userid, $id)
    {
        $sql = "
            update ktv_supplychain_package
            set PackageSupplychainID=?,PackageType=?,PackageWeight=?, PackageCapasity=?, DateUpdated=now(), LastModifiedBy=?
            where PackageID=?";
        $query = $this->db->query($sql, array($PackageSupplychainID, $PackageType, $PackageWeight, $PackageCapasity, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletePackage($id)
    {
        $sql = "
            DELETE FROM ktv_supplychain_package WHERE PackageID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //end Price

    //Relasi
    function readRelasis($id)
    {
        $sql = "
            select ksor.*,
               IF(OrgType='cpg',concat('[Kelompok Petani] ',GroupName),IF(OrgType='trader',
               concat('[Pedagang] ',concat(TraderName,IF(Company!='',concat(' (',Company,')'),''))),
               IF(OrgType='koperasi',concat('[Organisasi Petani] ',CoopName),
               IF(OrgType='sce',concat('[Professional Farmer] ',FarmerName),'')))) as label
            from ktv_supplychain_org_rel ksor
            left join ktv_supplychain_org kso on ksor.ChildOrgId=kso.SupplychainID
            left join ktv_traders kt on OrgType='trader' and TraderID=OrgID
            left join ktv_cooperatives kc on OrgType='koperasi' and CoopID=OrgID
            left join ktv_cpg kcpg on OrgType='cpg' and CPGid=OrgID
            left join ktv_warehouse kw on OrgType='warehouse' and Warehouseid=OrgID
            left join sce_farmer sf on OrgType='sce' and SceID=OrgID
            left join ktv_farmer kcf on OrgType='sce' and sf.FarmerID=kcf.FarmerID
            WHERE kso.StatusCode = 'active' AND ParentOrgId=?
            ORDER BY SupplychainID";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readRelasi($id)
    {
        $sql = "
            select *
            from ktv_supplychain_org_rel
            WHERE RelId=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function readRelasiChild($id)
    {
        $sql = "
            select SupplychainID as id, IF(OrgType='sce',concat('[Professional Farmer] ',kcf.FarmerID,' - ',FarmerName),
               IF(OrgType='cpg',concat('[Kelompok Petani] ',GroupName),IF(OrgType='trader',
               concat('[Pedagang] ',concat(TraderName,IF(Company!='',concat(' (',Company,')'),''))),
               IF(OrgType='koperasi',concat('[Organisasi Petani] ',CoopName),'')))) as label
            from ktv_supplychain_org
            left join sce_farmer sf on OrgType='sce' and SceID=OrgID
            left join ktv_farmer kcf on OrgType='sce' and sf.FarmerID=kcf.FarmerID
            left join ktv_traders kt on OrgType='trader' and TraderID=OrgID
            left join ktv_cooperatives kc on OrgType='koperasi' and CoopID=OrgID
            left join ktv_cpg kcpg on OrgType='cpg' and kcpg.CPGid=OrgID
            left join ktv_warehouse kw on OrgType='warehouse' and Warehouseid=OrgID
           where ktv_supplychain_org.StatusCode = 'active' AND IF(OrgType='sce',substr(kcf.VillageID,1,2),
                  IF(OrgType='cpg',substr(kcpg.VillageID,1,2),
                  IF(OrgType='trader',substr(kt.VillageID,1,2),
                  IF(OrgType='koperasi',substr(kc.VillageID,1,2),
                  IF(OrgType='warehouse',substr(kw.VillageID,1,2),''))))) and
               SupplychainID!=?";
        //$sql .= "and OrgType!=IF((select OrgType from ktv_supplychain_org where SupplychainID=?)='trader','koperasi','')";
        $sql .= "ORDER BY label";
        $query = $this->db->query($sql, array($id, $id, $id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createRelasi($ParentOrgId, $ChildOrgId, $NoKontrak, $StartDate, $EndDate, $File, $Description, $CreatedBy)
    {
        $sql = "
            insert into ktv_supplychain_org_rel (ParentOrgId,ChildOrgId,NoKontrak,StartDate,EndDate,File,Description,
               CreatedBy,DateCreated)
            VALUES (?,?,?,?,?,?,?,   ?,now())";
        $query = $this->db->query($sql, array($ParentOrgId, $ChildOrgId, $NoKontrak, $StartDate, $EndDate, $File, $Description, $CreatedBy));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateRelasi($ParentOrgId, $ChildOrgId, $NoKontrak, $StartDate, $EndDate, $File, $Description, $userid, $id)
    {
        $sql = "
            update ktv_supplychain_org_rel
            set ParentOrgId=?,ChildOrgId=?,NoKontrak=?,StartDate=?,EndDate=?,File=?,Description=?,
               DateUpdated=now(), LastModifiedBy=?
            where RelId=?";
        $query = $this->db->query($sql, array($ParentOrgId, $ChildOrgId, $NoKontrak, $StartDate, $EndDate, $File, $Description,
            $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteRelasi($id)
    {
        $sql = "
            DELETE FROM ktv_supplychain_org_rel WHERE RelId=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    //end Relasi
    function getTracebilityPerPetani($id, $awal, $akhir, $isPetani, $jenis = '')
    {
        $isPetani = ($isPetani == 'Non%20Farmer' ? 'NonFarmer' : 'Farmer');
        $sql = "select OrgID id,Name nama,OrgType jenis
            from ktv_supplychain_org_view
            where SupplychainID=?";
        $query = $this->db->query($sql, array($id));
        $data = $query->result_array();
        $result['data'] = $data[0];
        if ($data[0]['jenis'] == 'Gudang') $result['detail'] = $this->getTracebilityPerPetaniGudang($id, $awal, $akhir, $isPetani, $jenis);
        else $result['detail'] = $this->getTracebilityPerPetaniKoperasi($id, $awal, $akhir, $isPetani);

        return $result;
    }

    function getTracebilityPerPetaniGudang($id, $awal, $akhir, $isPetani, $jenis)
    {
        $sql = "select kcf.FarmerID,FarmerName,date(kst.DateTransaction) DateTransaction,ksb.SupplyBatchNumber,Name,
               sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey,
               (kst.FFVolumeBruto+kst.FAQVolumeBruto) FAQVolumeBruto,(kst.FFVolumeNetto+kst.FAQVolumeNetto)/ksb.VolumeNetto*ksbb.VolumeNetto FAQVolumeNetto,
               kst.FAQNetPrice,kst.FAQTotalPayment
            from ktv_supplychain_transaction kst
            left join ktv_farmer kcf on kst.SupplyID=kcf.FarmerID
            left join ktv_farmer_garden kcfg on kcf.FarmerID=kcfg.FarmerID
            inner join (select FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               kcfg.FarmerID = z.FarmerID and kcfg.GardenNr = z.GardenNr
            left join ktv_supplychain_batch ksb on ksb.SupplyBatchID=kst.SupplyBatchID
            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID

            left join ktv_supplychain_transaction kstb on kstb.SupplyID=ksb.SupplyBatchNumber
            left join ktv_supplychain_batch ksbb on ksbb.SupplyBatchID=kstb.SupplyBatchID
            where ksb.SupplyDestOrgID=? and (kst.DateTransaction between ? and ?) and kst.SupplyType=?
            GROUP BY kst.SupplyTransID
            ORDER BY FarmerID";
        $query = $this->db->query($sql, array($id, $awal, $akhir, $isPetani));
        $data = $query->result_array();
        return $data;
    }

    function getTracebilityPerPetaniKoperasi($id, $awal, $akhir, $isPetani, $jenis)
    {
        $sql = "select kcf.FarmerID,FarmerName,date(DateTransaction) DateTransaction,SupplyBatchNumber,Name,
               sum((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
               (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
               (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey,
               (FFVolumeBruto+FAQVolumeBruto) FAQVolumeBruto,(FFVolumeNetto+FAQVolumeNetto) FAQVolumeNetto,FAQNetPrice,FAQTotalPayment
            from ktv_supplychain_transaction kst
            left join ktv_farmer kcf on kst.SupplyID=kcf.FarmerID
            left join ktv_farmer_garden kcfg on kcf.FarmerID=kcfg.FarmerID
            inner join (select FarmerID,GardenNr,max(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) z on
               kcfg.FarmerID = z.FarmerID and kcfg.GardenNr = z.GardenNr
            left join ktv_supplychain_batch ksb on ksb.SupplyBatchID=kst.SupplyBatchID
            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
            where ksb.SupplyOrgID=? and (DateTransaction between ? and ?) and kst.SupplyType=?
            GROUP BY kst.SupplyTransID
            ORDER BY FarmerID";
        $query = $this->db->query($sql, array($id, $awal, $akhir, $isPetani));
        $data = $query->result_array();
        return $data;
    }

    function getTracebility($id, $awal, $akhir, $isPetani = 'Farmer', $jenis = '')
    {
        $isPetani = ($isPetani == 'Non%20Farmer' ? 'NonFarmer' : 'Farmer');
        $sql = "
            select OrgID id,Name nama,OrgType jenis
            from ktv_supplychain_org_view
            where SupplychainID=?";
        $query = $this->db->query($sql, array($id));
        $data = $query->result_array();
        $result['data'] = $data[0];
        //print_r($data[0]['jenis']);exit;
        if ($data[0]['jenis'] == 'Gudang') $result['detail'] = $this->getTracebilityDetailGudang($id, $awal, $akhir, $isPetani, $jenis);
        //elseif ($data[0]['jenis']=='Organisasi Petani')
        else $result['detail'] = $this->getTracebilityDetailKoperasi($id, $awal, $akhir, $isPetani);

        return $result;
    }

    function getPremium($id, $awal, $akhir, $isPetani = 'Farmer', $jenis = '')
    {
        $isPetani = ($isPetani == 'Non%20Farmer' ? 'NonFarmer' : 'Farmer');
        $sql = "
            select OrgID id,Name nama,OrgType jenis
            from ktv_supplychain_org_view
            where SupplychainID=?";
        $query = $this->db->query($sql, array($id));
        $data = $query->result_array();
        $result['data'] = $data[0];
        if ($data[0]['jenis'] == 'Gudang') $result['detail'] = $this->getPremiumDetailGudang($id, $awal, $akhir, $isPetani, $jenis);
        else $result['detail'] = $this->getPremiumDetailKoperasi($id, $awal, $akhir, $isPetani);

        return $result;
    }

    function getPremiumDetailKoperasi($id, $awal, $akhir, $isPetani)
    {
        $sql = "
            select ksov.Name,sum(ksb.VolumeNetto) koperasi_netto,sum(ksbb.VolumeNetto) warehouse_netto,PersenPerwakilan,Rupiah
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
            left join ktv_supplychain_org_view ksov on ksov.OrgID=ksb.PerwakilanOrgID
            left join ktv_supplychain_transaction kstb on kstb.SupplyType='Batch' and kstb.SupplyID=ksb.SupplyBatchNumber
            left join ktv_supplychain_batch ksbb on ksbb.SupplyBatchID=kstb.SupplyBatchID
            left join ktv_supplychain_premium ksp on ksp.PremiumSupplychainID=ksb.SupplyDestOrgID
            where ksb.SupplyOrgID=? and (ksb.SupplyBatchDate between ? and ?) and ksb.PerwakilanOrgID>0 AND kst.SupplyType=?
            group by ksov.OrgID";
        $query = $this->db->query($sql, array($id, $awal, $akhir, $isPetani));
        $data = $query->result_array();
        return $data;
    }

    function getPremiumPetani($id, $awal, $akhir, $isPetani = 'Farmer', $jenis = '')
    {
        $isPetani = ($isPetani == 'Non%20Farmer' ? 'NonFarmer' : 'Farmer');
        $sql = "
            select OrgID id,Name nama,OrgType jenis
            from ktv_supplychain_org_view
            where SupplychainID=?";
        $query = $this->db->query($sql, array($id));
        $data = $query->result_array();
        $result['data'] = $data[0];
        if ($data[0]['jenis'] == 'Gudang') $result['detail'] = $this->getPremiumPetaniGudang($id, $awal, $akhir, $isPetani, $jenis);
        else $result['detail'] = $this->getPremiumPetaniKoperasi($id, $awal, $akhir, $isPetani);

        return $result;
    }

    function getPremiumPetaniKoperasi($id, $awal, $akhir, $isPetani)
    {
        $sql = "
            select kcf.FarmerID,kcf.FarmerName,sum(kst.FAQVolumeNetto+kst.FFVolumeNetto) bruto,
              sum((kst.FAQVolumeNetto+kst.FFVolumeNetto)/ksb.VolumeNetto*ksbb.VolumeNetto) netto,PersenPetani,Rupiah
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
            left join ktv_farmer kcf on kcf.FarmerID=kst.SupplyID
            left join ktv_supplychain_org_view ksov on ksov.OrgID=ksb.PerwakilanOrgID
            left join ktv_supplychain_transaction kstb on kstb.SupplyType='Batch' and kstb.SupplyID=ksb.SupplyBatchNumber
            left join ktv_supplychain_batch ksbb on ksbb.SupplyBatchID=kstb.SupplyBatchID
            left join ktv_supplychain_premium ksp on ksp.PremiumSupplychainID=ksb.SupplyDestOrgID
            where ksb.SupplyOrgID=? and (ksb.SupplyBatchDate between ? and ?) and kst.SupplyType=?
            group by kst.SupplyBatchID";
        $query = $this->db->query($sql, array($id, $awal, $akhir, $isPetani));
        $data = $query->result_array();
        return $data;
    }

    function getTracebilityDetailGudang($id, $awal, $akhir, $isPetani, $jenis)
    {
        $sql = "
            select c.Name,a.SupplyBatchDate,a.VolumeBruto,a.VolumeNetto,a.SupplyBatchNumber batch,d.SupplyID,a.SupplyBatchID
            from ktv_supplychain_batch a
            left join ktv_supplychain_batch b on a.SupplyOrgID=b.SupplyDestOrgID
            left join ktv_supplychain_org_view c on b.SupplyOrgID=c.SupplychainID
            left join ktv_supplychain_transaction d on a.SupplyBatchID=d.SupplyBatchID
            left join ktv_supplychain_transaction de on b.SupplyBatchID=de.SupplyBatchID
            where a.SupplyOrgID=? and (a.SupplyBatchDate between ? and ?) AND (d.SupplyType=? OR de.SupplyType=?)
            GROUP BY a.SupplyBatchID";
        //echo $id;exit;
        $sql_detail = "
            select IFNULL(e.FarmerID,f.FarmerIdentity) FarmerID,IFNULL(e.FarmerName,f.FarmerName) FarmerName,d.SupplyType,
               IF(dd.Type='FAQ',d.FAQVolumeBruto,d.FFVolumeBruto) FAQVolumeBruto,
              IF(dd.Type='FF',d.FFNetPrice,d.FAQNetPrice) FAQNetPrice,
               IF(dd.Type='FF',d.FFTotalPayment,d.FAQTotalPayment) FAQTotalPayment,
               a.VolumeNetto,date(d.DateTransaction) tgl,FAQMoisture,Moisture,a.SupplyBatchNumber
            from ktv_supplychain_batch a
            left join ktv_supplychain_transaction b on a.SupplyBatchID=b.SupplyBatchID
            left join ktv_supplychain_batch c on b.SupplyID=c.SupplyBatchNumber
            left join ktv_supplychain_transaction d on c.SupplyBatchID=d.SupplyBatchID
            left join ktv_supplychain_transaction_dtl dd on dd.SupplyTransID=d.SupplyTransID
            left join ktv_farmer e on d.SupplyID=e.FarmerID and d.SupplyType='Farmer'
            left join ktv_supplychain_non_farmer f on d.SupplyID=f.FarmerID and d.SupplyType='NonFarmer'
            left join ktv_supplychain_quality g on a.SupplyOrgID=g.QualitySupplychainID
            where a.SupplyBatchID=? AND d.SupplyType=?";
        $query = $this->db->query($sql, array($id, $awal, $akhir, $isPetani, $isPetani));
        $data = $query->result_array();
        for ($i = 0; $i < sizeof($data); $i++) {
            $query = $this->db->query($sql_detail, array($data[$i]['SupplyBatchID'], $isPetani));
            $data[$i]['detail'] = $query->result_array();
        }
        //print_r($data);exit;
        return $data;
    }

    function getTracebilityDetailKoperasi($id, $awal, $akhir, $isPetani)
    {
        $sql = "
            select a.SupplyOrgID,c.Name,a.VolumeNetto,date(d.DateTransaction) DateTransaction,
               IFNULL(e.FarmerID,g.FarmerIdentity) FarmerID,IFNULL(e.FarmerName,g.FarmerName) FarmerName,
               IF(f.Type='FAQ',d.FAQVolumeBruto,d.FFVolumeBruto) FAQVolumeBruto,
               a.DestWeight,
               IF(f.Type='FAQ',FAQNetPrice,FFNetPrice) FAQNetPrice,
               IF(f.Type='FAQ',FAQTotalPayment,FFTotalPayment) FAQTotalPayment,
               Moisture,SupplyBatchNumber,
               IF(f.Type='FAQ',FAQMoisture,FFMoisture) FAQMoisture
            from ktv_supplychain_batch a
            left join ktv_supplychain_org_view c on a.SupplyOrgID=c.SupplychainID
            left join ktv_supplychain_transaction d on a.SupplyBatchID=d.SupplyBatchID
            left join ktv_supplychain_transaction_dtl f on f.SupplyTransID=d.SupplyTransID
            left join ktv_farmer e on d.SupplyID=e.FarmerID and d.SupplyType='Farmer'
            left join ktv_supplychain_non_farmer g on d.SupplyID=g.FarmerID and d.SupplyType='NonFarmer'
            left join ktv_supplychain_quality h on h.QualitySupplychainID=a.SupplyOrgID
            where a.SupplyOrgID=? and (a.SupplyBatchDate between ? and ?)";
        $query = $this->db->query($sql, array($id, $awal, $akhir));
        $data = $query->result_array();
        for ($i = 0; $i < sizeof($data); $i++) {
            $total[$data[$i]['SupplyBatchNumber']] += $data[$i]['FAQTotalPayment'];
        }
        $data['total'] = $total;
        return $data;
    }

    function getTracebilityDetail($id, $awal, $akhir)
    {
        $sql = "
            select ksb.SupplyBatchNumber, VolumeBruto,VolumeNetto,SupplyBatchDate,
              group_concat(kcf.FarmerID,'#',kcf.FarmerName,'#',FFVolumeBruto,'#',FAQVolumeBruto,'#',FFVolumeNetto,'#',
               FAQVolumeNetto,'#',FFNetPrice,'#',FAQNetPrice,'#',FFTotalPayment,'#',FAQTotalPayment separator '|') trans
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
            left join ktv_farmer kcf on kst.SupplyType='Farmer' and kst.SupplyID=kcf.FarmerID
            where SupplyOrgID=? and (SupplyBatchDate between ? and ?)
            group by ksb.SupplyBatchNumber";
        $query = $this->db->query($sql, array($id, $awal, $akhir));
        $data = $query->result_array();
        /*for ($i=0;$i<sizeof($data);$i++) {
            if ($data[$i][])
        }*/
        return $data;
    }

    //Perwakilan
    function readPerwakilans($id)
    {
        $sql = "
            select RelId,ParentOrgId,ChildOrgId,kt.TraderID,TraderName,SubDistrict,ks.SubDistrictID,
               concat(SubDistrict,' - ',Village) Village,kt.VillageID,Address,Handphone,
               TraderID id,concat(TraderID,' - ',TraderName,' - ',District) label
            from ktv_supplychain_org_rel ksor
            left join ktv_supplychain_org kso on ksor.ChildOrgId=kso.SupplychainID
            left join ktv_traders kt on OrgType='trader' and TraderID=OrgID
            left join ktv_subdistrict ks on SubDistrictID=substr(kt.VillageID,1,7)
            left join ktv_district kd on kd.DistrictID=substr(kt.VillageID,1,4)
            left join ktv_village kv on kv.VillageID=kt.VillageID
            WHERE ParentOrgId=?
            ORDER BY TraderName";
        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function createPerwakilan($ParentOrgId, $ChildOrgId, $TraderName, $VillageID, $Address, $Handphone, $CreatedBy)
    {
        $sql_trader = "
            insert into ktv_traders (TraderName,VillageID,Address,Handphone,CreatedBy,DateCreated)
            VALUES (?,?,?,?,  ?,now())";
        $sql_org = "
            insert into ktv_supplychain_org (OrgType,OrgID,CreatedBy,DateCreated)
            VALUES (?,?,  ?,now())";
        $sql_rel = "
            insert into ktv_supplychain_org_rel (ParentOrgId,ChildOrgId,CreatedBy,DateCreated)
            VALUES (?,?,  ?,now())";
        $query = $this->db->query($sql_trader, array($TraderName, $VillageID, $Address, $Handphone, $CreatedBy));
        $query = $this->db->query($sql_org, array('trader', $this->db->insert_id(), $CreatedBy));
        $query = $this->db->query($sql_rel, array($ParentOrgId, $this->db->insert_id(), $CreatedBy));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updatePerwakilan($ParentOrgId, $ChildOrgId, $TraderName, $VillageID, $Address, $Handphone, $userid, $id)
    {
        $sql_trader = "
            update ktv_traders
            set TraderName=?,VillageID=?,Address=?,Handphone=?,DateUpdated=now(), LastModifiedBy=?
            where TraderID=?";
        $query = $this->db->query($sql_trader, array($TraderName, $VillageID, $Address, $Handphone,
            $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletePerwakilan($id)
    {
        $sql_get = "SELECT SupplychainID,OrgID FROM ktv_supplychain_org_rel LEFT JOIN ktv_supplychain_org on SupplychainID=ChildOrgId
            WHERE RelId=?";
        $sql_rel = "DELETE FROM ktv_supplychain_org_rel WHERE RelId=?";
        $sql_org = "DELETE FROM ktv_supplychain_org WHERE SupplychainID=?";
        $sql_trader = "DELETE FROM ktv_traders WHERE TraderID=?";
        $query = $this->db->query($sql_get, array($id));
        $data = $query->result_array();

        $query = $this->db->query($sql_rel, array($id));
        $query = $this->db->query($sql_org, array($data[0]['SupplychainID']));
        $query = $this->db->query($sql_trader, array($data[0]['OrgID']));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function getPremiumKoperasi($awal, $akhir, $jenis = '', $userid = '')
    {
        if ($userid == '') $userid = $_SESSION['userid'];
        $isPetani = ($isPetani == 'Non%20Farmer' ? 'NonFarmer' : 'Farmer');
        $sql = "
            select Nameb,sum(VolumeBruto) bruto,sum(VolumeNetto) netto,PersenBuyinUnit,Rupiah from (
            select ksov.Name,ksovb.Name Nameb,ksb.VolumeBruto,ksb.VolumeNetto,PersenBuyinUnit,Rupiah,ksbb.SupplyOrgID,kstb.SupplyType,kss.UserId#,kssb.UserId
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
            #FROM
            left join ktv_supplychain_batch ksbb on ksbb.SupplyBatchNumber=kst.SupplyID
            left join ktv_supplychain_org_view ksovb on ksovb.SupplychainID=ksbb.SupplyOrgID
            left join ktv_supplychain_transaction kstb on ksbb.SupplyBatchID=kstb.SupplyBatchID
            #END FROM
            left join ktv_supplychain_org_rel ksor on ksor.ChildOrgId=ksb.SupplyOrgID and (ksb.SupplyBatchDate between ksor.StartDate and ksor.EndDate)
            left join ktv_supplychain_staff kss on kss.StaffSupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_staff kssb on kssb.StaffSupplychainID=ksbb.SupplyOrgID
            left join ktv_supplychain_premium ksp on ksp.PremiumSupplychainID=ksb.SupplyOrgID and (ksb.SupplyBatchDate between PremiumDateStart and PremiumDateEnd)
            where (ksb.SupplyBatchDate between ? and ?) and ksov.OrgType='Gudang' and kstb.SupplyType=?
              and (kss.UserId=? OR md5(kssb.UserId)=?)
            group by ksb.SupplyBatchID
            ) a group by SupplyOrgID";
        $query = $this->db->query($sql, array($awal, $akhir, $isPetani, $userid, $userid));
        $data = $query->result_array();
        return $data;
    }

    function getPremiumBu($awal, $akhir, $jenis = '')
    {
        $isPetani = ($isPetani == 'Non%20Farmer' ? 'NonFarmer' : 'Farmer');
        $sql = "
            select Nameb,PerwakilanOrgId,Namec,sum(VolumeBruto) bruto,sum(VolumeNetto) netto,PersenPerwakilan,Rupiah from (
            select ksov.Name,ksovb.Name Nameb,ksovc.Name Namec,ksbb.VolumeBruto,ksb.VolumeNetto,PersenBuyinUnit,PersenPerwakilan,Rupiah,
              ksbb.PerwakilanOrgId,ksbb.SupplyOrgID,kstb.SupplyType,kss.UserId#,kssb.UserId
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
            #FROM
            left join ktv_supplychain_batch ksbb on ksbb.SupplyBatchNumber=kst.SupplyID
            left join ktv_supplychain_org_view ksovb on ksovb.SupplychainID=ksbb.SupplyOrgID
            left join ktv_supplychain_transaction kstb on ksbb.SupplyBatchID=kstb.SupplyBatchID
            #END FROM
            #BU, 2=>1
            left join ktv_supplychain_org_view ksovc on ksovc.OrgID=ksbb.PerwakilanOrgId
            #END BU
            left join ktv_supplychain_org_rel ksor on ksor.ChildOrgId=ksb.SupplyOrgID and (ksb.SupplyBatchDate between ksor.StartDate and ksor.EndDate)
            left join ktv_supplychain_staff kss on kss.StaffSupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_staff kssb on kssb.StaffSupplychainID=ksbb.SupplyOrgID
            left join ktv_supplychain_premium ksp on ksp.PremiumSupplychainID=ksb.SupplyOrgID and (ksb.SupplyBatchDate between PremiumDateStart and PremiumDateEnd)
            where (ksb.SupplyBatchDate between ? and ?) and ksov.OrgType='Gudang' and kstb.SupplyType=?
              and (kss.UserId=? OR kssb.UserId=?)
            group by ksb.SupplyBatchID
            ) a group by PerwakilanOrgId";
        $query = $this->db->query($sql, array($awal, $akhir, $isPetani, $_SESSION['userid'], $_SESSION['userid']));
        $data = $query->result_array();
        return $data;
    }

    function getPremiumPetanii($awal, $akhir, $jenis = '')
    {
        $isPetani = ($isPetani == 'Non%20Farmer' ? 'NonFarmer' : 'Farmer');
        $sql = "
            select concat('[',FarmerID,'] ',FarmerName) Name,sum(FAQVolumeBruto) bruto,sum(netto) netto,PersenPetani,Rupiah,
               FarmerID,FarmerName,SubDistrict kec,Village desa
            from (
            select kcf.FarmerID,kcf.FarmerName,kstb.FAQVolumeBruto,kstb.FAQVolumeNetto,kstd.Moisture,
              ksbb.VolumeBruto,ksb.VolumeNetto,PersenPetani,Rupiah,kso.KalkulasiPremium,SubDistrict,Village,
              IF(kso.KalkulasiPremium='2',((100-(kstd.Moisture-7))/100*kstb.FAQVolumeBruto)/a.nett*ksb.VolumeNetto,kstb.FAQVolumeBruto/ksbb.VolumeBruto*ksb.VolumeNetto) netto
            from ktv_supplychain_batch ksb
            left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_org kso on ksov.SupplychainID=kso.SupplychainID
            left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
            #FROM
            left join ktv_supplychain_batch ksbb on ksbb.SupplyBatchNumber=kst.SupplyID
            left join ktv_supplychain_org_view ksovb on ksovb.SupplychainID=ksbb.SupplyOrgID
            left join ktv_supplychain_transaction kstb on ksbb.SupplyBatchID=kstb.SupplyBatchID
            left join (
              select SupplyBatchID,sum((100-(Moisture-7))/100*FAQVolumeBruto) nett
              from ktv_supplychain_transaction kst
              left join ktv_supplychain_transaction_dtl kstd on kst.SupplyTransID=kstd.SupplyTransID
              where SupplyType='Farmer'
              group by SupplyBatchID) a on ksbb.SupplyBatchID=a.SupplyBatchID
            #END FROM
            #petani
            left join ktv_farmer kcf on kcf.FarmerID=kstb.SupplyID
            left join ktv_supplychain_transaction_dtl kstd on kstd.SupplyTransID=kstb.SupplyTransID
            left join ktv_subdistrict ks on substr(kcf.VillageID,1,7)=ks.SubDistrictID
            left join ktv_village kv on kcf.VillageID=kv.VillageID
            #END petani
            left join ktv_supplychain_org_rel ksor on ksor.ChildOrgId=ksb.SupplyOrgID and (ksb.SupplyBatchDate between ksor.StartDate and ksor.EndDate)
            left join ktv_supplychain_staff kss on kss.StaffSupplychainID=ksb.SupplyOrgID
            left join ktv_supplychain_staff kssb on kssb.StaffSupplychainID=ksbb.SupplyOrgID
            left join ktv_supplychain_premium ksp on ksp.PremiumSupplychainID=ksb.SupplyOrgID and (ksb.SupplyBatchDate between PremiumDateStart and PremiumDateEnd)
            where (ksb.SupplyBatchDate between ? and ?) and ksov.OrgType='Gudang' and kstb.SupplyType=?
              and (kss.UserId=? OR kssb.UserId=?)
            group by ksb.SupplyBatchID,kstb.SupplyTransID
            ) a group by FarmerID";
        $query = $this->db->query($sql, array($awal, $akhir, $isPetani, $_SESSION['userid'], $_SESSION['userid']));
        $data = $query->result_array();
        return $data;
    }


    //premium
   function readTransPremiums($jenis, $district,$key, $awal, $akhir, $cpg = '',$paid = 'false', $start, $limit) {
      if ($jenis == 'koperasi') {
         $sql = "
            SELECT
            	1_orgid SupplychainID, `name`,SUM(1_bruto) bruto,SUM(1_netto) netto,
            	SUM(IF(DetailID IS NULL,0,1_netto)) terbayar,PersenBuyinUnit,IFNULL(Rupiah,USD*IF(Kurs<1,KursNominal,Kurs)) Rupiah,
                    COUNT(DISTINCT 1_transid) trans,
            	SUM(1_netto)/1000*PersenBuyinUnit/100*(IF(USD>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) total,
            	SUM(IF(DetailID IS NULL,0,1_netto))/1000*PersenBuyinUnit/100*(IF(usd>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) sudah,
            	SUM(1_netto)/1000*PersenBuyinUnit/100*(IF(USD>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) -
                  SUM(IF(DetailID IS NULL,0,1_netto))/1000*PersenBuyinUnit/100*(IF(usd>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) belum
            FROM (
            	SELECT a.*,d.Name `name`,DetailID,
            		c.SupplyOrgID 1_orgid,b.SupplyTransID 1_transid,SupplyID 1_supplyid,SupplyType 1_supplytype,
            		IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) 1_bruto,
            		IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto 1_netto,
            		d.OrgType 1_orgtype,
            		c.PerwakilanOrgID perwakilan
            	FROM (
            		SELECT a.SupplyOrgID wh_orgid, SupplyBatchDate wh_batchdate, SupplyID wh_supplyid,b.SupplyTransID wh_transid,
            			IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) wh_bruto,
            			IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) wh_netto
            		FROM ktv_supplychain_batch a
            		LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
            		LEFT JOIN ktv_supplychain_org_view c ON a.SupplyOrgID=c.SupplychainID
                  LEFT JOIN ktv_supplychain_staff kss ON kss.StaffSupplychainID=a.SupplyOrgID
            		WHERE c.OrgType='Gudang' AND (a.SupplyBatchDate BETWEEN ? AND ?) AND kss.UserId=?
            	) a
            	LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.wh_supplyid
            	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
            	LEFT JOIN ktv_supplychain_org_view d ON c.SupplyOrgID=d.SupplychainID
               #payment
               LEFT JOIN (
                  SELECT DetailID,PaymentSupplychainID,PaymentDestID,DetailSupplyTransID
                  FROM ktv_supplychain_payment kspb
                  LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
                  WHERE (PaymentDestType='Organisasi Petani')
               ) bb ON PaymentSupplychainID=wh_orgid AND PaymentDestID=c.SupplyOrgID AND
                  DetailSupplyTransID=b.SupplyTransID
               #end payment
            	LEFT JOIN (
            	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
            	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
            	   from ktv_supplychain_batch a
               	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
            	   GROUP BY a.SupplyBatchNumber
               ) z on z.batchnumber=c.SupplyBatchNumber
               where d.OrgType='Organisasi Petani'
               GROUP BY b.SupplyTransID
            ) a
            LEFT JOIN ktv_supplychain_premium ksp ON ksp.PremiumSupplychainID=a.wh_orgid AND
              (a.wh_batchdate BETWEEN PremiumDateStart AND PremiumDateEnd)
            LEFT JOIN ktv_supplychain_kurs ksk ON ksk.KursSupplychainID=a.wh_orgid AND (a.wh_batchdate BETWEEN
              ksk.KursDateStart AND ksk.KursDateEnd)
            WHERE `name` LIKE ? OR 1_orgid = ?
            GROUP BY 1_orgid";
      } else if ($jenis == 'bu') {
         if ($district!='') $dist = "SUBSTR(kcf.VillageID,1,4)='$district' and";
         $sql = "
            SELECT
            	2_orgid SupplychainID, `name`,SUM(2_bruto) bruto,SUM(2_netto) netto,
            	SUM(IF(DetailID IS NULL,0,2_netto)) terbayar,PersenPerwakilan,IFNULL(Rupiah,USD*IF(Kurs<1,KursNominal,Kurs)) Rupiah,
                    COUNT(DISTINCT 2_transid) trans,
            	SUM(2_netto)/1000*PersenPerwakilan/100*(IF(USD>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) total,
            	SUM(IF(DetailID IS NULL,0,2_netto))/1000*PersenPerwakilan/100*(IF(usd>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) sudah,
            	SUM(2_netto)/1000*PersenPerwakilan/100*(IF(USD>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah))-
            	SUM(IF(DetailID IS NULL,0,2_netto))/1000*PersenPerwakilan/100*(IF(usd>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) belum
            FROM (
               SELECT a.*,d.Name `name`,DetailID,
               	d.SupplychainID 2_orgid,IF(perwakilan IS NOT NULL,1_transid,b.SupplyTransID) 2_transid,
               	IF(perwakilan IS NOT NULL,1_supplyid,SupplyID) 2_supplyid,IF(perwakilan IS NOT NULL,1_supplytype,SupplyType) 2_supplytype,
               	IF(perwakilan IS NOT NULL,1_bruto,IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) 2_bruto,
               	IF(perwakilan IS NOT NULL,1_netto,IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*1_netto) 2_netto,
               	d.OrgType 2_orgtype
               FROM (
               	SELECT a.*,
               		c.SupplyOrgID 1_orgid,b.SupplyTransID 1_transid,SupplyID 1_supplyid,SupplyType 1_supplytype,
               		IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) 1_bruto,
               		IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto 1_netto,
               		d.OrgType 1_orgtype,
               		c.PerwakilanOrgID perwakilan
               	FROM (
               		SELECT a.SupplyOrgID wh_orgid, SupplyBatchDate wh_batchdate, SupplyID wh_supplyid,
               			IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) wh_bruto,
               			IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) wh_netto
               		FROM ktv_supplychain_batch a
               		LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
               		LEFT JOIN ktv_supplychain_org_view c ON a.SupplyOrgID=c.SupplychainID
                     LEFT JOIN ktv_supplychain_staff kss ON kss.StaffSupplychainID=a.SupplyOrgID
               		WHERE c.OrgType='Gudang' AND (a.SupplyBatchDate BETWEEN ? AND ?) AND kss.UserId=?
               	) a
               	LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.wh_supplyid
               	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
               	LEFT JOIN ktv_supplychain_org_view d ON c.SupplyOrgID=d.SupplychainID
               	LEFT JOIN (
               	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
               	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
               	   from ktv_supplychain_batch a
                  	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
               	   GROUP BY a.SupplyBatchNumber
                  ) z on z.batchnumber=c.SupplyBatchNumber
               ) a
               LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.1_supplyid
               LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
               LEFT JOIN ktv_supplychain_org_view d ON IF(perwakilan IS NOT NULL,d.OrgID,d.SupplychainID) = IF(perwakilan IS NOT NULL,perwakilan,c.SupplyOrgID)
            	LEFT JOIN (
            	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
            	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
            	   from ktv_supplychain_batch a
               	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
            	   GROUP BY a.SupplyBatchNumber
               ) z on z.batchnumber=c.SupplyBatchNumber
               #payment
               LEFT JOIN (
                  SELECT DetailID,PaymentSupplychainID,PaymentDestID,DetailSupplyTransID
                  FROM ktv_supplychain_payment kspb
                  LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
                  WHERE (PaymentDestType='Pedagang' OR PaymentDestType='sce')
               ) bb ON PaymentSupplychainID=1_orgid AND PaymentDestID=d.SupplychainID AND
                  DetailSupplyTransID=IF(perwakilan IS NOT NULL,1_transid,b.SupplyTransID)
               #end payment
               GROUP BY IF(perwakilan IS NOT NULL,1_transid,b.SupplyTransID)
            ) a
            LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=2_supplyid
            LEFT JOIN ktv_supplychain_premium ksp ON ksp.PremiumSupplychainID=a.wh_orgid AND
              (a.wh_batchdate BETWEEN PremiumDateStart AND PremiumDateEnd)
            LEFT JOIN ktv_supplychain_kurs ksk ON ksk.KursSupplychainID=a.wh_orgid AND (a.wh_batchdate BETWEEN
              ksk.KursDateStart AND ksk.KursDateEnd)
            WHERE $dist `name` LIKE ? OR 2_orgid = ?
            GROUP BY 2_orgid";
        } else if ($jenis == 'petani') {
         if ($district!='') $dist = "substr(VillageID,1,4)='$district' and ";
         if ($cpg!='') $cpg = "CPGid='$cpg' and ";
         $sql = "
            SELECT
            	3_orgid SupplychainID, `name`,SUM(3_bruto) bruto,SUM(3_netto) netto,CPGid,GroupName,
            	SUM(IF(DetailID IS NULL,0,3_netto)) terbayar,PersenPetani,IFNULL(Rupiah,USD*IF(Kurs<1,KursNominal,Kurs)) Rupiah,
                    COUNT(DISTINCT 3_transid) trans,
            	SUM(3_netto)/1000*PersenPetani/100*(IF(USD>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) total,
            	SUM(IF(DetailID IS NULL,0,3_netto))/1000*PersenPetani/100*(IF(usd>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) sudah,
            	SUM(3_netto)/1000*PersenPetani/100*(IF(USD>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah))-
            	SUM(IF(DetailID IS NULL,0,3_netto))/1000*PersenPetani/100*(IF(usd>0,USD*IF(Kurs<1,KursNominal,Kurs),Rupiah)) belum
            FROM (
               SELECT a.*,kcf.CPGid,(SELECT concat('[',CPGid,']',GroupName) AS GroupName from ktv_cpg WHERE CPGid = kcf.CPGid) AS GroupName,kcf.FarmerID 3_orgid,concat('[',kcf.FarmerID,'] ',FarmerName) `name`,2_bruto 3_bruto,
                  2_transid 3_transid,kcf.VillageID,DetailID,IF(Moisture is null,2_netto,3_nettoo) 3_netto
               FROM (
                  SELECT a.*,
                  	d.OrgID 2_orgid,IF(perwakilan IS NOT NULL,1_transid,b.SupplyTransID) 2_transid,
                  	IF(perwakilan IS NOT NULL,1_supplyid,SupplyID) 2_supplyid,IF(perwakilan IS NOT NULL,1_supplytype,SupplyType) 2_supplytype,
                  	IF(perwakilan IS NOT NULL,1_bruto,IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*c.VolumeBruto) 2_bruto,
                  	IF(perwakilan IS NOT NULL,1_netto,IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*1_netto) 2_netto,
                  	(100-(IFNULL(bb.Moisture,7)-7))/100*(IF(bb.Type='FAQ',b.FAQVolumeBruto,b.FFVolumeBruto))/nett*1_netto 3_nettoo,
                  	d.OrgType 2_orgtype,bb.Moisture
                  FROM (
                  	SELECT a.*,
                  		c.SupplyOrgID 1_orgid,b.SupplyTransID 1_transid,SupplyID 1_supplyid,SupplyType 1_supplytype,
                  		IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*c.VolumeBruto 1_bruto,
                  		IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto 1_netto,
                  		d.OrgType 1_orgtype,
                  		c.PerwakilanOrgID perwakilan
                  	FROM (
                  		SELECT a.SupplyOrgID wh_orgid, SupplyBatchDate wh_batchdate, SupplyID wh_supplyid,
                  			IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) wh_bruto,
                  			IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) wh_netto
                  		FROM ktv_supplychain_batch a
                  		LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
                  		LEFT JOIN ktv_supplychain_org_view c ON a.SupplyOrgID=c.SupplychainID
                        LEFT JOIN ktv_supplychain_staff kss ON kss.StaffSupplychainID=a.SupplyOrgID
                  		WHERE c.OrgType='Gudang' AND (a.SupplyBatchDate BETWEEN ? AND ?) AND kss.UserId=?
                  	) a
                  	LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.wh_supplyid
                  	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
                  	LEFT JOIN ktv_supplychain_org_view d ON c.SupplyOrgID=d.SupplychainID
                  	LEFT JOIN (
                  	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
                  	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
                  	   from ktv_supplychain_batch a
                     	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
                  	   GROUP BY a.SupplyBatchNumber
                     ) z on z.batchnumber=c.SupplyBatchNumber
                  ) a
                  LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.1_supplyid
                  LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
                  LEFT JOIN ktv_supplychain_transaction_dtl bb ON b.SupplyTransID=bb.SupplyTransID
                  LEFT JOIN ktv_supplychain_org_view d ON IF(perwakilan IS NOT NULL,d.OrgID,d.SupplychainID) = IF(perwakilan IS NOT NULL,perwakilan,c.SupplyOrgID)
                  LEFT JOIN (
                     SELECT SUM((100-(IFNULL(b.Moisture,7)-7))/100*(IF(b.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto))) nett,
                        SupplyBatchID
                     FROM ktv_supplychain_transaction a
                     LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
                     GROUP BY SupplyBatchID
                  ) ab ON c.SupplyBatchID=ab.SupplyBatchID
               	LEFT JOIN (
               	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
               	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
               	   from ktv_supplychain_batch a
                  	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
               	   GROUP BY a.SupplyBatchNumber
                  ) z on z.batchnumber=c.SupplyBatchNumber
               ) a
               left join ktv_farmer kcf on kcf.FarmerID=a.2_supplyid
               #payment
               left join (
                  select * from ktv_supplychain_payment kspb
                  LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
               ) aa on PaymentSupplychainID=1_orgid and PaymentDestType='Farmer' AND
                  PaymentDestID=kcf.FarmerID and DetailSupplyTransID=2_transid
               #end payment
               group by 2_transid
            ) a
            LEFT JOIN ktv_supplychain_premium ksp ON ksp.PremiumSupplychainID=a.wh_orgid AND
              (a.wh_batchdate BETWEEN PremiumDateStart AND PremiumDateEnd)
            LEFT JOIN ktv_supplychain_kurs ksk ON ksk.KursSupplychainID=a.wh_orgid AND (a.wh_batchdate BETWEEN
              ksk.KursDateStart AND ksk.KursDateEnd)
            WHERE $dist $cpg `name` LIKE ? OR 3_orgid = ?
            GROUP BY 3_orgid ";
            if($paid == 'false'){
              $sql .= 'HAVING belum > 0';
            }
        }
        if ($jenis != '') {
            $query = $this->db->query($sql, array($awal, $akhir, $_SESSION['userid'], "%$key%", $key));
            //echo $this->db->last_query();exit;
            return $query->result_array();
        }
    }

    function readTransPremium_old($id, $jenis, $awal, $akhir)
    {
        if ($jenis == 'koperasi' OR $jenis == 'bu') {
            $sql_kiri = "
            select ksovb.Name pemberi, ksov.Name penerima,ksovb.SupplychainID pemberi_id, ksov.SupplychainID penerima_id,
               ksov.OrgType penerima_type
            from ktv_supplychain_org_view ksov
            left join ktv_supplychain_org_rel ksor on ksor.ChildOrgId=ksov.SupplychainID
            left join ktv_supplychain_org_view ksovb on ksor.ParentOrgId=ksovb.SupplychainID
            where ksov.SupplychainID=?";
        } else {
            $sql_kiri = "
            select ksov.Name pemberi, concat('[',FarmerID,'] ',FarmerName) penerima,SupplychainID pemberi_id, FarmerID penerima_id,
               'Farmer' penerima_type
            from ktv_farmer kcf
            left join ktv_supplychain_org_view ksov on substr(kcf.VillageID,1,4)=substr(ksov.VillageID,1,4) and OrgType='Organisasi Petani'
            where FarmerID=?";
        }
        $qkiri = $this->db->query($sql_kiri, array($id));//var_dump($this->db->last_query());die;
        $kiri = $qkiri->result_array();
        $data['kiri'] = $kiri[0];
        return $data;
    }

    /**
     * @author Ardi <ardiantoro@koltiva.com>
     * @param String $id (Penerima Premium)
     * @param String $jenis (Jenis Penerima: Farmer / Buyingunit / Koperasi)
     * @param String $awal (awal periode transaksi)
     * @param String $akhir (akhir periode transaksi)
     * @return Array data pemberi dan penerima
     */
    function readTransPremium($id, $jenis, $awal, $akhir)
    {
        $output = false;

        //cek dulu jenis penerimanya apa? lalu dapatkan district penerimanya
        switch ($jenis) {
            case 'bu': //buying unit -> pedagang, untuk sce?
                $penerima = $this->_getBuyingUnitData($id);
                $penerima['type'] = 'Pedagang';
                break;
            case 'petani': //petani
                $penerima = $this->_getFarmerData($id);
                $penerima['type'] = 'Farmer';
                break;
            case 'koperasi': //koperasi
                $penerima = $this->_getCoopData($id);
                $penerima['type'] = 'Organisasi Petani';
                break;
        }

        //setelah dapat districtnya, dicari koperasi pada district yang sama
        $koperasi = $this->_getDistrictCoop($penerima['district']);

        //kalo dapet koperasinya lanjutkan pembayaran premium kalo ngga dapet di exit
        if($koperasi) {
            $output['kiri'] = array(
                'pemberi' => $koperasi['name'],
                'pemberi_id' => $koperasi['id'],
                'penerima' => $penerima['name'],
                'penerima_id' => $penerima['id'],
                'penerima_type' => $penerima['type']
            );
        }

        return $output;
    }

    private function _getBuyingUnitData($BuyingUnitID){

        /*$this->db->select('substr(ktv_traders.VillageID, 1, 4) as DistrictID, TraderName',false);
        $this->db->from('ktv_supplychain_org');
        $this->db->join('ktv_traders','ktv_traders.TraderID = ktv_supplychain_org.OrgID','left');
        $this->db->where('OrgType','trader');
        $this->db->where('SupplychainID',$BuyingUnitID);*/
        $sql = "SELECT substr(IF(a.OrgType='trader',b.VillageID,d.VillageID), 1, 4) as DistrictID, IF(a.OrgType='trader',TraderName,CONCAT('[',d.FarmerID,'] ',d.FarmerName)) as TraderName
                FROM ktv_supplychain_org a
                LEFT JOIN ktv_traders b ON b.TraderID = a.OrgID
                LEFT JOIN sce_farmer c ON a.OrgID=c.SceID
                LEFT JOIN ktv_farmer d ON c.FarmerID=d.FarmerID
                WHERE (OrgType =  'trader' OR OrgType='sce') AND SupplychainID=?";
        $Q = $this->db->query($sql,array($BuyingUnitID));
        //echo "<pre>".$this->db->last_query();exit;
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return array('district' => $row->DistrictID, 'id' => $BuyingUnitID, 'name' => $row->TraderName);
        }

        return false;
    }

    private function _getFarmerData($FarmerID){

        $this->db->select('substr(ktv_farmer.VillageID, 1, 4) as DistrictID,FarmerName',false);
        $this->db->from('ktv_farmer');
        $this->db->where('FarmerID',$FarmerID);
        $Q = $this->db->get(); //var_dump($this->db->last_query());die;
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return array('district' => $row->DistrictID, 'id' => $FarmerID, 'name' => $row->FarmerName);
        }

        return false;
    }

    private function _getCoopData($BuyingUnitID){

        $this->db->select('substr(ktv_cooperatives.VillageID, 1, 4) as DistrictID,CoopName',false);
        $this->db->from('ktv_supplychain_org');
        $this->db->join('ktv_cooperatives','ktv_cooperatives.CoopID = ktv_supplychain_org.OrgID','left');
        $this->db->where('OrgType','koperasi');
        $this->db->where('SupplychainID',$BuyingUnitID);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return array('district' => $row->DistrictID, 'id' => $BuyingUnitID, 'name' => $row->CoopName);
        }

        return false;
    }

    private function _getDistrictCoop($DistrictID){

        $this->db->select('ktv_supplychain_area.SupplychainID,CoopName');
        $this->db->from('ktv_supplychain_area');
        $this->db->join('ktv_supplychain_org','ktv_supplychain_org.SupplychainID = ktv_supplychain_area.SupplychainID');
        $this->db->join('ktv_cooperatives','ktv_cooperatives.CoopID = ktv_supplychain_org.OrgID');
        $this->db->where('ktv_supplychain_area.DistrictID',$DistrictID);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return array('id' => $row->SupplychainID, 'name' => $row->CoopName);;
        }

        return false;
    }

    function readDistrict(){
         $sql = "
         SELECT DISTINCT kd.DistrictID id, District label
      	FROM rpt_traceability rt
         LEFT JOIN ktv_supplychain_staff kss ON kss.StaffSupplychainID=wh_supplychainid
         LEFT JOIN ktv_district kd ON substr(farmer_id,1,4)=kd.DistrictID
         WHERE kss.UserId=?";
        $qkiri = $this->db->query($sql, array($_SESSION['userid']));
        //echo "<pre>".$this->db->last_query();exit;
        return $qkiri->result_array();
    }

    function readCPG($district) {
        $this->db->select('CPGid as id, concat("[",CPGid,"]",GroupName) as label',false);
        $this->db->from('ktv_cpg');
        $this->db->where('substr(VillageID,1,4) = "'.$district.'"',null,false);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            return $Q->result_array();
        }
        return array();
    }

    function readTransPremiumDetailsRpt($id, $jenis, $district, $awal, $akhir) {
        //var_dump($id);
        //var_dump($jenis);
        //var_dump($district);
        //var_dump($awal);
        //var_dump($akhir);
        switch($jenis) {
            case 'petani':
                return $this->_calculatePremiumPetani($id, $jenis, $district, $awal, $akhir);
                break;
            case 'bu':
                return $this->_calculatePremiumBuyingUnit($id, $jenis, $district, $awal, $akhir);
                break;
            case 'koperasi':
                return $this->_calculatePremiumKoperasi($id, $jenis, $district, $awal, $akhir);
                break;
        }
    }

    private function _getPaidPremium($transid,$type) {
        $this->db->select('SUM(DetailPremium) AS total,SUM(DetailBerat) AS netto',false);
        $this->db->from('ktv_supplychain_payment_detail');
        $this->db->join('ktv_supplychain_payment','ktv_supplychain_payment.PaymentID = ktv_supplychain_payment_detail.DetailPaymentID','left');
        if(is_array($transid)){
          $this->db->where_in('DetailSupplyTransID',$transid);
        } else {
          $this->db->where('DetailSupplyTransID',$transid);
        }
        if($type == 'Pedagang'){
          $this->db->where_in('PaymentDestType',array('Pedagang','sce'));
        } else {
          $this->db->where('PaymentDestType',$type);
        }

        $Q = $this->db->get(); //var_dump($this->db->last_query());die;
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return array('total' => $row->total, 'netto' => $row->netto);
        }
        return array('total' => 0, 'netto' => 0);
    }

    private function _calculatePremiumPetani($id, $jenis, $district, $awal, $akhir) {
        $output = array();
        $this->db->select('wh_po as DestPO,if(1_transid is null,2_transid,1_transid) as SupplyTransID,if(1_date is null,2_date,1_date) as DateTransaction,if(1_bruto is null,2_bruto,1_bruto) as bruto,farmer_netto as netto,wh_persenfarmer,wh_persenbs,wh_persenkoperasi,wh_dollar,wh_kurs',false);
        $this->db->from('rpt_traceability');
        $this->db->where('(wh_date between "'.$awal.'" and "'.$akhir.'")',null,false);//var_dump($this->db->_compile_select());die;
        $this->db->where('farmer_id',$id);//var_dump($this->db->_compile_select());die;
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();

            /**
             * Menghitung total
             * formula = (netto / 1000) * ((persenpetani/100) * premiumamount) * kurs
             *           (88.03 / 1000) * ((0.6) * 200) * 12905 = 136,323.258
             *
             * khusus untuk aceh: jika petani langsung menjual kepada koperasi, maka persentase premium pedagang digabung ke koperasi
             */
            foreach($result as $key => $value) {

                $total = (((floor($value['netto']*100) / 100)/1000) * ($value['wh_persenfarmer']/100)) * $value['wh_dollar'] * $value['wh_kurs'];
                $sudah = $this->_getPaidPremium($value['SupplyTransID'],'Farmer');
                $belum = $total;
                $netto = (floor($value['netto']*100) / 100);
                if($sudah['total'] > 0){
                  $belum = 0;
                  $netto = (floor($sudah['netto']*100) / 100);
                }

                //$belum = ($total - $sudah);
                $output[$key]['DestPO'] = $value['DestPO'];
                $output[$key]['SupplyTransID'] = $value['SupplyTransID'];
                $output[$key]['DateTransaction'] = $value['DateTransaction'];
                $output[$key]['netto'] = $netto;
                $output[$key]['bruto'] = number_format($value['bruto'],2);
                $output[$key]['total'] = number_format($total,2);
                $output[$key]['belum'] = number_format($belum,2);
                $output[$key]['sudah'] = number_format($sudah['total'],2);

            }
            //echo json_encode($output);die;

        }

        return $output;
    }

    private function _getTransID($id,$type = 'pedagang') {
      if($type == 'koperasi'){
        $this->db->select('2_transid as transid',false);
      } else {
        $this->db->select('1_transid as transid',false);
      }
      $this->db->from('rpt_traceability');
      $this->db->where('wh_transid',$id);
      $Q = $this->db->get();//var_dump($this->db->last_query());die;
      if($Q->num_rows() > 0){
        $result = $Q->result_array();
        $output = array();
        foreach($result as $keys => $values) {
          array_push($output,$values['transid']);
        }
        return $output;
      }

      return array();
    }

    private function _calculatePremiumBuyingUnit($id, $jenis, $district, $awal, $akhir) {

        $output = array();
        $this->db->select('wh_po as DestPO,wh_transid as SupplyTransID,1_date as DateTransaction,sum(if(1_bruto is null,2_bruto,1_bruto)) as bruto,sum(farmer_netto) as netto,wh_persenbs,wh_dollar,wh_kurs',false);
        $this->db->from('rpt_traceability');
        $this->db->where('(wh_date between "'.$awal.'" and "'.$akhir.'")',null,false);//var_dump($this->db->_compile_select());die;
        $this->db->where('1_supplychainid',$id);
        $this->db->group_by('wh_po');
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();

            /**
             * Menghitung total
             * formula = (netto / 1000) * ((persenbs/100) * premiumamount) * kurs
             *           (88.03 / 1000) * ((0.6) * 200) * 12905 = 136,323.258
             */
            foreach($result as $key => $value) { //var_dump($value['SupplyTransID']);die;
                $total = (($value['netto']/1000) * ($value['wh_persenbs']/100)) * $value['wh_dollar'] * $value['wh_kurs'];
                $rptTransID = $this->_getTransID($value['SupplyTransID'],'pedagang');//var_dump($rptTransID);die;
                $sudah = $this->_getPaidPremium($rptTransID,'Pedagang');
                //$sudah = $this->_getPaidPremium($value['SupplyTransID'],'Pedagang'); //knp cuma 1 begitu ya?

                $belum = $total;
                $netto = $value['netto'];
                if($sudah['total'] > 0){
                  $belum = 0;
                  $netto = $sudah['netto'];
                }

                //$belum = ($total - $sudah);
                $output[$key]['DestPO'] = $value['DestPO'];
                $output[$key]['SupplyTransID'] = $value['SupplyTransID'];
                $output[$key]['DateTransaction'] = $value['DateTransaction'];
                $output[$key]['netto'] = number_format($netto,2);
                $output[$key]['bruto'] = number_format($value['bruto'],2);
                $output[$key]['total'] = number_format($total,2);
                $output[$key]['belum'] = number_format($belum,2);
                $output[$key]['sudah'] = number_format($sudah['total'],2);


            }
            //echo json_encode($output);die;

        }

        return $output;
    }

    private function _calculatePremiumKoperasi($id, $jenis, $district, $awal, $akhir) {

        $output = array();
        $this->db->select('1_orgtype as tipe,wh_po as DestPO,wh_transid as SupplyTransID,wh_date as DateTransaction,sum(2_bruto) as bruto,sum(farmer_netto) as netto,wh_persenfarmer,wh_persenbs,wh_persenkoperasi,wh_dollar,wh_kurs',false);
        $this->db->from('rpt_traceability');
        $this->db->where('(wh_date between "'.$awal.'" and "'.$akhir.'")',null,false);//var_dump($this->db->_compile_select());die;
        $this->db->where('2_supplychainid',$id);//var_dump($this->db->_compile_select());die;
        $this->db->group_by('wh_po');
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            $aceh = false;
            if(substr($district, 1, 2) == 11) { $aceh = true; } //deteksi apakah ada di provinsi aceh?

            /**
             * Menghitung total
             * formula = (netto / 1000) * ((persenkoperasi/100) * premiumamount) * kurs
             *           (88.03 / 1000) * ((0.6) * 200) * 12905 = 136,323.258
             */
            foreach($result as $key => $value) {

                $persentase = $value['wh_persenkoperasi'];

                //khusus untuk aceh: jika petani langsung menjual kepada koperasi, maka persentase premium pedagang
                if($aceh && $value['tipe'] == 'perwakilan') { $persentase = ($value['wh_persenkoperasi'] + $value['wh_persenbs']); }

                $total = (($value['netto']/1000) * ($persentase/100)) * $value['wh_dollar'] * $value['wh_kurs'];
                $rptTransID = $this->_getTransID($value['SupplyTransID'],'koperasi');//var_dump($rptTransID);die;
                $sudah = $this->_getPaidPremium($rptTransID,'Organisasi Petani');
                $belum = $total;
                $netto = $value['netto'];
                if($sudah['total'] > 0){
                  $belum = 0;
                  $netto = $sudah['netto'];
                }

                //$belum = ($total - $sudah);
                $output[$key]['DestPO'] = $value['DestPO'];
                $output[$key]['SupplyTransID'] = $value['SupplyTransID'];
                $output[$key]['DateTransaction'] = $value['DateTransaction'];
                $output[$key]['netto'] = number_format($netto,2);
                $output[$key]['bruto'] = number_format($value['bruto'],2);
                $output[$key]['total'] = number_format($total,2);
                $output[$key]['belum'] = number_format($belum,2);
                $output[$key]['sudah'] = number_format($sudah['total'],2);

            }
            //echo json_encode($output);die;

        }

        return $output;
    }

    function readTransPremiumDetails($id, $jenis, $district, $awal, $akhir){
      $sql_umum = "
         SELECT
            a.SupplyID 1_orgid,IF(a.FAQVolumeBruto>0,a.FAQVolumeBruto,a.FFVolumeBruto) 1_bruto,
               IF(a.FAQVolumeNetto>0,a.FAQVolumeNetto,a.FFVolumeNetto)/b.VolumeNetto*nett 1_netto,
               a.SupplyTransID 1_transid,
               a.DateTransaction 1_transdate,
            IFNULL(b.PerwakilanOrgID,b.SupplyOrgID) 2_orgid,
               IF(a.FAQVolumeBruto>0,a.FAQVolumeBruto,a.FFVolumeBruto)/b.VolumeBruto*d.VolumeBruto 2_bruto,
               IF(a.FAQVolumeNetto>0,a.FAQVolumeNetto,a.FFVolumeNetto)/b.VolumeNetto*d.VolumeNetto 2_netto,
               IF(b.PerwakilanOrgID IS NULL,'0','1') is_perwakilan,
               IF(b.PerwakilanOrgID IS NULL,c.SupplyTransID,a.SupplyTransID) 2_transid,
               IF(b.PerwakilanOrgID IS NULL,c.DateTransaction,a.DateTransaction) 2_transdate,
            IF(b.PerwakilanOrgID IS NULL,d.SupplyOrgID,b.SupplyOrgID) 3_orgid,
               IF(a.FAQVolumeBruto>0,a.FAQVolumeBruto,a.FFVolumeBruto)/b.VolumeBruto*IF(b.PerwakilanOrgID IS NULL,f.VolumeBruto,d.VolumeBruto) 3_bruto,
               IF(a.FAQVolumeNetto>0,a.FAQVolumeNetto,a.FFVolumeNetto)/b.VolumeNetto*IF(b.PerwakilanOrgID IS NULL,f.VolumeBruto,d.VolumeBruto) 3_netto,
               IF(b.PerwakilanOrgID IS NULL,e.SupplyTransID,c.SupplyTransID) 3_transid,
               IF(b.PerwakilanOrgID IS NULL,e.DateTransaction,c.DateTransaction) 3_transdate,
            IFNULL(f.SupplyOrgID,d.SupplyOrgID) wh_orgid,
               IFNULL(f.SupplyBatchDate,d.SupplyBatchDate) wh_datebatch,
               IFNULL(f.DestPO,d.DestPO) DestPO
         #level farmer
         FROM ktv_supplychain_transaction a
         LEFT JOIN ktv_supplychain_transaction_dtl aa ON a.SupplyTransID=aa.SupplyTransID
         LEFT JOIN (
            SELECT SUM((100-(IFNULL(b.Moisture,7)-7))/100*(IF(b.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto))) nett,SupplyBatchID
            FROM ktv_supplychain_transaction a
            LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
            GROUP BY SupplyBatchID
         ) ab ON a.SupplyBatchID=ab.SupplyBatchID
         LEFT JOIN ktv_supplychain_batch b ON a.SupplyBatchID=b.SupplyBatchID
         #level 1(trader,sce,perwakilan)
         LEFT JOIN ktv_supplychain_transaction c ON b.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
         LEFT JOIN ktv_supplychain_batch d ON c.SupplyBatchID=d.SupplyBatchID
         #level 2(koperasi)
         LEFT JOIN ktv_supplychain_transaction e ON d.SupplyBatchNumber=e.SupplyID AND e.SupplyType='Batch'
         LEFT JOIN ktv_supplychain_batch f ON e.SupplyBatchID=f.SupplyBatchID
         WHERE %s=? AND (IFNULL(f.SupplyBatchDate,d.SupplyBatchDate) BETWEEN ? AND ?) AND a.SupplyType='Farmer'
         GROUP BY a.SupplyTransID";
        if ($jenis == 'koperasi') {
            $sql_kanan = "
               SELECT
                    (SELECT
                        ddd.DestPO
                    FROM
                        ktv_supplychain_transaction aaa
                        LEFT JOIN ktv_supplychain_batch bbb ON aaa.SupplyBatchID=bbb.SupplyBatchID
                        LEFT JOIN ktv_supplychain_transaction ccc ON bbb.SupplyBatchNumber=ccc.SupplyID
                        LEFT JOIN ktv_supplychain_batch ddd ON ccc.SupplyBatchID=ddd.SupplyBatchID
                    WHERE
                        aaa.SupplyTransID=1_transid) AS DestPO,
                  1_transid SupplyTransID,1_transdate DateTransaction,1_bruto bruto,1_netto netto,
                  round(1_netto/1000*PersenBuyinUnit/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),2) total,
                  round(IF(PaymentID is NULL,1_netto/1000*PersenBuyinUnit/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),0),2) belum,
                  round(IF(PaymentID is NOT NULL,1_netto/1000*PersenBuyinUnit/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),0),2) sudah
               FROM (
               	SELECT a.*,d.Name `name`,b.DateTransaction 1_transdate,DestPO,
               		c.SupplyOrgID 1_orgid,b.SupplyTransID 1_transid,SupplyID 1_supplyid,SupplyType 1_supplytype,
               		IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) 1_bruto,
               		IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto 1_netto,
               		d.OrgType 1_orgtype,
               		c.PerwakilanOrgID perwakilan
               	FROM (
               		SELECT a.SupplyOrgID wh_orgid, SupplyBatchDate wh_batchdate, SupplyID wh_supplyid,
               			IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) wh_bruto,
               			IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) wh_netto
               		FROM ktv_supplychain_batch a
               		LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
               		LEFT JOIN ktv_supplychain_org_view c ON a.SupplyOrgID=c.SupplychainID
               		WHERE c.OrgType='Gudang' AND (a.SupplyBatchDate BETWEEN ? AND ?)
               	) a
               	LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.wh_supplyid
               	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
               	LEFT JOIN ktv_supplychain_org_view d ON c.SupplyOrgID=d.SupplychainID
               	LEFT JOIN (
               	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
               	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
               	   from ktv_supplychain_batch a
                  	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
               	   GROUP BY a.SupplyBatchNumber
                  ) z on z.batchnumber=c.SupplyBatchNumber
               ) a
               LEFT JOIN ktv_supplychain_staff kss ON kss.StaffSupplychainID=a.wh_orgid
               LEFT JOIN ktv_supplychain_org_view ksov ON ksov.SupplychainID=a.1_orgid
               #payment
               LEFT JOIN ktv_supplychain_payment kspb ON PaymentDestType='Organisasi Petani' AND
                  PaymentSupplychainID=wh_orgid and PaymentDestID=a.1_orgid
               LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID AND
                  kspbd.DetailSupplyTransID=1_transid
               #end payment
               LEFT JOIN ktv_supplychain_premium ksp ON ksp.PremiumSupplychainID=a.wh_orgid AND
                  (a.wh_batchdate BETWEEN PremiumDateStart AND PremiumDateEnd)
               LEFT JOIN ktv_supplychain_kurs ksk ON ksk.KursSupplychainID=a.wh_orgid AND (a.wh_batchdate BETWEEN
                  ksk.KursDateStart AND ksk.KursDateEnd)
               WHERE 1_orgid=?
               GROUP BY wh_supplyid";
        } else if ($jenis == 'bu') {
            $sql_kanan = "
               SELECT
                    (SELECT
                        ddd.DestPO
                    FROM
                        ktv_supplychain_transaction aaa
                        LEFT JOIN ktv_supplychain_batch bbb ON aaa.SupplyBatchID=bbb.SupplyBatchID
                        LEFT JOIN ktv_supplychain_transaction ccc ON bbb.SupplyBatchNumber=ccc.SupplyID
                        LEFT JOIN ktv_supplychain_batch ddd ON ccc.SupplyBatchID=ddd.SupplyBatchID
                    WHERE
                        aaa.SupplyTransID=2_transid) AS DestPO,
                  2_transid SupplyTransID,2_transdate DateTransaction,2_bruto bruto,2_netto netto,
                  round(2_netto/1000*PersenPerwakilan/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),2) total,
                  round(IF(DetailID is NULL,2_netto/1000*PersenPerwakilan/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),0),2) belum,
                  round(IF(DetailID is NOT NULL,2_netto/1000*PersenPerwakilan/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),0),2) sudah
               FROM (
                  SELECT a.*,d.Name `name`,DestPO,
                  	c.SupplyOrgID 2_orgid,IF(perwakilan IS NOT NULL,1_transid,b.SupplyTransID) 2_transid,
                  	IF(perwakilan IS NOT NULL,1_transdate,b.DateTransaction) 2_transdate,
                  	IF(perwakilan IS NOT NULL,1_supplyid,SupplyID) 2_supplyid,IF(perwakilan IS NOT NULL,1_supplytype,SupplyType) 2_supplytype,
                  	IF(perwakilan IS NOT NULL,1_bruto,IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*c.VolumeBruto) 2_bruto,
                  	IF(perwakilan IS NOT NULL,1_netto,IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*1_netto) 2_netto,
                  	d.OrgType 2_orgtype
                  FROM (
                  	SELECT a.*,
                  		c.SupplyOrgID 1_orgid,b.SupplyTransID 1_transid,b.DateTransaction 1_transdate,SupplyID 1_supplyid,SupplyType 1_supplytype,
                  		IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*c.VolumeBruto 1_bruto,
                  		IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto 1_netto,
                  		d.OrgType 1_orgtype,
                  		c.PerwakilanOrgID perwakilan
                  	FROM (
                  		SELECT a.SupplyOrgID wh_orgid, SupplyBatchDate wh_batchdate, SupplyID wh_supplyid,
                  			IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) wh_bruto,
                  			IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) wh_netto
                  		FROM ktv_supplychain_batch a
                  		LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
                  		LEFT JOIN ktv_supplychain_org_view c ON a.SupplyOrgID=c.SupplychainID
                  		WHERE c.OrgType='Gudang' AND (a.SupplyBatchDate BETWEEN ? AND ?)
                  	) a
                  	LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.wh_supplyid
                  	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
                  	LEFT JOIN ktv_supplychain_org_view d ON c.SupplyOrgID=d.SupplychainID
                  	LEFT JOIN (
                  	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
                  	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
                  	   from ktv_supplychain_batch a
                     	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
                  	   GROUP BY a.SupplyBatchNumber
                     ) z on z.batchnumber=c.SupplyBatchNumber
                  ) a
                  LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.1_supplyid
                  LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
                  LEFT JOIN ktv_supplychain_org_view d ON IF(perwakilan IS NOT NULL,d.OrgID,d.SupplychainID) = IF(perwakilan IS NOT NULL,perwakilan,c.SupplyOrgID)
               	LEFT JOIN (
               	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
               	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
               	   from ktv_supplychain_batch a
                  	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
               	   GROUP BY a.SupplyBatchNumber
                  ) z on z.batchnumber=c.SupplyBatchNumber
                  WHERE d.SupplychainID=?
               ) a
               LEFT JOIN ktv_supplychain_staff kss ON kss.StaffSupplychainID=a.wh_orgid
               LEFT JOIN ktv_supplychain_org_view ksov ON ksov.SupplychainID=a.2_orgid
               #payment
               LEFT JOIN ktv_supplychain_payment kspb ON (PaymentDestType='sce' OR PaymentDestType='Pedagang') AND
                  PaymentSupplychainID=a.1_orgid and PaymentDestID=a.2_orgid
               LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID AND
                  kspbd.DetailSupplyTransID=2_transid
               #end payment
               LEFT JOIN ktv_supplychain_premium ksp ON ksp.PremiumSupplychainID=a.wh_orgid AND
                  (a.wh_batchdate BETWEEN PremiumDateStart AND PremiumDateEnd)
               LEFT JOIN ktv_supplychain_kurs ksk ON ksk.KursSupplychainID=a.wh_orgid AND (a.wh_batchdate BETWEEN
                  ksk.KursDateStart AND ksk.KursDateEnd)
               GROUP BY 2_transid";
        } else if ($jenis == 'petani') {
            if ($district!='') $dist = "substr(kcf.VillageID,1,4)='$district' and ";
            $sql_kanan = "
               SELECT
                    (SELECT
                        ddd.DestPO
                    FROM
                        ktv_supplychain_transaction aaa
                        LEFT JOIN ktv_supplychain_batch bbb ON aaa.SupplyBatchID=bbb.SupplyBatchID
                        LEFT JOIN ktv_supplychain_transaction ccc ON bbb.SupplyBatchNumber=ccc.SupplyID
                        LEFT JOIN ktv_supplychain_batch ddd ON ccc.SupplyBatchID=ddd.SupplyBatchID
                    WHERE
                        aaa.SupplyTransID=2_transid) AS DestPO,
                  2_transid SupplyTransID,2_transdate DateTransaction,2_bruto bruto,2_netto netto,
                  round(2_netto/1000*PersenPetani/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),2) total,
                  round(IF(DetailID is NULL,2_netto/1000*PersenPetani/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),0),2) belum,
                  round(IF(DetailID is NOT NULL,2_netto/1000*PersenPetani/100*(IF(usd>0,usd*IF(Kurs<1,KursNominal,Kurs),Rupiah)),0),2) sudah
               FROM (
                  SELECT a.*,DestPO,
                  	d.OrgID 2_orgid,IF(perwakilan IS NOT NULL,1_transid,b.SupplyTransID) 2_transid,
                  	IF(perwakilan IS NOT NULL,1_transdate,b.DateTransaction) 2_transdate,
                  	IF(perwakilan IS NOT NULL,1_supplyid,SupplyID) 2_supplyid,IF(perwakilan IS NOT NULL,1_supplytype,SupplyType) 2_supplytype,
                  	IF(perwakilan IS NOT NULL,1_bruto,IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*c.VolumeBruto) 2_bruto,
                  	IF(perwakilan IS NOT NULL,1_netto,IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*1_netto) 2_netto,
                  	(100-(IFNULL(bb.Moisture,7)-7))/100*(IF(bb.Type='FAQ',b.FAQVolumeBruto,b.FFVolumeBruto))/nett*1_netto 3_netto,
                  	d.OrgType 2_orgtype
                  FROM (
                  	SELECT a.*,
                  		c.SupplyOrgID 1_orgid,b.SupplyTransID 1_transid,b.DateTransaction 1_transdate,
                        SupplyID 1_supplyid,SupplyType 1_supplytype,
                  		IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*c.VolumeBruto 1_bruto,
                  		IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto 1_netto,
                  		d.OrgType 1_orgtype,
                  		c.PerwakilanOrgID perwakilan
                  	FROM (
                  		SELECT a.SupplyOrgID wh_orgid, SupplyBatchDate wh_batchdate, SupplyID wh_supplyid,
                  			IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) wh_bruto,
                  			IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) wh_netto
                  		FROM ktv_supplychain_batch a
                  		LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
                  		LEFT JOIN ktv_supplychain_org_view c ON a.SupplyOrgID=c.SupplychainID
                  		WHERE c.OrgType='Gudang' AND (a.SupplyBatchDate BETWEEN ? AND ?)
                  	) a
                  	LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.wh_supplyid
                  	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
                  	LEFT JOIN ktv_supplychain_org_view d ON c.SupplyOrgID=d.SupplychainID
                  	LEFT JOIN (
                  	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
                  	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
                  	   from ktv_supplychain_batch a
                     	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
                  	   GROUP BY a.SupplyBatchNumber
                     ) z on z.batchnumber=c.SupplyBatchNumber
                  ) a
                  LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.1_supplyid
                  LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
                  LEFT JOIN ktv_supplychain_transaction_dtl bb ON b.SupplyTransID=bb.SupplyTransID
                  LEFT JOIN ktv_supplychain_org_view d ON IF(perwakilan IS NOT NULL,d.OrgID,d.SupplychainID) = IF(perwakilan IS NOT NULL,perwakilan,c.SupplyOrgID)
                  LEFT JOIN (
                     SELECT SUM((100-(IFNULL(b.Moisture,7)-7))/100*(IF(b.Type='FAQ',a.FAQVolumeBruto,a.FFVolumeBruto))) nett,SupplyBatchID
                     FROM ktv_supplychain_transaction a
                     LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
                     GROUP BY SupplyBatchID
                  ) ab ON c.SupplyBatchID=ab.SupplyBatchID
               	LEFT JOIN (
               	   SELECT a.SupplyBatchNumber batchnumber, sum(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) total_netto,
               	        sum(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) total_bruto
               	   from ktv_supplychain_batch a
                  	LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=a.SupplyBatchID
               	   GROUP BY a.SupplyBatchNumber
                  ) z on z.batchnumber=c.SupplyBatchNumber
               ) a
               left join ktv_farmer kcf on kcf.FarmerID=a.2_supplyid
               LEFT JOIN ktv_supplychain_staff kss ON kss.StaffSupplychainID=a.wh_orgid
               LEFT JOIN ktv_supplychain_org_view ksov ON ksov.SupplychainID=a.1_orgid
               #payment
               left join (
                  select * from ktv_supplychain_payment kspb
                  LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
               ) aa on PaymentSupplychainID=1_orgid and PaymentDestType='Farmer' AND
                  PaymentDestID=a.2_supplyid and DetailSupplyTransID=2_transid
               #end payment
               LEFT JOIN ktv_supplychain_premium ksp ON ksp.PremiumSupplychainID=a.wh_orgid AND
                  (a.wh_batchdate BETWEEN PremiumDateStart AND PremiumDateEnd)
               LEFT JOIN ktv_supplychain_kurs ksk ON ksk.KursSupplychainID=a.wh_orgid AND (a.wh_batchdate BETWEEN
                  ksk.KursDateStart AND ksk.KursDateEnd)
               WHERE $dist kcf.FarmerID=?
               GROUP BY 2_transid";
        }
        //echo $sql;exit;
        if ($sql_kanan != '') {
            $qkanan = $this->db->query($sql_kanan, array($awal, $akhir, $id));
            //echo $this->db->last_query();exit;
            $data = $qkanan->result_array();
        } else {
            $qkanan = $this->db->query($sql, array($awal, $akhir, $id));
            $data = $qkanan->result_array();
        }
        return $data;

    }

    function readTransPremiumKuitansis($id, $jenis, $awal, $akhir)
    {
        if ($jenis == 'koperasi') {
            //PersenBuyinUnit/100*
            $sql_kanan = "
               SELECT ksp.*
               FROM ktv_supplychain_payment ksp
               LEFT JOIN ktv_supplychain_payment_detail kspd ON kspd.DetailPaymentID=ksp.PaymentID
               LEFT JOIN ktv_supplychain_transaction kst ON kspd.DetailSupplyTransID=kst.SupplyTransID
               LEFT JOIN ktv_supplychain_batch ksb ON kst.SupplyBatchID=ksb.SupplyBatchID
               WHERE PaymentDestID=? AND PaymentDestType='Organisasi Petani' AND (ksb.SupplyBatchDate BETWEEN ? AND ?)
               GROUP BY ksp.PaymentID";
            $qkanan = $this->db->query($sql_kanan, array($id, $awal, $akhir));
            $data = $qkanan->result_array();
            //echo $this->db->last_query();
            return $data;
        } elseif ($jenis == 'bu') {
            //PersenBuyinUnit/100*
            $sql_kanan = "
               SELECT ksp.*
               FROM ktv_supplychain_payment ksp
               LEFT JOIN ktv_supplychain_payment_detail kspd ON kspd.DetailPaymentID=ksp.PaymentID
               LEFT JOIN ktv_supplychain_transaction kst ON kspd.DetailSupplyTransID=kst.SupplyTransID
               LEFT JOIN ktv_supplychain_batch ksb ON kst.SupplyBatchID=ksb.SupplyBatchID
               LEFT JOIN ktv_supplychain_transaction kstb ON kstb.SupplyID=ksb.SupplyBatchNumber
               LEFT JOIN ktv_supplychain_batch ksbb ON kstb.SupplyBatchID=ksbb.SupplyBatchID
               WHERE PaymentDestID=? AND (PaymentDestType='sce' OR PaymentDestType='Pedagang') AND
               	(IFNULL(ksbb.SupplyBatchDate,ksb.SupplyBatchDate) BETWEEN ? AND ?)
               GROUP BY ksp.PaymentID";
            $qkanan = $this->db->query($sql_kanan, array($id, $awal, $akhir));
            $data = $qkanan->result_array();
            //echo $this->db->last_query();
            return $data;
        } elseif ($jenis == 'petani') {
            //PersenBuyinUnit/100*
            $sql_kanan = "
               SELECT ksp.*
               FROM ktv_supplychain_payment ksp
               LEFT JOIN ktv_supplychain_payment_detail kspd ON kspd.DetailPaymentID=ksp.PaymentID
               LEFT JOIN ktv_supplychain_transaction kst ON kspd.DetailSupplyTransID=kst.SupplyTransID
               LEFT JOIN ktv_supplychain_batch ksb ON kst.SupplyBatchID=ksb.SupplyBatchID
               LEFT JOIN ktv_supplychain_transaction kstb ON kstb.SupplyID=ksb.SupplyBatchNumber
               LEFT JOIN ktv_supplychain_batch ksbb ON kstb.SupplyBatchID=ksbb.SupplyBatchID
               LEFT JOIN ktv_supplychain_transaction kstc ON kstc.SupplyID=ksbb.SupplyBatchNumber
               LEFT JOIN ktv_supplychain_batch ksbc ON kstc.SupplyBatchID=ksbc.SupplyBatchID
               WHERE PaymentDestID=? AND PaymentDestType='Farmer' AND
               	(IFNULL(ksbc.SupplyBatchDate,IFNULL(ksbb.SupplyBatchDate,ksb.SupplyBatchDate)) BETWEEN ? AND ?)
               GROUP BY ksp.PaymentID";
            $qkanan = $this->db->query($sql_kanan, array($id, $awal, $akhir));
            $data = $qkanan->result_array();
            return $data;
        }
    }

    function createTransPremium($id, $pemberi_id, $penerima_type, $penerima_id, $bruto, $netto, $premium, $userid, $berat, $premi)
    {

        $sql_number = "
            select concat(OrgID,'-',year(now()),month(now()),'-',IFNULL(LPAD(max(SUBSTRING_INDEX(PaymentNumber, '-', -1))+1,5,'0'),'00001')) id
            from ktv_supplychain_org_view
            left join ktv_supplychain_payment on PaymentSupplychainID=SupplychainID
            WHERE SupplychainID=?";

        $query = $this->db->query($sql_number, array($pemberi_id));
        $data = $query->result_array();

        $sql = "
            insert into ktv_supplychain_payment (PaymentSupplychainID, PaymentDestType, PaymentDestID,
               PaymentNumber, PaymentBruto, PaymentNetto, PaymentPremium, PaymentDate,
               DateCreated, CreatedBy)
            VALUES (?,?,?,   ?,?,?,?,now(),   now(),?)";
        $sql_detail = "
            insert into ktv_supplychain_payment_detail (DetailPaymentID, DetailSupplyTransID,DetailBerat,DetailPremium)
            VALUES (?,?,?,?)";
        $query = $this->db->query($sql, array($pemberi_id, $penerima_type, $penerima_id, $data[0]['id'],
            $bruto, $netto, $premium, $userid));
        $idp = $this->db->insert_id();
        $ids = explode(',', $id);
        $berats = explode(',', $berat);
        $premis = explode(',', $premi);
        for ($i = 0; $i < sizeof($ids); $i++) {
            $query = $this->db->query($sql_detail, array($idp, $ids[$i], $berats[$i], $premis[$i]));
        }
        if ($query) {
            $results['id'] = $penerima_id;
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function getKuitansiByNomor($nomor)
    {
        $sql = "
            select CONCAT('coop/',kcoop.Photo) AS logo_koperasi, CONCAT('certification_provider/',kcp.Photo) AS logo_sertifikasi,
            ksov.Name nama, ksov.Address keterangan,PaymentNumber nomor,PaymentPremium total,
               IF(ksov.OrgType='Gudang',kpp.Photo,IF(ksov.OrgType='Organisasi Petani',kcb.Photo,'')) logo,
            IF(PaymentDestType='Organisasi Petani',IFNULL(kcf.FarmerName,StaffName),IF(PaymentDestType='Pedagang',TraderName,
                  IF(PaymentDestType='Farmer',kcfb.FarmerName,ksovb.Name))) StaffName,
            IF(PaymentDestType='Organisasi Petani',IFNULL(kcf.FarmerID,StaffID),IF(PaymentDestType='Pedagang',TraderID,
                  IF(PaymentDestType='Farmer',kcfb.FarmerID,ksovb.Name))) StaffID,
               District,PaymentDate,PaymentNetto
            from ktv_supplychain_payment
            left join ktv_supplychain_org_view ksov on PaymentSupplychainID=ksov.SupplychainID
            left join ktv_supplychain_org_view ksovb on PaymentDestID=ksovb.SupplychainID
            left join ktv_warehouse kw on ksov.OrgID=WarehouseID and ksov.OrgType='Gudang'
            left join ktv_program_partner kpp on kpp.PartnerID=kw.PartnerID
            left join ktv_cooperatives kcb on ksov.OrgID=kcb.CoopID and ksov.OrgType='Organisasi Petani'
            left join ktv_cooperatives kc on ksovb.OrgID=kc.CoopID and ksovb.OrgType='Organisasi Petani'
            left join ktv_traders kt on ksovb.OrgID=TraderID
            left join ktv_farmer kcfb on kcfb.FarmerID=PaymentDestID
            left join ktv_cooperative_staff kcs on kcs.CoopID=kc.CoopID and Position='Ketua'
            left join ktv_farmer kcf on kcf.FarmerID=kcs.FarmerID
            left join ktv_district kd on kd.DistrictID=substr(ksov.VillageID,1,4)
            LEFT JOIN ktv_cooperatives kcoop ON kcoop.CoopID=IF(ksov.OrgType='Organisasi Petani', ksov.OrgID, ksovb.OrgID)
            LEFT JOIN ktv_certification_provider_contract kcp ON kcp.ObjType='koperasi' AND kcp.ObjID=kcoop.CoopID AND PaymentDate BETWEEN kcp.CertificationStart AND kcp.CertificationEnd AND kcp.StatusCode='active'
            WHERE PaymentNumber=?";
        $query = $this->db->query($sql, array($nomor));
        $data = $query->result_array();
        $result['data'] = $data[0];
        $sql = "
            SELECT CONCAT('[',IFNULL(ksbb.`DestPO`,ksbc.`DestPO`),'] ',DetailSupplyTransID) id,DATE(kst.DateTransaction) tanggal,DetailBerat berat,DetailPremium premium
            FROM ktv_supplychain_payment
            LEFT JOIN ktv_supplychain_payment_detail ON DetailPaymentID=PaymentID
            LEFT JOIN ktv_supplychain_transaction kst ON kst.SupplyTransID=DetailSupplyTransID
            LEFT JOIN ktv_supplychain_batch ksbb ON kst.SupplyBatchID=ksbb.SupplyBatchID
            LEFT JOIN ktv_supplychain_transaction kstb ON ksbb.`SupplyBatchNumber`=kstb.`SupplyID`
            LEFT JOIN ktv_supplychain_batch ksbc ON kstb.SupplyBatchID=ksbc.SupplyBatchID
            LEFT JOIN ktv_supplychain_premium ksp ON ksp.PremiumSupplychainID=ksbb.SupplyOrgID AND
               (ksbb.SupplyBatchDate BETWEEN PremiumDateStart AND PremiumDateEnd)
            WHERE PaymentNumber=?";
        $query = $this->db->query($sql, array($nomor));
        $result['detail'] = $query->result_array();
        return $result;
    }

    function getKuitansiNomorBySearch($nama, $cpg, $awal, $akhir)
    {
        //print_r(array(str_replace('-','%',$nama),$cpg,$awal,$akhir));
        $sql = "select PaymentNumber
            from ktv_supplychain_payment
            left join ktv_farmer ON PaymentDestID=FarmerID
            WHERE PaymentDestType='Farmer' and FarmerName like ? and CPGid=? and (PaymentDate between ? and ?)";
        $query = $this->db->query($sql, array(str_replace('-', '%', $nama), $cpg, $awal, $akhir));
        $data = $query->result_array();
        return $data;
    }

    function getDetailsKoperasi($awal, $akhir, $jenis = '', $provinsi, $warehouse, $sert){
      $isPetani = ($jenis == 'Non Farmer' ? 'NonFarmer' : 'Farmer');
      if ($isPetani == 'Farmer') {
         if ($sert == '0') {
            $sertSql = "
               LEFT JOIN (
                  SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                     d.FarmerID
                  FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr
                     FROM ktv_farmer_garden
                     GROUP BY FarmerID,GardenNr
                  ) z
                  INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                    z.LatestSurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                  GROUP BY d.FarmerID
               ) e ON farmer_id=e.FarmerID";
            $where_sert = "";
         } else {
            $sertSql = "
               LEFT JOIN (
                  SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                     d.FarmerID
                  FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE (CertificationStart>='$awal' AND CertificationEnd<='$akhir') AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                  INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                     z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                  GROUP BY d.FarmerID
               ) e ON farmer_id=e.FarmerID";
            $where_sert = "AND farmer_iscertified='1'";
         }
      } else {
         $sertSql = "LEFT JOIN (SELECT 0 AS survey_production) e ON 1 = 1";
         $where_sert = "";
      }
      $sql = "
         SELECT
         	IF(2_orgtype='koperasi',2_orgid,'') orgid, IF(2_orgtype='koperasi',2_name,'') name,'koperasi' orgtype,
         	SUM(survey_production) survey,SUM(survey_production)+(0.1*SUM(survey_production)) quota,
         	SUM(IF(2_orgtype='koperasi',22_bruto,0)) bruto,
         	SUM(IF(2_orgtype='koperasi',whh_netto,0)) netto,
         	SUM(IF(2_orgtype='koperasi',whh_netto,0))/1000*wh_persenkoperasi/100*wh_dollar totalusd,
         	SUM(IF(2_orgtype='koperasi',whh_netto,0)/1000*wh_persenkoperasi/100*(wh_dollar*wh_kurs)) totalidr,
         	SUM(survey_production)+(0.1*SUM(survey_production))-SUM(IF(2_orgtype='koperasi',whh_netto,0)) balance,
         	SUM(IF(2_orgtype='koperasi',paid,0)) paidkg,
            SUM(IF(2_orgtype='koperasi',unpaid,0)) unpaidkg,
         	SUM(IF(2_orgtype='koperasi',paid,0))/1000*wh_persenkoperasi/100*wh_dollar paidusd,
            SUM(IF(2_orgtype='koperasi',unpaid,0))/1000*wh_persenkoperasi/100*wh_dollar unpaidusd
         FROM (
         	SELECT rt.*,survey_production,SUM(IFNULL(1_bruto,2_bruto)) 22_bruto,sum(farmer_netto) whh_netto,SUM(IF(2_ispaid='1',farmer_netto,0)) paid,
         	     SUM(IF(2_ispaid='0',farmer_netto,0)) unpaid
         	FROM rpt_traceability rt
         	{$sertSql}
         	WHERE (wh_date BETWEEN ? AND ?) AND wh_orgid=? AND SUBSTR(farmer_id,1,2) = ? $where_sert
         	GROUP BY farmer_id,1_supplychainid,2_supplychainid
         ) a
         GROUP BY IF(2_orgtype='koperasi',2_orgid,'')";
      $query = $this->db->query($sql, array($awal, $akhir, $warehouse, $provinsi));
      $data = $query->result_array();
      //echo "<pre>".$this->db->last_query();exit;
      //print_r($data);exit;
      return $data;
   }

   function getPremiumBuv($awal, $akhir, $jenis = '', $provinsi, $warehouse, $sert,$koperasi='') {
      $isPetani = ($jenis == 'Non Farmer' ? 'NonFarmer' : 'Farmer');
      if ($koperasi!='') $where = "and IF(2_orgtype='koperasi',2_orgid,'')=$koperasi";
      if ($isPetani == 'Farmer') {
         if ($sert == '0') {
            $sertSql = "
               LEFT JOIN (
                  SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                     d.FarmerID
                  FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr
                     FROM ktv_farmer_garden
                     GROUP BY FarmerID,GardenNr
                  ) z
                  INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                    z.LatestSurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                  GROUP BY d.FarmerID
               ) e ON farmer_id=e.FarmerID";
            $where_sert = "";
         } else {
            $sertSql = "
               LEFT JOIN (
                  SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                     d.FarmerID
                  FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE (CertificationStart>='$awal' AND CertificationEnd<='$akhir') AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                  INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                     z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                  GROUP BY d.FarmerID
               ) e ON farmer_id=e.FarmerID";
            $where_sert = "AND farmer_iscertified='1'";
         }
      } else {
         $sertSql = "LEFT JOIN (SELECT 0 AS survey_production) e ON 1 = 1";
         $where_sert = "";
      }
      $sql = "
         SELECT
            IF(2_orgtype!='koperasi',2_orgid,IF(1_orgtype!='koperasi',1_orgid,'0')) orgid,
               IF(2_orgtype!='koperasi',2_name,IF(1_orgtype!='koperasi',1_name,'Direct selling to cooperatives')) name,
               IF(2_orgtype='koperasi',2_name,'') name_b,
               IF(2_orgtype!='koperasi',2_orgtype,IF(1_orgtype!='koperasi',1_orgtype,'')) orgtype,
            SUM(survey_production) survey,SUM(survey_production)+(0.1*SUM(survey_production)) quota,
            SUM(IF(2_orgtype!='koperasi',22_bruto,IF(1_orgtype!='koperasi',11_bruto,22_bruto))) bruto,
            SUM(IF(2_orgtype!='koperasi',whh_netto,IF(1_orgtype!='koperasi',whh_netto,whh_netto))) netto,
            SUM(IF(2_orgtype!='koperasi',whh_netto,IF(1_orgtype!='koperasi',whh_netto,whh_netto))/1000*wh_persenbs/100*wh_dollar) totalusd,
            SUM(IF(2_orgtype!='koperasi',whh_netto,IF(1_orgtype!='koperasi',whh_netto,whh_netto))/1000*wh_persenbs/100*wh_dollar*wh_kurs) totalidr,
            SUM(survey_production)+(0.1*SUM(survey_production))-SUM(IF(2_orgtype!='koperasi',whh_netto,IF(1_orgtype!='koperasi',whh_netto,whh_netto))) balance,
            SUM(IF(2_orgtype!='koperasi',2_paid,IF(1_orgtype!='koperasi',1_paid,2_paid))) paidkg,
            SUM(IF(2_orgtype!='koperasi',2_unpaid,IF(1_orgtype!='koperasi',1_unpaid,2_paid))) unpaidkg,
            SUM(IF(2_orgtype!='koperasi',2_paid,IF(1_orgtype!='koperasi',1_paid,2_paid))/1000*wh_persenbs/100*wh_dollar) paidusd,
            SUM(IF(2_orgtype!='koperasi',2_unpaid,IF(1_orgtype!='koperasi',1_unpaid,2_paid))/1000*wh_persenbs/100*wh_dollar) unpaidusd
         FROM (
         	SELECT rt.*,survey_production,SUM(2_bruto) 22_bruto,SUM(1_bruto) 11_bruto,SUM(farmer_netto) whh_netto,
         	  SUM(IF(2_ispaid='1',farmer_netto,0)) 2_paid,SUM(IF(2_ispaid='0',farmer_netto,0)) 2_unpaid,
         	  SUM(IF(1_ispaid='1',farmer_netto,0)) 1_paid,SUM(IF(1_ispaid='0',farmer_netto,0)) 1_unpaid
         	FROM rpt_traceability rt
         	{$sertSql}
         	WHERE (wh_date BETWEEN ? AND ?) AND wh_orgid=? $where AND SUBSTR(farmer_id,1,2) = ? $where_sert
         	GROUP BY farmer_id,1_supplychainid,2_supplychainid
         ) a
         GROUP BY IF(2_orgtype!='koperasi',2_orgid,IF(1_orgtype!='koperasi',1_orgid,''))
         ORDER BY IF(2_orgtype='koperasi',2_orgid,'')";
      $query = $this->db->query($sql, array($awal, $akhir, $warehouse, $provinsi));
      //echo "<pre>".$this->db->last_query();exit;
      $data = $query->result_array();
      return $data;
   }

   function getDetailsPetani($awal, $akhir, $jenis = '', $buId = '', $provinsi, $warehouse, $sert, $start, $limit, $type='',$format=''){
      $isPetani = ($jenis == 'Non Farmer' ? 'NonFarmer' : 'Farmer');
      if ($buId > 0) { // default report
         // $sqlBuid = "(ksbb.PerwakilanOrgID = {$buId} OR '' = {$buId}) AND";
         $sqlLimit = '';
      } else {
            $sqlLimit = "LIMIT " . (int) $start . "," . (int) $limit;
            $or = ' OR true';
      }
      if($type=='summary'){
        $order_by = "";
      }else if($type=='summarycpg'){
        $order_by = "";
      }else{
        $order_by = "";
      }
      if ($isPetani == 'Farmer') {
         if ($sert == '0') {
            $sertSql = "
               LEFT JOIN (
                  SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                     d.FarmerID
                  FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) LatestSurveyNr
                     FROM ktv_farmer_garden
                     GROUP BY FarmerID,GardenNr
                  ) z
                  INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                    z.LatestSurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                  GROUP BY d.FarmerID
               ) e ON farmer_id=e.FarmerID";
            $where_sert = "";
         } else {
            $sertSql = "
               LEFT JOIN (
                  SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                     d.FarmerID
                  FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE (CertificationStart>='$awal' AND CertificationEnd<='$akhir') AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                  INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                     z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                  GROUP BY d.FarmerID
               ) e ON farmer_id=e.FarmerID";
            $where_sert = "AND farmer_iscertified='1'";
         }
      } else {
         $sertSql = "LEFT JOIN (SELECT 0 AS survey_production) e ON 1 = 1";
         $where_sert = "";
      }
        if($format==''){
            if($type=='summary'){
                $groupby = "farmer_id";
            }else if($type=='summarycpg'){
                $groupby = "kcf.CPGid";
            }else{
                $groupby = "IFNULL(1_batchid,2_batchid),IFNULL(1_transid,2_transid)";
            }
        }else{
            $groupby = "IFNULL(1_batchid,2_batchid),IFNULL(1_transid,2_transid)";
        }
      $sql = "
      	SELECT SQL_CALC_FOUND_ROWS
            IFnull(1_po,2_po) nopo, IFnull(1_date,2_date) datetransaction,farmer_id AS ids,
      	   farmer_name AS farmer,farmer_name AS name,survey_production survey,
      	   SUM(IFNULL(1_bruto,2_bruto)) bruto,survey_production+(0.1*survey_production) quota,
            SUM(farmer_netto) netto,
            SUM(farmer_netto/1000*wh_dollar*wh_persenfarmer/100) totalusd,
            SUM(farmer_netto/1000*wh_dollar*wh_persenfarmer/100*wh_kurs) totalidr,
            survey_production + (0.1*survey_production) - sum(farmer_netto) balance,
            IFNULL(1_orgid,2_orgid) AS orgid,IFNULL(2_batchnumber,1_batchnumber) AS batch,
            IF(farmer_ispaid='1',farmer_netto,0) paidkg,
            IF(farmer_ispaid='0',farmer_netto,0) unpaidkg,
            IF(farmer_ispaid='1',farmer_netto,0)/1000*wh_dollar*wh_persenfarmer/100 paidusd,
            IF(farmer_ispaid='0',farmer_netto,0)/1000*wh_dollar*wh_persenfarmer/100 unpaidusd,
            kcf.CPGid AS cpg_id,CONCAT('[',kcf.CPGid,'] ',kcpg.GroupName) AS cpg_name
      	FROM rpt_traceability rt
      	    {$sertSql}
            LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=farmer_id
            LEFT JOIN ktv_cpg kcpg ON kcf.CPGid=kcpg.CPGid
      	WHERE (IFNULL(1_date,2_date) BETWEEN ? AND ?) AND wh_orgid=? AND SUBSTR(farmer_id,1,2) = ? and (IFNULL(1_orgid,2_orgid)=? $or) $where_sert
         GROUP BY IFNULL(1_batchid,2_batchid),IFNULL(1_transid,2_transid)
         ORDER BY $orderby farmer_id, IFNULL(1_date,2_date)
         {$sqlLimit}";
      $query = $this->db->query($sql, array($awal, $akhir, $warehouse, $provinsi,$buId));
            //$query = $this->db->query($sql, array(
              //  $awal, $akhir, $isPetani, $warehouse, $buId, $buId,  $buId, $provinsi)
//        );
        //$data = $query->result_array();
      //echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
      $result['data'] = $query->result_array();
      if ($buId > 0) {
         $result['total'] = 0;
      } else {
         $query2 = $this->db->query("SELECT FOUND_ROWS() AS total");
         $row = $query2->row_array(0);
         $result['total'] = $row['total'];
      }
      return $result;
   }

    function getRekapPenjualan($farmerId, $partner, $jenis)
    {
        $result = array();
        $sql1 = "SELECT
                    kcf.FarmerID,
                    kcf.FarmerName,
                    kv.Village,
                    ks.SubDistrict,
                    kc.GroupName,
                    sum((IFNULL(kcfg.PanenTrekMonths,0)*IFNULL(kcfg.PanenTrekPanenMonth,0)*IFNULL(kcfg.PanenTrekKg,0))+
                        (IFNULL(kcfg.PanenBiasaMonths,0)*IFNULL(kcfg.PanenBiasaPanenMonth,0)*IFNULL(kcfg.PanenBiasaKg,0))+
                        (IFNULL(kcfg.PanenRayaMonths,0)*IFNULL(kcfg.PanenRayaPanenMonth,0)*IFNULL(kcfg.PanenRayaKg,0))) survey,
                    (
                        SELECT
                            Photo
                        FROM
                            ktv_program_partner
                        WHERE
                            PartnerID = ?
                    ) as photo
                FROM
                    ktv_farmer kcf
                    LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
                    LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_cpg kc ON kc.CPGid = kcf.CPGid
                    LEFT JOIN ktv_farmer_garden kcfg on kcf.FarmerID=kcfg.FarmerID
                    INNER JOIN (
                        SELECT
                            FarmerID,
                            GardenNr,
                            max(SurveyNr) LatestSurveyNr
                        FROM
                            ktv_farmer_garden
                        GROUP BY FarmerID,GardenNr
                    ) z on kcfg.FarmerID = z.FarmerID and kcfg.GardenNr = z.GardenNr
                WHERE
                    kcf.FarmerID = ?";

        $query1 = $this->db->query($sql1, array($partner, $farmerId));
        $data1 = $query1->result_array();
        $result['data'] = $data1[0];

        if ($jenis == 'Form Kosong') {
            $result['records'] = array();
        } else {
            $sql2 = "SELECT
                    DATE(kstb.DateTransaction) AS DateTransaction,
                    ksovc.Name as BuyingUnit,
                    (kstb.FFVolumeBruto+kstb.FAQVolumeBruto) FF_FAQ,
                    (kstb.FFVolumeBruto+kstb.FAQVolumeBruto) Bruto,
                    kstd.Moisture,
                    IF(
                        kso.KalkulasiPremium='2',((100-(kstd.Moisture-7))/100*kstb.FAQVolumeBruto)/a.nett*ksb.VolumeNetto,
                        kstb.FAQVolumeBruto/ksbb.VolumeBruto*ksb.VolumeNetto
                    ) Netto,
                    kstb.FAQNetPrice,
                    kstb.FAQTotalPayment,
                    cpg.GroupName AS cpg
                FROM
                    ktv_supplychain_batch ksb
                    LEFT JOIN ktv_supplychain_org_view ksov ON ksov.SupplychainID=ksb.SupplyOrgID
                    LEFT JOIN ktv_supplychain_org kso ON ksov.SupplychainID=kso.SupplychainID
                    LEFT JOIN ktv_supplychain_transaction kst ON ksb.SupplyBatchID=kst.SupplyBatchID
                    LEFT JOIN ktv_supplychain_batch ksbb ON ksbb.SupplyBatchNumber=kst.SupplyID
                    LEFT JOIN ktv_supplychain_org_view ksovc on ksovc.OrgID=ksbb.PerwakilanOrgId
                    LEFT JOIN ktv_supplychain_transaction kstb ON ksbb.SupplyBatchID=kstb.SupplyBatchID
                    LEFT JOIN (
                        SELECT
                            SupplyBatchID,
                            SUM((100-(Moisture-7))/100*FAQVolumeBruto) nett
                        FROM
                            ktv_supplychain_transaction kst
                            LEFT JOIN ktv_supplychain_transaction_dtl kstd ON kst.SupplyTransID=kstd.SupplyTransID
                        WHERE SupplyType='Farmer'
                        GROUP BY SupplyBatchID
                    ) a ON ksbb.SupplyBatchID=a.SupplyBatchID
                    LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=kstb.SupplyID
                    LEFT JOIN ktv_supplychain_transaction_dtl kstd ON kstd.SupplyTransID=kstb.SupplyTransID
                    LEFT JOIN ktv_cpg cpg ON cpg.CPGid = kcf.CPGid
                WHERE
                    kcf.FarmerID = ?
                GROUP BY
                    kstb.SupplyTransID
                ORDER BY
                    kstb.DateTransaction";
            $query2 = $this->db->query($sql2, array($farmerId));
            $result['records'] = $query2->result_array();
        }
        return $result;
    }

    public function getPremiumByOrg($OrgID,$date='')
    {
    if ($date=='') $date = date('Y-m-d');
        $sql = "SELECT
    `PremiumID`,
    `PremiumSupplychainID`,
    `PremiumDateStart`,
    `PremiumDateEnd`,
    `PersenPetani`,
    `PersenBuyinUnit`,
    `PersenPerwakilan`,
    `USD`,
    IFNULL(k.KursNominal,Kurs) Kurs,
    `Rupiah`
FROM
    `ktv_supplychain_premium` p
JOIN `ktv_supplychain_org` o ON o.`SupplychainID` = p.`PremiumSupplychainID`
LEFT JOIN `ktv_supplychain_kurs` k ON o.`SupplychainID` = k.`KursSupplychainID` and (CURRENT_DATE BETWEEN KursDateStart AND KursDateEnd)
WHERE
    o.`OrgID` = ? AND (? BETWEEN PremiumDateStart AND PremiumDateEnd)";
        $query = $this->db->query($sql, array($OrgID,$date));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
    }


    //invoice
    function readInvoices($key, $awal, $akhir){
    //'cetak_sudah','cetak_belum','bayar_sudah','bayar_belum
      $sql = "
         select ksb.SupplyDestOrgID SupplychainID,ksovb.Name name,sum(ksbb.VolumeNetto) berat, sum(kstw.FAQTotalPayment) total,
            sum(IF(ksid.DetailID is null,0,ksbb.VolumeNetto)) cetak_sudah, sum(IF(ksid.DetailID is null,ksbb.VolumeNetto,0)) cetak_belum,
            sum(IF(InvoiceIsPaid='1',ksbb.VolumeNetto,0)) bayar_sudah,sum(IF(InvoiceIsPaid='1',0,ksbb.VolumeNetto)) bayar_belum,
            ksov.Name pemberi,ksovb.Name penerima,ksov.SupplychainID pemberi_id,ksovb.SupplychainID penerima_id,
            concat(kb.BankID,'-',kb.BankName,'-',cashSourceNo,'-',cashSourceName) bank_id
         from ktv_supplychain_batch ksb
         left join ktv_supplychain_transaction kstw on kstw.SupplyType='Batch' and kstw.SupplyID=ksb.SupplyBatchNumber
         left join ktv_supplychain_batch ksbb on ksbb.SupplyOrgID=ksb.SupplyDestOrgID and ksbb.SupplyBatchID=kstw.SupplyBatchID
         left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
         left join ktv_supplychain_org_view ksovb on ksovb.SupplychainID=ksb.SupplyDestOrgID
         left join ktv_supplychain_staff kss on ksov.SupplychainID=kss.StaffSupplychainID
         left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
         left join ktv_supplychain_transaction_dtl kstd on kstd.SupplyTransID=kst.SupplyTransID
         left join ktv_supplychain_invoice_detail ksid on ksid.DetailSuplyBatchID=ksbb.SupplyBatchID
         left join ktv_supplychain_invoice ksi on ksi.InvoiceID=ksid.DetailInvoiceID
         left join coop_cash_source ccs on ccs.CoopID=ksov.OrgID and ksov.OrgType='Organisasi Petani' and ccs.BankID is not null
         left join ktv_bank kb on ccs.BankID=kb.BankID
         where kss.UserID=? and ksb.SupplyBatchNumber like ? and (kst.DateTransaction BETWEEN ? and ?) and ksb.SupplyDestOrgID IS NOT NULL
         GROUP BY ksb.SupplyDestOrgID";
      $query = $this->db->query($sql, array($_SESSION['userid'], "%$key%", $awal, $akhir));
      //echo $this->db->last_query();exit;
      return $query->result_array();
    }

    function readInvoiceBanks($id){
      $sql = "
         select concat(kb.BankID,'-',kb.BankName,'-',cashSourceNo,'-',cashSourceName) id, kb.BankName label
         from coop_cash_source ccs
         left join ktv_bank kb on ccs.BankID=kb.BankID
         left join ktv_supplychain_org_view ksov on ksov.OrgID=ccs.CoopID and ksov.OrgType='Organisasi Petani'
         where ccs.BankID is not null and ksov.SupplychainID=?";
      $query = $this->db->query($sql, array($id));
      $result['data'] = $query->result_array();
      return $result;
    }

    function readInvoice($id, $jenis, $awal, $akhir)
    {
        if ($jenis == 'koperasi' OR $jenis == 'bu') {
            $sql_kiri = "
            select ksovb.Name pemberi, ksov.Name penerima,ksovb.SupplychainID pemberi_id, ksov.SupplychainID penerima_id,
               ksov.OrgType penerima_type
            from ktv_supplychain_org_view ksov
            left join ktv_supplychain_org_rel ksor on ksor.ChildOrgId=ksov.SupplychainID
            left join ktv_supplychain_org_view ksovb on ksor.ParentOrgId=ksovb.SupplychainID
            where ksov.SupplychainID=?";
        } else {
            $sql_kiri = "
            select ksov.Name pemberi, concat('[',FarmerID,'] ',FarmerName) penerima,SupplychainID pemberi_id, FarmerID penerima_id,
               'Farmer' penerima_type
            from ktv_farmer kcf
            left join ktv_supplychain_org_view ksov on substr(kcf.VillageID,1,4)=substr(ksov.VillageID,1,4) and OrgType='Organisasi Petani'
            where FarmerID=?";
        }
        $qkiri = $this->db->query($sql_kiri, array($id));
        $kiri = $qkiri->result_array();
        $data['kiri'] = $kiri[0];
        return $data;
    }

    function readInvoiceDetails($id, $awal, $akhir) {
//        fields: [{name:'DestPO'},{name:'SupplyTransID'},{name:'DateTransaction'},{name:'jumlah_karung',type:'int'},
  //          {name:'netto',type:'float'},{name:'harga',type:'float'},{name:'belum',type:'float'},{name:'sudah',type:'float'}],
      $sql = "
         select ksb.SupplyBatchID,ksb.DestPO DestPO,kst.SupplyTransID,date(kst.DateTransaction) DateTransaction,
            IFNULL(count(kstd.DetailID),0) jumlah_karung,kstw.FAQNetPrice harga,
            ksbb.VolumeNetto netto, kstw.FAQTotalPayment total, IF(ksid.DetailID is null,'0','1') cetak,
            IF(InvoiceIsPaid='1',0,kstw.FAQTotalPayment) belum,IF(InvoiceIsPaid='1',kstw.FAQTotalPayment,0) sudah
         from ktv_supplychain_batch ksb
         left join ktv_supplychain_transaction kstw on kstw.SupplyType='Batch' and kstw.SupplyID=ksb.SupplyBatchNumber
         left join ktv_supplychain_batch ksbb on ksbb.SupplyOrgID=ksb.SupplyDestOrgID and ksbb.SupplyBatchID=kstw.SupplyBatchID
         left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
         left join ktv_supplychain_org_view ksovb on ksovb.SupplychainID=ksb.SupplyDestOrgID
         left join ktv_supplychain_staff kss on ksov.SupplychainID=kss.StaffSupplychainID
         left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
         left join ktv_supplychain_transaction_dtl kstd on kstd.SupplyTransID=kst.SupplyTransID
         left join ktv_supplychain_invoice_detail ksid on ksid.DetailSuplyBatchID=ksb.SupplyBatchID
         left join ktv_supplychain_invoice ksi on ksi.InvoiceID=ksid.DetailInvoiceID
         where (kst.DateTransaction BETWEEN ? and ?) and ksb.SupplyDestOrgID=?
         GROUP BY ksbb.SupplyBatchID";

      $qkanan = $this->db->query($sql, array($awal, $akhir, $id));
      $data = $qkanan->result_array();
      return $data;

    }

    function readInvoiceInvoices($id, $awal, $akhir) {
      $sql = "
         select ksi.*
         from ktv_supplychain_batch ksb
         left join ktv_supplychain_transaction kstw on kstw.SupplyType='Batch' and kstw.SupplyID=ksb.SupplyBatchNumber
         left join ktv_supplychain_batch ksbb on ksbb.SupplyOrgID=ksb.SupplyDestOrgID and ksbb.SupplyBatchID=kstw.SupplyBatchID
         left join ktv_supplychain_org_view ksov on ksov.SupplychainID=ksb.SupplyOrgID
         left join ktv_supplychain_org_view ksovb on ksovb.SupplychainID=ksb.SupplyDestOrgID
         left join ktv_supplychain_staff kss on ksov.SupplychainID=kss.StaffSupplychainID
         left join ktv_supplychain_transaction kst on ksb.SupplyBatchID=kst.SupplyBatchID
         left join ktv_supplychain_transaction_dtl kstd on kstd.SupplyTransID=kst.SupplyTransID
         left join ktv_supplychain_invoice_detail ksid on ksid.DetailSuplyBatchID=ksb.SupplyBatchID
         left join ktv_supplychain_invoice ksi on ksi.InvoiceID=ksid.DetailInvoiceID
         where (kst.DateTransaction BETWEEN ? and ?) and ksb.SupplyDestOrgID=? and ksi.InvoiceID is not null
         GROUP BY ksi.InvoiceID";
         $qkanan = $this->db->query($sql, array($awal, $akhir,$id));
         $data = $qkanan->result_array();
         return $data;
    }

    function createInvoice($id, $pemberi_id, $penerima_id, $netto, $total, $karung, $bankId,$bankRek,$bankAn,
         $berats, $hargas, $karungs, $userid) {
        $sql_number = "
            select concat(OrgID,'-',year(now()),month(now()),'-',IFNULL(LPAD(substr(max(InvoiceNumber),length(InvoiceNumber)-4,5)+1,5,'0'),'00001')) id
            from ktv_supplychain_org_view
            left join ktv_supplychain_invoice on InvoiceSupplychainID=SupplychainID
            WHERE SupplychainID=?";
        $query = $this->db->query($sql_number, array($pemberi_id));
        $data = $query->result_array();

        $sql = "
            insert into ktv_supplychain_invoice (InvoiceSupplychainID, InvoiceDestType, InvoiceDestID,
               InvoiceNumber, InvoiceBerat, InvoiceTotal, InvoiceJumlahKarung, InvoiceDate,
               InvoiceBankId,InvoiceBankRekening,InvoiceBankNama,InvoiceIsPaid,
               DateCreated, CreatedBy)
            VALUES (?,?,?,   ?,?,?,?,now(),   ?,?,?,'0',   now(),?)";
        $sql_detail = "
            insert into ktv_supplychain_invoice_detail (DetailInvoiceID, DetailSuplyBatchID,DetailBerat,DetailHarga,DetailKarung)
            VALUES (?,?,?,?,?)";
        $query = $this->db->query($sql, array($pemberi_id, $penerima_type, $penerima_id, $data[0]['id'],
            $netto, $total, $karung, $bankId,$bankRek,$bankAn, $userid));
        $idp = $this->db->insert_id();
        $ids = explode(',', $id);
        $bera = explode(',', $berats);
        $harg = explode(',', $hargas);
        $karun = explode(',', $karungs);
        for ($i = 0; $i < sizeof($ids); $i++) {
            $query = $this->db->query($sql_detail, array($idp, $ids[$i], $bera[$i], $harg[$i],$karun[$i]));
        }
        if ($query) {
            $results['id'] = $penerima_id;
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function bayarInvoice($no, $userid) {
        $sql = "
            update ktv_supplychain_invoice
            set InvoiceIsPaid='1',DateUpdated=now(),LastModifiedBy=?
            where InvoiceNumber=?";
        $query = $this->db->query($sql, array($userid,$no));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function getInvoiceByNomor($nomor)
    {
        $sql = "
            select ksov.Name nama,ksovb.Name kepada, ksovb.Address alamat,ksov.Address keterangan,InvoiceNumber nomor,
               District,InvoiceDate,InvoiceBerat,kw.Phone telepon,
               cashSourceName bank_an,cashSourceNo bank_rek,BankName bank_nama,
               kcs.StaffID,kcs.StaffName
            from ktv_supplychain_invoice
            left join ktv_supplychain_org_view ksov on InvoiceSupplychainID=ksov.SupplychainID
            left join ktv_supplychain_org_view ksovb on InvoiceDestID=ksovb.SupplychainID
            left join ktv_warehouse kw on WarehouseID=ksovb.OrgID and ksovb.OrgType='Gudang'
            left join ktv_cooperatives kcb on ksov.OrgID=kcb.CoopID and ksov.OrgType='Organisasi Petani'
            left join ktv_cooperatives kc on ksovb.OrgID=kc.CoopID and ksovb.OrgType='Organisasi Petani'
            left join ktv_cooperative_staff kcs on kcs.CoopID=kc.CoopID and Position='Ketua'
            left join ktv_district kd on kd.DistrictID=substr(ksov.VillageID,1,4)
            left join coop_cash_source ccs on ccs.CoopID=kcb.CoopID
            left join ktv_bank kb on ccs.BankID=kb.BankID
            WHERE InvoiceNumber=?";
        $query = $this->db->query($sql, array($nomor));
        $data = $query->result_array();
        $result['data'] = $data[0];
        $sql = "
            SELECT CONCAT('[',IFNULL(ksbb.`DestPO`,ksbc.`DestPO`),'] ',DetailSuplyBatchID) id,
               DATE(kstb.DateTransaction) tanggal,DetailBerat berat,ksbb.SupplyBatchID keterangan,
               DetailKarung karung,DetailBerat,DetailHarga
            FROM ktv_supplychain_invoice
            LEFT JOIN ktv_supplychain_invoice_detail ON DetailInvoiceID=InvoiceID
            LEFT JOIN ktv_supplychain_batch ksbb ON DetailSuplyBatchID=ksbb.SupplyBatchID
            LEFT JOIN ktv_supplychain_transaction kstb ON ksbb.`SupplyBatchNumber`=kstb.`SupplyID`
            LEFT JOIN ktv_supplychain_batch ksbc ON kstb.SupplyBatchID=ksbc.SupplyBatchID
            WHERE InvoiceNumber=?";
        $query = $this->db->query($sql, array($nomor));
        $result['detail'] = $query->result_array();
        return $result;
    }

    function getInvoiceNomorBySearch($nama, $cpg, $awal, $akhir)
    {
        //print_r(array(str_replace('-','%',$nama),$cpg,$awal,$akhir));
        $sql = "select InvoiceNumber
            from ktv_supplychain_invoice
            left join ktv_farmer ON InvoiceDestID=FarmerID
            WHERE InvoiceDestType='Farmer' and FarmerName like ? and CPGid=? and (InvoiceDate between ? and ?)";
        $query = $this->db->query($sql, array(str_replace('-', '%', $nama), $cpg, $awal, $akhir));
        $data = $query->result_array();
        return $data;
    }

//area
    function readDatasArea($id){
        $sql = "
            select ksd.*,District
            from ktv_supplychain_area ksd
            left join ktv_district kd on ksd.DistrictID=kd.DistrictID
            WHERE SupplychainID=?";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }
    function createDataArea($SupplychainID, $DistrictID){
        $sql = "
            insert into ktv_supplychain_area (SupplychainID, DistrictID)
            SELECT ?,DistrictID FROM ktv_district WHERE District=? AND ProvinceID=
               (SELECT SUBSTR(VillageID,1,2) FROM ktv_supplychain_org_view WHERE SupplychainID=?)";
        $query = $this->db->query($sql, array($SupplychainID, $DistrictID,$SupplychainID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateDataArea($DistrictID,$AreaID){
        $sql = "
            update ktv_supplychain_area
            set DistrictID=?
            where AreaID=?";
        $query = $this->db->query($sql, array($DistrictID,$AreaID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteDataArea($AreaID){
        $sql_detail = "
            DELETE FROM ktv_supplychain_area WHERE AreaID=?";
        $query = $this->db->query($sql_detail, array($AreaID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function readSetQuality($supplychainid,$transid){
        $sql = "
            SELECT
				a.`Name`,a.FAQValue, a.FFValue,a.FAQFormula,a.FFFormula,a.DetailID,a.StandardID,c.FAQReward,c.FFReward,c.FAQResult,c.FFResult,b.IsClaim,b.IsReward,d.FormulaNettoPrice, d.FormulaNettoAkhir
			FROM
				ktv_supplychain_quality_standard_detail a
				LEFT JOIN ktv_supplychain_quality_standard b ON a.StandardID=b.StandardID
				LEFT JOIN ktv_supplychain_transaction_quality c ON a.DetailID=c.DetailID AND a.StandardID=c.StandardID AND SupplyTransID=?
				LEFT JOIN ktv_supplychain_org d ON b.StandardSupplychainID=d.SupplychainID
                LEFT JOIN ktv_supplychain_quality e ON e.StandardID=b.StandardID
			WHERE
				b.StandardSupplychainID=? AND SUBSTR(NOW(),1,10) >= e.QualityDateStart AND SUBSTR(NOW(),1,10) <= e.QualityDateEnd";
        $query = $this->db->query($sql, array($transid,$supplychainid));
        $result['data'] = $query->result_array();
        return $result;
    }

    //var Quality
    function readVarQualitys($id) {
        $sql = "
            select %s
            from ktv_supplychain_quality_standard_detail
            WHERE StandardID=?
            ORDER BY `Order` asc";
        $query = $this->db->query(sprintf($sql, '*'), array($id));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total'), array($id));
        $result['total'] = $query->row()->total;
        return $result;
    }
    function createVarQuality($StandardID,$Name,$Order,$FAQFormula,$FFFormula,$FAQValue,$FFValue) {
        $sql = "
            insert into ktv_supplychain_quality_standard_detail (StandardID,Name,`Order`,FAQFormula,FFFormula,FAQValue,FFValue)
            VALUES (?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($StandardID,$Name,$Order,$FAQFormula,$FFFormula,$FAQValue,$FFValue));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    function updateVarQuality($Name,$Order,$FAQFormula,$FFFormula,$FAQValue,$FFValue, $id) {
        $sql = "
            update ktv_supplychain_quality_standard_detail
            set Name=?,`Order`=?,FAQFormula=?,FFFormula=?,FAQValue=?,FFValue=?
            where DetailID=?";
        $query = $this->db->query($sql, array($Name,$Order,$FAQFormula,$FFFormula,$FAQValue,$FFValue, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    function deleteVarQuality($id){
        $sql = "
            DELETE FROM ktv_supplychain_quality_standard_detail WHERE DetailID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //end var Quality

    public function check_for_usage($id) {

        $this->db->select('COUNT(SupplyBatchID) AS trans_usage',false);
        $this->db->from('ktv_supplychain_batch');
        $this->db->where('SupplyOrgID',$id);
        $this->db->or_where('SupplyDestOrgID',$id);

        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            if($row->trans_usage > 0){
                return false;
            }
        }

        return true;
    }

    public function delete_supplychain($id) {

        //ga jadi delete, di update aja StatusCode = nullified
        $this->db->where('SupplychainID',$id);
        $this->db->update('ktv_supplychain_org',array('StatusCode' => 'nullified'));

        if(!$this->db->_error_number()){
            return true;
        }
        return false;
    }

    function getReportLogo($start,$wh){
        $sql = "
            SELECT  d.CoopID,CONCAT('coop/',d.Photo) AS logo_koperasi, CONCAT('certification_provider/',kcp.Photo) AS logo_sertifikasi
            FROM 
                ktv_supplychain_org_rel a
                LEFT JOIN ktv_supplychain_org_view b ON a.ChildOrgId=b.SupplychainID
                LEFT JOIN ktv_supplychain_org_view c ON a.ParentOrgId=c.SupplychainID
                LEFT JOIN ktv_cooperatives d ON d.CoopID=b.OrgID
                LEFT JOIN ktv_certification_provider_contract kcp ON kcp.ObjType='koperasi' AND kcp.ObjID=d.CoopID  AND ? BETWEEN kcp.CertificationStart AND kcp.CertificationEnd
            WHERE c.OrgID=? AND b.OrgType='Organisasi Petani' LIMIT 1";
        $query = $this->db->query($sql, array($start,$wh));
        $data = $query->result_array();
        return $data[0];
    }

    function getReportTraceabilityWarehouse($awal, $akhir, $provinsi, $warehouse, $sert){
        if($sert=='1'){
            $where_sert = "AND farmer_iscertified='$sert'";
        }else{
            $where_sert = "";
        }
        $sql = "
            SELECT 
                %s
            FROM 
               rpt_traceability rt
                LEFT JOIN (
                    SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                        d.FarmerID
                    FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE 
                        !(? > CertificationEnd OR ? < CertificationStart) AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                    INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                        z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                    GROUP BY d.FarmerID
                ) e ON farmer_id=e.FarmerID
            WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND wh_dest LIKE %s %s";
        $query = $this->db->query(sprintf($sql, 
                    "CONCAT('[',wh_orgid,'] ',wh_name) name,
                    COUNT(DISTINCT wh_transid) AS transcount,
                    COUNT(DISTINCT wh_batchid) AS batchcount,
                    COUNT(DISTINCT IF(2_orgtype='Koperasi',2_orgid,NULL)) coopcount,
                    SUM(IF(wh_orgid IS NULL,NULL,IFNULL(2_netto,1_netto))) bruto,
                    SUM(wh_netto) netto","'%|$warehouse|%'", $where_sert), array($awal, $akhir, $awal, $akhir));
        $return = $query->result_array();
        $survey = $this->db->query(sprintf($sql, 
                    "DISTINCT(farmer_id), survey_production, survey_production+(0.1*survey_production) quota","'%|$warehouse|%'", $where_sert), array($awal, $akhir, $awal, $akhir, $sert));
        //echo "<pre>".$this->db->last_query();exit;
        $survey_production = 0;
        $quota = 0;
        foreach ($survey->result() as $row) {
            $survey_production = $survey_production + $row->survey_production;
            $quota = $quota + $row->quota;
        }
        $return[0]['survey'] = $survey_production;
        $return[0]['quota'] = $quota;
        return $return;
    }

    function getReportTraceabilityWarehouseTrans($awal, $akhir, $provinsi, $warehouse, $sert/*, $start = 0, $limit = 50*/){
        if($sert=='1'){
            $where_sert = "AND farmer_iscertified='$sert'";
        }else{
            $where_sert = "";
        }
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                wh_date datetransaction,
                wh_po po,
                ksb.SupplyBatchNumber batchnumber,
                SUM(IFNULL(2_netto,1_netto)) bruto,
                SUM(wh_netto) netto
            FROM 
                rpt_traceability rt
                
                /*LEFT JOIN (
                  SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                     d.FarmerID
                  FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE (CertificationStart>=? AND CertificationEnd<=?) AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                  INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                     z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                  GROUP BY d.FarmerID
                ) e ON farmer_id=e.FarmerID*/
                LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyBatchID=wh_batchid
            WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND 2_orgtype='Koperasi' AND wh_dest LIKE %s %s GROUP BY wh_batchid #LIMIT ?, ?";
        $query = $this->db->query(sprintf($sql, "'%|$warehouse|%'", $where_sert), array($awal, $akhir, $awal, $akhir/*, intval($start), intval($limit)*/));
        //$return = $query->result_array();
        //return $return;
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }else{
            return false;
        }
    }

    function getReportTraceabilityCoop($awal, $akhir, $provinsi, $warehouse, $sert, $coop){
        if($sert=='1'){
            $where_sert = "AND farmer_iscertified='$sert'";
        }else{
            $where_sert = "";
        }
        if($coop==''){
            $where_coop = "";
        }else{
            $c = explode("::", $coop);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$incoop .= ",";
                }
                @$incoop .=$value;

                $i++;
            }
            $where_coop = "AND 2_orgid IN ($incoop)";
        }
        $group_by = "GROUP BY 2_orgid";
        $sql = "
            SELECT 
                %s
            FROM 
               rpt_traceability rt
                LEFT JOIN (
                    SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                        d.FarmerID
                    FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE 
                        !(? > CertificationEnd OR ? < CertificationStart) AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                    INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                        z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                    GROUP BY d.FarmerID
                ) e ON farmer_id=e.FarmerID
            WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND 2_orgtype='Koperasi' AND wh_dest LIKE %s %s %s %s  ORDER BY 2_orgid";
        $query = $this->db->query(sprintf($sql, 
                    "/*CONCAT('[',2_orgid,'] - ',2_name)*/ 2_name name,
                    COUNT(DISTINCT 2_transid) AS transcount,
                    COUNT(DISTINCT 2_batchid) AS batchcount,
                    COUNT(DISTINCT IF(wh_orgid IS NOT NULL,2_batchid,NULL)) deliveredcount,
                    SUM(2_bruto) bruto,
                    SUM(2_netto) netto","'%|$warehouse|%'", $where_sert, $where_coop, $group_by), array($awal, $akhir, $awal, $akhir));
        $return = $query->result_array();
        //echo "<pre>".print_r($return,1);
        //echo "<pre>".$this->db->last_query();exit;
        $survey = $this->db->query(sprintf($sql, 
                    "DISTINCT(farmer_id), survey_production, survey_production+(0.1*survey_production) quota, 2_orgid orgid","'%|$warehouse|%'", $where_sert, $where_coop, ''), array($awal, $akhir, $awal, $akhir, $sert));
        //echo "<pre>".$this->db->last_query();exit;
        $surveys = array();
        foreach ($survey->result() as $row){
           $surveys[$row->orgid]['survey'] =  $surveys[$row->orgid]['survey'] + $row->survey_production;
           $surveys[$row->orgid]['quota'] =  $surveys[$row->orgid]['quota'] + $row->quota;
        }
        $surveys = array_values($surveys);
        for($i=0;$i<count($surveys);$i++){
            $return[$i]['survey'] = $surveys[$i]['survey'];
            $return[$i]['quota'] = $surveys[$i]['quota'];
        }
        //echo "<pre>".print_r($return,1);exit;
        return $return;
    }
    
    function getReportTraceabilityCoopTrans($awal, $akhir, $provinsi, $warehouse, $coop, $sert/*, $start = 0, $limit = 50*/){
        if($sert=='1'){
            $where_sert = "AND farmer_iscertified='$sert'";
        }else{
            $where_sert = "";
        }
        
        if($coop==''){
            $where_coop = "";
        }else{
            $c = explode("::", $coop);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$incoop .= ",";
                }
                @$incoop .=$value;

                $i++;
            }
            $where_coop = "AND 2_orgid IN ($incoop)";
        }
        
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                2_date datetransaction,
                2_po po,
                ksb.SupplyBatchNumber batchnumber,
                2_status batchstatus,
                /*CONCAT('[',2_orgid,'] - ',2_name)*/ 2_name name,
                CONCAT('[',2_destorgid,'] - ',2_destname) destination,
                SUM(2_bruto) bruto,
                SUM(2_netto) netto
            FROM 
                rpt_traceability rt
                LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyBatchID=wh_batchid
            WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND 2_orgtype='Koperasi' AND wh_dest LIKE %s %s %s GROUP BY 2_batchid ORDER BY 2_orgid, 2_date #LIMIT ?, ?";
        $query = $this->db->query(sprintf($sql, "'%|$warehouse|%'", $where_sert, $where_coop), array($awal, $akhir, $awal, $akhir/*, intval($start), intval($limit)*/));
        //echo "<pre>".$this->db->last_query();exit;
        //$return = $query->result_array();
        //return $return;

        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }else{
            return false;
        }
    }
    
    function getReportTraceabilityBu($awal, $akhir, $provinsi, $warehouse, $sert, $coop, $bu){
        if($sert=='1'){
            $where_sert = "AND farmer_iscertified='$sert'";
        }else{
            $where_sert = "";
        }
        /*if($coop==''){
            $where_coop = "";
        }else{
            $c = explode("::", $coop);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$incoop .= ",";
                }
                @$incoop .=$value;

                $i++;
            }
            $where_coop = "AND 2_orgid IN ($incoop)";
        }*/
        
        if($bu==''){
            $where_bu = "";
        }else{
            $c = explode("::", $bu);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$inbu.= ",";
                }
                @$inbu .=$value;

                $i++;
            }
            $where_bu = "AND IFNULL(1_orgid,2_orgid) IN ($inbu)";
        }
        
        $group_by = "GROUP BY IFNULL(1_orgid,2_orgid)";
        $sql = "
            SELECT 
                %s
            FROM 
               rpt_traceability rt
                LEFT JOIN (
                    SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                        d.FarmerID
                    FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE 
                        !(? > CertificationEnd OR ? < CertificationStart) AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                    INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                        z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                    GROUP BY d.FarmerID
                ) e ON farmer_id=e.FarmerID
            WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND ( IF(1_orgid IS NULL,2_orgtype,'Pedagang')!='koperasi') AND wh_dest LIKE %s %s %s %s ORDER BY IFNULL(1_orgid,2_orgid)";
        $query = $this->db->query(sprintf($sql, 
                    "CONCAT('[',IFNULL(1_orgtype, 2_orgtype),'] ',IF(1_orgid IS  NULL,2_name,1_name)) name,
                    COUNT(DISTINCT IFNULL(1_transid,2_transid)) AS transcount,
                    COUNT(DISTINCT IFNULL(1_batchid,2_batchid)) AS batchcount,
                    COUNT(DISTINCT IF(wh_orgid IS NOT NULL OR 2_orgid IS NOT NULL,IFNULL(1_batchid,2_batchid),NULL)) deliveredcount,
                    SUM(2_bruto) bruto,
                    SUM(2_netto) netto","'%|$warehouse|%'", $where_sert, $where_bu, $group_by), array($awal, $akhir, $awal, $akhir));
        $return = $query->result_array();
        //**//
        $survey = $this->db->query(sprintf($sql, 
                    "DISTINCT(farmer_id), survey_production, survey_production+(0.1*survey_production) quota, IFNULL(1_orgid,2_orgid) orgid","'%|$warehouse|%'", $where_sert, $where_bu, ''), array($awal, $akhir, $awal, $akhir, $sert));
        //echo "<pre>".$this->db->last_query();exit;
        $surveys = array();
        foreach ($survey->result() as $row){
           $surveys[$row->orgid]['survey'] =  $surveys[$row->orgid]['survey'] + $row->survey_production;
           $surveys[$row->orgid]['quota'] =  $surveys[$row->orgid]['quota'] + $row->quota;
        }
        $surveys = array_values($surveys);
        for($i=0;$i<count($surveys);$i++){
            $return[$i]['survey'] = $surveys[$i]['survey'];
            $return[$i]['quota'] = $surveys[$i]['quota'];
        }
        //**//
        return $return;
    }
    
    function getReportTraceabilityBuTrans($awal, $akhir, $provinsi, $warehouse, $coop, $bu, $sert/*, $start = 0, $limit = 50*/){
        if($sert=='1'){
            $where_sert = "AND farmer_iscertified='$sert'";
        }else{
            $where_sert = "";
        }
        
        /*if($coop==''){
            $where_coop = "";
        }else{
            $c = explode("::", $coop);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$incoop .= ",";
                }
                @$incoop .=$value;

                $i++;
            }
            $where_coop = "AND 2_orgid IN ($incoop)";
        }*/
        
        if($bu==''){
            $where_bu = "";
        }else{
            $c = explode("::", $bu);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$inbu.= ",";
                }
                @$inbu .=$value;

                $i++;
            }
            $where_bu = "AND IFNULL(1_orgid,2_orgid) IN ($inbu)";
        }
        
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                IFNULL(1_date, 2_date) datetransaction,
                IFNULL(1_po, 2_po) po,
                ksb.SupplyBatchNumber batchnumber,
                IFNULL(1_status, 2_status) batchstatus,
                CONCAT('[',IFNULL(1_orgtype, 2_orgtype),'] ',IF(1_orgid IS  NULL,2_name,1_name)) name,
                /*CONCAT('[',IFNULL(1_destorgid, 2_destorgid),'] - ',IFNULL(1_destname, 2_destname))*/ IFNULL(1_destname, 2_destname) destination,
                SUM(IFNULL(1_bruto, 2_bruto)) bruto,
                SUM(IFNULL(1_netto, 2_netto)) netto
            FROM 
                rpt_traceability rt
                LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyBatchID=wh_batchid
            WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND ( IF(1_orgid IS NULL,2_orgtype,'Pedagang')!='koperasi') AND wh_dest LIKE %s %s %s GROUP BY IFNULL(1_batchid,2_batchid) ORDER BY IFNULL(1_orgid,2_orgid), IFNULL(1_date,2_date)#LIMIT ?, ?";
        $query = $this->db->query(sprintf($sql, "'%|$warehouse|%'", $where_sert, $where_bu), array($awal, $akhir, $awal, $akhir/*, intval($start), intval($limit)*/));
        //echo "<pre>".$this->db->last_query();exit;
        //$return = $query->result_array();
        //return $return;

        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }else{
            return false;
        }
    }
    
    function getReportTraceabilityFarmer($awal, $akhir, $provinsi, $warehouse, $sert, $coop, $bu, $farmer){
        if($sert=='1'){
            $where_sert = "AND farmer_iscertified='$sert'";
        }else{
            $where_sert = "";
        }
        /*if($coop==''){
            $where_coop = "";
        }else{
            $c = explode("::", $coop);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$incoop .= ",";
                }
                @$incoop .=$value;

                $i++;
            }
            $where_coop = "AND 2_orgid IN ($incoop)";
        }
        
        if($bu==''){
            $where_bu = "";
        }else{
            $c = explode("::", $bu);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$inbu.= ",";
                }
                @$inbu .=$value;

                $i++;
            }
            $where_bu = "AND IFNULL(1_orgid,2_orgid) IN ($inbu)";
        }*/
        
        if($farmer==''){
            $where_farmer = "";
        }else{
            $c = explode("::", $farmer);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$infarmer.= ",";
                }
                @$infarmer .=$value;
                $i++;
            }
            $where_farmer = "AND farmer_id IN ($infarmer)";
        }
        
        $group_by = "GROUP BY farmer_id ORDER BY farmer_id";
        $sql = "
            SELECT 
                %s
            FROM 
               rpt_traceability rt
                LEFT JOIN (
                    SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                        d.FarmerID
                    FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE 
                        !(? > CertificationEnd OR ? < CertificationStart) AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                    INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                        z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                    GROUP BY d.FarmerID
                ) e ON farmer_id=e.FarmerID
                LEFT JOIN ktv_farmer kf ON kf.FarmerID=farmer_id
                LEFT JOIN ktv_cpg cp ON cp.CPGid=kf.CPGid
                LEFT JOIN ktv_village v ON v.VillageID=Farmer_villageid
            WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND wh_dest LIKE %s %s %s %s";
        $query = $this->db->query(sprintf($sql, 
                    "farmer_name name,
                    COUNT(DISTINCT IFNULL(1_transid,2_transid)) AS transcount,
                    #COUNT(DISTINCT IFNULL(1_batchid,2_batchid)) AS batchcount,
                    COUNT(DISTINCT IF(wh_orgid IS NOT NULL OR 1_orgid IS NOT NULL,IFNULL(1_transid,2_transid),NULL)) deliveredcount,
                    survey_production survey, survey_production+(0.1*survey_production) quota,
                    CONCAT('[',cp.CPGid,'] ',cp.GroupName) cpg, 
                    v.Village village,
                    SUM(IFNULL(1_netto, 2_netto)) bruto,
                    SUM(farmer_netto) netto","'%|$warehouse|%'", $where_sert, $where_farmer, $group_by), array($awal, $akhir, $awal, $akhir));
        $return = $query->result_array();
        //echo "<pre>".print_r($return,1);exit;
        //echo "<pre>".$this->db->last_query();exit;
        //$survey = $this->db->query(sprintf($sql, 
        //            "DISTINCT(farmer_id), survey_production, survey_production+(0.1*survey_production) quota, 2_orgid","'%|$warehouse|%'", $where_sert, $where_coop), array($awal, $akhir, $awal, $akhir, $sert));
        //echo "<pre>".$this->db->last_query();exit;
        //$survey_production = 0;
        //$quota = 0;
        //foreach ($survey->result() as $row) {
            //@$survey_production[$row->2_orgid] = @$survey_production[$row->2_orgid] + $row->survey_production;
            //$quota = $quota + $row->quota;
        //}
        //echo "<pre>".$this->db->last_query();exit;
        //$return[0]['survey'] = $survey_production;
        //$return[0]['quota'] = $quota;
        return $return;
    }
    
    function getReportTraceabilityFarmerTrans($awal, $akhir, $provinsi, $warehouse, $coop, $bu, $farmer, $sert/*, $start = 0, $limit = 50*/){
        if($sert=='1'){
            $where_sert = "AND farmer_iscertified='$sert'";
        }else{
            $where_sert = "";
        }
        
        /*if($coop==''){
            $where_coop = "";
        }else{
            $c = explode("::", $coop);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$incoop .= ",";
                }
                @$incoop .=$value;

                $i++;
            }
            $where_coop = "AND 2_orgid IN ($incoop)";
        }
        
        if($bu==''){
            $where_bu = "";
        }else{
            $c = explode("::", $bu);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$inbu.= ",";
                }
                @$inbu .=$value;

                $i++;
            }
            $where_bu = "AND IFNULL(1_orgid,2_orgid) IN ($inbu)";
        }*/
        
        if($farmer==''){
            $where_farmer = "";
        }else{
            $c = explode("::", $farmer);
            $i = 0; 
            foreach ($c as $key => $value) {
                if($i!=0){
                    @$infarmer.= ",";
                }
                @$infarmer .=$value;
                $i++;
            }
            $where_farmer = "AND farmer_id IN ($infarmer)";
        }
        
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                IFNULL(1_date, 2_date) datetransaction,
                IFNULL(1_transid, 2_transid) transactionnumber,
                IFNULL(1_po, 2_po) po,
                ksb.SupplyBatchNumber batchnumber,
                IFNULL(1_status, 2_status) batchstatus,
                farmer_name name,
                CONCAT('[',IFNULL(1_orgtype, 2_orgtype),'] ',IF(1_orgid IS  NULL,2_name,1_name)) destination,
                IFNULL(1_netto, 2_netto) bruto,
                farmer_netto netto,
                CONCAT('[',cp.CPGid,'] ',cp.GroupName) cpg, 
                v.Village village, survey_production survey, survey_production+(0.1*survey_production) quota
            FROM 
                rpt_traceability rt
                LEFT JOIN ktv_supplychain_batch ksb ON ksb.SupplyBatchID=wh_batchid
                LEFT JOIN ktv_farmer kf ON kf.FarmerID=farmer_id
                LEFT JOIN ktv_cpg cp ON cp.CPGid=kf.CPGid
                LEFT JOIN ktv_village v ON v.VillageID=Farmer_villageid
                LEFT JOIN (
                    SELECT SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
                        (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
                        (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) survey_production,
                        d.FarmerID
                    FROM (
                     SELECT FarmerID,GardenNr,MAX(SurveyNr) AS SurveyNr
                     FROM ktv_certification
                     WHERE 
                        !(? > CertificationEnd OR ? < CertificationStart) AND ExternalDate != '0000-00-00' AND
                        ExternalDate IS NOT NULL GROUP BY FarmerID,GardenNr) z
                    INNER JOIN ktv_farmer_garden d ON z.FarmerID = d.FarmerID AND z.GardenNr=d.GardenNr AND
                        z.SurveyNr=d.SurveyNr AND GardenHaUnCertified>0
                    GROUP BY d.FarmerID
                ) e ON farmer_id=e.FarmerID
            WHERE (IFNULL(wh_date,IFNULL(2_date,1_date)) BETWEEN ? AND ?) AND wh_dest LIKE %s %s %s ORDER BY farmer_id, IFNULL(1_date,2_date) #LIMIT ?, ?";
        $query = $this->db->query(sprintf($sql, "'%|$warehouse|%'", $where_sert, $where_farmer), array($awal, $akhir, $awal, $akhir/*, intval($start), intval($limit)*/));
        //echo "<pre>".$this->db->last_query();exit;
        //$return = $query->result_array();
        //return $return;

        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }else{
            return false;
        }
    }

    function getSupplychainArea($SupplychainID)
    {
        $sql = "
            SELECT  a.AreaID, b.District, c.Province
                FROM ktv_supplychain_area a
                LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID
                LEFT JOIN ktv_province c ON c.ProvinceID=b.ProvinceID
                WHERE a.SupplychainID=?";
        $query = $this->db->query($sql, array($SupplychainID));
        $result['data'] = $query->result_array();
        $result['total'] = $query->num_rows();;
        return $result;
    }

    function deleteArea($AreaID){
        $sql = "DELETE FROM ktv_supplychain_area WHERE AreaID=?";
        $query = $this->db->query($sql, array($AreaID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record deleted.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to delete record.";
        }
        return $results;
    }

    public function listProvinces(){
        $sql = "SELECT ProvinceID id, Province label FROM ktv_province WHERE active='1' AND StatusCode!='nullified'";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }
    
    public function listDistricts($ProvinceID, $SupplychainID){
        $sql = " SELECT a.DistrictID id, a.District label FROM ktv_district a LEFT JOIN ktv_supplychain_area b ON a.DistrictID=b.DistrictID AND b.SupplychainID=? WHERE b.AreaID IS NULL AND a.active='1' AND a.StatusCode!='nullified' AND a.ProvinceID=?";
        $query = $this->db->query($sql,array($SupplychainID, $ProvinceID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }

    public function addArea($SupplychainID, $DistrictID){
        $sql = " INSERT INTO ktv_supplychain_area(SupplychainID, DistrictID) VALUES (?,?)";
        $query = $this->db->query($sql, array($SupplychainID, $DistrictID));
        if ($query){
            $results['success']     = true;
            $results['message']     = "Area added.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to add area";
        }
        
        return $results;
    }

}

?>

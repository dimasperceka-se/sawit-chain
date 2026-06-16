<?php
class Mvillage extends CI_Model {
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }

    function readVillages($prov,$kab,$kec,$key,$sort,$start,$limit){
        // if (substr($kab,0,1)=='[') $kab = str_replace("[","",str_replace("]","",$kab));
        // else $kab = "'$kab'";
        $sort=json_decode($sort);
		$order = ($sort[0]->property==''?' b.SubDistrict ':$sort[0]->property);
		$by = ($sort[0]->direction==''?'ASC':$sort[0]->direction);
		$order_by = $sort[0]->property.' '.$sort[0]->direction.', '.@$sort[1]->property.' '.@$sort[1]->direction;

        $where = '';
        if (!empty($prov)) {
            $where .= " AND c.ProvinceID = {$prov}";
        }
        if (!empty($kab)) {
            $where .= " AND c.DistrictID = {$kab}";
        }
        if (!empty($kec)) {
            $where .= " AND b.SubDistrictID = {$kec}";
        }
        if (!empty($this->user['district_access'])) {
            $where .= " AND c.DistrictID IN ({$this->user['district_access']})";
        }
           $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
              a.VillageID AS id, a.Village, a.SubDistrictID, b.SubDistrict, b.DistrictID, c.District, c.ProvinceID, a.VillageHeadName, a.VillageHeadGender, a.VillageHeadLatitude, a.VillageHeadLongitude
            from ktv_village a
            left join ktv_subdistrict b on a.SubDistrictID=b.SubDistrictID
            left join ktv_district c on b.DistrictID=c.DistrictID
            WHERE
            1 = 1 AND
            a.StatusCode != 'nullified' AND
            (a.VillageID like ? OR a.Village like ? OR a.VillageHeadName like ?)
            %s
			%s
            ";
           $query = $this->db->query(sprintf($sql,
           $where,
           'ORDER BY '.$order_by.' LIMIT ?,?'),
            array("%$key%","%$key%","%$key%",(int)$start,(int)$limit));
           // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
		   $queryTotal = $this->db->query('SELECT FOUND_ROWS() AS total');

        $result['data'] = $query->result_array();
        $result['total'] = $queryTotal->row()->total;
        return $result;
    }

	function readProvinsis($sesPartner){
        if($sesPartner != 'ALL'){
            $join = 'LEFT JOIN ktv_cpg_partner b ON SUBSTR(b.CPGid,1,2) = a.ProvinceID';
            $where = " AND b.PartnerID = {$sesPartner} ";
        }
        $sql = "SELECT
                    distinct a.Province as label,
                    a.ProvinceID as id
                FROM
                    ktv_province a
                    $join
                WHERE
                    a.ProvinceID not in (12,31)
                    $where
                ORDER BY a.Province";
        $query = $this->db->query($sql);
		$result['data'] = $query->result_array();
        return $result;
    }

	function readKabupatens($provID='',$partnerID){
		$sql_where = "and DistrictID not in (1171,7373,7271,1377,7371)";
        if($partnerID != 'ALL'){
            $sql_where2 = " AND DistrictID in (SELECT DistrictID FROM ktv_district_partner WHERE PartnerID = {$partnerID})";
        }
        $sql = "SELECT distinct District as label, DistrictID as id
                FROM ktv_district a
                    LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID
                WHERE
                    a.ProvinceID = ? OR b.Province = ? %s %s
                ORDER BY District";
        $query = $this->db->query(sprintf($sql,$sql_where,@$sql_where2), array($provID,$provID));

		$result['data'] = $query->result_array();
        return $result;
    }

	function readKabupatenForms($key,$partnerID){
		$sql_where = "and DistrictID not in (1171,7373,7271,1377,7371)";
        if($partnerID != 'ALL'){
            $sql_where2 = " AND DistrictID in (SELECT DistrictID FROM ktv_district_partner WHERE PartnerID = {$partnerID})";
        }
        $sql = "SELECT distinct District as label, DistrictID as id
                FROM ktv_district a
                    LEFT JOIN ktv_province b ON a.ProvinceID=b.ProvinceID
                WHERE
                    a.ProvinceID = ? OR b.Province = ? %s %s
                ORDER BY District";
        $query = $this->db->query(sprintf($sql,$sql_where,@$sql_where2), array($key,$key));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readKecamatans($key){
        $sql = "
            SELECT distinct SubDistrict as label, SubDistrictID AS id
            FROM ktv_subdistrict a
            LEFT JOIN ktv_district b ON a.DistrictID=b.DistrictID
            WHERE a.DistrictID = ?
            ORDER BY SubDistrict";
        $query = $this->db->query($sql, array($key));
        $return['data'] = $query->result_array();
        return $return;
    }

	function readNewVillageID($SubDistrictID, $VillageID){
        $SubDistrictID_old = substr($VillageID,0,7);
		if($SubDistrictID==$SubDistrictID_old){
			$newVillageID = $VillageID;
		}else{
			$sql = " SELECT MAX(VillageID) AS maks FROM ktv_village WHERE SubDistrictID=?";
			$query = $this->db->query($sql, array($SubDistrictID));
			$oldVillageID = @$query->row()->maks;
			if($oldVillageID == ""){
				$newVillageID = $SubDistrictID."001";
			}else{
				$exp = explode($SubDistrictID, $oldVillageID);
				$new = $exp[1] + 1;
				$jml = strlen($new);
				if($jml == 1){
					$new = "00".$new;
				}else if($jml == 2){
					$new = "0".$new;
				}
				$newVillageID = $SubDistrictID.$new;
			}
		}
		return $newVillageID;
    }

    public function getNewVillageID($SubDistrictID)
    {
        $sql = "SELECT
    MAX(v.VillageID) AS id
FROM ktv_village v
WHERE
    v.SubDistrictID = ?
GROUP BY v.SubDistrictID
        ";
        $query = $this->db->query($sql, array($SubDistrictID));
        if ($query->num_rows()>0) {
            $result = $query->row_array(0);
            return $result['id']+1;
        }
        return $SubDistrictID.'001';
    }

	function createVillage($SubDistrictID, $VillageID, $Village, $VillageHeadName, $VillageHeadGender, $VillageHeadLatitude, $VillageHeadLongitude){
        $sql_village = "
            INSERT INTO ktv_village(VillageID, Village, SubDistrictID, VillageHeadName, VillageHeadGender, VillageHeadLatitude, VillageHeadLongitude, LastModifiedBy, DateUpdated)
            VALUES (?,?,?,?,?,?,?,?,NOW())";
        $query = $this->db->query($sql_village, array($VillageID, $Village, $SubDistrictID, $VillageHeadName, $VillageHeadGender, $VillageHeadLatitude, $VillageHeadLongitude, $_SESSION['userid']));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

	function readVillage($id){
        $sql = "SELECT a.VillageID, a.Village, a.VillageHeadName, a.VillageHeadGender, a.VillageHeadLatitude, a.VillageHeadLongitude, a.SubDistrictID, b.DistrictID, c.ProvinceID
				,b.SubDistrict, c.District
			FROM ktv_village a
				LEFT JOIN ktv_subdistrict b ON a.SubDistrictID=b.SubDistrictID
				LEFT JOIN ktv_district c ON b.DistrictID=c.DistrictID
            WHERE a.VillageID=?
            ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

	function updateVillage($VillageID_old, $SubDistrictID, $VillageID, $Village, $VillageHeadName, $VillageHeadGender, $VillageHeadLatitude, $VillageHeadLongitude){
      $sql = "
         UPDATE ktv_village
         SET SubDistrictID=?, VillageID=?, Village=?, VillageHeadName=?, VillageHeadGender=?, VillageHeadLatitude=?, VillageHeadLongitude=?, DateUpdated=now(), LastModifiedBy=?
         WHERE VillageID=?";
      $query = $this->db->query($sql, array($SubDistrictID, $VillageID, $Village, $VillageHeadName, $VillageHeadGender, $VillageHeadLatitude, $VillageHeadLongitude,
         $_SESSION['userid'],$VillageID_old));
      if ($query) {
         $results['success'] = true;
         $results['message'] = "record updated.";
      } else {
         $results['success'] = false;
         $results['message'] = "Failed to update record";
      }
      return $results;
	}

	function deleteVillage($VillageID){
      //$sql = "DELETE FROM ktv_village WHERE VillageID=?";
      $sql="UPDATE ktv_village SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE VillageID = ? LIMIT 1";
      $query = $this->db->query($sql, array($VillageID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
	//**Start Crop Village**//
	function readCrops($VillageID,$start,$limit){
        $sql = "
            select %s
            from ktv_village_crop
			   WHERE VillageID = ? AND
            StatusCode != 'nullified'
            %s
            ";
           $query = $this->db->query(sprintf($sql,"VillageCropID, VillageID, (CASE CropName WHEN 1 THEN 'Kakao' WHEN 2 THEN 'Jagung' WHEN 3 THEN 'Sawit' WHEN 4 THEN 'Karet' WHEN 5 THEN 'Cengkeh' WHEN 6 THEN 'Padi' WHEN 7 THEN 'Buah-buahan' WHEN 8 THEN 'Kayu-kayuan' END) AS CropName, CropYear, CropFarmers, CropHectares, CropProduction",
            'LIMIT ?,?'),
            array($VillageID, (int)$start, (int)$limit));
            $queryTotal = $this->db->query(sprintf($sql,'count(*) as total','',''),array($VillageID));
            $result['data'] = $query->result_array();
            $result['total'] = $queryTotal->row()->total;
            return $result;
    }

	function createCrop($CropVillageID, $CropName, $CropYear, $CropFarmers, $CropHectares, $CropProduction){
        $sql_village = "
            INSERT INTO ktv_village_crop(VillageID, CropName, CropYear, CropFarmers, CropHectares, CropProduction, CreatedBy, DateCreated)
            VALUES (?,?,?,?,?,?,?,NOW())";
        $query = $this->db->query($sql_village, array($CropVillageID, $CropName, $CropYear, $CropFarmers, $CropHectares, $CropProduction, $_SESSION['userid']));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

	function readCrop($id){
        $sql = "SELECT * FROM ktv_village_crop WHERE VillageCropID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

	function updateCrop($VillageCropID, $CropVillageID, $CropName, $CropYear, $CropFarmers, $CropHectares, $CropProduction){
      $sql = "
         UPDATE ktv_village_crop
         SET VillageID=?, CropName=?, CropYear=?, CropFarmers=?, CropHectares=?, CropProduction=?, LastModifiedBy=?, DateUpdated=NOW()
         WHERE VillageCropID=?";
      $query = $this->db->query($sql, array($CropVillageID, $CropName, $CropYear, $CropFarmers, $CropHectares, $CropProduction,
         $_SESSION['userid'],$VillageCropID));
      if ($query) {
         $results['success'] = true;
         $results['message'] = "record updated.";
      } else {
         $results['success'] = false;
         $results['message'] = "Failed to update record";
      }
      return $results;
	}

	function deleteCrop($VillageCropID){
      //$sql = "DELETE FROM ktv_village_crop WHERE VillageCropID=?";
      $sql="UPDATE ktv_village_crop SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE VillageCropID = ? LIMIT 1";
      $query = $this->db->query($sql, array($VillageCropID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
	//**End Crop Village**//
	//**Start Infrastructure Village**//
	function readInfrastructures($VillageID, $start, $limit){
        $sql = "
            select %s
            from ktv_village_infrastructure
			   WHERE
            VillageID = ? AND
            StatusCode != 'nullified'
            %s
            ";
           $query = $this->db->query(sprintf($sql,"InfrastructureID, VillageID, (CASE InfrastructureType WHEN 1 THEN 'School' WHEN 2 THEN 'Health Facility' WHEN 3 THEN 'Others' END) AS InfrastructureType, InfrastructureName, Latitude, Longitude",
            'LIMIT ?,?'),
            array($VillageID, (int)$start, (int)$limit));
            $queryTotal = $this->db->query(sprintf($sql,'count(*) as total','',''),array($VillageID));
            $result['data'] = $query->result_array();
            $result['total'] = $queryTotal->row()->total;
            return $result;
    }

	function createInfrastructure($InfrastructureVillageID, $InfrastructureType, $InfrastructureName, $Latitude, $Longitude){
        $sql_village = "
            INSERT INTO ktv_village_infrastructure(VillageID, InfrastructureType, InfrastructureName, Latitude, Longitude, CreatedBy, DateCreated)
            VALUES (?,?,?,?,?,?,NOW())";
        $query = $this->db->query($sql_village, array($InfrastructureVillageID, $InfrastructureType, $InfrastructureName, $Latitude, $Longitude, $_SESSION['userid']));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

	function readInfrastructure($id){
        $sql = "SELECT * FROM ktv_village_infrastructure WHERE InfrastructureID=?
            ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

	function updateInfrastructure($InfrastructureID, $InfrastructureVillageID, $InfrastructureType, $InfrastructureName, $Latitude, $Longitude){
      $sql = "
         UPDATE ktv_village_infrastructure
         SET VillageID=?, InfrastructureType=?, InfrastructureName=?, Latitude=?, Longitude=?, LastModifiedBy=?, DateUpdated=NOW()
         WHERE InfrastructureID=?";
      $query = $this->db->query($sql, array($InfrastructureVillageID, $InfrastructureType, $InfrastructureName, $Latitude, $Longitude,
         $_SESSION['userid'],$InfrastructureID));
      if ($query) {
         $results['success'] = true;
         $results['message'] = "record updated.";
      } else {
         $results['success'] = false;
         $results['message'] = "Failed to update record";
      }
      return $results;
	}

	function deleteInfrastructure($InfrastructureID){
      //$sql = "DELETE FROM ktv_village_infrastructure WHERE InfrastructureID=?";
      $sql="UPDATE ktv_village_infrastructure SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."', DateUpdated=NOW() WHERE InfrastructureID = ? LIMIT 1";
      $query = $this->db->query($sql, array($InfrastructureID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
	//**End Infrastructure Village**//
}
?>

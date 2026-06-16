<?php
/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Fri Mar 06 2020
 *  File : mregion.php
 *******************************************/
class Mregion_dq extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetCountry() {
        $sql="SELECT
                    a.`ISO2` AS id
                    , a.`CountryName` AS `name`
                    , a.ISO2 AS code
                    , 'active' StatusCode
                FROM
                    ktv_country a
                WHERE 1=1
                    AND a.ISO2 IN(SELECT DISTINCT CountryCode FROM ktv_province)
                ORDER BY a.`CountryName` ASC";
        $query = $this->db->query($sql);
	    return $query->result_array();
    }

    public function GetProvince($CountryID) {
        $sql="SELECT
                    a.`ProvinceID` AS id
                    , a.`Province` AS `name`
                    , '' AS code
                    , a.StatusCode
                FROM
                    ktv_province a
                WHERE 1=1
                    #AND a.`StatusCode` = 'active'
                    AND a.CountryCode = ?
                ORDER BY a.`Province` ASC";
        $query = $this->db->query($sql,array($CountryID));
	    return $query->result_array();
    }

    public function GetDistrict($ProvinceID) {
        $sql="SELECT
                    a.`DistrictID` AS id
                    , a.`District` AS `name`
                    , '' AS code
                    , a.StatusCode
                FROM
                    ktv_district a
                WHERE 1=1
                    #AND a.`StatusCode` = 'active'
                    AND a.ProvinceID = ?
                ORDER BY a.`District` ASC";
        $query = $this->db->query($sql,array($ProvinceID));
	    return $query->result_array();
    }

    public function GetSubDistrict($DistrictID) {
        $sql="SELECT
                    a.`SubDistrictID` AS id
                    , a.`SubDistrict` AS `name`
                    , '' AS code
                    , a.StatusCode
                FROM
                    ktv_subdistrict a
                WHERE 1=1
                    #AND a.`StatusCode` = 'active'
                    AND a.DistrictID = ?
                ORDER BY a.`SubDistrict` ASC";
        $query = $this->db->query($sql,array($DistrictID));
	    return $query->result_array();
    }

    public function GetVillage($SubDistrictID) {
        $sql="SELECT
                    a.`VillageID` AS id
                    , a.`Village` AS `name`
                    , '' AS code
                    , a.StatusCode
                FROM
                    ktv_village a
                WHERE 1=1
                    #AND a.`StatusCode` = 'active'
                    AND a.SubDistrictID = ?
                ORDER BY a.`Village` ASC";
        $query = $this->db->query($sql,array($SubDistrictID));
	    return $query->result_array();
    }

    public function GenID($type,$parent_id) {
        //echo '<pre>'; print_r(array($type,$parent_id)); exit;

        switch($type) {
            case 'country':
                $sql = "SELECT
                            (a.`CountryID` + 1) AS NewID
                        FROM
                            reg_country a
                        WHERE 1=1
                        ORDER BY a.`CountryID` DESC
                        LIMIT 1";
                $data = $this->db->query($sql)->row_array();
                if($data['NewID'] == "") {
                    return '10';
                } else {
                    return $data['NewID'];
                }
            break;
            case 'province':
                $sql = "SELECT
                            (a.`ProvinceID` + 1) AS NewID
                        FROM
                            reg_province a
                        WHERE 1=1
                            AND a.`CountryID` = ?
                        ORDER BY a.`ProvinceID` DESC
                        LIMIT 1";
                $data = $this->db->query($sql,array($parent_id))->row_array();
                if($data['NewID'] == "") {
                    return $parent_id.'01';
                } else {
                    return $data['NewID'];
                }
            break;
            case 'district':
                $sql = "SELECT
                            (a.`DistrictID` + 1) AS NewID
                        FROM
                            reg_district a
                        WHERE 1=1
                            and a.`ProvinceID` = ?
                        ORDER BY a.`DistrictID` DESC
                        LIMIT 1";
                $data = $this->db->query($sql,array($parent_id))->row_array();
                if($data['NewID'] == "") {
                    return $parent_id.'0001';
                } else {
                    return $data['NewID'];
                }
            break;
            case 'subdistrict':
                $sql = "SELECT
                            (a.`SubDistrictID` + 1) AS NewID
                        FROM
                            reg_subdistrict a
                        WHERE 1=1
                            and a.DistrictID = ?
                        ORDER BY a.`SubDistrictID` DESC
                        LIMIT 1";
                $data = $this->db->query($sql,array($parent_id))->row_array();
                if($data['NewID'] == "") {
                    return $parent_id.'0001';
                } else {
                    return $data['NewID'];
                }
            break;
            case 'village':
                $sql = "SELECT
                            (a.`VillageID` + 1) AS NewID
                        FROM
                            reg_village a
                        WHERE 1=1
                            and a.SubDistrictID = ?
                        ORDER BY a.`VillageID` DESC
                        LIMIT 1";
                $data = $this->db->query($sql,array($parent_id))->row_array();
                if($data['NewID'] == "") {
                    return $parent_id.'0001';
                } else {
                    return $data['NewID'];
                }
            break;
        }
    }

    public function getCountryDetail($id) {
		$this->db->select('CountryID AS id, CountryCode AS code, CountryName AS name, StatusCode', FALSE);
		$query = $this->db->get_where('reg_country', array('CountryID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
    }

    public function getProvinceDetail($id) {
		$this->db->select('ProvinceID AS id, ProvinceCode AS code, ProvinceName AS name, StatusCode', FALSE);
		$query = $this->db->get_where('reg_province', array('ProvinceID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
    }

    public function getDistrictDetail($id) {
		$this->db->select('DistrictID AS id, DistrictCode AS code, DistrictName AS name, StatusCode', FALSE);
		$query = $this->db->get_where('reg_district', array('DistrictID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
    }

    public function getSubDistrictDetail($id) {
		$this->db->select('SubDistrictID AS id, SubDistrictCode AS code, SubDistrictName AS name, StatusCode', FALSE);
		$query = $this->db->get_where('reg_subdistrict', array('SubDistrictID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
    }

    public function getVillageDetail($id) {
		$this->db->select('VillageID AS id, VillageCode AS code, VillageName AS name, StatusCode', FALSE);
		$query = $this->db->get_where('reg_village', array('VillageID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
    }

    public function addCountry($id, $code, $name, $parent_id, $StatusCode) {
        $this->db->trans_begin();

        $query = $this->db->insert('reg_country', array('CountryID' => $id, 'CountryCode' => $code, 'CountryName' => $name, 'StatusCode' => $StatusCode));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function addProvince($id, $code, $name, $parent_id, $StatusCode) {
        $this->db->trans_begin();

        $query = $this->db->insert('reg_province', array('ProvinceID' => $id, 'CountryID' => $parent_id, 'ProvinceCode' => $code, 'ProvinceName' => $name, 'StatusCode' => $StatusCode));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function addDistrict($id, $code, $name, $parent_id, $StatusCode) {
        $this->db->trans_begin();

        $query = $this->db->insert('reg_district', array('DistrictID' => $id, 'ProvinceID' => $parent_id, 'DistrictCode' => $code, 'DistrictName' => $name, 'StatusCode' => $StatusCode));

        //reg_region
        $RegID = $id.'00000000';
        $CountryID = substr($id,0,2);
        $ProvinceID = $parent_id;
        $sql = "INSERT INTO `reg_region` SET
                `RegID` = ?,
                `RegType` = 'District',
                `RegLabel` = ?,
                `CountryID` = ?,
                `ProvinceID` = ?,
                `DistrictID` = ?,
                `SubDistrictID` = NULL,
                `VillageID` = NULL";
        $p = array(
            $RegID,
            $name,
            $CountryID,
            $ProvinceID,
            $id
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function addSubDistrict($id, $code, $name, $parent_id, $StatusCode) {
        $this->db->trans_begin();

        $query = $this->db->insert('reg_subdistrict', array('SubDistrictID' => $id, 'DistrictID' => $parent_id, 'SubDistrictCode' => $code, 'SubDistrictName' => $name, 'StatusCode' => $StatusCode));

        //reg_region
        $RegID = $id.'0000';
        $CountryID = substr($id,0,2);
        $ProvinceID = substr($id,0,4);
        $DistrictID = $parent_id;
        $sql = "INSERT INTO `reg_region` SET
                `RegID` = ?,
                `RegType` = 'SubDistrict',
                `RegLabel` = ?,
                `CountryID` = ?,
                `ProvinceID` = ?,
                `DistrictID` = ?,
                `SubDistrictID` = ?,
                `VillageID` = NULL";
        $p = array(
            $RegID,
            $name,
            $CountryID,
            $ProvinceID,
            $DistrictID,
            $id
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function addVillage($id, $code, $name, $parent_id, $StatusCode) {
        $this->db->trans_begin();

        $query = $this->db->insert('reg_village', array('VillageID' => $id, 'SubDistrictID' => $parent_id, 'VillageCode' => $code, 'VillageName' => $name, 'StatusCode' => $StatusCode));

        //reg_region
        $RegID = $id;
        $CountryID = substr($id,0,2);
        $ProvinceID = substr($id,0,4);
        $DistrictID = substr($id,0,8);
        $SubDistrictID = $parent_id;
        $sql = "INSERT INTO `reg_region` SET
                `RegID` = ?,
                `RegType` = 'Village',
                `RegLabel` = ?,
                `CountryID` = ?,
                `ProvinceID` = ?,
                `DistrictID` = ?,
                `SubDistrictID` = ?,
                `VillageID` = ?";
        $p = array(
            $RegID,
            $name,
            $CountryID,
            $ProvinceID,
            $DistrictID,
            $SubDistrictID,
            $id
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function updateCountry($id, $code, $name, $StatusCode) {
        $this->db->trans_begin();

        $sql = "UPDATE reg_country a SET
                    a.`CountryCode` = ?
                    , a.`CountryName` = ?
                    , a.StatusCode = ?
                WHERE
                    a.`CountryID` = ?
                LIMIT 1";
        $p = array(
            $code,$name,$StatusCode,$id
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function updateProvince($id, $code, $name, $StatusCode) {
        $this->db->trans_begin();

        $sql = "UPDATE reg_province a SET
                    a.`ProvinceCode` = ?
                    , a.`ProvinceName` = ?
                    , a.StatusCode = ?
                WHERE
                    a.`ProvinceID` = ?
                LIMIT 1";
        $p = array(
            $code,$name,$StatusCode,$id
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function updateDistrict($id, $code, $name, $StatusCode) {
        $this->db->trans_begin();

        $sql = "UPDATE reg_district a SET
                    a.`DistrictCode` = ?
                    , a.`DistrictName` = ?
                    , a.StatusCode = ?
                WHERE
                    a.`DistrictID` = ?
                LIMIT 1";
        $p = array(
            $code,$name,$StatusCode,$id
        );
        $query = $this->db->query($sql,$p);

        //reg_region
        $sql = "UPDATE reg_region a SET
                    a.`RegLabel` = ?
                    , a.`SubDistrictID` = NULL
                    , a.`VillageID` = NULL
                WHERE
                    a.`RegType` = ?
                    AND a.`DistrictID` = ?
                LIMIT 1";
        $p = array(
            $name,
            'District',
            $id
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function updateSubDistrict($id, $code, $name, $StatusCode) {
        $this->db->trans_begin();

        $sql = "UPDATE reg_subdistrict a SET
                    a.`SubDistrictCode` = ?
                    , a.`SubDistrictName` = ?
                    , a.StatusCode = ?
                WHERE
                    a.`SubDistrictID` = ?
                LIMIT 1";
        $p = array(
            $code,$name,$StatusCode,$id
        );
        $query = $this->db->query($sql,$p);

        //reg_region
        $sql = "UPDATE reg_region a SET
                    a.`RegLabel` = ?
                    , a.`VillageID` = NULL
                WHERE
                    a.`RegType` = ?
                    AND a.`SubDistrictID` = ?
                LIMIT 1";
        $p = array(
            $name,
            'SubDistrict',
            $id
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function updateVillage($id, $code, $name, $StatusCode) {
        $this->db->trans_begin();

        $sql = "UPDATE reg_village a SET
                    a.`VillageCode` = ?
                    , a.`VillageName` = ?
                    , a.StatusCode = ?
                WHERE
                    a.`VillageID` = ?
                LIMIT 1";
        $p = array(
            $code,$name,$StatusCode,$id
        );
        $query = $this->db->query($sql,$p);

        //reg_region
        $sql = "UPDATE reg_region a SET
                    a.`RegLabel` = ?
                WHERE
                    a.`RegType` = ?
                    AND a.`VillageID` = ?
                LIMIT 1";
        $p = array(
            $name,
            'Village',
            $id
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function deleteCountry($id) {
        $this->db->trans_begin();

        $sql = "DELETE FROM reg_country WHERE CountryID = ? LIMIT 1";
        $query = $this->db->query($sql,array($id));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function deleteProvince($id) {
        $this->db->trans_begin();

        $sql = "DELETE FROM reg_province WHERE ProvinceID = ? LIMIT 1";
        $query = $this->db->query($sql,array($id));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function deleteDistrict($id) {
        $this->db->trans_begin();

        $sql = "DELETE FROM reg_region WHERE RegType = 'District' AND DistrictID = ? LIMIT 1";
        $query = $this->db->query($sql,array($id));

        $sql = "DELETE FROM reg_district WHERE DistrictID = ? LIMIT 1";
        $query = $this->db->query($sql,array($id));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function deleteSubDistrict($id) {
        $this->db->trans_begin();

        $sql = "DELETE FROM reg_region WHERE RegType = 'SubDistrict' AND SubDistrictID = ? LIMIT 1";
        $query = $this->db->query($sql,array($id));

        $sql = "DELETE FROM reg_subdistrict WHERE SubDistrictID = ? LIMIT 1";
        $query = $this->db->query($sql,array($id));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function deleteVillage($id) {
        $this->db->trans_begin();

        $sql = "DELETE FROM reg_region WHERE RegType = 'Village' AND VillageID = ? LIMIT 1";
        $query = $this->db->query($sql,array($id));

        $sql = "DELETE FROM reg_village WHERE VillageID = ? LIMIT 1";
        $query = $this->db->query($sql,array($id));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mregion extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();

	}

	public function getProvince()
	{
		/*$this->db->select('ProvinceID AS id, Province AS name', FALSE);
      $query = $this->db->get_where('ktv_province', array('active' => 1));
      $query = $this->db->where('StatusCode !=', 'nullified');*/
      $sql="SELECT
         ProvinceID AS id, Province AS name
      FROM
         ktv_province
      WHERE
         active = '1' AND
         StatusCode != 'nullified'
      ORDER BY Province ASC
      ";
      $query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getDistrict($provId)
	{
		//$this->db->select('DistrictID AS id, District AS name', FALSE);
		//$query = $this->db->get_where('ktv_district', array('ProvinceID' => $provId));
      $sql="SELECT
         DistrictID AS id, District AS name
      FROM
         ktv_district
      WHERE
         ProvinceID = ? AND
         active = '1' AND
         StatusCode != 'nullified'
      ";
      $query = $this->db->query($sql,array($provId));
		return $query->result_array();
	}

	public function getSubDistrict($distId)
	{
		//$this->db->select('SubDistrictID AS id, SubDistrict AS name', FALSE);
		//$query = $this->db->get_where('ktv_subdistrict', array('DistrictID' => $distId));
      $sql="SELECT
         SubDistrictID AS id, SubDistrict AS name
      FROM
         ktv_subdistrict
      WHERE
         DistrictID = ? AND
         StatusCode != 'nullified'
      ";
      $query = $this->db->query($sql,array($distId));
		return $query->result_array();
	}

	public function getVillage($subdistId)
	{
		//$this->db->select('VillageID AS id, Village AS name', FALSE);
		//$query = $this->db->get_where('ktv_village', array('SubDistrictID' => $subdistId));
      $sql="SELECT
         VillageID AS id, Village AS name
      FROM
         ktv_village
      WHERE
         SubDistrictID = ? AND
         StatusCode != 'nullified'
      ";
      $query = $this->db->query($sql,array($subdistId));
		return $query->result_array();
	}

	public function getProvinceDetail($id)
	{
		$this->db->select('ProvinceID AS id, Province AS name', FALSE);
		$query = $this->db->get_where('ktv_province', array('ProvinceID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
	}

	public function addProvince($id, $name)
	{
		return $this->db->insert('ktv_province', array('ProvinceID' => $id, 'Province' => $name));
	}

	public function updateProvince($id, $name)
	{
		return $this->db->update('ktv_province', array('Province' => $name), array('ProvinceID' => $id));
	}

	public function deleteProvince($id)
	{
      $sql="UPDATE ktv_province SET StatusCode = 'nullified',active='0' WHERE ProvinceID = ? LIMIT 1";
      return $this->db->query($sql,array($id));
		//return $this->db->delete('ktv_province', array('ProvinceID' => $id));
	}

	public function getDistrictDetail($id)
	{
		$this->db->select('DistrictID AS id, District AS name', FALSE);
		$query = $this->db->get_where('ktv_district', array('DistrictID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
	}

	public function addDistrict($id, $name, $parent_id)
	{
		return $this->db->insert('ktv_district', array('DistrictID' => $id, 'District' => $name, 'ProvinceID' => $parent_id));
	}

	public function updateDistrict($id, $name)
	{
		return $this->db->update('ktv_district', array('District' => $name), array('DistrictID' => $id));
	}

	public function deleteDistrict($id)
	{
      $sql="UPDATE ktv_district SET StatusCode = 'nullified',active='0' WHERE DistrictID = ? LIMIT 1";
      return $this->db->query($sql,array($id));
		//return $this->db->delete('ktv_district', array('DistrictID' => $id));
	}

	public function getSubDistrictDetail($id)
	{
		$this->db->select('SubDistrictID AS id, SubDistrict AS name', FALSE);
		$query = $this->db->get_where('ktv_subdistrict', array('SubDistrictID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
	}

	public function addSubDistrict($id, $name, $parent_id)
	{
		return $this->db->insert('ktv_subdistrict', array('SubDistrictID' => $id, 'SubDistrict' => $name, 'DistrictID' => $parent_id));
	}

	public function updateSubDistrict($id, $name)
	{
		return $this->db->update('ktv_subdistrict', array('SubDistrict' => $name), array('SubDistrictID' => $id));
	}

	public function deleteSubDistrict($id)
	{
      $sql="UPDATE ktv_subdistrict SET StatusCode = 'nullified' WHERE SubDistrictID = ? LIMIT 1";
      return $this->db->query($sql,array($id));
		//return $this->db->delete('ktv_subdistrict', array('SubDistrictID' => $id));
	}

	public function getVillageDetail($id)
	{
		$this->db->select('VillageID AS id, Village AS name', FALSE);
		$query = $this->db->get_where('ktv_village', array('VillageID' => $id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
	}

	public function addVillage($id, $name, $parent_id)
	{
		return $this->db->insert('ktv_village', array('VillageID' => $id, 'Village' => $name, 'SubDistrictID' => $parent_id));
	}

	public function updateVillage($id, $name)
	{
		return $this->db->update('ktv_village', array('Village' => $name), array('VillageID' => $id));
	}

	public function deleteVillage($id)
	{
      $sql="UPDATE ktv_village SET StatusCode = 'nullified', LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW() WHERE VillageID = ? LIMIT 1";
      return $this->db->query($sql,array($id));
		//return $this->db->delete('ktv_village', array('VillageID' => $id));
	}

    public function listProvince()
    {
        $sql = "SELECT
    ProvinceID AS id
    , Province AS `label`
FROM
    ktv_province
#WHERE
    #active = 1
ORDER BY label
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function listDistrict($ProvinceID = null)
    {
        $sql = "SELECT
    DistrictID AS id
    , District AS label
FROM
    ktv_district
WHERE
    1 = 1
    #active = 1
    --filter--
ORDER BY label
        ";
        $filter = '';
        $params = array();
        if (!empty($ProvinceID)) {
            $filter .= " AND ProvinceID = ?";
            $params[] = $ProvinceID;
        }
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function listSubDistrict($DistrictID = null)
    {
        $sql = "SELECT
    SubDistrictID AS id
    , SubDistrict AS label
FROM
    ktv_subdistrict
WHERE
    1 = 1
    #active = 1
    --filter--
ORDER BY label
        ";
        $filter = '';
        $params = array();
        if (!empty($DistrictID)) {
            $filter .= " AND DistrictID = ?";
            $params[] = $DistrictID;
        }
        $sql = str_replace('--filter--', $filter, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

}

/* End of file mregion.php */
/* Location: ./application/models/mregion.php */
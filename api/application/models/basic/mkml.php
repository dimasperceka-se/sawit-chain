<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mkml extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function getKmls($ProvinceID = null, $cat = null, $key = null, $start = 0, $limit = 20)
	{
		$sql = "
SELECT SQL_CALC_FOUND_ROWS
    k.ID
    , k.ProvinceID
    , k.DistrictID
    , k.SubDistrictID
    , k.VillageID
    , k.CategoryID
    , k.`Name`
    , k.`FileName`
    , k.`Color`
    , k.DateCreated
    , k.CreatedBy
    , k.DateUpdated
    , k.LastModifiedBy 
    , p.Province
    , d.District
    , sd.SubDistrict
    , v.Village
    , t.Name AS category
FROM
    ktv_kml k
LEFT JOIN ktv_province p ON p.ProvinceID = k.ProvinceID
LEFT JOIN ktv_district d ON d.DistrictID = k.DistrictID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = k.SubDistrictID
LEFT JOIN ktv_village v ON v.VillageID = k.VillageID
LEFT JOIN ktv_kml_category t ON t.ID = k.CategoryID
WHERE 1 = 1 AND k.StatusCode != 'nullified'
	-- where --
ORDER BY Name	
LIMIT ?, ?
		";
		$where  = '';
		$params = [];
		if (!empty($ProvinceID)) {
			$where .= " AND k.ProvinceID = ?";
			$params[] = intval($ProvinceID);
		}
		if (!empty($cat)) {
			$where .= " AND k.CategoryID = ?";
			$params[] = intval($cat);
		}
		if (!empty($key)) {
			$where .= " AND k.Name LIKE '%{$key}%'";
		}
		$sql      = str_replace("-- where --", $where, $sql);
		$params[] = intval($start);
		$params[] = intval($limit);
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			$result['data']  = $query->result_array();
			$qtotal          = $this->db->query("SELECT FOUND_ROWS() AS total");
			$result['total'] = $qtotal->row_array(0)['total'];
			return $result;
		}
		return false;
	}

	public function getCategory()
	{
		$this->db->select('ID as id, Name as label', FALSE);
		$query = $this->db->get_where('ktv_kml_category', array('Leaf'=>1));
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function getKml($ID)
	{
		$sql = "
SELECT 
    k.ID
    , k.ProvinceID
    , k.DistrictID
    , k.SubDistrictID
    , k.VillageID
    , k.CategoryID
    , k.`Name`
    , k.`Color`
    , k.`FileName`
    , k.`FilePath`
    , k.DateCreated
    , k.CreatedBy
    , k.DateUpdated
    , k.LastModifiedBy 
    , p.Province
    , d.District
    , sd.SubDistrict
    , v.Village
    , t.Name AS category
FROM
    ktv_kml k
LEFT JOIN ktv_province p ON p.ProvinceID = k.ProvinceID
LEFT JOIN ktv_district d ON d.DistrictID = k.DistrictID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = k.SubDistrictID
LEFT JOIN ktv_village v ON v.VillageID = k.VillageID
LEFT JOIN ktv_kml_category t ON t.ID = k.CategoryID
WHERE 1 = 1
	AND k.ID = ?
		";
		$query = $this->db->query($sql, array($ID));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
	}

	public function uploadKML($post, $file)
	{
		$result['success'] = true;
		$result['msg']     = '';
		$data = [
			'Name'          => $post['Name'],
			'CategoryID'    => intval($post['CategoryID']),
			'ProvinceID'    => intval($post['ProvinceID']),
			'DistrictID'    => intval($post['DistrictID']),
			'SubDistrictID' => intval($post['SubDistrictID']),
			'VillageID'     => intval($post['VillageID']),
			'FileName'      => $file['file_name'],
			'FilePath'      => $file['full_path'],
			'Color'         => $post['Color'],
		];
		if (empty($post['ID'])) {
			// add
			$data['CreatedBy'] = $_SESSION['userid'];
			$data['DateCreated'] = date('Y-m-d H:i:s');
			$result['success'] = $this->db->insert('ktv_kml', $data);
		} else {
			// update
			$data['LastModifiedBy'] = $_SESSION['userid'];
			$data['DateUpdated'] = date('Y-m-d H:i:s');
			$detail = $this->getKml($post['ID']);
			delete_file($detail['FilePath']);
			$result['success'] = $this->db->update('ktv_kml', $data, array('ID' => $post['ID']));
		}
		return $result;
	}

	public function updateKML($post)
	{
		$result['success'] = true;
		$result['msg']     = '';
		$data = [
			'Name'          => $post['Name'],
			'CategoryID'    => intval($post['CategoryID']),
			'ProvinceID'    => intval($post['ProvinceID']),
			'DistrictID'    => intval($post['DistrictID']),
			'SubDistrictID' => intval($post['SubDistrictID']),
			'VillageID'     => intval($post['VillageID']),
			'Color'         => $post['Color'],
		];
		if (empty($post['ID'])) {
			// add
			$data['CreatedBy'] = $_SESSION['userid'];
			$data['DateCreated'] = date('Y-m-d H:i:s');
			$result['success'] = $this->db->insert('ktv_kml', $data);
		} else {
			// update
			$data['LastModifiedBy'] = $_SESSION['userid'];
			$data['DateUpdated'] = date('Y-m-d H:i:s');
			$result['success'] = $this->db->update('ktv_kml', $data, array('ID' => $post['ID']));
		}
		return $result;
	}

	public function deleteKml($ID)
	{
		// return $this->db->delete('ktv_kml', ['ID' => $ID]);
		return $this->db->update('ktv_kml', ['StatusCode' => 'nullified'], ['ID' => $ID]);
	}

}

/* End of file mkml.php */
/* Location: ./application/models/mkml.php */
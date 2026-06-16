<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mmessage extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	private function foundRows()
	{
		$query = $this->db->query('SELECT FOUND_ROWS() as total');		
		
		return $query->row(0)->total;
		
	}

	public function readInbounds($params = array())
	{
		extract($params);

		$this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
		if (!empty($key)) {
			$this->db->like('msisdn', $key, 'both');
			$this->db->or_like('text', $key, 'both');
		}
		$query = $this->db->get('ktv_sms_inbound', $limit, $offset);

        $data['data']   = $query->result_array();
        $data['total']  = $this->foundRows();

		return $data;
	}

	public function readOutbounds($params = array())
	{
		extract($params);

		$this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
		if (!empty($key)) {
			$this->db->like('to', $key, 'both');
			$this->db->or_like('message', $key, 'both');
		}
		$query = $this->db->get('ktv_sms_outbound', $limit, $offset);

        $data['data']   = $query->result_array();
        $data['total']  = $this->foundRows();

		return $data;
	}

	public function createPost($data)
	{
		
		return $this->db->insert('ktv_sms_outbound', $params);
	}
        
        public function readBroadcasts($key, $start = 0, $limit = 50){
            $sql = "SELECT SQL_CALC_FOUND_ROWS
                        a.BroadcastID, a.message, IFNULL(d.total_all,0) total_all, IFNULL(d.total_new,0) total_new, IFNULL(d.total_success,0) total_success, IFNULL(d.total_failed,0) total_failed
                    FROM ktv_sms_broadcast a
                    LEFT JOIN (
                        SELECT
                            BroadcastID, 
                            COUNT(*) total_all,
                            SUM(IF(BroadcastStatus=0,1,0)) total_new,
                            SUM(IF(BroadcastStatus=1,1,0)) total_success,
                            SUM(IF(BroadcastStatus=2,1,0)) total_failed
                        FROM ktv_sms_broadcast_details
                        WHERE StatusCode!='nullified'
                        GROUP BY BroadcastID
                    ) d ON d.BroadcastID=a.BroadcastID
                    WHERE a.StatusCode!='nullified' AND (a.message LIKE ?)
                    ORDER BY BroadcastID DESC
                    LIMIT ?, ?";
            $query = $this->db->query($sql,array("%$key%", intval($start), intval($limit)));
            //echo "<pre>".$this->db->last_query();exit;
            $sql_total = "SELECT FOUND_ROWS() AS total";
            $query_total = $this->db->query($sql_total);
            if ($query->num_rows() > 0) {
                $total = $query_total->row_array(0);
                return array(
                    'data'      => $query->result_array(),
                    'total'     => $total['total']
                    );
            }
            return false;
	}
        
        public function readBroadcastTo($BroadcastID, $key, $start = 0, $limit = 50){
            $sql = "SELECT SQL_CALC_FOUND_ROWS
                        a.BroadcastDetailID, a.BroadcastID, a.to, CASE a.BroadcastStatus WHEN 0 THEN 'Pending' WHEN 1 THEN 'Success' WHEN 2 THEN 'Failed' END BroadcastStatus, c.GroupName, a.ToName Name, b.FarmerID
                    FROM ktv_sms_broadcast_details a
                        LEFT JOIN ktv_sms_group_details b ON a.GroupDetailID=b.GroupDetailID
                        LEFT JOIN ktv_sms_group c ON b.GroupID=c.GroupID
                        LEFT JOIN ktv_sms_broadcast d ON d.BroadcastID=a.BroadcastID
                        LEFT JOIN ktv_farmer f ON f.FarmerID=b.FarmerID
                    WHERE a.BroadcastID=? AND a.StatusCode!='nullified' AND (d.message LIKE ? OR a.ToName LIKE ? OR f.FarmerName LIKE ? OR `to` LIKE ? OR c.GroupName LIKE ?)
                    ORDER BY BroadcastID DESC
                    LIMIT ?, ?";
            $query = $this->db->query($sql,array($BroadcastID, "%$key%", "%$key%", "%$key%", "%$key%", "%$key%", intval($start), intval($limit)));
            //echo "<pre>".$this->db->last_query();exit;
            $sql_total = "SELECT FOUND_ROWS() AS total";
            $query_total = $this->db->query($sql_total);
            if ($query->num_rows() > 0) {
                $total = $query_total->row_array(0);
                return array(
                    'data'      => $query->result_array(),
                    'total'     => $total['total']
                    );
            }
            return false;
	}
        
        public function createBroadcast($message, $userid){
            $sql = "INSERT INTO ktv_sms_broadcast(message, StatusCode, CreatedBy, DateCreated) VALUES (?, 'active', ?, NOW())";
            $query = $this->db->query($sql, array($message, $userid));
            if ($query) {
                $results['success']     = true;
                $results['message']     = "record created.";
                $results['BroadcastID'] = $this->db->insert_id();
            } else {
                $results['success']     = false;
                $results['message']     = "Failed to create record";
            }
            return $results;
        }
        
        public function updateBroadcast($BroadcastID, $message, $userid){
            $sql = "UPDATE ktv_sms_broadcast SET message=?, LastModifiedBy=?, DateUpdated=NOW() WHERE BroadcastID=?";
            $query = $this->db->query($sql, array($message, $userid, $BroadcastID));
            if ($query) {
                $results['success']     = true;
                $results['message']     = "record updated.";
            } else {
                $results['success']     = false;
                $results['message']     = "Failed to update record";
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
        
        public function listDistricts($ProvinceID){
            $sql = "SELECT DistrictID id, District label FROM ktv_district WHERE active='1' AND StatusCode!='nullified' AND ProvinceID=?";
            $query = $this->db->query($sql,array($ProvinceID));
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
        }

        public function listSubDistricts($ProvinceID, $DistrictID){
            $sql = "SELECT SubDistrictID id, SubDistrict label FROM ktv_subdistrict WHERE active='1' AND StatusCode!='nullified' AND DistrictID=?";
            $query = $this->db->query($sql,array($DistrictID));
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
        }

        public function listVillages($SubDistrictID){
            $sql = "SELECT VillageID id, Village label FROM ktv_village WHERE StatusCode='active' AND SubDistrictID=?";
            $query = $this->db->query($sql,array($SubDistrictID));
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
        }
        
        public function listSmsGroups(){
            $sql = "SELECT GroupID id, GroupName label FROM ktv_sms_group WHERE StatusCode!='nullified'";
            $query = $this->db->query($sql);
            if ($query->num_rows()>0) {
                return $query->result_array();
            }
        }
        
        public function readFarmerList($BroadcastID, $key, $ProvinceID, $DistrictID, $SubDistrictID, $Village, $GroupID, $start = 0, $limit = 50){
            //disini besok ya ud
            if($ProvinceID==""){ $p="#"; }else{ $p="";}
            if($DistrictID==""){ $d="#"; }else{ $d="";}
            if($SubDistrictID==""){ $sd="#"; }else{ $sd="";}
            if($Village==""){ $v="#"; }else{ $v="";}
            if($GroupID==""){ $g="#"; }else{ $g="";}
            $V = str_replace("::", ",", $Village);
            $sql = "SELECT SQL_CALC_FOUND_ROWS
                        a.GroupDetailID, a.FarmerID, IFNULL(b.FarmerName,a.Name) ToName, a.PhoneNumber `to`, c.GroupName
                    FROM ktv_sms_group_details a
                        LEFT JOIN ktv_farmer b ON b.FarmerID=a.FarmerID
                        LEFT JOIN ktv_sms_group c ON c.GroupID=a.GroupID
                        LEFT JOIN ktv_sms_broadcast_details bd ON bd.GroupDetailID=a.GroupDetailID AND bd.BroadcastID=? AND bd.StatusCode!='nullified'
                        LEFT JOIN ktv_village v ON v.VillageID=b.VillageID
                        LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID=v.SubDistrictID
                        LEFT JOIN ktv_district d ON d.DistrictID=sd.DistrictID
                    WHERE 
                        bd.GroupDetailID IS NULL
                        $p AND SUBSTR(b.FarmerID,1,2)=?
                        $d AND d.DistrictID=?
                        $sd AND sd.SubDistrictID=?
                        $v AND v.VillageID IN ($V)
                        $g AND c.GroupID=?
                        AND (a.FarmerID LIKE ? OR IFNULL(b.FarmerName,a.Name) LIKE ?)
                    ORDER BY IFNULL(b.FarmerName,ToName)
                    LIMIT ?, ?";
            $query = $this->db->query($sql,array($BroadcastID, $ProvinceID, $DistrictID, $SubDistrictID,  $GroupID, "%$key%", "%$key%", intval($start), intval($limit)));
            //echo "<pre>".$this->db->last_query();exit;
            $sql_total = "SELECT FOUND_ROWS() AS total";
            $query_total = $this->db->query($sql_total);
            if ($query->num_rows() > 0) {
                $total = $query_total->row_array(0);
                return array(
                    'data'      => $query->result_array(),
                    'total'     => $total['total']
                    );
            }
            return false;
	}

    public function addFarmers($BroadcastID, $message, $farmers, $userid){
        $sql = " INSERT INTO ktv_sms_broadcast_details(BroadcastID, GroupDetailID, ToName, `to`, message, BroadcastStatus, StatusCode, CreatedBy, DateCreated)
            VALUES (?,?,?,?,?,0,'active',?,NOW())";
        $this->db->trans_start();
        $farmer = explode(',', $farmers);
        for($i=1;$i<count($farmer);$i++){
            $data = explode('_', $farmer[$i]);
            $query = $this->db->query($sql, array($BroadcastID, $data[0], $data[1], $this->get_number($data[2]), $message, $userid));
        }
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === TRUE){
            $results['success']     = true;
            $results['message']     = "Farmer added.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to add farmer";
        }
        return $results;
    }

    public function addNumber($BroadcastID, $message, $ToName, $to, $userid){
        $sql = "SELECT * FROM ktv_sms_broadcast_details WHERE BroadcastID=? AND `to`=? AND StatusCode!='nullified'";
        $query = $this->db->query($sql, array($BroadcastID,$to));
        if($query->num_rows() > 0){
            $results['success']     = "failed";
            $results['message']     = "Number already added!";
        }else{
            $sql = " INSERT INTO ktv_sms_broadcast_details(BroadcastID, GroupDetailID, ToName, `to`, message, BroadcastStatus, StatusCode, CreatedBy, DateCreated)
                VALUES (?,?,?,?,?,0,'active',?,NOW())";
            $query = $this->db->query($sql, array($BroadcastID, NULL, $ToName, $to, $message, $userid));
            if ($query){
                $results['success']     = true;
                $results['message']     = "Number added.";
            } else {
                $results['success']     = false;
                $results['message']     = "Failed to add number";
            }
        }
        return $results;
    }

    private function get_number($number)
    {
        $number = trim($number);
        $number = str_replace(" ", "", $number);
        $number = str_replace("-", "", $number);
        $first = substr($number, 0, 1);
        switch ($first) {
            case '0':
                $number = preg_replace('/0/', '62', $number, 1);
                break;
            case '+':
                $number = preg_replace('/\+/', '', $number, 1);
                break;
            
            default:
                // do nothing
                break;
        }
        return $number;
    }
    
    public function readBroadcastDetail($BroadcastID){
        $sql = "SELECT * FROM ktv_sms_broadcast WHERE BroadcastID=?";
        $query = $this->db->query($sql,array($BroadcastID));
        $return = $query->result_array();
        return $return[0];
    }
    
    public function deleteBroadcast($userid, $BroadcastID){
        $sql = "UPDATE ktv_sms_broadcast SET StatusCode='nullified', LastModifiedBy=?, DateUpdated=NOW() WHERE BroadcastID=?";
        $query = $this->db->query($sql, array($userid, $BroadcastID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record deleted.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to delete record.";
        }
        return $results;
    }
    
    public function readSentDetail($BroadcastDetailID){
        $sql = "SELECT * FROM ktv_sms_broadcast_details WHERE BroadcastDetailID=?";
        $query = $this->db->query($sql,array($BroadcastDetailID));
        $return = $query->result_array();
        return $return[0];
    }
    
    public function updateNumber($BroadcastDetailID, $ToName, $to, $userid){
        $sql = "UPDATE ktv_sms_broadcast_details SET ToName=?, `to`=?, LastModifiedBy=?, DateUpdated=NOW() WHERE BroadcastDetailID=?";
        $query = $this->db->query($sql, array($ToName, $to, $userid, $BroadcastDetailID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record updated.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to update record.";
        }
        return $results;
    }
    
    public function deleteNumber($BroadcastDetailID, $userid){
        $sql = "UPDATE ktv_sms_broadcast_details SET StatusCode='nullified', LastModifiedBy=?, DateUpdated=NOW() WHERE BroadcastDetailID=?";
        $query = $this->db->query($sql, array($userid, $BroadcastDetailID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record deleted.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to delete record.";
        }
        return $results;
    }
    
    public function readSentBroadcast($BroadcastID){
        $sql = "SELECT BroadcastDetailID id FROM ktv_sms_broadcast_details WHERE StatusCode!='nullified' AND BroadcastStatus=0 AND BroadcastID=?";
        $query = $this->db->query($sql,array($BroadcastID));
        if ($query->num_rows()>0) {
            $results['success']  = 'true';
            $results['total']    = $query->num_rows();
            $results['data']     = $query->result_array();
        }else{
            $results['success']     = 'false';
            $results['message']     = "No Pending message!";
        }
        return $results;
    }
    
    public function updateBroadcastDetail($BroadcastDetailID, $message, $response, $userid){
        if($response['status']=='0'){
            $BroadcastStatus=1;
            $status = 'true';
        }else{
            $BroadcastStatus=2;
            $status = 'false';
        }
        $sql = "UPDATE ktv_sms_broadcast_details SET message=?, `message-id`=?, `status`=?, `remaining-balance`=?, `message-price`=?, `network`=?, `error-text`=?, BroadcastStatus=?, LastModifiedBy=?, DateUpdated=NOW() WHERE BroadcastDetailID=?";
        $query = $this->db->query($sql, array($message, $response['message-id'], $response['status'], $response['remaining-balance'], $response['message-price'], $response['network'], $response['error-text'], $BroadcastStatus, $userid, $BroadcastDetailID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record deleted.";
            $results['status']      = $status;
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to delete record.";
            $results['status']      = $status;
        }
        return $results;
    }
}

/* End of file minbound.php */
/* Location: ./application/models/minbound.php */
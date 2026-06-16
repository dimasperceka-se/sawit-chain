<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Msms extends CI_Model
{

    public $variable;

    public function __construct()
    {
        parent::__construct();
    }

    public function handleInbound($params)
    {
        // if (is_array($params)) {
        // 	extract($params);
        // }
        // do something with the data
        $valid_key = array(
            'type',
            'to',
            'msisdn',
            'messageId',
            'message-timestamp',
            'text',
            'keyword',
            'concat',
            'concat-ref',
            'concat-total',
            'concat-part',
            'data',
            'udh',
        );
        $insert_param = array();
        foreach ($params as $key => $value) {
            if (in_array($key, $valid_key)) {
                $insert_param[$key] = $value;
            }
        }
        $insert_param['insert-timestamp'] = date('Y-m-d H:i:s');
        $result = $this->db->insert('ktv_sms_inbound', $insert_param);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        return $result;
    }

    public function insertOutbound($insert_param)
    {
        $insert_param['insert-timestamp'] = date('Y-m-d H:i:s');
        $result = $this->db->insert('ktv_sms_outbound', $insert_param);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        return $result;
    }

    public function isFarmer($farmer_id)
    {
        $sql = "SELECT * FROM ktv_farmer WHERE FarmerID=? AND StatusCode='active'";
        $query = $this->db->query($sql, array($farmer_id));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function isRegistered($farmer_id, $phone)
    {
        $sql = "SELECT * FROM ktv_sms_group_details WHERE FarmerID=? AND PhoneNumber=? AND StatusCode!='nullified'";
        $query = $this->db->query($sql, array($farmer_id, $phone));
        //$query = $this->db->get_where('ktv_sms_reg', array('FarmerID' => $farmer_id, 'PhoneNumber' => $phone, 'Status' => 'REG'));
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function isPhoneRegistered($farmer_id, $phone)
    {
        $sql = "SELECT * FROM ktv_sms_group_details WHERE FarmerID!=? AND PhoneNumber=? AND StatusCode!='nullified'";
        $query = $this->db->query($sql, array($farmer_id, $phone));
        //$where = "PhoneNumber = '$phone' AND FarmerID != '$farmer_id' AND Status = 'REG'";
        //$this->db->where($where);
        //$query = $this->db->get('ktv_sms_reg');

        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function getDetailRegisterByPhone($phone)
    {
        /*$where = "PhoneNumber = '$phone' AND Status = 'REG'";
        $this->db->where($where);
        $query = $this->db->get('ktv_sms_reg');

        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }*/
        $sql = "SELECT * FROM ktv_sms_group_details WHERE PhoneNumber=?";
        $query = $this->db->query($sql,array($phone));
        $return = $query->result_array();
        return $return[0];
    }

    public function insertRegister($farmer_id, $phone, $StatusCode='active', $Desc = 'REG')
    {
        /*$sql = <<<SQL
INSERT INTO `ktv_sms_reg` (
    `FarmerID`,
    `PhoneNumber`,
    `Status`
) 
VALUES
    (
        ?,
        ?,
        ?
    )
ON DUPLICATE KEY UPDATE
	`Status` = VALUES(`Status`)
SQL;
        return $this->db->query($sql, array($farmer_id, $phone, $status));*/
        $farmer_name = $this->db->query("SELECT FarmerName FROM ktv_farmer WHERE FarmerID=?",array($farmer_id))->row()->FarmerName;
        $sql = "INSERT INTO ktv_sms_group_details(FarmerID, Name, PhoneNumber, Description, StatusCode, DateCreated) VALUES (?,?,?,?,?,NOW())";
        $query = $this->db->query($sql, array($farmer_id, $farmer_name, $phone, $Desc, $StatusCode));
        return $query;
    }
    
    public function updateRegister($farmer_id, $phone, $StatusCode='active', $Desc = 'REG')
    {
        $sql = "UPDATE ktv_sms_group_details SET Description=?, StatusCode=?, DateUpdated=NOW()) WHERE PhoneNumber=? AND FarmerID=?";
        $query = $this->db->query($sql, array($Desc, $StatusCode, $farmer_id, $farmer_name, $phone));
        return $query;
    }

    public function getPriceByFarmer($farmer_id)
    {
    	/*$sql = <<<SQL
            SELECT
            	w.`WarehouseName`,
            	p.`FFPrice`,
            	p.`FAQPrice`
            FROM 
            ktv_warehouse w
            JOIN ktv_supplychain_org o ON o.`OrgID` = w.`WarehouseID`
            JOIN `ktv_supplychain_price` p ON p.`PriceSupplychainID` = o.`SupplychainID`
            JOIN (
            SELECT 
            	p.`PriceSupplychainID`,
            	MAX(p.`PriceDate`) AS `PriceDate`
            FROM `ktv_supplychain_price` p 
            GROUP BY p.`PriceSupplychainID`
            ) r ON r.PriceSupplychainID = p.PriceSupplychainID AND r.PriceDate = p.PriceDate
            WHERE
            	SUBSTRING(w.`VillageID`,1,4) = SUBSTRING(?,1,4)
            SQL;
		$query = $this->db->query($sql, array($farmer_id));
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}*/
        $sql = "SELECT ff.CocoaPrice FFPrice, faq.CocoaPrice FAQPrice
                FROM
                    (SELECT CocoaPrice, DistrictID FROM ktv_price WHERE DistrictID=SUBSTR(?,1,4) AND CocoaPriceDate<=CURDATE() AND Type='FAQ' ORDER BY CocoaPriceID DESC LIMIT 1) faq
                    LEFT JOIN (SELECT CocoaPrice, DistrictID FROM ktv_price WHERE DistrictID=SUBSTR(?,1,4) AND CocoaPriceDate<=CURDATE() AND Type='FF' ORDER BY CocoaPriceID DESC LIMIT 1) ff ON ff.DistrictID=faq.DistrictID ";
        $query = $this->db->query($sql, array($farmer_id, $farmer_id));
        $return = $query->result_array();
        return $return[0]; 
        
    }    

    private function get_number($number)
    {
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

    public function isRegisteredStaff($phone)
    {
        $first = substr($phone, 0, 1);
        switch ($first) {
            case '0':
            $phone = preg_replace('/0/', '', $phone, 1);
            break;
            case '+':
            $phone = preg_replace('/\+62/', '', $phone, 1);
            break;
            case '6':
            $phone = preg_replace('/62/', '', $phone, 1);
            break;

            default:
            // do nothing
            break;
        }
        $sql = "
SELECT
    s.StaffID,
    p.PersonID,
    p.UserID,
    p.PersonNm
FROM ktv_staffs s
JOIN ktv_persons p ON p.PersonID = s.PersonID
WHERE
    s.OfficialPhone LIKE '%{$phone}'
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function getDistrictID($district)
    {
        $query = $this->db->get_where('ktv_district', array('District' => $district), 1);
        if ($query->num_rows() > 0) {
            $result = $query->row_array(0);
            return $result['DistrictID'];
        }
        return false;
    }

    public function getPrice($DistrictID, $Type, $CocoaPriceDate)
    {
        $query = $this->db->get_where('ktv_price', array('DistrictID' => $DistrictID,'Type' => $Type,'CocoaPriceDate' => $CocoaPriceDate,), 1);
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
        return false;        
    }

    public function setPrice($DistrictID, $Type, $CocoaPriceDate, $CocoaPrice, $UserID)
    {
        $sql = "
INSERT INTO ktv_price
SET
    DistrictID = ?,
    Type = ?,
    CocoaPriceDate = ?,
    CocoaPrice = ?,
    CreatedBy = ?,
    DateCreated = NOW()
ON DUPLICATE KEY UPDATE
    CocoaPrice = VALUES(CocoaPrice),
    LastModifiedBy = VALUES(CreatedBy),
    DateUpdated = NOW()
        ";
        return $this->db->query($sql, array($DistrictID, $Type, $CocoaPriceDate, $CocoaPrice, $UserID));        
    }

}

/* End of file msms.php */
/* Location: ./application/models/msms.php */
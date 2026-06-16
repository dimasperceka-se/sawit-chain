<?php
/**
 * @Author: nikolius
 * @Date:   2016-08-22 13:39:28
 */
class Mcompost extends CI_Model
{
    public function getFarmerCompostBySceId($sce_id)
    {
        $sql="SELECT
                    a.CompostID,
                    a.Established,
                    a.MesinChooper,
                    a.RumahKompos,
                    a.Latitude,
                    a.Longitude
                FROM
                    ktv_compost a
                WHERE
                    a.ObjType = 'farmer' AND
                    a.ObjID = (SELECT FarmerID FROM sce_farmer WHERE SceID = ?)
                LIMIT 1";
        $query = $this->db->query($sql,array($sce_id));
        $dataCompost = $query->result_array();

        //get farmer name
        $sql="SELECT
                a.`FarmerName`
            FROM
                ktv_farmer a
            WHERE
                a.FarmerID = (SELECT FarmerID FROM sce_farmer WHERE SceID = ?)
            LIMIT 1";
        $query = $this->db->query($sql,array($sce_id));
        $data = $query->result_array();

        $dataCompost[0]['FarmerName'] = $data[0]['FarmerName'];
        return $dataCompost[0];
    }

    public function insertCompostFarmer($paramInsert){
        if($paramInsert['MesinChooper'] == "") $paramInsert['MesinChooper'] = "2";
        if($paramInsert['RumahKompos'] == "") $paramInsert['RumahKompos'] = "2";

        $sql="INSERT INTO `ktv_compost` SET
              `ObjType` = 'farmer',
              `ObjID` = (SELECT FarmerID FROM sce_farmer WHERE SceID = ?),
              `Established` = ?,
              `MesinChooper` = ?,
              `RumahKompos` = ?,
              Latitude = ?,
              Longitude = ?,
              DateCreated = NOW(),
              CreatedBy = ?
              ";
        $p = array(
            $paramInsert['SceID'],
            $paramInsert['Established'],
            $paramInsert['MesinChooper'],
            $paramInsert['RumahKompos'],
            $paramInsert['CompostLatitude'],
            $paramInsert['CompostLongitude'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);
        $CompostID = $this->db->insert_id();

        return $CompostID;
    }

    public function updateCompostFarmer($paramUpdate){
        $sql="UPDATE `ktv_compost` SET
              `Established` = ?,
              `MesinChooper` = ?,
              `RumahKompos` = ?,
              Latitude = ?,
              Longitude = ?,
              DateUpdated = NOW(),
              LastModifiedBy = ?
              WHERE
                CompostID = ?
              LIMIT 1";
        $p = array(
            $paramUpdate['Established'],
            $paramUpdate['MesinChooper'],
            $paramUpdate['RumahKompos'],
            $paramUpdate['CompostLatitude'],
            $paramUpdate['CompostLongitude'],
            $_SESSION['userid'],
            $paramUpdate['CompostID']
        );
        return $this->db->query($sql,$p);
    }

    public function getCompostTrans($CompostID,$start,$limit){
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    CompostTransactionID AS id,
                    Buyer,
                    Volume,
                    Price,
                    Volume*Price AS Total,
                    DATE(DateTransaction) AS DateTransaction
                FROM
                    ktv_compost_transaction kcct
                WHERE
                    CompostID = ? AND kcct.StatusCode != 'nullified'
                ORDER BY CompostID ASC
                #LIMIT ?,?";
        $query = $this->db->query($sql, array($CompostID,(int) $start,(int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function insertCompostTransFarmer($paramInsert){
        $sql = "insert into ktv_compost_transaction (CompostID, Buyer, Volume, Price, DateTransaction, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,now(),?)";
        $p = array(
            $paramInsert['CompostID'],
            $paramInsert['Buyer'],
            $paramInsert['Volume'],
            $paramInsert['Price'],
            $paramInsert['DateTransaction'],
            $paramInsert['userid']
        );
        $query = $this->db->query($sql, $p);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateCompostTransFarmer($paramUpdate){
        $tgl = explode('T', $paramUpdate['DateTransaction']);

        $sql = "
            update ktv_compost_transaction
            set CompostID=?, Buyer=?, Volume=?, Price=?, DateTransaction=?, DateUpdated=now(), LastModifiedBy=?
            where CompostTransactionID=? LIMIT 1";
        $p = array(
            $paramUpdate['CompostID'],
            $paramUpdate['Buyer'],
            $paramUpdate['Volume'],
            $paramUpdate['Price'],
            $tgl[0],
            $paramUpdate['userid'],
            $paramUpdate['id']
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function deleteCompostTransFarmer($id){
        $sql="UPDATE ktv_compost_transaction SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW() WHERE CompostTransactionID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
}
?>
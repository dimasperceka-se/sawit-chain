<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mmonitoring extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getDataMonitoring($district) {

        $mon = array();
        //$userid = $this->_getUserId($user);
        //if(is_array($district) && count($district) > 0){
            $this->db->select('*');
            $this->db->from('ktv_monitoring a');
            $this->db->join('ktv_village v','v.VillageID = a.VillageID','left');
            $this->db->join('ktv_subdistrict sd','sd.SubDistrictID = v.SubDistrictID','left');
            $this->db->join('ktv_district d','d.DistrictID = sd.DistrictID','left');
            $this->db->join('ktv_province pr','pr.ProvinceID = d.ProvinceID','left');
            //$this->db->where_in('sd.DistrictID',$district);
            //$this->db->limit(100);
            $Q = $this->db->get();

            if($Q->num_rows() > 0){

                foreach($Q->result_array() as $details => $monitoring) {
                    $monitoring['details'] = $this->_getMonitoringDetail($monitoring['MonitoringID']);
                    array_push($mon, $monitoring);
                }
            }
        //}

        return array('details' => $mon, 'total' => count($mon));
    }

    private function _getUserId($user) {
        $this->db->select('UserId');
        $this->db->from('sys_user');
        $this->db->where('UserName',$user);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return $row->UserId;
        }

        return false;
    }

    private function _getMonitoringDetail($id) {

        $this->db->select('*,CONCAT("'.$this->config->item('base_url').'/",FilePath,"/",FileName) AS FilePath',FALSE);
        $this->db->from('ktv_monitoring_files a');
        $this->db->where('a.MonitoringID',$id);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $total = $Q->num_rows();
            $detail = $Q->result_array();

            return array('detail' => $detail, 'total' => $total);
        }

        return array();
    }

    private function _getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $unit){

        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344).' km';
        } else if ($unit == "N") {
            return ($miles * 0.8684).' nm';
        } else {
            return $miles.' mi';
        }
    }

    public function syncMonitoring(&$data) {

        foreach($data as $key => $values) {

            $monitoringID = $values["MonitoringID"];

            $sql  = 'INSERT INTO `ktv_monitoring` (`ObjectCategory`,`ObjectType`,`ObjectID`,`ObjectName`,`VillageID`,`Description`,`VisitDate`,`VisitTime`,`DateCreated`,`CreatedBy`) VALUES("'.$values["ObjectCategory"].'","'.$values["ObjectType"].'","'.$values["ObjectID"].'","'.$values["ObjectName"].'","'.$values["VillageID"].'","'.$values["Description"].'","'.date('Y-m-d',strtotime($values["VisitDate"])).'","'.$values["VisitTime"].'","'.date('Y-m-d H:i:s').'","'.$this->_getUserId($this->user).'")';
            //$sql .= ' ON DUPLICATE KEY UPDATE `MonitoringID` = "'.$values["MonitoringID"].'", `ObjectCategory` = "'.$values["ObjectCategory"].'",`ObjectType` = "'.$values["ObjectType"].'",`ObjectID` = "'.$values["ObjectID"].'",`ObjectName` = "'.$values["ObjectName"].'",`VillageID` = "'.$values["VillageID"].'",`Description` = "'.$values["Description"].'",`VisitDate` = "'.$values["VisitDate"].'",`VisitTime` = "'.$values["VisitTime"].'",`DateUpdated` = "'.date('Y-m-d H:i:s').'",`LastModifiedBy` = "'.$this->_getUserId($this->user).'"';

            $Q = $this->db->query($sql);
            $ins = $this->db->insert_id();
            if($ins){
                $monitoringID = $ins;
            }

            $data[$key]['insert_id'] = $monitoringID;

            foreach($values['details']['detail'] as $keyd => $detail) {

                $sql2  = 'INSERT INTO `ktv_monitoring_files` (`MonitoringID`, `FilePath`, `FileTitle`, `FileName`, `FileType`, `FileSize`,`DateCreated`,`CreatedBy`) VALUES("'.$monitoringID.'","/images/photo_activity/","'.$detail["FileTitle"].'","'.$detail["FileName"].'","'.$detail["FileType"].'","'.$detail["FileSize"].'","'.date('Y-m-d H:i:s').'","'.$this->_getUserId($this->user).'")';
                //$sql2 .= ' ON DUPLICATE KEY UPDATE `MonitoringFilesID` = "'.$detail["MonitoringFilesID"].'", `MonitoringID` = "'.$monitoringID.'",`FilePath` = "/images/photo_activity/",`FileTitle` = "'.$detail["FileTitle"].'",`FileName` = "'.$detail["FileName"].'",`FileType` = "'.$detail["FileType"].'",`FileSize` = "'.$detail["FileSize"].'",`DateUpdated` = "'.date('Y-m-d H:i:s').'",`LastModifiedBy` = "'.$this->_getUserId($this->user).'"';

                $Q2 = $this->db->query($sql2);

                if($Q) {
                    $data[$key]['details']['detail'][$keyd]['insert_id'] = $this->db->insert_id();
                }
            }

        }
    }

    function create_foto($MonitoringID,$FileTitle,$FilePath,$FileName,$FileType,$FileSize){
        $userId = $this->_getUserId($this->user);
        $sql = "
            INSERT INTO ktv_monitoring_files
            (MonitoringID,FileTitle,FilePath,FileName,FileType,FileSize,CreatedBy,DateCreated)
            VALUES
            (?,?,?,?,?,?,?,now())";
        $query = $this->db->query($sql, array($MonitoringID,$FileTitle,$FilePath,$FileName,$FileType,$FileSize,$userId));
         //echo $this->db->last_query(); die();

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to created record";
        }
        return $results;
    }
}

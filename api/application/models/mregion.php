<?php
class Mregion extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function getRegionals(){

        $sql = "select RegionID, RegionName, ProvinceCode, DistrictCode, SubDistrictCode, VillageCode, Status"
            . " from ktv_regional "
            . " where "
            . " ProvinceCode IN ('11','13')";
           // . " LIMIT 0,10";

        // ,'72','73','74','76'
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $rs)
        {
            $results[] = $rs;
        }
        return $results;
    }

    function getAllProvince(){

        $sql = "select RegionID, RegionName, ProvinceCode, Status"
             . " from tblregional "
             . " where "
             . " RegionID like '%00000000'";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $rs)
        {
            $results[] = $rs;
        }
        return $results;
    }

    function getProvince($id){

        $sql = "select RegionID, RegionName, ProvinceCode, Status"
            . " from tblregional "
            . " where "
            . " RegionID='".$id."'";
        $query = $this->db->query($sql);
        if($query){
            foreach ($query->result_array() as $rs)
            {
                $results[] = $rs;
            }
        } else {
            $results[] = "";
        }
        return $results;
    }


    function getDistrict($id){

        $pro = substr($id,0,2);
       // $kab = substr($id,2,2);
       // $kec = substr($id,4,3);
       // $kel = substr($id,7,3);

        $sql = "SELECT RegionID, RegionName, ProvinceCode, DistrictCode, Status"
            . " FROM tblregional"
            . " WHERE ProvinceCode = '$pro'"
            . " AND DistrictCode != '00'"
            . " AND SubDistrictCode = '000'";

        $query = $this->db->query($sql);
        if($query){
            foreach ($query->result_array() as $rs)
            {
                $results[] = $rs;
            }
        } else {
            $results[] = "";
        }
        return $results;
    }

    function getSubDistrict($id){

        $pro = substr($id,0,2);
        $kab = substr($id,2,2);
        // $kec = substr($id,4,3);
        // $kel = substr($id,7,3);

        $sql = "SELECT RegionID, RegionName, ProvinceCode,DistrictCode,SubDistrictCode, Status"
            . " FROM tblregional"
            . " WHERE ProvinceCode = '$pro'"
            . " AND DistrictCode = '$kab'"
            . " AND SubDistrictCode != '000'"
            . " AND VillageCode = '000'";


        $query = $this->db->query($sql);
        if($query){
            foreach ($query->result_array() as $rs)
            {
                $results[] = $rs;
            }
        } else {
            $results[] = "";
        }
        return $results;
    }


    function getVillage($id){

        $pro = substr($id,0,2);
        $kab = substr($id,2,2);
        $kec = substr($id,4,3);
        //$kel = substr($id,7,3);

        $sql = "SELECT RegionID, RegionName, ProvinceCode,DistrictCode, SubDistrictCode, Village, Status"
            . " FROM tblregional"
            . " WHERE ProvinceCode = '$pro'"
            . " AND DistrictCode = '$kab'"
            . " AND SubDistrictCode = '$kec'"
            . " AND VillageCOde != '000'";

        $query = $this->db->query($sql);
        if($query){
            foreach ($query->result_array() as $rs)
            {
                $results[] = $rs;
            }
        } else {
            $results[] = "";
        }
        return $results;
    }


}
?>
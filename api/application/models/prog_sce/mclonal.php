<?php
/**
 * @Author: nikolius
 * @Date:   2016-08-29 10:23:24
 */
class Mclonal extends CI_Model
{
    public function getGardenNrCombo($sce_id)
    {
        $sql="SELECT
                    CONCAT(a.GardenNr,'@',IFNULL(b.`ClonalID`,'-')) AS id,
                    a.GardenNr AS label
                FROM
                    ktv_farmer_garden a
                    LEFT JOIN ktv_clonal_garden b ON b.ObjType = 'farmer' AND b.`ObjID` = a.`FarmerID`
                WHERE
                    a.FarmerID = (SELECT FarmerID FROM sce_farmer WHERE SceID = ? LIMIT 1) AND
                    a.StatusCode != 'nullified'
                GROUP BY a.GardenNr
                ORDER BY a.GardenNr ASC";
        $query = $this->db->query($sql,array($sce_id));
        return $query->result_array();
    }

    public function getFarmerGarden($sce_id,$GardenNr){
        $sql="SELECT *, Hybrid AS LOCAL, LokalNr AS LocalNr, J45Nr AS CG45Nr, J45 AS CG45, CloneLain AS OtherClones, CloneLainNr AS OtherClonesNr, ICRRI3 AS ICCRI3,  ICRRI4 AS ICCRI4, ICRRI5 AS ICCRI5, ICRRI3Nr AS ICCRI3Nr,  ICRRI4Nr AS ICCRI4Nr,  ICRRI5Nr AS ICCRI5Nr,
        Kelapa AS Coconut, KelapaNr AS CoconutNr, Gamal AS Gliricidia, GamalNr AS GliricidiaNr, Pinang AS ArecaPalm, PinangNr AS ArecaPalmNr, Karet AS Rubber, KaretNr AS RubberNr, JackFruit AS Jackfruit, JackFruitNr AS JackfruitNr,
        Lamtoro AS Leucaena, LamtoroNr AS LeucaenaNr, Mahoni AS Mahagony, MahoniNr AS MahagonyNr, Pisang AS Banana, PisangNr AS BananaNr, Sukun AS Breadfruit, SukunNr AS BreadfruitNr, Jengkol AS Archidendron, JengkolNr AS ArchidendronNr,
        Sengon AS Albizia, SengonNr AS AlbiziaNr, Petai AS Parkia, PetaiNr AS ParkiaNr, Jabon  AS Anthocephalus, JabonNr AS AnthocephalusNr, Uru AS Ermerilla, UruNr AS ErmerillaNr, Biti AS Vitex, BitiNr AS VitexNr,
        Jati AS Teak, JatiNr AS TeakNr, Jeruk AS Citrus, JerukNr AS CitrusNr, Jambu AS Guava, JambuNr AS GuavaNr, Kedondong AS SpondiasDulcis, KedondongNr AS SpondiasDulcisNr, Manggis AS Mangosteen, ManggisNr AS MangosteenNr,
        Pepaya AS Papaya, PepayaNr AS PapayaNr, Alpukat AS Avocado, AlpukatNr AS AvocadoNr, Kemiri AS Hazelnut, KemiriNr AS HazelnutNr, JambuMente AS Cashew, JambuMenteNr AS CashewNr, Pala AS Nutmeg, PalaNr AS NutmegNr,
        Aren AS SugarPalm, ArenNr AS SugarPalmNr, Sawit AS OilPalm, SawitNr AS OilPalmNr, Cengkeh AS Clove, CengkehNr AS CloveNr, Mangga AS Mango, ManggaNr AS MangoNr FROM ktv_farmer_garden WHERE FarmerID= (SELECT FarmerID FROM sce_farmer WHERE SceID = ? LIMIT 1) AND GardenNr = ? ORDER BY SurveyNr DESC LIMIT 1";
        $query = $this->db->query($sql, array($sce_id, $GardenNr));
        $result= $query->result_array();
        return $result[0];
    }

    public function getClonalGardenById($ClonalID,$GardenNr){
        $sql = "SELECT *, 45Nr AS CG45Nr,`45` AS CG45 FROM ktv_clonal_garden WHERE ClonalID=? LIMIT 1";
        $query = $this->db->query($sql, array($ClonalID));
        $result= $query->result_array();
        return $result[0];
    }

    public function createClonalGarden($post){
        $sce_id = getSceID();
        //get farmerId
        $sql="SELECT FarmerID FROM sce_farmer WHERE SceID = ? LIMIT 1";
        $query = $this->db->query($sql,array($sce_id));
        $data = $query->row_array();
        $FarmerID = $data['FarmerID'];

        //garden nr
        $tmp = explode('@',$post['GardenNr']);
        $post['GardenNr'] = $tmp[0];

        $SurveyNr = $post['SurveyNr'];
        unset($post['SurveyNr']);

        $this->db->trans_start();

        //insert ke ktv_clonal_garden

        foreach($post as $k=>$v){
            $k = str_replace("FCG", "", $k);
            if($k=='CG45') $k = '45';
            if($k=='CG45Nr') $k = '45Nr';
            $insert[$k] = $v;
        }

        $insert['ObjType'] = 'farmer';
        $insert['ObjID'] = $FarmerID;
        $insert['StatusCode'] = 'active';
        $insert['DateCreated'] = date('Y-m-d H:i:s');
        $insert['CreatedBy'] = $_SESSION['userid'];

        if($insert['ClonalID'] == "") $insert['ClonalID'] = NULL;
        if($insert['Area'] == "") $insert['Area'] = 0;

        $this->db->insert('ktv_clonal_garden', $insert);
        $ClonalID = $this->db->insert_id();

        //get data garden area
        $sql="SELECT
              `FarmerID`,
              `GardenNr`,
              `SurveyNr`,
              `OrderNr`,
              `Latitude`,
              `Longitude`
            FROM
              `ktv_farmer_garden_area`
            WHERE
                FarmerID = ? AND
                `GardenNr` = ? AND
                `SurveyNr` = ?
            ORDER BY OrderNr ASC";
        $query = $this->db->query($sql,array($FarmerID,$post['GardenNr'],$SurveyNr));
        $dataGardenArea = $query->result_array();

        if($dataGardenArea[0]['FarmerID'] != ""){
            for ($i=0; $i < count($dataGardenArea); $i++) {
                $sql="INSERT INTO `ktv_clonal_garden_area` SET
                      `ClonalID` = ?,
                      `GardenNr` = ?,
                      `OrderNr` = ?,
                      `Latitude` = ?,
                      `Longitude` = ?,
                      `DateCreated` = NOW(),
                      `CreatedBy` = ?";
                $p = array(
                    $ClonalID,
                    $post['GardenNr'],
                    $dataGardenArea[$i]['OrderNr'],
                    $dataGardenArea[$i]['Latitude'],
                    $dataGardenArea[$i]['Longitude'],
                    $_SESSION['userid']
                );
                $query = $this->db->query($sql,$p);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['comboGardenNr'] = (string) $post['GardenNr'].'@'.$ClonalID;
            $results['prosesnya'] = 'insert';
            $results['success']    = true;
            $results['message']    = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function updateClonalGarden($post){
        $sce_id = getSceID();

        //get farmerId
        $sql="SELECT FarmerID FROM sce_farmer WHERE SceID = ? LIMIT 1";
        $query = $this->db->query($sql,array($sce_id));
        $data = $query->row_array();
        $FarmerID = $data['FarmerID'];

        $ClonalID = $post['ClonalID'];
        unset($post['ClonalID']);

        //garden nr
        unset($post['GardenNr']);
        //$tmp = explode('@',$post['GardenNr']);
        //$post['GardenNr'] = $tmp[0];

        $SurveyNr = $post['SurveyNr'];
        unset($post['SurveyNr']);

        foreach($post as $k=>$v){
            $k = str_replace("FCG", "", $k);
            if($k=='CG45') $k = '45';
            if($k=='CG45Nr') $k = '45Nr';
            $update[$k] = $v;
        }

        $update['ObjType'] = 'farmer';
        $update['ObjID'] = $FarmerID;
        $update['DateUpdated'] = date('Y-m-d H:i:s');
        $update['LastModifiedBy'] = $_SESSION['userid'];

        $this->db->where('ClonalID', $ClonalID);
        $query = $this->db->update('ktv_clonal_garden', $update);

        if($query){
            $results['success'] = 'sukses';
            $results['message'] = "record updated.";
            $results['id'] = $update['ClonalID'];
        }else{
            $results['success'] = 'gagal';
            $results['message'] = "Error. Please reload page and try again.";
        }
        return $results;
    }

    public function deleteClonalGarden($ClonalID){
        $this->db->trans_start();

        //hapus area
        $sql="DELETE FROM ktv_clonal_garden_area WHERE ClonalID = ?";
        $query = $this->db->query($sql,array((int) $ClonalID));

        //hapus clonal
        $sql="DELETE FROM ktv_clonal_garden WHERE ClonalID = ?";
        $query = $this->db->query($sql,array((int) $ClonalID));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success']    = true;
            $results['message']    = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getClonalPenjualan($ClonalID,$start,$limit){
        $sql="SELECT
                    SQL_CALC_FOUND_ROWS
                  a.`ClonalTransactionID` AS id,
                  a.`ClonalID`,
                  a.`Buyer`,
                  a.`Volume`,
                  a.`CloneTypeID`,
                  a.`Price`,
                  a.Volume * a.Price AS Total,
                  DATE(a.`DateTransaction`) AS DateTransaction,
                  b.CloneTypeName
                FROM
                    `ktv_clonal_garden_transaction` a
                    LEFT JOIN ktv_clone_type b ON a.CloneTypeID = b.CloneTypeID
                WHERE
                    a.ClonalID = ? AND
                    a.StatusCode != 'nullified'
                ORDER BY a.ClonalTransactionID DESC
                #LIMIT ?,?";
        $query = $this->db->query($sql, array($ClonalID,(int) $start,(int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function createClonalPenjualan($paramInsert){
        $tgl = explode("T",$paramInsert['DateTransaction']);

        $sql="INSERT INTO  `ktv_clonal_garden_transaction` SET
              `ClonalID` = ?,
              `Buyer` = ?,
              `Volume` = ?,
              `CloneTypeID` = ?,
              `Price` = ?,
              `DateTransaction` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $paramInsert['ClonalID'],
            $paramInsert['Buyer'],
            $paramInsert['Volume'],
            $paramInsert['CloneTypeID'],
            $paramInsert['Price'],
            $tgl[0],
            $_SESSION['userid']
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

    public function updateClonalPenjualan($paramUpdate){
        $tgl = explode("T",$paramUpdate['DateTransaction']);

        $sql="UPDATE `ktv_clonal_garden_transaction` SET
                  `Buyer` = ?,
                  `Volume` = ?,
                  `CloneTypeID` = ?,
                  `Price` = ?,
                  `DateTransaction` = ?,
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = ?
                WHERE
                    ClonalTransactionID = ?
                LIMIT 1";
        $p = array(
            $paramUpdate['Buyer'],
            $paramUpdate['Volume'],
            $paramUpdate['CloneTypeID'],
            $paramUpdate['Price'],
            $tgl[0],
            $_SESSION['userid'],
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

    public function deleteClonalPenjualan($id){
        $sql="UPDATE `ktv_clonal_garden_transaction` SET
                  StatusCode = 'nullified',
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = ?
                WHERE
                    ClonalTransactionID = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $id
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function getClonalMonitoring($ClonalID,$start,$limit){
        $sql="SELECT
                SQL_CALC_FOUND_ROWS
              `ClonalMonitoringID`,
              `ClonalMonitoringID` AS id,
              `ClonalID`,
              `MonitoringDate`,
              `MonitoringStatus`,
              `Description`
            FROM
                `ktv_clonal_garden_monitoring`
            WHERE
                StatusCode != 'nullified' AND
                ClonalID = ?
            ORDER BY ClonalMonitoringID DESC
            #LIMIT ?,?";
        $query = $this->db->query($sql, array($ClonalID,(int) $start,(int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function insertClonalMonitoring($paramInsert){
        $tgl = explode("T"  ,$paramInsert['MonitoringDate']);

        $sql="INSERT INTO `ktv_clonal_garden_monitoring` SET
              `ClonalID` = ?,
              `MonitoringDate` = ?,
              `MonitoringStatus` = ?,
              `Description` = ?,
              `DateCreated` = NOW(),
              `CreatedBy` = ?";
        $p = array(
            $paramInsert['ClonalID'],
            $tgl[0],
            $paramInsert['MonitoringStatus'],
            $paramInsert['Description'],
            $_SESSION['userid']
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

    public function updateClonalMonitoring($paramUpdate){
        $tgl = explode("T",$paramUpdate['MonitoringDate']);

        $sql="UPDATE `ktv_clonal_garden_monitoring` SET
              `MonitoringDate` = ?,
              `MonitoringStatus` = ?,
              `Description` = ?,
              `DateUpdated` = NOW(),
              `LastModifiedBy` = ?
            WHERE
              ClonalMonitoringID = ?
            LIMIT 1";
        $p = array(
            $tgl[0],
            $paramUpdate['MonitoringStatus'],
            $paramUpdate['Description'],
            $_SESSION['userid'],
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

    public function deleteClonalMonitoring($id){
        $sql="UPDATE `ktv_clonal_garden_monitoring` SET
                  StatusCode = 'nullified',
                  `DateUpdated` = NOW(),
                  `LastModifiedBy` = ?
                WHERE
                    ClonalMonitoringID = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $id
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function getClonalGardenPolygonArea($ClonalID,$latitude,$longitude){
        $sql="SELECT
                Latitude,
                Longitude
            FROM
                ktv_clonal_garden_area
            WHERE
                ClonalID = ?
            ORDER BY OrderNr ASC";
        $query = $this->db->query($sql,array($ClonalID));
        //$data = $query->result_array();
        if($query->num_rows() > 0){
            $result = "[";
            $no = 0;
            foreach ($query->result() as $row) {
                if($no!=0){
                    $result .= ",";
                }
                $result .= "[";
                $result .= $row->Latitude;
                $result .= ",";
                $result .= $row->Longitude;
                $result .= "]";
                $no++;
            }
            $result .= "]";
            return $result;
        }else{
            if(($latitude!='0.000000'||$longitude!='0.000000') && ($latitude!=''||$longitude!='')){
                return "[[$latitude,$longitude]]";
            }else{
                return "[[-1.2674336,113.6939433]]";
            }
        }
    }

    public function updateClonalPolygonCenter($ClonalID, $GardenNr, $lat, $long){
        $sql = "SELECT * FROM ktv_clonal_garden WHERE ClonalID=? AND GardenNr=?";
        $query = $this->db->query($sql, array($ClonalID, $GardenNr));
        if($lat)
        if((@$query->row()->Latitude == '' || @$query->row()->Latitude == '0.000000') && (@$query->row()->Longitude == '' || @$query->row()->Longitude == '0.000000')){
            $sql = "UPDATE ktv_clonal_garden SET Latitude=?, Longitude=? WHERE ClonalID=? AND GardenNr=?";
            $query = $this->db->query($sql, array($lat, $long, $ClonalID, $GardenNr));
            if ($query) {
                $results['success'] = true;
                $results['message'] = "Success.";
            } else {
                $results['success'] = false;
                $results['message'] = "Error. Please reload page and try again.";
            }
            return $results;
        }else{
            $results['success'] = true;
            $results['message'] = "No Update";
            return $results;
        }
    }

    public function updateClonalPolygon($ClonalID, $GardenNr, $area, $luas, $lat, $long){
        $result = false;

        if($luas=='0.00'){
            $lat = null; $long = null;
        }

        $this->db->trans_start(FALSE);

        //hapus datanya terlebih dahulu
        $sql="DELETE FROM ktv_clonal_garden_area WHERE ClonalID = ?";
        $query = $this->db->query($sql,array($ClonalID));

        // insert new area
        if (is_array($area)) {
            $no = 1;
            $data = array();
            foreach ($area as $val) {
              $data[] = array(
                'ClonalID'    => $ClonalID,
                'GardenNr'    => $GardenNr,
                'OrderNr'     => $no,
                'DateCreated' => date('Y-m-d H:i:s'),
                'CreatedBy'   => $_SESSION['userid'],
                'Latitude'    => $val[0],
                'Longitude'   => $val[1]
               );
              $no++;
            }
            $this->db->insert_batch('ktv_clonal_garden_area', $data);
            $sql = "UPDATE ktv_clonal_garden SET Area=?,Latitude=?,Longitude=? WHERE ClonalID=? AND GardenNr=?";
            $query = $this->db->query($sql, array($luas,$lat,$long, $ClonalID, $GardenNr));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Success.";
        } else {
            $results['success'] = false;
            $results['message'] = "Error. Please reload page and try again.";
        }
        return $results;
    }

    public function updateClonalGardenAreaGet($ClonalID){
        $sql = "SELECT Area,Latitude,Longitude FROM ktv_clonal_garden WHERE ClonalID=? LIMIT 1";
        $query = $this->db->query($sql, array($ClonalID));
        $result= $query->result_array();
        return $result[0];
    }

}
?>
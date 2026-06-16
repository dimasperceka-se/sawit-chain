<?php
/*
 * @Author: sofyan
 * @Date:   2021-11-08 
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mfarm_summary extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    private function generateSqlHakAkses(){
        $sqlHakAkses = array();

        if($_SESSION['is_admin'] == "1"){
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";

            //cek ktv_access_partner_mill
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_mill acc_pmi ON a.MillID = acc_pmi.apmiMillID AND acc_pmi.apmiPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = " AND f.DistrictID IN (".$_SESSION['daerah_access'].")";
        }

        return $sqlHakAkses;
    }

    public function getGridMainFarmSummary($pSearch,$start,$limit,$sortingField,$sortingDir){
        
        $result = array();
        $result["verified"]=0;

        $sqlFilter = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if($pSearch['prov'] != ""){
            $sqlFilter .= " AND e.ProvinceID = ".$pSearch['prov'];
        }

        if($pSearch['kab'] != ""){
            $sqlFilter .= " AND f.DistrictID = ".$pSearch['kab'];
        }

        if($pSearch['kec'] != ""){
            $sqlFilter .= " AND d.SubDistrictID = ".$pSearch['kec'];
        }


        if($pSearch['textSearch'] != ""){
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['textSearch']}%' OR a.MemberDisplayID like '%{$pSearch['textSearch']}%' ) ";
        }

        if($pSearch['CmbPolygonStatus'] == "irrelevant"){
            $sqlFilter .= " AND fgeo.StatusCheck=''";
        }elseif($pSearch['CmbPolygonStatus'] != "" && $pSearch['CmbPolygonStatus'] != "all" ){
            $sqlFilter .= " AND fgeo.StatusCheck = '".$pSearch['CmbPolygonStatus']."'";
        }

        //BENTUK QUERY FILTER =============================================== (END)
        //Bentuk SQL Hak Akses
        $sqlHakAkses = $this->generateSqlHakAkses();

        $sql="SELECT SQL_CALC_FOUND_ROWS
                fgeo.`MemberID`
                , a.MemberDisplayID
                , a.MemberName
                , fgeo.PlotNr
                , fgeo.Revision
                , fgeo.AreaHa
                , IF(fgeo.StatusCheck='','Irrelevant',fgeo.StatusCheck) as StatusCheck
                , DATE_FORMAT(fgeo.`DateCreated`,'%Y-%m-%d') AS DateCreated
                , e.Province as ProvinceName
                , f.District as DistrictName
                , IFNULL(d.SubDistrict,'-') AS SubDistrictName
                , IFNULL(c.Village,'-') AS VillageName
                , null AS Region
                , null AS Location
            FROM
                ( 
                    SELECT x.MemberID 
                        , x.PlotNr 
                        , x.Revision 
                        , x.AreaHa 
                        , x.StatusCheck 
                        , x.DateCreated
                        , ROW_NUMBER() OVER (PARTITION BY x.MemberID, x.PlotNr ORDER BY x.Revision DESC) AS ROWNUM
                    FROM ktv_survey_plot_polygon_geo x
                ) fgeo
                LEFT JOIN `ktv_members` a ON fgeo.MemberID = a.MemberID
                LEFT JOIN `ktv_survey_plot` b ON fgeo.MemberID = b.MemberID
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID     
            WHERE 1=1
                AND ROWNUM = 1
                AND a.`StatusCode` = 'active'
                AND b.`StatusCode` = 'active'
                $sqlFilter
            GROUP BY fgeo.MemberID, fgeo.PlotNr
            LIMIT ?,?";

        $p = array(
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
            // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;

        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        // Status ==================================================================================================================

        $sql = "SELECT IF(r.StatusCheck = '','irrelevant',r.StatusCheck) as StatusCheck, COUNT(r.StatusCheck) as count
                    FROM
                        (
                            SELECT fgeo.StatusCheck
                            FROM
                                ( 
                                    SELECT x.MemberID 
                                        , x.PlotNr 
                                        , x.Revision 
                                        , x.AreaHa 
                                        , x.StatusCheck 
                                        , x.DateCreated
                                        , ROW_NUMBER() OVER (PARTITION BY x.MemberID, x.PlotNr ORDER BY x.Revision DESC) AS ROWNUM
                                    FROM ktv_survey_plot_polygon_geo x
                                ) fgeo
                                LEFT JOIN `ktv_members` a ON fgeo.MemberID = a.MemberID
                                LEFT JOIN `ktv_survey_plot` b ON fgeo.MemberID = b.MemberID
                                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID     
                            WHERE 1=1
                                AND ROWNUM = 1
                                AND a.`StatusCode` = 'active'
                                AND b.`StatusCode` = 'active'
                                $sqlFilter
                            GROUP BY fgeo.MemberID, fgeo.PlotNr
                        ) r
                    GROUP BY r.StatusCheck
                "; 
        $status = $this->db->query($sql)->result_array();

        foreach ($status as $s) {
            $result[$s['StatusCheck']] = $s['count'];
        }

        //generate information grid result (begin)
        if($sortingDir == 'ASC'){
            $sortingInfo = 'ascending';
        }
        if($sortingDir == 'DESC'){
            $sortingInfo = 'descending';
        }

        $infoFilter = '';
        foreach ($pSearch as $key => $value) {
            if($value != ""){
                switch ($key) {
                    case 'prov':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Province').'</li>';
                    break;
                    case 'kab':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('District').'</li>';
                    break;
                    case 'kec':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Kecamatan').'</li>';
                    break;
                    case 'textSearch':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('ID / Name').'</li>';
                    break;
                }
            }
        }

        $_SESSION['informationGrid'] = '<div class="gridInformationContainer">
                                <h4>Information</h4>
                                <ul>
                                    <li>'.$query->row()->total.' '.lang('datas, Sorted by').' '.lang($sortingField).' '.$sortingInfo.'</li>
                                    '.$infoFilter.'
                                </ul>
                            </div>';
        //generate information grid result (end)
        return $result;
    }

    public function getMainFarmSummaryExcel($pSearch){
        $sqlFilter = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if($pSearch['prov'] != ""){
            $sqlFilter .= " AND e.ProvinceID = ".$pSearch['prov'];
        }

        if($pSearch['kab'] != ""){
            $sqlFilter .= " AND f.DistrictID = ".$pSearch['kab'];
        }

        if($pSearch['kec'] != ""){
            $sqlFilter .= " AND d.SubDistrictID = ".$pSearch['kec'];
        }


        if($pSearch['textSearch'] != ""){
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['textSearch']}%' OR a.MemberDisplayID like '%{$pSearch['textSearch']}%' ) ";
        }

        if($pSearch['CmbPolygonStatus'] == "irrelevant"){
            $sqlFilter .= " AND fgeo.StatusCheck=''";
        }elseif($pSearch['CmbPolygonStatus'] != "" && $pSearch['CmbPolygonStatus'] != "all" ){
            $sqlFilter .= " AND fgeo.StatusCheck = '".$pSearch['CmbPolygonStatus']."'";
        }
        //BENTUK QUERY FILTER =============================================== (END)

        //Bentuk SQL Hak Akses
        $sqlHakAkses = $this->generateSqlHakAkses();

        //BENTUK QUERY FILTER =============================================== (END)
        //'FarmerID', 'Name', 'PlotNr', 'Revision', 'Area (ha)','Status','District', 'Location','Date Created'

        $sql="SELECT SQL_CALC_FOUND_ROWS
                a.MemberDisplayID as FarmerID
                , a.MemberName as FarmerName
                , fgeo.PlotNr 
                , fgeo.Revision
                , fgeo.AreaHa
                , fgeo.StatusCheck
                , DATE_FORMAT(fgeo.`DateCreated`,'%Y-%m-%d') AS DateCreated
                , CONCAT(e.Province, ', ', f.District) AS Region
                , IF(ISNULL(d.SubDistrict) and ISNULL(c.Village),'-', 
                    IF(ISNULL(c.Village), d.SubDistrict, CONCAT(d.SubDistrict, ', ', c.Village))
                  ) AS Location
            FROM
             
                ( 
                    SELECT x.MemberID 
                        , x.PlotNr 
                        , x.Revision 
                        , x.AreaHa 
                        , x.StatusCheck 
                        , x.DateCreated
                        , ROW_NUMBER() OVER (PARTITION BY x.MemberID, x.PlotNr ORDER BY x.Revision DESC) AS ROWNUM
                    FROM ktv_survey_plot_polygon_geo x
                ) fgeo
                LEFT JOIN `ktv_members` a ON fgeo.MemberID = a.MemberID
                LEFT JOIN `ktv_survey_plot` b ON fgeo.MemberID = b.MemberID
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID     
            WHERE 1=1
                AND ROWNUM = 1
                AND a.`StatusCode` = 'active'
                AND b.`StatusCode` = 'active'
                $sqlFilter
            GROUP BY fgeo.MemberID, fgeo.PlotNr";

        $query = $this->db->query($sql);
        if($query->num_rows()>0){
            return $query->result_array();
        }else{
            return false;
        }
    }

    public function GetFarmSummaryPolygon($pSearch) {
        $DataReturn = array();

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if($pSearch['prov'] != ""){
            $sqlFilter .= " AND e.ProvinceID = ".$pSearch['prov'];
        }

        if($pSearch['kab'] != ""){
            $sqlFilter .= " AND f.DistrictID = ".$pSearch['kab'];
        }

        if($pSearch['kec'] != ""){
            $sqlFilter .= " AND d.SubDistrictID = ".$pSearch['kec'];
        }


        if($pSearch['textSearch'] != ""){
            $sqlFilter .= " AND (a.MemberName like '%{$pSearch['textSearch']}%' OR a.MemberDisplayID like '%{$pSearch['textSearch']}%' ) ";
        }

        if($pSearch['CmbPolygonStatus'] != "" && $pSearch['CmbPolygonStatus'] != "all" ){
            $sqlFilter .= " AND fgeo.StatusCheck = '".$pSearch['CmbPolygonStatus']."'";
        }

        // var_dump($pSearch);die();

        //BENTUK QUERY FILTER =============================================== (END)

        //========== Hak akses (Begin) =====================
        if($_SESSION['group_access'] != "") $sqlHakAkses = "AND ds.DistrictID IN ({$_SESSION['group_access']})"; else $sqlHakAkses = "AND ds.DistrictID IN ('')";
        //========== Hak akses (End) =======================


        $sql = "SELECT SQL_CALC_FOUND_ROWS
            fgeo.`MemberID`
            , a.`MemberDisplayID` AS ID
            , a.`MemberName` AS FarmerName
            , fgeo.PlotNr
            , fgeo.Revision
            , fgeo.AreaHa
            , IF(fgeo.StatusCheck='','Irrelevant',fgeo.StatusCheck) as StatusCheck
            , a.PartnerID
            , DATE_FORMAT(fgeo.`DateCreated`,'%Y-%m-%d') AS DateCreated
            , e.Province as ProvinceName
            , f.District as DistrictName
            , IFNULL(d.SubDistrict,'-') AS SubDistrictName
            , IFNULL(c.Village,'-') AS VillageName
            , ST_ASGEOJSON(fgeo.Polygon) PolyGeoJson
        FROM
            ( 
                SELECT x.MemberID 
                    , x.PlotNr 
                    , x.Revision 
                    , x.AreaHa 
                    , x.StatusCheck 
                    , x.DateCreated
                    , x.Polygon
                    , ROW_NUMBER() OVER (PARTITION BY x.MemberID, x.PlotNr ORDER BY x.Revision DESC) AS ROWNUM
                FROM ktv_survey_plot_polygon_geo x
            ) fgeo
                LEFT JOIN `ktv_members` a ON fgeo.MemberID = a.MemberID
                LEFT JOIN `ktv_survey_plot` b ON fgeo.MemberID = b.MemberID
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID     
            WHERE 1=1
                AND ROWNUM = 1
                AND a.`StatusCode` = 'active'
                AND b.`StatusCode` = 'active'
                $sqlFilter
            GROUP BY fgeo.MemberID, fgeo.PlotNr";
        
        $DataReturn = $this->db->query($sql)->result_array();

        return $DataReturn;
    }

}
?>
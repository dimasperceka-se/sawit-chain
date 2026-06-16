<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-03 15:33:31
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mkcp extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('awsfileupload');
    }

    public function getKCPBulk($KCPID){
        $sql = "SELECT
            a.KCPID
            , a.KCPDisplayID
            , a.KCPName
            , a.KCPRole
            , a.CompanyName
            , a.Alias
            , a.`Year`
            , a.`Status`
            , a.VillageID Village
            , d.SubDistrictID Subdistrict
            , f.DistrictID District
            , e.ProvinceID Province
            , a.Phone
            , a.Address
            , a.Latitude
            , a.Longitude
        FROM
            ktv_kcp_bulking a
        LEFT JOIN 
            ktv_village c ON a.`VillageID` = c.`VillageID`
        LEFT JOIN 
            ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
        LEFT JOIN 
            ktv_district f ON d.DistrictID = f.DistrictID
        LEFT JOIN 
            ktv_province e ON f.ProvinceID = e.ProvinceID
        WHERE
            a.StatusCode = 'active'
        AND
            a.KCPID = ?";

        $query = $this->db->query($sql,array($KCPID));

        if($query->num_rows()>0){
            $data = array();

            foreach($query->result() as $num => $row){
                foreach($row as $key => $value){
                    if($key == "Province" || $key == "District" || $key == "Subdistrict" || $key == "Village"){
                        $data[$key] = $value;
                    }else{
                        $data["Koltiva.view.KCP.FormMainKCPBulk-FormBasicData-".$key] = $value;
                    }
                }
            }

            $return["success"]  = true;
            $return["data"]     = $data;
        }else{
            $return["success"]  = true;
            $return["data"]     = array();
        }

        return $return;
    }

    public function insertKCPBulk($paramPost){
        $post = array();
        if($paramPost){
            //prep variabel (begin)
            foreach ($paramPost as $key => $value) {
                $keyNew = str_replace("Koltiva_view_KCP_FormMainKCPBulk-FormBasicData-", '', $key);
                if ($value == "")
                    $value = null;

                $post[$keyNew] = $value;
            }
            $post['VillageID']      = $post['Village'];

            unset($post["SupplychainID"]);
            unset($post["Province"]);
            unset($post["District"]);
            unset($post["Subdistrict"]);
            unset($post["Village"]);

            $id = $this->GenDisplayID($post['VillageID'],$post['KCPRole']);

            $post['KCPID']          = $id['KCPID'];
            $post['KCPDisplayID']   = $id['KCPDisplayID'];
            $post['CreatedBy']      = $_SESSION['userid'];
            $post['DateCreated']    = date("Y-m-d H:i:s");

            $query = $this->db->insert("ktv_kcp_bulking",$post);

            if($query){
                if($post["Latitude"] != '' & $post["Longitude"] != ""){
                    $LatitudeProses = (float) $post['Latitude'];
                    $LongitudeProses = (float) $post['Longitude'];
                    
                    //Check Latitude
                    if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                        //Cek valid tidak koordinatnya
                        $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                        $DataCekKoordinat = $this->db->query($sql2)->row_array();
                        
                        if ($DataCekKoordinat['HasilCek'] == "1") {
                            $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                            $sql2 = "UPDATE ktv_kcp_bulking a SET
                                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                    WHERE
                                        a.`KCPID` = ?
                                    LIMIT 1";
                            $p = array(
                                $id['KCPID']
                            );
                            $query = $this->db->query($sql2,$p);
                        }

                    }
                }

                $results['success'] = true;
                $results['message'] = "Data saved";
                $results['KCPID'] = $id['KCPID'];
            }else{                
                $results['success'] = false;
                $results['message'] = "Failed to save data";
            }
        }else{            
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }

        return $results;
    }

    public function updateKCPBulk($paramPost){
        $post = array();
        if($paramPost){
            //prep variabel (begin)
            foreach ($paramPost as $key => $value) {
                $keyNew = str_replace("Koltiva_view_KCP_FormMainKCPBulk-FormBasicData-", '', $key);
                if ($value == "")
                    $value = null;

                $post[$keyNew] = $value;
            }
            $KCPID     = $post['KCPID'];

            unset($post["SupplychainID"]);
            unset($post["Province"]);
            unset($post["District"]);
            unset($post["Subdistrict"]);
            unset($post["Village"]);
            unset($post["KCPID"]);
            unset($post["KCPDisplayID"]);


            $post['LastModifiedBy']      = $_SESSION['userid'];
            $post['DateUpdated']    = date("Y-m-d H:i:s");

            $this->db->where('KCPID',$KCPID);
            $query = $this->db->update("ktv_kcp_bulking",$post);

            if($query){
                if($post["Latitude"] != '' & $post["Longitude"] != ""){
                    $LatitudeProses = (float) $post['Latitude'];
                    $LongitudeProses = (float) $post['Longitude'];
                    
                    //Check Latitude
                    if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                        //Cek valid tidak koordinatnya
                        $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                        $DataCekKoordinat = $this->db->query($sql2)->row_array();
                        
                        if ($DataCekKoordinat['HasilCek'] == "1") {
                            $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                            $sql2 = "UPDATE ktv_kcp_bulking a SET
                                        a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                    WHERE
                                        a.`KCPID` = ?
                                    LIMIT 1";
                            $p = array(
                                $KCPID
                            );
                            $query = $this->db->query($sql2,$p);
                        }

                    }
                }

                $results['success'] = true;
                $results['message'] = "Data saved";
                $results['KCPID'] = $KCPID;
            }else{                
                $results['success'] = false;
                $results['message'] = "Failed to save data";
            }
        }else{            
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }

        return $results;
    }

    public function GenDisplayID($VillageID,$prefixId='kcp'){
        //MillID
        $sql="SELECT
                a.`KCPID`
            FROM
                ktv_kcp_bulking a
            ORDER BY a.`KCPID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if($data['KCPID'] != ""){
            $return['KCPID'] = $data['KCPID'] + 1;
        }else{
            $return['KCPID'] = 1;
        }

        if($prefixId == "kcp"){
            $prefixId = 'K';
        }else{
            $prefixId = 'B';
        }

        //KCPDisplayID
        $awalan = $prefixId.substr($VillageID,0,7);
        $sql="SELECT
                a.`KCPDisplayID`
            FROM
                ktv_kcp_bulking a
            WHERE
                a.`KCPDisplayID` LIKE '$awalan%'
            ORDER BY a.`KCPDisplayID` DESC
            LIMIT 1";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if($data['KCPDisplayID'] != ""){
            $temp = (int) substr($data['KCPDisplayID'],-4);
            $temp++;

            switch (strlen($temp)) {
                case '1':
                    $temp = $awalan."000".$temp;
                break;
                case '2':
                    $temp = $awalan."00".$temp;
                break;
                case '3':
                    $temp = $awalan."0".$temp;
                break;
                default:
                    $temp = $awalan.$temp;
                break;
            }
            $return['KCPDisplayID'] = $temp;
        }else{
            $return['KCPDisplayID'] = $awalan."0001";
        }

        return $return;
    }

    public function deleteKCPBulk($KCPID){
        $sql="UPDATE `ktv_kcp_bulking` SET
                StatusCode = 'nullified'
            WHERE
                `KCPID` = ?
            LIMIT 1";
        $p = array(
            $KCPID
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Data deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    public function getGridMainMill($pSearch,$start,$limit,$sortingField,$sortingDir){
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
            $sqlFilter .= " AND (a.KCPName like '%{$pSearch['textSearch']}%' OR a.KCPDisplayID like '%{$pSearch['textSearch']}%' ) ";
        }

        if($pSearch['rowStatusPerusahaan'] == "true"){
            $sqlFilter .= " AND a.Status = '{$pSearch['cmbStatusPerusahaan']}' ";
        }

        if($pSearch['rowTahunTerbentuk'] == "true"){
            $sqlFilter .= " AND a.Year {$pSearch['cmbOpTahunTerbentuk']} '{$pSearch['textTahunTerbentuk']}' ";
        }

        if($pSearch['rowPhone'] == "true"){
            $sqlFilter .= " AND a.Phone LIKE '{$pSearch['textPhone']}' ";
        }

        if($pSearch['rowHavePhoto'] == "true"){
            if($pSearch['cmbHavePhoto'] == "Yes"){
                $sqlFilter .= " AND a.Photo != '' AND a.Photo IS NOT NULL ";
            }
            if($pSearch['cmbHavePhoto'] == "No"){
                $sqlFilter .= " AND (a.Photo = '' OR a.Photo IS NULL) ";
            }
        }

        if($pSearch['rowTotalPermanentEmployee'] == "true"){
            $sqlFilter .= " AND (a.`PermanentEmployeeMale` + a.`PermanentEmployeeFemale`) {$pSearch['cmbOpTotalPermanentEmployee']} '{$pSearch['textTotalPermanentEmployee']}' ";
        }
        //BENTUK QUERY FILTER =============================================== (END)

        //Bentuk SQL Hak Akses
        // $sqlHakAkses = $this->generateSqlHakAkses();

        if($sortingField == "") $sortingField = 'Name';
        if($sortingDir == "") $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.`KCPID` AS id
                , a.`KCPDisplayID`
                , a.`KCPName` AS `Name`
                , a.`Address`
                , c.`Village` AS Desa
                , d.`SubDistrict` AS Kecamatan
                , IFNULL(a.DateUpdated,a.`DateCreated`) AS LastUpdated
                , e.Province
                , f.District
                , CASE
                    WHEN a.Status = '1' THEN '".lang('Sole Proprietorship')."'
                    WHEN a.Status = '2' THEN '".lang('Partnership')."'
                    WHEN a.Status = '3' THEN '".lang('Limited Partnership')."'
                    WHEN a.Status = '4' THEN '".lang('Limited Liability Company')."'
                    WHEN a.Status = '5' THEN '".lang('Corporation')."'
                    WHEN a.Status = '6' THEN '".lang('Cooperative')."'
                    WHEN a.Status = '7' THEN '".lang('Foundation')."'
                    WHEN a.Status = '8' THEN '".lang('Association')."'
                    WHEN a.Status = '9' THEN '".lang('State Owned')."'
                END AS StatusPerusahaan
                , a.`Year` AS TahunTerbentuk
                , a.`Alias`
                , a.`Phone`
                , a.CompanyName
                , GROUP_CONCAT(DISTINCT a.Latitude, ',', a.Longitude) AS GPS
            FROM
                ktv_kcp_bulking a
                LEFT JOIN ktv_village c ON a.`VillageID` = c.`VillageID`
                LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.`SubDistrictID`
                LEFT JOIN ktv_district f ON d.DistrictID = f.DistrictID
                LEFT JOIN ktv_province e ON f.ProvinceID = e.ProvinceID
                -- {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                $sqlFilter
                -- {$sqlHakAkses['where']}
            GROUP BY a.KCPID
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $p = array(
            (int) $start, (int) $limit
        );
        $query = $this->db->query($sql,$p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

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

            if($value == "true"){
                switch ($key) {
                    case 'rowStatusPerusahaan':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Status Perusahaan').'</li>';
                    break;
                    case 'rowTahunTerbentuk':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Tahun Terbentuk').'</li>';
                    break;
                    case 'rowPhone':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Phone').'</li>';
                    break;
                    case 'rowTotalPermanentEmployee':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Total Permanent Employee').'</li>';
                    break;
                    case 'rowHavePhoto':
                        $infoFilter .= '<li>'.lang('Filter by').' '.lang('Have Photo').'</li>';
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
}
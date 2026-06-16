<?php
class mcooperatives extends CI_Model
{
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->muserprofile->getUserProfile();
    }

    public function updateCooperatives($post){

        $post['LastModifiedBy']  = $_SESSION['userid'];
        $post['DateUpdated']  = date("Y-m-d H:i:s");
        $CoopID = $post['CoopID'];

        unset($post['CoopID']);
        unset($post['Province']);
        unset($post['District']);
        unset($post['Subdistrict']);
        unset($post['Village']);


        $this->db->where('CoopID',$CoopID);
        $update = $this->db->update("ktv_cooperatives",$post);
        

        if($update){
            $results['success'] = true;
            $results['message'] = lang("Data Saved");
            $results['CoopID'] = $CoopID;            

            //Koordinat Geometry ============= (Begin)
            if($post['Latitude'] != "" && $post['Longitude'] != "") {

                $LatitudeProses = (float) $post['Latitude'];
                $LongitudeProses = (float) $post['Longitude'];
                
                //Check Latitude
                if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                    //Cek valid tidak koordinatnya
                    $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                    $DataCekKoordinat = $this->db->query($sql2)->row_array();
                    
                    if ($DataCekKoordinat['HasilCek'] == "1") {
                        $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                        $sql2 = "UPDATE ktv_cooperatives a SET
                                    a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                WHERE
                                    a.`CoopID` = ?
                                LIMIT 1";
                        $p = array(
                            $CoopID
                        );
                        
                        $this->db->query($sql2,$p);
                    }
                }
            }
        }else{
            $results['success'] = false;
            $results['message'] = lang("Failed to Save Data");
        }
        
        return $results;
    }

    public function insertCooperatives($post){
        $post['CreatedBy']  = $_SESSION['userid'];
        $post['DateCreated']  = date("Y-m-d H:i:s");

        unset($post['CoopID']);
        unset($post['Province']);
        unset($post['District']);
        unset($post['Subdistrict']);
        unset($post['Village']);

        $insert = $this->db->insert("ktv_cooperatives",$post);

        if($insert){
            $results['success'] = true;
            $results['message'] = lang("Data Saved");
            $results['CoopID'] = $this->db->insert_id();            

            //Koordinat Geometry ============= (Begin)
            if($post['Latitude'] != "" && $post['Longitude'] != "") {

                $LatitudeProses = (float) $post['Latitude'];
                $LongitudeProses = (float) $post['Longitude'];
                
                //Check Latitude
                if( ($LatitudeProses >= -90 && $LatitudeProses <= 90) && ($LongitudeProses >= -180 && $LongitudeProses <= 180) ) {

                    //Cek valid tidak koordinatnya
                    $sql2 = "SELECT ST_IsValid(ST_GeomFromText('POINT({$LatitudeProses} {$LongitudeProses})', 4326)) AS HasilCek";
                    $DataCekKoordinat = $this->db->query($sql2)->row_array();
                    
                    if ($DataCekKoordinat['HasilCek'] == "1") {
                        $PointInsert = "POINT({$LatitudeProses} {$LongitudeProses})";

                        $sql2 = "UPDATE ktv_cooperatives a SET
                                    a.`LatLong` = ST_GEOMFROMTEXT('$PointInsert', 4326)
                                WHERE
                                    a.`CoopID` = ?
                                LIMIT 1";
                        $p = array(
                            $this->db->insert_id()
                        );
                        
                        $this->db->query($sql2,$p);
                    }
                }
            }
        }else{
            $results['success'] = false;
            $results['message'] = lang("Failed to Save Data");
        }
        
        return $results;
    }

    public function readDatas($kec, $kab, $prov, $start, $limit, $key, $textsearch)
    {
        $pSearch['prov']    = $prov;
        $pSearch['kab']     = $kab;
        $pSearch['kec']     = $kec;
        $pSearch['textSearch'] = $textsearch;
        
        $sqlFilter = $this->generateSqlFilter($pSearch);

        $sqlHakAkses = $this->generateSqlHakAkses();

        if ($sortingField == "")
            $sortingField = 'CoopName';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        if ($opsiCall == 'for_grid') {
            $start = (int) $start;
            $limit = (int) $limit;
            $sqlLimit = " LIMIT {$start},{$limit}";
        } else {
            $sqlLimit = "";
        }

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                kc.CoopID,
                kc.CoopCode,
                kc.CoopName,
                kc.Phone,
                kc.Email,
                kc.YearEstablished,
                kc.Status,
                f.District,
                d.Subdistrict
            FROM
                ktv_cooperatives kc
                {$sqlHakAkses['join']}
                LEFT JOIN ktv_village kv ON kv.VillageID = kc.VillageID
                LEFT JOIN ktv_subdistrict d ON d.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district f ON f.DistrictID = d.DistrictID
                LEFT JOIN ktv_province e ON e.ProvinceID = f.ProvinceID
            WHERE
                1=1
                AND kc.StatusCode = 'active'
                $sqlFilter
                {$sqlHakAkses['where']}
            GROUP BY kc.CoopID
            ORDER BY $sortingField $sortingDir
            $sqlLimit";
    $query = $this->db->query($sql);
    $result['data'] = $query->result_array();

    $query = $this->db->query('SELECT FOUND_ROWS() AS total');
    $result['total'] = $query->row()->total;

        return $result;
    }

    private function generateSqlFilter($pSearch) {
        $sqlFilter = "";

        //BENTUK QUERY FILTER =============================================== (BEGIN)
        if ($pSearch['prov'] != "") {
            $sqlFilter .= " AND e.ProvinceID = " . $pSearch['prov'];
        }

        if ($pSearch['kab'] != "") {
            $sqlFilter .= " AND f.DistrictID = " . $pSearch['kab'];
        }

        if ($pSearch['kec'] != "") {
            $sqlFilter .= " AND d.SubDistrictID = " . $pSearch['kec'];
        }

        if ($pSearch['textSearch'] != "") {
            $sqlFilter .= " AND (kc.CoopName like '%{$pSearch['textSearch']}%' OR kc.CoopCode like '%{$pSearch['textSearch']}%' ) ";
            $_SESSION['grid_filter']['Text'] = $pSearch['textSearch'];
        } else {
            unset($_SESSION['grid_filter']['Text']);
        }

        if ($pSearch['textSearchDesa'] != "") {
            $sqlFilter .= " AND c.Village like '%{$pSearch['textSearchDesa']}%'";
            $_SESSION['grid_filter']['Desa'] = $pSearch['textSearchDesa'];
        } else {
            unset($_SESSION['grid_filter']['Desa']);
        }

        if ($pSearch['categorySearch'] != "") {
            $_SESSION['grid_filter']['FarmerCategory'] = $pSearch['categorySearch'];
            if ($pSearch['categorySearch'] != "Registered")
                $sqlFilter .= " AND a.FarmerCategory = '".$pSearch['categorySearch']."'";
        }

        //advanced filter
        if ($pSearch['AdvRowEnumerator'] == "true") {
            $sqlFilter .= " AND a.CreatedBy = '{$pSearch['AdvTextEnumerator']}' ";
        }
        if ($pSearch['AdvRowHandphone'] == "true") {
            $sqlFilter .= " AND a.HandPhone LIKE '%{$pSearch['AdvTextHandphone']}%' ";
        }

        if ($pSearch['AdvRowAge'] == "true") {
            if ($pSearch['AdvOpAge'] != "" && $pSearch['AdvTextAge'] != "") {
                $sqlFilter .= " AND (a.`DateOfBirth` IS NOT NULL AND a.`DateOfBirth` != '0000-00-00')
                                AND TIMESTAMPDIFF(YEAR, a.DateOfBirth, CURDATE()) " . $pSearch['AdvOpAge'] . " " . $pSearch['AdvTextAge'];
            }
        }

        if ($pSearch['AdvRowMaritalStatus'] == "true") {
            $sqlFilter .= " AND a.MaritalStatus = '{$pSearch['AdvMaritalStatus']}'";
        }

        if ($pSearch['AdvRowDateCollection'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateCollection) BETWEEN '{$pSearch['AdvDateCollectionBegin']}' AND '{$pSearch['AdvDateCollectionEnd']}' ) ";
        }

        if ($pSearch['AdvRowDateCreated'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateCreated) BETWEEN '{$pSearch['AdvDateCreatedBegin']}' AND '{$pSearch['AdvDateCreatedEnd']}' ) ";
        }

        if ($pSearch['AdvRowDateSynced'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateSync) BETWEEN '{$pSearch['AdvDateSyncedBegin']}' AND '{$pSearch['AdvDateSyncedEnd']}' ) ";
        }

        if ($pSearch['AdvRowLastUpdatedDate'] == "true"){
            $sqlFilter .= " AND ( DATE(a.DateUpdated) BETWEEN '{$pSearch['AdvLastUpdatedDateBegin']}' AND '{$pSearch['AdvLastUpdatedDateEnd']}' ) ";
        }

        return $sqlFilter;
        //BENTUK QUERY FILTER =============================================== (END)
    }

    

    private function generateSqlHakAkses() {
        $sqlHakAkses = array();

        /*if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {*/
            //cek ktv_access_staff
            if($_SESSION['role'] != 'SME'){
                $sqlHakAkses['where'] = " AND f.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            }
        /*} else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND SUBSTR(a.VillageID,1,4) IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }*/

        return $sqlHakAkses;
    }

    public function getCmbYearEstablish()
    {
        $sql="SELECT
               a.`TahunTerbentuk` AS id,
               a.`TahunTerbentuk` AS label
            FROM
               ktv_cooperatives a
            WHERE
               a.`TahunTerbentuk` IS NOT NULL AND a.`TahunTerbentuk` != '0000'
            GROUP BY a.`TahunTerbentuk`
            ORDER BY a.`TahunTerbentuk` DESC";
        $data = $this->db->query($sql);
        return $data->result_array();
    }

    

    public function getFarmerGroupMemberInputGrid($CoopID,$textSearch,$villageSearch,$start,$limit,$sortingField, $sortingDir,$Enumerator = null){

        //get ProvinceID (begin)
        $sql="SELECT
                prov.`ProvinceID`
            FROM
                ktv_cooperatives a
            LEFT JOIN
                ktv_village vil on vil.VillageID = a.VillageID
            LEFT JOIN 
                ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
            LEFT JOIN 
                ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
            LEFT JOIN 
                ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
            WHERE
                a.`CoopID` = ?
            LIMIT 1";
        $query = $this->db->query($sql, array($CoopID));
        $data = $query->row_array();
        $ProvinceID = $data['ProvinceID'];
        //get ProvinceID (end)

        //generate filter hak akses (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND dis.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND dis.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //generate filter hak akses (end)

        if ($sortingField == "")
            $sortingField = 'MemberDisplayID';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        if($Enumerator != null || $Enumerator != ''){
            $sqlHakAkses['where'] = " AND a.CreatedBy = '$Enumerator'";
        }

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                a.MemberID
                , a.`MemberDisplayID`
                , a.`MemberName`
                , subd.SubDistrict
                , vil.`Village`
                , s.UserRealName Enumerator
            FROM
                ktv_members a
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID` AND b.`MRoleID` = '1'
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                LEFT JOIN ktv_subdistrict subd ON vil.SubDistrictID = subd.SubDistrictID
                LEFT JOIN ktv_district dis ON subd.DistrictID = dis.DistrictID
                LEFT JOIN ktv_province prov ON dis.ProvinceID = prov.ProvinceID
                LEFT JOIN sys_user s ON s.UserId = a.CreatedBy
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                AND prov.ProvinceID = ?
                AND ( a.`FarmerGroupID` IS NULL AND a.CoopID IS NULL)
                AND ( a.MemberName LIKE ? OR a.MemberDisplayID LIKE ? )
                AND vil.Village LIKE ?
                {$sqlHakAkses['where']}
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?";
        $query = $this->db->query($sql, array(
            $ProvinceID,
            '%'.$textSearch.'%', '%'.$textSearch.'%',
            '%'.$villageSearch.'%',
            (int) $start, (int) $limit)
        );
        
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function inputFarmerGroupMember($arrMemberID,$CoopID){
        $this->db->trans_begin();

        $impMemberID = implode(",",$arrMemberID);

        $sql="UPDATE ktv_members a SET
                    a.`CoopID` = ?
                    , a.inCoop = '1'
                    , a.`DateUpdated` = NOW()
                    , a.`LastModifiedBy` = ?
                WHERE
                    a.`MemberID` IN ({$impMemberID})";
        $p = array(
            $CoopID,
            $_SESSION['userid'],
            $impMemberID
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
        }
        return $results;
    }

    public function deleteCoopMember($MemberID,$CoopID){
        $this->db->trans_begin();

        $sql="UPDATE ktv_members a SET
                    a.CoopID = null
                    , a.inCoop = '0'
                    , a.`DateUpdated` = NOW()
                    , a.`LastModifiedBy` = ?
                WHERE
                    a.MemberID = ?
                LIMIT 1";
        $p = array(
            $_SESSION['userid'],
            $MemberID
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data deleted";
        }
        return $results;
    }

    public function readSettingCoopDatas($UserId, $start, $limit)
    {
        $sql = "select %s
                from sys_user a
                left join ktv_cooperative_staff b ON a.UserId = b.UserId
                join ktv_cooperatives c ON b.CoopID = c.CoopID
                LEFT JOIN ktv_village kv ON kv.VillageID = c.VillageID
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                LEFT JOIN ktv_district d ON d.DistrictID = ksd.DistrictID
                where TRUE";

//         $sql = "select %s
//                from sys_user a
//                left join ktv_cooperative_staff b ON a.UserId = b.UserId
//                join ktv_cooperatives c ON b.CoopID = c.CoopID
//                LEFT JOIN ktv_district d ON d.DistrictID=substr(c.VillageID,1,4)
//                where a.UserId=?";68
        $add=null;
        $query = $this->db->query(sprintf($sql, 'c.CoopID,CoopName,c.Phone,c.Email,c.AutoJournal,TahunTerbentuk,LimitTransaction,c.Status,District',
            $add.' ', 'LIMIT ?,?'),
            array($UserId, (int)$start, (int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(distinct c.CoopID) as total', $add, ''), array($UserId));
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readSettingCoopData($UserId, $start=0, $limit=1)
    {
        $sql = "select %s
                from sys_user a
                join ktv_cooperative_staff b ON a.UserId = b.UserId
                join ktv_cooperatives c ON b.CoopID = c.CoopID
                LEFT JOIN ktv_village d ON c.VillageID = d.VillageID
                LEFT JOIN ktv_subdistrict e ON d.SubDistrictID = e.SubDistrictID
                LEFT JOIN ktv_district f ON f.DistrictID = e.DistrictID
                LEFT JOIN ktv_province i ON f.ProvinceID = i.ProvinceID
                where a.UserId=?";

        $add=null;
        $query = $this->db->query(sprintf($sql, 'c.CoopID,CoopName,c.Phone,c.Email,TahunTerbentuk,c.AutoJournal,c.Status,District,c.VillageID,c.Address,c.Latitude,c.Longitude,e.SubDistrictID,f.DistrictID,i.ProvinceID,e.SubDistrict',
            $add.' ', 'LIMIT ?,?'),
            array($UserId, (int)$start, (int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(distinct c.CoopID) as total', $add, ''), array($UserId));
//        echo $this->db->last_query();
//        exit;
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function readData($CoopID)
    {
        $sql = "SELECT
            co.CoopID AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopID\"
            , co.CoopCode AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopCode\"
            , co.CoopName AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopName\"
            , co.DateCollection AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-DateCollection\"
            , co.LegalStatus AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-LegalStatus\"
            , co.Phone AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Phone\"
            , co.Email AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Email\"
            , co.YearEstablished AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-YearEstablished\"
            , co.VillageID AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-VillageID\"
            , co.VillageID
            , d.SubDistrictID
            , f.DistrictID
            , e.ProvinceID
            , co.ZipCode AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-ZipCode\"
            , co.Address AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Address\"
            , IFNULL(ST_Latitude(co.LatLong), co.Latitude) AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Latitude\"
            , IFNULL(ST_Longitude(co.LatLong), co.Longitude) AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Longitude\"
            , co.Website AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Website\"
            , co.Fax AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Fax\"
            , co.Linked AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Linked\"
            , co.Certificate AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Certificate\"
            , co.IndicateNumber AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-IndicateNumber\"
            , co.EstCertVolPurchased AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolPurchased\"
            , co.EstCertVolProcess AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolProcess\"
            , co.PermanentWorkers AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-PermanentWorkers\"
            , co.TemporaryWorkers AS \"Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-TemporaryWorkers\"
            FROM
                ktv_cooperatives co                
            LEFT JOIN 
                ktv_village c ON co.VillageID = c.VillageID
            LEFT JOIN 
                ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN 
                ktv_district f ON d.DistrictID = f.DistrictID
            LEFT JOIN 
                ktv_province e ON f.ProvinceID = e.ProvinceID
            WHERE
                co.CoopID = ?";
        $query = $this->db->query($sql, array($CoopID));
        $result = $query->result_array();

        $return['success'] = true;
        $return['data'] = $result[0];

        return $return;
    }

    public function getCoopMemberPanelGrid($CoopID,$start,$limit,$sortingField, $sortingDir){
        //generate filter hak akses (begin)
        if ($_SESSION['is_admin'] == "1") {
            $sqlHakAkses['join'] = "";
            $sqlHakAkses['where'] = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";

            //cek ktv_access_partner_member
            $sqlHakAkses['join'] = " INNER JOIN ktv_access_partner_member acc_pm ON a.MemberID = acc_pm.apmMemberID AND acc_pm.apmPartnerID = '{$_SESSION['PartnerID']}' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAkses['where'] = " AND kd.DistrictID IN (" . $_SESSION['daerah_access'] . ")";
            $sqlHakAkses['join'] = "";
        }
        //generate filter hak akses (end)

        if ($sortingField == "")
            $sortingField = 'MemberDisplayID';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                '{$CoopID}' AS CoopID
                , a.`MemberDisplayID`
                , a.`MemberName`
                , vil.`Village`
                , a.MemberID
                , s.UserRealName Enumerator
            FROM
                ktv_members a
                INNER JOIN ktv_member_role b ON a.`MemberID` = b.`MemberID` AND b.`MRoleID` = '1'
                LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = vil.SubDistrictID
                LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                LEFT JOIN sys_user s ON s.UserId = a.CreatedBy
                {$sqlHakAkses['join']}
            WHERE
                a.`StatusCode` = 'active'
                AND a.`CoopID` = ?
                {$sqlHakAkses['where']}
            ORDER BY $sortingField $sortingDir
            LIMIT ?,?
            ";
        $p = array(
            $CoopID, (int) $start, (int) $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function readStaffs($id)
    {
        $sql = "SELECT StaffID,IF(kcs.FarmerID,'Farmer','Non Farmer') Status,IF(kcs.FarmerID,concat('[',kcs.FarmerID,'] ',FarmerName),PersonNm)FarmerID,
               Position,Phone,kcs.Email,IF(kcf.FarmerID is not null,concat('[',kcs.FarmerID,'] ',FarmerName),PersonNm) StaffName,StaffBirthday,IF(StaffGender='1','Laki-laki',IF(StaffGender='2','Perempuan','')) StaffGender
               ,StaffStatus,PaymentStatus
            from ktv_cooperative_staff kcs
            LEFT JOIN ktv_persons p ON p.PersonID = kcs.PersonID
            LEFT JOIN ktv_farmer kcf ON kcs.FarmerID=kcf.FarmerID
            WHERE CoopID=?  AND kcs.StatusCode != 'nullified'
            ORDER BY StaffName";

        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readBoardData($id=null)
    {
        $sql = "
            select BoardID,Status,BoardName,
               Position,Phone,Email,BoardBirthday,IF(BoardGender='1','Laki-laki',IF(BoardGender='2','Perempuan','')) BoardGender
               ,BoardStatus
            from ktv_cooperative_board kcs
            WHERE CoopID=?
            ORDER BY BoardName";

        $query = $this->db->query($sql, array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function readStaffsFarmer($district, $name)
    {
        $sql = "
            select FarmerID id, FarmerName name,HandPhone handphone, Birthdate birthdate,Gender kelamin
            from ktv_farmer f
            left join ktv_village v on v.`VillageID` = f.`VillageID`
            left join ktv_subdistrict sd on sd.`SubDistrictID` = v.`SubDistrictID`
            left join ktv_district d on d.`DistrictID` = sd.`DistrictID`
            WHERE (FarmerID=? OR FarmerName like ?) and (VillageID like ? OR District like ?)
            ORDER BY FarmerName";
        $query = $this->db->query($sql, array($name, '%'.$name.'%', $district.'%', $district));
        $result['data'] = $query->result_array();
        return $result;
    }
    public function createDataStaff($CoopID, $FarmerID, $StaffName, $Position, $Status, $Phone, $Email, $StaffBirthday, $StaffGender, $CreatedBy, $StaffStatus=null, $PaymentStatus=null)
    {
        $q = $this->db->get_where('sys_user', array('UserName'=>$Email));
        if ($q->num_rows()>0) {
            $results['success'] = false;
            $results['message'] = "duplicated record.";
            return $results;
        }

        //user
         $this->db->trans_start();
        $sql_user = "
             INSERT INTO sys_user(UserRealName,UserName,UserActive)
             VALUES (?,?,?)";
        $query = $this->db->query($sql_user, array($StaffName, $Email, 'No'));
        $user = $this->db->insert_id();
        $sql_user_group = "INSERT INTO sys_user_group(UserGroupUserId,UserGroupGroupId,UserGroupIsDefault)
             values (?,?,'1')";
        $query = $this->db->query($sql_user_group, array($user, null));
         //end user

        $sql = "
            INSERT INTO ktv_cooperative_staff(CoopID, FarmerID, StaffName, Position, Status, Phone, Email,
               StaffBirthday, StaffGender, UserId,CreatedBy, DateCreated,StaffStatus,PaymentStatus)
            VALUES (?,?,?,?,?,?,?,   ?,?,?,?,now(),?,?)";
        if ($StaffGender=='Laki-laki') {
            $StaffGender = 1;
        } elseif ($StaffGender=='Perempuan') {
            $StaffGender = 1;
        }
        $query = $this->db->query($sql, array($CoopID, $FarmerID, $StaffName, $Position, $Status, $Phone, $Email,
            $StaffBirthday, $StaffGender, $user, $CreatedBy, $StaffStatus, $PaymentStatus));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function createDataBoard($data)
    {
        $data = array(
                'CoopID'=>$data['CoopID'],
                'BoardName'=>$data['BoardName'],
                'Position'=>$data['Position'],
                'Status'=>$data['Status'],
                'Phone'=>$data['Phone'],
                'Email'=>$data['Email'],
                'BoardBirthday'=>str_replace('T00:00:00', '', $data['BoardBirthday']),
                'BoardGender'=>$data['BoardGender'],
                'BoardStatus'=>$data['BoardStatus'],
                // 'CreatedBy'=>$data['CreatedBy'],
                'DateCreated'=>date('Y-m-d H:m:s')
            );
        $this->db->insert('ktv_cooperative_board', $data);

        if ($this->db->affected_rows()>0) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateDataBoard($data)
    {
        $d = array(
                'CoopID'=>$data['CoopID'],
                'BoardName'=>$data['BoardName'],
                'Position'=>$data['Position'],
                'Status'=>$data['Status'],
                'Phone'=>$data['Phone'],
                'Email'=>$data['Email'],
                'BoardBirthday'=>str_replace('T00:00:00', '', $data['BoardBirthday']),
                'BoardGender'=>$data['BoardGender'],
                'BoardStatus'=>$data['BoardStatus'],
                // 'CreatedBy'=>$data['CreatedBy'],
                'DateUpdated'=>date('Y-m-d H:m:s')
            );
        $this->db->where('BoardID', $data['BoardID']);
        $this->db->update('ktv_cooperative_board', $d);

        if ($this->db->affected_rows()>0) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to updating record";
        }
        return $results;
    }

    public function updateDataStaff($CoopID, $FarmerID, $StaffName, $Position, $Status, $Phone, $Email,
            $StaffBirthday, $StaffGender, $LastModifiedBy, $id, $StaffStatus=null, $PaymentStatus=null)
    {
        if ($StaffGender=='Laki-laki') {
            $StaffGender = 1;
        } elseif ($StaffGender=='Perempuan') {
            $StaffGender = 1;
        }
        $sql = "
            UPDATE ktv_cooperative_staff
            SET CoopID=?, FarmerID=?, StaffName=?, Position=?, Status=?, Phone=?, Email=?,
               StaffBirthday=?, StaffGender=?, LastModifiedBy=?, DateUpdated=now(),StaffStatus=?,PaymentStatus=?
            WHERE StaffID=?";
        $query = $this->db->query($sql, array($CoopID, $FarmerID, $StaffName, $Position, $Status, $Phone, $Email,
            $StaffBirthday, $StaffGender, $LastModifiedBy, $StaffStatus, $PaymentStatus, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function deleteDataStaff($id)
    {
        //$sql = "DELETE FROM ktv_cooperative_staff WHERE StaffID=?";
         $sql="UPDATE ktv_cooperative_staff SET StatusCode = 'nullified', LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW()  WHERE StaffID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    public function createData($CoopCode, $CoopName, $Phone, $Email, $TahunTerbentuk, $Status, $VillageID, $Address, $Photo,
      $Latitude, $Longitude, $CreatedBy)
    {
        $sql = "
            INSERT INTO ktv_cooperatives(CoopCode, CoopName, Phone, Email, TahunTerbentuk, Status, VillageID, Address, Photo,
               Latitude, Longitude, CreatedBy, DateCreated)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,now())";
        $query = $this->db->query($sql, array($CoopCode, $CoopName, $Phone, $Email, $TahunTerbentuk, $Status, $VillageID,
            $Address, $Photo, $Latitude, $Longitude, $CreatedBy, date('Y-m-d H:m:s')));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    public function updateData($data, $userid)
    {
        $this->db->trans_start();
        if ($data['Photo_name']!='') {
            $sql = "UPDATE ktv_cooperatives SET Photo=?, LastModifiedBy=?, DateUpdated=? WHERE CoopID=?";
            $query = $this->db->query($sql, array($data['Photo_name'], $userid, date('Y-m-d H:m:s'), $data['CoopID']));
        }
        /*if($data['Photo_cert_name']!=''){
            $sql = "UPDATE ktv_cooperatives SET PhotoCertification=?, LastModifiedBy=?, DateUpdated=? WHERE CoopID=?";
            $query = $this->db->query($sql, array($data['Photo_cert_name'], $userid,date('Y-m-d H:m:s'),$data['CoopID']));
        }*/

        $sql = "
            UPDATE ktv_cooperatives
            SET CoopName=?, Phone=?, Email=?, TahunTerbentuk=?, Status=?, VillageID=?, Address=?,Latitude=?,Longitude=?, AutoJournal=?, LastModifiedBy=?, DateUpdated=? WHERE CoopID=?";
        $query = $this->db->query($sql, array($data['CoopName'], $data['Phone'], $data['Email'], $data['TahunTerbentuk'], $data['Status'], $data['Desa'], $data['Address'], $data['Latitude'], $data['Longitude'], $data['AutoJournal'], $userid, date('Y-m-d H:m:s'), $data['CoopID']));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function deleteData($CoopID)
    {
        /*
        $sql_staff = "
            DELETE FROM ktv_cooperative_staff WHERE CoopID=?";
        $sql = "
            DELETE FROM ktv_cooperatives WHERE CoopID=?";
        $query = $this->db->query($sql_staff, array($id));
        $query = $this->db->query($sql, array($id));
        */
         $sql="UPDATE ktv_cooperatives SET StatusCode = 'nullified', LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW() WHERE CoopID = ? LIMIT 1";
        $query = $this->db->query($sql, array($CoopID));

        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function readIcsGroup($id)
    {
        $sql = "SELECT *
                FROM ktv_cooperatives kc
                    LEFT JOIN ktv_village kv ON kv.VillageID = kc.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID=kv.VillageID
                    LEFT JOIN ktv_district ks ON ks.DistrictID=ksd.DistrictID
                    LEFT JOIN ktv_ics kic ON kic.ObjID=kc.CoopID
                WHERE CoopID=?";
        $query = $this->db->query($sql, array($id));
        $result= $query->result_array();
        return $result[0];
    }

    public function readIcsMember($icsID, $limit, $page, $start)
    {
        $limit = " LIMIT {$start},{$limit} ";

        $sql = "SELECT
                    %s
                FROM
                (
                    SELECT
			v.IcsMemberID as icsid,
			a.PrivateStaffID as id,
			a.StaffName as `name`,
			IF(a.StaffGender = '1','Pria','Wanita') as gender,
			c.District as district,
			CONCAT('') as subdistrict,
			CONCAT('private staff') as tipe
                    FROM
			ktv_private_staff a
			LEFT JOIN ktv_province b ON SUBSTR(a.Location,1,2) = b.ProvinceID
			LEFT JOIN ktv_district c ON a.Location = c.DistrictID
			LEFT JOIN ktv_ics_members v ON a.PrivateStaffID = v.FarmerID
                    WHERE
			v.IcsID = $icsID
                    UNION
                    SELECT
			w.IcsMemberID as icsid,
			d.PersonID as id,
			e.PersonNm as `name`,
			IF(e.Gender = 'm','Pria','Wanita') as gender,
			g.District as district,
			CONCAT('') as subdistrict,
			CONCAT('program staff') as tipe
                    FROM
			ktv_program_staff d
			JOIN ktv_persons e ON d.PersonID = e.PersonID
			LEFT JOIN ktv_province f ON SUBSTR(d.WorkArea,1,2) = f.ProvinceID
			LEFT JOIN ktv_district g ON d.WorkArea = g.DistrictID
			LEFT JOIN ktv_ics_members w ON d.PersonID = w.FarmerID
                    WHERE
			w.IcsID = $icsID
                    UNION
                    SELECT
			x.IcsMemberID as icsid,
			h.FarmerID as id,
			h.FarmerName as `name`,
			IF(h.Gender = '1','Pria','Wanita') as gender,
			j.District as district,
			l.SubDistrict as subdistrict,
			CONCAT('farmer') as tipe
                    FROM
			ktv_farmer h
			LEFT JOIN ktv_province i ON SUBSTR(h.FarmerID,1,2) = i.ProvinceID
			LEFT JOIN ktv_district j ON SUBSTR(h.FarmerID,1,4) = j.DistrictID
			LEFT JOIN ktv_village k ON h.VillageID = k.VillageID
			LEFT JOIN ktv_subdistrict l ON k.SubDistrictID = l.SubDistrictID
			LEFT JOIN ktv_ics_members x ON h.FarmerID = x.FarmerID
                    WHERE
			x.IcsID = $icsID AND x.StatusCode != 'nullified'
                    ) z
                    %s ";
        $query = $this->db->query(sprintf($sql, "z.icsid as IcsMemberID,
                    concat('[',z.id,'] ',z.`name`) as FarmerName,
                    z.id as FarmerID,
                    z.gender as Gender,
                    z.district as District,
                    z.subdistrict as SubDistrict,
                    z.tipe", $limit));
        $result['data'] = $query->result_array();
        $query2 = $this->db->query(sprintf($sql, "count(*) as total", ""));
        $result['total']= $query2->row()->total;
        return $result;
    }

    public function createIcs($type, $objId, $uid)
    {
        $sql = "INSERT INTO ktv_ics (ObjType,ObjID,DateCreated,CreatedBy,DateUpdated,LastModifiedBy)
                VALUES (?,?,now(),?,now(),?)";
        $query = $this->db->query($sql, array($type, $objId, $uid, $uid));
        if ($query) {
            $results['id'] = $this->db->insert_id();
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateIcs($icsId, $type, $objId, $uid)
    {
        $sql = "UPDATE ktv_ics
                SET ObjType=?,ObjID=?,DateUpdated=now(),LastModifiedBy=?
                WHERE IcsID=?";
        $query = $this->db->query($sql, array($type, $objId, $uid, $icsId));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function searchFarmer($district, $province, $name)
    {
        $sql = "SELECT
                    a.PrivateStaffID as id,
                    a.StaffName as `name`,
                    IF(a.StaffGender = '1','Pria','Wanita') as gender,
                    c.District as district,
                    CONCAT('') as subdistrict,
                    CONCAT('private staff') as tipe
		FROM
                    ktv_private_staff a
                    LEFT JOIN ktv_province b ON SUBSTR(a.Location,1,2) = b.ProvinceID
                    LEFT JOIN ktv_district c ON a.Location = c.DistrictID
		WHERE
                    a.StaffName like ?
		UNION
		SELECT
                    d.PersonID as id,
                    e.PersonNm as `name`,
                    IF(e.Gender = 'm','Pria','Wanita') as gender,
                    g.District as district,
                    CONCAT('') as subdistrict,
                    CONCAT('program staff') as tipe
		FROM
                    ktv_program_staff d
                    JOIN ktv_persons e ON d.PersonID = e.PersonID
                    LEFT JOIN ktv_province f ON SUBSTR(d.WorkArea,1,2) = f.ProvinceID
                    LEFT JOIN ktv_district g ON d.WorkArea = g.DistrictID
		WHERE
                    e.PersonNm like ?
		UNION
		SELECT
                    h.FarmerID as id,
                    h.FarmerName as `name`,
                    IF(h.Gender = '1','Pria','Wanita') as gender,
                    j.District as district,
                    l.SubDistrict as subdistrict,
                    CONCAT('farmer') as tipe
		FROM
                    ktv_farmer h
                    LEFT JOIN ktv_province i ON SUBSTR(h.FarmerID,1,2) = i.ProvinceID
                    LEFT JOIN ktv_district j ON SUBSTR(h.FarmerID,1,4) = j.DistrictID
                    LEFT JOIN ktv_village k ON h.VillageID = k.VillageID
                    LEFT JOIN ktv_subdistrict l ON k.SubDistrictID = l.SubDistrictID
		WHERE
                    (h.FarmerName like ?) AND
                    (i.ProvinceID = ?)";
        /*
        $sql = "SELECT
                    a.FarmerID as id,
                    a.FarmerName as `name`,
                    IF(a.Gender = '1','Pria','Wanita') as gender,
                    b.district,
                    d.SubDistrict as subdistrict
                FROM
                    ktv_farmer a
                    LEFT JOIN ktv_district b on b.DistrictID=substr(a.VillageID,1,4)
                    LEFT JOIN ktv_village c ON a.VillageID = c.VillageID
                    LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                    LEFT JOIN ktv_province e ON substr(a.FarmerID,1,2) = e.ProvinceID
                WHERE
                    (a.FarmerID=? OR a.FarmerName like ?)
                    and (e.ProvinceID = ?)
                ORDER BY a.FarmerName";
         *
         */
        $query = $this->db->query($sql, array('%'.$name.'%', '%'.$name.'%', '%'.$name.'%', $province));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function addIcsMember($icsID, $farmerID, $uid)
    {
        $sql = "INSERT INTO ktv_ics_members (IcsID,FarmerID,DateCreated,CreatedBy,DateUpdated,LastModifiedBy)
                VALUES (?,?,now(),?,now(),?)";
        $query = $this->db->query($sql, array($icsID, $farmerID, $uid, $uid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function deleteIcsMember($id)
    {
        //$sql = "DELETE FROM ktv_ics_members WHERE IcsMemberID=?";
         $sql="UPDATE ktv_ics_members SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."', DateUpdated = NOW() WHERE IcsMemberID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function readDocument($key=null)
    {
        $basepath = 'http://'.$_SERVER['HTTP_HOST'].'/api'.str_replace('.', '', $this->config->item('coop_document')).'/';

        if ($key!=null) {
            $wer = "WHERE (a.FileName like '%".$key."%' OR a.FileLabel like '%".$key."%')";
        } else {
            $wer = null;
        }

        $query = $this->db->query("select a.DocCoopID,a.FileLabel,a.FileName,a.FileSize,a.FileType,DateCreated,FileCategory,
            concat('$basepath',FileName,a.FileType) path
            from coop_documents a
            $wer");



        $result['data'] = $query->result_array();
        $result['total'] = $query->num_rows();
        return $result;
    }

    public function saveUploadDocCoop($label, $FileCategory, $datafile, $userid)
    {
        // return $datafile;
        $d = array(
                // `CoopID` int(11) DEFAULT NULL,
                'FileLabel' => $label,
                'FileName' => $datafile['raw_name'],
                'FileSize' => $datafile['file_size'],
                'FileType' => $datafile['file_ext'],
                'DateCreated' => date('Y-m-d H:m:s'),
                'FileCategory'=>$FileCategory,
                'CreatedBy' => $userid,
                'CoopID' => getCoopID()
                // `UpdatedBy` int(11) DEFAULT NULL,
                // `UpdatedDate` datetime DEFAULT NULL,
         );
        // return $d;

        $query = $this->db->insert('coop_documents', $d);
        // return $this->db->last_query();

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Success uploading file";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed uploading file";
        }
        return $results;
    }

    public function createLimitTrans($data, $userid)
    {
        $d = array(
                'Position' => $data['Position'],
                'MinTransaction' => $data['MinAmount'],
                'MaxTransaction' => $data['MaxAmount'],
                'Deposit' => $data['deposit'] == 'on' ? 1 : 0,
                'Withdrawal' => $data['withdrawal'] == 'on' ? 1 : 0,
                'CreatedBy' => $userid,
                'CoopID' => getCoopID(),
                'CreatedDate' => date('Y-m-d H:m:s'),
            );
        $this->db->insert('coop_approval', $d);

        $id = $this->db->insert_id();

        $staffArr = explode(',', $data['StaffApproval']);
        foreach ($staffArr as $key => $value) {
            $dstaff = array(
                'ApprovalID'=>$id,
                'CoopID' => getCoopID(),
                'StaffID'=>$value
            );
            $this->db->insert('coop_approval_staff', $dstaff);
        }

        if ($this->db->affected_rows()>0) {
            $results['success'] = true;
            $results['message'] = "Success inserting data";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed inserting data";
        }
        return $results;
    }

    public function updateLimitTrans($data, $userid)
    {
        $d = array(
                'Position' => $data['Position'],
                'MinTransaction' => $data['MinAmount'],
                'MaxTransaction' => $data['MaxAmount'],
                'Deposit' => $data['deposit'] == 'on' ? 1 : 0,
                'Withdrawal' => $data['withdrawal'] == 'on' ? 1 : 0,
                'CreatedBy' => $userid,
                'CreatedDate' => date('Y-m-d H:m:s'),
            );
        $this->db->where('ApprovalID', $data['ApprovalID']);
        $this->db->update('coop_approval', $d);


        //insert dari ulang
        $this->db->where('ApprovalID', $data['ApprovalID']);
        $this->db->delete('coop_approval_staff');


        $staffArr = explode(',', $data['StaffApproval']);
        foreach ($staffArr as $key => $value) {
            $dstaff = array(
                'ApprovalID'=>$data['ApprovalID'],
                'StaffID'=>$value
            );
            $this->db->insert('coop_approval_staff', $dstaff);
        }

        if ($this->db->affected_rows()>0) {
            $results['success'] = true;
            $results['message'] = "Success updating data";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed updating data";
        }
        return $results;
    }

    public function readLimitTrans($id=null, $start=null, $limit=null)
    {
        if ($id!=null) {
            $wer = "WHERE a.ApprovalID=$id";
        } else {
            $wer = null;
        }

        $query = $this->db->query("select a.ApprovalID,a.Position,a.MinTransaction,a.MaxTransaction,Deposit,Withdrawal
            from coop_approval a
            $wer");

        $result['data'] = $query->result_array();
        $result['total'] = $query->num_rows();
        $staff = null;

        if ($id!=null) {
            $qstaff = $this->db->get_where('coop_approval_staff', array('ApprovalID'=>$id));
            $c = $qstaff->num_rows();
            $i=1;
            $staff='';
            foreach ($qstaff->result() as $r) {
                $staff.=$r->StaffID;
                if ($i!=$c) {
                    $staff.=',';
                }
                $i++;
            }
        }
        $result['staff'] = $staff;
        return $result;
    }

    public function readCustomer($CoopID, $Type, $Name)
    {
        $wer = null;

        if ($Type==1) {
            //member
            $sql = "select a.memberID as id,a.name,a.address
            from coop_member a
            join coop_member_type b ON a.typeID = b.typeID
            where b.coopID=$CoopID ";

            if ($Name!='') {
                $wer=" and a.name like '%$Name%'";
            }
        } else {
            //non member
            $sql = "select a.CustomerId as id, a.Name as name,a.Address as address
                    from ktv_customer a
                    where MemberID is null";
            if ($Name!='') {
                $wer=" and Name like '%$Name%'";
            }
        }

        $query = $this->db->query($sql.$wer);

        $result['data'] = $query->result_array();
        $result['total'] = $query->num_rows();
        return $result;
    }

    public function readCooperative($province=null, $district=null, $subdistrict=null)
    {
        $this->db->select('c.CoopID AS id, c.CoopName AS name');
        $this->db->from('ktv_cooperatives c');
        $this->db->join('ktv_village v', 'v.VillageID = c.VillageID');
        $this->db->join('ktv_subdistrict sd', 'sd.SubDistrictID = v.SubDistrictID');
        $this->db->join('ktv_district d', 'd.DistrictID = sd.DistrictID');
        $this->db->join('ktv_province p', 'p.ProvinceID = d.ProvinceID');
        if (!empty($province)) {
            $this->db->where('p.ProvinceID', $province, false);
        }
        if (!empty($district)) {
            $this->db->where('d.DistrictID', $district, false);
        }
        if (!empty($subdistrict)) {
            $this->db->where('sd.SubDistrictID', $subdistrict, false);
        }

        $query = $this->db->get();
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }

    public function readClonalGarden($ObjID, $ObjType, $ClonalID, $GardenNr)
    {
        $sql = "SELECT *, 45Nr AS CG45Nr,`45` AS CG45 FROM ktv_clonal_garden WHERE ObjID=? AND ObjType=? AND ClonalID=? AND GardenNr=?";
        $query = $this->db->query($sql, array($ObjID, $ObjType, $ClonalID, $GardenNr));
        $result= $query->result_array();
        return $result[0];
    }

    public function readClonalGardenArea($ObjType, $ObjID, $GardenNr, $ClonalID)
    {
        $sql = "SELECT Area,Latitude,Longitude FROM ktv_clonal_garden WHERE ObjType=? AND ObjID=? AND ClonalID=? AND GardenNr=?";
        $query = $this->db->query($sql, array($ObjType, $ObjID, $ClonalID, $GardenNr));
        $result= $query->result_array();
        return $result[0];
    }

    public function createClonalGarden($post)
    {
        if ($post['ObjType']!='') {
            $ObjType = $post['ObjType'];
            $ObjID = $post['ObjID'];
            $GardenNr = $post['GardenNr'];
        } elseif ($post['FCGObjType']!='') {
            $ObjType = $post['FCGObjType'];
            $ObjID = $post['FCGObjID'];
            $GardenNr = $post['FCGGardenNr'];
        }
        $sql_check = "SELECT * FROM ktv_clonal_garden WHERE ObjType=? AND ObjID=? AND GardenNr=? AND StatusCode!='nullified'";
        $query = $this->db->query($sql_check, array($ObjType, $ObjID, $GardenNr));
        if ($query->num_rows() > 0) {
            $results['success'] = 'gagal';
            $results['message'] = "Error! GardenNr duplicated.";
            return $results;
        } else {
            foreach ($post as $k=>$v) {
                $k = str_replace("FCG", "", $k);
                if ($k=='CG45') {
                    $k = '45';
                }
                if ($k=='CG45Nr') {
                    $k = '45Nr';
                }
                if ($k!='GardenNr_default') {
                    $insert[$k] = $v;
                }
            }
            $insert['StatusCode'] = 'active';
            $insert['DateCreated'] = date('Y-m-d H:i:s');
            $insert['CreatedBy'] = $_SESSION['userid'];
            $this->db->insert('ktv_clonal_garden', $insert);
            if ($this->db->affected_rows() > 0) {
                $results['success'] = 'sukses';
                $results['message'] = "record created.";
                $results['id'] = $this->db->insert_id();
            } else {
                $results['success'] = 'gagal';
                $results['message'] = "Failed to create record";
            }
            return $results;
        }
    }

    public function updateClonalGarden($put)
    {
        if ($put['GardenNr_default']!='' && $put['GardenNr']!=$put['GardenNr_default']) {
            $sql_check = "SELECT * FROM ktv_clonal_garden WHERE ObjType=? AND ObjID=? AND ClonalID=? AND GardenNr=? AND StatusCode!='nullified'";
            $query = $this->db->query($sql_check, array($put['ObjType'], $put['ObjID'], $put['ClonalID'], $put['GardenNr']));
            if ($query->num_rows() > 0) {
                $results['success'] = 'duplicated';
                $results['message'] = "Error! GardenNr duplicated.";
                return $results;
            }
        }

        foreach ($put as $k=>$v) {
            $k = str_replace("FCG", "", $k);
            if ($k=='CG45') {
                $k = '45';
            }
            if ($k=='CG45Nr') {
                $k = '45Nr';
            }
            if ($k!='GardenNr_default') {
                $update[$k] = $v;
            }
        }
        $insert['DateUpdated'] = date('Y-m-d H:i:s');
        $insert['LastModifiedBy'] = $_SESSION['userid'];
        $this->db->where('ClonalID', $update['ClonalID']);
        $query = $this->db->update('ktv_clonal_garden', $update);
        if ($query) {
            $results['success'] = 'sukses';
            $results['message'] = "record updated.";
            $results['id'] = $update['ClonalID'];
        } else {
            $results['success'] = 'gagal';
            $results['message'] = "Error. Please reload page and try again.";
        }
        return $results;
    }

    public function create_json_sync($coopID)
    {
        //untuk kopreasi lokal
    }



    public function checkSync($coopID)
    {
        //access : local

        $totalFarmer = 0;
        $totalPurchase = 0;
        $totalSale = 0;
        $totalSupplier = 0;
        $totalJurnal = 0;
        $totalSupplier = 0;
        $totalInventory = 0;
        $totalSaving = 0;
        $totalTransaction = 0;
        $totalMember = 0;
        $totalApproval = 0;
        $totalCoa = 0;

        //start coa sync
        //$rSCoa = $this->StartSyncCoa($coopID);
        // var_dump($rSCoa);
        //$totalCoa = $rSCoa['num_rows'];

        $ceks = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/check_data_sync', array('CoopID'=>$coopID));

        // return array('status'=>true,'data'=>'null');
        if ($ceks) {
            $c = json_decode($ceks);
            if (isset($c->farmer)) {
                if ($c->farmer === true) {
                    //farmer

                  $result = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/get_data_sync', array('CoopID'=>$coopID, 'dataType'=>'farmer'));
                    $data = json_decode($result);


                      //post data : local -> server


                      //update data : server -> local

                      /*
                          data example

                          stdClass Object
                              (
                                  [data] => Array
                                      (
                                          [0] => stdClass Object
                                              (
                                                  [FarmerID] => 110400001
                                                  [OldFarmerID] => 1018
                                                  [CPGid] => 11040017
                                              )

                                      )

                              )
                      */
                      $farmerIDArr = array();
                    $totalFarmer = 0;
                    foreach ($data->data as $key => $value) {
                        // echo $value->FarmerID;
                          $data = array(
                                  'OldFarmerID'=>$value->OldFarmerID,
                                  'CPGid'=>$value->CPGid,
                                  'FarmerName'=>$value->FarmerName,
                                  'DateCollection'=>$value->DateCollection,
                                  'Gender'=>$value->Gender,
                                  'VillageID'=>$value->VillageID,
                                  'Address'=>$value->Address,
                                  'HandPhone'=>$value->HandPhone,
                                  'MaritalStatus'=>$value->MaritalStatus,
                                  'Birthdate'=>$value->Birthdate,
                                  'Education'=>$value->Education,
                                  'Photo'=>$value->Photo,
                                  'Photo_base64'=>$value->Photo_base64,
                                  'Latitude'=>$value->Latitude,
                                  'Longitude'=>$value->Longitude,
                                  'KeyFarmer'=>$value->KeyFarmer,
                                  'DemoPlot'=>$value->DemoPlot,
                                  'OtherTraining'=>$value->OtherTraining,
                                  'CPGmembership'=>$value->CPGmembership,
                                  'OtherTrainingSiapa'=>$value->OtherTrainingSiapa,
                                  'OtherTrainingTahun'=>$value->OtherTrainingTahun,
                                  'OtherTrainingLama'=>$value->OtherTrainingLama,
                                  'DemoPlotLama'=>$value->DemoPlotLama,
                                  'DemoPlotRehab'=>$value->DemoPlotRehab,
                                  'FarmerGroupFunctionsID'=>$value->FarmerGroupFunctionsID,
                                  'DateCreated'=>$value->DateCreated,
                                  'CreatedBy'=>$value->CreatedBy,
                                  'DateUpdated'=>$value->DateUpdated,
                                  'LastModifiedBy'=>$value->LastModifiedBy,
                                  'StatusCode'=>$value->StatusCode,
                                  'DeleteReason'=>$value->DeleteReason,
                                  'isValid'=>$value->isValid,
                                  'DateCreated'=>$value->DateCreated,
                                  'isValidPostHarvest'=>$value->isValidPostHarvest,
                                  'isValidNutrition'=>$value->isValidNutrition,
                                  'isValidPPIScore'=>$value->isValidPPIScore,
                                  'ApprovedByME'=>$value->ApprovedByME,
                                  'ApprovedByGO'=>$value->ApprovedByGO,
                                  'ApprovedByDC'=>$value->ApprovedByDC,
                                  'CommentValid'=>$value->CommentValid,
                                  'LahanKakao'=>$value->LahanKakao,
                                  'LahanProduksiLain'=>$value->LahanProduksiLain,
                                  'TotalLahan'=>$value->TotalLahan,
                                  'KebunKakao'=>$value->KebunKakao,
                                  'Elevation'=>$value->Elevation,
                                  'LahanKosong'=>$value->LahanKosong,
                                  'Muge'=>$value->Muge,
                                  'ActiveMemberCooperation'=>$value->ActiveMemberCooperation,
                                  'DateSurvey'=>$value->DateSurvey,
                                  'DateSynced'=>$value->DateSynced,
                                  'StatusFarmer'=>$value->StatusFarmer,
                                  'DeceasedStatus'=>$value->DeceasedStatus,
                                  'FamilyMemberID'=>$value->FamilyMemberID,
                                  'MovedLeftArea'=>$value->MovedLeftArea,
                                  'SwitchOtherCrop'=>$value->SwitchOtherCrop,
                                  'AccountBeneficiary'=>$value->AccountBeneficiary,
                                  'BankName'=>$value->BankName,
                                  'BankBranch'=>$value->BankBranch,
                                  'AccountNumber'=>$value->AccountNumber,
                                  'LearningContractStatus'=>$value->LearningContractStatus,
                                  'LearningContractSign'=>$value->LearningContractSign,
                                  'LearningContractFile'=>$value->LearningContractFile,
                                  'LearningContractDate'=>$value->LearningContractDate,
                                  'DateSync'=>$value->DateSync,
                                  'isTrained'=>$value->isTrained
                              );

                        $this->db->trans_begin();

                        $qcek = $this->db->get_where('ktv_farmer', array('FarmerID'=>$value->FarmerID));
                        if ($qcek->num_rows()>0) {
                            $this->db->where('FarmerID', $value->FarmerID);
                            $this->db->update('ktv_farmer', $data);
                        } else {
                            $this->db->insert('ktv_farmer', $data);
                        }

                        if ($this->db->trans_status() === false) {
                            $this->db->trans_rollback();
                        } else {
                            $this->db->trans_commit();
                            $farmerIDArr[] = $value->FarmerID;
                            $totalFarmer++;

                                   //send feedback if succeed
                                   $r = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/feedback_sync', array('Type'=>'farmer', 'CoopID'=>$coopID, 'FarmerID'=>$value->FarmerID), 'POST');
                                   // var_dump($r);
                        }
                    } //end foreach
                }
            }
        }

            //MEMBER TYPE
                $totalMemberType = $this->startSyncMemberType($coopID);
            //END MEMBER TYPE

            //SAVING TYPE
                $totalSavingType = $this->startSyncSavingType($coopID);
            //END SAVING TYPE

            //JURNAL
                $totalMember = $this->startSyncMember($coopID);
            //END JURNAL

            //JURNAL
                $totalJurnal = $this->startSyncJournal($coopID);
            //END JURNAL

            //SUPPLIER
               $totalSupplier = $this->startSyncSupplier($coopID);
            //END SUPPLIER

            //INVENTORY
                $totalInventory = $this->startSyncInventory($coopID);
            //END INVENTORY

            ////PURCHASE////
                $totalPurchase = $this->startSyncPurchase($coopID);
            ////END PURCHASE////

             //SALE////
                $totalSale = $this->startSyncSale($coopID);
            ////END PURCHASE////

            //SAVING
                $totalSaving = $this->startSyncSaving($coopID);
            //END SAVING

            //TRANSACTION
                $totalTransaction = $this->startSyncTransaction($coopID);
            //END TRANSACTION

            //approval staff
                // $totalApproval = $this->startSyncApproval($coopID);



        return array('status'=>true,'totalCoa'=>$totalCoa,'totalMember'=>$totalMember, 'totalFarmer'=>$totalFarmer,'totalSupplier'=>$totalSupplier, 'totalJurnal'=>$totalJurnal,'totalPurchase'=>$totalPurchase,'totalSale'=>$totalSale,'totalSaving'=>$totalSaving,'totalTransaction'=>$totalTransaction,'totalInventory'=>$totalInventory,'totalApproval'=>$totalApproval);
    }

    public function save_sync_act()
    {
        $d = array(
            );
        $this->db->insert('coop_sync');
    }

    public function checkSyncDataFarmer($CoopID)
    {
        $q = $this->db->get_where('coop_sync_farmer', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        if ($q->num_rows()>0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkSyncCoop($CoopID)
    {
        //cek koperasi apa sudah ada di server live
        $q = $this->db->get_where('ktv_cooperatives', array('CoopID'=>$CoopID));
        if ($q->num_rows()>0) {
            return false;
        } else {
            return true;
        }
    }

    public function checkSyncCoa($coopID)
    {
        //cek coa/akun perkiraan apa sudah ada di server live
        $q = $this->db->get_where('coop_sync_coa', array('CoopID'=>$CoopID, 'Source'=>2));
        if ($q->num_rows()>0) {
            return false;
        } else {
            return true;
        }
    }

    public function GetFarmerDataSync($coopID, $dataType)
    {
        if ($dataType=='farmer') {
            $q = $this->db->query("select b.*
                                    from coop_sync_farmer a
                                    join ktv_farmer b ON a.FarmerID = b.FarmerID
                                    where SyncedDate is null");
            return $q->result_array();
        }
    }

    public function StartSyncCoa($CoopID)
    {
        $r = true;
        $i=0;
        // $this->db->limit(1);
        $qSyncCoa = $this->db->get_where('accounting_coa', array('CoopID'=>$CoopID, 'SyncedDate'=>null));

        if ($qSyncCoa->num_rows()>0) {
            // post data : local -> server
            $CoaData = array();
            $i=0;
            foreach ($qSyncCoa->result() as $r) {
                $CoaData['CoaID'] = $r->CoaID;
                $CoaData['CoopID'] = $r->CoopID;
                $CoaData['CoaCode'] = $r->CoaCode;
                $CoaData['CoaCodeParent'] = $r->CoaCodeParent;
                $CoaData['CoaGroupID'] = $r->CoaGroupID;
                $CoaData['CoaGroupCode'] = $r->CoaGroupCode;
                $CoaData['CurrencyID'] = $r->CurrencyID;
                $CoaData['CoaTitle'] = $r->CoaTitle;
                $CoaData['CoaType'] = $r->CoaType;
                $CoaData['CoaRelated'] = $r->CoaRelated;
                $CoaData['CoaStatus'] = $r->CoaStatus;
                $CoaData['CoaForReceived'] = $r->CoaForReceived;
                $CoaData['CoaForSpent'] = $r->CoaForSpent;
                $CoaData['CoaForCash'] = $r->CoaForCash;
                $CoaData['CoaForNonCash'] = $r->CoaForNonCash;
                $CoaData['CoaOrder'] = $r->CoaOrder;
                $CoaData['CoaReportDisplay'] = $r->CoaReportDisplay;

                $qBalance = $this->db->get_where('accounting_coa_balance', array('CoaID'=>$r->CoaID, 'CoopID'=>$r->CoopID));

                $i=0;
                foreach ($qBalance->result() as $rBalance) {
                    $CoaData['Balance'][$i]['CoaCode'] = $rBalance->CoaCode;
                    $CoaData['Balance'][$i]['CoaBalanceAmount'] = $rBalance->CoaBalanceAmount;
                    $CoaData['Balance'][$i]['CoopID'] = $rBalance->CoopID;
                    $CoaData['Balance'][$i]['CoaID'] = $rBalance->CoaID;
                    $CoaData['Balance'][$i]['DateCreated'] = $rBalance->DateCreated;
                    $i++;
                }

                $RetCoa = $this->curl->simple_post($this->config->item('coop_sync_server').'/cooperatives/receive_coa_sync', array('CoopID'=>$CoopID, 'CoaData'=>$CoaData));
                $obj = json_decode($RetCoa);
                // echo $obj->success;
                // var_dump($RetCoa);
                // echo 'asdasdsa'.$obj->success;
                // exit;
                //set Sync field to 0 (synced)
                if (isset($obj->success)) {
                    if ($obj->success) {
                        $this->db->where(array('CoaID'=>$obj->data->CoaID, 'CoopID'=>$obj->data->CoopID));
                        $this->db->update('accounting_coa', array('SyncedDate'=>gmdate(('Y-m-d H:m:s'))));
                        $i++;
                    }
                }
            }
        } else {
            // return array('num_rows'=>'ga ada');
        }

        return array('num_rows'=>$i);
    }

    public function startSyncSavingType($CoopID)
    {
        $q = $this->db->get_where('coop_saving_type', array('SyncedDate'=>null, 'coopID'=>$CoopID));

        $totalRow = 0;
        if ($q->num_rows()>0) {
            foreach ($q->result() as $r) {
                unset($data);

                $data['savingTypeID'] = $r->savingTypeID;
                $data['savingTypeCode'] = $r->savingTypeCode;
                $data['savingTypeDefault'] = $r->savingTypeDefault;
                $data['coopID'] = $r->coopID;
                $data['CoaID'] = $r->CoaID;
                $data['savingTypeSHU'] = $r->savingTypeSHU;
                $data['savingTypeName'] = $r->savingTypeName;
                $data['savingTypeMinAmount'] = $r->savingTypeMinAmount;
                $data['savingTypeMinTrans'] = $r->savingTypeMinTrans;
                $data['savingTypeInterestRate'] = $r->savingTypeInterestRate;
                $data['savingTypeInterestCalc'] = $r->savingTypeInterestCalc;
                $data['savingTypeActiveDate'] = $r->savingTypeActiveDate;
                $data['savingTypeMonthlyFee'] = $r->savingTypeMonthlyFee;
                $data['savingTypeInterestPayment'] = $r->savingTypeInterestPayment;
                $data['savingTypeSHUProfit'] = $r->savingTypeSHUProfit;
                $data['savingTypeStatus'] = $r->savingTypeStatus;
                $data['savingRemark'] = $r->savingRemark;
                $data['CreatedBy'] = $r->CreatedBy;
                $data['CreatedDate'] = $r->CreatedDate;
                $data['UpdatedBy'] = $r->UpdatedBy;
                $data['UpdatedDate'] = $r->UpdatedDate;

                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_savingtype_sync', array('CoopID'=>$CoopID, 'data'=>$data), 'POST');
                $retValObj = ($retVal);
                // var_dump($retValObj);
                // exit;

                if ($retValObj->success) {
                    //update flag DateSynced to NULL
                    $this->db->where(array('coopID'=>$CoopID, 'savingTypeID'=>$r->savingTypeID));
                    $this->db->update('coop_saving_type', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }
            } //end foreach
        } else {
            return 0;
        }

        return $totalRow;
    }

    public function startSyncMemberType($CoopID)
    {
        $q = $this->db->get_where('coop_member_type', array('SyncedDate'=>null, 'coopID'=>$CoopID));

        $totalRow = 0;
        if ($q->num_rows()>0) {
            foreach ($q->result() as $r) {
                unset($data);

                $data['typeID'] = $r->typeID;
                $data['coopID'] = $r->coopID;
                $data['typeCode'] = $r->typeCode;
                $data['typeName'] = $r->typeName;
                $data['typeMaxProfit'] = $r->typeMaxProfit;
                $data['typeSimPokokAmount'] = $r->typeSimPokokAmount;
                $data['typeSimWajibAmount'] = $r->typeSimWajibAmount;
                $data['typeSimWajibPeriod'] = $r->typeSimWajibPeriod;
                $data['typeSimPokokPeriod'] = $r->typeSimPokokPeriod;
                $data['RegistrationFee'] = $r->RegistrationFee;
                $data['CoaRegMemberTypeID'] = $r->CoaRegMemberTypeID;
                $data['CreatedBy'] = $r->CreatedBy;
                $data['CreatedDate'] = $r->CreatedDate;
                $data['UpdatedDate'] = $r->UpdatedDate;
                //var_dump($this->config->item('coop_sync_server').'/cooperatives/receive_membertype_sync');die;
                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_membertype_sync', array('CoopID'=>$CoopID, 'data'=>$data), 'POST');
                //var_dump($retVal);die;
                //test pake file_get_contents

                $retValObj = $retVal;
                // var_dump($retValObj);
                // exit;

                if ($retValObj->success) {
                    //update flag DateSynced to NULL
                    $this->db->where(array('coopID'=>$CoopID, 'typeID'=>$r->typeID));
                    $this->db->update('coop_member_type', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }
            } //end foreach
        } else {
            return 0;
        }

        return $totalRow;
    }

    public function startSyncApproval($CoopID)
    {
        $q = $this->db->get_where('coop_approval', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        $totalRow = 0;
        if ($q->num_rows()>0) {
            foreach ($q->result() as $r) {
                $data['ApprovalID'] = $r->ApprovalID;
                $data['CoopID'] = $r->CoopID;
                $data['Position'] = $r->Position;
                $data['MinTransaction'] = $r->MinTransaction;
                $data['MaxTransaction'] = $r->MaxTransaction;
                $data['Deposit'] = $r->Deposit;
                $data['Withdrawal'] = $r->Withdrawal;
                $data['CreatedBy'] = $r->CreatedBy;
                $data['CreatedDate'] = $r->CreatedDate;
                $data['UpdatedBy'] = $r->UpdatedBy;
                $data['UpdatedDate'] = $r->UpdatedDate;
                // $data['UpdatedBy'] = $r->UpdatedBy;

                $qDetail = $this->db->get_where('coop_approval_staff', array('ApprovalID'=>$r->ApprovalID, 'CoopID'=>$r->CoopID));
                if ($qDetail->num_rows()>0) {
                    $i=0;
                    foreach ($qDetail->result() as $rD) {
                        $data['Detail'][$i]['ApprovalID'] = $rD->ApprovalID;
                        $data['Detail'][$i]['CoopID'] = $rD->CoopID;
                        $data['Detail'][$i]['StaffID'] = $rD->StaffID;
                        $i++;
                    }
                }

                $retVal = $this->curl->simple_post($this->config->item('coop_sync_server').'/cooperatives/receive_approval_sync', array('CoopID'=>$CoopID, 'data'=>$data));
                $retValObj = ($retVal);
                // var_dump($retValObj);
                // echo $retValObj->success;
                // exit;
                if ($retValObj->success) {
                    //update flag DateSynced to NULL
                    $this->db->where(array('CoopID'=>$CoopID, 'ApprovalID'=>$r->ApprovalID));
                    $this->db->update('coop_approval', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }

                unset($data);
            }
        } else {
            return 0;
        }
    }

    public function startSyncSupplier($CoopID)
    {
        $q = $this->db->get_where('ktv_supplier', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        $totalRow = 0;
        if ($q->num_rows()>0) {
            foreach ($q->result() as $r) {
                unset($data);

                $data['SupplierID'] = $r->SupplierID;
                $data['CoopID'] = $r->CoopID;
                $data['OrgType'] = $r->OrgType;
                $data['OrgID'] = $r->OrgID;
                $data['Name'] = $r->Name;
                $data['Address'] = $r->Address;
                $data['Phone'] = $r->Phone;
                $data['Email'] = $r->Email;
                $data['VillageID'] = $r->VillageID;
                $data['Note'] = $r->Note;

                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_supplier_sync', array('CoopID'=>$CoopID, 'data'=>$data), 'POST');
                $retValObj = ($retVal);
                // var_dump($retValObj);
                // exit;

                if ($retValObj->success) {
                    //update flag DateSynced to NULL
                    $this->db->where(array('CoopID'=>$CoopID, 'SupplierID'=>$r->SupplierID));
                    $this->db->update('ktv_supplier', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }

                unset($data);
            } //end foreach
        } else {
            return 0;
        }
    }

    public function startSyncPurchase($CoopID)
    {
        /*
            items : Journal, ktv_purchase, ktv_purchase_detail, ktv_inventory, ktv_inventory_stok
            access : local coop
            route : local coop -> server
        */
        $q = $this->db->get_where('ktv_purchase', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        $totalRow = 0;
        if ($q->num_rows()>0) {
            $dataPurchase = null;
            $detail = null;
            foreach ($q->result() as $r) {
                unset($dataPurchase);

                $data['PurchaseID'] = $r->PurchaseID;
                $data['CoopID'] = $r->CoopID;
                $data['OrgType'] = $r->OrgType;
                $data['OrgID'] = $r->OrgID;
                $data['JournalID'] = $r->JournalID;
                $data['Number'] = $r->Number;
                $data['SupplierID'] = $r->SupplierID;
                $data['DueDate'] = $r->DueDate;
                $data['Date'] = $r->Date;
                $data['Diskon'] = $r->Diskon;
                $data['Pajak'] = $r->Pajak;
                $data['Total'] = $r->Total;
                $data['Pembayaran'] = $r->Pembayaran;
                $data['SisaBayar'] = $r->SisaBayar;
                $data['TipeBayar'] = $r->TipeBayar;
                $data['DateCreated'] = $r->DateCreated;
                $data['CreatedBy'] = $r->CreatedBy;
                $data['DateUpdated'] = $r->DateUpdated;
                $data['LastModifiedBy'] = $r->LastModifiedBy;

                    //DETAIL
                    // unset($detail);
                    // $detail = array();
                $qDetail = $this->db->get_where('ktv_purchase_detail', array('CoopID'=>$r->CoopID, 'PurchaseId'=>$r->PurchaseID));
                if ($qDetail->num_rows()>0) {
                    $i=0;
                    foreach ($qDetail->result() as $rD) {
                        $data['Detail'][$i]['DetailId'] = $r->DetailId;
                        $data['Detail'][$i]['CoopID'] = $r->CoopID;
                        $data['Detail'][$i]['PurchaseId'] = $r->PurchaseId;
                        $data['Detail'][$i]['InventoryID'] = $r->InventoryID;
                        $data['Detail'][$i]['Qty'] = $r->Qty;
                        $data['Detail'][$i]['Price'] = $r->Price;
                        $i++;
                    }
                }


                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_purchase_sync', array('data'=>$data, 'CoopID'=>$CoopID), 'POST');
                $retValObj = ($retVal);
                // var_dump($retValObj);
                // exit;

                if ($retValObj->success) {
                    //update flag synced to 1
                    $this->db->where(array('CoopID'=>$CoopID, 'PurchaseID'=>$r->PurchaseID));
                    $this->db->update('ktv_purchase', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }
                 //
            }
        }

        return $totalRow;
    }

    public function startSyncSale($CoopID)
    {
        /*
            items : Journal, ktv_sale, ktv_sale_detail, ktv_inventory, ktv_inventory_stok
            access : local coop
            route : local coop -> server
        */

        $q = $this->db->get_where('ktv_sale', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        $totalRow = 0;
        if ($q->num_rows()>0) {
            $detail = null;
            foreach ($q->result() as $rSale) {
                $dataSale['data'] = array(
                            'SaleId'=>$rSale->SaleId,
                            'CoopID'=>$rSale->CoopID,
                            'OrgType'=>$rSale->OrgType,
                            'OrgID'=>$rSale->OrgID,
                            'JournalID'=>$rSale->JournalID,
                            'Number'=>$rSale->Number,
                            'CustomerID'=>$rSale->CustomerID,
                            // 'DueDate'=>$rSale->DueDate,
                            'Date'=>$rSale->Date,
                            'Diskon'=>$rSale->Diskon,
                            'Pajak'=>$rSale->Pajak,
                            'Total'=>$rSale->Total,
                            'Pembayaran'=>$rSale->Pembayaran,
                            'SisaBayar'=>$rSale->SisaBayar,
                            // 'TipeBayar'=>$rSale->TipeBayar,
                            'DateCreated'=>$rSale->DateCreated,
                            'CreatedBy'=>$rSale->CreatedBy,
                            'DateUpdated'=>$rSale->DateUpdated,
                            'LastModifiedBy'=>$rSale->LastModifiedBy
                        );

                    //DETAIL
                    $dataSale['Detail'] = null;

                    // unset($detail);
                    // $detail = array();
                    $i=0;
                $qDetail = $this->db->get_where('ktv_sale_detail', array('CoopID'=>$CoopID, 'SaleId'=>$rSale->SaleId));
                foreach ($qDetail->result() as $rDetail) {
                    $dataSale['Detail'][$i]['DetailID'] = $rDetail->DetailID;
                    $dataSale['Detail'][$i]['CoopID'] = $rDetail->CoopID;
                    $dataSale['Detail'][$i]['SaleId'] = $rDetail->SaleId;
                    $dataSale['Detail'][$i]['InventoryID'] = $rDetail->InventoryID;
                    $dataSale['Detail'][$i]['Qty'] = $rDetail->Qty;
                    $dataSale['Detail'][$i]['Price'] = $rrDetail->Price;
                    $dataSale['Detail'][$i]['Problem'] = $rrDetail->Problem;
                    $dataSale['Detail'][$i]['Solution'] = $rrDetail->Solution;
                    $dataSale['Detail'][$i]['DateStart'] = $rrDetail->DateStart;
                    $dataSale['Detail'][$i]['DateEnd'] = $rrDetail->DateEnd;

                    $i++;
                }


                // if(count($dataSale)>0)
                // {
                    $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_sale_sync', array('data'=>$dataSale, 'CoopID'=>$CoopID), 'POST');
                $retValObj = ($retVal);
                    // var_dump($retValObj);
                    // exit;
                    if ($retValObj->success) {
                        //update flag synced to 1
                        $this->db->where(array('CoopID'=>$CoopID, 'SaleId'=>$rSale->SaleId));
                        $this->db->update('ktv_sale', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                        $totalRow++;
                    }
                // }
                 //
                      unset($dataSale);
            }
        }

        return $totalRow;
    }

    public function startSyncJournal($CoopID)
    {
        /*
            items : accounting_journal,accounting_journal_detail
            access : local coop
            route : local coop -> server
        */
        $totalRow = 0;
        $q = $this->db->get_where('accounting_journal', array('CoopID'=>$CoopID, 'SyncedDate'=>null));
        if ($q->num_rows()>0) {
            foreach ($q->result() as $r) {
                $data['JournalID'] = $r->JournalID;
                $data['JournalTypeCode'] = $r->JournalTypeCode;
                $data['JournalDate'] = $r->JournalDate;
                $data['JournalMemo'] = $r->JournalMemo;
                $data['JournalIsPosted'] = $r->JournalIsPosted;
                $data['JournalPostedDate'] = $r->JournalPostedDate;
                $data['JournalCRBY'] = $r->JournalCRBY;
                $data['JournalCRDT'] = $r->JournalCRDT;
                $data['JournalUPBY'] = $r->JournalUPBY;
                $data['JournalUPDT'] = $r->JournalUPDT;

                $qDetail = $this->db->get_where('accounting_journal_detail', array('JournalID'=>$r->JournalID, 'CoopID'=>$r->CoopID));
                if ($qDetail->num_rows()>0) {
                    $i=0;
                    foreach ($qDetail->result() as $rD) {
                        $data['Detail'][$i]['JournalDetailID'] = $r->JournalDetailID;
                        $data['Detail'][$i]['JournalID'] = $r->JournalID;
                        $data['Detail'][$i]['CoopID'] = $r->CoopID;
                        $data['Detail'][$i]['CoaCode'] = isset($r->CoaCode) ? $r->CoaCode : null;
                        $data['Detail'][$i]['JournalDetailDesc'] = isset($r->JournalDetailDesc) ? $r->JournalDetailDesc : null;
                        $data['Detail'][$i]['CurrencyID'] = isset($r->CurrencyID) ? $r->CurrencyID : null;
                        $data['Detail'][$i]['JournalDetailOrig'] = isset($r->JournalDetailOrig) ? $r->JournalDetailOrig : null;
                        $data['Detail'][$i]['JournalDetailExRate'] = isset($r->JournalDetailExRate) ? $r->JournalDetailExRate : null;
                        $data['Detail'][$i]['JournalDetailSum'] = isset($r->JournalDetailSum) ? $r->JournalDetailSum : null;
                        $data['Detail'][$i]['JournalDetailType'] = isset($r->JournalDetailType) ? $r->JournalDetailType : null;
                        $data['Detail'][$i]['JournalDetailCRBY'] = isset($r->JournalDetailCRBY) ? $r->JournalDetailCRBY : null;
                        $data['Detail'][$i]['JournalDetailCRDT'] = isset($r->JournalDetailCRDT) ? $r->JournalDetailCRDT : null;
                        $data['Detail'][$i]['JournalDetailUPBY'] = isset($r->JournalDetailUPBY) ? $r->JournalDetailUPBY : null;
                        $data['Detail'][$i]['JournalDetailUPDT'] = $isset($r->JournalDetailUPDT) ? $r->JournalDetailUPDT : null;
                        $i++;
                    }
                }

                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_journal_sync', array('CoopID'=>$CoopID, 'data'=>$data), 'POST');
                $retValObj = ($retVal);
                // var_dump($retValObj);
                // echo $retValObj->success;
                // exit;
                if ($retValObj->success) {
                    //update flag DateSynced to NULL
                    $this->db->where(array('CoopID'=>$CoopID, 'JournalID'=>$r->JournalID));
                    $this->db->update('accounting_journal', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }

                unset($data);
            }
        } else {
            return 0;
        }

        return $totalRow;
    }

    public function startSyncInventory($CoopID)
    {
        /*
            items : ktv_inventory,ktv_inventory_stok
            access : local coop
            route : local coop -> server
        */

        $totalRow = 0;

        $q = $this->db->get_where('ktv_inventory', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        if ($q->num_rows()>0) {
            foreach ($q->result() as $r) {
                unset($DataInventory);

                $DataInventory = array(
                        'InventoryID' => $r->InventoryID,
                        'OrgType' => $r->OrgType,
                        'Status' => $r->Status,
                        'OrgID' => $r->OrgID,
                        'CoopID' => $r->CoopID,
                        'JournalID' => $r->JournalID,
                        'Number' => $r->Number,
                        'SerialNumber' => $r->SerialNumber,
                        'Name' => $r->Name,
                        'Description' => $r->Description,
                        'UnitMeasurementID' => $r->UnitMeasurementID,
                        'IsInventory' => $r->IsInventory,
                        'IsSell' => $r->IsSell,
                        'IsBuy' => $r->IsBuy,
                        'IsRemoved' => $r->IsRemoved,
                        'RemoveReason' => $r->RemoveReason,
                        'coaIDAsset' => $r->coaIDAsset,
                        'coaIDAkumDepres' => $r->coaIDAkumDepres,
                        'coaIDBebanDepres' => $r->coaIDBebanDepres,
                        'Stock' => $r->Stock,
                        'Images'  => $r->Images,
                        'Cost'  => $r->Cost,
                        'UnitMeasure'  => $r->UnitMeasure,
                        'MinStock'  => $r->MinStock,
                        'SupplierID' => $r->SupplierID,
                        'SupplierName'  => $r->SupplierName,
                        'SellingPrice' => $r->SellingPrice,
                        'SelingTax' => $r->SelingTax,
                        'Notes' => $r->Notes,
                        'YearBuy' => $r->YearBuy,
                        'MonthBuy' => $r->MonthBuy,
                        'DateBuy' => $r->DateBuy,
                        'CategoryID' => $r->CategoryID,
                        'BuyTax'  => $r->BuyTax,
                        'Location'  => $r->Location,
                        'Residu'  => $r->Residu,
                        'Umur' => $r->Umur,
                        'AkumulasiBeban' => $r->AkumulasiBeban,
                        'BebanBerjalan' => $r->BebanBerjalan,
                        'NilaiBuku' => $r->NilaiBuku,
                        'BebanPerBulan' => $r->BebanPerBulan,
                        'AkumulasiAkhir' => $r->AkumulasiAkhir,
                        'IsPaket'  => $r->IsPaket,
                        'ParentInventoryID' => $r->ParentInventoryID,
                        'ParentConvertion' => $r->ParentConvertion,
                        'EvaluateType' => $r->EvaluateType,
                        'EvaluateReason' => $r->EvaluateReason,
                        'EvaluateSoldPrice' => $r->EvaluateSoldPrice,
                        'CreatedBy' => $r->CreatedBy,
                        'CreatedDate' => $r->CreatedDate,
                        'UpdatedBy' => $r->UpdatedBy,
                        'UpdatedDate' => $r->UpdatedDate
                );


                $qDetail = $this->db->get_where('ktv_inventory_stok', array('CoopID'=>$CoopID, 'InventoryID'=>$r->InventoryID));

                $i=0;
                foreach ($qDetail->result() as $rDetail) {
                    $DataInventory['stok'][$i]['StokID'] = $rDetail->StokID;
                    $DataInventory['stok'][$i]['InventoryID'] = $rDetail->InventoryID;
                    $DataInventory['stok'][$i]['CoopID'] = $rDetail->CoopID;
                    $DataInventory['stok'][$i]['Type'] = $rDetail->Type;
                    $DataInventory['stok'][$i]['ID'] = $rDetail->ID;
                    $DataInventory['stok'][$i]['Awal'] = $rDetail->Awal;
                    $DataInventory['stok'][$i]['Jumlah'] = $rDetail->Jumlah;
                    $DataInventory['stok'][$i]['Akhir'] = $rDetail->Akhir;
                    $DataInventory['stok'][$i]['CreatedBy'] = $rDetail->CreatedBy;
                    $i++;
                }

                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_inventory_sync', array('data'=>$DataInventory, 'CoopID'=>$CoopID), 'POST');
                $retValObj = json_decode($retVal);
                // var_dump($retValObj);
                // exit;
                if ($retValObj->success) {
                    //update flag synced to 1
                    $this->db->where(array('CoopID'=>$CoopID, 'InventoryID'=>$r->InventoryID));
                    $this->db->update('ktv_inventory', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }
            }
        } else {
            return 0;
        }

        return $totalRow;
    }

    public function startSyncSaving($CoopID)
    {
        /*
            items : coop_member_saving
            access : local coop
            route : local coop -> server
        */
        $q = $this->db->get_where('coop_member_saving', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        if ($q->num_rows()>0) {
            $data = array();
            $totalRow = 0;
            foreach ($q->result() as $r) {
                unset($data);

                $data = array(
                        'memberSavingID' => $r->memberSavingID,
                        'CoopID' => $r->CoopID,
                        'memberID' => $r->memberID,
                        'savingTypeID' => $r->savingTypeID,
                        'memberSavingRegisteredDate' => $r->memberSavingRegisteredDate,
                        'AmountSaving' => $r->AmountSaving,
                        'memberSavingNo' => $r->memberSavingNo,
                        'memberSavingStatus' => $r->memberSavingStatus,
                        'memberSavingRemark' => $r->memberSavingRemark,
                        'CreatedBy' => $r->CreatedBy,
                        'CreatedDate' => $r->CreatedDate,
                        'UpdatedDate' => $r->UpdatedDate
                    );


                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_membersaving_sync', array('data'=>$data, 'CoopID'=>$CoopID), 'POST');
                $retValObj = json_decode($retVal);
                // var_dump($retValObj);
                // exit;
                if ($retValObj->success) {
                    //update flag synced to 1
                    $this->db->where(array('CoopID'=>$CoopID, 'memberSavingID'=>$r->memberSavingID));
                    $this->db->update('coop_member_saving', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }
            }
        } else {
            return 0;
        }

        return $totalRow;
    }

    public function startSyncMember($CoopID)
    {
        $q = $this->db->get_where('coop_member', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        // echo $this->db->last_query();
        // exit;
        if ($q->num_rows()>0) {
            $data = array();
            $totalRow = 0;
            foreach ($q->result() as $r) {
                unset($data);

                $data = array(
                        'memberID' => $r->memberID,
                        'CoopID' => $r->CoopID,
                        'memberRefID' => $r->memberRefID,
                        'farmerID' => $r->farmerID,
                        'primaryNo' => $r->primaryNo,
                        'registeredDate' => $r->registeredDate,
                        'typeID' => $r->typeID,
                        'name' => $r->name,
                        'identityType' => $r->identityType,
                        'identityNumber' => $r->identityNumber,
                        'gender' => $r->gender,
                        'placeOfBirth' => $r->placeOfBirth,
                        'dateOfBirth' => $r->dateOfBirth,
                        'address' => $r->address,
                        'villageID' => $r->villageID,
                        'phone' => $r->phone,
                        'maritalStatus' => $r->maritalStatus,
                        'education' => $r->education,
                        'job' => $r->job,
                        'status' => $r->status,
                        'remark' => $r->remark,
                        'signature' => $r->signature,
                        'ResignationDate' => $r->ResignationDate,
                        'ResignationReason' => $r->ResignationReason,
                        'familyName' => $r->familyName,
                        'familyRelation' => $r->familyRelation,
                        'familyIdentityType' => $r->familyIdentityType,
                        'familyIdentityNumber' => $r->familyIdentityNumber,
                        'familyAddress' => $r->familyAddress,
                        'familyPhone' => $r->familyPhone,
                        'savingPokok' => $r->savingPokok,
                        'savingWajib' => $r->savingWajib,
                        'uangPangkal' => $r->uangPangkal,
                        'CreatedBy' => $r->CreatedBy,
                        'CreatedDate' => $r->CreatedDate,
                        'UpdatedBy' => $r->UpdatedBy,
                        'UpdatedDate' => $r->UpdatedDate
                    );


                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_membercoop_sync', array('data'=>$data, 'CoopID'=>$CoopID), 'POST');
                $retValObj = ($retVal);

                // echo 'adasd:';
                // print_r($data);
                  // var_dump($retValObj);
                // exit;
                if ($retValObj->success) {
                    //update flag synced to 1
                    $this->db->where(array('CoopID'=>$CoopID, 'memberID'=>$r->memberID));
                    $this->db->update('coop_member', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }
            }
        } else {
            return 0;
        }

        return $totalRow;
    }

    public function startSyncTransaction($CoopID)
    {
        $q = $this->db->get_where('coop_member_transaction', array('SyncedDate'=>null, 'CoopID'=>$CoopID));
        if ($q->num_rows()>0) {
            $data = array();
            $totalRow = 0;
            foreach ($q->result() as $r) {
                unset($data);

                $data = array(
                          'MemberTransactionID' => $r->MemberTransactionID,
                          'CoopID' => $r->CoopID,
                          'MemberTransactionType' => $r->MemberTransactionType,
                          // 'savingTypeID' => $r->savingTypeID,
                          'MemberTransactionNumber' => $r->MemberTransactionNumber,
                          'MemberTransactionDate' => $r->MemberTransactionDate,
                          'MemberTransactionName' => $r->MemberTransactionName,
                          'MemberTransactionIdentity' => $r->MemberTransactionIdentity,
                          'MemberTransactionAddress' => $r->MemberTransactionAddress,
                          'MemberID' => $r->MemberID,
                          'MemberSavingID' => $r->MemberSavingID,
                          'MemberTransactionAmount' => $r->MemberTransactionAmount,
                          'MemberTransactionCurrentBalance' => $r->MemberTransactionCurrentBalance,
                          'MemberTransactionRemark' => $r->MemberTransactionRemark,
                          'CreatedBy' => $r->CreatedBy,
                          'CreatedDate' => $r->CreatedDate,
                          'UpdatedBy' => $r->UpdatedBy,
                          'UpdatedDate' => $r->UpdatedDate,
                          'ApprovedBy' => $r->ApprovedBy
                    );

                $retVal = $this->curl->simple_get($this->config->item('coop_sync_server').'/cooperatives/receive_transaction_sync', array('data'=>$data, 'CoopID'=>$CoopID), 'POST');
                $retValObj = json_decode($retVal);
                // var_dump($retValObj);
                // exit;
                if ($retValObj->success) {
                    //update flag synced to 1
                    $this->db->where(array('CoopID'=>$CoopID, 'MemberTransactionID'=>$r->MemberTransactionID));
                    $this->db->update('coop_member_transaction', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                    $totalRow++;
                }
            }
        } else {
            return 0;
        }

        return $totalRow;
    }

    public function add_area_member($d)
    {
        $this->db->trans_begin();

        $data = array(
                'CoopID'=>$this->post('CoopID'),
                'VillageID'=>$this->post('Desa'),
                'DateCreated'=>date('Y-m-d H:m:s'),
                'CoopID'=>getCoopID()
                // 'CreatedBy'=>
            );

        $this->db->insert('coop_area_member', $data);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('success'=>false);
        } else {
            $this->db->trans_commit();
            return array('success'=>true);
        }
    }

    public function get_area_members($prov=null, $kab=null, $kec=null, $start, $limit)
    {
        $wer = '';
        $CoopID = getCoopID();
        if ($prov!=null) {
            // $wer.=" AND "
        }

        if ($kab!=null) {
            $wer.=" AND c.District='".$kab."'";
        }

        if ($kec!=null) {
            $wer.=" AND b.SubDistrict='".$kec."'";
        }

         // $limit = "LIMIT"
         $q = $this->db->query("SELECT distinct a.VillageID,a.Village,b.SubDistrict,c.District,x.CoopAreaMemberID,x.VillageID,
                                    a.DateCreated
                                from coop_area_member x
                                LEFT JOIN ktv_village a ON x.VillageID = a.VillageID
                                LEFT JOIN ktv_subdistrict b ON a.SubDistrictID=b.SubDistrictID
                                LEFT JOIN ktv_district c ON b.DistrictID=c.DistrictID
                                LEFT JOIN ktv_supplychain_area d ON c.DistrictID in (d.DistrictID)
                                WHERE TRUE $wer AND x.CoopID = '$CoopID' order by x.CoopAreaMemberID desc");

        $result['total'] = $q->num_rows();
        $result['data'] = array_slice($q->result_array(), $start, $limit);
        return $result;
    }

    public function insert_sync_farmer($FarmerID, $Deleted)
    {
        // `Deleted`  '1: yes 0: no',

        $q = $this->db->get('ktv_cooperatives');
        foreach ($q->result() as $r) {
            $cek = $this->db->get_where('coop_sync_farmer', array('FarmerID'=>$FarmerID, 'CoopID'=>$r->CoopID));
            if ($cek->num_rows()>0) {
                $this->db->where(array('FarmerID'=>$FarmerID, 'CoopID'=>$r->CoopID));
                $this->db->update('coop_sync_farmer', array('SyncedDate'=>null, 'Deleted'=>$Deleted));
            } else {
                $this->db->insert('coop_sync_farmer', array('SyncedDate'=>null, 'FarmerID'=>$FarmerID, 'CoopID'=>$r->coopID, 'Deleted'=>$Deleted));
            }
        }
    }

    public function readDataNurseyNumbers($ObjType, $ObjID)
    {
        $sql = "SELECT *, (Panjang*Lebar) AS Luas FROM ktv_nursery WHERE ObjType=? AND ObjID=? ORDER BY NurseryNr";
        $query = $this->db->query($sql, array($ObjType, $ObjID));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getNurseryResponByType($responsibleType, $CoopID)
    {
        if ($responsibleType == 'farmer') {
            $sql="SELECT
                    a.`farmerID` AS id,
                    CONCAT(a.`farmerID`,' - ',b.`FarmerName`) AS label
                FROM
                    coop_member a
                    INNER JOIN ktv_farmer b ON a.`farmerID` = b.`FarmerID`
                WHERE
                    a.`status` = '1'
                    AND a.`CoopID` = ?
                    AND (a.`farmerID` != '0' OR a.`farmerID` IS NOT NULL)
                ORDER BY  a.`farmerID` ASC";
            $query = $this->db->query($sql, array($CoopID));
        }

        if ($responsibleType == 'staff') {
            $sql="SELECT
                    a.`StaffID` AS id,
                    CONCAT('[',a.`StaffID`,'] ',b.`PersonNm`,' - ',IFNULL(d.`PositionName`,'No Position')) AS label
                FROM
                    ktv_staffs a
                    INNER JOIN ktv_persons b ON a.`PersonID` = b.`PersonID`
                    LEFT JOIN `ktv_staff_positions` c ON a.`StaffID` = c.StaffPosStaffID
                        AND CURDATE() BETWEEN c.`StaffPostStart` AND c.`StaffPostEnd`
                        AND c.StatusCode = 'active'
                    LEFT JOIN `ktv_ref_position_type` d ON c.`StaffPosPositionID` = d.`PositionID`
                WHERE
                    a.`ObjType` = 'cooperative'
                    AND a.`StatusCode` = 'active'
                    AND b.`PersonNm` != ''
                    AND a.ObjID = ?
                ORDER BY b.`PersonNm` ASC";
            $query = $this->db->query($sql, array($CoopID));
        }

        $data = $query->result_array();
        $return['data'] = $data;
        return $return;
    }

    public function checkNurseryNr($varPro)
    {
        $sql="SELECT
                NurseryID
            FROM
                ktv_nursery
            WHERE
                ObjID = ?
                AND ObjType = 'koperasi'
                AND NurseryNr = ?";
        $query = $this->db->query($sql, array($varPro['id_obj'], $varPro['NurseryNr']));
        $data = $query->row_array();
        if ($data['NurseryID'] != "") {
            return false;
        } else {
            return true;
        }
    }

    public function createDataNursery($varPro, $varNurseryCeklist)
    {

        //get DistrictID
        $sql="SELECT
            kd.DistrictID AS DistrictID
        FROM
            ktv_cooperatives sub_a
            LEFT JOIN ktv_village kv ON kv.VillageID = sub_a.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.`SubDistrictID` = kv.`SubDistrictID`
            LEFT JOIN ktv_district kd ON kd.`DistrictID` = ksd.`DistrictID`
        WHERE
            sub_a.`CoopID` = ?
        LIMIT 1";
        $query = $this->db->query($sql, array($varPro['id_obj']));
        $dataDistrict = $query->row_array();

        $data = array(
                'ObjType' => $varPro['type_obj'],
                'ObjID' => $varPro['id_obj'],
                'NurseryNr'=> $varPro['NurseryNr'],
                'DistrictID' => $dataDistrict['DistrictID'],
                'ResponsibleType' => $varPro['ResponsibleType_idcoop'],
                'Responsible' => $varPro['Responsible_idcoop'],
                'ResponsibleName' => $varPro['ResponsibleName_idcoop'],
                'ResponsibleBirthday' => $varPro['ResponsibleBirthday_idcoop'],
                'ResponsiblePhone' => $varPro['ResponsiblePhone_idcoop'],
                'ResponsibleGender' => $varPro['ResponsibleGender_idcoop'],
                'ResponsiblePhoto' => $varPro['Photo_old_responsible_idcoop'],
                'Photo' => $varPro['Photo_old_idcoop'],
                'Established'=> $varPro['Established'],
                'Panjang'=> $varPro['Panjang'],
                'Lebar'=> $varPro['Lebar'],
                'Kapasitas'=> $varPro['Kapasitas'],
                'Latitude'=> $varPro['Latitude'],
                'Longitude'=> $varPro['Longitude'],
                'DateCertification'=> $varPro['DateCertification'],
                'DateAppliedCertification'=> $varPro['DateAppliedCertification'],
                'CertificationStatus'=> $varPro['CertificationStatus'],
                'CreatedBy' => $varPro['userid'],
                'DateCreated' => date('Y-m-d H:m:s')
        );
        $this->db->insert('ktv_nursery', $data);
        $id = $this->db->insert_id();

        $sql="UPDATE `ktv_nursery` SET
          `LocationCloseToCommunity` = ?,
          `LocationCloseToCommunityNo` = ?,
          `GoodLandArea` = ?,
          `GoodLandAreaNo` = ?,
          `LocationNearCocoaFarm` = ?,
          `LocationNearCocoaFarmNo` = ?,
          `ContinuousWaterSupply` = ?,
          `ContinuousWaterSupplyNo` = ?,
          `IrrigationInstalled` = ?,
          `IrrigationInstalledNo` = ?,
          `UseShadingNet` = ?,
          `UseShadingNetNo` = ?,
          `AdequateSupplyTopSoil` = ?,
          `AdequateSupplyTopSoilNo` = ?,
          `ImprovedVariety` = ?,
          `ImprovedVarietyNo` = ?,
          `ConstructStoring` = ?,
          `ConstructStoringNo` = ?,
          `CorrectEquipment` = ?,
          `CorrectEquipmentNo` = ?,
          `WindBreakInstalled` = ?,
          `WindBreakInstalledNo` = ?,
          `SecurityFenceInstalled` = ?,
          `SecurityFenceInstalledNo` = ?,
          `FertilizerUsed` = ?,
          `FertilizerUsedNo` = ?,
          `OperatorAdequateTraining` = ?,
          `OperatorAdequateTrainingNo` = ?,
          `AdequateFacility` = ?,
          `AdequateFacilityNo` = ?,
          `SustainablePestDisease` = ?,
          `SustainablePestDiseaseNo` = ?,
          `CloneGrading` = ?,
          `CloneGradingNo` = ?,
          `SeedlingCullingDone` = ?,
          `SeedlingCullingDoneNo` = ?,
          `ProperInputSalesRecord` = ?,
          `ProperInputSalesRecordNo` = ?,
          `SeedsPreGerminated` = ?,
          `SeedsPreGerminatedNo` = ?
        WHERE
            `NurseryID` = ?
        LIMIT 1";
        $p = array(
              $varNurseryCeklist['LocationCloseToCommunity'],
              $varNurseryCeklist['LocationCloseToCommunityNo'],
              $varNurseryCeklist['GoodLandArea'],
              $varNurseryCeklist['GoodLandAreaNo'],
              $varNurseryCeklist['LocationNearCocoaFarm'],
              $varNurseryCeklist['LocationNearCocoaFarmNo'],
              $varNurseryCeklist['ContinuousWaterSupply'],
              $varNurseryCeklist['ContinuousWaterSupplyNo'],
              $varNurseryCeklist['IrrigationInstalled'],
              $varNurseryCeklist['IrrigationInstalledNo'],
              $varNurseryCeklist['UseShadingNet'],
              $varNurseryCeklist['UseShadingNetNo'],
              $varNurseryCeklist['AdequateSupplyTopSoil'],
              $varNurseryCeklist['AdequateSupplyTopSoilNo'],
              $varNurseryCeklist['ImprovedVariety'],
              $varNurseryCeklist['ImprovedVarietyNo'],
              $varNurseryCeklist['ConstructStoring'],
              $varNurseryCeklist['ConstructStoringNo'],
              $varNurseryCeklist['CorrectEquipment'],
              $varNurseryCeklist['CorrectEquipmentNo'],
              $varNurseryCeklist['WindBreakInstalled'],
              $varNurseryCeklist['WindBreakInstalledNo'],
              $varNurseryCeklist['SecurityFenceInstalled'],
              $varNurseryCeklist['SecurityFenceInstalledNo'],
              $varNurseryCeklist['FertilizerUsed'],
              $varNurseryCeklist['FertilizerUsedNo'],
              $varNurseryCeklist['OperatorAdequateTraining'],
              $varNurseryCeklist['OperatorAdequateTrainingNo'],
              $varNurseryCeklist['AdequateFacility'],
              $varNurseryCeklist['AdequateFacilityNo'],
              $varNurseryCeklist['SustainablePestDisease'],
              $varNurseryCeklist['SustainablePestDiseaseNo'],
              $varNurseryCeklist['CloneGrading'],
              $varNurseryCeklist['CloneGradingNo'],
              $varNurseryCeklist['SeedlingCullingDone'],
              $varNurseryCeklist['SeedlingCullingDoneNo'],
              $varNurseryCeklist['ProperInputSalesRecord'],
              $varNurseryCeklist['ProperInputSalesRecordNo'],
              $varNurseryCeklist['SeedsPreGerminated'],
              $varNurseryCeklist['SeedsPreGerminatedNo'],
              $id
        );
        $query = $this->db->query($sql, $p);
        //update nursery ceklist (end)

        if ($query) {
            $results['id'] = $id;
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['id'] = null;
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateDataNursery($varPro, $varNurseryCeklist)
    {
        $data = array(
            'ResponsibleType' => $varPro['ResponsibleType_idcoop'],
            'Responsible' => $varPro['Responsible_idcoop'],
            'ResponsibleName' => $varPro['ResponsibleName_idcoop'],
            'ResponsibleBirthday' => $varPro['ResponsibleBirthday_idcoop'],
            'ResponsiblePhone' => $varPro['ResponsiblePhone_idcoop'],
            'ResponsibleGender' => $varPro['ResponsibleGender_idcoop'],
            'ResponsiblePhoto' => $varPro['Photo_old_responsible_idcoop'],
            'Photo' => $varPro['Photo_old_idcoop'],
            'Established'=> $varPro['Established'],
            'Panjang'=> $varPro['Panjang'],
            'Lebar'=> $varPro['Lebar'],
            'Kapasitas'=> $varPro['Kapasitas'],
            'Latitude'=> $varPro['Latitude'],
            'Longitude'=> $varPro['Longitude'],
            'DateCertification'=> $varPro['DateCertification'],
            'DateAppliedCertification'=> $varPro['DateAppliedCertification'],
            'CertificationStatus'=> $varPro['CertificationStatus'],
            'LastModifiedBy' => $varPro['userid'],
            'DateUpdated' => date('Y-m-d H:m:s')
        );
        $this->db->where('NurseryID', $this->input->post('NurseryID'));
        $query = $this->db->update('ktv_nursery', $data);

        $sql="UPDATE `ktv_nursery` SET
          `LocationCloseToCommunity` = ?,
          `LocationCloseToCommunityNo` = ?,
          `GoodLandArea` = ?,
          `GoodLandAreaNo` = ?,
          `LocationNearCocoaFarm` = ?,
          `LocationNearCocoaFarmNo` = ?,
          `ContinuousWaterSupply` = ?,
          `ContinuousWaterSupplyNo` = ?,
          `IrrigationInstalled` = ?,
          `IrrigationInstalledNo` = ?,
          `UseShadingNet` = ?,
          `UseShadingNetNo` = ?,
          `AdequateSupplyTopSoil` = ?,
          `AdequateSupplyTopSoilNo` = ?,
          `ImprovedVariety` = ?,
          `ImprovedVarietyNo` = ?,
          `ConstructStoring` = ?,
          `ConstructStoringNo` = ?,
          `CorrectEquipment` = ?,
          `CorrectEquipmentNo` = ?,
          `WindBreakInstalled` = ?,
          `WindBreakInstalledNo` = ?,
          `SecurityFenceInstalled` = ?,
          `SecurityFenceInstalledNo` = ?,
          `FertilizerUsed` = ?,
          `FertilizerUsedNo` = ?,
          `OperatorAdequateTraining` = ?,
          `OperatorAdequateTrainingNo` = ?,
          `AdequateFacility` = ?,
          `AdequateFacilityNo` = ?,
          `SustainablePestDisease` = ?,
          `SustainablePestDiseaseNo` = ?,
          `CloneGrading` = ?,
          `CloneGradingNo` = ?,
          `SeedlingCullingDone` = ?,
          `SeedlingCullingDoneNo` = ?,
          `ProperInputSalesRecord` = ?,
          `ProperInputSalesRecordNo` = ?,
          `SeedsPreGerminated` = ?,
          `SeedsPreGerminatedNo` = ?
        WHERE
            `NurseryID` = ?
        LIMIT 1";
        $p = array(
              $varNurseryCeklist['LocationCloseToCommunity'],
              $varNurseryCeklist['LocationCloseToCommunityNo'],
              $varNurseryCeklist['GoodLandArea'],
              $varNurseryCeklist['GoodLandAreaNo'],
              $varNurseryCeklist['LocationNearCocoaFarm'],
              $varNurseryCeklist['LocationNearCocoaFarmNo'],
              $varNurseryCeklist['ContinuousWaterSupply'],
              $varNurseryCeklist['ContinuousWaterSupplyNo'],
              $varNurseryCeklist['IrrigationInstalled'],
              $varNurseryCeklist['IrrigationInstalledNo'],
              $varNurseryCeklist['UseShadingNet'],
              $varNurseryCeklist['UseShadingNetNo'],
              $varNurseryCeklist['AdequateSupplyTopSoil'],
              $varNurseryCeklist['AdequateSupplyTopSoilNo'],
              $varNurseryCeklist['ImprovedVariety'],
              $varNurseryCeklist['ImprovedVarietyNo'],
              $varNurseryCeklist['ConstructStoring'],
              $varNurseryCeklist['ConstructStoringNo'],
              $varNurseryCeklist['CorrectEquipment'],
              $varNurseryCeklist['CorrectEquipmentNo'],
              $varNurseryCeklist['WindBreakInstalled'],
              $varNurseryCeklist['WindBreakInstalledNo'],
              $varNurseryCeklist['SecurityFenceInstalled'],
              $varNurseryCeklist['SecurityFenceInstalledNo'],
              $varNurseryCeklist['FertilizerUsed'],
              $varNurseryCeklist['FertilizerUsedNo'],
              $varNurseryCeklist['OperatorAdequateTraining'],
              $varNurseryCeklist['OperatorAdequateTrainingNo'],
              $varNurseryCeklist['AdequateFacility'],
              $varNurseryCeklist['AdequateFacilityNo'],
              $varNurseryCeklist['SustainablePestDisease'],
              $varNurseryCeklist['SustainablePestDiseaseNo'],
              $varNurseryCeklist['CloneGrading'],
              $varNurseryCeklist['CloneGradingNo'],
              $varNurseryCeklist['SeedlingCullingDone'],
              $varNurseryCeklist['SeedlingCullingDoneNo'],
              $varNurseryCeklist['ProperInputSalesRecord'],
              $varNurseryCeklist['ProperInputSalesRecordNo'],
              $varNurseryCeklist['SeedsPreGerminated'],
              $varNurseryCeklist['SeedsPreGerminatedNo'],
              $this->input->post('NurseryID')
        );
        $query = $this->db->query($sql, $p);
        //update nursery ceklist (end)

        if ($query) {
            $results['id'] = $this->input->post('NurseryID');
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['id'] = null;
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function createDataNurseryTransaction($uid)
    {
        $data = array(
                'NurseryID'=> $this->input->post('id_nursey'),
                'Buyer'=> $this->input->post('Buyer'),
                'Volume' => $this->input->post('Volume'),
                'Price'=> $this->input->post('Price'),
                'DateTransaction'=> str_replace("T00:00:00", "", $this->input->post('DateTransaction')),
                'CreatedBy' => $uid,
                'DateCreated' => date('Y-m-d H:m:s')
        );
        $this->db->insert('ktv_nursery_transaction', $data);

        $id = $this->db->insert_id();

        if ($this->db->affected_rows() > 0) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['id'] = null;
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function readDataNurseryTrans($id, $prov, $kab, $start, $limit)
    {
        $limit = $this->input->get('limit');
        $start = $this->input->get('start');

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    NurseryTransactionID,NurseryID,Buyer,Volume,Price,SUBSTR(DateTransaction,1,10) AS DateTransaction,Volume*Price as Total
                FROM
                    ktv_nursery_transaction
                WHERE
                    NurseryID=? AND StatusCode != 'nullified'
                ORDER BY DateTransaction DESC
                #LIMIT ?,?";
        $query = $this->db->query($sql, array($id, (int)$start, (int)$limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;
        return $result;
    }

    public function deleteTransaction($id)
    {
        //$sql = "DELETE FROM ktv_nursery_transaction WHERE NurseryTransactionID=?";
        $sql="UPDATE ktv_nursery_transaction SET StatusCode='nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE NurseryTransactionID = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function updateDataNurseryTransaction($id_nursey, $Buyer, $Volume, $Price, $DateTransaction, $uid, $id)
    {
        $data = array(
                'NurseryID'=> $id_nursey,
                'Buyer'=> $Buyer,
                'Volume' => $Volume,
                'Price'=> $Price,
                'DateTransaction'=> str_replace("T00:00:00", "", $DateTransaction),
                'LastModifiedBy' => $uid,
                'DateUpdated' => date('Y-m-d H:m:s')
        );
        $this->db->where('NurseryTransactionID', $id);
        $this->db->update('ktv_nursery_transaction', $data);
        if ($this->db->affected_rows()>0) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = " Failed to update record";
        }
        return $results;
    }

    public function readNurseryArea($ObjType, $ObjID, $NurseryID, $NurseryNr)
    {
        $sql = "SELECT Latitude,Longitude FROM ktv_nursery WHERE ObjType=? AND ObjID=? AND NurseryID=? AND NurseryNr=?";
        $query = $this->db->query($sql, array($ObjType, $ObjID, $NurseryID, $NurseryNr));
        $result= $query->result_array();
        return $result[0];
    }

    public function readDataFormNursery($id, $NurseryID)
    {
        $sql = "select *, (Panjang*Lebar) AS Luas from ktv_nursery where ObjID = ? AND NurseryID=?";
        $q = $this->db->query($sql, array($id, $NurseryID));
        if ($q->num_rows()>0) {
            $data = $q->result_array();
            $return['success'] = true;
            $return['data'] = $data[0];
            return $return;
        } else {
            return array('success'=>false);
        }
    }

    public function simple_get_content($url, $options, $method = 'GET')
    {

      //test pake file_get_contents
      $postdata = http_build_query(
          $options
      );

        $opts = array('http' =>
          array(
              'method'  => $method,
              'header'  => 'Content-type: application/x-www-form-urlencoded',
              'content' => $postdata
          )
      );

        $context  = stream_context_create($opts);

        $retVal = file_get_contents($url, false, $context);

        $retValObj = json_decode($retVal);

        return $retValObj;
    }

    public function getTraining($CoopID)
    {
        $sql = "
SELECT 
    ct.CoopTrainingsID
    , ct.CoopID
    , ct.CoopTrainingID
    , ct.TrainingStart
    , ct.TrainingEnd 
    , ct.TrainingDays
    , ct.ServiceProvID
    , ct.FacilitatorStaff 
    , COUNT(tp.ParticipantsID) AS participants
    , rt.CoopTrainingName 
    , ct.DistrictID
    , d.ProvinceID
    , ct.Location
FROM
    ktv_cooperative_trainings ct
LEFT JOIN ktv_cooperative_trainings_participants tp ON tp.CoopTrainingsID = ct .CoopTrainingsID
LEFT JOIN ktv_ref_cooperative_trainings rt ON rt.CoopTrainingID = ct.CoopTrainingID
LEFT JOIN ktv_district d ON d.DistrictID = ct.DistrictID
WHERE
    ct.StatusCode != 'nullified'
    AND ct.CoopID = ?
GROUP BY ct.CoopTrainingsID
        ";
        $query = $this->db->query($sql, array($CoopID));
        if ($query->num_rows()>0) {
            return array(
                'data'      => $query->result_array(),
                'total'     => $query->num_rows()
            );
        }
        return array('data' => null, 'total' => 0);
    }

    public function getParticipants($CoopTrainingID)
    {
        $sql = "
SELECT 
    p.ParticipantsID
    , p.CoopTrainingsID
    , p.MemberID
    , p.MemberFarmerID
    , p.WritingStart
    , p.WritingEnd
    , p.BallotStart
    , p.BallotEnd
    , p.Remarks
    , m.Name
FROM
    ktv_cooperative_trainings_participants p
LEFT JOIN coop_member m ON m.MemberID = p.MemberID
WHERE
    StatusCode = 'active'
    AND CoopTrainingsID = ?
        ";
        $query = $this->db->query($sql, array($CoopTrainingID));
        if ($query->num_rows()>0) {
            return array(
                'data'      => $query->result_array(),
                'total'     => $query->num_rows()
            );
        }
        return array('data' => null, 'total' => 0);
    }

    public function getTrainingType()
    {
        $sql = "
SELECT 
    CoopTrainingID AS id
    , CoopTrainingName AS label
FROM
    ktv_ref_cooperative_trainings 
WHERE
    StatusCode = 'active'
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getProvinces()
    {
        $sql = "
SELECT 
    CoopTrainingID AS id
    , CoopTrainingName AS label
FROM
    ktv_ref_cooperative_trainings 
WHERE
    StatusCode = 'active'
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getServiceProvider()
    {
        $sql = "
SELECT 
    ServiceProvID AS id
    , ServiceProvName AS label
FROM
    ktv_service_provider 
WHERE
    StatusCode = 'active'
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getCoopMember($CoopID, $CoopTrainingsID = '')
    {
        $params[] = $CoopID;
        $sql = "
SELECT 
    MemberID AS id
    , `Name` AS label
FROM
    coop_member 
WHERE
    `Status`= 1
    AND CoopID = ?
    --where--
ORDER BY label
        ";
        $where = '';
        if (!empty($CoopTrainingsID)) {
            $where = " AND MemberID NOT IN (SELECT p.MemberID FROM ktv_cooperative_trainings_participants p WHERE p.CoopTrainingsID = ?)";
            $params[] = $CoopTrainingsID;
        }
        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, $params);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function addTraining($data)
    {
        $sql = "
INSERT INTO ktv_cooperative_trainings (
    CoopID
    , CoopTrainingID
    , ServiceProvID
    , Location
    , DistrictID
    , FacilitatorStaff
    , TrainingStart
    , TrainingEnd
    , TrainingDays
    , StatusCode
    , Remarks
    , DateCreated
    , CreatedBy
) 
VALUES
    (
    ?
    , ?
    , ?
    , ?
    , ?
    , ?
    , ?
    , ?
    , ?
    , 'active'
    , ?
    , NOW()
    , ?    
    )
";
        return $this->db->query($sql, array(
    $data['CoopID']
    , $data['CoopTrainingID']
    , $data['ServiceProvID']
    , $data['Location']
    , $data['DistrictID']
    , $data['FacilitatorStaff']
    , $data['TrainingStart']
    , $data['TrainingEnd']
    , $data['TrainingDays']
    , $data['Remarks']
    , $_SESSION['userid']
            ));
    }

    public function updateTraining($data)
    {
        $sql = "
UPDATE ktv_cooperative_trainings 
SET
    CoopTrainingID      = ?
    , ServiceProvID     = ?
    , Location          = ?
    , DistrictID        = ?
    , FacilitatorStaff  = ?
    , TrainingStart     = ?
    , TrainingEnd       = ?
    , TrainingDays      = ?
    , Remarks           = ?
    , DateUpdated       = NOW()
    , LastModifiedBy    = ?
WHERE
    CoopTrainingsID = ?
";
        return $this->db->query($sql, array(
    $data['CoopTrainingID']
    , $data['ServiceProvID']
    , $data['Location']
    , $data['DistrictID']
    , $data['FacilitatorStaff']
    , $data['TrainingStart']
    , $data['TrainingEnd']
    , $data['TrainingDays']
    , $data['Remarks']
    , $_SESSION['userid']
    , $data['CoopTrainingsID']
            ));
    }

    public function addParticipants($data)
    {
        $sql = "
INSERT INTO ktv_cooperative_trainings_participants (
    CoopTrainingsID
    , MemberID
    , WritingStart
    , WritingEnd
    , BallotStart
    , BallotEnd
    , StatusCode
    , DateCreated
    , CreatedBy
) 
VALUES
    (
        ?
        , ?
        , ?
        , ?
        , ?
        , ?
        , 'active'
        , NOW()
        , {$_SESSION['userid']}
    ) ;
        ";
        return $this->db->query($sql, array($data['CoopTrainingsID'], $data['MemberID'], $data['WritingStart'], $data['WritingEnd'], $data['BallotStart'], $data['BallotEnd']));
    }

    public function updateParticipants($data)
    {
        $sql = "
UPDATE ktv_cooperative_trainings_participants 
SET
    MemberID = ?
    , WritingStart = ?
    , WritingEnd = ?
    , BallotStart = ?
    , BallotEnd = ?
    , StatusCode = 'active'
    , DateUpdated = NOW()
    , LastModifiedBy = {$_SESSION['userid']}
WHERE
    ParticipantsID = ?
        ";
        return $this->db->query($sql, array($data['MemberID'], $data['WritingStart'], $data['WritingEnd'], $data['BallotStart'], $data['BallotEnd'], $data['ParticipantsID']));
    }

    public function getParticipantAttendance($CoopTrainingsID, $MemberID, $DayNumber = '') {
        $params = array($CoopTrainingsID, $MemberID);
        $sql = "
        SELECT 
            a.`DayNumber`, a.`SignAttendance1`, IF(a.TrainingDate = '0000-00-00',NULL,a.TrainingDate) AS TrainingDate,
            IF(a.`Attendance1` = 0 OR a.`Attendance1` IS NULL, '',1)`Attendance1`,
            IF(a.`Attendance2` = 0 OR a.`Attendance2` IS NULL, '',1)`Attendance2`
        FROM ktv_cooperative_trainings_attendance a
        WHERE
            a.`CoopTrainingsID` = ?
        AND a.`MemberID` = ?";
        if ($DayNumber != '') {
            $params[] = $DayNumber;
            $sql .= " AND a.`DayNumber` = ?";
        } 
        $query = $this->db->query($sql, $params);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            $this->generateParticipantAttendance($CoopTrainingsID, $MemberID);
            return $this->getParticipantAttendance($CoopTrainingsID, $MemberID, $DayNumber);
        }
    }

    public function generateParticipantAttendance($CoopTrainingsID, $MemberID) {
        $query = $this->db->get_where('ktv_cooperative_trainings', array('CoopTrainingsID' => $CoopTrainingsID));
        $detail = $query->row_array(0);

        $attendance = array();
        for ($i = 1; $i <= $detail['TrainingDays']; $i++) {
            $attendance[] = array(
                'CoopTrainingsID' => $CoopTrainingsID,
                'MemberID' => $MemberID,
                'DayNumber' => $i,
                'Attendance1' => 0,
                'Attendance2' => 0,
            );
        }
        return $this->db->insert_batch('ktv_cooperative_trainings_attendance', $attendance);
    }

    public function getParticipantDetail($ParticipantsID) {
        $sql = "
SELECT
    tp.ParticipantsID,
    m.Name,
    cpg.`GroupName`,
    t.TrainingDays,
    DATE(t.TrainingStart) AS TrainingStart,
    DATE(t.TrainingEnd) AS TrainingEnd
FROM `ktv_cooperative_trainings_participants` AS tp
JOIN ktv_cooperative_trainings t ON t.CoopTrainingsID = tp.CoopTrainingsID
JOIN coop_member m ON m.MemberID = tp.MemberID
JOIN ktv_farmer f ON f.`FarmerID` = m.FarmerID
JOIN ktv_cpg cpg ON cpg.`CPGid` = f.`CPGid`
WHERE 1 = 1
    AND tp.ParticipantsID = ?
        ";
        $query = $this->db->query($sql, array($ParticipantsID));
        if ($query->num_rows() > 0) {
            return $query->row_array(0);
        }
    }

    public function getTrainingDetail($CoopTrainingsID)
    {
        $sql = "
SELECT 
    t.CoopTrainingsID
    , r.CoopTrainingName
    , c.CoopName
    , sp.ServiceProvName
    , t.FacilitatorStaff
    , t.TrainingDays
    , t.TrainingStart
    , t.TrainingEnd
    , d.District
    , p.Province
    , t.Location
FROM
    ktv_cooperative_trainings t
LEFT JOIN ktv_cooperatives c ON c.CoopID = t.CoopID
LEFT JOIN ktv_ref_cooperative_trainings r ON r.CoopTrainingID = t.CoopTrainingID
LEFT JOIN ktv_service_provider sp ON sp.ServiceProvID = t.ServiceProvID
LEFT JOIN ktv_district d ON d.DistrictID = t.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
WHERE
    t.CoopTrainingsID = ?
LIMIT 1
        ";
        $query = $this->db->query($sql, array($CoopTrainingsID));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function updateParticipantAttendance($CoopTrainingsID, $MemberID, $DayNumber, $Attendance1, $Attendance2, $TrainingDate)
    {
        $sql = "
UPDATE ktv_cooperative_trainings_attendance 
SET
    TrainingDate = ?
    , Attendance1 = ?
    , Attendance2 = ?
    , DateUpdated = NOW()
    , LastModifiedBy = {$_SESSION['userid']}
WHERE
    CoopTrainingsID = ?
    AND MemberID = ?
    AND DayNumber = ?
        ";
        return $this->db->query($sql, array($TrainingDate, $Attendance1, $Attendance2, $CoopTrainingsID, $MemberID, $DayNumber));
    }

    public function getTrainingParticipant($CoopTrainingsID)
    {
        $sql = "
SELECT
    m.MemberID
    , m.Name
    , m.Gender
    , v.Village
    , 0 AS Attendance1
    , 0 AS Attendance2
FROM ktv_cooperative_trainings_participants tp
LEFT JOIN coop_member m ON m.MemberID = tp.MemberID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
WHERE
    tp.CoopTrainingsID = ?    
ORDER BY m.Name
        ";
        $query = $this->db->query($sql, array($CoopTrainingsID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

    public function getTrainingAttendance($CoopTrainingsID, $DayNumber)
    {
        $sql = "
SELECT
    m.MemberID
    , m.Name
    , m.Gender
    , v.Village
    , a.Attendance1
    , a.Attendance2
FROM ktv_cooperative_trainings_participants tp
LEFT JOIN coop_member m ON m.MemberID = tp.MemberID
LEFT JOIN ktv_village v ON v.VillageID = m.VillageID
LEFT JOIN ktv_cooperative_trainings_attendance a ON a.CoopTrainingsID = tp.CoopTrainingsID AND a.MemberID = tp.MemberID AND a.DayNumber = ?
WHERE
    tp.CoopTrainingsID = ?    
ORDER BY m.Name
        ";
        $query = $this->db->query($sql, array($DayNumber, $CoopTrainingsID));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }
}

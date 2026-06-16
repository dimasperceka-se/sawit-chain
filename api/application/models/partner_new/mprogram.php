<?php
class Mprogram extends CI_Model {

    function readPrograms($start,$limit,$searchText){
        $a1 = "/*";
        $a2 = "*/";

        if (!empty($searchText)) {
            $a1 = "";
            $a2 = "";
        }

        $sql = "
            select %s
            from ktv_program_partner
            WHERE
               StatusCode != 'nullified'
            $a1 AND PartnerName LIKE ? AND PartnerFullName LIKE ? $a2
            ORDER BY PartnerName %s";
        $query = $this->db->query(sprintf($sql,'PartnerID as id,PartnerName,
            IF(PartnerIndustry="0","Implementer",IF(PartnerIndustry="1","Donor",IF(PartnerIndustry="2","Trader",
               IF(PartnerIndustry="3","Processor",IF(PartnerIndustry="4","Manufacturer","Input Suplier"))))) as type,
            PartnerFullName,Photo,PartnerIndustry,PartnerProgramName','LIMIT ?,?'),
            array("%$searchText%","%$searchText%",(int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''),["%$searchText%","%$searchText%"]);
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readProgram($id){
        $this->load->library('awsfileupload');
        $sql = "
            select PartnerID as id, PartnerName,PartnerIndustry,PartnerFullName,Photo,FlagAccess,PhotoProgram,PartnerProgramName
            from ktv_program_partner
            WHERE PartnerID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->row_array();
        
        if($this->awsfileupload->doesObjectExist($result['Photo']) == true) {
            $result['PhotoOld'] = $result['Photo'];
            $result['Photo'] = $this->config->item('CTCDN')."/".$result['Photo'];
        }else{
            $result['PhotoOld'] = 'images/Photo/'.$result['Photo'];
            $result['Photo'] = base_url().'images/Photo/'.$result["Photo"];
        }
        
        if($this->awsfileupload->doesObjectExist($result['PhotoProgram']) == true) {
            $result['PhotoProgramOld'] = $result['PhotoProgram'];
            $result['PhotoProgram'] = $this->config->item('CTCDN')."/".$result['PhotoProgram'];
        }else{
            $result['PhotoProgramOld'] = 'images/Photo/'.$result['PhotoProgram'];
            $result['PhotoProgram'] = base_url().'images/Photo/'.$result["PhotoProgram"];
        }
        return $result;
    }

    function createProgram($PartnerName,/*$PartnerIndustry,*/$PartnerFullName,$PartnerProgramName,$FlagAkses,$districtId,$cpgId){
        $sql = "
            INSERT INTO ktv_program_partner(
                            PartnerName
                            ,PartnerFullName
                            ,PartnerProgramName
                            ,FlagAccess
                        )
            VALUES (?,?,?,?)";
        $query = $this->db->query($sql, array($PartnerName,/*$PartnerIndustry,*/$PartnerFullName,$PartnerProgramName,$FlagAkses));
        $id = $this->db->insert_id();
        
        if($districtId != ''){
            $sql4 = "INSERT INTO ktv_district_partner VALUES (?,?)";
            $arr = explode(',',$districtId);
            foreach($arr as $arr2){
                $this->db->query($sql4,array($arr2,$id));
            }
        }
        /*
        if($cpgId != ''){
            $sql5 = "INSERT INTO ktv_cpg_partner VALUES (?,?)";
            $arrCpg = explode(',',$cpgId);
            foreach($arrCpg as $arrCpg2){
                if (!empty($arrCpg2)) {
                    $this->db->query($sql5,array($arrCpg2,$id));
                }
            }
        }
         *
         */
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updatePhoto($PartnerID,$path){
        $sql = "
            UPDATE
                ktv_program_partner
            SET
                Photo = '$path'
            WHERE PartnerID = '$PartnerID'
        ";

        $query = $this->db->query($sql);
    }

    function updatePhotoProgram($PartnerID,$path){
        $sql = "
            UPDATE
                ktv_program_partner
            SET
                PhotoProgram = '$path'
            WHERE PartnerID = '$PartnerID'
        ";

        $query = $this->db->query($sql);
    }

    function updateProgram($PartnerName,/*$PartnerIndustry,*/$PartnerFullName,$PartnerProgramName,$FlagAkses,$cmbDistrict,$id,$districtId,$cpgId){

        $this->db->trans_begin();
        $PartnerID = (int) $id;

        $sql = "UPDATE ktv_program_partner
                SET
                    PartnerName=?,
                    PartnerFullName=?,
                    PartnerProgramName=?,
                    FlagAccess=?
                WHERE
                    PartnerID=?";
        $sql2 = "DELETE FROM ktv_district_partner WHERE PartnerID = ?";
        $query = $this->db->query($sql, array($PartnerName,/*$PartnerIndustry,*/$PartnerFullName,$PartnerProgramName,$FlagAkses,$id));
        $this->db->query($sql2,array($id));

        if($districtId != ''){
            $sql4 = "INSERT INTO ktv_district_partner VALUES (?,?)";
            $arr = explode(',',$districtId);
            foreach($arr as $arr2){
                $this->db->query($sql4,array($arr2,$id));
            }
        }

        //ketika ada update ktv_district_partner, maka harus disesuaikan lagi data2 di cpg ktv_cpg_partner nya
        //hapus di tabel ktv_cpg_partner yg district nya tidak dipilih (begin)
        $sql="DELETE FROM ktv_cpg_partner WHERE PartnerID = ? AND SUBSTR(CPGid,1,4) NOT IN ($districtId)";
        $query = $this->db->query($sql,array((int) $id));
        //hapus di tabel ktv_cpg_partner yg district nya tidak dipilih (end)

        //hapus assign2 member atau mill yg diluar district partnernya (begin)

            //ktv_access_partner_member
            $sql="SELECT
                    DISTINCT(SUBSTR(b.`VillageID`,1,4)) AS DistrictID
                FROM
                    ktv_access_partner_member a
                    INNER JOIN ktv_members b ON a.`apmMemberID` = b.`MemberID`
                WHERE
                    a.`apmPartnerID` = ?";
            $query = $this->db->query($sql, array((int) $id));
            $dataDistrictAccessMember = $query->result_array();

            for ($i=0; $i < count($dataDistrictAccessMember); $i++) {
                //cek apakah ada district partner, jika ada maka lewati, jika tidak ada maka harus dihapus
                $sql="SELECT
                        a.`PartnerID`
                    FROM
                        ktv_district_partner a
                    WHERE
                        a.`PartnerID` = '{$PartnerID}'
                        AND a.`DistrictID` = '{$dataDistrictAccessMember[$i]['DistrictID']}'";
                $query = $this->db->query($sql);
                $dataCek = $query->row_array();

                if($dataCek['PartnerID'] == ""){
                    //hapus assign membernya
                    $sql="DELETE ktv_access_partner_member
                            FROM
                                ktv_access_partner_member
                                INNER JOIN ktv_members ON ktv_access_partner_member.`apmMemberID` = ktv_members.`MemberID`
                            WHERE
                                SUBSTR(ktv_members.`VillageID`,1,4) = '{$dataDistrictAccessMember[$i]['DistrictID']}'
                                AND ktv_access_partner_member.`apmPartnerID` = '{$PartnerID}'";
                    $query = $this->db->query($sql);
                }
            }

            //ktv_access_partner_mill
            $sql="SELECT
                    DISTINCT(SUBSTR(b.`VillageID`,1,4)) AS DistrictID
                FROM
                    ktv_access_partner_mill a
                    INNER JOIN ktv_mill b ON a.`apmiMillID` = b.`MillID`
                WHERE
                    a.`apmiPartnerID` = ?";
            $query = $this->db->query($sql, array((int) $PartnerID));
            $dataDistrictAccessMill = $query->result_array();

            for ($i=0; $i < count($dataDistrictAccessMill); $i++) {
                //cek apakah ada district partner, jika ada maka lewati, jika tidak ada maka harus dihapus
                $sql="SELECT
                        a.`PartnerID`
                    FROM
                        ktv_district_partner a
                    WHERE
                        a.`PartnerID` = '{$PartnerID}'
                        AND a.`DistrictID` = '{$dataDistrictAccessMill[$i]['DistrictID']}'";
                $query = $this->db->query($sql);
                $dataCek = $query->row_array();

                if($dataCek['PartnerID'] == ""){
                    //hapus assign membernya
                    $sql="DELETE ktv_access_partner_mill
                            FROM
                                ktv_access_partner_mill
                                INNER JOIN ktv_mill ON ktv_access_partner_mill.`apmiMillID` = ktv_mill.`MillID`
                            WHERE
                                SUBSTR(ktv_mill.`VillageID`,1,4) = '{$dataDistrictAccessMill[$i]['DistrictID']}'
                                AND ktv_access_partner_mill.`apmiPartnerID` = '{$PartnerID}'";
                    $query = $this->db->query($sql);
                }
            }

        //hapus assign2 member atau mill yg diluar district partnernya (end)

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Record Updated";
        }

        return $results;
    }

    function deleteProgram($id){
        //$sql = "DELETE FROM ktv_program_partner WHERE PartnerID=?";
        $sql="UPDATE ktv_program_partner SET StatusCode = 'nullified',LastModifiedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE PartnerID=? LIMIT 1";

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

    function readDistrictInPartner($id){
        $sql = "
            select
                z.DistrictID as value,
                concat(a.Province,' / ',b.District) as text
                -- b.District as text
                -- , Province as province
            from ktv_district_partner z
            left join ktv_district b on z.DistrictID=b.DistrictID
            left join ktv_province a on b.ProvinceID=a.ProvinceID
            WHERE z.PartnerID=?";
        $query = $this->db->query($sql,array($id));
        $result = $query->result_array();
        if(count($result) < 1){
            $result = array(
                'value' => '',
                'text' => ''
            );
        }
        return $result;
    }
    function readCpgInPartner($id, $DistrictID = ''){
        if ($DistrictID === '') {
            $DistrictID = '0';
        }
        $sql = "SELECT DistrictID, `value`, CONCAT(`text`, IF(partner != '', CONCAT(' (',partner,')'),'')) AS `text`
        FROM (
            SELECT
                d.DistrictID,
                a.CPGid as value,
                CONCAT(d.SubDistrict,' / ','[',b.CPGid,'] ',b.GroupName) as text,
                GROUP_CONCAT(DISTINCT pp.PartnerName) AS partner
            FROM ktv_cpg_partner a
            LEFT JOIN ktv_cpg b ON a.CPGid = b.CPGid
            LEFT JOIN ktv_program_partner pp ON pp.PartnerID = a.PartnerID
            LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            WHERE b.CPGid
            AND a.PartnerID = ? AND (d.DistrictID IN({$DistrictID}) OR '0' = '{$DistrictID}')
            GROUP BY a.CPGid
        ) a
        ";
        $query = $this->db->query($sql,array($id));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result = $query->result_array();
        if(count($result) < 1){
            $result = array(
                'value' => '',
                'text' => ''
            );
        }
        return $result;
    }

    public function getCpg($DistrictID)
    {
        $sql = "SELECT `value`, CONCAT(`text`, IF(partner != '', CONCAT(' (',partner,')'),'')) AS `text`
FROM
(
    SELECT
        c.CPGid AS `value`,
        CONCAT(sd.SubDistrict,' / ','[',c.CPGid,'] ',c.GroupName) AS `text`,
        GROUP_CONCAT(DISTINCT pp.PartnerName) AS partner
    FROM ktv_cpg c
    LEFT JOIN ktv_cpg_partner cp ON cp.CPGid = c.CPGid
    LEFT JOIN ktv_program_partner pp ON pp.PartnerID = cp.PartnerID
    LEFT JOIN ktv_village v ON c.VillageID = v.VillageID
    LEFT JOIN ktv_subdistrict sd ON v.SubDistrictID = sd.SubDistrictID
    WHERE
        sd.DistrictID IN ({$DistrictID})
    GROUP BY c.CPGid
)a
ORDER BY a.value";
        $query = $this->db->query($sql, array($DistrictID));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    function readCpgList($id,$idPartner,$stat){
        if($stat == 'search'){
            $sql = "SELECT *
                    FROM
                    (
                        SELECT
                            b.CPGid as value,
                            concat(d.SubDistrict,' / ','[',b.CPGid,'] ',b.GroupName) as text
                        FROM ktv_district a
                            LEFT JOIN (
                                SELECT CPGid,GroupName,VillageID FROM ktv_cpg
                                WHERE CPGid NOT IN (SELECT e.CPGid FROM ktv_cpg_partner e)
                            )as b ON a.DistrictID = SUBSTR(b.CPGid,1,4)
                            LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
                            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                        WHERE
                            a.DistrictID = {$id} AND
                            b.CPGid is not null
                        UNION
                        SELECT
                            a.CPGid as value,
                            concat(d.SubDistrict,' / ','[',b.CPGid,'] ',b.GroupName) as text
                        FROM ktv_cpg_partner a
                            LEFT JOIN ktv_cpg b ON a.CPGid = b.CPGid
                            LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
                            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                        WHERE
                            a.PartnerID = {$idPartner}
                    )a
                    ORDER BY a.value";

            /*
            $sql = "SELECT
                        b.CPGid as value,
                        concat(d.SubDistrict,' / ','[',b.CPGid,'] ',b.GroupName) as text
                    FROM ktv_district a
                        LEFT JOIN (
                            SELECT CPGid,GroupName,VillageID FROM ktv_cpg
                            WHERE CPGid NOT IN (SELECT e.CPGid FROM ktv_cpg_partner e)
                        )as b ON a.DistrictID = SUBSTR(b.CPGid,1,4)
                        LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
                        LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                    WHERE
                        a.DistrictID =    {$id} AND
                        b.CPGid is not null";
             *
             */
        }else{
            /*
            $sql = "SELECT
                    b.CPGid as value,
                    concat(d.SubDistrict,' / ','[',b.CPGid,'] ',b.GroupName) as text
                FROM ktv_district_partner a
                    LEFT JOIN (
                        SELECT CPGid,GroupName,VillageID FROM ktv_cpg
                        WHERE CPGid NOT IN (SELECT e.CPGid FROM ktv_cpg_partner e)
                    )as b ON a.DistrictID = SUBSTR(b.CPGid,1,4)
                    LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
                    LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                WHERE
                    a.PartnerID = {$id} AND
                    b.CPGid is not null";
             *
             */
            $sql = "SELECT `value`, CONCAT(`text`, IF(partner != '', CONCAT(' (',partner,')'),'')) AS `text`
                    FROM
                    (
                        SELECT
                            b.CPGid AS `value`,
                            CONCAT(d.SubDistrict,' / ','[',b.CPGid,'] ',b.GroupName) AS `text`,
                            GROUP_CONCAT(p.PartnerName) AS partner
                        FROM ktv_cpg b
                            LEFT JOIN ktv_cpg_partner a ON a.CPGid = b.CPGid
                            LEFT JOIN ktv_program_partner p ON p.PartnerID = a.PartnerID
                            LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
                            LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                        GROUP BY b.CPGid
                        -- UNION
                        -- SELECT
                        --     b.CPGid as value,
                        --     concat(d.SubDistrict,' / ','[',b.CPGid,'] ',b.GroupName) as text
                        -- FROM ktv_district_partner a
                        --     LEFT JOIN (
                        --         SELECT CPGid,GroupName,VillageID FROM ktv_cpg
                        --         WHERE  CPGid NOT IN (SELECT e.CPGid FROM ktv_cpg_partner e)
                        --     )as b ON a.DistrictID = SUBSTR(b.CPGid,1,4)
                        --     LEFT JOIN ktv_village c ON b.VillageID = c.VillageID
                        --     LEFT JOIN ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
                        -- WHERE
                        --     a.PartnerID = {$id} AND
                        --     b.CPGid is not null
                    )a
                    ORDER BY a.value";
        }

        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(count($result) < 1){
            $result = array(
                'value' => '',
                'text' => ''
            );
        }
        return $result;
    }

    function readDistrictAll($CountryID, $TxtSearch){

        $sqlfilter = ($CountryID != '') ? " AND b.CountryCode = '{$CountryID}'" :"";
        $sqlfilter .= ($TxtSearch != '') ? " AND a.District like '%{$TxtSearch}%'" :"";

        $sql = "SELECT
                    a.DistrictID
                    , a.District DistrictName
                    , b.Province ProvinceName
                    , c.CountryName
                    -- a.District as text
                    -- ,b.Province
                FROM ktv_district a
                    LEFT JOIN ktv_province b on a.ProvinceID=b.ProvinceID
                    LEFT JOIN ktv_country c on c.ISO2=b.CountryCode
                WHERE
                    b.active = '1'
                    AND b.StatusCode = 'active'
                    AND a.active = '1'
                    AND a.StatusCode = 'active'
                    $sqlfilter
                ORDER BY Province ASC";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    function readProvince(){
        $sql = "
            select ProvinceID as id, Province as province
             from ktv_province WHERE ProvinceID NOT IN (12,31)";
        $query = $this->db->query($sql,array($id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readDistrict($id){
        $sql = "
            select DistrictID as id, District as district
             from ktv_district a
             left join ktv_province b on a.ProvinceID=b.ProvinceID
             where a.ProvinceID=? OR Province like ?";
        $query = $this->db->query($sql,array($id,$id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readListDistrict($id){
        $sql = "SELECT
                    a.DistrictID as id,
                    a.District as district,
                    b.Province
                FROM ktv_district a
                LEFT JOIN ktv_province b on a.ProvinceID=b.ProvinceID
                WHERE
                a.PartnerID!=?";
        $query = $this->db->query($sql,array($id));
        //$result['data'] = $query->result_array();
        $data = $query->result_array();
        $parent = array();
        if(count($data)>0){
            foreach($data as $hasil){
                $parent[$hasil['Province']]['children'][] = array(
                    'id' => $hasil['id'],
                    'text' => $hasil['district'],
                    'leaf'=> true
                );
            }
        }
        return $parent;
    }

    function readSelectedDistrict($id){
        $sql = "SELECT
                    a.DistrictID as id,
                    a.District as district,
                    b.Province
                FROM ktv_district a
                LEFT JOIN ktv_province b on a.ProvinceID=b.ProvinceID
                WHERE
                a.PartnerID=?";
        $query = $this->db->query($sql,array($id));
        //$result['data'] = $query->result_array();
        $data = $query->result_array();
        $parent = array();
        if(count($data)>0){
            foreach($data as $hasil){
                $parent[$hasil['Province']]['children'][] = array(
                    'id' => $hasil['id'],
                    'text' => $hasil['district'],
                    'leaf'=> true
                );
            }
        }
        return $parent;
    }

    function updateProgramDist($PartnerID,$DistrictID){
        $sql = "
            DELETE FROM ktv_district_partner
            WHERE PartnerID = ? AND DistrictID=?";
        $query = $this->db->query($sql, array($PartnerID,$DistrictID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function updateAddDistrict($PartnerID,$DistrictID) {
        $sql = "
            UPDATE ktv_district
            SET PartnerID = ?
            WHERE DistrictID=?";
        $query = $this->db->query($sql, array($PartnerID,$DistrictID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function addPartnerDistrict($PartnerID,$DistrictID) {
        $sql = "
            INSERT ktv_district_partner VALUES (?,?)";
        $query = $this->db->query($sql, array($DistrictID,$PartnerID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function readDistrictPartner($id){
        $sql = "SELECT
                    a.DistrictID as id,
                    a.District as district,
                    b.Province
                FROM ktv_district a
                LEFT JOIN ktv_province b on a.ProvinceID=b.ProvinceID";
        $sql2 = "SELECT DistrictID FROM ktv_district_partner WHERE PartnerID=?";
        $query = $this->db->query($sql);
        $query2 = $this->db->query($sql2,array($id));
        //$result['data'] = $query->result_array();
        $data = $query->result_array();
        $data2 = $query2->result_array();
        $arr = array();
        foreach($data2 as $hasil2){
            $arr[] = $hasil2['DistrictID'];
        }
        $parent = array();
        if(count($data)>0){
            foreach($data as $hasil){
                if(!in_array($hasil['id'], $arr)){
                    $c=false;
                }else{
                    $c=true;
                    $parent[$hasil['Province']]['expand'] = true;
                }
                $parent[$hasil['Province']]['children'][] = array(
                    'id' => $hasil['id'],
                    'text' => $hasil['district'],
                    'leaf'=> true,
                    'checked'=> $c
                );

            }
        }
        return $parent;
    }
    
    function insertDistrictPartnerMember($districtId, $partnerId) {
        $sql = "INSERT INTO ktv_district_partner_member (DistrictID, PartnerID) VALUES (?,?)";
        return $this->db->query($sql, array($districtId, $partnerId));
    }

    function updateDistrictPartnerMember($districtId, $partnerId) {
        $sql = "UPDATE ktv_district_partner_member SET PartnerID = ? WHERE DistrictID = ?";
        return $this->db->query($sql, array($partnerId, $districtId));
    }

    function readPartnerMember(){
        $sql = "SELECT PartnerID, PartnerName, PartnerFullName FROM ktv_program_partner order by PartnerName";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        
        $result['data'] = $data;
        return $result;
    }
    
    function readListDistrictPartnerMember() {
        $sql="SELECT
                b.`ProvinceID`
                , b.`Province`
                , a.`DistrictID`
                , a.`District`
                , d.`PartnerID`
                , d.`PartnerParentID`
                , d.`PartnerName`
                , d.`PartnerParentID`
                , d.`PartnerFullName`
                , d. `Alias`
            FROM
                ktv_district a
                INNER JOIN ktv_province b ON a.`ProvinceID` = b.`ProvinceID`
                LEFT JOIN ktv_district_partner_member c ON a.`DistrictID` = c.`DistrictID`
                LEFT JOIN ktv_program_partner d ON c.`PartnerID` = d.`PartnerID`
            WHERE
                a.`StatusCode` = 'active'
                AND a.active = '1'
            ORDER BY b.`Province`";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        
        $result['data'] = $data;
        return $result;
        
    }

    function deleteDistrictPartner($districtId, $partnerId){
        $sql="DELETE FROM ktv_district_partner_member WHERE DistrictID =? and PartnerID=? LIMIT 1";

        $query = $this->db->query($sql, array($districtId, $partnerId));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function findDistrict($id){
        $sql = "SELECT DistrictID as id, District as district "
                . "FROM ktv_district "
                . "WHERE "
                . "DistrictID IN ({$id})";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getDistrictAccessCombo($PartnerID){
        $sql="SELECT
                a.`DistrictID` AS id
                , CONCAT(c.`Province`,' - ',b.`District`) AS label
            FROM
                ktv_district_partner a
                LEFT JOIN ktv_district b ON a.`DistrictID` = b.`DistrictID`
                LEFT JOIN ktv_province c ON b.`ProvinceID` = c.`ProvinceID`
            WHERE
                a.`PartnerID` = ?
            ORDER BY c.`Province` ASC";
        $query = $this->db->query($sql,array($PartnerID));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getCpgAccessCombo($DistrictID){
        $sql="SELECT
                a.`CPGid` AS id
                , CONCAT('[',a.`CPGid`,'] ',a.`GroupName`,' (',IF(c.PartnerID IS NOT NULL,GROUP_CONCAT(c.`PartnerName` SEPARATOR ','),'-'),')') AS label
            FROM
                ktv_cpg a
                LEFT JOIN ktv_cpg_partner b ON a.`CPGid` = b.`CPGid`
                LEFT JOIN ktv_program_partner c ON b.`PartnerID` = c.`PartnerID`
            WHERE
                SUBSTR(a.`VillageID`,1,4) = ?
            GROUP BY a.`CPGid`
            ORDER BY a.`CPGid` ASC";
        $query = $this->db->query($sql,array($DistrictID));
        $result['data'] = $query->result_array();
        return $result;
    }

    public function getCpgAccessSelected($PartnerID,$DistrictID){
        $sql="SELECT
                a.`CPGid`
            FROM
                ktv_cpg_partner a
            WHERE
                a.`PartnerID` = ?
                AND SUBSTR(a.`CPGid`,1,4) = ?";
        $query = $this->db->query($sql,array($PartnerID,$DistrictID));
        $data = $query->result_array();

        $arrTmp = array();
        foreach ($data as $key => $value) {
            $arrTmp[] = $value['CPGid'];
        }

        $return['result'] = true;
        $return['data'] = $arrTmp;
        return $return;
    }

    public function updateAssignCpg($varPost){
        $this->db->trans_start();

        //delete dl datanya
        $sql="DELETE FROM ktv_cpg_partner WHERE SUBSTR(CPGid,1,4) = ? AND PartnerID = ?";
        $query = $this->db->query($sql,array($varPost['AssCpgDistrictAccessSelected'],$varPost['AssCpgPartnerID']));

        //tambahkan kembali
        $assCpg = explode(",",$varPost['AssCpgAssignCpg']);
        foreach ($assCpg as $key => $value) {
            $sql="INSERT INTO `ktv_cpg_partner` SET
                  `CPGid` = ?,
                  `PartnerID` = ?";
            $p = array(
                $value,
                $varPost['AssCpgPartnerID']
            );
            $query = $this->db->query($sql,$p);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    function getLDAPdetail($id){
        $sql = "select PartnerID as id, ad_host, ad_port, ad_basedn, ad_domain, ad_auth from ktv_program_partner WHERE PartnerID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function updateLDAP($id, $ad_host, $ad_port, $ad_basedn, $ad_domain, $ad_auth, $userid){
        if($ad_auth=='true'){
            $ad_auth = '1';
        }else if($ad_auth=='false'){
            $ad_auth = '0';
        }
        $sql = "UPDATE ktv_program_partner SET ad_host=?, ad_port=?, ad_basedn=?, ad_domain=?, ad_auth=?, DateUpdated=NOW(), LastModifiedBy=? WHERE PartnerID=?";
        $query = $this->db->query($sql, array($ad_host, $ad_port, $ad_basedn, $ad_domain, $ad_auth, $userid, $id));

        if ($query){
            $results['success']     = "true";
            $results['message']     = "Update succes.";
        } else {
            $results['success']     = "false";
            $results['message']     = "Update failed! Please try again later.";
        }
        return $results;
    }

    public function getComboOrganizationType(){
        $sql="SELECT
                a.`OrganizationTypeID` AS id
                , a.`OrganizationTypeName` AS label
            FROM
                ktv_ref_organization_type a
            WHERE
                a.`StatusCode` = 'active'
            ORDER BY a.`OrganizationTypeName` ASC";
        $query = $this->db->query($sql);

        return $query->result_array();
    }

    public function GetMainGridInternalProgram($PartnerID){
        $return = array();

        $sql = "SELECT
                bie.BuInExID
                , bie.BuInExName
            FROM
                `ktv_ref_bu_internal_external` bie
            WHERE
                bie.PartnerID = ?
            AND
                bie.BuInExType = 'Internal'
            AND
                bie.StatusCode = 'active'
            GROUP BY
                bie.BuInExID
            ORDER BY
                bie.BuInExID DESC";
        $p = array(
            $PartnerID
        );
        $data = $this->db->query($sql,$p)->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;    
    }

    public function GetMainGridRegion($PartnerID,$TextSearch,$itemAdded,$itemDeleted){
        $return = array();

        $sqlwhere = ($TextSearch != '') ? " AND (dis.District like '%{$TextSearch}%' OR prov.Province like '%{$TextSearch}%')" : "";

        
        if ($itemAdded != "" && $itemAdded != NULL) {
            $FilteritemAdded= " OR dis.DistrictID IN ({$itemAdded})";
            // array_push($p, $pSearch['itemAdded']);
        }
        if ($itemDeleted != "" && $itemDeleted != NULL) {
            $FilteritemDeleted .= " AND dis.DistrictID NOT IN ({$itemDeleted})";
            // array_push($p, $pSearch['itemDeleted']);
        }
        
        $sql = "SELECT
                dp.DistrictID
                , dp.PartnerID
                , dis.District
                , prov.Province
                , co.CountryName
            FROM
                `ktv_district_partner` dp
            LEFT JOIN
                ktv_district dis on dis.DistrictID = dp.DistrictID
            LEFT JOIN
                ktv_province prov on prov.ProvinceID = dis.ProvinceID
            LEFT JOIN
                ktv_country co on co.ISO2 = prov.CountryCode
            WHERE
                1=1
                AND (dp.PartnerID = ? $FilteritemAdded)
                $FilteritemDeleted
                $sqlwhere
            GROUP BY dis.DistrictID
            ORDER BY
                dis.District ASC";
        $p = array(
            $PartnerID
        );
        $data = $this->db->query($sql,$p)->result_array();
        // echo "<pre>";print_r($this->db->last_query());die;

        $return['data'] = $data;
        $return['success'] = true;
        return $return;    
    }

    public function InputInternalProgram($paramPost){
        $sql    = "SELECT * FROM ktv_ref_bu_internal_external WHERE PartnerID = ? AND BuInExType = 'Internal' AND BuInExName = ?";
        $query  = $this->db->query($sql,array($paramPost['PartnerID'],$paramPost['ProgramName']));
        if($query->num_rows()>0){
            $return['success'] = false;
            $return['message'] = lang('Program Already Exist');
        }else{
            $dataPost['PartnerID'] = $paramPost['PartnerID'];
            $dataPost['BuInExName'] = $paramPost['ProgramName'];
            $dataPost['StatusCode'] = 'active';
            $dataPost['BuInExType'] = 'Internal';
            $dataPost['DateCreated'] = date("Y-m-d H:i:s");
            $dataPost['CreatedBy'] = $_SESSION['userid'];
            
            $query = $this->db->insert("ktv_ref_bu_internal_external",$dataPost);

            if($query) {
                $return['success'] = true;
                $return['message'] = lang('Data Saved');
            } else {
                $return['success'] = false;
                $return['message'] = lang('Failed to save data');
            }
        }

        return $return;
    }

    public function DeleteInternalProgram($PartnerID,$BuInExID) {
        /* $sql    = "SELECT * FROM ktv_supplier_bu_in_ex WHERE BuInExID = ?";
        $query  = $this->db->query($sql,array($BuInExID));

        if($query->num_rows()>0){
            $return['success'] = false;
            $return['message'] = lang('Failed to Delete Data, Program had Linked to The Suppliers');
        }else{ */
            $sql = "DELETE FROM ktv_ref_bu_internal_external WHERE BuInExID=? AND BuInExType = 'Internal' LIMIT 1";
            $query = $this->db->query($sql,array($BuInExID));

            if($query) {
                $return['success'] = true;
                $return['message'] = lang('Data Deleted');
            } else {
                $return['success'] = false;
                $return['message'] = lang('Failed to delete data');
            }
        /* } */

        return $return;
            
    }

    public function GetMainGridExternalProgram($PartnerID){
        $return = array();

        $sql = "SELECT
                bie.BuInExID
                , bie.BuInExName
            FROM
                `ktv_ref_bu_internal_external` bie
            WHERE
                bie.PartnerID = ?
            AND
                bie.BuInExType = 'External'
            AND
                bie.StatusCode = 'active'
            GROUP BY
                bie.BuInExID
            ORDER BY
                bie.BuInExID DESC";
        $p = array(
            $PartnerID
        );
        $data = $this->db->query($sql,$p)->result_array();

        $return['data'] = $data;
        $return['success'] = true;
        return $return;    
    }

    public function InputExternalProgram($paramPost){
        $sql    = "SELECT * FROM ktv_ref_bu_internal_external WHERE PartnerID = ? AND BuInExType = 'External' AND BuInExName = ?";
        $query  = $this->db->query($sql,array($paramPost['PartnerID'],$paramPost['ProgramName']));
        if($query->num_rows()>0){
            $return['success'] = false;
            $return['message'] = lang('Program Already Exist');
        }else{
            $dataPost['PartnerID'] = $paramPost['PartnerID'];
            $dataPost['BuInExName'] = $paramPost['ProgramName'];
            $dataPost['BuInExType'] = 'External';
            $dataPost['StatusCode'] = 'active';
            $dataPost['DateCreated'] = date("Y-m-d H:i:s");
            $dataPost['CreatedBy'] = $_SESSION['userid'];
            
            $query = $this->db->insert("ktv_ref_bu_internal_external",$dataPost);

            if($query) {
                $return['success'] = true;
                $return['message'] = lang('Data Saved');
            } else {
                $return['success'] = false;
                $return['message'] = lang('Failed to save data');
            }
        }

        return $return;
    }

    public function DeleteExternalProgram($PartnerID,$BuInExID) {
        /* $sql    = "SELECT * FROM ktv_supplier_bu_in_ex WHERE BuInExID = ?";
        $query  = $this->db->query($sql,array($BuInExID));

        if($query->num_rows()>0){
            $return['success'] = false;
            $return['message'] = lang('Failed to Delete Data, Program had Linked to The Suppliers');
        }else{ */
            $sql = "DELETE FROM ktv_ref_bu_internal_external WHERE BuInExID=? AND BuInExType = 'External' LIMIT 1";
            $query = $this->db->query($sql,array($BuInExID));

            if($query) {
                $return['success'] = true;
                $return['message'] = lang('Data Deleted');
            } else {
                $return['success'] = false;
                $return['message'] = lang('Failed to delete data');
            }
        /* } */

        return $return;
    }

    public function getGridGroupAccessArea($pSearch,$start,$limit,$sortingField,$sortingDir){
        if ($sortingField == "") $sortingField = 'rc.CountryName';
        if ($sortingDir == "") $sortingDir = 'ASC';

        $result['data']     = array();
        $result['total']    = 0;

        if ($pSearch['GroupId'] == "" && $pSearch['itemAdded'] == "" && $pSearch['itemAdded'] == "") {
            return $result;
        }

        $result = array();
        $p = array($pSearch['GroupId']);
        $where = "";
        $filterItemAdded = "";
        $filterItemDeleted = "";

        //================== Filter (Begin) =============================//
        if ($pSearch['TxtSearch'] != "") {
            $where .= "
                AND (
                        rc.CountryName LIKE '%{$pSearch['TxtSearch']}%'
                        OR rp.ProvinceName LIKE '%{$pSearch['TxtSearch']}%'
                        OR rd.DistrictName LIKE '%{$pSearch['TxtSearch']}%'
                    )
            ";
        }
        if ($pSearch['itemAdded'] != "" && $pSearch['itemAdded'] != NULL) {
            $filterItemAdded = "OR rd.DistrictID IN ({$pSearch['itemAdded']})";
            // array_push($p, $pSearch['itemAdded']);
        }
        if ($pSearch['itemDeleted'] != "" && $pSearch['itemDeleted'] != NULL) {
            $filterItemDeleted = "AND rd.DistrictID NOT IN ({$pSearch['itemDeleted']})";
            // array_push($p, $pSearch['itemDeleted']);
        }
        //================== Filter (End) ===============================//

        array_push($p, $start, $limit);

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                rd.`DistrictID`
                , rc.`CountryName`
                , rp.`ProvinceName`
                , rd.`DistrictName`
            FROM
                reg_country rc
                LEFT JOIN reg_province rp ON rp.`CountryID` = rc.`CountryID`
                LEFT JOIN reg_district rd ON rd.`ProvinceID` = rp.`ProvinceID`
                LEFT JOIN sys_group_access acc ON acc.`DistrictID` = rd.`DistrictID`
            WHERE 1=1
                AND rc.StatusCode = 'active'
                AND rp.StatusCode = 'active'
                AND rd.StatusCode = 'active'
                AND (acc.`GroupID` = ? $filterItemAdded)
                $filterItemDeleted
                $where
            GROUP BY
                rd.`DistrictID`
            ORDER BY 
                $sortingField $sortingDir
            LIMIT ?, ?";
        $result['data'] = $this->db->query($sql, $p)->result_array();

        $query_total        = $this->db->query("SELECT FOUND_ROWS() AS total");
        $total              = $query_total->row_array(0);
        $result['total']    = $total['total'];
        
        return $result;
    }

    public function UpdateLogo($PartnerID, $gambarPath) {
        //Cek terlebih dahulu, apakah ada foto lama, kalau ada dihapus dl
        $sql = "SELECT
                    b.`Photo` AS LogoPath
                FROM
                    ktv_program_partner b
                WHERE
                    b.`PartnerID` = ?
                LIMIT 1";
        $DataCek = $this->db->query($sql, array($PartnerID))->row_array();
        if (isset($DataCek['LogoPath']) && $DataCek['LogoPath'] != "") {
            $this->load->library('awsfileupload');
            $this->awsfileupload->delete($DataCek['LogoPath']);
        }

        $sql = "UPDATE ktv_program_partner SET
                Photo = ?
            WHERE
                PartnerID = ?
            LIMIT 1";
        $p = array(
            $gambarPath, $PartnerID
        );
        return $this->db->query($sql, $p);
    }

    public function InsertPartner($paramPost) {
        $results = array();

        // if (trim($paramPost["PartnerParentID"]) == trim(lang("Select Parent"))) {
        //     $paramPost["PartnerParentID"] = 0;
        // }

        /* $PartnerParentID = (int) $paramPost['PartnerParentID'];

        if (@$PartnerParentID == 0) {
            $PartnerParentID = NULL;
        } */

        if ($paramPost['SetasParent'] == 'Yes') {
            $PartnerParentID = NULL;
        } else {
            $PartnerParentID = (int) $paramPost['PartnerParentID'];
        }

        $sql = "INSERT INTO `ktv_program_partner` SET
                `PartnerName` = ?,
                `PartnerFullName` = ?,
                `PartnerIndustry` = ?,
                `AsParent` = ?,
                `DateActivation` = ?,
                `PartnerParentID` = ?,
                `StatusCode` = ?,
                `CreatedBy` = ?,
                `DateCreated` = NOW()
                ";
        $p = array(
            $paramPost['PartnerName'],
            $paramPost['PartnerName'],
            $paramPost['OrganizationType'],
            $paramPost['SetasParent'],
            date($paramPost['ActivationDate']),
            $PartnerParentID,
            $paramPost['Status'],
            $_SESSION['userid']
        );

        $query     = $this->db->query($sql, $p);
        $PartnerID = $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success']   = true;
            $results['message']   = lang("Data saved");
            $results['PartnerID'] = $PartnerID;

            //Proses foto
            if ($paramPost['LogoOld'] != "" && file_exists('files/tmp/' . $paramPost['LogoOld'])) {
                $pathLogoOld = 'files/tmp/' . $paramPost['LogoOld'];

                //upload ke aws s3
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload($pathLogoOld, $paramPost['LogoOld'], AWSS3_LOGO_PARTNER, 'images');

                if ($upload['success'] == true) {
                    $sql = "UPDATE `ktv_program_partner` SET  `Photo` =  ? WHERE  PartnerID = ? LIMIT 1";
                    $query = $this->db->query($sql, array($upload['filenamepath'], $PartnerID));
                }

                //hapus foto temporary
                delete_file($pathLogoOld);
            }
        }

        return $results;
    }

    public function UpdatePartner($paramPost) {
        $results = array();
        $this->db->trans_begin();
        $PartnerID = $paramPost['PartnerID'];

        /* $PartnerParentID = (int) $paramPost['PartnerParentID'];

        if (@$PartnerParentID == 0) {
            $PartnerParentID = NULL;
        } */
        
        // if (trim($paramPost["PartnerParentID"]) == trim(lang("Select Parent"))) {
        //     $paramPost["PartnerParentID"] = 0;
        // }

        if ($paramPost['SetasParent'] == 'Yes') {
            $PartnerParentID = NULL;
        } else {
            $PartnerParentID = (int) $paramPost['PartnerParentID'];
        }

        $sql = "UPDATE ktv_program_partner SET
                    `PartnerName` = ?,
                    `PartnerFullName` = ?,
                    `PartnerIndustry` = ?,
                    `AsParent` = ?,
                    `DateActivation` = ?,
                    `PartnerParentID` = ?,
                    `StatusCode` = ?,
                    `LastModifiedBy` = ?,
                    `DateUpdated` = NOW()
                WHERE PartnerID = ?
                LIMIT 1";
        $p = array(
            $paramPost['PartnerName'],
            $paramPost['PartnerName'],
            $paramPost['OrganizationType'],
            $paramPost['SetasParent'],
            date($paramPost['ActivationDate']),
            $PartnerParentID,
            $paramPost['Status'],
            $_SESSION['userid'],
            $PartnerID
        );

        $query = $this->db->query($sql, $p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();

            //Proses foto
            if ($paramPost['LogoOld'] != "" && file_exists('files/tmp/' . $paramPost['LogoOld'])) {
                $pathLogoOld = 'files/tmp/' . $paramPost['LogoOld'];

                //upload ke aws s3
                $this->load->library('awsfileupload');
                $upload = $this->awsfileupload->upload($pathLogoOld, $paramPost['LogoOld'], AWSS3_LOGO_PARTNER, 'images');

                if ($upload['success'] == true) {
                    $sql = "UPDATE `ktv_program_partner` SET  `Photo` =  ? WHERE  PartnerID = ? LIMIT 1";
                    $query = $this->db->query($sql, array($upload['filenamepath'], $PartnerID));
                }

                //hapus foto temporary
                delete_file($pathLogoOld);
            }

            $checkExistCommodity = $this->db->where('PartnerID', (int) $PartnerID)
                                            ->get('ktv_partner_commodity')
                                            ->result();

            if (!empty($checkExistCommodity)) {
                $this->db->delete('ktv_partner_commodity', ['PartnerID' => (int) $PartnerID]);
            }

            if (!empty($paramPost["CommodityOptions"])) {
                foreach (explode(',', $paramPost["CommodityOptions"]) as $key => $value) {
                    $dataCommodity = array(
                        "PartnerID"  => $PartnerID,
                        "CommoID"    => (int) $value
                    );

                    $query = $this->db->insert('ktv_partner_commodity',$dataCommodity);
                }
            }

            $passingSelection = json_decode(json_decode($paramPost['passing_selection']));

            if (!empty($passingSelection->itemAdded)) {
                foreach ($passingSelection->itemAdded as $key => $value) {
                    $dataInsert = [
                        'PartnerID'  => (int) $PartnerID,
                        'DistrictID' => (int) $value
                    ];

                    $this->db->insert('ktv_district_partner', $dataInsert);
                }
            }

            if (!empty($passingSelection->itemDeleted)) {
                foreach ($passingSelection->itemDeleted as $key => $value) {
                    $dataDelete = [
                        'PartnerID'  => (int) $PartnerID,
                        'DistrictID' => (int) $value
                    ];

                    $this->db->delete('ktv_district_partner', $dataDelete);
                }
            }

            $results['success']   = true;
            $results['message']   = lang("Data saved");
            $results['PartnerID'] = $PartnerID;
        }

        return $results;
    }

    public function GetPartnerBasicDataForm($PartnerID) {
        $return = array();

        $sql = "SELECT
                    a.PartnerID
                    ,a.PartnerName
                    ,a.Photo as Logo
                    ,a.PartnerIndustry as OrganizationType
                    ,a.AsParent as SetasParent
                    ,IF(a.PartnerParentID = 0, '', a.PartnerParentID) as PartnerParentID
                    ,a.DateActivation as ActivationDate
                    ,a.StatusCode as Status
                FROM
                    ktv_program_partner a
                    /* INNER JOIN reg_region rg ON a.RegID = rg.RegID
                    LEFT JOIN reg_country ct ON SUBSTR(a.`RegID`,1,2) = ct.`CountryID`
                    LEFT JOIN reg_province pv ON SUBSTR(a.`RegID`,1,4) = pv.ProvinceID
                    LEFT JOIN reg_district ds ON SUBSTR(a.`RegID`,1,8) = ds.DistrictID
                    LEFT JOIN reg_subdistrict sd ON SUBSTR(a.`RegID`,1,12) = sd.SubDistrictID
                    LEFT JOIN reg_village vl ON a.`RegID` = vl.VillageID */
                WHERE 1=1
                    AND a.`PartnerID` = ?
                LIMIT 1";
        $data = $this->db->query($sql, array($PartnerID))->row_array();

        $sql= "SELECT CommoID FROM ktv_partner_commodity Where PartnerID = ?";
        $query_selected = $this->db->query($sql,array($PartnerID))->result_array();

        foreach ($query_selected as $key) {
            $CommodityOptions[] = $key['CommoID'];
        }

        $data += ['CommodityOptions' => $CommodityOptions];


        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Partner.MainFormNew-FormBasicData-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $dataRow['Logo'] = $data['Logo'];

        //Check gambarnya ada tidak
        $this->load->library('awsfileupload');

        if ($dataRow['Logo'] != "") {
            //Cek ada tidak filenya di AWS
            if ($this->awsfileupload->doesObjectExist($dataRow['Logo']) == false) {
                $dataRow['Logo'] = null;
            }
        } else {
            $dataRow['Logo'] = null;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function getCommodityOptions(){
        $sql = "SELECT 
                    CommoID as id, CommoName as label
                from ktv_ref_commodity WHERE StatusCode = 'active'";
        $query = $this->db->query($sql);
        
        return array(
         'data'      => $query->result_array(),
        );
        
    }
}
?>
<?php
class Mprogram extends CI_Model {

    function readPrograms($start,$limit){
        $sql = "
            select %s
            from ktv_program_partner
            WHERE
               StatusCode != 'nullified'
            ORDER BY PartnerName %s";
        $query = $this->db->query(sprintf($sql,'PartnerID as id,PartnerName,
            IF(PartnerIndustry="0","Implementer",IF(PartnerIndustry="1","Donor",IF(PartnerIndustry="2","Trader",
               IF(PartnerIndustry="3","Processor",IF(PartnerIndustry="4","Manufacturer","Input Suplier"))))) as type,
            PartnerFullName,Photo,PartnerIndustry,PartnerProgramName','LIMIT ?,?'),
            array((int)$start,(int)$limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql,'count(*) as total',''));
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

    function readDistrictAll(){
        $sql = "SELECT
                    a.DistrictID as value,
                    concat(b.Province,' / ',a.District) as text
                    -- a.District as text
                    -- ,b.Province
                FROM ktv_district a
                    LEFT JOIN ktv_province b on a.ProvinceID=b.ProvinceID
                WHERE
                    b.active = '1'
                    AND b.StatusCode = 'active'
                    AND a.active = '1'
                    AND a.StatusCode = 'active'
                ORDER BY text ASC";
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

}
?>
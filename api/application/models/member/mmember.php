<?php

class Mmember extends CI_Model {
    
    
    public function __construct() {
        parent::__construct();
        $this->coop = getCoopID();
    }
    
    function readMembers($key, $status, $start, $limit,$newAddMember) {

        $sql = "
            SELECT
                a.`memberID` AS id,
                a.`farmerID`,
                a.`primaryNo`,
                a.`registeredDate`,
                a.`typeID`,
                a.`name`,
                a.`identityType`,
                a.`identityNumber`,
                a.`gender`,
                a.`placeOfBirth`,
                a.`dateOfBirth`,
                a.`address`,
                a.`villageID`,
                b.`Village`,
                c.`subDistrict`,
                d.`district`,
                a.`phone`,
                a.`maritalStatus`,
                a.`education`,
                a.`status`,
                a.`remark`,
                a.`photo`,
                a.`signature`,
                a.`familyName`,
                a.`familyAddress`,
                a.`familyRelation`,
                a.`familyIdentityType`,
                a.`familyIdentityNumber`,
                a.`familyPhone`,
                e.saldo as saldoSimpok,
                f.saldo as saldoWajib,
                g.saldo as saldoSuka,
				cpg.GroupName,
                a.uangPangkal,
                a.savingPokok,
                a.savingWajib
            FROM
                `coop_member` a
            LEFT JOIN ktv_village b ON a.`villageID` = b.`VillageID`
            LEFT JOIN coop_member_type e ON e.`typeID` = a.`typeID`
            LEFT JOIN ktv_subdistrict c ON c.`SubDistrictID` = b.`SubDistrictID`
            LEFT JOIN ktv_district d ON d.`DistrictID` = c.`DistrictID`
            LEFT JOIN ktv_farmer farmer ON farmer.farmerID = a.farmerID
            LEFT JOIN ktv_cpg cpg ON cpg.CPGid = farmer.CPGid
            left join (
                select memberID,(setoran-tarikan) as saldo
                from (
                    select a.memberID,a.memberSavingID,a.savingTypeID,b.savingTypeSHU,setoran,tarikan
                        from coop_member_saving a
                        join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                        join (select a.memberSavingID,IFNULL(setoran,0) as setoran,IFNULL(tarikan,0) as tarikan
                                from coop_member_saving a
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as setoran
                                    from coop_member_transaction
                                    where memberTransactionType=1
                                    GROUP BY memberSavingID) b ON a.memberSavingID = b.memberSavingID
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as tarikan
                                    from coop_member_transaction
                                    where memberTransactionType=2
                                    GROUP BY memberSavingID) c ON a.memberSavingID = c.memberSavingID) c ON a.memberSavingID = c.memberSavingID
                    where b.savingTypeSHU = 1
                ) a
            ) e ON a.memberID = e.memberID
            left join (
                select memberID,(setoran-tarikan) as saldo
                from (
                    select a.memberID,a.memberSavingID,a.savingTypeID,b.savingTypeSHU,setoran,tarikan
                        from coop_member_saving a
                        join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                        join (select a.memberSavingID,IFNULL(setoran,0) as setoran,IFNULL(tarikan,0) as tarikan
                                from coop_member_saving a
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as setoran
                                    from coop_member_transaction
                                    where memberTransactionType=1
                                    GROUP BY memberSavingID) b ON a.memberSavingID = b.memberSavingID
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as tarikan
                                    from coop_member_transaction
                                    where memberTransactionType=2
                                    GROUP BY memberSavingID) c ON a.memberSavingID = c.memberSavingID) c ON a.memberSavingID = c.memberSavingID
                    where b.savingTypeSHU = 2
                ) a
            ) f ON a.memberID = f.memberID
            left join (
                select memberID,(setoran-tarikan) as saldo
                from (
                    select a.memberID,a.memberSavingID,a.savingTypeID,b.savingTypeSHU,setoran,tarikan
                        from coop_member_saving a
                        join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                        join (select a.memberSavingID,IFNULL(setoran,0) as setoran,IFNULL(tarikan,0) as tarikan
                                from coop_member_saving a
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as setoran
                                    from coop_member_transaction
                                    where memberTransactionType=1
                                    GROUP BY memberSavingID) b ON a.memberSavingID = b.memberSavingID
                                left join(
                                    select memberSavingID,sum(memberTransactionAmount) as tarikan
                                    from coop_member_transaction
                                    where memberTransactionType=2
                                    GROUP BY memberSavingID) c ON a.memberSavingID = c.memberSavingID) c ON a.memberSavingID = c.memberSavingID
                    where b.savingTypeSHU = 3
                ) a
            ) g ON a.memberID = g.memberID";

            $sql .= " WHERE a.coopID = ".getCoopID()." ";

            if(strlen($key) > 0){
                $sql .= " AND (a.name LIKE '%$key%' OR a.primaryNo LIKE '%$key%' OR b.Village LIKE '%$key%' OR remark LIKE '%$key%')";
                // $key = "%$key%";
            } else {
                // if(strlen($status) > 0){
                //     $key = (int)$status;
                // } else {
                //     $key = null;
                // }
            }

            if(strlen($status) > 0){
                $sql .= " AND a.status = $status ";
            }

            // $sqltotal = " select memberID from coop_member a LEFT JOIN ktv_village b ON a.`villageID` = b.`VillageID`
            // LEFT JOIN coop_member_type e ON e.`typeID` = a.`typeID`
            // LEFT JOIN ktv_subdistrict c ON c.`SubDistrictID` = b.`SubDistrictID`
            // LEFT JOIN ktv_district d ON d.`DistrictID` = c.`DistrictID`
            // LEFT JOIN ktv_farmer farmer ON farmer.farmerID = a.farmerID
            // LEFT JOIN ktv_cpg cpg ON cpg.CPGid = farmer.CPGid WHERE e.coopID = ".getCoopID()."  AND (a.name LIKE '%$key%' OR a.primaryNo LIKE '%$key%' OR b.Village LIKE '%$key%' OR remark LIKE '%$key%')";

        $total = $this->db->query($sql)->num_rows();
        $orderType = "a.primaryNo";
        // print_r($newAddMember);
        if($newAddMember){
          $orderType = "a.memberID";
        }
		$sql .= " ORDER BY ". $orderType ." desc LIMIT ".$start.",".$limit;
        $query = $this->db->query($sql);
        // echo $this->db->last_query();die;
        $result['data'] = $query->result_array();

        // get total
        // $row = $this->db->query($sql);
        $result['total'] = $total;

        return $result;
    }

    function readMember($id) {
        $sql = "
            SELECT
                a.`memberID` AS id,
                a.`farmerID`,
                a.`primaryNo`,
                a.`registeredDate`,
                a.`typeID`,
                e.`typeName`,
                a.`name`,
                a.`identityType`,
                a.`identityNumber`,
                a.`gender`,
                a.`placeOfBirth`,
                a.`dateOfBirth`,
                a.`address`,
                a.`villageID`,
                b.`Village`,
                d.`DistrictID`,
                c.`SubDistrictID`,
                c.`subDistrict`,
                d.`district`,
                a.`phone`,
                a.job,
                a.`maritalStatus`,
                a.`education`,
                a.`status`,
                a.`remark`,
                a.`photo`,
                a.`signature`,
                a.`familyName`,
                a.`familyAddress`,
                a.`familyRelation`,
                a.`familyIdentityType`,
                a.`familyIdentityNumber`,
                a.`familyPhone`,
                CONCAT('/images/coop/members/', a.`photo`) AS memberPhoto,
                CONCAT('/images/coop/members/', a.`signature`) AS memberSignature,
                (IFNULL(SUM(DISTINCT g.`memberTransactionAmount`),0) - IFNULL(SUM(DISTINCT m.`memberTransactionAmount`),0)) AS simpananPokok,
                (IFNULL(SUM(DISTINCT i.`memberTransactionAmount`),0) - IFNULL(SUM(DISTINCT o.`memberTransactionAmount`),0))AS simpananWajib,
                (IFNULL(SUM(DISTINCT k.`memberTransactionAmount`),0) - IFNULL(SUM(DISTINCT q.`memberTransactionAmount`),0)) AS simpananSukarela,
                (IF(IFNULL(a.farmerID,0),1,0)) AS scpp,
                f.memberSavingID AS idSimpananPokok,
                h.memberSavingID AS idSimpananWajib,
                k.memberSavingID AS idSimpananSukarela,
                a.uangPangkal,
                a.savingPokok,
                a.savingWajib,
                r.isCertified
            FROM
                `coop_member` a
            LEFT JOIN ktv_village b ON a.`villageID` = b.`VillageID`
            LEFT JOIN ktv_subdistrict c ON c.`SubDistrictID` = b.`SubDistrictID`
            LEFT JOIN ktv_district d ON d.`DistrictID` = c.`DistrictID`
            LEFT JOIN coop_member_type e ON e.typeID = a.typeID
            LEFT JOIN coop_member_saving f ON f.`memberID` = a.`memberID` AND f.savingTypeID = 1
            LEFT JOIN coop_member_transaction g ON g.`memberSavingID` = f.`memberSavingID` AND g.memberTransactionType = 1
            LEFT JOIN coop_member_saving h ON h.`memberID` = a.`memberID` AND h.savingTypeID = 2
            LEFT JOIN coop_member_transaction i ON i.`memberSavingID` = h.`memberSavingID` AND i.memberTransactionType = 1
            LEFT JOIN coop_member_saving j ON j.`memberID` = a.`memberID` AND j.savingTypeID = 3
            LEFT JOIN coop_member_transaction k ON k.`memberSavingID` = j.`memberSavingID` AND k.memberTransactionType = 1

            LEFT JOIN coop_member_saving l ON l.`memberID` = a.`memberID` AND l.savingTypeID = 1
            LEFT JOIN coop_member_transaction m ON m.`memberSavingID` = l.`memberSavingID` AND m.memberTransactionType = 2
            LEFT JOIN coop_member_saving n ON n.`memberID` = a.`memberID` AND n.savingTypeID = 2
            LEFT JOIN coop_member_transaction o ON o.`memberSavingID` = n.`memberSavingID` AND o.memberTransactionType = 2
            LEFT JOIN coop_member_saving p ON p.`memberID` = a.`memberID` AND p.savingTypeID = 3
            LEFT JOIN coop_member_transaction q ON q.`memberSavingID` = p.`memberSavingID` AND q.memberTransactionType = 2
            LEFT JOIN ktv_farmer r ON a.FarmerID = r.FarmerID
            WHERE
                a.memberID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();

        if($result[0]['uangPangkal']===null)
        {
            //kalo null, uang pangkal diambil dari konfigurasi member type
            $q = $this->db->query("select b.RegistrationFee
                                        from coop_member a
                                        join coop_member_type b ON a.typeID = b.typeID
                                        where a.memberID = $id");
            if($q->num_rows()>0)
            {
                $r = $q->row();
                $result[0]['uangPangkal'] = $r->RegistrationFee;
            }
        }

        if($result[0]['savingPokok']===null)
        {
            //kalo null, savingPokok diambil dari konfigurasi saving type
            $q = $this->db->query("select b.AmountSaving
                                    from coop_member a
                                    join coop_member_saving b ON a.memberID = b.memberID
                                    where a.memberID = $id and b.savingTypeID = 1");
            if($q->num_rows()>0)
            {
                $r = $q->row();
                $result[0]['savingPokok'] = $r->AmountSaving;
            }
        }

        if($result[0]['savingWajib']===null)
        {
            //kalo null, savingWajib diambil dari konfigurasi saving type
            $q = $this->db->query("select b.AmountSaving
                                    from coop_member a
                                    join coop_member_saving b ON a.memberID = b.memberID
                                    where a.memberID = $id and b.savingTypeID = 2");
            if($q->num_rows()>0)
            {
                $r = $q->row();
                $result[0]['savingWajib'] = $r->AmountSaving;
            }
        }


//        $return['data'] = $result[0];
        return $result[0];
    }

    function readBalanceMember($memberID)
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                d.memberTransactionID AS `id`,
                d.`memberTransactionNumber`,
                c.`savingTypeCode`,
                d.`memberTransactionType`,
                d.`memberTransactionDate`,
                d.`memberTransactionAmount`,
                c.`savingTypeName`,
                b.`memberSavingNo`,
                a.`name`
            FROM
                `coop_member` a
            LEFT JOIN coop_member_saving b ON a.`memberID` = b.`memberID`
            LEFT JOIN coop_saving_type c ON c.`savingTypeID` = b.`savingTypeID`
            LEFT JOIN coop_member_transaction d ON d.memberSavingID = b.`memberSavingID`
            WHERE a.memberID = ? AND d.`memberSavingID` IS NOT NULL
            ORDER BY d.memberTransactionDate
        ";
        $query = $this->db->query($sql, array($memberID));

        $qmember = $this->db->query("select a.primaryNo,a.name
            from coop_member a
            where a.memberID = $memberID");
        $result['memberData'] = $qmember->result_array();
// $result['memberData'] = 'asdsad';
        $result['data'] = $query->result_array();
        return $result;
    }

    function getEmailByUserId($userid) {
        $sql = "
            SELECT
                UserName
            FROM sys_user
            WHERE UserId = ?
            ";
        $query = $this->db->query($sql, array($userid));
        $result = $query->result_array();
        return $result[0]['UserName'];
    }

    function createMember($data) {
        // $number = getMemberNumber($data['typeID']);
        $reapply = isset($data['reapply']) ? true : false;


        //Create Member
        $input = array(

            // 'primaryNo' => $number, //setelah active baru dapet
            // 'StatusMemberID'=>$data['StatusMemberID'],
            'registeredDate' => date('Y-m-d'),
            'typeID' => $data['typeID'],
            'name' => $data['name'],
            'identityType' => 1,
            'identityNumber' => $data['identityNumber'],
            'gender' => $data['gender'],
            'placeOfBirth' => $data['placeOfBirth'],
            'dateOfBirth' => $data['dateOfBirth'],
            'address' => $data['address'],
            'villageID' => $data['villageID'],
            'phone' => $data['phone'],
            'maritalStatus' => $data['maritalStatus'],
            'savingPokok' => str_replace(',', '', $data['RegSimpananPokok']),
            'savingWajib' => str_replace(',', '', $data['RegSimpananWajib']),
            'uangPangkal' => str_replace(',', '', $data['RegUangPangkal']),
            'job' => $data['job'],
            'status' => 4,
            'familyName' => $data['familyName'],
            'familyRelation' => $data['familyRelation'],
            'familyIdentityType' => 1,
            'familyIdentityNumber' => $data['familyIdentityNumber'],
            'familyAddress' => $data['familyAddress'],
            'familyPhone' => $data['familyPhone'],
            'CreatedBy' => $_SESSION['userid'],
            'CreatedDate' => date('Y-m-d'),
            'CoopID'=>getCoopID()
        );

        if(array_key_exists('farmerID', $data) && strlen($data['farmerID']) > 0){
            $input['farmerID'] = $data['farmerID'];
        }

        if(strlen($data['memberPhotoName']) > 0){
            $input['photo'] = $data['memberPhotoName'];
        }

        if(strlen($data['memberSigiName']) > 0){
            $input['signature'] = $data['memberSigiName'];
        }

        if($reapply)
        {

          // $results['success'] = true;
          //   $results['message'] = "record created.";
          //   return $results;

            $input['primaryNo'] = null;
            $input['memberRefID'] = $data['id'];
            // $this->db->where('memberID',$data['id']);
            $this->db->insert('coop_member',$input);

            //set to inactive old member
            $this->db->where('memberID',$data['id']);
            $this->db->update('coop_member',array('status'=>2));

            // $q = $this->db->get_where('coop_member',array('memberID'=>$data['id']))->row();

            $id = $data['id'];
        } else {
            $this->db->insert('coop_member',$input);
            $id = $this->db->insert_id();
        }



        //Create Primary Saving (simpok, simwa, sumsuk)
        if($id){
            
            $simpokok = array(
                'memberID' => $id,
                'CoopID' => $this->coop,
                'savingTypeID' => $this->_getSavingTypeCoop(1),
                'memberSavingRegisteredDate' => date('Y-m-d'),
                'memberSavingNo' => getSavingNumber(1),
                'memberSavingStatus' => 1,
                'CreatedBy' => $_SESSION['userid'],
                'CreatedDate' => date('Y-m-d'),
            );

            $this->db->insert('coop_member_saving',$simpokok);

           

            $simwajib = array(
                'memberID' => $id,
                'CoopID' => $this->coop,
                'savingTypeID' => $this->_getSavingTypeCoop(2),
                'memberSavingRegisteredDate' => date('Y-m-d'),
                'memberSavingNo' => getSavingNumber(2),
                'memberSavingStatus' => 1,
                'CreatedBy' => $_SESSION['userid'],
                'CreatedDate' => date('Y-m-d'),
            );
            
            $this->db->insert('coop_member_saving',$simwajib);
            
            $sukarela = array(
                'memberID' => $id,
                'CoopID' => $this->coop,
                'savingTypeID' => $this->_getSavingTypeCoop(4),
                'memberSavingRegisteredDate' => date('Y-m-d'),
                'memberSavingNo' => getSavingNumber(4),
                'memberSavingStatus' => 1,
                'CreatedBy' => $_SESSION['userid'],
                'CreatedDate' => date('Y-m-d'),
            );

            $this->db->insert('coop_member_saving',$sukarela);
            
            //var_dump($this->db->last_query());

            if (!$this->db->_error_number()) {
                $results['success'] = true;
                $results['message'] = "record created.";
            } else {
                $results['success'] = false;
                $results['message'] = "Failed to create record";
            }
        }

        return $results;
    }
    
    protected function _getSavingTypeCoop($type = false) {
        
        $this->db->select('SavingTypeID');
        $this->db->from('coop_saving_type');
        $this->db->where('CoopID', $this->coop);
        if($type){
            $this->db->where('SavingTypeSHU', $type);
        }
        $this->db->where('isPrimary', 1);
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->SavingTypeID;
        }
        return false;
    }
    
    function saveMemberImport($v)
    {
        $nums=0;
        foreach ($v as $key => $data) {

            $q = $this->db->get_where('coop_member_type',array('typeCode'=>$data[3]));
            if($q->num_rows()>0)
            {
                $r = $q->row();
                $typeid = $r->typeID;
            } else {
                $typeid = null;
            }

            $input = array(
                'registeredDate' => date('Y-m-d'),
                'typeID' => $typeid,
                'name' => $data[2],
                'identityType' => 1,
                'identityNumber' => $data[4],
                'gender' => $data[6],
                'placeOfBirth' => $data[7],
                'dateOfBirth' => $data[8],
                'address' => $data[5],
                'villageID' => $data[9],
                'phone' => $data[10],
                'maritalStatus' => $data[12],
                'job' => $data[11],
                'status' => 4,
                // 'familyName' => $data['familyName'],
                // 'familyRelation' => $data['familyRelation'],
                // 'familyIdentityType' => 1,
                // 'familyIdentityNumber' => $data['familyIdentityNumber'],
                // 'familyAddress' => $data['familyAddress'],
                // 'familyPhone' => $data['familyPhone'],
                'CreatedBy' => $_SESSION['userid'],
                'CreatedDate' => date('Y-m-d')
            );

            if(isset($data[1]) && $data[1]!=''){
                $input['farmerID'] = $data[1];
            }

            $this->db->insert('coop_member',$input);
            $nums++;
        }

        if ($nums>0) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }

        return $results;
    }

    function createMemberImport($data)
    {
        //Create Member
        $input = array(
            'registeredDate' => date('Y-m-d'),
            'typeID' => $data[3],
            'name' => $data[2],
            'identityType' => 1,
            'identityNumber' => $data[4],
            'gender' => $data[6],
            'placeOfBirth' => $data[7],
            'dateOfBirth' => $data[8],
            'address' => $data[5],
            'villageID' => $data[9],
            'phone' => $data[10],
            'maritalStatus' => $data[12],
            'job' => $data[11],
            'status' => 4,
            // 'familyName' => $data['familyName'],
            // 'familyRelation' => $data['familyRelation'],
            // 'familyIdentityType' => 1,
            // 'familyIdentityNumber' => $data['familyIdentityNumber'],
            // 'familyAddress' => $data['familyAddress'],
            // 'familyPhone' => $data['familyPhone'],
            'CreatedBy' => $_SESSION['userid'],
            'CreatedDate' => date('Y-m-d')
        );

        if(isset($data[1]) && $data[1]!=''){
            $input['farmerID'] = $data[1];
        }

        $this->db->insert('coop_member',$input);

        if (!$this->db->_error_number()) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }

        return $results;
    }

    function updateMember($id,$data) {

        $input = array(
            'name' => $data['name'],
            'identityNumber' => $data['identityNumber'],
            // 'StatusMemberID'=>$data['StatusMemberID'],
            'typeID' => $data['typeID'],
            'gender' => $data['gender'],
            'placeOfBirth' => $data['placeOfBirth'],
            'dateOfBirth' => $data['dateOfBirth'],
            'address' => $data['address'],
            'villageID' => $data['villageID'],
            'phone' => $data['phone'],
            'maritalStatus' => $data['maritalStatus'],
            'job' => $data['job'],
            'familyName' => $data['familyName'],
            'familyRelation' => $data['familyRelation'],
            'familyIdentityNumber' => $data['familyIdentityNumber'],
            'familyAddress' => $data['familyAddress'],
            'familyPhone' => $data['familyPhone'],
            'UpdatedBy' => $_SESSION['userid'],
            'UpdatedDate' => date('Y-m-d'),
        );

        if(array_key_exists('famerID', $data) && strlen($data['farmerID']) > 0){
            $input['farmerID'] = $data['farmerID'];
        }

        if(strlen($data['memberPhotoName']) > 0){
            $input['photo'] = $data['memberPhotoName'];
        }

        if(strlen($data['memberSigiName']) > 0){
            $input['signature'] = $data['memberSigiName'];
        }

        $this->db->where('memberID',$id);

        $query = $this->db->update('coop_member', $input);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteMember($id) {
        $sql = "
            DELETE
            FROM
              `coop_member`
            WHERE `memberID` = ?
        ";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    function getComboMemberType() {
        $sql = "
            SELECT
                a.`typeID`,
                a.`typeName`
            FROM
                `coop_member_type` a where coopID = ".getCoopID()."
            ORDER BY a.typeName ASC
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

	function getComboGroup($key = '') {
        $sql = "
            SELECT
                a.`CPGid`,
                a.`GroupName`
            FROM
                `ktv_cpg` a";
		$sql .= " INNER JOIN coop_area_member ON coop_area_member.VillageID = a.VillageID";
		if(strlen($key) > 0){
			$sql .= " WHERE GroupName LIKE '%".$key."%'";
		}
		$sql .= " ORDER BY a.GroupName ASC";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    function getComboDistrict() {
        $sql = "
            SELECT
                a.`DistrictID` AS `id`,
                a.`District` AS label
            FROM
                `ktv_district` a
        ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    function getComboSubdistrict($district_id) {
        $district = "";
        if ($district_id) {
            $district = "WHERE a.DistrictID = $district_id";
        }
        $sql = "
            SELECT
                b.`SubDistrictID` AS `id`,
                b.`SubDistrict` AS label
            FROM
                `ktv_district` a
            LEFT JOIN ktv_subdistrict b ON a.`DistrictID` = SUBSTR(b.`SubDistrictID`,1,4)
            %s
        ";
        $sql = sprintf($sql, $district);
        $query = $this->db->query($sql, array());
        $result['data'] = $query->result_array();
        return $result;
    }

    function getComboVillage($subdistrict_id) {
        $sql = "
            SELECT
                c.`VillageID` AS `id`,
                -- CONCAT(a.`District`, b.`SubDistrict`, c.`Village`) AS label
                c.`Village` AS label
            FROM
                `ktv_district` a
                LEFT JOIN ktv_subdistrict b ON a.`DistrictID` = a.DistrictID
                LEFT JOIN `ktv_village` c ON b.`SubDistrictID` = c.SubDistrictID
            WHERE b.SubDistrictID = ?
        ";
        $query = $this->db->query($sql, array($subdistrict_id));
        $result['data'] = $query->result_array();
        return $result;
    }

    function getComboIdentity() {
        $sql = "
            SELECT
                a.`typeID`,
                a.`typeName`
            FROM
                `coop_member_type` a
            ORDER BY a.typeName ASC
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        $result['data'] = array(
            array(
                'id' => 'ktp',
                'label' => 'KTP'
            ),
            array(
                'id' => 'sim',
                'label' => 'SIM'
            )
        );

        return $result;
    }

    function getComboStatus() {
        $result['data'] = array(
            array(
                'id' => '1',
                'label' => 'Active'
            ),
            // array(
            //     'id' => '2',
            //     'label' => 'Inactive'
            // ),
            array(
                'id' => '3',
                'label' => 'Suspended'
            )
            ,
            array(
                'id' => '4',
                'label' => 'Candidate'
            ),
            array(
                'id' => '5',
                'label' => 'Closed'
            )
        );

        return $result;
    }

    function readFarmers($key, $cpg, $start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                a.`FarmerID`,
                a.`FarmerName`,
                a.`VillageID`,
                b.Village,
                c.SubDistrict,
				GroupName,
                d.District,
                a.isCertified
            FROM
                `ktv_farmer` a
            LEFT JOIN ktv_village b ON b.VillageID = a.VillageID
            LEFT JOIN ktv_subdistrict c ON c.SubDistrictID = b.SubDistrictID
            LEFT JOIN ktv_district d ON d.DistrictID = c.DistrictID
            LEFT JOIN ktv_cpg cpg ON cpg.CPGid = a.CPGid
            WHERE a.StatusCode = 'active'
                AND (a.FarmerID LIKE ? OR a.FarmerName LIKE ?)
                AND a.VillageID IN (Select VillageID from coop_area_member where coop_area_member.CoopID = '".getCoopID()."')";
		if(strlen($cpg) > 0){
			$sql .= " AND (a.CPGid LIKE '".$cpg."')";
		}

        $sql .= "LIMIT ?,?";
        $query = $this->db->query($sql, array("%$key%", "%$key%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $total = $row->result_array();
        $result['total'] = $total[0]['total'];

        return $result;
    }

    function readFarmer($id) {
        $sql = "
            SELECT
                `FarmerID`,
                `OldFarmerID`,
                `CPGid`,
                `SubDistrictID`,
                'Petani' AS job,
                `OldCPGid`,
                `FarmerName` AS name,
                `DateCollection`,
                `Gender` AS gender,
                ktv_farmer.`VillageID` AS villageID,
                `Address` AS address,
                `HandPhone` AS phone,
                `MaritalStatus`,
                `Birthdate` AS dateOfBirth,
                `Education`,
                `Photo`,
                `Photo_base64`,
                `Latitude`,
                `Longitude`,
                `KeyFarmer`,
                `DemoPlot`,
                `OtherTraining`,
                `CPGmembership`,
                `OtherTrainingSiapa`,
                `OtherTrainingTahun`,
                `OtherTrainingLama`,
                `DemoPlotLama`,
                `DemoPlotRehab`,
                `FarmerGroupFunctionsID`,
                ktv_farmer.`DateCreated`,
                ktv_farmer.`CreatedBy`,
                ktv_farmer.`DateUpdated`,
                ktv_farmer.`LastModifiedBy`,
                ktv_farmer.`StatusCode`,
                `DeleteReason`,
                `isValid`,
                `isValidGarden`,
                `isValidPostHarvest`,
                `isValidNutrition`,
                `isValidPPIScore`,
                `ApprovedByME`,
                `ApprovedByGO`,
                `ApprovedByDC`,
                `CommentValid`,
                `LahanKakao`,
                `LahanProduksiLain`,
                `TotalLahan`,
                `KebunKakao`,
                `Elevation`,
                `LahanKosong`,
                `Muge`,
                `ActiveMemberCooperation`,
                `DateSurvey`,
                `DateSynced`,
                `StatusFarmer`,
                `DeceasedStatus`,
                `FamilyMemberID`,
                `MovedLeftArea`,
                `SwitchOtherCrop`
            FROM
                `ktv_farmer`
            left join ktv_village ON ktv_village.VillageID=ktv_farmer.VillageID
            WHERE FarmerID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function setStatusSavingMember($memberSavingID,$status)
    {
        $this->db->where('memberSavingID',$memberSavingID);
        $q = $this->db->update('coop_member_saving',array('memberSavingStatus'=>$status,'CoopID'=>getCoopID()));
        $results['success'] = true;
        $results['message'] = "data updated";
        return $results;
    }

    function GetSavingMember($memberID)
    {
        $sql = "select memberSavingID,memberID,a.savingTypeID,b.savingTypeName,memberSavingStatus
                from coop_member_saving a
                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                where memberID=? and savingTypeCode!='SP'";
        $query = $this->db->query($sql, array($memberID));

        $result = $query->result_array();
        return $result;
    }

    function SavingMember($memberID,$savingTypeID)
    {
        $data = array('memberID'=>$memberID,'savingTypeID'=>$savingTypeID,'memberSavingStatus'=>1);
        $q = $this->db->get_where('coop_member_saving',$data);
        if($q->num_rows()>0)
        {
//            $this->db->where($data);
//            $this->db->update('coop_member_saving',$data);
              $results['success'] = false;
            $results['message'] = "Data exists";
        } else {
            $data['memberSavingNo'] = getSavingNumber($savingTypeID);
            $data['memberSavingRegisteredDate'] = date('Y-m-d');
            $data['CreatedDate'] = date('Y-m-d H:m:s');
            $data['CreatedBy'] = $_SESSION['userid'];
            $data['CoopID'] = getCoopID();

            $this->db->insert('coop_member_saving',$data);
              if ($this->db->affected_rows()>0) {
                $results['success'] = true;
                $results['message'] = "data created.";
            } else {
                $results['success'] = false;
                $results['message'] = "Failed to create record".$this->db->last_query();
            }
        }


        return $results;
    }

    function getSavingByMember($id, $start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                d.memberTransactionID AS `id`,
                d.`memberTransactionNumber`,
                d.`memberTransactionType`,
                d.`memberTransactionDate`,
                d.`memberTransactionAmount`,
                c.`savingTypeName`,
                b.`memberSavingNo`
            FROM
                `coop_member` a
            LEFT JOIN coop_member_saving b ON a.`memberID` = b.`memberID`
            LEFT JOIN coop_saving_type c ON c.`savingTypeID` = b.`savingTypeID`
            LEFT JOIN coop_member_transaction d ON d.memberSavingID = b.`memberSavingID`
            WHERE a.memberID = ? AND d.`memberSavingID` IS NOT NULL
            ORDER BY d.memberTransactionDate
            %s
        ";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array($id, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        // get total
        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());

        $total = $row->result_array();
        $result['total'] = $total[0]['total'];

        return $result;
    }

    function getComboTransactionType() {
        $result['data'] = array(
            array(
                'id' => '1',
                'label' => 'Setoran'
            ),
            array(
                'id' => '2',
                'label' => 'Tarikan'
            )
        );

        return $result;
    }

    function createTransaction($memberTransactionType, $memberTransactionDate, $cashSourceID, $memberSavingID, $memberTransactionAmount, $memberTransactionRemark, $userid) {
        $sql = "INSERT INTO `coop_member_transaction` (
                `memberTransactionType`,
                `memberTransactionDate`,
                `cashSourceID`,
                `memberSavingID`,
                `memberTransactionAmount`,
                `memberTransactionRemark`,
                CreatedBy,
                CreatedDate
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW()
            )
        ";
        $query = $this->db->query($sql, array($memberTransactionType, $memberTransactionDate, $cashSourceID, $memberSavingID, $memberTransactionAmount, $memberTransactionRemark, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function getSummaryTrans2($memberSavingID)
    {
         $sql = "select a.memberSavingID,setoran,tarikan
                    from coop_member_saving a
                    left join(
                            select memberSavingID,sum(memberTransactionAmount) as setoran
                            from coop_member_transaction
                            where memberTransactionType=1
                            GROUP BY memberSavingID) b ON a.memberSavingID = b.memberSavingID
                    left join(
                            select memberSavingID,sum(memberTransactionAmount) as tarikan
                            from coop_member_transaction
                            where memberTransactionType=2
                            GROUP BY memberSavingID) c ON a.memberSavingID = c.memberSavingID
                    where a.memberSavingID=$memberSavingID";
            $q2 = $this->db->query($sql);
            if($q2->num_rows()>0)
            {
                $r2 = $q2->row();
                if($r2->setoran==null && $r2->tarikan==null)
                {
                    return 0;
                } else {
                    $saldo = $r2->setoran-$r2->tarikan;
                    return $saldo;
                }
            } else {
                return 0;
            }
    }

    function getSummaryTrans($id)
    {
        $data = array();
        $q = $this->db->query("select savingTypeId from coop_saving_type");
        $i=1;
        foreach($q->result() as $r)
        {
//            $r = $q->row();
//            echo $r->savingTypeId."<br>";
            $sql = "select a.memberSavingID,sum(setoran-tarikan) as saldo
                    from coop_member_saving a
                    left join(
                            select memberSavingID,sum(memberTransactionAmount) as setoran
                            from coop_member_transaction
                            where memberTransactionType=1
                            GROUP BY memberSavingID) b ON a.memberSavingID = b.memberSavingID
                    left join(
                            select memberSavingID,sum(memberTransactionAmount) as tarikan
                            from coop_member_transaction
                            where memberTransactionType=2
                            GROUP BY memberSavingID) c ON a.memberSavingID = c.memberSavingID
                    where memberID=$id and savingTypeID=$r->savingTypeId";
            $q2 = $this->db->query($sql);
            if($q2->num_rows()>0)
            {
                $r2 = $q2->row();
                $data[]['typeid'.$r->savingTypeId] = number_format($r2->saldo);
            } else {
//                $data[][$r->savingTypeId] = 0;
            }
            $i++;
        }

        return $data;
    }

    function loanMemberData($id)
    {
        $sql = "select a.LoanInstallmentID,a.LoanInstallmentDueDate,a.LoanInstallmentNum,a.MemberLoanID,a.LoanInstallmentValue,a.LoanInstallmentPinalty,a.LoanInstallmentTotal,
                            a.LoanInstallmentPaidDate,c.LoanTypeName,a.LoanInstallmentInterestPercent,a.LoanInstallmentInterest
                            from coop_loan_installment a
                            join coop_member_loan b ON a.MemberLoanID = b.MemberLoanID
                            join coop_loan_type c ON b.LoanTypeID = c.LoanTypeID
                            where b.MemberID = $id";

        $total = $this->db->query($sql)->num_rows();

        $sql .= " ORDER BY a.LoanInstallmentID desc limit 100";
        $query = $this->db->query($sql);

        $result['data'] = $query->result_array();

        $result['total'] = $total;

        return $result;
    }

    function loanMemberSummary($id=null)
    {
        $totalPaid = 0;
        $totalLoan = 0;
        
        if($id!=null)
        {
            $wer = "where b.MemberID = $id";
        } else {
            $wer = null;
        }
        $sql = "select sum(LoanInstallmentTotal) as totalPaid
                from coop_loan_installment a
                join coop_member_loan b ON a.MemberLoanID = b.MemberLoanID
                $wer";

        $query = $this->db->query($sql);
        if($query->num_rows() > 0){
            $result = $query->result_array();
            $totalPaid = $result[0]['totalPaid'];
            $d['totalPaid'] = number_format($result[0]['totalPaid']);
        }
        $sql = "select sum(MemberLoanProposedAmount) as totalLoan
                from coop_member_loan b
                $wer";

        $query = $this->db->query($sql);
        if($query->num_rows() > 0) {
            $result = $query->result_array();
            $d['totalLoan'] = number_format($result[0]['totalLoan']);
            $totalLoan = $result[0]['totalLoan'];
        }
        $sql = "select sum(LoanInstallmentInterest) as totalInterest
                from coop_member_loan b
                join coop_loan_installment c ON b.MemberLoanID = c.MemberLoanID
                $wer";

        $query = $this->db->query($sql);
        $result = $query->result_array();
        $d['totalInterest'] = number_format($result[0]['totalInterest']);

        $sql = "select sum(MemberLoanProposedAmount) as totalLoan
                from coop_member_loan b
                $wer";
        
        $oustanding = $totalPaid-$totalLoan;
        $d['totalOutstanding'] = number_format($oustanding);
        
        return $d;
    }

    function getDetailMember($id) {
        $sql = "
            SELECT
                a.`memberID`,
                b.`memberSavingNo`,
                c.`savingTypeName`,
                a.`farmerID`,
                a.`primaryNo`,
                a.`registeredDate`,
                a.`typeID`,
                a.`name`,
                a.`identityType`,
                a.`identityNumber`,
                a.`gender`,
                a.`placeOfBirth`,
                a.`dateOfBirth`,
                a.`address`,
                a.`villageID`,
                a.`phone`,
                a.`maritalStatus`,
                a.`education`,
                a.`status`,
                a.`remark`,
                a.`photo`,
                a.`familyName`,
                a.`familyRelation`,
                a.`familyIdentityType`,
                a.`familyIdentityNumber`,
                a.`familyAddress`,
                a.`familyPhone`,
                b.`savingTypeID`
            FROM
                `coop_member` a
            LEFT JOIN coop_member_saving b ON a.memberID = b.memberID
            LEFT JOIN coop_saving_type c ON c.savingTypeID = b.savingTypeID
            WHERE b.memberSavingID = ?
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createSaving($memberID, $savingTypeID, $memberSavingNo, $memberSavingRemark, $userid) {
        $sql = "
            INSERT INTO `coop_member_saving` (
                `memberID`,
                `savingTypeID`,
                `memberSavingRegisteredDate`,
                `memberSavingNo`,
                `memberSavingStatus`,
                `memberSavingRemark`,
                `CreatedBy`,
                `CreatedDate`
            )
            VALUES
            (
                ?,
                ?,
                NOW(),
                ?,
                1,
                ?,
                ?,
                NOW()
            )
        ";
        $query = $this->db->query($sql, array($memberID, $savingTypeID, $memberSavingNo, $memberSavingRemark, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    
    /**
     * 
     * @param type $id MemberID
     * @param type $status (1: Active, 2:Inactive)
     * @return success/failed
     * 
     */
    function updateStatus($id,$status) {

        // $this->db->select('savingTypeID');
        // $qSavType = $this->db->get_where('coop_saving_type',array('coopID'=>getCoopID(),'savingTypeDefault'=>1));
        if($r->Status==1)
        {
            $results['success'] = false;
            $results['message'] = "Anggota sudah aktif";
            return $results;
        }

        //update member status dan nomor anggota
        $primaryNo = getMemberNumber($r->TypeID);

        $this->db->where('memberID',$id);
        $this->db->set('status',$status);
        $this->db->set('primaryNo',$primaryNo);
        $query = $this->db->update('coop_member');
            
        if ($this->db->affected_rows()>0) {
            $this->migrasi_member($id);
            $results['success'] = true;
            $results['message'] = "data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        }
        
        return $results;
    }

    function saveDepositPokok($MemberID,$coopID)
    {
        $this->load->model('coop/mtransaction');

        $q = $this->db->query("select a.MemberSavingID,b.SavingTypeMonthlyFee
                                from coop_member_saving a
                                join coop_saving_type b ON a.SavingTypeID = b.SavingTypeID
                                where b.SavingTypeSHU = 1 and a.MemberID = ".$MemberID."");
        if($q->num_rows()>0)
        {
            $data = $q->result_array()[0];
        } else {
            echo 'Membersaving ID not found';
        }

         $transactionNumber = getTransactionNumber(1);

         // $this->mtransaction->createTransaction($form,1);

        $values = array(
            'MemberTransactionType' => 1,
            'MemberID' => $MemberID,
            'MemberSavingID' => $data['memberSavingID'],
            'MemberTransactionNumber' => $transactionNumber,
            'MemberTransactionDate' => date('Y-m-d'),
            'MemberTransactionName' => 'System Generated',
            // 'memberTransactionIdentity' => isset($data['identityNumber']) ? $data['identityNumber'] : null,
            // 'memberTransactionAddress' => isset($data['address']) ? $data['address'] : null,
            'MemberTransactionAmount' => $data['savingTypeMonthlyFee'],
            // 'memberTransactionCurrentBalance' => $balance,
            'MemberTransactionRemark' => 'Deposit Simpanan Pokok',
            // 'ApprovedBy' => isset($data['UserId']) ? $data['UserId'] : null,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'CreatedBy' => $_SESSION['userid']
        );
        // return $values;
        $query = $this->db->insert('coop_member_transaction', $values);

        if($this->db->affected_rows()>0)
        {
            return true;
        } else {
            return false;
        }
    }

    function SavingMemberByDefault($id,$type)
    {
        $CoopID = getCoopID();
        $this->db->join('coop_saving_type_member','coop_saving_type_member.SavingTypeID = coop_saving_type.SavingTypeID AND coop_saving_type_member.TypeID = '.$type,'inner');
        $q = $this->db->get_where('coop_saving_type',array(
            'SavingTypeDefault'=>1,
            'coop_saving_type_member.CoopID'=>getCoopID(),
            'TypeID' => $type, 
            'isPrimary' => 2
        ));

        if($q->num_rows()>0)
        {
            // $r = $q->row();
            foreach ($q->result() as $r) {
                $data['MemberSavingNo'] = getSavingNumber($r->SavingTypeID);
                $data['MemberID'] = $id;
                $data['SavingTypeID'] = $r->SavingTypeID;
                $data['MemberSavingStatus'] = 1; //active
                $data['MemberSavingRegisteredDate'] = date('Y-m-d');
                $data['CreatedDate'] = date('Y-m-d H:m:s');
                $data['CreatedBy'] = $_SESSION['userid'];
                $data['CoopID'] = $CoopID;

                $qcek = $this->db->get_where('coop_member_saving',array('MemberID'=>$id,'SavingTypeID'=>$r->SavingTypeID));

                if($qcek->num_rows()>0)
                {
                    $rr = $qcek->row();
                    $this->db->where('MemberSavingID',$rr->MemberSavingID);
                    $this->db->update('coop_member_saving',$data);

                    $memberSavingID = $rr->MemberSavingID;
                } else {
                    $this->db->insert('coop_member_saving',$data);
                    $memberSavingID = $this->db->insert_id();
                }

                //simpanan pokok
                if($r->SavingTypeSHU==1)
                {
                    $number = getTransactionNumber(1);

                     $values = array(
                        'CoopID' => $CoopID,
                        'MemberTransactionType' => 1, //deposit
                        'MemberID' => $id,
                        'MemberSavingID' => $memberSavingID,
                        'MemberTransactionNumber' => $number,
                        'MemberTransactionDate' => date('Y-m-d'),
                        // 'memberTransactionName' => $data['name'],
                        // 'memberTransactionIdentity' => isset($data['identityNumber']) ? $data['identityNumber'] : null,
                        // 'memberTransactionAddress' => isset($data['address']) ? $data['address'] : null,
                        'MemberTransactionAmount' => $r->SavingTypeMonthlyFee,
                        'MemberTransactionCurrentBalance' => 0,
                        'MemberTransactionRemark' => 'Setoran Awal Anggota',
                        'ApprovedBy' => $_SESSION['userid'],
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'CreatedBy' => $_SESSION['userid']
                    );

                    $query = $this->db->insert('coop_member_transaction', $values);
                }
            }
            $results['success'] = true;
            $results['message'] = 'Success';
        } else {
            $results['success'] = false;
            $results['message'] = "Gagal mengubah status: Belum ada pengaturan awal tabungan";
        }

        return $results;
    }

    // function SavingMemberIsPrimary($mid,$primaryNo){
    //     $CoopID = getCoopID();

    //     $this->db->from('coop_saving_type');
    //     $this->db->where('SavingTypeSHU', 1); //tipe simpanan pokok
    //     $this->db->where('SavingTypeSHU', 2); //tipe simpanan wajib
    //     $this->db->where('CoopID', $CoopID);
        
    //     $get = $this->db->get();

    //     foreach ($get->result_array() as $key => $val) {
    //         $dt = array(
    //             'CoopID' => $CoopID,
    //             'MemberID' => $mid,
    //             'SavingTypeID' => $val['SavingTypeID'],
    //             'MemberSavingRegisteredDate' =>
    //         );
    //     }
    // }

    function closeMember($data)
    {
        $input = array(
            'ResignationDate' => $data['ResignationDate'],
            'ResignationReason' => $data['ResignationReason'],
            'status' => 2
        );

        $this->db->where('memberID',$data['memberID']);
        $query = $this->db->update('coop_member',$input);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        }
        return $results;
    }

    function readCloseMember($id)
    {
        $this->db->select('ResignationDate,ResignationReason,memberID');
        $query = $this->db->get_where('coop_member',array('memberID'=>$id));
        $result = $query->result_array();
        return $result[0];
    }

    function checkSaving($id)
    {
        //buat ngecek udah ada data product saving belum di coop_member_saving
        $q = $this->db->get_where('coop_member_saving',array('memberID'=>$id));
        if($q->num_rows()>0)
        {
            $results['success'] = true;
        } else {
            $results['success'] = false;
        }
        return $results;
    }

    function savingMemberData($id)
    {
        $query = $this->db->query("select a.memberSavingID,a.memberID,a.memberSavingNo,savingTypeName,
                                case
                                    when a.AmountSaving = 0 THEN b.savingTypeMinAmount
                                    else a.AmountSaving
                                END as savingTypeMinAmount
                            from coop_member_saving a
                            join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                            where memberID=$id");
        // $result = $query->result_array();
        $result['data'] = $query->result_array();
        $result['total'] = count($query);

        return $result;
    }

    function savingSavingSetup($data)
    {
        $this->db->where('memberSavingID',$data['memberSavingID']);
        $this->db->update('coop_member_saving',array('AmountSaving'=>str_replace('.00', '', $data['amountSaving'])));
        if($this->db->affected_rows()>0)
        {
            $results['success'] = true;
        } else {
            $results['success'] = false;
        }
        return $results;
    }
    
    
    public function migrasi_member($id) {
        
        if($id){
            
            $simpokok = array(
                'memberID' => $id,
                'CoopID' => $this->coop,
                'savingTypeID' => $this->_getSavingTypeCoop(1),
                'memberSavingRegisteredDate' => date('Y-m-d'),
                'memberSavingNo' => getSavingNumber(1),
                'memberSavingStatus' => 1,
                'CreatedBy' => $_SESSION['userid'],
                'CreatedDate' => date('Y-m-d'),
            );

            $this->db->insert('coop_member_saving',$simpokok);



            $simwajib = array(
                'memberID' => $id,
                'CoopID' => $this->coop,
                'savingTypeID' => $this->_getSavingTypeCoop(2),
                'memberSavingRegisteredDate' => date('Y-m-d'),
                'memberSavingNo' => getSavingNumber(2),
                'memberSavingStatus' => 1,
                'CreatedBy' => $_SESSION['userid'],
                'CreatedDate' => date('Y-m-d'),
            );

            $this->db->insert('coop_member_saving',$simwajib);

            $sukarela = array(
                'memberID' => $id,
                'CoopID' => $this->coop,
                'savingTypeID' => $this->_getSavingTypeCoop(4),
                'memberSavingRegisteredDate' => date('Y-m-d'),
                'memberSavingNo' => getSavingNumber(4),
                'memberSavingStatus' => 1,
                'CreatedBy' => $_SESSION['userid'],
                'CreatedDate' => date('Y-m-d'),
            );

            $this->db->insert('coop_member_saving',$sukarela);

        }
        
    }

}

?>

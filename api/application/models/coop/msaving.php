<?php

class Msaving extends CI_Model {

    function readSavings($key, $status, $start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                a.`memberSavingID` AS `id`,
                a.`memberID`,
                b.`primaryNo`,
                b.`name`,
                a.`savingTypeID`,
                c.`savingTypeName`,
                c.`savingTypeMinAmount`,
                c.`savingTypeMinTrans`,
                c.`savingTypeInterestRate`,
                a.`memberSavingRegisteredDate`,
                a.`memberSavingNo`,
                a.`memberSavingStatus`,
                a.`memberSavingRemark` 
            FROM
                `coop_member_saving` a
            LEFT JOIN coop_member b ON a.`memberID` = b.`memberID`
            LEFT JOIN coop_saving_type c ON c.`savingTypeID` = a.`savingTypeID`
            WHERE b.name LIKE ? AND a.memberSavingStatus LIKE ? GROUP BY a.memberID
            %s 
        ";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array("%$key%", "%$status%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        // get total
        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $result['total'] = $row->result_array();

        return $result;
    }

    function readSaving($id) {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.`memberSavingID` AS `id`,
                a.`memberID`,
                b.`primaryNo`,
                b.`name`,
                a.`savingTypeID`,
                c.`savingTypeName`,
                c.`savingTypeMinAmount`,
                c.`savingTypeMinTrans`,
                c.`savingTypeInterestRate`,
                a.`memberSavingRegisteredDate`,
                a.`memberSavingNo`,
                a.`memberSavingStatus`,
                a.`memberSavingRemark` 
            FROM
                `coop_member_saving` a
            LEFT JOIN coop_member b ON a.`memberID` = b.`memberID`
            LEFT JOIN coop_saving_type c ON c.`savingTypeID` = a.`savingTypeID`
            WHERE a.memberSavingID = ?
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

    function updateSaving($memberID, $savingTypeID, $memberSavingNo, $memberSavingRemark, $userid, $id) {
        $sql = "
            UPDATE 
                `coop_member_saving` 
            SET
                `memberID` = ?,
                `savingTypeID` = ?,
                `memberSavingNo` = ?,
                `memberSavingRemark` = ?,
                `UpdatedBy` = ?,
                `UpdatedDate` = NOW(),
                `SyncedDate` = ?
            WHERE `memberSavingID` = ?
        ";
        $query = $this->db->query($sql, array($memberID, $savingTypeID, $memberSavingNo, $memberSavingRemark, $userid, null, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteSaving($id) {
        $sql = "
            DELETE FROM
                `coop_member_saving` 
            WHERE `memberSavingID` = ?
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

    function getMember($id) {
        $sql = "
            SELECT 
                `memberID`,
                `farmerID`,
                `primaryNo`,
                `registeredDate`,
                `typeID`,
                `name`,
                `identityType`,
                `identityNumber`,
                `gender`,
                `placeOfBirth`,
                `dateOfBirth`,
                `address`,
                `villageID`,
                `phone`,
                `maritalStatus`,
                `education`,
                `status`,
                `remark`,
                `photo`,
                `familyName`,
                `familyRelation`,
                `familyIdentityType`,
                `familyIdentityNumber`,
                `familyAddress`,
                `familyPhone`,
                `CreatedBy`,
                `CreatedDate`,
                `UpdatedBy`,
                `UpdatedDate` 
            FROM
                `coop_member`
            WHERE memberID = ?
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function getSavingByMember($id, $start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                a.`memberSavingID` AS `id`,
                a.`memberID`,
                b.`primaryNo`,
                b.`name`,
                a.`savingTypeID`,
                c.`savingTypeName`,
                c.`savingTypeMinAmount`,
                c.`savingTypeMinTrans`,
                c.`savingTypeInterestRate`,
                a.`memberSavingRegisteredDate`,
                a.`memberSavingNo`,
                a.`memberSavingStatus`,
                a.`memberSavingRemark` 
            FROM
                `coop_member_saving` a
            LEFT JOIN coop_member b ON a.`memberID` = b.`memberID`
            LEFT JOIN coop_saving_type c ON c.`savingTypeID` = a.`savingTypeID`
            WHERE a.memberID = ?
            %s
        ";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array($id, (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        // get total
        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
//        $row = $row->result_array();
//        $result['total'] = $row['total'];
        $result['total'] = $row->result_array();

        return $result;
    }

    function getSavingType($id) {
        $sql = "
            SELECT 
                `savingTypeID`,
                `savingTypeName`,
                `savingTypeMinAmount`,
                `savingTypeMinTrans`,
                `savingTypeInterestRate`,
                `savingRemark`
            FROM
                `coop_saving_type`
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function getComboSavingType() {
        $sql = "
            SELECT 
                `savingTypeID` AS `id`,
                `savingTypeName` AS `label`
            FROM
                `coop_saving_type` 
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    function getComboMember() {
        $sql = "
            SELECT 
                `memberID` AS `id`,
                CONCAT('[',`primaryNo`,'] ',`name`) AS label
            FROM
                `coop_member` 
        ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    function getComboStatus() {
        $result['data'] = array(
            array(
                'id' => '1',
                'label' => 'Active'
            ),
            array(
                'id' => '2',
                'label' => 'Inactive'
            )
        );

        return $result;
    }

    function getDataDeposito($CoopID,$sd=null,$nd=null)
    {
         $sql = "select a.memberSavingID,a.memberID,b.savingTypeName,minAmountDepositLimit,lengthDeposito,memberSavingRegisteredDate,a.MemberSavingID,
                    metodeDeposito,pajakDeposito,bungaDeposito,depositoAutoJurnal,sum(MemberTransactionAmount) as totalDeposito,d.name,
                    a.memberSavingNo,e.amount,e.CreatedDate,b.savingTypeName
                    from coop_member_saving a
                    join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                    join coop_member_transaction c ON a.memberSavingID = c.MemberSavingID
                    join coop_member d ON a.memberID = d.memberID and a.CoopID = d.CoopID
                    join coop_deposit_interest e ON a.memberSavingID = e.memberSavingID and a.CoopID = e.CoopID
                where a.CoopID = $CoopID";

        if($sd!=null && $nd!=null)
        {
            // $d1 = explode('-', $sd);
            // $d2 = explode('-', $nd);
            $sql.=" and a.Date between '".$sd."' and '".$nd."'";
        }

        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        $result['total'] = $query->num_rows();
        return $result;
    }

}

?>

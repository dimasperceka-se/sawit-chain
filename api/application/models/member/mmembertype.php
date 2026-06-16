<?php

class Mmembertype extends CI_Model {

    var $coop;

    function __Construct() {
        parent::__Construct();
        $this->coop = getCoopID();
    }

    function readMemberTypes($key, $start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                `typeID` AS `id`,
                `coopID`,
                `typeCode`,
                `typeName`,
                `typeMaxProfit`,
                `typeSimPokokAmount`,
                `typeSimWajibAmount`,
                `CreatedBy`,
                `CreatedDate`,
                `UpdatedBy`,
                `UpdatedDate`,
                `typeSimPokokPeriod`,
                `typeSimWajibPeriod`
            FROM
                `coop_member_type`
            WHERE coopID = ".$this->coop." AND typeName LIKE ?
            %s
        ";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array("%$key%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $total = $row->result_array();
        $result['total'] = $total[0]['total'];

        return $result;
    }

    function readMemberTypeList() {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                `typeID` AS `id`,
                `coopID`,
                `typeCode`,
                `typeName`,
                `typeMaxProfit`,
                `typeSimPokokAmount`,
                `typeSimWajibAmount`,
                `CreatedBy`,
                `CreatedDate`,
                `UpdatedBy`,
                `UpdatedDate`,
                `typeSimPokokPeriod`,
                `typeSimWajibPeriod`
            FROM
                `coop_member_type` where coopID = ".getCoopID();
        $query = $this->db->query(sprintf($sql, ''), array());
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $total = $row->result_array();
        $result['total'] = $total[0]['total'];

        return $result;
    }

    function readMemberType($id) {
        $sql = "
            SELECT
                `typeID`,
                a.`coopID`,
                `typeCode`,
                `typeName`,
                `typeMaxProfit`,
                `typeSimPokokAmount`,
                `typeSimWajibAmount`,
                a.`CreatedBy`,
                a.`CreatedDate`,
                a.`UpdatedBy`,
                a.`UpdatedDate`,
                `typeSimPokokPeriod`,
                `typeSimWajibPeriod` ,
                `CoaRegMemberTypeID`,
                `RegistrationFee`,
                `coaTitle` as CoaRegMemberTypeName
            FROM
                `coop_member_type` a
            LEFT JOIN accounting_coa b ON a.CoaRegMemberTypeID = b.coaID
            WHERE typeID = ?
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createMemberType($coopID, $typeCode, $typeName, $maxProfit, $simPokokAmount, $simPokokPeriod, $simWajibAmount, $simPokokPeriod, $CoaRegMemberTypeID, $RegistrationFee, $userid) {

        $sql = "
            INSERT INTO `coop_member_type` (
                `coopID`,
                `typeCode`,
                `typeName`,
                `typeMaxProfit`,
                `typeSimPokokAmount`,
                `typeSimPokokPeriod`,
                `typeSimWajibAmount`,
                `typeSimWajibPeriod`,
                `CoaRegMemberTypeID`,
                `RegistrationFee`,
                `CreatedBy`,
                `CreatedDate`
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
                ?,
                ?,
                ?,
                ?,
                NOW()
            )
        ";
        $query = $this->db->query($sql, array($this->coop, $typeCode, $typeName, $maxProfit, str_replace(',', '', $simPokokAmount), $simPokokPeriod,
            str_replace(',', '', $simWajibAmount), $simPokokPeriod, 0, str_replace(',', '', $RegistrationFee), $userid));


        //get all isPrimary saving_type on this Coop, then auto create for this user
        $membertypeid = $this->db->insert_id();
        $this->db->from('coop_saving_type');
        $this->db->where('isPrimary', 1);
        // $this->db->or_where('SavingTypeDefault', 1);
        $this->db->where('CoopID', $this->coop);

        $stm = $this->db->get();
        $found = $stm->num_rows();

        if($found >= 1){
            foreach ($stm->result_array() as $st) {
                $idt = array(
                    'CoopID' => $this->coop,
                    'SavingTypeID' => $st['SavingTypeID'],
                    'TypeID' => $membertypeid
                );                
                $this->db->insert('coop_saving_type_members',$idt);
            }
        }

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Member Type Created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateMemberType($id,$data) {
        $sql = "
            UPDATE
                `coop_member_type`
            SET
                `typeCode` = ?,
                `typeName` = ?,
                `typeMaxProfit` = ?,
                `typeSimPokokAmount` = ?,
                `typeSimPokokPeriod` = ?,
                `typeSimWajibAmount` = ?,
                `typeSimWajibPeriod` = ?,
                `CoaRegMemberTypeID` = ?,
                `RegistrationFee` = ?,
                `UpdatedBy` = ?,
                `UpdatedDate` = NOW()
            WHERE `typeID` = ?
        ";
        $data = array(
            $data['typeCode'],
            $data['typeName'],
            $data['typeMaxProfit'],
            $data['typeSimPokokAmount'],
            $data['typeSimPokokPeriod'],
            $data['typeSimWajibAmount'],
            $data['typeSimWajibPeriod'],
            $data['CoaRegMemberTypeID'],
            str_replace(',', '', $data['RegistrationFee']),
            $_SESSION['userid'],
            $id
        );
        $query = $this->db->query($sql, $data);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Member Type Updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update Member Type";
        }
        return $results;
    }

    function deleteMemberType($id) {

        //cek dulu udah ada yang make blom typeid nya di coop_member
        $q = $this->db->get_where('coop_member',array('typeID'=>$id));
        if($q->num_rows() > 0)
        {
            $r = $q->row();
            $results['success'] = false;
            $results['message'] = "Tidak bisa dihapus karena tipe member  sedang digunakan";
        } else {
            $sql = "
                DELETE
                    FROM
                        `coop_member_type`
                    WHERE `typeID` = ?
                ";
            $query = $this->db->query($sql, array($id));
            if ($query) {
                $results['success'] = true;
                $results['message'] = "Member Type Deleted.";
            } else {
                $results['success'] = false;
                $results['message'] = "Failed to delete Member Type";
            }
        }


        return $results;
    }

}

?>

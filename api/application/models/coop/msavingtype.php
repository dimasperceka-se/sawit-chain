<?php

class Msavingtype extends CI_Model {

    var $coop;
    
    function __Construct() {
        parent::__construct();
        $this->coop = getCoopID();
    }
    
    // function readSavingTypes($key, $start, $limit) {
    //     $sql = "
    //         SELECT SQL_CALC_FOUND_ROWS
    //             `savingTypeID` AS id,
    //             `savingTypeName`,
    //             `savingTypeDefault`,
    //             `savingTypeMinAmount`,
    //             `savingTypeMinTrans`,
    //             `savingTypeInterestRate`,
    //             `savingRemark`,
    //             IF(savingTypeStatus = 1, 'Active','Inactive') as status
    //         FROM
    //             `coop_saving_type` 
    //         WHERE coopID = ".$this->coop." AND `savingTypeName` LIKE ?
    //         %s
    //     ";
    //     $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array("%$key%", (int) $start, (int) $limit));
    //     $result['data'] = $query->result_array();

    //     $sql_row = "SELECT FOUND_ROWS() AS total";
    //     $row = $this->db->query($sql_row, array());
    //     $result['total'] = $row->result_array();

    //     return $result;
    // }

    function readSavingTypes($start = 0, $limit = 20, $sort = 'SavingTypeID', $dir = 'DESC', $filter = array()){

        $this->db->from('coop_saving_type');
        $this->db->where('CoopID', $this->coop);
        
        $this->db->like($filter);

        $total = $this->db->_compile_select();
        $total = $this->db->query($total)->num_rows();

        $this->db->limit($limit, $start);

        $Q = $this->db->get();
//        var_dump($Q->result_array()); die;
        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }

    function readSavingType($id) {
        $sql = "
            SELECT 
                a.savingTypeID,a.savingTypeCode,a.savingTypeDefault,a.coopID,a.CoaID,a.savingTypeSHU,a.savingTypeName,a.savingTypeMinAmount,a.savingTypeMinTrans,a.savingTypeInterestRate,a.savingTypeSHUPayment,a.savingTypeInterestCalc,
                a.savingTypeActiveDate,a.savingTypeMonthlyFee,a.savingTypeInterestPayment,a.savingTypeSHUProfit,a.savingTypeStatus,a.savingRemark,
                a.MinAmountDepositLimit,a.LengthDeposito,a.MetodeDeposito,a.PajakDeposito,
                (SELECT GROUP_CONCAT(TypeID SEPARATOR ', ') FROM coop_saving_type_members WHERE savingTypeID = ".$id.") AS 'usedby', b.CoaTitle as coaNameSavingType
            FROM
                coop_saving_type a
                left join accounting_coa b ON a.CoaID = b.CoaID
            WHERE `savingTypeID` = ?
        ";
        $query = $this->db->query($sql, array($id));
        if($query->num_rows()>0)
        {
            return $query->result_array()[0];
        } else {
            return false;
        }
        
    }

    function createSavingType($data) {
        $query = false;
        $sql = "
            INSERT INTO `coop_saving_type` (
                `coopID`,
                `savingTypeCode`,
                `savingTypeDefault`,
                `savingTypeName`,
                `savingTypeSHU`,
                `savingTypeMinAmount`,
                `savingTypeMinTrans`,
                `savingTypeInterestRate`,
                `savingTypeSHUPayment`,
                `savingTypeInterestCalc`,
                `savingTypeActiveDate`,
                `savingTypeMonthlyFee`,
                `savingTypeInterestPayment`,
                `savingTypeSHUProfit`,
                `savingTypeStatus`,
                `savingRemark`,
                `CoaID`,
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
        
        $coop = $this->getCoopID();
        
        if($coop){


        $data['savingTypeMinAmount'] = isset($data['savingTypeMinAmount']) ? str_replace(',', '', $data['savingTypeMinAmount']) : null;
        $data['savingTypeMinTrans'] = isset($data['savingTypeMinTrans']) ? str_replace(',', '', $data['savingTypeMinTrans']) : null;
        $data['savingTypeInterestRate'] = isset($data['savingTypeInterestRate']) ? str_replace(',', '', $data['savingTypeInterestRate']) : null;
        $data['savingTypeSHUPayment'] = isset($data['savingTypeSHUPayment']) ? str_replace(',', '', $data['savingTypeSHUPayment']) : null;
        $data['savingTypeInterestCalc'] = isset($data['savingTypeInterestCalc']) ? str_replace(',', '', $data['savingTypeInterestCalc']) : null;
        $data['savingTypeInterestPayment'] = isset($data['savingTypeInterestPayment']) ? str_replace(',', '', $data['savingTypeInterestPayment']) : null;
        $data['savingTypeMonthlyFee'] = isset($data['savingTypeMonthlyFee']) ? str_replace(',', '', $data['savingTypeMonthlyFee']) : null;
        
            $params = array(
                $coop, 
                $data['savingTypeCode'], 
                $data['savingTypeDefault'],
                $data['savingTypeName'], 
                $data['savingTypeSHU'], 
                $data['savingTypeMinAmount'], 
                $data['savingTypeMinTrans'], 
                $data['savingTypeInterestRate'], 
                $data['savingTypeSHUPayment'], 
                $data['savingTypeInterestCalc'], 
                $data['savingTypeActiveDate'], 
                $data['savingTypeMonthlyFee'], 
                $data['savingTypeInterestPayment'], 
                2, 
                $data['savingTypeStatus'], 
                '', 
                $data['CoaID'],
                $_SESSION['userid'] 
            );
            
            $query = $this->db->query($sql, $params);
            
            $savingTypeID = $this->db->insert_id();
            
            //re-insert
            foreach($data['usedby'] as $values){
                $this->db->insert('coop_saving_type_members',array('savingTypeID' => $savingTypeID, 'memberID' => $values));
            }
        }
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        
        return $results;
    }
    
    // function getCoopID() {
        
    //     $this->db->select('coopID');
    //     $this->db->from('ktv_cooperative_staff');
    //     $this->db->where('userId',$_SESSION['userid']);
    //     $Q = $this->db->get();
    //     if($Q->num_rows() > 0){
    //         $row = $Q->row();
    //         return $row->coopID;
    //     }
        
    //     return false;
    // }
    
    function updateSavingType($id,$data) {
    
        $status = $data['savingTypeStatus'];
        
        if(!$status){
            
            $this->db->set('savingTypeStatus',2);
            $this->db->where('savingTypeID',$id);
            $this->db->update('coop_saving_type');
            
            return true;
        }
        
        $sql = "
            UPDATE 
                `coop_saving_type` 
            SET
                `savingTypeCode` = ?,
                `savingTypeDefault` = ?,
                `savingTypeName` = ?,
                `savingTypeSHU` = ?,
                `savingTypeMinAmount` = ?,
                `savingTypeMinTrans` = ?,
                `savingTypeInterestRate` = ?,
                `savingTypeSHUPayment` = ?,
                `savingTypeInterestCalc` = ?,
                `savingTypeActiveDate` = ?,
                `savingTypeMonthlyFee` = ?,
                `savingTypeInterestPayment` = ?,
                `savingTypeSHUProfit` = ?,
                `CoaID` = ?,
                `UpdatedBy` = ?,
                `UpdatedDate` = NOW()
            WHERE `savingTypeID` = ?
        ";

        $data['savingTypeMinAmount'] = isset($data['savingTypeMinAmount']) ? str_replace(',', '', $data['savingTypeMinAmount']) : null;
        $data['savingTypeMinTrans'] = isset($data['savingTypeMinTrans']) ? str_replace(',', '', $data['savingTypeMinTrans']) : null;
        $data['savingTypeInterestRate'] = isset($data['savingTypeInterestRate']) ? str_replace(',', '', $data['savingTypeInterestRate']) : null;
        $data['savingTypeSHUPayment'] = isset($data['savingTypeSHUPayment']) ? str_replace(',', '', $data['savingTypeSHUPayment']) : null;
        $data['savingTypeInterestCalc'] = isset($data['savingTypeInterestCalc']) ? str_replace(',', '', $data['savingTypeInterestCalc']) : null;
        $data['savingTypeInterestPayment'] = isset($data['savingTypeInterestPayment']) ? str_replace(',', '', $data['savingTypeInterestPayment']) : null;
        $data['savingTypeMonthlyFee'] = isset($data['savingTypeMonthlyFee']) ? str_replace(',', '', $data['savingTypeMonthlyFee']) : null;
        
        $params = array(
            $data['savingTypeCode'], 
            $data['savingTypeDefault'], 
            $data['savingTypeName'], 
            $data['savingTypeSHU'], 
            $data['savingTypeMinAmount'], 
            $data['savingTypeMinTrans'], 
            $data['savingTypeInterestRate'], 
            $data['savingTypeSHUPayment'], 
            $data['savingTypeInterestCalc'], 
            $data['savingTypeActiveDate'], 
            $data['savingTypeMonthlyFee'], 
            $data['savingTypeInterestPayment'], 
            2,
            $data['CoaID'] ,
            $_SESSION['userid'] ,
            $id
        );

        $query = $this->db->query($sql, $params);

        
        //delete all member
        $this->db->where('savingTypeID',$id);
        $this->db->delete('coop_saving_type_members');
        
        //re-insert
        foreach($data['usedby'] as $values){
            $this->db->insert('coop_saving_type_members',array('savingTypeID' => $id, 'memberID' => $values));
        }
        
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteSavingType($id) {
        //cek dulu masih digunakan oleh member ?
        $q = $this->db->get_where('coop_member_saving',array('savingTypeID'=>$id));
            // print_r($q->num_rows());exit;
        if($q->num_rows()>0)
        {
            $results['success'] = false;
            $results['message'] = "Cannot deleting saving type. Because saving type still used by member.";
        } else {
            $this->db->delete('coop_saving_type_members',array('savingTypeID' => $id));
            $query = $this->db->delete('coop_saving_type',array('savingTypeID' => $id));
              // $sql = "
              //       DELETE 
              //       FROM `coop_saving_type` 
              //       WHERE `savingTypeID` = ?
              //   ";
              //    = $this->db->query($sql, array($id));
                if ($query) {
                    $results['success'] = true;
                    $results['message'] = "data deleted.";
                } else {
                    $results['success'] = false;
                    $results['message'] = "Failed to delete data";
                }
        }
      
        return $results;
    }
    
    public function getComboMemberType($query = '') {
        
        $coop = getCoopID();
        
        $this->db->select(array('TypeID','TypeName'), FALSE);
        $this->db->from('coop_member_type');
        $this->db->where('CoopID', $this->coop);
        
        // if(mysql_real_escape_string($query) != ''){
        //     $key = $query;
        //     $query = explode(' ', $query);
        //     $query = implode('|', $query);
        //     $this->db->where($display . ' REGEXP "' . $query . '"');
        // }

        if($query != ''){ $this->db->like('TypeName', $query); }

        $Q = $this->db->get();
        $total = $Q->num_rows();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }

    function readCOA(){
        $this->db->select(array('CoaID id', 'CoaCode code', 'CoaTitle title'), FALSE);
        $this->db->from('accounting_coa');
        $this->db->where('CoopID', $this->coop);
        $this->db->where('CoaStatus', '1');

        $Q = $this->db->get();
        $total = $Q->num_rows();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }

    function addSavingType($post){
        $ub = $post['UsedBy'];
        unset($post['UsedBy']);

        $usedby = json_decode($ub);

        $post['CoopID'] = $this->coop;
        $this->db->insert('coop_saving_type', $post);
        $id = $this->db->insert_id();

        if(count($usedby)){
            foreach ($usedby as $value) {
                $mt = array(
                    'CoopID' => $this->coop,
                    'SavingTypeID' => $id,
                    'TypeID' => $value
                    );
                $this->db->insert('coop_saving_type_members', $mt);
            }
        }

        return $id;
    }

    function editSavingType($post){
        $ub = $post['UsedBy'];
        unset($post['UsedBy']);

        $usedby = json_decode($ub);

        $this->db->where('savingTypeID', $post['SavingTypeID']);
        $this->db->update('coop_saving_type', $post);

        //reinsert used by member data
        $this->db->where('SavingTypeID', $post['SavingTypeID']);
        $this->db->delete('coop_saving_type_members');
        
         if(count($usedby)){
            foreach ($usedby as $value) {
                $mt = array(
                    'CoopID' => $this->coop,
                    'SavingTypeID' => $post['SavingTypeID'],
                    'TypeID' => $value
                    );
                $this->db->insert('coop_saving_type_members', $mt);
            }
        }
        
        return $id = $post['SavingTypeID'];
    }
}


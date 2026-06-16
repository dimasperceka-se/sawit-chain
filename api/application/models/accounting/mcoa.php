<?php

class Mcoa extends CI_Model {

    public function getCoaClass() {
        $this->db->select("coaClassID AS id, '' AS code, coaClassTitle AS name, coaClassTitle AS title, '' AS coaType", FALSE);
        $query = $this->db->get_where('accounting_coa_class', array());
        return $query->result_array();
    }

    public function getCoaGroup($classId) {
        $this->db->select("coaGroupID AS id,coaGroupCode AS code, CONCAT(`coaGroupCode`,' - ',`coaGroupTitle`) AS name, coaGroupTitle AS title, '' AS coaType", FALSE);
        $query = $this->db->get_where('accounting_coa_group', array('coaClassID' => $classId));
        return $query->result_array();
    }

    function getAll()
    {
        $sql = " SELECT 
                a.CoaID AS id, 
                a.CoaCode AS COA_CODE, 
                CONCAT(a.`coaCode`,' - ',a.`coaTitle`) AS COA_TITLE, 
                a.CoaTitle AS title, 
                a.CoaType AS coaType,
                b.CoaBalanceAmount,
                c.JournalClosedID,
                c.JournalClosedDate,
                a.CoaOrder
            FROM 
                accounting_coa a
            LEFT JOIN accounting_coa_balance b ON a.CoaID = b.CoaID
            LEFT JOIN accounting_journal_closed c ON c.journalClosedID = b.journalClosedID
            WHERE a.CoaCodeParent IS NULL";
        $query = $this->db->query($sql, array($groupId, $closedDateID));

        return $query->result_array();
    }

    public function getCoa($groupId, $closedDateID) {
        $sql = "
            SELECT 
                a.CoaID AS id, 
                a.CoaCode AS code, 
                CONCAT(a.`coaCode`,' - ',a.`coaTitle`) AS name, 
                a.CoaTitle AS title, 
                a.CoaType AS coaType,
                b.CoaBalanceAmount,
                c.JournalClosedID,
                c.JournalClosedDate,
                a.CoaOrder
            FROM 
                accounting_coa a
            LEFT JOIN accounting_coa_balance b ON a.CoaID = b.CoaID
            LEFT JOIN accounting_journal_closed c ON c.journalClosedID = b.journalClosedID
            WHERE a.CoaGroupID = ?
                AND c.JournalClosedID = ?
                AND a.CoaCodeParent IS NULL
        ";
        $query = $this->db->query($sql, array($groupId, $closedDateID));

        return $query->result_array();
    }

    public function getCoa2($groupId, $closedDateID=null, $UseType=null) {
        //  $sql = "
        //     SELECT 
        //         a.coaID AS id, 
        //         a.coaCode AS code, 
        //         CONCAT(a.`coaCode`,' - ',a.`coaTitle`) AS name, 
        //         a.coaTitle AS title, 
        //         a.coaType AS coaType,
        //         CASE CoaReportDisplay
        //            when 1 then 'Balance Sheet' else 'Profit and Loss'
        //         END as CoaReportDisplay
        //     FROM 
        //         accounting_coa a
        //     LEFT JOIN accounting_coa_balance b ON a.coaCode = b.coaCode
        //     WHERE a.coaGroupID = ?
        //         AND a.coaCodeParent IS NULL
        // ";

        // /asdasda

          // CASE CoaStatus
          //          when 1 then false else false
          //       END as CoaStatus,
          //       CASE CoaForReceived
          //          when 1 then false else false
          //       END as CoaForReceived,
          //       CASE CoaForSpent
          //          when 1 then false else false
          //       END as CoaForSpent,
          //       CASE CoaForCash
          //          when 1 then false else false
          //       END as CoaForCash,
          //       CASE CoaForNonCash
          //          when 1 then false else false
          //       END as CoaForNonCash,
        $werUseType = null;
        
        if($UseType!=null)
        {
            if($UseType==1)
            {
                //CoaForReceived
                $werUseType = " AND CoaForReceived = 1";
            } else if($UseType==2)
                {
                    //CoaForSpent
                    $werUseType = " AND CoaForSpent = 1";
                } else if($UseType==3)
                    {
                        //CoaForCash
                        $werUseType = " AND CoaForCash = 1";
                    } else if($UseType==4)
                        {
                            //CoaForNonCash
                            $werUseType = " AND CoaForNonCash = 1";
                        } else {
                                $werUseType = null;
                        }
        }

        $sql = "
            SELECT 
                a.coaID AS id, 
                a.coaCode AS code, 
                CONCAT(a.`coaCode`,' - ',a.`coaTitle`) AS name, 
                a.coaTitle AS title, 
                a.coaType AS coaType,
                b.coaBalanceAmount,
                a.CoaStatus,
                a.CoaForReceived,
                a.CoaForSpent,
                a.CoaForCash,
                a.CoaForNonCash,
                a.CoaOrder,
                CASE CoaReportDisplay
                   when 1 then 'Balance Sheet' else 'Profit and Loss'
                END as CoaReportDisplay
            FROM 
                accounting_coa a
            LEFT JOIN accounting_coa_balance b ON a.coaCode = b.coaCode
            WHERE a.coaGroupID = ?
                AND a.coaCodeParent IS NULL $werUseType
        ";
        $query = $this->db->query($sql, array($groupId));
        $query = $query->result_array();

        $i=0;
         foreach ($query as $key => $value) {
            if($query[$i]['CoaStatus']==0 || $query[$i]['CoaStatus']==null) unset($query[$i]['CoaStatus']);
            if($query[$i]['CoaForReceived']==0 || $query[$i]['CoaForReceived']==null) unset($query[$i]['CoaForReceived']);
            if($query[$i]['CoaForSpent']==0 || $query[$i]['CoaForSpent']==null) unset($query[$i]['CoaForSpent']);
            if($query[$i]['CoaForCash']==0 || $query[$i]['CoaForCash']==null) unset($query[$i]['CoaForCash']);
            if($query[$i]['CoaForNonCash']==0 || $query[$i]['CoaForNonCash']==null) unset($query[$i]['CoaForNonCash']);
            $i++;
       }
        return $query;
    }

    public function getCoaChild($coaCode, $closedDateID) {
        $sql = "
            SELECT 
                a.coaID AS id, 
                a.coaCode AS code, 
                CONCAT(a.`coaCode`,' - ',a.`coaTitle`) AS name, 
                a.coaTitle AS title, 
                a.coaType AS coaType,
                b.coaBalanceAmount,
                c.journalClosedID,
                c.journalClosedDate
            FROM 
                accounting_coa a
            LEFT JOIN accounting_coa_balance b ON a.coaCode = b.coaCode
            LEFT JOIN accounting_journal_closed c ON c.journalClosedID = b.journalClosedID
            WHERE a.coaCodeParent = ?
                AND c.journalClosedID = ?
                AND a.coaCodeParent IS NULL
        ";
        $query = $this->db->query($sql, array($coaCode, $closedDateID));
        echo "<pre>";
        print_r($this->db->last_query());
        echo "</pre>";exit;
        $result = $query->result_array();
        return $result;
    }

    function readCoaClasss($start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                `coaClassID`,
                `coaClassTitle`,
                `coaClassType`
            FROM
                `accounting_coa_class`
        ";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array((int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $result['total'] = $row->result_array();

        return $result;
    }

    function readCoaGroups($start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                `coaGroupID` AS id,
                `coaGroupCode`,
                `coaGroupParent`,
                `coaClassID`,
                `coaGroupTitle` AS name 
            FROM
                `accounting_coa_group`
        ";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array((int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $result['total'] = $row->result_array();

        return $result;
    }

    function readCoas($start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                `coaID` AS id,
                `coaCode`,
                `coaCodeParent`,
                `coaGroupCode`,
                `currencyID`,
                `coaTitle` AS name,
                `coaType`,
                `CoaStatus` 
            FROM
                `accounting_coa`
            %s
        ";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array((int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $result['total'] = $row->result_array();

        return $result;
    }

    function readCoa($id) {
        $sql = "
            SELECT 
                `coaID`,
                `coaCode`,
                CONCAT(`coaCode`, ' - ', `coaTitle`) AS code,
                `coaCodeParent`,
                `coaGroupCode`,
                `currencyID`,
                `coaTitle`,
                `coaType` 
            FROM
                `accounting_coa` 
            WHERE coaID = ?
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createCoaClass($name, $userid) {
        $sql = "
            INSERT INTO `accounting_coa_class` (
                `coaClassTitle`,
                CreatedBy,
                CreatedDate
            ) 
            VALUES
            (
                ?,
                ?,
                NOW()
            )
        ";
        $query = $this->db->query($sql, array($name, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "COA Class created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function createCoaGroup($code, $parent, $source_id, $name, $userid) {
        $sql = "
            INSERT INTO `accounting_coa_group` (
                `coaGroupCode`,
                `coaGroupParent`,
                `coaClassID`,
                `coaGroupTitle`,
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
                NOW()
            )
        ";
        $query = $this->db->query($sql, array($code, $parent, $source_id, $name, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "COA Group created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function createCoa($code, $parent, $source_id, $name, $coaType, $closedDate, $balance, $userid) {
        $this->db->trans_start();

        $sql = "
            INSERT INTO `accounting_coa` (
                `coaCode`,
                `CoopID`,
                `coaCodeParent`,
                `coaGroupID`,
                `currencyID`,
                `coaTitle`,
                `coaType`,
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
                ?,
                NOW()
            )
        ";
        $query = $this->db->query($sql, array($code, getCoopID(), $parent, $source_id, '1', $name, $coaType, $userid));

        $sql2 = "
            INSERT INTO `accounting_coa_balance` (
                `coaCode`,
                `CoopID`,
                `coaBalanceAmount`,
                `journalClosedID`
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ";
        $query = $this->db->query($sql2, array($code, getCoopID(), $balance, $closedDate));

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "COA created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateCoaClass($name, $userid, $id) {
        $sql = "
            UPDATE 
                `accounting_coa_class` 
            SET
                `coaClassTitle` = ?,
                `UpdatedBy` = ?,
                `UpdatedDate` = NOW()
            WHERE 
                `coaClassID` = ?
        ";
        $query = $this->db->query($sql, array($name, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function updateCoaGroup($code, $parent, $source_id, $name, $userid, $id) {
        $sql = "
            UPDATE 
                `accounting_coa_group` 
            SET
                `coaGroupCode` = ?,
                `coaGroupParent` = ?,
                `coaClassID` = ?,
                `coaGroupTitle` = ?,
                `UpdatedBy` = ?,
                `UpdatedDate` = NOW()
            WHERE `coaGroupID` = ?
        ";
        $query = $this->db->query($sql, array($code, $parent, $source_id, $name, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function updateCoa($code, $parent, $source_id, $name, $coaType, $userid, $id) {
        $sql = "
            UPDATE 
                `accounting_coa` 
            SET
                `coaCode` = ?,
                `coaCodeParent` = ?,
                `coaGroupCode` = ?,
                `currencyID` = ?,
                `coaTitle` = ?,
                `coaType` = ?,
                UpdatedBy = ?,
                UpdatedDate = NOW(),
                SyncedDate = ?
            WHERE 
                `coaID` = ?
        ";
        $query = $this->db->query($sql, array($code, $parent, $source_id, '1', $name, $coaType, $userid, NULL, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function editCoa($data,$userid)
    {
        $this->db->trans_begin();

        foreach ($data as $key => $value) {

            if(isset($value->CoaStatus))
            {
                if($value->CoaStatus=='true' || $value->CoaStatus=='1')
                {
                    $CoaStatus = 1;
                } else {
                    $CoaStatus = 0;
                }
            } else {
                $CoaStatus = 0;
            }

            if(isset($value->CoaForReceived))
            {
                if($value->CoaForReceived=='true' || $value->CoaForReceived=='1')
                {
                    $CoaForReceived = 1;
                } else {
                    $CoaForReceived = 0;
                }
            } else {
                $CoaForReceived = 0;
            }

            if(isset($value->CoaForSpent))
            {
                 if($value->CoaForSpent=='true' || $value->CoaForSpent=='1')
                {
                    $CoaForSpent = 1;
                } else {
                    $CoaForSpent = 0;
                }
            } else {
                $CoaForSpent = 0;
            }

            if(isset($value->CoaForCash))
            {
                if($value->CoaForCash=='true' || $value->CoaForCash=='1')
                {
                    $CoaForCash = 1;
                } else {
                    $CoaForCash = 0;
                }
            } else {
                $CoaForCash = 0;
            }

            if(isset($value->CoaForNonCash))
            {
                 if($value->CoaForNonCash=='true' || $value->CoaForNonCash=='1')
                {
                    $CoaForNonCash = 1;
                } else {
                    $CoaForNonCash = 0;
                }
            } else {
                $CoaForNonCash = 0;
            }

            if(isset($value->CoaReportDisplay))
            {
                if($value->CoaReportDisplay=='Balance Sheet')
                {
                    $CoaReportDisplay = 1;
                } else {
                    $CoaReportDisplay = 2;
                }
            } 

            $this->db->where('CoaID',$value->id);
            $this->db->update('accounting_coa',array(
                    'CoaStatus'=>$CoaStatus,
                    'CoaForReceived'=>$CoaForReceived,
                    'CoaForSpent'=>$CoaForSpent,
                    'CoaForCash'=>$CoaForCash,
                    'CoaForNonCash'=>$CoaForNonCash,
                    'CoaOrder'=>isset($value->CoaOrder) ? $value->CoaOrder : null,
                    'CoaReportDisplay'=>$CoaReportDisplay,
                    'SyncedDate'=>NULL
                ));
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
             $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        else
        {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data Updated";
        }
        return $results;
    }

    function deleteCoaClass($id) {
        $sql = "
            DELETE 
            FROM
              `accounting_coa_class` 
            WHERE `coaClassID` = ?
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

    function deleteCoaGroup($id) {
        $sql = "
            DELETE FROM
                `accounting_coa_group` 
            WHERE 
                `coaGroupID` = ?
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

    function deleteCoa($id) {
        $sql = "
            DELETE FROM
                `accounting_coa` 
            WHERE 
                `coaID` = ?
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

    function getComboJournalClosingDate() {

        $sql = "
            SELECT 
                `journalClosedID` AS id,
                `journalClosedDate` AS label
            FROM
                `accounting_journal_closed` 
            ORDER BY journalClosedDate DESC
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

}

?>

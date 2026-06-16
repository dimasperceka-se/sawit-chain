<?php

/* * ****************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 2020-11-17
 *  File : muser_dashboard.php
 * ***************************************** */

class Muser_dashboard extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir) {
        if ($sortingField == "")
            $sortingField = 'DashName';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        //========== Search (Begin) =====================
        $SqlSearch = "";
        if ($_SESSION['is_admin'] != "1") {
            $SqlSearch .= "AND (a.CreatedBy = {$_SESSION['userid']} OR (s.UserID = {$_SESSION['userid']} AND a.ActiveStatus = 'yes'))";
        }
        //========== Search (End) =======================

        $sql = "SELECT SQL_CALC_FOUND_ROWS 
                        a.DashID, a.DashName, a.BoardID, a.Description, a.ActiveStatus, a.DateCreated, IFNULL(a.DateUpdated, a.DateCreated) DateUpdated, a.CreatedBy, u.UserRealName CreatedName
                FROM adm_user_dashboards a
                LEFT JOIN adm_user_dashboards_share_setting s ON s.DashID = a.DashID
                LEFT JOIN sys_user u ON u.UserId = a.CreatedBy
                WHERE 1=1
                        $SqlSearch
                        AND (a.DashName LIKE ? OR a.BoardID LIKE ? OR a.Description LIKE ?)
                        AND a.StatusCode = 'active'
                GROUP BY a.DashID
                ORDER BY `$sortingField` $sortingDir
                LIMIT ?,?
                ";
        $p = array(
            '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', $start, $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();
//        $result['sql'] = $this->db->last_query();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        $_SESSION['informationGrid'] = '
            <div class="Sfr_BoxInfoDataGrid_Title"><strong>' . number_format($query->row()->total, 0, ".", ",") . '</strong> ' . lang('Data') . '</div>
            <ul class="Sft_UlListInfoDataGrid">
                <li class="Sft_ListInfoDataGrid">
                    <img class="Sft_ListIconInfoDataGrid" src="' . base_url() . '/assets/images/sort.png" width="20" />&nbsp;&nbsp;Sorted by ' . lang($sortingField) . ' ' . $sortingInfo . '
                </li>
            </ul>';

        return $result;
    }

    public function UserDash($DashID) {
        $return = array();

        $sql = "SELECT
                    a.DashID
                    , a.DashName
                    , a.BoardID
                    , a.Description
                    , a.ActiveStatus
                FROM
                    `adm_user_dashboards` a
                WHERE 1=1 AND a.DashID = ?
                LIMIT 1";
        $p = array(
            $DashID
        );
        $data = $this->db->query($sql, $p)->row_array();
        $return['data'] = $data;
        return $return;
    }

    public function UserDashFormOpen($DashID) {
        $return = array();

        $sql = "SELECT
                    a.DashID
                    , a.DashName
                    , a.BoardID
                    , a.Description
                    , a.ActiveStatus
                FROM
                    `adm_user_dashboards` a
                WHERE 1=1 AND a.DashID = ?
                LIMIT 1";
        $p = array(
            $DashID
        );
        $data = $this->db->query($sql, $p)->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.UserDashboard.MainForm-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function InsertUserDash($paramPost) {
        $results = array();
        //echo '<pre>'; print_r($paramPost); exit;
        $this->db->trans_begin();

        $sql = "INSERT INTO `adm_user_dashboards` SET
                `DashName` = ?,
                `BoardID` = ?,
                `Description` = ?,
                `ActiveStatus` = ?,
                `StatusCode` = 'active',
                `DateCreated` = NOW(),
                `CreatedBy` = ?";
        $p = array(
            $paramPost['DashName'],
            $paramPost['BoardID'],
            $paramPost['Description'],
            $paramPost['ActiveStatus'],
            $_SESSION['userid']
        );
        $query = $this->db->query($sql, $p);
        $DashID = $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['DashID'] = $DashID;
        }
        return $results;
    }

    public function UpdateUserDash($paramPost) {
        $results = array();
        //echo '<pre>'; print_r($paramPost); exit;
        $this->db->trans_begin();

        $sql = "UPDATE `adm_user_dashboards` SET
                `DashName` = ?,
                `BoardID` = ?,
                `Description` = ?,
                `ActiveStatus` = ?,
                `DateUpdated` = NOW(),
                `LastModifiedBy` = ?
                WHERE
                    DashID = ?
                LIMIT 1
                ";
        $p = array(
            $paramPost['DashName'],
            $paramPost['BoardID'],
            $paramPost['Description'],
            $paramPost['ActiveStatus'],
            $_SESSION['userid'],
            $paramPost['DashID']
        );
        $query = $this->db->query($sql,$p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
            $results['DashID'] = $paramPost['DashID'];
        }
        return $results;        
    }

    public function DeleteUserDash($DashID) {
        $results = array();

        $sql = "UPDATE adm_user_dashboards SET StatusCode='nullified', DateUpdated=NOW(), LastModifiedBy=? WHERE DashID=? LIMIT 1";
        $query = $this->db->query($sql, array($_SESSION['userid'],$DashID));

        if ($query) {
            $results['success'] = true;
            $results['message'] = lang("Data deleted");
        } else {
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        }

        return $results;
    }

    public function GetUserDashGridMain($pSearch, $start, $limit, $sortingField, $sortingDir) {
        if ($sortingField == "")
            $sortingField = 'UserName';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        //========== Search (Begin) =====================
        $SqlSearch = "";
        //========== Search (End) =======================

        $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    a.DashSetID
                    , a.DashID
                    , a.UserID
                    , u.UserName
                    , u.UserRealName
                    , g.GroupName
                    , pt.PositionName
                    , r.RoleName
                    , a.DateCreated
                FROM adm_user_dashboards_share_setting a
                LEFT JOIN sys_user u ON u.UserId = a.UserID
                LEFT JOIN sys_user_group ug ON ug.UserGroupUserId = u.UserId
                LEFT JOIN sys_group g ON g.GroupId = ug.UserGroupGroupId
                LEFT JOIN ktv_persons p ON p.UserID = u.UserId
                LEFT JOIN ktv_staffs s ON s.PersonID = p.PersonID
                LEFT JOIN ktv_staff_positions sp ON s.StaffID = sp.StaffPosStaffID
                LEFT JOIN ktv_ref_position_type pt ON pt.PositionID = sp.StaffPosPositionID
                LEFT JOIN sys_user_role ur ON ur.UserId = a.UserID
                LEFT JOIN sys_role r ON r.RoleId = ur.RoleId
                WHERE 1=1
                            AND a.DashID = ?
                    AND (a.UserID LIKE ? OR u.UserRealName LIKE ? OR u.UserRealName LIKE ?)
                GROUP BY a.UserID
                ORDER BY `$sortingField` $sortingDir
                LIMIT ?,?
                ";
        $p = array(
            $pSearch['DashID'], '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', $start, $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();
//        $result['sql'] = $this->db->last_query();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        return $result;
    }

    public function GetSelectStaffGridMain($pSearch, $start, $limit, $sortingField, $sortingDir) {
        if ($sortingField == "")
            $sortingField = 'UserName';
        if ($sortingDir == "")
            $sortingDir = 'ASC';

        //========== Search (Begin) =====================
        $SqlSearch = "";
        if ($pSearch['CmbGroup']) {
            $SqlSearch .= " AND g.GroupId = {$pSearch['CmbGroup']}";
        }
        //========== Search (End) =======================

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    u.UserId UserID
                    , u.UserRealName
                    , u.UserName
                    , g.GroupName
                    , pt.PositionName
                    , r.RoleName
                FROM sys_user u
                LEFT JOIN sys_user_group ug ON ug.UserGroupUserId = u.UserId
                LEFT JOIN sys_group g ON g.GroupId = ug.UserGroupGroupId
                LEFT JOIN ktv_persons p ON p.UserID = u.UserId
                LEFT JOIN ktv_staffs s ON s.PersonID = p.PersonID
                LEFT JOIN ktv_staff_positions sp ON s.StaffID = sp.StaffPosStaffID
                LEFT JOIN ktv_ref_position_type pt ON pt.PositionID = sp.StaffPosPositionID
                LEFT JOIN sys_user_role ur ON ur.UserId = u.UserID
                LEFT JOIN sys_role r ON r.RoleId = ur.RoleId
                WHERE 1=1
                    AND u.UserId NOT IN (
                            SELECT a.UserID
                            FROM adm_user_dashboards_share_setting a
                            WHERE a.DashID = ?
                    )
                    AND u.StatusCode = 'active'
                    AND s.StatusCode = 'active'
                    AND (u.UserId LIKE ? OR u.UserRealName LIKE ? OR u.UserName LIKE ?)
                    $SqlSearch
                GROUP BY u.UserId
                ORDER BY `$sortingField` $sortingDir
                LIMIT ?,?
                ";
        $p = array(
            $pSearch['DashID'], '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', '%' . $pSearch['KeySearch'] . '%', $start, $limit
        );
        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();
        $result['sql'] = $this->db->last_query();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        if ($sortingDir == 'ASC') {
            $sortingInfo = 'ascending';
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = 'descending';
        }

        return $result;
    }

    public function AddUserSharing($DashID, $UserIDs) {
        $results = array();
        $this->db->trans_begin();

        $ArrUserIDAdd = explode(",", $UserIDs);
        for ($i = 0; $i < count($ArrUserIDAdd); $i++) {
            $UserID = (int) $ArrUserIDAdd[$i];

            $sql = "INSERT INTO `adm_user_dashboards_share_setting` SET
                    DashID = ?,
                    UserID = ?,
                    DateCreated = NOW(),
                    CreatedBy = ?
                ";
            $p = array(
                $DashID, $UserID, $_SESSION['userid']
            );
            $query = $this->db->query($sql, $p);
            $DashSetID = $this->db->insert_id();
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        }

        return $results;
    }

    public function DeleteUserSharing($DashSetID) {
        $results = array();
        $this->db->trans_begin();

        $sql = "DELETE FROM adm_user_dashboards_share_setting WHERE DashSetID = ? LIMIT 1";
        $query = $this->db->query($sql, array($DashSetID));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data deleted");
        }

        return $results;
    }

}

<?php

if (!function_exists('getSceID')) {
    function getSceID()
    {
    // get from group filter
        if ($_SESSION['filter_by'] == '2') {
            return $_SESSION['filter_id'];
        }
    // if not, get from user
        $CI = & get_instance();
        $sql = "SELECT
        s.SceID
        FROM ktv_persons p
        JOIN sce_farmer_staff s ON s.PersonID = p.PersonID
        WHERE
        p.UserID = ?
        ";
        $query = $CI->db->query($sql, array($_SESSION['userid']));
        if ($query->num_rows()>0) {
            $sce = $query->row_array(0);
            return $sce['SceID'];
        }
    // fallback
        return false;
    }
}

if (!function_exists('IsUrlImageExist')) {
    function IsUrlImageExist($url){
        if (@file_get_contents($url, 0, NULL, 0, 1)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('getFarmerIDForSce')) {
    function getFarmerIDForSce($SceID)
    {
        $CI = & get_instance();
        $sql = "SELECT FarmerID FROM sce_farmer WHERE SceID = ? LIMIT 1";
        $query = $CI->db->query($sql, array($SceID));
        if ($query->num_rows()>0) {
            $sce = $query->row_array(0);
            return $sce['FarmerID'];
        }
    // fallback
        return false;
    }
}

if (!function_exists('getFarmerInfoForSce')) {
    function getFarmerInfoForSce($SceID)
    {
        $CI = & get_instance();
        $sql = "SELECT
                    a.FarmerID,
                    b.FarmerName
                FROM
                    sce_farmer a
                    INNER JOIN ktv_farmer b ON a.FarmerID = b.FarmerID
                WHERE
                    a.SceID = ?
                LIMIT 1";
        $query = $CI->db->query($sql, array($SceID));
        if ($query->num_rows()>0) {
            $sce = $query->row_array(0);
            return $sce;
        }
    // fallback
        return false;
    }
}

if (!function_exists('getCoopID')) {
  function getCoopID() {

      $CI = & get_instance();

      if(isset($_SESSION['coopID']))
      {
          return $_SESSION['coopID'];
      } else {
          $CI->db->select('coopID');
          $CI->db->from('ktv_cooperative_staff');
          $CI->db->where('userId', $_SESSION['userid']);
          $Q = $CI->db->get();
          if ($Q->num_rows() > 0) {
              $row = $Q->row();
              //$_SESSION['coopID'] = $coopID;
              return $row->coopID;
          }

          return 1;
      }

  }
}

if (!function_exists('UserLogout')) {
    function UserLogout()
    {
        $_SESSION = array();
        redirect('system/login', 'location'); exit;
    }
}

if (!function_exists('LoadLangJs')) {
    function LoadLangJs($lang){
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('sys_translation');
        $CI->db->where('language', $lang);
        $CI->db->where('set', 'general');

        $query  = $CI->db->get()->result();
        $return = array();
        foreach ($query as $row) {
            $return[$row->key] = $row->text;
        }

        unset($CI, $query);
        return $return;
    }
}

if (! function_exists('writeLogUserAccess'))
{
    function writeLogUserAccess($type, $UserID, $AttempProcess, $remark) {
        $CI = & get_instance();

        $sql = "INSERT INTO `sys_log_access` SET
                `type` = ?,
                `UserID` = ?,
                `SessionIP` = ?,
                `UserAgent` = ?,
                `AttempProcess` = ?,
                Remark = ?
            ";
        $p = array(
            $type,
            $UserID,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'],
            $AttempProcess,
            $remark
        );
        $CI->db->query($sql, $p);
    }
}

if (! function_exists('InsertLogAWS'))
{
    function InsertLogAWS($UserInfo, $response) {
        $CI = & get_instance();

        ob_start();
        var_dump($response);
        $resultResponse = ob_get_clean();

        $sql = "INSERT INTO `sys_log_aws` SET
                `UserInfo` = ?,
                `Remark` = ?,
                `DateGenerated` = NOW()";
        $p = array(
            $UserInfo,$resultResponse
        );
        $CI->db->query($sql, $p);
    }
}

if (! function_exists('IsPaymentMethod'))
{
    function IsPaymentMethod() {
        $CI = & get_instance();

        $userid = $_SESSION['userid'];

        $sql = "SELECT 
                    IsPaymentMethod
                FROM 
                    view_tc_supplychain_staff vs
                LEFT JOIN ktv_tc_supplychain_org kso ON kso.SupplychainID = vs.SupplychainID
                WHERE
                    vs.UserID = $userid";

        $result = $CI->db->query($sql, $p)->row_array();

        return $result['IsPaymentMethod'];
    }
}
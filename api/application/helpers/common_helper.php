<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function getCoopID() {

    $CI = & get_instance();
    /*
    if(isset($_SESSION['coopID']))
    {
        return $_SESSION['coopID'];
    } else {
        $CI->db->select('coopID');
        $CI->db->from('ktv_cooperative_staff');
        //$CI->db->where('userId', $_SESSION['userid']);
        $Q = $CI->db->get();
        if ($Q->num_rows() > 0) {
            $row = $Q->row();
            //$_SESSION['coopID'] = $coopID;
            return $row->coopID;
        }

        return 1;
    }
    */
}

/**
 *
 * @param type $type
 * @return string {COOPCODE}.{CODE}.{MONTH}.{YEAR}.{NUMBER} Revised to: {CODE}{NUMBER}
 */
function getMemberNumber($type) {

    $CI = & get_instance();

    $number = '00001';

    $CI->db->select('TypeCode,CoopCode');
    $CI->db->from('coop_member_type');
    $CI->db->join('ktv_cooperatives','ktv_cooperatives.CoopID=coop_member_type.CoopID','left');
    $CI->db->where('TypeID', $type);
    $Q = $CI->db->get();

    if ($Q->num_rows() > 0) {
        $row = $Q->row();
        $row->TypeCode;

        //$rule = trim($row->CoopCode) . "." . trim($row->TypeCode) . ".[0-9]{2}.[0-9]{4}.[0-9]{5}";
        $rule = trim($row->TypeCode) . ".[0-9]{5}";
        //get last number
        $CI->db->select('MAX(RIGHT(primaryNo,5)) AS LAST', FALSE);
        $CI->db->from('coop_member');
        $CI->db->where('typeID', $type);
        $CI->db->where('primaryNo REGEXP "' . $rule . '"', NULL, FALSE);
        $Q2 = $CI->db->get();
        if ($Q2->num_rows()) {
            $last = $Q2->row();
            if ((int) $last->LAST > 0) {
                $number = ((int) $last->LAST) + 1;
            }
        }

        return trim($row->TypeCode) . '.' . sprintf("%05d", $number);
    }

    return false;
}

/**
 *
 * @param type $type
 * @return string {COOPCODE}.{CODE}.{MONTH}.{YEAR}.{NUMBER}
 */
function getSavingNumber($type) {

    $CI = & get_instance();

    $number = '00001';

    $CI->db->select('savingTypeCode,CoopCode');
    $CI->db->from('coop_saving_type');
    $CI->db->join('ktv_cooperatives','ktv_cooperatives.CoopID=coop_saving_type.CoopID','left');
    $CI->db->where('savingTypeID', $type);
    $Q = $CI->db->get();

    if ($Q->num_rows() > 0) {
        $row = $Q->row();
        $row->savingTypeCode;
        $rule = trim($row->CoopCode) . "." . trim($row->savingTypeCode) . ".[0-9]{2}.[0-9]{4}.[0-9]{5}";
        //get last number
        $CI->db->select('MAX(RIGHT(memberSavingNo,5)) AS LAST', FALSE);
        $CI->db->from('coop_member_saving');
        $CI->db->where('savingTypeID', $type);
        $CI->db->where('memberSavingNo REGEXP "' . $rule . '"', NULL, FALSE);
        $Q2 = $CI->db->get();
        if ($Q2->num_rows()) {
            $last = $Q2->row();
            if ((int) $last->LAST > 0) {
                $number = ((int) $last->LAST) + 1;
            }
        }

        return trim($row->CoopCode) . "." . trim($row->savingTypeCode) . '.' . date('m') . '.' . date('Y') . '.' . sprintf("%05d", $number);
    }

    return false;
}

/**
 *
 * @param type $type (1: Deposit, 2: Withdraw)
 * @return string {CODE}.{MONTH}.{YEAR}.{NUMBER}
 */
function getTransactionNumber($type) {

    $CI = & get_instance();

    $number = '00001';

    switch ($type) {
        case 1:
            $code = 'DEPO';
            break;

        case 2:
            $code = 'WITD';
            break;

        default:
            break;
    }

    //get CoopCode
    $c = getCoopID();

    $CI->db->select('CoopCode');
    $CI->db->from('ktv_cooperatives');
    $CI->db->where('CoopID',$c);
    $QCoop = $CI->db->get();
    if($QCoop->num_rows() > 0){
      $row = $QCoop->row();
      $rule = trim($row->CoopCode) . "." . trim($code) . ".[0-9]{2}.[0-9]{4}.[0-9]{5}";

      //get last number
      $CI->db->select('MAX(RIGHT(memberTransactionNumber,5)) AS LAST', FALSE);
      $CI->db->from('coop_member_transaction');

      $CI->db->where('memberTransactionType', $type);
      $CI->db->where('memberTransactionNumber REGEXP "' . $rule . '"', NULL, FALSE);
      $Q2 = $CI->db->get();
      if ($Q2->num_rows()) {
          $last = $Q2->row();
          if ((int) $last->LAST > 0) {
              $number = ((int) $last->LAST) + 1;
          }
      }

      return trim($row->CoopCode) . "." . trim($code) . '.' . date('m') . '.' . date('Y') . '.' . sprintf("%05d", $number);
    }

}

function getTransactionNumber2($type,$productId) {

    $CI = & get_instance();

       $sType = $CI->db->query("select b.savingTypeCode
                    from coop_member_saving a
                    join coop_saving_type b On a.savingTypeID = b.savingTypeID
                    where a.memberSavingID = $productId")->row();


    $number = '00001';

    switch ($type) {
        case 1:
            $code = 'DEPO';
            break;

        case 2:
            $code = 'WITD';
            break;

        default:
            break;
    }

    //get CoopCode
    $c = getCoopID();

    $CI->db->select('CoopCode');
    $CI->db->from('ktv_cooperatives');
    $CI->db->where('CoopID',$c);
    $QCoop = $CI->db->get();
    if($QCoop->num_rows() > 0){
      $row = $QCoop->row();
      $rule = trim($row->CoopCode) . "." . trim($code) . ".[0-9]{2}.[0-9]{4}.[0-9]{5}";

      //get last number
      $CI->db->select('MAX(RIGHT(memberTransactionNumber,5)) AS LAST', FALSE);
      $CI->db->from('coop_member_transaction');

      $CI->db->where('memberTransactionType', $type);
      $CI->db->where('memberTransactionNumber REGEXP "' . $rule . '"', NULL, FALSE);
      $Q2 = $CI->db->get();
      if ($Q2->num_rows()) {
          $last = $Q2->row();
          if ((int) $last->LAST > 0) {
              $number = ((int) $last->LAST) + 1;
          }
      }

      return $sType->savingTypeCode. sprintf("%05d", $number);
    }

}
/**
 *
 * @param type $type (1: Deposit, 2: Withdraw)
 * @return string {CODE}.{MONTH}.{YEAR}.{NUMBER}
 */
function getRecBookNumber($type) {

    $CI = & get_instance();

    $number = '00001';

    switch ($type) {
        case 1:
            $code = 'KM';
            break;

        case 2:
            $code = 'KK';
            break;

        default:
            break;
    }

    //get CoopCode
    // $CI->db->select('CoopCode');
    // $CI->db->from('ktv_cooperatives');
    // $CI->db->where('CoopID',getCoopID());
    // $QCoop = $CI->db->get();

    // imam @ 22-08-16 : somehow active record diatas gagal dijalankan zzz....

    $QCoop = $CI->db->query("select CoopCode from ktv_cooperatives where CoopID = ".getCoopID()."");

    if($QCoop->num_rows() > 0){
      $row = $QCoop->row();
      $rule = trim($row->CoopCode) . "." . trim($code) . ".[0-9]{2}.[0-9]{4}.[0-9]{5}";

      //get last number
      $CI->db->select('MAX(RIGHT(TransactionNumber,5)) AS LAST', FALSE);
      $CI->db->from('coop_transactions');
      $CI->db->where('TransactionType', $type);
      $CI->db->where('TransactionNumber REGEXP "' . $rule . '"', NULL, FALSE);
      $Q2 = $CI->db->get();
      if ($Q2->num_rows()) {
          $last = $Q2->row();
          if ((int) $last->LAST > 0) {
              $number = ((int) $last->LAST) + 1;
          }
      }

      return trim($row->CoopCode) . "." . trim($code) . '.' . date('m') . '.' . date('Y') . '.' . sprintf("%05d", $number);
    } else {
        return false;
    }

}

/**
 *
 * @param type $type
 * @return string {COOPCODE}.{CODE}.{MONTH}.{YEAR}.{NUMBER}
 */
function getLoanNumber($type) {

    $CI = & get_instance();

    $number = '00001';

    $CI->db->select('loanTypeCode');
    $CI->db->from('coop_loan_type');
    $CI->db->where('loanTypeID', $type);
    $Q = $CI->db->get();

    if ($Q->num_rows() > 0) {
        $row = $Q->row();
        $row->loanTypeCode;
        $rule = 'LOAN' . ".[0-9]{2}.[0-9]{4}.[0-9]{5}";
        //get last number
        $CI->db->select('MAX(RIGHT(memberLoanNo,5)) AS LAST', FALSE);
        $CI->db->from('coop_member_loan');
        $CI->db->where('loanTypeID', $type);
        $CI->db->where('memberLoanNo REGEXP "' . $rule . '"', NULL, FALSE);
        $Q2 = $CI->db->get();
        if ($Q2->num_rows()) {
            $last = $Q2->row();
            if ((int) $last->LAST > 0) {
                $number = ((int) $last->LAST) + 1;
            }
        }

        return 'LOAN' . '.' . date('m') . '.' . date('Y') . '.' . sprintf("%05d", $number);
        //return trim($row->loanTypeCode).'.'.date('m').'.'.date('Y').'.'.sprintf("%05d",$number);
    }

    return false;
}

function insertNotifLoan($memberLoanID, $memberID, $userid) {
    $CI = & get_instance();

    //next id ktv_notifikasi
    $idNotif = $CI->db->query("SELECT `auto_increment` as id FROM INFORMATION_SCHEMA.TABLES
                                WHERE table_name = 'ktv_notifikasi'")->row();

    $CI->db->select('name');
    $qm = $CI->db->get_where('coop_member', array('memberID' => $memberID))->row();
    $dn = array(
        'NotifID' => $idNotif->id,
        'NotifDate' => date('Y-m-d'),
        'UserId' => $userid,
        'memberLoanID' => $memberLoanID,
        'Type' => 1,
//        'Action' => 'loan/loan_notifctrl/loanapproval/'.$idNotif->id.'/'.$idLoan,
        'Action' => 'loan/loan_member/index',
        'Title' => 'New Loan Approval',
        'Message' => 'New loan from <b>' . $qm->name . '</b> needs to approval',
        'Status' => 0,
        'createdBy' => $userid,
        'createdDate' => date('Y-m-d')
    );
    $CI->db->insert('ktv_notifikasi', $dn);
    if ($CI->db->affected_rows() > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Mengolah struktur array bertingkat untuk di insert ke db
 * @author Ardi <hariardi@gmail.com>
 * @param array $data
 *
 */
function jsonParser(&$data, $whitelist = array(), $ignorelist = array()) {

    $CI = & get_instance();

    $CI->db->trans_start();
    if (is_array($data)) {
        foreach ($data as $key => $value) {

            //Find table based on key
            if (is_string($key) && $CI->db->table_exists($key) && is_array($value)) {
                $override = in_array($key, $whitelist);

                $data[$key] = _arrayChecker($key, $value, $override, $whitelist, $ignorelist);
            }
        }
    }
    $CI->db->trans_complete();
    return $CI->db->trans_status();
}

function isArray($var) {
    return !is_array($var);
}

function isPrimary($var) {
    return $var->primary_key;
}

function _arrayChecker($table, $data, $override = FALSE, $whitelist, $fields) {

    $CI = & get_instance();
    //var_dump($fields);die;
    $output = array();

    foreach ($data as $value) {

        $insert = array_filter($value, "isArray");

        $returnid = _arrayDB($table, $fields, $insert, $override);

        $diff = array_diff_assoc($value, $insert);

        if (count($diff) > 0) {
            jsonParser($diff, $whitelist);
            array_push($insert, $diff);
        }

        $insert['insert_id'] = $returnid['insert_id'];
        $class = $CI->router->fetch_class();
        if ($class === 'sync') {
            $insert['sync_date'] = $returnid['sync_date'];
        }

        array_push($output, $insert);
    }

    return $output;
}

if (! function_exists('uidGenerateCode'))
{
    function uidGenerateCode(){
        $LETTERS = "abcdefghijklmnopqrstuvwxyz".
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $ALLOWED_CHARS = "0123456789".$LETTERS;
        $NUMBER_OF_CODE_POINTS = strlen($ALLOWED_CHARS);
        $CODE_SIZE = 11;

        $randomChars = ''; 
        $index = random_int(0, strlen($LETTERS) - 1); 
        $randomChars[0] = $LETTERS[$index]; 
        
        for ($i = 1; $i < $CODE_SIZE; $i++) { 
            $index = random_int(0, strlen($ALLOWED_CHARS) - 1); 
            $randomChars[$i] = $ALLOWED_CHARS[$index]; 
        } 
        preg_match("/^[a-zA-Z]{1}[a-zA-Z0-9]{10}$/",$randomChars,$validCode);
        return $randomChars; 
    }
}  

function _arrayDB($table, $fields, $insert, $override = FALSE) {

    $CI = & get_instance();

    if (array_key_exists('DateSync', $insert)) {
        unset($insert['DateSync']);
    } //jika ditemukan field DateSync, dihapus dari array biar ngga terjadi duplicate ^_^

    foreach ($insert as $key => $value) {
        if(in_array($key, $fields)){
            unset($insert[$key]);
        }
    }
    //var_dump($insert);die;
    $keys = _arrayKey($insert);
    $values = array_values($insert);
    $update = array();

    foreach ($insert as $key => $value) {

        if (!$override) {
            $key = '`' . $key . '` = IF(`' . $key . '` IS NULL OR `' . $key . '` = "","' . $value . '",`' . $key . '`)';
        } else {
            $key = '`' . $key . '` = "' . $value . '"';
        }

        //var_dump($key);
        array_push($update, $key);
    }
    //var_dump('updateee');
    //var_dump($update);
    $_explain = _explain($table);
    $fields = array();

    foreach ($_explain['fields'] as $keyfield => $vfield) {
        array_push($fields, $vfield->name);
    }
    $class = $CI->router->fetch_class();
    if ($class === 'sync') {
        if (in_array('DateSync', $fields)) {
            array_push($keys, 'DateSync');
            array_push($values, date('Y-m-d'));
            array_push($update, '`DateSync` = "' . date('Y-m-d') . '"');
        }
    }
    foreach ($keys as $keyss => $keyv){
        $keys[$keyss] = '`' . $keyv . '`';
    }

    $sql = 'INSERT INTO `' . $table . '` (' . implode(',', $keys) . ') VALUES("' . implode('","', $values) . '")';
    $sql .= ' ON DUPLICATE KEY UPDATE ' . implode(',', $update);
//    echo "<pre>";
//    print_r($sql);
//    echo "</pre>";
//    die();
    $CI->db->query($sql);

    if ($CI->db->_error_number()) {
        return $CI->db->_error_message();
    }

    $returnid = array('sync_date' => date('Y-m-d'), 'insert_id' => NULL);

    if ($CI->db->insert_id() > 0) {
        $returnid['insert_id'] = $CI->db->insert_id();
    }

    return $returnid;
}

function _arrayKey($array) {

//    $keys = array();
//    while (($a = current($array)) !== FALSE) {
//        array_push($keys, key($array));
//        next($array);
//    }
    $keys = array_keys($array);

    return $keys;
}

function _explain($table) {

    $CI = & get_instance();

    $fields = $CI->db->field_data($table);

    $primary = array_filter($fields, "isPrimary");

    return array('fields' => $fields, 'primary' => $primary);
}

function generateHakAksesDistrictId($sessDaerah,$provId){
    $arrTemp = explode(",",$sessDaerah);
    $arrDistrictId = array();

    for ($i=0; $i < count($arrTemp); $i++) {
        $arrTempFor = explode("##",$arrTemp[$i]);
        $arrDistrictId[] = $arrTempFor[0];
    }

    //ambil districtId sesuai provinsinya
    $arrRangeIdReturn = array();
    for ($i=0; $i < count($arrDistrictId); $i++) {
        if($provId == substr($arrDistrictId[$i],0,2)){
            $arrRangeIdReturn[] = $arrDistrictId[$i];
        }
    }

    return $arrRangeIdReturn;
}

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

/**
 * Membuat Nomor Batch dengan format {SupplychainID:[4 digits]}{date:Ymd}{No.Urut[3 digits]} Contoh: 000420160919001
 * Penomoran ini di reset per hari
 * @param  $SupplychainID [ID Buying Unit pemilik Batch]
 * @return String [Nomor Batch]
 */
function getBatchNumber($SupplychainID) {

    $sid = sprintf("%04d", $SupplychainID);

    $CI = & get_instance();

    $number = '001';

    $rule = $sid . date('Ymd') . "[0-9]{3}";
    //get last number
    $CI->db->select('(RIGHT(MAX(SupplyBatchNumber), 3)) AS LAST', FALSE);
    $CI->db->from('ktv_supplychain_batch');
    $CI->db->where('SupplyOrgID', $SupplychainID);
    $CI->db->where('SupplyBatchNumber REGEXP "' . $rule . '"', NULL, FALSE);
    $Q2 = $CI->db->get();
    if ($Q2->num_rows()) {
        $last = $Q2->row();
        if ((int) $last->LAST > 0) {
            $number = ((int) $last->LAST) + 1;
        }
    }

    return $sid . date('Ymd') . sprintf("%03d", $number);

}

function getInvoiceNumber($SupplychainID) {

    $sid = sprintf("%04d", $SupplychainID);

    $CI = & get_instance();

    $number = '001';

    $rule = $sid . date('Ymd') . "[0-9]{3}";
    //get last number
    $CI->db->select('(RIGHT(MAX(InvoiceNumber), 3)) AS LAST', FALSE);
    $CI->db->from('ktv_supplychain_transaction');
    $CI->db->join('ktv_supplychain_batch', 'ktv_supplychain_batch.SupplyBatchID = ktv_supplychain_transaction.SupplyBatchID', 'left');
    $CI->db->where('SupplyOrgID', $SupplychainID);
    $CI->db->where('InvoiceNumber REGEXP "' . $rule . '"', NULL, FALSE);
    $Q2 = $CI->db->get();
    if ($Q2->num_rows()) {
        $last = $Q2->row();
        if ((int) $last->LAST > 0) {
            $number = ((int) $last->LAST) + 1;
        }
    }

    return $sid . date('Ymd') . sprintf("%03d", $number);

}

//hapus folder dan semua file didalamnya
function unlinkr($dir, $pattern = "*") {
    // find all files and folders matching pattern
    $files = glob($dir . "/$pattern");
    //interate thorugh the files and folders
    foreach($files as $file){
        //if it is a directory then re-call unlinkr function to delete files inside this directory
        if (is_dir($file) and !in_array($file, array('..', '.')))  {
            unlinkr($file, $pattern);
            //remove the directory itself
            rmdir($file);
            } else if(is_file($file) and ($file != __FILE__)) {
            // make sure you don't delete the current script
            unlink($file);
        }
    }
    rmdir($dir);
}
function cek_data_duplicate($data=array()){
    $CI = & get_instance();
    $CI->db->where($data['where']);
    $CI->db->select('*');
    $CI->db->from($data['table']);
    $data = $CI->db->get();

    return $data->num_rows();
}

//Added by nikolius.lau@koltiva.com ==== (Begin)
function waktu_buat_orang($timestamp) {
    try {
        $diff = time() - $timestamp;
    } catch (Exception $e) {
        return '-';
    }

    if ($diff <= 0) {
        return 'just now';
    }
    else if ($diff < 60) {
        return grammar_enggress_buat_waktu(floor($diff), ' second(s) ago');
    }
    else if ($diff < 60*60) {
        return grammar_enggress_buat_waktu(floor($diff/60), ' minute(s) ago');
    }
    else if ($diff < 60*60*24) {
        return grammar_enggress_buat_waktu(floor($diff/(60*60)), ' hour(s) ago');
    }
    else if ($diff < 60*60*24*30) {
        return grammar_enggress_buat_waktu(floor($diff/(60*60*24)), ' day(s) ago');
    }
    else if ($diff < 60*60*24*30*12) {
        return grammar_enggress_buat_waktu(floor($diff/(60*60*24*30)), ' month(s) ago');
    }
    else {
        return grammar_enggress_buat_waktu(floor($diff/(60*60*24*30*12)), ' year(s) ago');
    }
}

function grammar_enggress_buat_waktu($val,$kata){
    if ($val > 1) {
        return $val.str_replace('(s)', 's', $kata);
    } else {
        return $val.str_replace('(s)', '', $kata);
    }
}

function generateTransTraceabilityNumber($SupplychainID){
    date_default_timezone_set('UTC');

    $sid = sprintf("%04d", $SupplychainID);
    $tgl = date('Ymd');
    $CI = & get_instance();
    $number = 1;

    $CI->db->select('(RIGHT(MAX(TransNumber), 3)) AS LAST', FALSE);
    $CI->db->from('ktv_tc_supplychain_transaction');
    $CI->db->where('SupplychainID', $SupplychainID);
    $CI->db->where('DATE(DateTransaction)', date('Y-m-d'));
    $inc = $CI->db->get();

    if ($inc->num_rows()) {
        $last = $inc->row();
        if ((int) $last->LAST > 0) {
            $number = ((int) $last->LAST) + 1;
        }
    }

    return 'W'.$sid.$tgl.sprintf("%03d", $number);

}

function generateBatchTraceabilityNumber($SupplychainID){
    $sid = sprintf("%04d", $SupplychainID);
    $tgl = date('Ymd');
    $CI = & get_instance();
    $number = 1;

    $CI->db->select('(RIGHT(MAX(SupplyBatchNumber), 3)) AS LAST', FALSE);
    $CI->db->from('ktv_tc_supplychain_batch');
    $CI->db->where('SupplyOrgID', $SupplychainID);
    $CI->db->where('DATE(SupplyBatchDate)', date('Y-m-d'));
    $inc = $CI->db->get();

    if ($inc->num_rows()) {
        $last = $inc->row();
        if ((int) $last->LAST > 0) {
            $number = ((int) $last->LAST) + 1;
        }
    }

    return 'WB'.$sid.$tgl.sprintf("%03d", $number);

}

function _debuglog($str){
    $myfile = fopen("files/tmp/debuglog", "a") or die("Unable to open file!");
    if($str){
        $txt = $str;
    } else {
        $txt = "-";
    }
    fwrite($myfile, "\n". $txt);
    fclose($myfile);
}

function GetMySqlConn(){
    $CI = & get_instance();
    try {
        $ConnPg = new PDO('mysql:host='.$CI->db->hostname.';dbname='.$CI->db->database.'', $CI->db->username, $CI->db->password);
    }
    catch (PDOException $e) {
        $ConnPg = false;
    }
    return $ConnPg;
}


if (!function_exists('loginCognito')) {
    function loginCognito($username='', $password=''){
        $CI = &get_instance();
        $CI->config->load('awscognito');
        $config = $CI->config->item('awscog');

        $aws = new Aws\Sdk($config);
        $cognitoClient = $aws->createCognitoIdentityProvider();
        
        $client = new \pmill\AwsCognito\CognitoClient($cognitoClient);
        $client->setAppClientId($config['app_client_id']);
        $client->setAppClientSecret($config['app_client_secret']);
        $client->setRegion($config['region']);
        $client->setUserPoolId($config['user_pool_id']);

        try {
            $authenticationResponse = $client->authenticate($username, $password);
        } catch (ChallengeException $e) {
            if ($e->getChallengeName() === CognitoClient::CHALLENGE_NEW_PASSWORD_REQUIRED) {
                $authenticationResponse = $client->respondToNewPasswordRequiredChallenge($username, 'password_new', $e->getSession());
            }
        } catch (PasswordResetRequiredException $e) {
            die("PASSWORD RESET REQUIRED");
        }

        if(is_array($authenticationResponse)){
            return $authenticationResponse;
        }else{
            return array('success' => false, 'message'=> 'config aws cognito is wrong !');
        }
    }
}

if (! function_exists('GetFileExt'))
{
    function GetFileExt($filename) {
        $arrTemp = explode(".", $filename);
        $ext = strtolower(array_values(array_slice($arrTemp, -1))[0]);
        return $ext;
    }
}

if (! function_exists('mappingArrayCognitoAttributes'))
{
    function mappingArrayCognitoAttributes($ArrProcess) {
        $returnArr = array();
        for ($i=0; $i < count($ArrProcess); $i++) {
            $returnArr[$ArrProcess[$i]['Name']] = $ArrProcess[$i]['Value'];
        }
        return $returnArr;
    }
}

if (! function_exists('decodeMsgAws'))
{
    function decodeMsgAws($msg) {
        $regex = '/{(.*?)[\|\|.*?]?}/';
        preg_match_all($regex, $msg, $matches);
        //echo '<pre>'; print_r($matches[0]); exit;
        $arrfound = $matches[0];
        $lastKey = key(array_slice($arrfound, -1, 1, true)); //Ambil key array terakhir
        //echo '<pre>'; print_r($lastKey); exit;
        $arrtmp = json_decode($arrfound[$lastKey], true);
        return $arrtmp['message'];
    }
}

if (! function_exists('cekValidasiPassword'))
{
    function cekValidasiPassword($passwd) {
        $return = array();
        $prosesValidasi = true;
        $messageNya = lang('Password valid');
        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/"; //Uppercase, lowercase, angka, simbol

        //cek dulu apakah mengandung +,- dan (spasi)
        if (strpos($passwd, ' ') !== false || strpos($passwd, '+') !== false || strpos($passwd, '-') !== false) {
            $prosesValidasi = false;
            $messageNya = lang('Password cannot contains +,- and (space)');
        } elseif(strlen($passwd) < 8) {
            $prosesValidasi = false;
            $messageNya = lang('Password minimun length is 8');
        } elseif(!preg_match($regex, $passwd)) {
            $prosesValidasi = false;
            $messageNya = lang('Password must contains uppercase, lowercase, numbers and special characters');
        }

        $return['success'] = $prosesValidasi;
        $return['message'] = $messageNya;
        return $return;
    }
}

if (! function_exists('getRandomBytes'))
{
    function getRandomBytes($nbBytes = 32)
    {
        $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);
        if (false !== $bytes && true === $strong) {
            return $bytes;
        }
        else {
            throw new \Exception("Unable to generate secure token from OpenSSL.");
        }
    }
}

if (! function_exists('passwordGenerator'))
{
    function passwordGenerator($length) {
        $length = $length - 1;
        $pass = substr(preg_replace("/[^a-zA-Z0-9]/", "", base64_encode(getRandomBytes($length+1))),0,$length);

        //Special Characters
        if(rand() % 2 == 0) {
            $spec = 'Kv8!';
        } else {
            if(rand() % 2 == 0) {
                $spec = 'Ta5#';
            } else {
                $spec = 'Ol3*';
            }
        }

        return $pass.$spec;
    }
}

if (! function_exists('usernameOfEmail'))
{
    function usernameOfEmail($email) {
        $ArrTmp = explode('@',$email);
        return trim($ArrTmp[0]);
    }
}

if (! function_exists('randomPhonenumber'))
{
    function randomPhonenumber() {
        $return = '+62'.date('YmdHis');
        return $return;
    }
}

if (! function_exists('ExtractPhoneNumberWithPhoneCode'))
{
    function ExtractPhoneNumberWithPhoneCode($PhoneProses,$PhoneCodeCountry) {
        $return = array();

        if($PhoneCodeCountry == '') $PhoneCodeCountry = null;
        if($PhoneProses == '') $PhoneProses = null;

        $Cek = strpos($PhoneProses,$PhoneCodeCountry);

        if($Cek === false) {
            //false
            $return['Phonecode'] = '+';
            $return['Phonenumber'] = str_replace('+','',$PhoneProses);
        } else {
            if($Cek == 0) {
                //true
                $return['Phonecode'] = $PhoneCodeCountry;
                $return['Phonenumber'] = str_replace($PhoneCodeCountry,'',$PhoneProses);
            } else {
                //false
                $return['Phonecode'] = '+';
                $return['Phonenumber'] = str_replace('+','',$PhoneProses);
            }
        }

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

if (!function_exists('checkLogTraceability')) {
    function checkLogTraceability($version, $method='', $url='', $payload='', $curlTable=array()){
        $CI = &get_instance();
        
        if($url!=''){
            $insert_log = array(
                'Version' => $version,
                'Method' => $method,
                // 'Header' => json_encode($CI->input->request_headers()),
                'Url' => $url,
                'Token' => '',
                'Payload' => json_encode($payload),
                'Response' => 'success',
                'TimeStart' => date('Y-m-d H:i:s'),
            );
            
            if(@$curlTable['TableName']!=''){
                $insert_log['TableName'] = $curlTable['TableName'];
                $insert_log['TableField'] = $curlTable['TableField'];
            }
            
            $CI->db->insert('sys_log_tc_farmgate_access', $insert_log);
            $LogID = $CI->db->insert_id();
        }else{
            $LogID = '';
        }

        if($url!=''){
            $return['LogID'] = $LogID;
            $sql = "UPDATE sys_log_tc_farmgate_access SET TimeFinish=?, Response=? WHERE LogID=?";
            $query = @$CI->db->query($sql, array(date('Y-m-d H:i:s'), $return['message'], $LogID));
        }
        return $return;
    }
}   

if (! function_exists('GetDatesFromRange')) {
    function GetDatesFromRange($start, $end, $format = 'Y-m-d') {
        $array = array();
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
        foreach($period as $date) {
            $array[] = $date->format($format);
        }
        return $array;
    }
}

if (! function_exists('getTokenCognito'))
{
    function getTokenCognito($UserID){
        $CI = & get_instance();
      
        $query = @$CI->db->query("SELECT UserMobileToken as Token FROM sys_user WHERE UserId=? ", array($UserID))->row();
        return $query->Token;
    }
}

if (! function_exists('dynamicSettingAPIPayment'))
{
    function dynamicSettingAPIPayment($baseUrl){
        $app = strpos($baseUrl,"app");
        $demo = strpos($baseUrl,"demo.");
        $demolive = strpos($baseUrl,"demo-live");
        $devel = strpos($baseUrl,"devel");
        $local = strpos($baseUrl,"local");

        if($app>0){
            $url ="https://api-traceability.koltiva.com";
            $TraceKey ="TtL2Ouo1ec!roial0meiiP2dnrcPalv";
            $link ="live";
        }else if($demo>0){
            $url ="https://api-traceability-demo.koltiva.com";
            $TraceKey ="aocmP1l20e!iOTreD2mla";
            $link ="demo";
        }else if($demolive>0){
            $url ="https://api-traceability-demo.koltiva.com";
            $TraceKey ="acelm!toePr22Temlp1Olv0aineD";
            $link ="demo-live";
        }else if($devel>0){
            $url ="https://api-traceability-demo.koltiva.com";
            $TraceKey ="aocmP1l20e!iOTreD2mla";
            $link ="devel";
        }else{
            $url ="https://api-traceability-demo.koltiva.com";
            $TraceKey ="aocmP1l20e!iOTreD2mla";
            $link ="local";
        }

        return [
            'url'      => $url,
            'traceKey' => $TraceKey,
            'link'     => $link
        ];
    }
}

if (! function_exists('checkExistingDataCMSNew'))
{
    function checkExistingDataCMSNew($id, $parameter) {
        $CI =& get_instance();
        $checkExistingData = $CI->db->where('ObjectID', (int) $id)
                                      ->where('ObjectType', $parameter)
                                      ->get('cms_content')->result();

        if (!empty($checkExistingData)) {
            $CI->db->delete('cms_content', [
                'ObjectID'   => (int) $id,
                'ObjectType' => $parameter
            ]);
        }
    }
}


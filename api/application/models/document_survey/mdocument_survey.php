<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-10 11:42:37
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdocument_survey extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getGridDocumentSurvey($MemberID){
        $sql="SELECT
                WillingnesParticipate AS ConsentStatus,
                WillingnesSignature AS ConsentFile,
                WithdrawalConsentStatus AS WithdrawalConsentStatus,
                WithdrawalConsentSign AS WithdrawalConsentFile,
                WillingnesCommit AS WillingnesCommit,
                WillingnesCommitSignature AS WillingnesCommitSignature
            FROM
                ktv_members a
            WHERE
                a.`MemberID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array((int) $MemberID));
        $data = $query->row_array();

        $arrReturn = array();

        //Project Background
        // $arrReturn[0]['DocNameID'] = 'ProjBg';
        // $arrReturn[0]['DocName'] = lang('Project Background');
        // $arrReturn[0]['Status'] = '-';
        // $arrReturn[0]['StatusId'] = '2';
        // $arrReturn[0]['FileAvail'] = 'No';

        //Consent Notes
        $arrReturn[0]['DocNameID'] = 'ConNotes';
        $arrReturn[0]['DocName'] = lang('Consent Notes');
        $arrReturn[0]['StatusId'] = $data['ConsentStatus'];
        if($data['ConsentStatus'] == "1"){
            $arrReturn[0]['Status'] = lang('Yes');
        }else{
            $arrReturn[0]['Status'] = lang('No');
        }
        if($data['ConsentFile'] != ""){
            $arrReturn[0]['FileAvail'] = 'Yes';
        }else{
            $arrReturn[0]['FileAvail'] = 'No';
        }

        //RSPO Document
        $arrReturn[1]['DocNameID'] = 'RSPODoc';
        $arrReturn[1]['DocName'] = lang('RSPO Document');
        $arrReturn[1]['StatusId'] = $data['WillingnesCommit'];
        if($data['WillingnesCommit'] == "1"){
            $arrReturn[1]['Status'] = lang('Yes');
        }else{
            $arrReturn[1]['Status'] = lang('No');
        }
        if($data['WillingnesCommitSignature'] != ""){
            $arrReturn[1]['FileAvail'] = 'Yes';
        }else{
            $arrReturn[1]['FileAvail'] = 'No';
        }

        //Withdrawal
        $arrReturn[2]['DocNameID'] = 'Withdrawal';
        $arrReturn[2]['DocName'] = lang('Withdrawal of Consent Notes');
        $arrReturn[2]['StatusId'] = $data['WithdrawalConsentStatus'];
        if($data['WithdrawalConsentStatus'] == "1"){
            $arrReturn[2]['Status'] = lang('Yes');
        }else{
            $arrReturn[2]['Status'] = lang('No');
        }
        if($data['WithdrawalConsentFile'] != ""){
            $arrReturn[2]['FileAvail'] = 'Yes';
        }else{
            $arrReturn[2]['FileAvail'] = 'No';
        }

        return $arrReturn;
    }

    public function CheckFarmerPartnerID($MemberID){
        $sql="SELECT
                a.`PartnerID`
            FROM
                ktv_members a
            WHERE
                a.`MemberID` = ?
            LIMIT 1";
        $query = $this->db->query($sql,array($MemberID));
        $data = $query->row_array();
        return $data['PartnerID'];
    }

}
?>
<?php

/**
 * Authentication Model for API
 *
 * @author ardiantoro@koltiva.com
 */
class Mcashflow extends CI_Model {

    function __construct() {
        parent::__construct();
        ini_set('display_errors',true);
        error_reporting(E_ALL);
    }

    function getCashflow($class = 111) {
        
        $data = array();
        $coopID = getCoopID();
        $this->db->select('CoaTitle,CoaCode');
        $this->db->from('accounting_coa');
        $this->db->where('CoopID',$coopID);
        $this->db->where('CoaGroupCode',$class);
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $data = $Q->result_array();
            foreach ($data as $key => $value) {
                $data[$key]['transactions'] = $this->getCoaTransaction($value['CoaCode']);
            }
        }
        
        return $data;
    }
    
    function getCoaSaldo($Coa) {
        
        $sql = 'SELECT SUM(IF(JournalDetailType = 1,IFNULL(JournalDetailSum,0),(IFNULL(JournalDetailSum,0) * -1))) saldo FROM accounting_journal_detail left JOIN `accounting_coa` coa ON coa.CoaCode = accounting_journal_detail.CoaCode WHERE CoaCode = "'.$Coa.'"';
        $Q = $this->db->query($sql);
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->saldo;
        }
        return 0;
    }
    
    function getCoaTransaction($coa) {
        
        $data = array();
        
        $this->db->select('JournalMemo,IF(JournalDetailType = 1,JournalDetailSum,0) AS debet, IF(JournalDetailType = 2,JournalDetailSum,0) AS kredit, JournalDate',false);
        $this->db->from('accounting_journal_detail');
        $this->db->join('accounting_journal','accounting_journal.JournalID = accounting_journal_detail.JournalID','left');
        $this->db->join('accounting_coa','accounting_coa.CoaCode = accounting_journal_detail.CoaCode','left');
        $this->db->where('accounting_journal_detail.CoaCode',$coa);
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $data = $Q->result_array();
        }
        
        return $data;
    }

}

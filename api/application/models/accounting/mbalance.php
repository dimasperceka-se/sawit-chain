<?php

/**
 * Authentication Model for API
 *
 * @author hostune
 */
class mbalance extends CI_Model {

    public $coop;
    
    function __construct() {
        parent::__construct();
        $this->coop = getCoopID();
        ini_set('display_errors',true);
        error_reporting(E_ALL);
    }

    function getCoaGroupParent($class = 1) {
        
        $data = array();
        
        $this->db->select('CoaGroupTitle,CoaGroupCode');
        $this->db->from('accounting_coa_group');
        $this->db->where('coaClassID',$class);
        $this->db->where('CoopID',$this->coop);
        $this->db->where('coaGroupParent',null);
        $Q = $this->db->get();
        if($Q->num_rows() > 0) {
            $data = $Q->result_array();
            foreach($data as $key => $values) {
                $data[$key]['children'] = $this->getCoaGroupChildrenByParent($values['CoaGroupCode']);
            }
        }
        
        return $data;
    }
    
    function getCoaGroupChildrenByParent($parent = false) {
        $data = array();
        if($parent) {
            $this->db->select('CoaGroupTitle,CoaGroupCode');
            $this->db->from('accounting_coa_group');
            $this->db->where('coaGroupParent',$parent);
            $this->db->where('CoopID',$this->coop);
            $Q = $this->db->get();
            if($Q->num_rows() > 0) {
                $data = $Q->result_array();
                foreach($data as $key => $values) {
                    $data[$key]['saldo'] = $this->getCoaSaldo($values['CoaGroupCode']);
                }
            }
        }
        
        return $data;
    }
    
    function getCoaSaldo($groupCoa) {
        
        $sql = 'SELECT SUM(IF(JournalDetailType = 1,IFNULL(JournalDetailSum,0),(IFNULL(JournalDetailSum,0) * -1))) saldo FROM accounting_journal_detail left JOIN `accounting_coa` coa ON coa.CoaCode = accounting_journal_detail.CoaCode AND coa.CoopID = "'.$this->coop.'" WHERE CoaGroupCode = "'.$groupCoa.'" AND accounting_journal_detail.CoopID = ' . $this->coop;
        $Q = $this->db->query($sql);
        if($Q->num_rows() > 0) {
            $row = $Q->row();
            return $row->saldo;
        }
        return 0;
    }

}

<?php

class Mcashbox extends CI_Model {

	public function __construct() {
        parent::__construct();
    }
	
	function getList($start = 0, $limit = 20, $sort = 'cashSourceID', $dir = 'DESC', $filter = array()){
		$CoopID = getCoopID();

		$this->db->select(array('*'), FALSE);
        $this->db->from('coop_cash_source');
        $this->db->join('ktv_cooperatives', 'coop_cash_source.CoopID = ktv_cooperatives.CoopID', 'left');
        $this->db->join('ktv_bank', 'coop_cash_source.BankID = ktv_bank.BankID', 'left');
        $this->db->join('accounting_coa', 'coop_cash_source.coaCode = accounting_coa.CoaCode', 'left');
        $this->db->where('coop_cash_source.CoopID', $CoopID);

        $this->db->like($filter);

        $total = $this->db->_compile_select();
        $total = $this->db->query($total)->num_rows();

        $this->db->limit($limit, $start);

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
	}

	function getByID($id){
		$this->db->select(array('*'),FALSE);
		$this->db->from('coop_cash_source');
		$this->db->where('CashSourceID', $id);

		$total = $this->db->_compile_select();
        $total = $this->db->query($total)->num_rows();

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->row_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
	}

	function createCashbox($post){
		$add = array(
			'cashSourceName' => $post['cashboxName'],
			'cashSourceNo' => $post['bankAccNo'],
			'coaCode' => $post['coa'],
			'CoopID' => getCoopID(),
			'BankID' => $post['bank'],
		);

		$this->db->insert('coop_cash_source', $add);
		$id = $this->db->insert_id();

		return array('data' => array('id'=>$id, 'msg'=>'Data has been saved'), 'total' => 1);
	}

	function updateCashbox($post){
		$update = array(
			'cashSourceName' => $post['cashboxName'],
			'cashSourceNo' => $post['bankAccNo'],
			'coaCode' => $post['coa'],
			'CoopID' => getCoopID(),
			'BankID' => $post['bank'],
		);

		$this->db->where('cashSourceID', $post['cashboxID']);
		$this->db->update('coop_cash_source', $update);
		$id = $post['cashboxID'];

		return array('data' => array('id'=>$id, 'msg'=>'Data has been updated'), 'total' => 1);
	}

	function deleteCashbox($did){
		$this->db->where('cashSourceID', $did);
		$this->db->delete('coop_cash_source');
		return true;
	}

	function getComboBanks($filter = array()){
		$this->db->select(array('*'),FALSE);
		$this->db->from('ktv_bank');

		$this->db->like($filter);

		$total = $this->db->_compile_select();
        $total = $this->db->query($total)->num_rows();

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
	}

	function getComboCOA($filter = array()){
		$CoopID = getCoopID();

		$this->db->select(array('*, CONCAT(CoaCode," - ",CoaTitle) CoaDisplay'), FALSE);
        $this->db->from('accounting_coa');
        $this->db->where('CoaStatus', 1);
        $this->db->where('CoopID', $CoopID);

        $this->db->like($filter);

        $total = $this->db->_compile_select();
        $total = $this->db->query($total)->num_rows();

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
	}
} //end of class
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Maction extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function getAction($Page, $Start, $Limit, $key){
		$this->db->from('sys_act');
		if ($key != FALSE) {
			# code...
			$this->db->like('AksiName', $key, 'BOTH');
			$this->db->or_like('AksiFungsi', $key, 'BOTH');
		}
		$this->db->limit($Limit, $Start);
		$this->db->order_by('AksiId', 'asc');

		return $this->db->get();
	}

	public function getAllData($key){
		$this->db->from('sys_act');
		if ($key != FALSE) {
			# code...
			$this->db->like('AksiName', $key, 'BOTH');
			$this->db->or_like('AksiFungsi', $key, 'BOTH');
		}
		return $this->db->get();
	}

	public function insertAction($data){
		$this->db->insert('sys_act', $data);

		return $this->db->affected_rows();
	}

	public function updateAction($data, $id){
		$this->db->where('AksiId', $id);
		$this->db->update('sys_act', $data);

		return $this->db->affected_rows();
	}

	public function deleteAction($id){
		$this->db->where('AksiId', $id);
		$this->db->delete('sys_act');

		return $this->db->affected_rows();
	}

}

/* End of file maction.php */
/* Location: ./application/models/system/maction.php */
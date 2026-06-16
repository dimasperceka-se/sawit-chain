<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdocument extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
    }

    public function getDocuments()
    {
        $query = $this->db->get('ktv_documents', 5);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

}

/* End of file mannouncement.php */
/* Location: ./application/models/mannouncement.php */
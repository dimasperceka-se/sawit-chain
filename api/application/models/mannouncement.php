<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mannouncement extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
    }

    public function getAnnoucements()
    {
        $this->db->where('Type', 'Announcement');
        $this->db->order_by('DateCreated', 'desc');
        $query = $this->db->get('ktv_notification', 10);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        return false;
    }

}

/* End of file mannouncement.php */
/* Location: ./application/models/mannouncement.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpage extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getInfo($module)
    {
        $sql = "
SELECT 
    i.id
    , i.MenuId
    , i.Content
    , i.CreatedBy
    , i.DateCreated
    , i.UpdatedBy
    , i.DateUpdated 
FROM
    sys_page_info AS i 
JOIN sys_menu m ON m.MenuId = i.MenuId
WHERE
    m.MenuModule = ?
LIMIT 1
        ";
        $query = $this->db->query($sql, array($module));
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

}

/* End of file mpage.php */
/* Location: ./application/models/mpage.php */
<?php
class Mpartner extends CI_Model {
    function readPartners(){
        $sql = "
            select PartnerID as id,PartnerName as label
            from ktv_program_partner
            ORDER BY PartnerName";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
?>
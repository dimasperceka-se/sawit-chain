<?php
class Msys_act extends CI_Model {

    function getSysAct(){
        $sql = "SELECT 
                    AksiId as id, AksiName as label
                from sys_act";
        $query = $this->db->query($sql);
        
        return array(
         'data'      => $query->result_array(),
        );
        
    }


}
?>

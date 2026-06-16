<?php
    class Mlanguage extends CI_Model {
        function readLanguages(){
            $sql = "
                SELECT 
                    Name as id,
                    Name as label
                FROM 
                    sys_language
                ORDER BY 
                    Name Asc
            ";
            $query = $this->db->query($sql);
            return $query->result_array();
        }

    }
?>
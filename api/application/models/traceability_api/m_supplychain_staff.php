<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf
 */
class m_supplychain_staff extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }

    public function get_data($StaffID,$key,$start,$limit,$sortingField,$sortingDir){
        if($sortingField == "") $sortingField = 'srel.SupplychainID';
        if($sortingDir == "") $sortingDir = 'ASC'; 
     
        if($key <> ''){
            $where = " AND ( vso.Name like '%$key%' OR m.MemberDisplayID = '$key' )";
        }
        
        $sql="SELECT
            srel.StaffRelID
            , m.MemberDisplayID
            , vso.Name
            , srel.StartDate
            , srel.EndDate
        FROM
            `ktv_tc_supplychain_staff_rel` srel
        LEFT JOIN
            view_tc_supplychain_org vso on vso.SupplychainID = srel.SupplychainID
        LEFT JOIN
            ktv_members m on m.MemberID = vso.ObjID AND vso.ObjType = 'agent'
        WHERE
            srel.StaffID = ?
        AND
            srel.StatusCode = 'active'
            $where
        ORDER BY $sortingField $sortingDir
        LIMIT ?,?";
        $query = $this->db->query($sql,array($StaffID,(int) $start,(int) $limit)); 
        //echo '<pre>';
        //echo $this->db->last_query();die;
        $result['data'] = $query->result_array();
        
        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }
}

?>

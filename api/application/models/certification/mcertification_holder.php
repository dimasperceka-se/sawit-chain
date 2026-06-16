<?php
class Mcertification_holder extends CI_Model {
    
    public function listHolderType(){
        
        $data[] = array("id"=>"farmer_group","label"=>lang("Farmer Group"));
        $data[] = array("id"=>"cooperative","label"=>lang("Cooperative"));
        $data[] = array("id"=>"gapoktan","label"=>lang("Gapoktan"));

        return $data;
    }
    
    public function readCertificationHolders($tipe, $key, $start = 0, $limit = 50)
    {
        if($tipe == "gapoktan"){
            $tipe = "farmer_group";
        }
        if($tipe==""){ $tp = "#"; }else{ $tp = ""; }
        $sql = "SELECT
            ch.CertHolderID
            , ch.CertHolderOrgName HolderName
            , CASE
                    WHEN ch.ObjType = 'farmer_group' THEN 'Farmer Group'
                    WHEN ch.ObjType = 'cooperative' THEN 'Cooperative'
                    ELSE '-' 
                END HolderType
            , c.CertProgName ProgramName
            , ch.GIPNumber
            , ch.CertProgMemberID
            , ch.CertProgMemberDate 
        FROM
            ktv_certification_holders ch
        LEFT JOIN 
                ktv_ref_certification_program c ON c.CertProgID = ch.CertProgID
        WHERE
            ch.StatusCode = 'active'
        $tp AND ch.ObjType=?
        AND (ch.CertHolderOrgName LIKE ? OR c.CertProgName LIKE ? OR ch.GIPNumber LIKE ?)
                            -- filter--
                    LIMIT ?, ?";
        $query = $this->db->query($sql,array($tipe, "%$key%", "%$key%", "%$key%", intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }
        return false;
    }
    
    public function listFarmerGroup($ObjID = null){
        $where = '';
        if($ObjID != ''){
            $where .= ' AND fg.FarmerGroupID = "'.$ObjID.'"';
        }

        $sql = "SELECT
        fg.FarmerGroupID id,
            fg.GroupName label,
            fg.Chairman responsible,
            ch.CertHolderID
        FROM
            ktv_farmer_group fg
        LEFT JOIN 
            ktv_certification_holders ch ON ch.ObjID = fg.FarmerGroupID AND ch.ObjType = 'farmer_group' 
        WHERE
            fg.StatusCode = 'active'
        AND
            ch.CertHolderID IS NULL
        $where
        ORDER BY GroupName ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }

    public function listCooperative($ObjID = null){
        $where = '';
        if($ObjID != ''){
            $where .= ' AND c.CoopID = "'.$ObjID.'"';
        }

        $sql ="SELECT
            c.CoopID id,
            c.CoopName label,
            c.Chairman responsible
        FROM
            ktv_cooperatives c
        WHERE
            c.StatusCode = 'active'
            $where
        ORDER BY
            CoopName ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }
    
    public function listPrograms(){
        $sql = "SELECT CertProgID id, CertProgName label FROM ktv_ref_certification_program WHERE StatusCode='active'";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
        
    }
    
    public function createCertificationHolder($ObjType,$ObjID,$CertProgID, $GIPNumber, $CertProgMemberID, $CertProgMemberDate, $userid){
        if($ObjType == 'farmer_group'){
            $dataOrg = $this->listFarmerGroup($ObjID);
        }

        if($ObjType == 'cooperative'){
            $dataOrg = $this->listCooperative($ObjID);
        }

        $dataPost['CertHolderResponsible']  = $dataOrg[0]['responsible'];
        $dataPost['CertHolderOrgName']      = $dataOrg[0]['label'];
        $dataPost['ObjType']            = $ObjType;
        $dataPost['ObjID']              = $ObjID;
        $dataPost['CertProgID']         = $CertProgID;
        $dataPost['GIPNumber']          = $GIPNumber;
        $dataPost['CertProgMemberID']   = $CertProgMemberID;
        $dataPost['CertProgMemberDate'] = $CertProgMemberDate;
        $dataPost['CreatedBy']          = $userid;
        $dataPost['DateCreated']        = date('Y-m-d H:i:s');
        $dataPost['StatusCode']         = 'active';

        $query = $this->db->insert('ktv_certification_holders',$dataPost);

        if ($query) {
            $results['success']     = true;
            $results['message']     = "record created.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to create record";
        }
        return $results;
    }
    
    public function readCertificationHolderDetail($CertHolderID){
        $sql = "SELECT a.*, b.ObjType tipe FROM ktv_certification_holders a LEFT JOIN view_tc_supplychain_org b ON a.SupplychainID=b.SupplychainID WHERE a.CertHolderID=?";
        $query = $this->db->query($sql,array($CertHolderID));
        $return = $query->result_array();
        return $return[0];
    }
    
    public function updateCertificationHolder($ObjType,$ObjID,$CertProgID, $GIPNumber, $CertProgMemberID, $CertProgMemberDate, $userid, $CertHolderID){
        if($ObjType == 'farmer_group'){
            $dataOrg = $this->listFarmerGroup($ObjID);
        }

        if($ObjType == 'cooperative'){
            $dataOrg = $this->listCooperative($ObjID);
        }

        $dataPost['CertHolderResponsible']  = $dataOrg[0]['responsible'];
        $dataPost['CertHolderOrgName']      = $dataOrg[0]['label'];
        $dataPost['ObjType']            = $ObjType;
        $dataPost['ObjID']              = $ObjID;
        $dataPost['CertProgID']         = $CertProgID;
        $dataPost['GIPNumber']          = $GIPNumber;
        $dataPost['CertProgMemberID']   = $CertProgMemberID;
        $dataPost['CertProgMemberDate'] = $CertProgMemberDate;
        $dataPost['LastModifiedBy']     = $userid;
        $dataPost['DateUpdated']        = date('Y-m-d H:i:s');

        $this->db->where('CertHolderID',$CertHolderID);
        $query = $this->db->update('ktv_certification_holders',$dataPost);

        if ($query) {
            $results['success']     = true;
            $results['message']     = "record updated.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to update record.";
        }
        return $results;
    }
    
    public function deleteCertificationHolder($userid, $CertHolderID){
        $sql = "
            UPDATE ktv_certification_holders SET StatusCode='nullified', LastModifiedBy=?, DateUpdated=NOW() WHERE CertHolderID=?";
        $query = $this->db->query($sql, array($userid, $CertHolderID));
        if ($query) {
            $results['success']     = true;
            $results['message']     = "record deleted.";
        } else {
            $results['success']     = false;
            $results['message']     = "Failed to delete record.";
        }
        return $results;
    }

}
?>

<?php
class Mreport_mill_refinery extends CI_Model {

    public function readTree()
    {
        $data['children'] = $this->getTree($get);
        return $data;
    }

    public function readTreeRefinery()
    {
        $data['children'] = $this->getTreeRefinery($get);
        return $data;
    }

    public function getTree($get){
        $relation = array();
        
        $id = $_SESSION['SupplychainID'];

        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesPartner = " AND apm.apmiPartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlHakAksesPartner = " AND org1.SupplychainID = '$_SESSION[SupplychainID]' ";
        }elseif($_SESSION['role'] == "Refinery"){
            $sqlHakAksesPartner = " AND org1.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
        }
        // echo "<pre>";
        // print_r($_SESSION);
        // die;

        //kalau user private mill/staff mill dia headernya mill
        $sql_parent = "SELECT
                CONCAT('Mill',org1.SupplychainID) id
                , org1.SupplychainID
                , 0 ParentID
                , org1.ObjType
                , org1.ObjID
                , CONCAT(m.MillDisplayID,' - ',m.MillName) AS `Name`
                , m.CompanyName AS CompanyName
            FROM
                view_tc_supplychain_org org1
            LEFT JOIN ktv_mill m ON org1.ObjType='mill' AND m.MillID=org1.ObjID
            LEFT JOIN ktv_access_partner_mill apm on apm.apmiMillID = org1.ObjID
            WHERE
                1=1
            AND
                org1.ObjType = 'mill'
            $sqlHakAksesPartner GROUP BY org1.SupplychainID ORDER BY Name ASC";
        $parent = $this->db->query($sql_parent);

        if($parent->num_rows > 0){
            $data_parent = $parent->result_array();
            foreach($parent->result() as $key => $row){
                list($total_transaction,$VolumeNetto)= $this->getTransaction($row->SupplychainID);
                $child = $this->getTraderCategory($row->SupplychainID);
                $data_parent[$key]["total_transaction"] = $child['total_trans'];
                $data_parent[$key]["VolumeNetto"]       = $VolumeNetto;
                $data_parent[$key]["children"]          = $child['data'];
            }
            $relation = array_merge($relation, $data_parent);
        }

        return $relation;
    }

    public function getTreeRefinery($get){
        $relation = array();
        
        $id = $_SESSION['SupplychainID'];

        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesPartner = " AND org1.PartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlHakAksesPartner = " AND org1.SupplychainID = '$_SESSION[SupplychainID]' ";
        }elseif($_SESSION['role'] == "Refinery"){
            $sqlHakAksesPartner = " AND org1.SupplychainID = '$_SESSION[SupplychainID]' ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
        }

        //Cek kalau sebagai refinery / admin / user koltiva dia header treenya refinery
        $sql_refinery = "SELECT
                    CONCAT('Refinery',org1.SupplychainID) id
                    , org1.SupplychainID
                    , 0 ParentID
                    , org1.ObjType
                    , org1.ObjID
                    , CONCAT(m.RefineryDisplayID,' - ',m.RefineryName) AS `Name`
                    , m.CompanyName AS CompanyName
                FROM
                    view_tc_supplychain_org org1
                LEFT JOIN 
                    ktv_refinery m ON org1.ObjType='refinery' AND m.RefineryID=org1.ObjID
                WHERE
                    1=1
                AND
                    org1.ObjType = 'refinery'
                    $sqlHakAksesPartner
                GROUP BY m.RefineryID
                ORDER BY Name ASC";
        $parent = $this->db->query($sql_refinery);
        if($parent->num_rows > 0){
            $data_parent = $parent->result_array();
            foreach($parent->result() as $key => $row){
                list($total_transaction,$VolumeNetto)= $this->getTransactionRefinery($row->SupplychainID);
                $data_parent[$key]["total_transaction"] = $total_transaction;
                $data_parent[$key]["VolumeNetto"]       = $VolumeNetto;
                $data_parent[$key]["children"] = $this->getMillRelation($row->SupplychainID);
                if($total_transaction == 0 ){
                    $data_parent[$key]["leaf"]       = true;
                }
            }
            $relation = array_merge($relation, $data_parent);
        }

        return $relation;
    }

    function getMillRelation($SupplychainID){
        $relation = array();
        $sql = "SELECT
                CONCAT('Mill',org1.SupplychainID) id
                , org1.SupplychainID
                , 'Refinery$SupplychainID' ParentID
                , org1.ObjType
                , org1.ObjID
                , CONCAT( m.MillDisplayID, ' - ', m.MillName ) AS `Name`
                , m.CompanyName AS CompanyName
            FROM
                view_tc_supplychain_org org1
            LEFT JOIN 
                ktv_mill m ON org1.ObjType = 'mill' AND m.MillID = org1.ObjID
            INNER JOIN 
                ktv_access_partner_mill apm on apm.apmiMillID = org1.ObjID
            LEFT JOIN
                ktv_tc_supplychain_org_rel orel on orel.ChildID = org1.SupplychainID AND orel.StatusCode = 'active'
            WHERE
                1 = 1 
                AND org1.ObjType = 'mill' --                 $sqlHakAksesPartner
                AND orel.ParentID = ?
            GROUP BY org1.SupplychainID
            ORDER BY
            NAME ASC";
        $parent = $this->db->query($sql,array($SupplychainID));
        if($parent->num_rows > 0){
            $data_parent = $parent->result_array();
            foreach($parent->result() as $key => $row){
                list($total_transaction,$VolumeNetto)= $this->getTransaction($row->SupplychainID);
                $child = $this->getTraderCategory($row->SupplychainID);
                $data_parent[$key]["total_transaction"] = $child['total_trans'];
                $data_parent[$key]["VolumeNetto"]       = $VolumeNetto;
                $data_parent[$key]["children"]          = $child['data'];
            }
            $relation = array_merge($relation, $data_parent);
        }
        return $relation;
    }

    function getTransactionRefinery($SupplychainID){
        $sql = "SELECT
                IFNULL(count(DISTINCT rd.DespatchDetailID),0) jml_transaksi
                , IFNULL(SUM(rd.ReceptionVolume),0) VolumeNetto
            FROM
                `ktv_tc_reception` r
            LEFT JOIN
                ktv_tc_despatch d on d.DespatchID = r.DespatchID
            LEFT JOIN
                ktv_tc_reception_detail rd on rd.ReceptionID = r.ReceptionID
            WHERE
                r.SupplychainID = ?
            AND 
                r.StatusCode = 'active'";
        $query = $this->db->query($sql,array($SupplychainID));
        if($query->num_rows > 0){
            return array($query->row()->jml_transaksi,$query->row()->VolumeNetto);
        }else{
            return array(0,0);
        }
    }

    function getTransaction($SupplychainID){
       
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                IFNULL(count(st2.SupplyTransID),0) AS jml_transaksi,
                IFNULL(SUM(st1.VolumeNetto),0) AS VolumeNetto
                FROM
                    ktv_tc_supplychain_batch sb1
                LEFT JOIN 
                    ktv_tc_supplychain_transaction st1 ON st1.SupplyBatchID = sb1.SupplyBatchID
                LEFT JOIN 
                    ktv_tc_supplychain_transaction st2 ON st2.SupplyID = sb1.SupplyBatchID 
                WHERE
                    st2.SupplychainID = ?
                    AND sb1.StatusCode = 'active'
                    AND st1.SupplyID IS NOT NULL";
        $query = $this->db->query($sql,array($SupplychainID,$SupplychainID));
        
        if($query->num_rows > 0){
            return array($query->row()->jml_transaksi,$query->row()->VolumeNetto);
        }else{
            return array(0,0);
        }
    }

    function getTraderCategory($parent_id){
        
        //Data Owned Estate
        $dataOwnedEstate = $this->getTraderData(11,$parent_id);
        $data[0]["id"]       = "TRADER11$parent_id";
        $data[0]["parentID"] = "Mill".$parent_id;
        $data[0]["Name"]     = lang("Owned Estate");
        if(!empty($dataOwnedEstate)){
            $data[0]["children"] = $dataOwnedEstate;
        }else{
            $data[0]['leaf'] = true;
        }
        
        //Data External Estate
        $dataExternalEstate = $this->getTraderData(12,$parent_id);
        $data[1]["id"]       = "TRADER12$parent_id";
        $data[1]["parentID"] = "Mill".$parent_id;
        $data[1]["Name"]     = lang("External Estate");
        if(!empty($dataExternalEstate)){
            $data[1]["children"] = $dataExternalEstate;
        }else{
            $data[1]['leaf'] = true;
        }
        
        //Data Plasma
        $dataPlasma = $this->getTraderData(14,$parent_id);
        $data[2]["id"]       = "TRADER14$parent_id";
        $data[2]["parentID"] = "Mill".$parent_id;
        $data[2]["Name"]     = lang("Plasma");
        if(!empty($dataPlasma)){
            $data[2]["children"] = $dataPlasma;
        }else{
            $data[2]['leaf'] = true;
        }
        
        //Data Agent
        $dataAgent = $this->getTraderData(array(5,6,7,8,9,10,13),$parent_id);
        $data[3]["id"]       = "TRADER13$parent_id";
        $data[3]["parentID"] = "Mill".$parent_id;
        $data[3]["Name"]     = lang("Agent/Dealer/Vendor");
        if(!empty($dataAgent)){
            $data[3]["children"] = $dataAgent;
        }else{
            $data[3]['leaf'] = true;
        }
        
        //Data Direct Smallholder
        $dataAgent = $this->getTraderDataDirect('direct',$parent_id);
        $data[4]["id"]       = "TRADERDirect$parent_id";
        $data[4]["parentID"] = "Mill".$parent_id;
        $data[4]["Name"]     = lang("Direct Smallholder");
        if(!empty($dataAgent)){
            $data[4]["children"] = $dataAgent;
        }else{
            $data[4]['leaf'] = true;
        }
        
        //Data Untraceable
        $dataAgent = $this->getTraderDataUntraceable('untrace',$parent_id);
        $data[5]["id"]       = "TRADERUntrace$parent_id";
        $data[5]["parentID"] = "Mill".$parent_id;
        $data[5]["Name"]     = lang("Non-Traceable");
        if(!empty($dataAgent)){
            $data[5]["children"] = $dataAgent;
        }else{
            $data[5]['leaf'] = true;
        }

        $total_trans = 0;
        if(count($data)>0){
            for($i=0;count($data)>$i;$i++){
                if(count($data[$i]['children'])>0){
                    for($j=0;count($data[$i]['children'])>$j;$j++){
                        if($data[$i]['children'][$j]['total_transaction'] != ''){
                            $total_trans += $data[$i]['children'][$j]['total_transaction'];
                        }else{
                            $total_trans += 0;
                        }
                    }
                }
            }
        }

        $return['total_trans']  = $total_trans;
        $return['data']         = $data;

        return $return;
    }

    function getTraderDataDirect($role,$SupplychainID){
        $sql = "SELECT
                    m.MemberID id
                    , 'TRADERDirect$SupplychainID' parentID
                    , CONCAT(m.MemberDisplayID,' - ',m.MemberName) Name
                    , 1 leaf
                    , COUNT(st.SupplyTransID) total_transaction
                    , SUM(st.VolumeNetto) VolumeNetto
                FROM
                    ktv_tc_supplychain_transaction st
                LEFT JOIN
                    ktv_members m on m.MemberID = st.SupplyID
                -- LEFT JOIN
                -- 	ktv_access_partner_member apm on apm.apmMemberID = m.MemberID
                -- LEFT JOIN
                -- 	ktv_mill mill on mill.PartnerID = apm.apmPartnerID
                -- LEFT JOIN
                -- 	view_tc_supplychain_org vso on vso.ObjID = mill.MillID
                WHERE
                    st.StatusCode = 'active'
                AND
                    st.SupplyType = 'Farmer'
                AND 
                    m.SupplybaseType = 'direct'
                AND
                    st.SupplychainID = ?
                GROUP BY
                    m.MemberID";
        $query = $this->db->query($sql,array($SupplychainID));
        if($query->num_rows > 0){
            return $query->result_array();
        }else{
            return array("id"=>"Empty-$role-$SupplychainID","parnetID"=>"TRADER$role$SupplychainID","Name"=>"-","leaf"=>true);
        }
    }

    function getTraderData($role,$SupplychainID){
        if(is_array($role)){
            $role = implode(",",$role);
        }
        if($role == "11" OR $role == "12" OR $role == "14"){
            $id = $role;
        }else{
            $id = "13";
        }
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS
            -- CONCAT('TRADER',sb2.SupplyOrgID,'2055') id, 
            sb2.SupplyOrgID AS SupplyChainID
            , 'TRADER1180' parentID
            , vso1.Name
            , IFNULL(count(DISTINCT(CONCAT(st2.SupplyTransID,st2.SupplychainID))),0) AS total_transaction
            , IFNULL(SUM(st2.VolumeNetto),0) AS VolumeNetto
            , COUNT(DISTINCT(sb2.SupplyOrgID)) AS SupplyOrgID
            , COUNT(DISTINCT(st.SupplyID)) AS total_farmer
        FROM
            ktv_tc_supplychain_transaction st
        LEFT JOIN 
            ktv_tc_supplychain_batch sb2 ON sb2.SupplyBatchID=st.SupplyID
        LEFT JOIN 
            ktv_tc_supplychain_transaction st2 ON st2.SupplyBatchID=sb2.SupplyBatchID
        LEFT JOIN 
            ktv_tc_supplychain_batch sb3 ON sb3.SupplyBatchID=st2.SupplyID 
        LEFT JOIN 
            ktv_tc_supplychain_transaction st3 ON st3.SupplyBatchID=sb3.SupplyBatchID
        LEFT JOIN 
            ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryId = st.SupplyID 
        LEFT JOIN 
            ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID= sb2.SupplyBatchID
        LEFT JOIN 
            view_tc_supplychain_org vso1 ON vso1.SupplychainID = ktsd.SupplychainID
        LEFT JOIN 
            view_tc_supplychain_org vso2 ON vso2.SupplychainID = ktsd.SupplyDestMillOrgID
        LEFT JOIN
            ktv_member_role mr on mr.MemberID = vso1.ObjID 
        LEFT JOIN
            ktv_members mem on mem.MemberID = mr.MemberID
        LEFT JOIN 
            ktv_mill km ON km.MillID = vso1.ObjID AND vso1.ObjType = 'mill'
        LEFT JOIN 
            ktv_members_extension kme ON kme.MemberID = vso1.ObjID AND vso1.ObjType = 'trader'
        LEFT JOIN 
            ktv_members_extension kmee ON kmee.MemberID = vso1.ObjID 
        WHERE
            vso2.SupplychainID  = ?
        AND 
            st2.VolumeNetto IS NOT null
        AND 
            mr.MRoleID IN ($role)
        GROUP BY
            mr.MemberID";

        $query = $this->db->query($sql,array($SupplychainID,$SupplychainID));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if($query->num_rows > 0){
            $datareturn = $query->result_array();
            foreach($query->result_array() as $key => $row){
                //Jika Bukan Agent Dia Berhenti
                if($role == 11 || $role == 12){
                    $datareturn[$key]["leaf"] = true;
                } elseif ($role == 14){
                    //Data Farmer Plasma
                    $dataFarmerAgent = $this->getTraderDataFarmer('14',$row["SupplyChainID"],$SupplychainID);
                    if(!empty($dataFarmerAgent)){
                        $datareturn[$key]["children"] = $dataFarmerAgent;
                    }else{
                        $datareturn[$key]['leaf'] = true;
                    }
                } else{
                    //Data Farmer Agent
                    $dataFarmerAgent = $this->getTraderDataFarmer('agent',$row["SupplyChainID"],$SupplychainID);
                    if(!empty($dataFarmerAgent)){
                        $datareturn[$key]["children"] = $dataFarmerAgent;
                    }else{
                        $datareturn[$key]['leaf'] = true;
                    }
                }
            }
            return $datareturn;
        }else{
            return array("id"=>"Empty-Trader-$id-$SupplychainID","parnetID"=>"TRADER$id$SupplychainID","Name"=>"-","leaf"=>true);
        }
    }

    function getTraderDataUntraceable($role,$SupplychainID){
        $sql = "SELECT 
                    CONCAT('UNTRACE',sb.SupplyOrgID,$SupplychainID) id
                    , sb.SupplyOrgID SuppplyChainID
                    , 'TRADERUntrace'
                    , IFNULL(CONCAT(mem.MemberDisplayID,' - ',vso.`Name`), CONCAT(mem.MemberDisplayID,' - ',vso.`Name`)) AS `Name`
                    , 1 total_transaction
                    , IFNULL(sb.ReceiveWeight,SUM(st.VolumeNetto)) VolumeNetto
                    , 0 total_farmer
                    , 1 leaf
                FROM
                    ktv_tc_supplychain_batch sb
                LEFT JOIN
                    ktv_tc_supplychain_transaction st on st.SupplyBatchID = sb.SupplyBatchID
                LEFT JOIN 
                    view_tc_supplychain_org vso ON vso.SupplychainID = sb.SupplyOrgID
                LEFT JOIN 
                    ktv_members_extension kme ON kme.MemberID = vso.ObjID
                LEFT JOIN
                    ktv_members mem on mem.MemberID = vso.ObjID
                WHERE
                    sb.StatusCode = 'active'
                AND
                    sb.SupplyBatchStatus = 'Delivered'
                AND
                    ( st.SupplyTransID IS NULL OR st.SupplyType = 'batch')
                AND
                    sb.SupplyOrgID > 0 
                AND 
                    sb.SupplyOrgID IS NOT NULL
                AND
                    (sb.SupplyDestOrgID = ? OR sb.SupplyDestMillOrgID = ?)
                GROUP BY
                    sb.SupplyOrgID";
        $query = $this->db->query($sql,array($SupplychainID,$SupplychainID));
        
        if($query->num_rows > 0){
            return $query->result_array();
        }else{
            return array("id"=>"Empty-$role-$SupplychainID","parnetID"=>"$MemberID$SupplychainID","Name"=>"-","leaf"=>true);
        }
    }

    function getTraderDataFarmer($role,$MemberID,$SupplychainID){
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    CONCAT('Farmer',st1.SupplyID,$SupplychainID) id
                    , CONCAT('TRADER',$MemberID,$SupplychainID) parentID
                    , CONCAT(m.MemberDisplayID,' - ',m.MemberName) AS `Name`
                    , IFNULL(count(st2.SupplyTransID),0) total_transaction
                    , IFNULL(SUM(st2.VolumeNetto),0) VolumeNetto
                    , COUNT(DISTINCT(SupplyOrgID)) SupplyOrgID
                    , COUNT(DISTINCT(st1.SupplyID)) total_farmer
                    , 1 leaf
                FROM
                    ktv_tc_supplychain_batch sb1
                LEFT JOIN 
                    ktv_tc_supplychain_transaction st1 ON st1.SupplyBatchID = sb1.SupplyBatchID
                LEFT JOIN 
                    ktv_tc_supplychain_transaction st2 ON st2.SupplyID = sb1.SupplyBatchID AND st2.SupplyType = 'Batch'
                LEFT JOIN 
                    ktv_members m ON m.MemberID = st1.SupplyID
                WHERE
                    1 = 1 
                AND sb1.SupplyBatchStatus IN ( 'Delivered') /* AND sb1.SupplyBatchID=0 */
                AND sb1.SupplyOrgID = ?
                AND (sb1.SupplyDestOrgID = ? OR sb1.SupplyDestMillOrgID = ?)
                AND sb1.StatusCode = 'active'
                AND st1.SupplyID IS NOT NULL
                GROUP BY
                    st1.SupplyID 
                    ";
        $query = $this->db->query($sql,array($MemberID,$SupplychainID,$SupplychainID));

        if($query->num_rows > 0){
            return $query->result_array();
        }else{
            return array("id"=>"Empty-$role-$SupplychainID","parnetID"=>"$MemberID$SupplychainID","Name"=>"-","leaf"=>true);
        }
    }
    
    function getRelationBySupplychainID($SupplychainID, &$relation){
        $sql = "SELECT
                    org1.SupplychainID AS id,
                    rel1.ParentID,
                    org1.ObjType,
                    org1.ObjID,
                    CASE org1.ObjType WHEN 'refinery' THEN r.RefineryName WHEN 'mill' THEN m.MillName WHEN 'agent' THEN mb.MemberName ELSE '-' END  AS `Name`,
                    CASE org1.ObjType WHEN 'refinery' THEN r.CompanyName WHEN 'mill' THEN m.CompanyName WHEN 'agent' THEN mx.agCompanyName ELSE '-' END  AS CompanyName
                FROM
                    ktv_tc_supplychain_org_rel rel1
                LEFT JOIN ktv_tc_supplychain_org org1 ON org1.SupplychainID=rel1.ChildID
                LEFT JOIN ktv_refinery r ON org1.ObjType='refinery' AND r.RefineryID=org1.ObjID
                LEFT JOIN ktv_mill m ON org1.ObjType='mill' AND m.MillID=org1.ObjID
                LEFT JOIN ktv_members mb ON org1.ObjType='agent' AND mb.MemberID=org1.ObjID
                LEFT JOIN ktv_members_extension mx ON mx.MemberID=mb.MemberID
                WHERE
                    rel1.StatusCode='active'
                    AND rel1.ParentID = ?";
        $query = $this->db->query($sql, array($SupplychainID));
        if($query->num_rows() > 0){
            //$relation[] = $query->result_array();
            $relation = array_merge($relation, $query->result_array());
            foreach($query->result() as $row){
                $this->getRelationBySupplychainID($row->id, $relation);
                $this->getRelationSupplychainIDxFarmerID($row->id, $relation);
            }
        }else{
            return false;
        }
        return true;
    }

    function getRelationSupplychainIDxFarmerID($SupplychainID, &$relation){
        $sql ="SELECT
                m.MemberID AS ObjID,
                ? AS ParentID,
                'farmer' AS ObjType,
                m.MemberDisplayID AS id,
                m.MemberName AS `Name`
            FROM
                ktv_tc_supplychain_transaction st
                LEFT JOIN ktv_members m ON m.MemberID=st.SupplyID
            WHERE
                st.StatusCode='active' AND st.SupplyType!='Batch' AND st.SupplyID > 0
                AND m.MemberID IS NOT NULL
                AND st.SupplychainID=?
            GROUP BY m.MemberID";
        $query = $this->db->query($sql, array($SupplychainID, $SupplychainID, $SupplychainID));
        if($query->num_rows() > 0){
            $relation = array_merge($relation, $query->result_array());
        }else{
            return false;
        }
        return true;
    }

    function readTreeExport(){

        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
            $sqlHakAksesPartner2 = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesPartner = " AND vtso.PartnerID = '$_SESSION[PartnerID]' ";
            $sqlHakAksesPartner2 = " AND vso.PartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
                $sqlHakAksesPartner2 = "";
            }
        }elseif($_SESSION['role'] == "Mill"){
            $sqlHakAksesPartner = " AND ktsd1.SupplyDestMillOrgID = '$_SESSION[SupplychainID]'";
            $sqlHakAksesPartner2 = " AND vso.PartnerID = '$_SESSION[PartnerID]'";
        }elseif($_SESSION['role'] == "Refinery"){
            $sqlHakAksesPartner = " ";
            $sqlHakAksesPartner2 = " ";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
            $sqlHakAksesPartner2 = " ";
        }
        
        // $sql = "SELECT
        //             st1.SupplyID AgregatorID1
        //             , IF(st1.SupplyType = 'Farmer', m.MemberName, vso.`Name`) 'Agregator1'
        //             , IF(st1.SupplyType = 'Farmer', rm.MRoleID, rm2.MRoleID) 'MRoleIDAgregator1'
        //             , IF(st1.SupplyType = 'Farmer', rm.MRoleName, rm2.MRoleName) 'RoleAgregator1'
        //             , DATE(st1.DateTransaction) 'DateTransactionAgregator1'
        //             , st1.VolumeNetto 'FFBNettoAgregator1'
        //             -- , sb2.SupplyBatchID
        //             , sb2.`Name` 'Agregator2'
        //             , sb2.ObjType 'RoleAgregator2'
        //             , DATE(sb2.SupplyBatchDate) 'DateTransactionAgregator2'
        //             , sb2.VolumeNetto 'FFBNettoAgregator2'
        //             , sb3.DoName 'Agregator3'
        //             , IF(sb3.DoName = ' - ', ' - ','Dealer') 'RoleAgregator3'
        //             , IF(sb3.DoName = ' - ', ' - ',DATE(sb3.ReceivedDate)) 'DateTransactionAgregator3'
        //             , IF(sb3.DoName = ' - ', ' - ',sb3.VolumeNetto) 'FFBNettoAgregator3'
        //             , sb3.MillName 'Agregator4'
        //             , IF(sb3.MillName = ' - ', ' - ','Mill') 'RoleAgregator4'
        //             , IF(sb3.MillName = ' - ', ' - ',DATE(sb3.ReceivedDate)) 'DateTransactionAgregator4'
        //             , IF(sb3.MillName = ' - ', ' - ',sb3.VolumeNetto) 'FFBNettoAgregator4'
        //         FROM
        //             ktv_tc_supplychain_batch sb1
        //         INNER JOIN
        //             ktv_tc_supplychain_transaction st1 on st1.SupplyBatchID = sb1.SupplyBatchID
        //         LEFT JOIN
        //             ktv_members m on m.MemberID = st1.SupplyID AND st1.SupplyType = 'Farmer'
        //         LEFT JOIN
        //             ktv_member_role mr on mr.MemberID = m.MemberID
        //         LEFT JOIN
        //             ktv_ref_member_role rm on rm.MRoleID = mr.MRoleID
        //         LEFT JOIN
        //             view_tc_supplychain_org vso on vso.SupplychainID = st1.SupplyID AND st1.SupplyType <> 'Farmer'
        //         LEFT JOIN
        //             ktv_member_role mr2 on mr2.MemberID = vso.ObjID
        //         LEFT JOIN
        //             ktv_ref_member_role rm2 on rm2.MRoleID = mr2.MRoleID
        //         LEFT JOIN
        //             (
        //                 SELECT
        //                     sb3.SupplyBatchID
        //                     , SUM(sb3.DestWeight) VolumeNetto
        //                     , vso3.`Name`
        //                     , DATE(sb3.SupplyBatchDate) SupplyBatchDate                            
        //                     , rm.MRoleName ObjType
        //                 FROM
        //                     ktv_tc_supplychain_batch sb3
        //                 LEFT JOIN
        //                     view_tc_supplychain_org vso3 on vso3.SupplychainID = sb3.SupplyOrgID
        //                 LEFT JOIN
        //                         ktv_member_role mr on mr.MemberID = vso3.ObjID
        //                 LEFT JOIN
        //                         ktv_ref_member_role rm on rm.MRoleID = mr.MRoleID
        //                 WHERE
        //                     sb3.SupplyBatchStatus = 'Delivered'
        //                 AND
        //                     sb3.StatusCode = 'active'
        //                 AND
        //                     ( ( sb3.SupplyDestOrgID IS NOT NULL AND sb3.SupplyDestOrgID > 0 ) OR ( sb3.SupplyDestMillOrgID IS NOT NULL AND sb3.SupplyDestMillOrgID > 0 ) )
        //                 GROUP BY
        //                     sb3.SupplyBatchID
        //             ) sb2 on sb2.SupplyBatchID = sb1.SupplyBatchID
        //         LEFT JOIN
        //             (
        //                 SELECT
        //                     sb.SupplyBatchID
        //                     , SUM(sb.ReceiveWeight) VolumeNetto
        //                     , sb.SupplyDestDoOrgID
        //                     , sb.ReceivedDate
        //                     , CASE
        //                         WHEN sb.SupplyDestType = 'do' THEN vso2.`Name`
        //                         ELSE ' - '
        //                     END DOName
		// 	                , IFNULL(sb.SupplyDestMillOrgID,sb.SupplyDestOrgID) SupplychainID
        //                     , CASE
        //                         WHEN sb.SupplyDestType = 'do' THEN vso3.`Name`
        //                         WHEN sb.SupplyDestType IS NULL THEN vso1.`Name`
        //                         WHEN sb.SupplyDestType = 'mill' THEN vso1.`Name`
        //                         ELSE ' - '
        //                     END MillName
        //                 FROM
        //                     ktv_tc_supplychain_batch sb
        //                 LEFT JOIN
        //                     view_tc_supplychain_org vso1 on vso1.SupplychainID = sb.SupplyDestOrgID
        //                 LEFT JOIN
        //                     view_tc_supplychain_org vso2 on vso2.SupplychainID = sb.SupplyDestDoOrgID
        //                 LEFT JOIN
        //                     view_tc_supplychain_org vso3 on vso3.SupplychainID = sb.SupplyDestMillOrgID
        //                 WHERE
        //                     sb.SupplyBatchStatus = 'Delivered'
        //                 AND
        //                     sb.StatusCode = 'active'
        //                 AND
        //                     ( ( sb.SupplyDestOrgID IS NOT NULL AND sb.SupplyDestOrgID > 0 ) OR ( sb.SupplyDestMillOrgID IS NOT NULL AND sb.SupplyDestMillOrgID > 0 ) )
        //                 GROUP BY
        //                     sb.SupplyBatchID
        //             ) sb3 on sb3.SupplyBatchID = sb1.SupplyBatchID
        //         LEFT JOIN
        //             view_tc_supplychain_org vso4 on vso4.SupplychainID = sb3.SupplychainID
        //         LEFT JOIN 
        //             ktv_tc_supplychain_delivery_detail ktsdd1 ON ktsdd1.DeliveryID = sb1.SupplyBatchID
        //         LEFT JOIN 
        //             ktv_tc_supplychain_delivery ktsd1 ON ktsd1.DeliveryID = ktsdd1.DeliveryID
        //         LEFT JOIN 
        //             ktv_tc_supplychain_batch ktsb1 ON ktsb1.SupplyBatchID = st1.SupplyBatchID
        //         WHERE
        //             sb1.StatusCode = 'active'
        //         AND
        //             ( ( sb1.SupplyDestOrgID IS NOT NULL AND sb1.SupplyDestOrgID > 0 ) OR ( sb1.SupplyDestMillOrgID IS NOT NULL AND sb1.SupplyDestMillOrgID > 0 ) )
        //         AND
        //             sb1.SupplyBatchStatus = 'Delivered'
        //         $sqlHakAksesPartner
        //         UNION
        //         SELECT
        //             m.MemberID AgregatorID1
        //             , m.MemberName Agregator1
        //             , 'direct' MRoleIDAgregator1
        //             , 'Planter' RoleAgregator1
        //             , DATE(st.DateTransaction) DateTransactionAgregator1
        //             , SUM(st.VolumeNetto) FFBNettoAgregator1
        //             , '' Agregator2
        //             , '' RoleAgregator2
        //             , '' DateTransactionAgregator2
        //             , '' FFBNettoAgregator2
        //             , '' Agregator3
        //             , '' RoleAgregator3
        //             , '' DateTransactionAgregator3
        //             , '' FFBNettoAgregator3
        //             , vso.`Name` Agregator4
        //             , 'Mill' RoleAgregator4
        //             , DATE(st.DateTransaction) DateTransactionAgregator4
        //             , SUM(st.VolumeNetto) FFBNettoAgregator4
        //         FROM
        //             ktv_tc_supplychain_transaction st
        //         LEFT JOIN
        //             ktv_members m on m.MemberID = st.SupplyID
        //         LEFT JOIN 
        //             ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = st.SupplyBatchID
        //         LEFT JOIN 
        //             ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = ktsb.SupplyBatchID
        //         LEFT JOIN 
        //             ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
        //         LEFT JOIN
        //         	view_tc_supplychain_org vso on vso.SupplychainID = st.SupplychainID
        //         WHERE
        //             st.StatusCode = 'active'
        //         AND
        //             st.SupplyType = 'Farmer'
        //             /* AND 
        //         m.SupplybaseType = 'direct'*/
        //         $sqlHakAksesPartner2
        //         GROUP BY
        //             m.MemberID
        //         ORDER BY RoleAgregator1, RoleAgregator2
        //         ";

        // $sql = "SELECT
        //             km.MemberName AS Agregator1,
        //             'Farmer' AS RoleAgregator1,
        //             ktst.DateTransaction AS DateTransactionAgregator1,
        //             ktst.VolumeNetto AS FFBNettoAgregator1,
        //             vtso2.Name AS Agregator2,
        //             'Agent/DO' AS RoleAgregator2,
        //             ktsd.DeliveryDate AS DateTransactionAgregator2,
        //             ktsdd.Weight AS FFBNettoAgregator2,
        //             vtso.Name AS Agregator3, 
        //             'Mill' AS RoleAgregator3,
        //             ktsd.DeliveryDate AS DateTransactionAgregator3,
        //             ktstd.TotalCapacity AS FFBNettoAgregator3
        //         FROM 
        //             view_tc_supplychain_org vtso 
        //         LEFT JOIN
        //             ktv_tc_supplychain_delivery ktsd ON ktsd.SupplyDestMillOrgID = vtso.SupplychainID AND vtso.ObjType = 'mill' 
        //         LEFT JOIN 
        //             ktv_tc_supplychain_transaction_detail ktstd ON ktstd.DeliveryDetailID = ktsd.DeliveryID
        //         LEFT JOIN 
        //             ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktsd.DeliveryID 
        //         LEFT JOIN 
        //             view_tc_supplychain_org vtso2 ON vtso2.SupplychainID = ktsd.SupplychainID AND vtso2.ObjType = 'agent'
        //         LEFT JOIN 
        //             ktv_tc_supplychain_batch ktsb ON ktsb.SupplyOrgID = ktsd.SupplychainID 
        //         LEFT JOIN 
        //             ktv_tc_supplychain_transaction ktst ON ktst.SupplyBatchID = ktsb.SupplyBatchID 
        //         LEFT JOIN 
        //             ktv_members km ON km.MemberID = ktst.SupplyID
        //     WHERE  
        //         ktsd.StatusCode = 'active'
        //     AND 
        //         ktst.SupplyType = 'Farmer' 
        //     AND
        //         vtso.PartnerID = '$_SESSION[PartnerID]'
        //     GROUP BY 
        //      	ktsd.DeliveryID";

       $sql = "SELECT
                    m.MemberName Agregator1
                    ,'Farmer' AS RoleAgregator1
                    ,st1.DateTransaction AS DateTransactionAgregator1
                    ,st1.VolumeNetto AS FFBNettoAgregator1
                    ,vso4.Name AS Agregator2
                    ,'Agent/DO' AS RoleAgregator2
					,sb1.SupplyBatchDate AS DateTransactionAgregator2
					,sb1.DestWeight AS FFBNettoAgregator2
					,vso5.Name AS Agregator3 
	                ,'Mill' AS RoleAgregator3
	                ,ktsd1.DeliveryDate AS DateTransactionAgregator3
	                ,ktsdd1.Weight AS DateTransactionAgregator3
                FROM
                    ktv_tc_supplychain_batch sb1
                LEFT JOIN
                    ktv_tc_supplychain_transaction st1 on st1.SupplyBatchID = sb1.SupplyBatchID
                LEFT JOIN
                    ktv_members m on m.MemberID = st1.SupplyID AND st1.SupplyType = 'Farmer'
                LEFT JOIN 
                    ktv_tc_supplychain_delivery_detail ktsdd1 ON ktsdd1.DeliveryID = sb1.SupplyBatchID
                LEFT JOIN 
                    ktv_tc_supplychain_delivery ktsd1 ON ktsd1.DeliveryID = ktsdd1.DeliveryID
                LEFT JOIN
                    view_tc_supplychain_org vso5 on vso5.SupplychainID = ktsd1.SupplyDestMillOrgID AND vso5.ObjType = 'mill'
                LEFT JOIN
                    view_tc_supplychain_org vso4 on vso4.SupplychainID = ktsd1.SupplychainID AND vso4.ObjType = 'agent'
                WHERE
                    ktsd1.StatusCode = 'active'
                    $sqlHakAksesPartner
                GROUP BY ktsdd1.DeliveryID 
                UNION
                SELECT
                    m.MemberName Agregator1
                    ,'Farmer' AS RoleAgregator1
                    ,st.DateTransaction AS DateTransactionAgregator1
                    ,st.VolumeNetto AS FFBNettoAgregator1
                    ,vso.name AS Agregator2
                    ,'Agent/DO' AS RoleAgregator2
                    ,ktsb.SupplyBatchDate AS DateTransactionAgregator2
                    ,ktsb.DestWeight AS FFBNettoAgregator2
                    ,vso2.Name AS Agregator3 
	                ,'Mill' AS RoleAgregator3
	                ,ktsd.DeliveryDate AS DateTransactionAgregator3
	                ,ktsdd.Weight AS FFBNettoAgregator3
                FROM
                    ktv_tc_supplychain_transaction st
                LEFT JOIN
                    ktv_members m on m.MemberID = st.SupplyID AND st.SupplyType = 'farmer'
                LEFT JOIN 
                    ktv_tc_supplychain_batch ktsb ON ktsb.SupplyBatchID = st.SupplyBatchID
                LEFT JOIN 
                    ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.SupplyBatchID = ktsb.SupplyBatchID
                LEFT JOIN 
                    ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID = ktsd.SupplyDestMillOrgID AND vso2.ObjType = 'mill'
                LEFT JOIN
                	view_tc_supplychain_org vso on vso.SupplychainID = ktsd.SupplychainID AND vso.ObjType = 'agent'
                WHERE
                    st.StatusCode = 'active'
                $sqlHakAksesPartner2
                GROUP BY
                    ktsd.DeliveryID ";
        
            $query = $this->db->query($sql);
            // echo '<pre>'.$this->db->last_query();die;

            if($query->num_rows()>0){
                $return = array();
                $return2 = array();
                foreach($query->result() as $key => $row){

                    $return[$key]["Agregator 1"] = $row->Agregator1;
                    $return[$key]["Role Agr 1"] = lang("$row->RoleAgregator1");
                    $return[$key]["Date Transaction 1"] = $row->DateTransactionAgregator1;
                    $return[$key]["FFB Agr 1"] = $row->FFBNettoAgregator1;
                    if($row->MRoleIDAgregator1 == 11 || $row->MRoleIDAgregator1 == 12 || $row->MRoleIDAgregator1 == 7 || $row->MRoleIDAgregator1 == 14 || $row->MRoleIDAgregator1 == 'direct'){
                        $return[$key]["Agregator 2"] = $row->Agregator4;
                        $return[$key]["Role Agr 2"] = lang("$row->RoleAgregator4");
                        $return[$key]["Date Transaction 2"] = $row->DateTransactionAgregator4;
                        $return[$key]["FFB Agr 2"] = $row->FFBNettoAgregator4;
                        $return[$key]["Agregator 3"] = '-';
                        $return[$key]["Role Agr 3"] = '-';
                        $return[$key]["Date Transaction 3"] = '-';
                        $return[$key]["FFB Agr 3"] = '-';
                        $return[$key]["Agregator 4"] = '-';
                        $return[$key]["Role Agr 4"] = '-';
                        $return[$key]["Date Transaction 4"] = '-';
                        $return[$key]["FFB Agr 4"] = '-';
                        // $return = (object) array_merge((array) $return, (array) $retur2);
                    }else{
                        $return[$key]["Agregator 2"] = $row->Agregator2;
                        $return[$key]["Role Agr 2"] = lang("$row->RoleAgregator2");
                        $return[$key]["Date Transaction 2"] = $row->DateTransactionAgregator2;
                        $return[$key]["FFB Agr 2"] = $row->FFBNettoAgregator2;
                        if($row->Agregator3 == '' || $row->Agregator3 == ' - '){
                            $return[$key]["Agregator 3"] = $row->Agregator4;
                            $return[$key]["Role Agr 3"] = lang("$row->RoleAgregator4");
                            $return[$key]["Date Transaction 3"] = $row->DateTransactionAgregator4;
                            $return[$key]["FFB Agr 3"] = $row->FFBNettoAgregator4;
                            $return[$key]["Agregator 4"] = '-';
                            $return[$key]["Role Agr 4"] = '-';
                            $return[$key]["Date Transaction 4"] = '-';
                            $return[$key]["FFB Agr 4"] = '-';
                        }else{
                            $return[$key]["Agregator 3"] = $row->Agregator3;
                            $return[$key]["Role Agr 3"] = lang("$row->RoleAgregator3");
                            $return[$key]["Date Transaction 3"] = $row->DateTransactionAgregator3;
                            $return[$key]["FFB Agr 3"] = $row->FFBNettoAgregator3;
                            $return[$key]["Agregator 4"] = $row->Agregator4;
                            $return[$key]["Role Agr 4"] = lang("$row->RoleAgregator4");
                            $return[$key]["Date Transaction 4"] = $row->DateTransactionAgregator4;
                            $return[$key]["FFB Agr 4"] = $row->FFBNettoAgregator4;
                        }
                    }
                }
                
                // $result = array_map("unserialize", array_unique(array_map("serialize", $return)));
                // echo "<pre>";
                // // print_r($return);
                // print_r($query->result());
                // die;
                return $return;
            }else{
                return false;
            }
    }

    function readTreeRefineryExport(){
        if($_SESSION['is_admin'] == "1"){
            $sqlHakAksesPartner = "";
        } elseif ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program"){
            //cek ktv_access_staff
            $sqlHakAksesPartner = " AND vso.PartnerID = '$_SESSION[PartnerID]' ";
            if($_SESSION['PartnerID'] == 1){
                $sqlHakAksesPartner = "";
            }
        }elseif($_SESSION['role'] == "Refinery"){
            $sqlHakAksesPartner = " Vso.SupplychainID = '$_SESSION[SupplychainID]'";
        } else {
            //cek ktv_access_staff
            $sqlHakAksesPartner = "";
            $sqlHakAksesPartner2 = " ";
        }

        $sql = "SELECT
                    st.SupplyID
                    , rd.ReceptionDetailID
                    , IF(st.SupplyType = 'Farmer', m.MemberName, vso.`Name`) 'Agregator1'
                    , IF(st.SupplyType = 'Farmer', rm.MRoleID, rm2.MRoleID) 'MRoleIDAgregator1'
                    , IF(st.SupplyType = 'Farmer', rm.MRoleName, rm2.MRoleName) 'RoleAgregator1'
                    , st.DateTransaction DateTransactionAgregator1
                    , st.VolumeNetto FFBNettoAgregator1
                    , sb2.`Name` 'Agregator2'
                    , sb2.ObjType 'RoleAgregator2'
                    , DATE(sb2.SupplyBatchDate) 'DateTransactionAgregator2'
                    , sb2.VolumeNetto 'FFBNettoAgregator2'	
                    , IF(sb3.DoName IS NULL, ' - ',sb3.DoName) 'Agregator3'
                    , IF(sb3.DoName = ' - ' OR sb3.Doname IS NULL, ' - ','Dealer') 'RoleAgregator3'
                    , IF(sb3.DoName = ' - ' OR sb3.Doname IS NULL, ' - ',DATE(sb3.ReceivedDate)) 'DateTransactionAgregator3'
                    , IF(sb3.DoName = ' - ' OR sb3.Doname IS NULL, ' - ',sb3.VolumeNetto) 'FFBNettoAgregator3'	
                    , sb3.MillName 'Agregator4'
                    , IF(sb3.MillName = ' - ', ' - ','Mill') 'RoleAgregator4'
                    , IF(sb3.MillName = ' - ', ' - ',DATE(sb3.ReceivedDate)) 'DateTransactionAgregator4'
                    , IF(sb3.MillName = ' - ', ' - ',sb3.VolumeNetto) 'FFBNettoAgregator4'	
                    , rec.`Name` 'Agregator5'
                    , IF(rec.RoleAgregator5 = ' - ', ' - ',rec.RoleAgregator5) 'RoleAgregator5'
                    , IF(rec.ReceptionDate = ' - ', ' - ',DATE(rec.ReceptionDate)) 'DateTransactionAgregator5'
                    , IF(rec.FFBAgregator5 = ' - ', ' - ',rec.FFBAgregator5) 'FFBNettoAgregator5'
                FROM
                    ktv_tc_reception tr
                LEFT JOIN
                    ktv_tc_reception_detail rd on rd.ReceptionID = tr.ReceptionID
                LEFT JOIN
                    ktv_tc_despatch_detail dd on dd.DespatchDetailID = rd.DespatchDetailID
                LEFT JOIN
                    ktc_tc_processing_product pp on pp.ProcessingProductID = dd.ProcessingProductID
                LEFT JOIN
                    ktv_tc_processing p on p.ProcessingID = pp.ProcessingID
                LEFT JOIN
                    ktv_tc_processing_detail pd on pd.ProcessingID = p.ProcessingID AND pd.ObjTypeID = 1
                LEFT JOIN
                    ktv_tc_supplychain_transaction st on st.SupplyTransID = pd.ObjID
                LEFT JOIN
                    ktv_tc_supplychain_batch sb on sb.SupplyBatchID = st.SupplyBatchID
                LEFT JOIN
                        ktv_members m on m.MemberID = st.SupplyID AND st.SupplyType = 'Farmer'
                LEFT JOIN
                        ktv_member_role mr on mr.MemberID = m.MemberID
                LEFT JOIN
                        ktv_ref_member_role rm on rm.MRoleID = mr.MRoleID
                LEFT JOIN
                        view_tc_supplychain_org vso on vso.SupplychainID = st.SupplyID AND st.SupplyType <> 'Farmer'
                LEFT JOIN
                        ktv_member_role mr2 on mr2.MemberID = vso.ObjID
                LEFT JOIN
                        ktv_ref_member_role rm2 on rm2.MRoleID = mr2.MRoleID
                LEFT JOIN
                (
                        SELECT
                                sb3.SupplyBatchID
                                , SUM(sb3.DestWeight) VolumeNetto
                                , vso3.`Name`
                                , DATE(sb3.SupplyBatchDate) SupplyBatchDate                            
                                , rm.MRoleName ObjType
                        FROM
                                ktv_tc_supplychain_batch sb3
                        LEFT JOIN
                                view_tc_supplychain_org vso3 on vso3.SupplychainID = sb3.SupplyOrgID
                        LEFT JOIN
                                        ktv_member_role mr on mr.MemberID = vso3.ObjID
                        LEFT JOIN
                                        ktv_ref_member_role rm on rm.MRoleID = mr.MRoleID
                        WHERE
                                sb3.SupplyBatchStatus = 'Delivered'
                        AND
                                sb3.StatusCode = 'active'
                        AND
                                ( ( sb3.SupplyDestOrgID IS NOT NULL AND sb3.SupplyDestOrgID > 0 ) OR ( sb3.SupplyDestMillOrgID IS NOT NULL AND sb3.SupplyDestMillOrgID > 0 ) )
                        GROUP BY
                                sb3.SupplyBatchID
                ) sb2 on sb2.SupplyBatchID = sb.SupplyBatchID		
                LEFT JOIN
                (
                        SELECT
                                sb.SupplyBatchID
                                , SUM(sb.ReceiveWeight) VolumeNetto
                                , sb.SupplyDestDoOrgID
                                , sb.ReceivedDate
                                , CASE
                                        WHEN sb.SupplyDestType = 'do' THEN vso2.`Name`
                                        ELSE ' - '
                                END DOName
                    , IFNULL(sb.SupplyDestMillOrgID,sb.SupplyDestOrgID) SupplychainID
                                , CASE
                                        WHEN sb.SupplyDestType = 'do' THEN vso3.`Name`
                                        WHEN sb.SupplyDestType IS NULL THEN vso1.`Name`
                                        WHEN sb.SupplyDestType = 'mill' THEN vso1.`Name`
                                        ELSE ' - '
                                END MillName
                        FROM
                                ktv_tc_supplychain_batch sb
                        LEFT JOIN
                                view_tc_supplychain_org vso1 on vso1.SupplychainID = sb.SupplyDestOrgID
                        LEFT JOIN
                                view_tc_supplychain_org vso2 on vso2.SupplychainID = sb.SupplyDestDoOrgID
                        LEFT JOIN
                                view_tc_supplychain_org vso3 on vso3.SupplychainID = sb.SupplyDestMillOrgID
                        WHERE
                                sb.SupplyBatchStatus = 'Delivered'
                        AND
                                sb.StatusCode = 'active'
                        AND
                                ( ( sb.SupplyDestOrgID IS NOT NULL AND sb.SupplyDestOrgID > 0 ) OR ( sb.SupplyDestMillOrgID IS NOT NULL AND sb.SupplyDestMillOrgID > 0 ) )
                        GROUP BY
                                sb.SupplyBatchID
                ) sb3 on sb3.SupplyBatchID = sb.SupplyBatchID
                LEFT JOIN
                (
                    SELECT
                            vso.`Name`
                            , r.ReceptionDate
                            , 'Refinery' RoleAgregator5
                            , SUM(rd.ReceptionVolume) FFBAgregator5
                            , rd.DespatchDetailID
                        FROM
                            `ktv_tc_reception` r
                        LEFT JOIN
                            ktv_tc_reception_detail rd on rd.ReceptionID = r.ReceptionID
                        LEFT JOIN
                            view_tc_supplychain_org vso on vso.SupplychainID = r.SupplychainID
                        GROUP BY
                            rd.DespatchDetailID
                ) rec on rec.DespatchDetailID = dd.DespatchDetailID
                        WHERE
                    tr.StatusCode = 'active'
                    $sqlHakAksesPartner
                GROUP BY
                    st.SupplyTransID";
        

            $query = $this->db->query($sql);
            if($query->num_rows()>0){
                $return = array();
                $return2 = array();
                foreach($query->result() as $key => $row){
                    $return[$key]["Agregator 1"] = $row->Agregator1;
                    $return[$key]["Role Agr 1"] = lang("$row->RoleAgregator1");
                    $return[$key]["Date Transaction 1"] = $row->DateTransactionAgregator1;
                    $return[$key]["FFB Agr 1"] = $row->FFBNettoAgregator1;
                    if($row->MRoleIDAgregator1 == 11 || $row->MRoleIDAgregator1 == 12 || $row->MRoleIDAgregator1 == 7 || $row->MRoleIDAgregator1 == 14 || $row->MRoleIDAgregator1 == 'direct'){
                        $return[$key]["Agregator 2"] = $row->Agregator4;
                        $return[$key]["Role Agr 2"] = lang("$row->RoleAgregator4");
                        $return[$key]["Date Transaction 2"] = $row->DateTransactionAgregator4;
                        $return[$key]["FFB Agr 2"] = $row->FFBNettoAgregator4;
                        $return[$key]["Agregator 3"] = '-';
                        $return[$key]["Role Agr 3"] = '-';
                        $return[$key]["Date Transaction 3"] = '-';
                        $return[$key]["FFB Agr 3"] = '-';
                        $return[$key]["Agregator 4"] = '-';
                        $return[$key]["Role Agr 4"] = '-';
                        $return[$key]["Date Transaction 4"] = '-';
                        $return[$key]["FFB Agr 4"] = '-';
                        // $return = (object) array_merge((array) $return, (array) $retur2);
                    }else{
                        $return[$key]["Agregator 2"] = $row->Agregator2;
                        $return[$key]["Role Agr 2"] = lang("$row->RoleAgregator2");
                        $return[$key]["Date Transaction 2"] = $row->DateTransactionAgregator2;
                        $return[$key]["FFB Agr 2"] = $row->FFBNettoAgregator2;
                        if($row->Agregator3 == '' || $row->Agregator3 == ' - '){
                            $return[$key]["Agregator 3"] = $row->Agregator4;
                            $return[$key]["Role Agr 3"] = lang("$row->RoleAgregator4");
                            $return[$key]["Date Transaction 3"] = $row->DateTransactionAgregator4;
                            $return[$key]["FFB Agr 3"] = $row->FFBNettoAgregator4;
                            $return[$key]["Agregator 4"] = $row->Agregator5;
                            $return[$key]["Role Agr 4"] = lang("$row->RoleAgregator5");
                            $return[$key]["Date Transaction 4"] = $row->DateTransactionAgregator5;
                            $return[$key]["FFB Agr 4"] = $row->FFBNettoAgregator5;
                        }else{
                            $return[$key]["Agregator 3"] = $row->Agregator3;
                            $return[$key]["Role Agr 3"] = lang("$row->RoleAgregator3");
                            $return[$key]["Date Transaction 3"] = $row->DateTransactionAgregator3;
                            $return[$key]["FFB Agr 3"] = $row->FFBNettoAgregator3;
                            $return[$key]["Agregator 4"] = $row->Agregator4;
                            $return[$key]["Role Agr 4"] = lang("$row->RoleAgregator4");
                            $return[$key]["Date Transaction 4"] = $row->DateTransactionAgregator4;
                            $return[$key]["FFB Agr 4"] = $row->FFBNettoAgregator4;
                            $return[$key]["Agregator 5"] = $row->Agregator5;
                            $return[$key]["Role Agr 5"] = lang("$row->RoleAgregator5");
                            $return[$key]["Date Transaction 5"] = $row->DateTransactionAgregator5;
                            $return[$key]["FFB Agr 5"] = $row->FFBNettoAgregator5;
                        }
                    }
                }
                
                // $result = array_map("unserialize", array_unique(array_map("serialize", $return)));
                // echo "<pre>";
                // // print_r($return);
                // print_r($query->result());
                // die;
                return $return;
            }else{
                return false;
            }
    }

    function arrayCustomMerge(array $array1, array $arrays): array
    {
        foreach ($arrays as $additionalArray) {
            foreach ($additionalArray as $key => $item) {
                if (is_string($key)) {
                    // if associative array.
                    // item on the right will always overwrite on the left.
                    $array1[$key] = $item;
                } elseif (is_int($key) && !array_key_exists($key, $array1)) {
                    // if key is number. this should be indexed array.
                    // and if array 1 is not already has this key.
                    // add this array with the key preserved to array 1.
                    $array1[$key] = $item;
                } else {
                    // if anything else...
                    // get all keys from array 1 (numbers only).
                    $array1Keys = array_filter(array_keys($array1), 'is_int');
                    // next key index = get max array key number + 1.
                    $nextKeyIndex = (intval(max($array1Keys)) + 1);
                    unset($array1Keys);
                    // set array with the next key index.
                    $array1[$nextKeyIndex] = $item;
                    unset($nextKeyIndex);
                }
            }// endforeach; $additionalArray
            unset($item, $key);
        }// endforeach;
        unset($additionalArray);

        return $array1;
    }// arrayCustomMerge
}
?>

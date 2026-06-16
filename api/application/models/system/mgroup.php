<?php
class Mgroup extends CI_Model {

   function readGroups($key,$unitId,$start,$limit){
      if($unitId == "") $unitId = 'all';

        $sql = "
            SELECT %s
            FROM sys_group a
			 LEFT JOIN sys_unit b ON b.UnitId=a.GroupUnitId
			 LEFT JOIN sys_user_group c ON c.UserGroupGroupId=a.GroupId
			 left join sys_user d on d.`UserId`=c.`UserGroupUserId` and d.`UserActive`='Yes'
			WHERE
			a.StatusCode != 'nullified' AND
         a.GroupName LIKE ? AND
         ( (a.GroupUnitId = ?) OR (?='all'))
			GROUP BY a.GroupId
            ORDER BY a.GroupName %s";
        $query = $this->db->query(sprintf($sql,"GroupId,GroupName, GroupDescription,GroupUnitId,UnitName,count(d.UserId) AS ActiveUser, IF(a.GroupActive = 'Yes', 'Active', 'Not Active') AS GroupStatus, a.IsLocked",'LIMIT ?,?'), array( '%'.$key.'%', $unitId,$unitId, (int) $start,(int) $limit) );
        $result['data'] = $query->result_array();
		foreach($result['data'] as $k=>$v){
			if($result['data'][$k]['ActiveUser']==''){
				$result['data'][$k]['ActiveUser'] = 0;
			}
		}

      $sqlTotal="SELECT
         COUNT(*) AS total
      FROM
         sys_group a
      WHERE
         a.StatusCode != 'nullified' AND
         a.GroupName LIKE ? AND
         ( (a.GroupUnitId = ?) OR (?='all'))
      ";
      $query = $this->db->query($sqlTotal,array('%'.$key.'%',$unitId,$unitId));
      $result['total'] = $query->row()->total;
      return $result;
   }

   function readGroupUser($groupId,$start,$limit){
      $sql="SELECT
               %s
            FROM
               sys_user a
               LEFT JOIN sys_user_group b ON a.UserId = b.UserGroupUserId
               LEFT JOIN sys_group c ON b.UserGroupGroupId = c.`GroupId`
            WHERE
               b.UserGroupGroupId = ?
               AND a.UserActive = 'Yes'
            ORDER BY a.UserRealName ASC %s";
      $query = $this->db->query(
                  sprintf($sql,"a.UserRealName,
                                 a.UserId,
                                 a.UserName,
                                 a.UserActive,
                                 c.`GroupName`,
                                 CASE
                                    WHEN (SELECT COUNT(*) FROM ktv_program_staff WHERE UserId = a.UserId) > 0 THEN 'Program'
                                    WHEN (SELECT COUNT(*) FROM ktv_private_staff WHERE UserId = a.UserId) > 0 THEN 'Private'
                                    WHEN (SELECT COUNT(*) FROM sce_farmer_staff WHERE UserId = a.UserId) > 0 THEN 'Professional Farmer'
                                    WHEN (SELECT COUNT(*) FROM ktv_trader_staff WHERE UserId = a.UserId) > 0 THEN 'Trader'
                                    WHEN (SELECT COUNT(*) FROM ktv_warehouse_staff WHERE UserId = a.UserId) > 0 THEN 'Warehouse'
                                    WHEN (SELECT COUNT(*) FROM ktv_cooperative_staff WHERE UserId = a.UserId) > 0 THEN 'Cooperatives'
                                    WHEN (SELECT COUNT(*) FROM ktv_bank_branch_staff WHERE UserId = a.UserId) > 0 THEN 'Bank'
                                    ELSE '-'
                                 END AS UserType
                                 ",
                                 'LIMIT ?,?'),
                  array( $groupId, (int)$start, (int)$limit )
               );
      $result['data'] = $query->result_array();
      $query = $this->db->query(sprintf($sql,'count(*) as total',''),array($groupId));
      $result['total'] = $query->row()->total;
      return $result;
   }

    function readGroup($id){
        $sql = "
            select 
                   a.GroupId
                  ,a.GroupName
                  ,a.GroupDescription
                  ,a.GroupUnitId
                  ,a.GroupMenuId
                  ,a.GroupFilterBy
                  ,IF(a.GroupPartnerID = 0, '', a.GroupPartnerID) as GroupPartnerID
                  ,GROUP_CONCAT(DISTINCT bu.`BusinessUnitID` ORDER BY bu.`BusinessUnitID` SEPARATOR ',') AS GroupMenuBusinessUnit
            from sys_group as a
            LEFT JOIN sys_group_bu bu ON a.`GroupId` = bu.`GroupID`
            WHERE a.GroupId=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createGroup($name,$description,$unitid,$aksi,$listReport,$menuid,$filterby,$userid,$partnerid,$GroupMenuBusinessUnit){
        if (empty($partnerid)) {
            $partnerid = 0;
        }

        $sql = "
            INSERT INTO sys_group(GroupName,GroupDescription,GroupUnitId,GroupAddUserId,GroupAddTime,GroupMenuId,GroupFilterBy,GroupPartnerID)
            VALUES (?,?,?,?,now(),?,?,?)";
         $sql_aksi = "
            INSERT IGNORE INTO sys_group_menu_act(GroupMenuMenuAksiId,GroupMenuGroupId,GroupMenuSegmen)
            SELECT MenuAksiId,?,concat(MenuModule,'/',AksiFungsi,IF(MenuParam!='',concat('/',MenuParam),''))
            FROM sys_menu_act
            LEFT JOIN sys_menu ON MenuAksiMenuId=MenuId
            LEFT JOIN sys_act ON MenuAksiAksiId=AksiId
            where MenuAksiId=?";
        $this->db->trans_start();
        $this->db->query($sql, array($name,$description,$unitid,$userid,$menuid,$filterby,$partnerid));
        $id = $this->db->insert_id();
        $arrAksi = explode(',', $aksi);
        for ($i=0;$i<sizeof($arrAksi);$i++) {
            $this->db->query($sql_aksi, array($id,$arrAksi[$i]));
        }

        $sql_report_delete = "DELETE FROM sys_group_report WHERE GroupId = ?";
        $sql_report_add = "INSERT INTO sys_group_report VALUES(?,?)";

        $this->db->query($sql_report_delete, array($id));
        $arrReport = explode(',',$listReport);
        for($j=0;$j<sizeof($arrReport);$j++){
            $this->db->query($sql_report_add, array($id,$arrReport[$j]));
        }

        if (!empty($GroupMenuBusinessUnit)) {
            $ArrBusiness = explode(",", $GroupMenuBusinessUnit);
            if ($ArrBusiness[0] != "") {
                for ($i = 0; $i < count($ArrBusiness); $i++) {
                    $sql = "INSERT INTO sys_group_bu SET
                        GroupID = ?,
                        BusinessUnitID = ?,
                        DateCreated = NOW(),
                        CreatedBy = ?";
                    $p = array(
                        $id,
                        $ArrBusiness[$i],
                        $_SESSION['userid'],
                    );
                    $query = $this->db->query($sql, $p);
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateGroup($id,$name,$description,$unitid,$aksi,$listReport,$menuid,$filterby,$userid,$partnerid,$GroupMenuBusinessUnit){
        if (empty($partnerid)) {
            $partnerid = 0;
        }

        $sql = "
            UPDATE sys_group
            SET GroupName=?,GroupDescription=?,GroupUnitId=?,GroupMenuId=?,GroupFilterBy=?,GroupUpdateUserId=?,GroupUpdateTime=now(),GroupPartnerID=?
            WHERE GroupId=?";
        $sql_aksi_delete = "
            DELETE FROM sys_group_menu_act WHERE GroupMenuGroupId=?";
        $sql_aksi_add = "
            INSERT IGNORE INTO sys_group_menu_act(GroupMenuMenuAksiId,GroupMenuGroupId,GroupMenuSegmen)
            SELECT MenuAksiId,?,concat(MenuModule,'/',AksiFungsi,IF(MenuParam!='',concat('/',MenuParam),''))
            FROM sys_menu_act
            LEFT JOIN sys_menu ON MenuAksiMenuId=MenuId
            LEFT JOIN sys_act ON MenuAksiAksiId=AksiId
            where MenuAksiId=?";
        $sql_report_delete = "DELETE FROM sys_group_report WHERE GroupId = ?";
        $sql_report_add = "INSERT INTO sys_group_report VALUES(?,?)";
        $this->db->trans_start();
        $this->db->query($sql, array($name,$description,$unitid,$menuid,$filterby,$userid,$partnerid,$id));
        $this->db->query($sql_aksi_delete, array($id));
        $arrAksi = explode(',', $aksi);
        for ($i=0;$i<sizeof($arrAksi);$i++) {
            $this->db->query($sql_aksi_add, array($id,$arrAksi[$i]));
        }
        $this->db->query($sql_report_delete, array($id));
        $arrReport = explode(',',$listReport);
        for($j=0;$j<sizeof($arrReport);$j++){
            $this->db->query($sql_report_add, array($id,$arrReport[$j]));
        }

        $checkExistBussinesUnit = $this->db->where('GroupID', (int) $id)
                                           ->get('sys_group_bu')
                                           ->result();

        if (!empty($checkExistBussinesUnit)) {
            $this->db->delete('sys_group_bu', ['GroupID' => $id]);
        }

        if (!empty($GroupMenuBusinessUnit)) {
            $ArrBusiness = explode(",", $GroupMenuBusinessUnit);
            if ($ArrBusiness[0] != "") {
                for ($i = 0; $i < count($ArrBusiness); $i++) {
                    $sql = "INSERT INTO sys_group_bu SET
                        GroupID = ?,
                        BusinessUnitID = ?,
                        DateCreated = NOW(),
                        CreatedBy = ?";
                    $p = array(
                        $id,
                        $ArrBusiness[$i],
                        $_SESSION['userid'],
                    );
                    $query = $this->db->query($sql, $p);
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteGroup($id){
        $sql = "
            DELETE FROM sys_group WHERE GroupId=?";
        $sql_aksi = "
            DELETE FROM sys_group_menu_act WHERE GroupMenuGroupId=?";
        $this->db->trans_start();
        $this->db->query($sql_aksi, array($id));
        $this->db->query($sql, array($id));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

	function deleteGroupStatus($id){
        $sql = "UPDATE sys_group SET StatusCode='nullified',GroupUpdateUserId='".$_SESSION['userid']."', GroupUpdateTime = NOW() WHERE GroupId=?";
        //$sql_aksi = "DELETE FROM sys_group_menu_act WHERE GroupMenuGroupId=?";
        //$this->db->trans_start();
        //$this->db->query($sql_aksi, array($id));
        $this->db->query($sql, array($id));
        //$this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function readGroupaksi($id){
        /*
        $sql = "
            SELECT
                -- MenuId as value,
                -- MenuParentId as parent,
                MenuAksiId as value,
            	if(AksiId=1,if(MenuParentId=0,MenuName,concat(sys_menu.MenuName,' / ',MenuName)),concat(sys_menu.MenuName,' / ',AksiName)) as text
            	-- ,if(GroupMenuMenuAksiId is null,'','true') as checked
            FROM
                sys_menu_act
                left join sys_menu on MenuAksiMenuId=MenuId
                left join sys_act on MenuAksiAksiId=AksiId
                left join sys_group_menu_act on GroupMenuMenuAksiId=MenuAksiId and GroupMenuGroupId=?
            WHERE
                MenuShow='Yes' AND
                GroupMenuMenuAksiId IS NOT NULL
            ORDER BY MenuId";
        */
        // order by MenuParentId,MenuOrder,AksiId
        // 41
        $sql = "SELECT
                    e.MenuAksiId as value,
                    a.MenuId,
                    b.AksiId,
                    COUNT(z.MenuId) AS child,
                    -- if(b.AksiId=1,if(a.MenuParentId=0,a.MenuName,concat(c.MenuName,' / ',a.MenuName)),concat(c.MenuName,' / ',a.MenuName,' / ',AksiName)) as text
                    IF(a.MenuParentId = 0,
                        IF(b.AksiId = 1, a.MenuName, CONCAT(a.MenuName,' / ',AksiName)),
                        IF(c.MenuParentId = 0,
                            IF(b.AksiId = 1, CONCAT(c.MenuName,' / ',a.MenuName), CONCAT(c.MenuName,' / ',a.MenuName,' / ',AksiName)),
                            IF(b.AksiId = 1, CONCAT(d.MenuName,' / ',c.MenuName,' / ',a.MenuName), CONCAT(d.MenuName,' / ',c.MenuName,' / ',a.MenuName,' / ',AksiName))
                        )
                    ) AS `text`
                FROM sys_menu_act e
                    left join sys_menu a on MenuAksiMenuId=MenuId
                    left join sys_act b on MenuAksiAksiId=AksiId
                    left join sys_group_menu_act g on g.GroupMenuMenuAksiId=e.MenuAksiId and g.GroupMenuGroupId=?
                    left join sys_menu c on c.MenuId = a.MenuParentId
                    LEFT JOIN sys_menu d ON d.MenuId = c.MenuParentId
                    LEFT JOIN sys_menu z ON a.MenuId = z.MenuParentId
                WHERE
                    a.MenuShow='Yes' AND
                    g.GroupMenuMenuAksiId IS NOT NULL
                GROUP BY e.MenuAksiId
                ORDER BY text";

        $query = $this->db->query($sql, array($id));

        $result = $query->result_array();
        /*
        for ($i=0;$i<sizeof($result);$i++) {
            if ($result[$i]['parent']=='0') {
                 $res[] = $result[$i];
                 for ($j=0;$j<sizeof($result);$j++) {
                     if ($result[$j]['parent']==$result[$i]['MenuId']) $res[] = $result[$j];
                 }
            } else break;
        }
         *
         */
        return $result;
    }

    function readGroupaksiList(){
        $sql = "
                SELECT
                    DISTINCT(e.MenuAksiId) AS `value`,
                    a.MenuId,
                    b.AksiId,
                    COUNT(z.MenuId) AS child,
                    -- if(b.AksiId=1,if(a.MenuParentId=0,a.MenuName,concat(c.MenuName,' / ',a.MenuName)),concat(c.MenuName,' / ',a.MenuName,' / ',AksiName)) as text
                    IF(a.MenuParentId = 0,
                        IF(b.AksiId = 1, a.MenuName, CONCAT(a.MenuName,' / ',AksiName)),
                        IF(c.MenuParentId = 0,
                            IF(b.AksiId = 1, CONCAT(c.MenuName,' / ',a.MenuName), CONCAT(c.MenuName,' / ',a.MenuName,' / ',AksiName)),
                            IF(b.AksiId = 1, CONCAT(d.MenuName,' / ',c.MenuName,' / ',a.MenuName), CONCAT(d.MenuName,' / ',c.MenuName,' / ',a.MenuName,' / ',AksiName))
                        )
                    ) AS `text`
                FROM sys_menu_act e
                    LEFT JOIN sys_menu a ON MenuAksiMenuId=MenuId
                    LEFT JOIN sys_act b ON MenuAksiAksiId=AksiId
                    LEFT JOIN sys_menu c ON c.MenuId = a.MenuParentId
                    LEFT JOIN sys_menu d ON d.MenuId = c.MenuParentId
                    LEFT JOIN sys_menu z ON a.MenuId = z.MenuParentId
                WHERE
                    a.MenuShow='Yes'
                GROUP BY e.MenuAksiId
                ORDER BY text

                ";
        $query = $this->db->query($sql);
        //echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $result = $query->result_array();
        return $result;
    }

    function readGroupReport($id){
        $sql = "SELECT
                    a.MenuName as value,
                    CASE
                        WHEN b.Kategori=1
                            THEN CONCAT('Summary Report / ',b.MenuName)
                        WHEN b.Kategori=2
                            THEN CONCAT('Farmer Report / ',b.MenuName)
                        WHEN b.Kategori=3
                            THEN CONCAT('Garden Report / ',b.MenuName)
                        ELSE ''
                    END as text
                FROM sys_group_report a
                    LEFT JOIN sys_menu_report b ON a.MenuName = b.MenuName
                WHERE
                    a.GroupId = ?
                ORDER BY b.Kategori";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        if(count($result) < 1){
            $result = array(
                'value' => '',
                'text' => ''
            );
        }
        return $result;
    }

    function readGroupReportList(){
        $sql = "SELECT
                    MenuName as value,
                    CASE
                        WHEN Kategori=1
                            THEN CONCAT('Summary Report / ',MenuName)
                        WHEN Kategori=2
                            THEN CONCAT('Detail Report / ',MenuName)
                        WHEN Kategori=3
                            THEN CONCAT('Garden Report / ',MenuName)
                        ELSE ''
                    END as text
                FROM sys_menu_report
                ORDER BY Kategori";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    //edited: ardiantoro@koltiva.com
    public function readGroupprogramList($id)
    {
        $sql = "SELECT DISTINCT 
            a.programid AS `value`,
            a.name AS `text`,
            a.shortname,
            a.use_json,
            a.program_table,
            a.program_table parent_table,
            a.reference_field reference_field,
            a.uid_field uid_field, 
            (SELECT IF(gp.GroupMwProgramId > 0,1,0) FROM sys_group_mw_program gp WHERE gp.GroupId = ? AND gp.ProgramId = a.programid ) AS program_status, 
            (SELECT IFNULL(GROUP_CONCAT(OrgUnitId),'') FROM sys_program_orgunit WHERE ProgramId = a.programid AND GroupId = ?) AS orgs
        FROM mw_program a 
        LEFT JOIN sys_group_mw_program gp ON gp.ProgramId = a.programid
        LEFT JOIN mw_program pt ON pt.uid = a.parentuid";

        $query = $this->db->query($sql,array($id,$id)); 
        $result = $query->result_array();
        foreach($result as $key => $val) {
            $result[$key]['program_status'] = (int)$result[$key]['program_status'];
            $result[$key]['use_json'] = (int)$result[$key]['use_json'];
        }
        return $result;
    }

    public function readGroupprogramSelected($id)
    {
        $sql = "SELECT 
        ProgramId
        FROM sys_group_mw_program WHERE GroupId = ?";
        $query = $this->db->query($sql,[$id]);
        $result = $query->result_array();
        
        return $result;
    }

    public function updateGroupprogramSelected($id,$programs)
    {
        try {
            //delete records
            $sql = "DELETE 
            FROM sys_group_mw_program WHERE GroupId = ?";
            $query = $this->db->query($sql,[$id]);
            
            //reinsert
            foreach($programs as $key => $val) {
                if($val['program_status'] == 1) {
                    $orgs = array_filter(explode(',',$val['orgs']));
                    $orgs = $orgs[0] == '' ? array() : $orgs;
                    $result = [];

                    $this->db->insert('sys_group_mw_program',['ProgramId' => $val['value'], 'GroupId' => $id]);
                    
                    //delete dulu
                    $this->db->where('ProgramId',$val['value']);
                    $this->db->where('GroupId',$id);
                    $this->db->delete('sys_program_orgunit');

                    if(count($orgs) > 0) {
                        foreach($orgs as $org) {
                            if((int)$org > 0) {
                                $this->db->insert('sys_program_orgunit',[
                                    'ProgramId' => $val['value'],
                                    'OrgUnitId' => $org,
                                    'levelOrgUnit' => 'District',
                                    'GroupId' => $id
                                ]);
                            }
                        }
                    }
                    
                    //update program config
                    $usejson = $val['use_json'] ?? 0;
                    $programtable = $val['parent_table'] ?? null;
                    $uidfield = $val['uid_field'] ?? null;
                    $referencefield = $val['reference_field'] ?? null;

                    $update = [
                        'use_json' => $usejson,
                        'program_table' => $programtable,
                        'uid_field' => $uidfield,
                        'reference_field' => $referencefield
                    ];

                    $this->db->where('programid',$val['value']);
                    $this->db->update('mw_program',$update);
                }
            }
            
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function table_reff() {
        
        $currdb = $this->db->database;
        $sql = "SELECT DISTINCT
				table_name as 'table_name'
			FROM
				INFORMATION_SCHEMA.COLUMNS
			WHERE
				TABLE_SCHEMA = '${currdb}'
				AND TABLE_NAME LIKE 'ktv%'
			ORDER BY
				table_name ASC";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function column_reff($TableName) {

        $currdb = $this->db->database;
        $sql = "SELECT DISTINCT
					column_name AS 'column_name',
					concat( column_name, ' - ', column_comment ) AS column_detail
				FROM
					INFORMATION_SCHEMA.COLUMNS
				WHERE
					TABLE_SCHEMA = '${currdb}'
					AND TABLE_NAME = '$TableName'
				ORDER BY
					column_name ASC";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function getInternalProgram($PartnerID){
        $sql = "SELECT
                rbi.BuInExID id
                , BuInExName label
            FROM
                ktv_ref_bu_internal_external rbi
            WHERE
                rbi.StatusCode = 'active'
                AND rbi.BuInExType = 'Internal'
                AND rbi.PartnerID = ?
            ORDER BY
                rbi.BuInExName ASC";
        $query = $this->db->query($sql, [$PartnerID]);

        return $query->result_array();
    }

    //--eoe--
}
?>

<?php
class Msys_menu extends CI_Model {

    function readMenus($get){
        $order = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'MenuName DESC' : $order[0]['property'];
        $sort = $order[0]['direction']=='' ? '' : $order[0]['direction'];
        $MenuName = @$get['textSearch'] =='' ? '*/' : '';
        $MenuModule = @$get['textSearch'] =='' ? '*/' : '';
        if($get['ParentMenu']=="Parent"){
            $ParentMenuSearch = "MenuParentId=0 AND";
        }else if($get['ParentMenu']=="Child"){
            $ParentMenuSearch = "MenuParentId<>0 AND";
        }else{
            $ParentMenuSearch = "";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    MenuId, MenuParentId, MenuName, MenuModule, MenuShow, MenuIcon, MenuOrder, MenuJenis, MenuParam
                from sys_menu 
                WHERE 
                    $ParentMenuSearch
                    (MenuName LIKE ? 
                    OR
                    MenuModule LIKE ?)
                ORDER BY $order_by $sort
                limit ?,?
                ";
        $query = $this->db->query($sql,array("%".$get['textSearch']."%",$get['textSearch'],intval($get['start']), intval($get['limit'])));
        
        $sql_total= "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }else{
            return false;
        }
        
    }

    function getMenuById($MenuId){
        $this->db->select('MenuId,MenuParentId,MenuName,MenuModule,MenuShow,MenuIcon,MenuOrder,MenuJenis,MenuParam');
        $this->db->where('MenuId',$MenuId);
        $query = $this->db->get('sys_menu');

        $sql= "SELECT MenuAksiAksiId FROM sys_menu_act Where MenuAksiMenuId = ?";
        $query_selected = $this->db->query($sql,array($MenuId))->result_array();

        foreach ($query_selected as $key) {
            $MenuSelected[] = $key['MenuAksiAksiId'];
        }

        return array(
            'SysMenu'           => $query->row_array(),
            'SysActSelected'    => $MenuSelected
        );
    }

    function getMenuParent(){
        $sql = "SELECT 
        MenuId, MenuName
            from sys_menu
            WHERE MenuParentId =?
            ORDER BY MenuName ASC";
        $query = $this->db->query($sql,array(0));

        return array(
        'data'      => $query->result_array(),
        );

    }


    function createMenu($post){
        
        $MenuId        = $post['MenuID'];
        if($post['MenuParentId']!=""){
            $MenuParentId = $post['MenuParentId'];
        }else{
            $MenuParentId = 0 ;
        }

        if($post['MenuJenis']!=""){
            $MenuJenis = $post['MenuJenis'];
        }else{
            $MenuJenis = null;
        }

        if($post['MenuParam']!=""){
            $MenuParam = $post['MenuParam'];
        }else{
            $MenuParam = null;
        }

        $data = array(
            "MenuParentId"  => $MenuParentId,
            "MenuName"      => $post['MenuName'],
            "MenuModule"    => $post['MenuModule'],
            "MenuShow"      => $post['MenuShow'],
            "MenuIcon"      => $post['MenuIcon'],
            "MenuOrder"     => $post['MenuOrder'],
            "MenuJenis"     => $MenuJenis,
            "MenuParam"     => $MenuParam

        );
        if(empty($MenuId)){
            $this->db->trans_start();
            $query = $this->db->insert('sys_menu',$data);
            $messageSuccess = "Record Added";
            $messageFaild   = "Failed to add record";

            $MenuId= $this->db->insert_id();
            
            if(!empty($post['ComboSysActDisplays'])){
                
                $this->AddSysMenuAct($post['ComboSysActDisplays'],$MenuId);
            }

            $this->db->trans_complete();

        }else{
            $this->db->trans_start();

            $this->db->where('MenuId',$MenuId);
            $query = $this->db->update('sys_menu',$data);
            $messageSuccess = "Record Updated";
            $messageFaild   = "Failed to update record";

            //$this->deleteMenuAct($MenuId);

            $this->deleteSysGroupMenuAct($MenuId);

            if(!empty($post['ComboSysActDisplays'])){

                $this->AddSysMenuAct($post['ComboSysActDisplays'],$MenuId);
            }

            $this->db->trans_complete();
        }

        if ($query) {
            $results['success'] = true;
            $results['message'] = $messageSuccess;
        } else {
            $results['success'] = false;
            $results['message'] = $messageFaild;
        }
        
        return $results;
    }

    function deleteMenu($MenuId){
        $this->db->trans_start();
        $this->deleteSysGroupMenuAct($MenuId);
        $this->db->where('MenuId',$MenuId);
        $query = $this->db->delete('sys_menu');
        $this->db->trans_complete();
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    function deleteMenuAct($MenuId){
        $this->db->where('MenuAksiMenuId',$MenuId);
		$query = $this->db->delete('sys_menu_act');
    }

    function deleteSysGroupMenuAct($MenuId){
        $sql = "SELECT MenuAksiId,MenuAksiMenuId FROM sys_menu_act where MenuAksiMenuId=?";
        $query =  $this->db->query($sql,array($MenuId))->result();

        foreach ($query as $key) {
            $this->db->where('GroupMenuMenuAksiId',$key->MenuAksiId);
		    $del = $this->db->delete('sys_group_menu_act');
        }

        if($del){
            $this->db->where('MenuAksiMenuId',$MenuId);
		    $query = $this->db->delete('sys_menu_act');
        }
    }


    function AddSysMenuAct($post,$MenuId){
        foreach ($post as $key => $value ) {
            $dataMenuAct = array(
                "MenuAksiMenuId"    => $MenuId,
                "MenuAksiAksiId"    => $value
            );
            $query = $this->db->insert('sys_menu_act',$dataMenuAct);
        }
    }

}
?>

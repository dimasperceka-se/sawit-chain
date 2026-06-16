<?php
class Mrole extends CI_Model
{

    function readRoles($start,$limit){
        $result['data']     = array();
        $result['total']    = 0;
        $sql = "SELECT SQL_CALC_FOUND_ROWS
    RoleId,
    RoleName,
    RoleObjectId,
    RoleDesc,
    o.ObjectName
FROM
    sys_role r
INNER JOIN sys_object o ON o.ObjectId = r.RoleObjectId
WHERE
    r.StatusCode = 'active'
LIMIT ?, ?
        ";
        $query = $this->db->query($sql, array(intval($start), intval($limit)));
        if ($query->num_rows()>0) {
            $result['data']     = $query->result_array();
            $query_total        = $this->db->query("SELECT FOUND_ROWS() AS total");
            $total              = $query_total->row_array(0);
            $result['total']    = $total['total'];
        }
        return $result;
    }

    function readRole($id){
        $sql = "SELECT
    RoleId,
    RoleName,
    RoleObjectId,
    RoleDesc
FROM
    sys_role r
WHERE RoleId = ?
        ";
        $query = $this->db->query($sql, array(intval($id)));
        if ($query->num_rows()>0) {
            $result     = $query->row_array(0);
        }
        return $result;
    }

    public function listObject()
    {
        return $this->db->get('sys_object')->result_array();
    }

    public function createRole($RoleName, $RoleDesc, $RoleObjectId, $role_group, $user_id)
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $role_id = $this->insertRole($RoleName, $RoleDesc, $RoleObjectId, $user_id);
        if ($role_id !== false) {
            $result = $this->insertRoleGroup($role_id, $role_group);
        }
        $this->db->trans_complete();
        return $result;
    }

    public function updateRole($RoleId, $RoleName, $RoleDesc, $RoleObjectId, $role_group, $user_id)
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $result = $this->editRole($RoleId, $RoleName, $RoleDesc, $RoleObjectId, $user_id);
        if ($result !== false) {
            $this->deleteRoleGroup($RoleId);
            $result = $this->insertRoleGroup($RoleId, $role_group);
        }
        $this->db->trans_complete();
        return $result;
    }

    public function insertRole($RoleName, $RoleDesc, $RoleObjectId, $user_id)
    {
        $data = compact('RoleName', 'RoleDesc', 'RoleObjectId');
        $data['created_by'] = $user_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $query = $this->db->insert('sys_role', $data);
        if ($query) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function deleteRole($RoleId)
    {
        $this->db->trans_start(FALSE);
        $result = $this->db->delete('sys_role_group', array('RoleId' => $RoleId));
        $result = $result && $this->db->delete('sys_role', array('RoleId' => $RoleId));
        $this->db->trans_complete();
        return $result;
    }

    public function editRole($RoleId, $RoleName, $RoleDesc, $RoleObjectId, $user_id)
    {
        $data = compact('RoleName', 'RoleDesc', 'RoleObjectId');
        $data['updated_by'] = $user_id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('sys_role', $data, array('RoleId' => $RoleId));
    }

    public function deleteRoleGroup($RoleId)
    {
        return $this->db->delete('sys_role_group', array('RoleId' => $RoleId));
    }

    public function insertRoleGroup($RoleId, $role_group)
    {
        $data = array();
        $groups = explode(',',$role_group);
        foreach ($groups as $key => $group) {
            $data[] = array(
                'RoleId' => $RoleId,
                'GroupId' => $group
            );
        }
        return $this->db->insert_batch('sys_role_group', $data);
    }

    public function listRoleGroup($RoleId)
    {
        $query = $this->db->get_where('sys_role_group', array('RoleId' => $RoleId));
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }

    public function listRoles()
    {
        $sql = "SELECT
    r.RoleId AS id,
    r.RoleName AS `name`,
    o.ObjectName AS object
FROM sys_role r
JOIN sys_object o ON o.ObjectId = r.RoleObjectId
ORDER BY `name`";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }

    public function listLang()
    {
        $this->db->select('id, name', TRUE);
        $this->db->order_by('name', 'asc');
        $query = $this->db->get_where('sys_language');
        if ($query->num_rows()>0) {
            return $query->result_array();
        }
    }

}

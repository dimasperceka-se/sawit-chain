<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class System extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('system/munit');
        $this->load->model('system/mgroup');
        $this->load->model('system/mrole');
        $this->load->model('system/muser');
        $this->load->model('system/mlogupload');
        $this->load->model('system/maction');
    }

    //unit
    function units_get() {
        $units = $this->munit->readUnits($this->get('start'), $this->get('limit'));
        if ($units)
            $this->response($units, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function unit_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $unit = $this->munit->readUnit($this->get('id'));
        if ($unit)
            $this->response($unit, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_post() {
        if (!$this->post('UnitName'))
            $this->response(NULL, 400);
        $unit = $this->munit->createUnit($this->post('UnitName'), $this->post('UnitDescription'), $_SESSION['userid']);
        if ($unit)
            $this->response($unit, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_put() {
        if (!$this->put('UnitId'))
            $this->response(NULL, 400);
        $unit = $this->munit->updateUnit($this->put('UnitId'), $this->put('UnitName'), $this->put('UnitDescription'), $_SESSION['userid']);
        if ($unit)
            $this->response($unit, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_delete() {
        if (!$this->delete('UnitId'))
            $this->response(NULL, 400);
        $unit = $this->munit->deleteUnit($this->delete('UnitId'));
        if ($unit)
            $this->response($unit, 200);
        else
            $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function cek_username_get() {
        $data = $this->muser->readUsername($this->get('id'), $this->get('username'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    //group
    function groups_get() {
        $groups = $this->mgroup->readGroups($this->get('key'),$this->get('unitId'),$this->get('start'), $this->get('limit'));
        if ($groups)
            $this->response($groups, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any groups!'), 404);
    }

    function group_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $group = $this->mgroup->readGroup($this->get('id'));
        if ($group)
            $this->response($group, 200);
        else
            $this->response(array('error' => 'Group could not be found'), 404);
    }

    function group_post() {
        if (!$this->post('GroupName'))
            $this->response(NULL, 400);
        $group = $this->mgroup->createGroup(
                $this->post('GroupName'), $this->post('GroupDescription'), $this->post('GroupUnitId'), $this->post('itemselector'),$this->post('itemselectorReport'), $this->post('GroupMenuId'), $this->post('GroupFilterBy'), $_SESSION['userid'], $this->post('GroupPartnerID'), $this->post('GroupMenuBusinessUnit')
        );
        if ($group)
            $this->response($group, 200);
        else
            $this->response(array('error' => 'Group could not be found'), 404);
    }

    function group_put() {
        if (!$this->put('GroupId'))
            $this->response(NULL, 400);
        $group = $this->mgroup->updateGroup(
                $this->put('GroupId'), $this->put('GroupName'), $this->put('GroupDescription'), $this->put('GroupUnitId'), $this->put('itemselector'), $this->put('itemselectorReport'), $this->put('GroupMenuId'),  $this->put('GroupFilterBy'), $_SESSION['userid'], $this->put('GroupPartnerID'), $this->put('GroupMenuBusinessUnit')
        );
        if ($group)
            $this->response($group, 200);
        else
            $this->response(array('error' => 'Group could not be found'), 404);
    }

    function group_delete() {
        if (!$this->delete('GroupId'))
            $this->response(NULL, 400);
        //$group = $this->mgroup->deleteGroup($this->delete('GroupId'));
        $group = $this->mgroup->deleteGroupStatus($this->delete('GroupId'));
        if ($group)
            $this->response($group, 200);
        else
            $this->response(array('error' => 'Group could not be delete'), 404);
    }

   function group_user_get(){
      if (!$this->get('groupId')) $this->response(NULL, 400);
      $groups = $this->mgroup->readGroupUser($this->get('groupId'),$this->get('start'), $this->get('limit'));
      if ($groups)
         $this->response($groups, 200);
      else
         $this->response(array('error' => 'Group User could not be found'), 404);
   }

    //user
    function users_get() {
        $users = $this->muser->readUsers($this->get('key'),$this->get('RoleId'),$this->get('GroupId'),$this->get('Status'), $this->get('start'), $this->get('limit'));
        if ($users)
            $this->response($users, 200);
        else
            $this->response(array(), 200);
            // $this->response(array('error' => 'Couldn\'t find any users!'), 404);
    }

    function user_get() {
        if (!$this->get('UserId')) {
            $this->response(NULL, 400);
        }
        $user = $this->muser->readUser($this->get('UserId'));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        if ($user !== false) {
            $Role = $this->muser->get_role_detail($user['RoleId']);
            if ($Role !== false) {                
                $method = "get_".strtolower($Role['object'])."_staff";
                $tmp_staff =  $this->muser->$method($user['PersonID']);
            }

            $staff = array();
            if (!empty($tmp_staff)) {
                foreach ($tmp_staff as $key => $value) {
                    $staff[$Role['object'].$key] = $value;
                }
            }
            $groups = array();
            $default_group = 0;
            $tmp_group = $this->muser->getGroups($this->get('UserId'));
            if (!empty($tmp_group)) {
                foreach ($tmp_group as $key => $value) {
                    $groups[] = $value['UserGroupGroupId'];
                    if ($value['UserGroupIsDefault'] == '1') {
                        $default_group = $value['UserGroupGroupId'];
                    }
                }
            }
            $user['UserGroupIsDefault'] = $default_group;
            $user['groups'] = $groups;

            $access = array();
            $tmp_acces = $this->muser->getAccess($this->get('UserId'));
            if (!empty($tmp_acces)) {
                foreach ($tmp_acces as $key => $value) {
                    $access[] = $value['DistrictID'];
                }
            }
            $user['access'] = $access;
            $this->response(array_merge($user, $staff), 200);
        } else {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }

    /**
     * [user_affiliate_get description]
     * @return [type] [description]
     */
    function user_affiliates_get() {
        $users = $this->muser->readUserAffiliates($this->get('key'),$this->get('RoleId'),$this->get('GroupId'),$this->get('Status'), $this->get('start'), $this->get('limit'));
        if ($users)
            $this->response($users, 200);
        else
            $this->response(array(), 200);
    }

     function user_affiliates_delete() {
        if ($this->delete('UserId')) {
            # code...
            $isDelete = $this->muser->resetAffiliated($this->delete('UserId'));
            if ($isDelete) {
                # code...
                $this->response(
                        array(
                            'status' => $isInsert,
                            'message' => 'Success'
                        )
                    , 200);
            } else {
                # code...
                $this->response(
                        array(
                            'status' => $isInsert,
                            'message' => 'Failed'
                        )
                    , 200);
            }
            
        } else {
            # code...
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }

    /**
     * [user_get description]
     * @return [type] [description]
     */
    function user_affiliate_get() {
        if (!$this->get('UserId')) {
            $this->response(NULL, 400);
        }
        $user = $this->muser->readUserAffiliate($this->get('UserId'));
        if ($user !== false) {
            $this->response(array(
                                'user' => $user
                            ), 200);
        } else {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }

    function user_affiliate_post() {
        if ($this->post('UserId')) {
            # code...
            $temp = explode(",", $this->post('UserIds'));
            $data = array(
                    'UserId' => $this->post('UserId'),
                    'Affiliates' => $temp
                );
            $isInsert = $this->muser->insertAffiliate($data);
            if ($isInsert) {
                # code...
                $this->response(
                        array(
                            'status' => $isInsert,
                            'message' => 'Success'
                        )
                    , 200);
            } else {
                $this->response(
                        array(
                            'status' => $isInsert,
                            'message' => 'Failed'
                        )
                    , 500);
            }
        } else {
            # code...
            $this->response(array('error' => 'User could not be found'), 404);
        }
        
        $this->response($data, 200);
    }

    function user_affiliate_delete(){
        if ($this->delete('UserId')) {
            # code...
            $isDelete = $this->muser->deleteAffiliate($this->delete('UserId'));
            if ($isDelete) {
                # code...
                $this->response(
                        array(
                            'status' => $isInsert,
                            'message' => 'Success'
                        )
                    , 200);
            } else {
                # code...
                $this->response(
                        array(
                            'status' => $isInsert,
                            'message' => 'Failed'
                        )
                    , 200);
            }
            
        } else {
            # code...
            $this->response(array('error' => 'User could not be found'), 404);
        }
        
    }

    function user_affiliated_get(){
        if (!$this->get('UserId')) {
            $this->response(NULL, 400);
        }
        $affiliated = $this->muser->getAffiliated($this->get('UserId'));
        $this->response($affiliated->result(), 200);
    }

    function user_affiliated_delete(){
        if (!$this->delete('UserId') && !$this->delete('UserIdAff')) {
            $this->response(NULL, 400);
        } else {
            $deleteAffiliated = $this->muser->deleteAffiliated($this->delete('UserId'), $this->delete('UserIdAff'));
            if ($deleteAffiliated != FALSE) {
                $this->response(array(
                                'status' => true,
                                'message' => lang('User afiliasi telah dihapus.')
                            ), 200);
            } else {
                $this->response(array('error' => 'User could not be found'), 404);
            }
        }
    }

    function other_users_get(){
        $data = $this->muser->getOtherUsers($this->get('UserId'), $this->get('key'));
        $this->response($data->result(), 200);
    }

    function staff_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $user = $this->muser->readStaff($this->get('id'));
        if ($user)
            $this->response($user, 200);
        else
            $this->response(array('error' => 'User could not be found'), 404);
    }

    // function user_post() {
    //     if (!$this->post('UserName'))
    //         $this->response(NULL, 400);
    //     $user = $this->muser->createUser($this->post('UserRealName'), $this->post('UserName'), $this->post('UserActive'), $this->post('UserGroupGroupId'), $this->post('UserPassword'), $_SESSION['userid']);
    //     if ($user)
    //         $this->response($user, 200);
    //     else
    //         $this->response(array('error' => 'User could not be found'), 404);
    // }

    public function user_post()
    {
        $result     = true;
        $message    = '';
        $post       = $this->post(null);
        // echo '<pre>'; print_r($post); echo '</pre>';
        // exit;

        $this->db->trans_start(FALSE);
        if (empty($post['UserId'])) {
            // insert user
            if ($result !== false) {
                $UserId = $this->muser->insert_user($post['UserName'], $post['PersonNm'], md5($post['UserPassword']), $post['OfficialEmail'], $post['StatusCd']=='active'?'Yes':'No', $post['UserLanguage'], $post['UserIsAdmin']);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
                if ($UserId !== false) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
            // insert user role
            if ($result !== false) {
                $result = $this->muser->insert_user_role($UserId, $post['RoleId']);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
            // insert user groups
            if ($result !== false) {
                $GroupIds = explode(',',$post['GroupIds']);
                foreach ($GroupIds as $key => $GroupId) {
                    if ($result !== false) {
                        $isDefault = $GroupId == $post['UserGroupIsDefault'] ? '1' : '0';
                        $result = $this->muser->insert_user_group($UserId, $GroupId, $isDefault);
                        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
                    }
                }
            }
            // insert user access
            if ($result !== false && !empty($post['AccessStaff'])) {
                $AccessStaff = explode(',',$post['AccessStaff']);
                foreach ($AccessStaff as $key => $DistrictID) {
                    if ($result !== false) {
                        $result = $this->muser->insert_user_access($UserId, $DistrictID);
                        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
                    }
                }
            }
            // insert person
            if ($result !== false) {
                $PersonID = $this->muser->insert_person($UserId,$post['Ssn'],$post['EmpNr'],$post['PersonNm'],$post['BirthDate'],$post['BirthPlace'],$post['Gender'],$post['Address'],$post['WorkAreaID'],$post['MaritalSt'],$post['Education'],$post['NationalityNm'],$post['StatusCd'],$post['WorkPhone'],$post['PrivateCellPhone'],$post['OfficialCellPhone'],$post['PrivateEmail'],$post['OfficialEmail']);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
                if ($PersonID !== false) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
            // insert staff
            if ($result !== false) {
                $Role = $this->muser->get_role_detail($post['RoleId']);
                $method = "process_".$Role['object'];
                // echo '<pre>'; print_r($method); echo '</pre>';
                $result =  $this->$method($post, $PersonID, $UserId);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
        } else {
            // update user
            $UserId = $post['UserId'];
            $old_role = $this->muser->get_user_role($UserId);
            // echo '<pre>'; print_r($old_role); echo '</pre>';
            if ($result !== false) {
                $result = $this->muser->update_user($post['UserName'], $post['PersonNm'], $post['OfficialEmail'], $post['StatusCd']=='active'?'Yes':'No', $post['UserLanguage'], $post['UserIsAdmin'], $UserId);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
            // update password if any
            if ($result !== false && $post['UserPassword']) {
                $result = $this->muser->update_user_password(md5($post['UserPassword']), $UserId);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
            // update user role
            if ($result !== false) {
                if ($old_role !== false) {
                    $result = $this->muser->update_user_role($UserId, $post['RoleId']);
                } else {
                    $result = $this->muser->insert_user_role($UserId, $post['RoleId']);
                }
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
            // delete user groups
            if ($result !== false) {
                $result = $this->muser->delete_user_group($UserId);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
            // insert user groups
            if ($result !== false) {
                $GroupIds = explode(',',$post['GroupIds']);
                foreach ($GroupIds as $key => $GroupId) {
                    if ($result !== false) {
                        $isDefault = $GroupId == $post['UserGroupIsDefault'] ? '1' : '0';
                        $result = $this->muser->insert_user_group($UserId, $GroupId, $isDefault);
                        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
                    }
                }
            }
            // delete person access
            if ($result !== false) {
                $result = $this->muser->delete_user_access($UserId);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
            // insert person access
            if ($result !== false && !empty($post['AccessStaff'])) {
                $AccessStaff = explode(',',$post['AccessStaff']);
                foreach ($AccessStaff as $key => $DistrictID) {
                    if ($result !== false) {
                        $result = $this->muser->insert_user_access($UserId, $DistrictID);
                        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
                    }
                }
            }
            // udpate person
            if ($result !== false) {
                $PersonID = $post['PersonID'];
                $result = $this->muser->update_person($UserId,$post['Ssn'],$post['EmpNr'],$post['PersonNm'],$post['BirthDate'],$post['BirthPlace'],$post['Gender'],$post['Address'],$post['WorkAreaID'],$post['MaritalSt'],$post['Education'],$post['NationalityNm'],$post['StatusCd'],$post['WorkPhone'],$post['PrivateCellPhone'],$post['OfficialCellPhone'],$post['PrivateEmail'],$post['OfficialEmail'],$PersonID);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
            // update staff
            if ($result !== false ) {
                // echo '<pre>'; print_r($old_role); echo '</pre>'; 
                if (!empty($old_role) AND $old_role['RoleId'] !== $post['RoleId']) {
                    $OldRole = $this->muser->get_role_detail($old_role['RoleId']);
                    $method = "delete_".strtolower($OldRole['object'])."_staff";    
                    $result = $this->muser->$method($PersonID, $UserId);
                    // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
                }
                $Role = $this->muser->get_role_detail($post['RoleId']);
                $method = "process_".$Role['object'];
                // echo '<pre>'; print_r($method); echo '</pre>';
                $result =  $this->$method($post, $PersonID, $UserId);
                // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
            }
        }
        // $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        // exit;
        // $this->db->trans_complete();
        $this->response(array(
            'success' => $result,
            'message' => $message
        ), 200);
    }

    public function process_Bank($post, $PersonID, $UserId)
    {
        if (!empty($post['BankStaffID'])) {
            return $this->muser->update_bank_staff($post['BankBranchID'], $PersonID, $post['StatusCd'], $post['BankStaffID'], $UserId);
        }
        return $this->muser->insert_bank_staff($post['BankBranchID'], $PersonID, $post['StatusCd'], $UserId);
    }

    public function process_Cooperative($post, $PersonID, $UserId)
    {
        if (!empty($post['CooperativeStaffID'])) {
            return $this->muser->update_cooperative_staff($post['CooperativeCoopID'], $PersonID, $post['CooperativeFarmerID'], $post['CooperativePosition'], $post['CooperativeStaffStatus'], $post['CooperativePaymentStatus'], $post['StatusCd'], $post['CooperativeStaffID'], $UserId);
        }
        return $this->muser->insert_cooperative_staff($post['CooperativeCoopID'], $PersonID, $post['CooperativeFarmerID'], $post['CooperativePosition'], $post['CooperativeStaffStatus'], $post['CooperativePaymentStatus'], $post['StatusCd'], $UserId);
    }

    public function process_CPG($post, $PersonID, $UserId)
    {
        if (!empty($post['CPGStaffID'])) {
            return $this->muser->update_cpg_staff($post['CPGCPGid'], $PersonID, $post['CPGFarmerID'], $post['CPGPosition'], $post['StatusCd'], $post['CPGStaffID'], $UserId);
        }
        return $this->muser->insert_cpg_staff($post['CPGCPGid'], $PersonID, $post['CPGFarmerID'], $post['CPGPosition'], $post['StatusCd'], $UserId);
    }

    public function process_Extension($post, $PersonID, $UserId)
    {
        if (!empty($post['ExtensionExtensionID'])) {
            return $this->muser->update_extension_staff($PersonID,$post['ExtensionStaffPosition'],$post['ExtensionGovInstitute'],$post['StatusCd'],$post['ExtensionExtensionID'], $UserId);
        }
        return $this->muser->insert_extension_staff($PersonID,$post['ExtensionStaffPosition'],$post['ExtensionGovInstitute'],$post['StatusCd'], $UserId);
    }

    public function process_Private($post, $PersonID, $UserId)
    {
        if (!empty($post['PrivatePrivateStaffID'])) {
            return $this->muser->update_private_staff($PersonID,$post['PrivatePartnerID'],$post['StatusCd'],$post['PrivatePrivateStaffID'], $UserId);
        }
        return $this->muser->insert_private_staff($PersonID,$post['PrivatePartnerID'],$post['StatusCd'], $UserId);
    }

    public function process_Program($post, $PersonID, $UserId)
    {
        if (!empty($post['ProgramStaffID'])) {
            return $this->muser->update_program_staff($PersonID,$post['ProgramPartnerID'],null,$post['ProgramPosition'],$post['StatusCd'], $post['ProgramStaffID'], $UserId);
        }
        return $this->muser->insert_program_staff($PersonID,$post['ProgramPartnerID'],null,$post['ProgramPosition'],$post['StatusCd'], $UserId);
    }

    public function process_SCE($post, $PersonID, $UserId)
    {
        if (!empty($post['SCEStaffID'])) {
            return $this->muser->update_sce_staff($PersonID,$post['SCESceID'],$post['SCEFarmerID'],$post['SCEPosition'],$post['StatusCd'],$post['SCEStaffID'], $UserId);
        }
        return $this->muser->insert_sce_staff($PersonID,$post['SCESceID'],$post['SCEFarmerID'],$post['SCEPosition'],$post['StatusCd'], $UserId);
    }

    public function process_Trader($post, $PersonID, $UserId)
    {
        if (!empty($post['TraderTraderStaffID'])) {
            return $this->muser->update_trader_staff($PersonID,$post['TraderTraderID'],$post['TraderPosition'],$post['StatusCd'],$post['TraderTraderStaffID'], $UserId);
        }
        return $this->muser->insert_trader_staff($PersonID,$post['TraderTraderID'],$post['TraderPosition'],$post['StatusCd'], $UserId);
    }

    public function process_Warehouse($post, $PersonID, $UserId)
    {
        if (!empty($post['WarehouseStaffID'])) {
            return $this->muser->update_warehouse_staff($PersonID,$post['WarehouseWarehouseID'],$post['WarehousePosition'],$post['StatusCd'],$post['WarehouseStaffID'], $UserId);
        }
        return $this->muser->insert_warehouse_staff($PersonID,$post['WarehouseWarehouseID'],$post['WarehousePosition'],$post['StatusCd'], $UserId);
    }

    function user_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $user = $this->muser->updateUser($this->put('UserRealName'), $this->put('UserName'), $this->put('UserActive'), $this->put('UserGroupGroupId'), $this->put('UserPassword'), $this->put('id'), $_SESSION['userid']);
        if ($user)
            $this->response($user, 200);
        else
            $this->response(array('error' => 'User could not be found'), 404);
    }

    // function user_delete() {
    //     if (!$this->delete('id'))
    //         $this->response(NULL, 400);
    //     $user = $this->muser->deleteUser($this->delete('id'));
    //     if ($user)
    //         $this->response($user, 200);
    //     else
    //         $this->response(array('error' => 'User could not be delete'), 404);
    // }

    function user_delete() {
        $UserId = $this->delete('UserId');
        if (empty($UserId)) {
            $this->response(NULL, 400);
        }
        $this->db->trans_start(FALSE);
        $old_role = $this->muser->get_user_role($UserId);
        $old_person = $this->muser->get_user_person($UserId);
        // delete staff
        if ($result !== false) {
            $OldRole = $this->muser->get_role_detail($old_role['RoleId']);
            $method = "delete_".strtolower($OldRole['object'])."_staff";    
            $result = $this->muser->$method($old_person['PersonID']);
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        }
        // delete person
        if ($result !== false) $result = $this->muser->delete_person($old_person['PersonID']);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // delete group
        // if ($result !== false) $result = $this->muser->delete_user_group($UserId);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // delete role
        // if ($result !== false) $result = $this->muser->delete_user_role($UserId);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // delete user
        if ($result !== false) $result = $this->muser->delete_user($UserId);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // $this->db->trans_rollback();
        // exit;
        $this->db->trans_complete();
        $response = array(
            'success' => $result,
            'message' => ''
        );
        $this->response($response, 200);
    }

    function userpass_post() {
        $user = $this->muser->updateProfile($this->post('bahasa'), $this->post('notification'), $this->post('id'), $_SESSION['userid']);
        $user = $this->muser->readUser($this->post('UserId'));
        if ($this->post('password_lama') AND $this->post('password') AND $this->post('repassword') AND
                $this->post('password') == $this->post('repassword') AND $user[0]['UserPassword'] == md5($this->post('password_lama')))
            $user = $this->muser->changeUserPassword($this->post('password'), $this->post('UserId'));
        if ($user)
            $this->response($user, 200);
        else
            $this->response(array('error' => 'User could not be found'), 404);
    }

    function grouplist_get() {
        $role = $this->get('RoleId');
        $groups = $this->muser->readGroupList($role);
        if ($groups)
            $this->response($groups, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any group!'), 404);
    }

   function grouplist_search_get(){
      $groups = $this->muser->readGroupListSearch();
      if ($groups)
         $this->response($groups, 200);
      else
         $this->response(array('error' => 'Couldn\'t find any groups!'), 404);
   }

    function groupaksi_get() {
        $aksi = $this->mgroup->readGroupaksi($this->get('id'));
        if ($aksi)
            $this->response($aksi, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function groupaksilist_get() {
        $aksi = $this->mgroup->readGroupaksiList();
        if ($aksi)
            $this->response($aksi, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function membertypelist_get(){
        $this->load->model('member/mmembertype');
        $aksi = $this->mmembertype->readMemberTypeList();
        if ($aksi)
            $this->response($aksi, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function groupreport_get() {
        $aksi = $this->mgroup->readGroupReport($this->get('id'));
        if ($aksi)
            $this->response($aksi, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function groupreportlist_get() {
        $aksi = $this->mgroup->readGroupReportList();
        if ($aksi)
            $this->response($aksi, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    //profile
    function staff_profile_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $user = $this->muser->readStaffProfile($this->get('id'));
        if ($user)
            $this->response($user, 200);
        else
            $this->response(array('error' => 'User could not be found'), 404);
    }

    function staff_profile_put() {
        $user = $this->muser->updateStaffProfile($this->put('bahasa'), $this->put('notification'), $_SESSION['userid']);
        if ($user)
            $this->response($user, 200);
        else
            $this->response(array('error' => 'User could not be found'), 404);
    }

    /*    function profile_post() {
      if(!$this->post('UnitName')) $this->response(NULL, 400);
      $unit = $this->munit->createUnit($this->post('UnitName'), $this->post('UnitDescription'));
      if($unit) $this->response($unit, 200);
      else $this->response(array('error' => 'Unit could not be found'), 404);
      }
     */

    function userpassu_post() {
        $user2 = $this->muser->readUser($this->post('id'));
        if (!$this->post('password_lama') OR !$this->post('password') OR !$this->post('repassword')) {
            $this->response(NULL, 400);
        } elseif ($this->post('password') != $this->post('repassword')) {
            $this->response(NULL, 400);
        } elseif ($user2['UserPassword'] != md5($this->post('password_lama'))) {
            $this->response(NULL, 400);
        }
        else
            $user = $this->muser->changeUserPassword($this->post('password'), $this->post('id'), $_SESSION['userid']);
        if ($user)
            $this->response($user, 200);
        else
            $this->response(array('error' => 'User could not be found'), 404);
    }

    function profile_update_post() {
        $user = $this->muser->updateProfile($this->post('password'), $this->post('id'));
        if ($user)
            $this->response($user, 200);
        else
            $this->response(array('error' => 'User could not be found'), 404);
    }

    function loguploads_get() {
        $loguploads = $this->mlogupload->getLogUploads($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($loguploads)
            $this->response($loguploads, 200);
        else
            $this->response(array('error' => 'Log upload could not be found'), 404);
    }

    public function logupload_excel_get($key = '') {
        $start = 0;
        $limit = 100000;
        if ($key == 'null') {
            $key = '';
        }

        $result = $this->mlogupload->getLogUploads($key, $start, $limit);

        if ($key)
            $detail['key'] = 'Search Key : ' . $key;

        $data = $result['data'];

        foreach ($data[0] as $k => $v) {
            $header[] = $k;
        }

        $this->load->library('Excel', null, 'PHPExcel');
        $filename = 'Report Log Upload '.date('d-m-Y').'.xls';
        $this->PHPExcel->filename($filename);

        $sheet['title'] = 'Log Upload';
        $sheet['header'][] = 'Log Upload Report';
        $sheet['header'][] = $detail['key'];

        $sheet['cols'] = array(
            array(
                'name' => 'No',
                'data' => 'no',
                'size' => 5,
                'align' => 'center'
            )
            , array(
                'name' => 'Log Category',
                'data' => 'logCategory',
                'size' => 25,
                'align' => 'left',
            // 'wrap' => true,
            // 'type' => 'text',
            )
            , array(
                'name' => 'File Name',
                'data' => 'logFileName',
                'size' => 60,
                'align' => 'left',
            // 'wrap' => true,
            // 'type' => 'text',
            )
            , array(
                'name' => 'Status',
                'data' => 'logStatus',
                'size' => 10,
                'align' => 'center',
            // 'wrap' => true,
            // 'type' => 'text',
            )
            , array(
                'name' => 'User',
                'data' => 'UserRealName',
                'size' => 30,
                'align' => 'left',
                'wrap' => true,
                'type' => 'text',
            )
            , array(
                'name' => 'Date',
                'data' => 'DateExecuted',
                'size' => 30,
                'align' => 'center',
                'wrap' => true,
                'type' => 'text',
            )
        );
        $sheet['data'] = $data;

        $path = $this->PHPExcel->create(compact('sheet'), '');
//        header('Location: '.base_url().$filename);
        exit;
    }

    public function roles_get()
    {
        $roles = $this->mrole->readRoles($this->get('start'), $this->get('limit'));
        if ($roles) {
            $this->response($roles, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function role_get()
    {
        $roles = $this->mrole->readRole($this->get('id'));
        if ($roles) {
            $this->response($roles, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    function role_post() {
        if (!$this->post('RoleName'))
            $this->response(NULL, 400);
        $group = $this->mrole->createRole(
                $this->post('RoleName'), $this->post('RoleDesc'), $this->post('RoleObjectId'), $this->post('role_group'), $_SESSION['userid']
        );
        if ($group)
            $this->response($group, 200);
        else
            $this->response(array('error' => 'Group could not be found'), 404);
    }

    function role_put() {
        if (!$this->put('RoleId'))
            $this->response(NULL, 400);
        $group = $this->mrole->updateRole(
                $this->put('RoleId'), $this->put('RoleName'), $this->put('RoleDesc'), $this->put('RoleObjectId'), $this->put('role_group'), $_SESSION['userid']
        );
        if ($group)
            $this->response($group, 200);
        else
            $this->response(array('error' => 'Group could not be found'), 404);
    }

    function role_delete() {
        if (!$this->delete('RoleId'))
            $this->response(NULL, 400);
        $result = $this->mrole->deleteRole($this->delete('RoleId'));
        if ($result)
            $this->response(array('success'=>true), 200);
        else
            $this->response(array('message' => 'Role could not be delete'), 404);
    }

    public function objectlist_get()
    {
        $data = $this->mrole->listObject();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any objects!'), 404);
        }
    }

    public function role_group_get()
    {
        $data = $this->mrole->listRoleGroup($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any objects!'), 404);
        }
    }

    public function role_list_get()
    {
        $data = $this->mrole->listRoles();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
        
    }

    public function lang_list_get()
    {
        $data = $this->mrole->listLang();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any languages!'), 404);
        }
        
    }

    function action_get(){
        $data = $this->maction->getAction($this->get('page'), $this->get('start'), $this->get('limit'), $this->get('key'));
        $this->response(array(
                            'data' => $data->result(),
                            'total' => $this->maction->getAllData($this->get('key'))->num_rows()
                        ), 200);
    }

    function action_post(){
        $data = array(
                    'AksiName' => $this->post('Koltiva_view_Action_MainForm-AksiName'),
                    'AksiFungsi' => $this->post('Koltiva_view_Action_MainForm-AksiFungsi'),
                );
        $result = $this->maction->insertAction($data);
        if ($result) {
            # code...
            $this->response(array(
                                'status' => true,
                                'message' => lang('Success')
                            ), 200);
        } else {
            # code...
            $this->response(array(
                                'status' => false,
                                'message' => lang('Failed')
                            ), 404);
        }
    }

    function action_put(){
        $data = array(
                    'AksiName' => $this->put('Koltiva_view_Action_MainForm-AksiName'),
                    'AksiFungsi' => $this->put('Koltiva_view_Action_MainForm-AksiFungsi'),
                );
        $result = $this->maction->updateAction($data, $this->put('Koltiva_view_Action_MainForm-AksiID'));
        if ($result) {
            # code...
            $this->response(array(
                                'status' => true,
                                'message' => lang('Success')
                            ), 200);
        } else {
            # code...
            $this->response(array(
                                'status' => false,
                                'message' => lang('Failed')
                            ), 404);
        }
    }

    function action_delete(){
        $result = $this->maction->deleteAction($this->delete('AksiId'));
        if ($result) {
            # code...
            $this->response(array(
                                'status' => true,
                                'message' => lang('Success')
                            ), 200);
        } else {
            # code...
            $this->response(array(
                                'status' => false,
                                'message' => lang('Failed')
                            ), 404);
        }
    }

    //edited: ardiantoro@koltiva.com
    function groupprogramlist_get()
    {
        $id = $this->get('id');
        $list = $this->mgroup->readGroupprogramList($id);
        if ($list)
            $this->response($list, 200);
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function groupprogramselected_get()
    {
        $id = (int) $this->get('id');
        $output = [];

        if($id == 0) {
            $this->response(array('error' => 'Group is invalid'), 403);
        }

        $selected = $this->mgroup->readGroupprogramSelected($id);
        if($selected) {
            foreach($selected as $key => $val) {
                array_push($output,$val['ProgramId']);
            }
        }
        
        $this->response(['success' => true, 'data' => $output], 200);
    }

    function updategroupprogramselected_post()
    {
        $id = (int) $this->post('id');
        $programs = json_decode($this->post('programs'),true);
        $output = [];

        if($id == 0) {
            $this->response(array('error' => 'Group is invalid'), 403);
        }

        $selected = $this->mgroup->updateGroupprogramSelected($id,$programs);
        if($selected) {
            $this->response(['success' => true], 200);
        }
        
        $this->response(['success' => false], 200);
    }

    public function regiontree_get() {
        ini_set('display_errors',true);
        error_reporting(E_ALL);
        $tree = array();
        $tree['id'] = '';
		$tree['text'] = 'Country';
		$tree['cls'] = 'root';
		$tree['expanded'] = true;
        $tree['children'] = array();

        $this->load->model('mregion_dq');

        $country = $this->mregion_dq->GetCountry();
        if (!empty($country)) {
            foreach ($country as $key_ct => $ct) {
                $tree['children'][$key_ct]['id']    = $ct['id'];
                $tree['children'][$key_ct]['text']  = $ct['name'];
                $tree['children'][$key_ct]['cls']  = 'country';
                $tree['children'][$key_ct]['expanded']  = false;
                $tree['children'][$key_ct]['checked'] = false;

                $province = $this->mregion_dq->GetProvince($ct['id']);
                if (!empty($province)) {
                    $tree['children'][$key_ct]['leaf'] = false;
                    foreach ($province as $key_prov => $prov) {
                        $tree['children'][$key_ct]['children'][$key_prov]['id']    = $prov['id'];
                        $tree['children'][$key_ct]['children'][$key_prov]['text']  = $prov['name'];
                        $tree['children'][$key_ct]['children'][$key_prov]['cls']  = 'province';
                        $tree['children'][$key_ct]['children'][$key_prov]['expanded']  = false;
                        $tree['children'][$key_ct]['children'][$key_prov]['checked']  = false;

                        $district = $this->mregion_dq->GetDistrict($prov['id']);
                        if (!empty($district)) {
                            $tree['children'][$key_ct]['children'][$key_prov]['leaf']  = false;
                            foreach ($district as $key_dis => $dis) {
                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['id']    = $dis['id'];
                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['text']  = $dis['name'];
                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['cls']  = 'district';
                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['expanded']  = false;
                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['checked']  = false;

                                $subdistrict = array(); //$this->mregion_dq->GetSubDistrict($dis['id']);
                                if (!empty($subdistrict)) {
                                    $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['leaf']  = false;
                                    foreach ($subdistrict as $key_subdis => $subdis) {
                                        $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['id'] = $subdis['id'];
                                        $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['text'] = $subdis['name'];
                                        $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['cls'] = 'subdistrict';
                                        $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['expanded'] = false;
                                        $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['leaf'] = true;
                                        $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['checked']  = false;
                                        
                                        $village = $this->mregion_dq->GetVillage($subdis['id']);
                                        if (!empty($village)) {
                                            $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['leaf'] = false;
                                            foreach ($village as $key_village => $vill) {
                                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['children'][$key_village]['id'] = $vill['id'];
                                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['children'][$key_village]['text'] = $vill['name'];
                                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['children'][$key_village]['cls'] = 'village';
                                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['children'][$key_village]['expanded'] = false;
                                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['children'][$key_village]['leaf'] = true;
                                                $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['children'][$key_subdis]['children'][$key_village]['checked']  = false;
                                                
                                            }
                                        } else {
                                            $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['leaf']  = true;
                                        }
                                    }
                                } else {
                                    $tree['children'][$key_ct]['children'][$key_prov]['children'][$key_dis]['leaf']  = true;
                                }
                            }
                        } else {
                            $tree['children'][$key_ct]['children'][$key_prov]['leaf']  = true;
                        }
                    }
                } else {
                    $tree['children'][$key_ct]['leaf']  = true;
                }
            }
        }

        $this->response(array(
			'text' => '.',
			'children' => $tree
		), 200);
    }

    public function cmbprogramtable_get() {

        $data = $this->mgroup->table_reff();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
    }

    public function cmbprogramfield_get() {

        $data = $this->mgroup->column_reff($this->get('table'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any field!'), 404);
        }
    }

    public function combo_internal_program_get(){
        $PartnerID = $this->get('PartnerID');

        if (empty($PartnerID)) {
            $data = [];
        } else {
            $data = $this->mgroup->getInternalProgram($PartnerID);
        }

        $this->response($data, 200);
    }

    //--eoe--

}

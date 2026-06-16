<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Conversion extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mconversion');
        $this->load->model('system/muser');
    }

    public function bank_get()
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $staffs = $this->mconversion->getBankStaffs();
        echo '<pre>'; print_r($staffs); echo '</pre>'; 
        if ($staffs) {
            foreach ($staffs as $key => $staff) {
                $data = array();
                $data['PersonNm']           = $staff['StaffName'];
                $data['OfficialCellPhone']  = $staff['Phone'];
                $data['OfficialEmail']      = $staff['Email'];
                $data['BirthDate']          = $staff['StaffBirth'];
                $data['Gender']             = $staff['StaffGender'];
                $data['Photo']              = $staff['Photo'];
                $data['Address']            = $staff['Address'];
                $data['UserID']             = $staff['UserId'];
                $data['StatusCd']           = $staff['StatusCode'];
                $data['DateCreated']        = $staff['DateCreated'];
                $data['CreatedBy']          = $staff['CreatedBy'];
                $data['DateUpdated']        = $staff['DateUpdated'];
                $data['UpdatedBy']          = $staff['LastModifiedBy'];
                $PersonID = $this->mconversion->insertPerson($data);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $result = $this->mconversion->updateBankStaff(array('PersonID'=>$PersonID), $staff['StaffID']);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                // insert user role
                $result = $this->muser->insert_user_role($staff['UserId'], 1);
            }
        }
        // debuging 
        // $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        exit;
        $this->response($result, 200);
    }

    public function cooperative_get()
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $staffs = $this->mconversion->getCooperativeStaffs();
        // echo '<pre>'; print_r($staffs); echo '</pre>'; 
        if ($staffs) {
            foreach ($staffs as $key => $staff) {
                echo '<pre>'; print_r($staff); echo '</pre>'; 
                if (empty($staff['UserId'])) {
                    $staffname = is_numeric($staff['StaffName'])?$staff['FarmerName']:$staff['StaffName'];
                    echo '<pre>'; print_r($staffname); echo '</pre>'; 
                    $username = $this->muser->get_username(str_replace(' ', '_', $staffname));
                    $staff['UserId'] = $this->muser->insert_user($username, $staffname, md5($staff['StaffName']), $staff['Email'], 'No', 'Indonesia', '0');
                    echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                }

                $data = array();
                $data['PersonNm']           = $staffname;
                $data['OfficialCellPhone']  = $staff['Phone'];
                $data['OfficialEmail']      = $staff['Email'];
                $data['BirthDate']          = !empty($staff['StaffBirthday'])?$staff['StaffBirthday']:'0000-00-00';
                $data['Gender']             = $staff['StaffGender']=='2'?'f':'m';
                $data['UserID']             = $staff['UserId'];
                $data['StatusCd']           = $staff['StatusCode'];
                $data['DateCreated']        = $staff['DateCreated'];
                $data['CreatedBy']          = $staff['CreatedBy'];
                $data['DateUpdated']        = $staff['DateUpdated'];
                $data['UpdatedBy']          = $staff['LastModifiedBy'];
                $PersonID = $this->mconversion->insertPerson($data);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $result = $this->mconversion->updateCooperativeStaff(array('PersonID'=>$PersonID), $staff['StaffID']);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                // insert user role
                $result = $this->muser->insert_user_role($staff['UserId'], 2);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        // debuging 
        // $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        exit;
        $this->response($result, 200);
    }

    public function cpg_get()
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $this->mconversion->updateCPGStaffData();
        $staffs = $this->mconversion->getCPGStaffs();
        echo '<pre>'; print_r($staffs); echo '</pre>'; 
        if ($staffs) {
            foreach ($staffs as $key => $staff) {
                $data = array();
                $data['PersonNm']           = $staff['StaffName'];
                $data['OfficialCellPhone']  = $staff['Phone'];
                $data['OfficialEmail']      = $staff['Email'];
                $data['BirthDate']          = !empty($staff['StaffBirthday'])?$staff['StaffBirthday']:'0000-00-00';
                $data['Gender']             = $staff['StaffGender']=='2'?'f':'m';
                $data['UserID']             = $staff['UserId'];
                $data['StatusCd']           = $staff['StatusCode'];
                $data['DateCreated']        = $staff['DateCreated'];
                $data['CreatedBy']          = $staff['CreatedBy'];
                $data['DateUpdated']        = $staff['DateUpdated'];
                $data['UpdatedBy']          = $staff['LastModifiedBy'];

                $PersonID = $this->mconversion->insertPerson($data);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $result = $this->mconversion->updateCPGStaff(array('PersonID'=>$PersonID), $staff['StaffID']);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                // insert user role
                $result = $this->muser->insert_user_role($staff['UserId'], 3);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        // debuging 
        $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        exit;
        $this->response($result, 200);
    }

    // not finished, can not create user, username already exist
    public function extension_get()
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $staffs = $this->mconversion->getExtensionStaffs();
        // echo '<pre>'; print_r($staffs); echo '</pre>'; 
        if ($staffs) {
            foreach ($staffs as $key => $staff) {
                echo '<pre>'; print_r($staff); echo '</pre>'; 
                
                $staffname = $staff['StaffName'];
                $username = $this->muser->get_username(str_replace(',', '', str_replace(' ', '_', $staffname)));
                $staff['UserId'] = $this->muser->insert_user($username, $staffname, md5($staff['StaffName']), $staff['Email'], 'No', 'Indonesia', '0');
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                
                $data = array();
                $data['PersonNm']           = $staff['StaffName'];
                $data['Address']            = $staff['Address'];
                $data['OfficialCellPhone']  = $staff['Handphone'];
                $data['OfficialEmail']      = $staff['Email'];
                $data['BirthDate']          = !empty($staff['BirthDttm'])?$staff['BirthDttm']:'0000-00-00';
                $data['Gender']             = $staff['Gender']=='2'?'f':'m';
                $data['Photo']              = $staff['Photo'];
                $data['MaritalSt']          = $staff['MaritalSt'];
                $data['Education']          = $staff['Education'];
                $data['UserID']             = $staff['UserId'];
                $data['StatusCd']           = $staff['StatusCd'];
                $data['DateCreated']        = $staff['DateCreated'];
                $data['CreatedBy']          = $staff['CreatedBy'];
                $data['DateUpdated']        = $staff['DateUpdated'];
                $data['UpdatedBy']          = $staff['LastModifiedBy'];

                $PersonID = $this->mconversion->insertPerson($data);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $result = $this->mconversion->updateExtensionStaff(array('PersonID'=>$PersonID), $staff['ExtensionID']);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                // insert user role
                $result = $this->muser->insert_user_role($staff['UserId'], 4);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        // debuging 
        // $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        exit;
        $this->response($result, 200);
    }

    public function private_get()
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $staffs = $this->mconversion->getPrivateStaffs();
        // echo '<pre>'; print_r($staffs); echo '</pre>'; 
        if ($staffs) {
            foreach ($staffs as $key => $staff) {
                echo '<pre>'; print_r($staff); echo '</pre>'; 
                if (empty($staff['UserId'])) {
                    $staffname = $staff['StaffName'];
                    echo '<pre>'; print_r($staffname); echo '</pre>'; 
                    $username = $this->muser->get_username(str_replace(',', '', str_replace(' ', '_', $staffname)));
                    $staff['UserId'] = $this->muser->insert_user($username, $staffname, md5($staff['StaffName']), $staff['Email'], 'No', 'Indonesia', '0');
                    echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                }
                $data = array();
                $data['PersonNm']           = $staff['StaffName'];
                $data['PrivateCellPhone']   = $staff['PrivateCellphone'];
                $data['OfficialCellPhone']  = $staff['OfficialCellphone'];
                $data['PrivateEmail']       = $staff['PrivateStaffEmail'];
                $data['OfficialEmail']      = $staff['OfficialStaffEmail'];
                $data['BirthDate']          = !empty($staff['StaffBirth'])?$staff['StaffBirth']:'0000-00-00';
                $data['Gender']             = $staff['StaffGender']=='2'?'f':'m';
                $data['Photo']              = $staff['Photo'];
                $data['UserID']             = $staff['UserId'];
                $data['StatusCd']           = $staff['StatusCode'];
                $data['DateCreated']        = $staff['DateCreated'];
                $data['CreatedBy']          = $staff['CreatedBy'];
                $data['DateUpdated']        = $staff['DateUpdated'];
                $data['UpdatedBy']          = $staff['LastModifiedBy'];

                $PersonID = $this->mconversion->insertPerson($data);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $result = $this->mconversion->updatePrivateStaff(array('PersonID'=>$PersonID), $staff['PrivateStaffID']);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                // insert user role
                $result = $this->muser->insert_user_role($staff['UserId'], 5);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        // debuging 
        // $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        exit;
        $this->response($result, 200);
    }

    public function program_get()
    {
        $update_person = "UPDATE ktv_persons p, ktv_program_staff ps
SET p.UserID = ps.UserId
WHERE
    p.PersonID = ps.PersonID
        ";
        $insert_role = "INSERT INTO sys_user_role (RoleId, UserId) 
SELECT
    6,
    p.UserID
FROM ktv_persons p
JOIN ktv_program_staff ps ON ps.PersonID = p.PersonID
        ";
        echo '<pre>'; var_dump($update_person, $insert_role); echo '</pre>'; exit;
    }

    public function sce_get()
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $staffs = $this->mconversion->getSCEStaffs();
        echo '<pre>'; print_r($staffs); echo '</pre>'; 
        if ($staffs) {
            foreach ($staffs as $key => $staff) {
                $data = array();
                $data['PersonNm']           = $staff['StaffName'];
                $data['OfficialCellPhone']  = $staff['Phone'];
                $data['OfficialEmail']      = $staff['Email'];
                $data['BirthDate']          = !empty($staff['StaffBirthday'])?$staff['StaffBirthday']:'0000-00-00';
                $data['Gender']             = $staff['StaffGender']=='2'?'f':'m';
                $data['UserID']             = $staff['UserId'];
                $data['StatusCd']           = $staff['StatusCode'];
                $data['DateCreated']        = $staff['DateCreated'];
                $data['CreatedBy']          = $staff['CreatedBy'];
                $data['DateUpdated']        = $staff['DateUpdated'];
                $data['UpdatedBy']          = $staff['LastModifiedBy'];              

                $PersonID = $this->mconversion->insertPerson($data);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $result = $this->mconversion->updateSCEStaff(array('PersonID'=>$PersonID), $staff['StaffID']);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                // insert user role
                $result = $this->muser->insert_user_role($staff['UserId'], 7);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        // debuging 
        // $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        exit;
        $this->response($result, 200);
    }

    public function trader_get()
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $staffs = $this->mconversion->getTraderStaffs();
        echo '<pre>'; print_r($staffs); echo '</pre>'; 
        if ($staffs) {
            foreach ($staffs as $key => $staff) {
                $data = array();
                $data['PersonNm']           = $staff['StaffName'];
                $data['PrivateCellPhone']   = $staff['PrivateCellphone'];
                $data['OfficialCellPhone']  = $staff['OfficialCellphone'];
                $data['PrivateEmail']       = $staff['PrivateStaffEmail'];
                $data['OfficialEmail']      = $staff['OfficialStaffEmail'];
                $data['BirthDate']          = !empty($staff['StaffBirth'])?$staff['StaffBirth']:'0000-00-00';
                $data['Gender']             = $staff['StaffGender']=='2'?'f':'m';
                $data['Photo']              = $staff['Photo'];
                $data['Education']          = $staff['Education'];
                $data['Address']            = $staff['Address'];
                $data['UserID']             = $staff['UserId'];
                $data['StatusCd']           = $staff['StatusCode'];
                $data['DateCreated']        = $staff['DateCreated'];
                $data['CreatedBy']          = $staff['CreatedBy'];
                $data['DateUpdated']        = $staff['DateUpdated'];
                $data['UpdatedBy']          = $staff['LastModifiedBy'];

                $PersonID = $this->mconversion->insertPerson($data);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $result = $this->mconversion->updateTraderStaff(array('PersonID'=>$PersonID), $staff['TraderStaffID']);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                // insert user role
                $result = $this->muser->insert_user_role($staff['UserId'], 8);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        // debuging 
        // $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        exit;
        $this->response($result, 200);
    }

    public function warehouse_get()
    {
        $result = true;
        $this->db->trans_start(FALSE);
        $staffs = $this->mconversion->getWarehouseStaffs();
        echo '<pre>'; print_r($staffs); echo '</pre>'; 
        if ($staffs) {
            foreach ($staffs as $key => $staff) {
                $data = array();
                $data['PersonNm']           = $staff['StaffName'];
                $data['OfficialCellPhone']  = $staff['Phone'];
                $data['OfficialEmail']      = $staff['Email'];
                $data['BirthDate']          = !empty($staff['StaffBirth'])?$staff['StaffBirth']:'0000-00-00';
                $data['Gender']             = $staff['StaffGender']=='2'?'f':'m';
                $data['Photo']              = $staff['Photo'];
                $data['Education']          = $staff['Education'];
                $data['Address']            = $staff['Address'];
                $data['UserID']             = $staff['UserId'];
                $data['StatusCd']           = $staff['StatusCd'];
                $data['DateCreated']        = $staff['DateCreated'];
                $data['CreatedBy']          = $staff['CreatedBy'];
                $data['DateUpdated']        = $staff['DateUpdated'];
                $data['UpdatedBy']          = $staff['LastModifiedBy'];               

                $PersonID = $this->mconversion->insertPerson($data);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                $result = $this->mconversion->updateWarehouseStaff(array('PersonID'=>$PersonID), $staff['TraderStaffID']);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
                // insert user role
                $result = $this->muser->insert_user_role($staff['UserId'], 8);
                echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            }
        }
        // debuging 
        $result = false;
        if ($result !== false) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
        exit;
        $this->response($result, 200);
    }

}

/* End of file conversion.php */
/* Location: ./application/controllers/conversion.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bank extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('bank/mbank');
	}

    public function bangs_get()
    {
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = $sorting[0]->property;
        else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = $sorting[0]->direction;
        else $sortingDir = null;

        $data = $this->mbank->getBanks($this->get('textSearch'),$this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any program stage!'), 404);
        }
    }

	public function bang_get()
	{
        $data = $this->mbank->getBank($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
	}

    public function add_post()
    {
        $varPost = $this->post();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Basic_NewBank_MainForm-FormBasicData-", '', $key);
            if ($value == "") {
                $value = null;
            }

            $paramPost[$keyNew] = $value;
        }

        if ($this->post('opsiDisplay') == "insert") {
            //insert
            $proses = $this->mbank->insertNewBank($paramPost);
        } else {
            //update
            $proses = $this->mbank->updateNewBank($paramPost);
        }

        if ($proses['success'] == true) {
            $response = $this->response($proses, 200);
        } else {
            $response = $this->response($proses, 400);
        }

        return $response;
    }

    public function newbank_basic_data_form_get()
    {
        $BankID = (int)$this->get('BankID');
    
        $data     = $this->mbank->getNewBankBasicDataForm($BankID);

        $this->response($data, 200);
    }

    public function newbank_remove_delete()
    {
        $BankID     = (int)$this->delete('BankID');

        $proses     = $this->mbank->DeleteNewBank($BankID);

        return $this->response($proses, 200);
    }

    public function bang_post()
    {
        if(!$this->post('name')) $this->response(NULL, 400);
        $data = $this->mbank->createBank($this->post('name'),$this->post('desc'));
        if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    public function bang_put()
    {
        if(!$this->put('id')) $this->response(NULL, 400);
        $data = $this->mbank->updateBank($this->put('name'),$this->put('desc'),$this->put('id'));
        if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    public function bang_delete()
    {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mbank->deleteBank($this->delete('id'));
        if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

    public function branchs_get()
    {
        $data = $this->mbank->getBranches($this->get('start'),$this->get('limit'),$this->get('ProvinceID'),$this->get('DistrictID'),$this->get('SubDistrictID'),$this->get('key'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

	public function branch_get()
	{
        $data = $this->mbank->getBranch($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
	}

    public function branch_post()
    {
        if(!$this->post('name')) $this->response(NULL, 400);
        $bankid         = $this->post('bankid');
        $name           = $this->post('name');
        $address        = $this->post('address');
        $provinceid     = $this->post('provinceid');
        $districtid     = $this->post('districtid');
        $subdistrictid  = $this->post('subdistrictid');
        $villageid      = $this->post('villageid');
        $phone          = $this->post('phone');
        $latitude       = $this->post('latitude');
        $longitude      = $this->post('longitude');
        $desc           = $this->post('desc');
        $data = $this->mbank->createBranch($bankid,$name,$address,$provinceid,$districtid,$subdistrictid,$villageid,$phone,$latitude,$longitude,$desc);
        if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    public function branch_put()
    {
        if(!$this->put('id')) $this->response(NULL, 400);
        $id         = $this->put('id');
        $bankid         = $this->put('bankid');
        $name           = $this->put('name');
        $address        = $this->put('address');
        $provinceid     = $this->put('provinceid');
        $districtid     = $this->put('districtid');
        $subdistrictid  = $this->put('subdistrictid');
        $villageid      = $this->put('villageid');
        $phone          = $this->put('phone');
        $latitude       = $this->put('latitude');
        $longitude      = $this->put('longitude');
        $desc           = $this->put('desc');
        $data = $this->mbank->updateBranch($bankid,$name,$address,$provinceid,$districtid,$subdistrictid,$villageid,$phone,$latitude,$longitude,$desc,$id);
        if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    public function branch_delete()
    {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mbank->deleteBranch($this->delete('id'));
        if($data) $this->response(array('success'=>true), 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

    public function banglist_get()
    {
        $get = $this->get(null);
        $data = $this->mbank->getBangList($get);
        $this->response($data, 200);
    }

    public function branchlist_get()
    {
        $data = $this->mbank->getBranchList($this->get('bank'),$this->get('SubDistrictID'));
        $this->response($data, 200);
    }

    public function province_get()
    {
    	$data = $this->mbank->getProvince();
    	$this->response($data, 200);
    }

    public function district_get()
    {
        $provinceid = $this->get('provinceid');
        $data = $this->mbank->getDistrict($provinceid);
        $this->response($data, 200);
    }

    public function subdistrict_get()
    {
        $districtid = $this->get('districtid');
    	$data = $this->mbank->getSubDistrict($districtid);
    	$this->response($data, 200);
    }

    public function village_get()
    {
        $subdistrictid = $this->get('subdistrictid');
    	$data = $this->mbank->getVillage($subdistrictid);
    	$this->response($data, 200);
    }

    public function branch_staffs_get()
    {
        $BranchID = $this->get('BranchID');
        $data = $this->mbank->getBranchStafs($BranchID);
        $this->response($data, 200);
    }

    public function branch_staff_get()
    {
        $StaffID = $this->get('StaffID');
        $data = $this->mbank->getBranchStaf($StaffID);
        $this->response($data, 200);
    }

    public function branch_staff_post()
    {
        $msg = '';
        $post = $this->post(NULL);
        if (empty($post['StaffID'])) {
            // ADD
            $this->db->trans_start(FALSE);
            // add branch staff
            $result = $this->mbank->addBranchStaf(
                $post['BranchID'],
                $post['StaffName'],
                $post['Phone'],
                $post['Email'],
                $post['StaffBirth'],
                $post['StaffGender'],
                // $post['Photo'],
                $post['IdentityNumber'],
                $post['VillageID'],
                $post['Address']
                // $post['UserId']
            );
            // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
            $StaffID = $this->db->insert_id();
            // add user
            if ($result && $post['Username'] && $post['Password'] && $post['GroupId']) {
                $this->load->model('system/muser', 'muser');
                // $UserRealName,$UserName,$UserActive,$groupId,$pass,$userid
                $result_user = $this->muser->createUser($post['StaffName'], $post['Username'], 1, $post['GroupId'], $post['Password'], $_SESSION['userid']);
                $result = $result_user['success'];
                $UserId = $result_user['userid'];
            }
            if ($result) {
                $this->mbank->updateStaffUser($UserId, $StaffID);
            }
            // upload photo
            if ($result && !empty($_FILES['PhotoUpload'])) {
                $config['upload_path']      = './images/bank_staff/';
                $config['allowed_types']    = 'gif|jpg|png';
                // $config['max_size']         = '100';
                $config['max_width']        = '1024';
                $config['max_height']       = '768';

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('PhotoUpload'))
                {
                    // $error = array('error' => $this->upload->display_errors());
                    // echo "<pre>"; print_r($error); echo "</pre>";
                } else {
                    $upload_data = array('upload_data' => $this->upload->data());
                    // echo "<pre>"; print_r($data); echo "</pre>";
                    $this->mbank->updateStaffPhoto($upload_data['upload_data']['file_name'], $StaffID);
                    // delete old photo if any
                    if (!empty($post['Photo']) && file_exists($config['upload_path'].$post['Photo'])) {
                        @unlink($config['upload_path'].$post['Photo']);
                    }
                }
            }
            if ($result) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }
        } else {
            // UPDATE
            $this->db->trans_start(FALSE);
            // add branch staff
            $result = $this->mbank->updateBranchStaf(
                $post['BranchID'],
                $post['StaffName'],
                $post['Phone'],
                $post['Email'],
                $post['StaffBirth'],
                $post['StaffGender'],
                // $post['Photo'],
                $post['IdentityNumber'],
                $post['VillageID'],
                $post['Address'],
                $post['StaffID']
            );
            // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
            $StaffID = $post['StaffID'];
            // add user
            if ($result && $post['Username'] && $post['GroupId']) {
                $this->load->model('system/muser', 'muser');
                // $UserRealName,$UserName,$UserActive,$groupId,$pass,$userid
                $result_user = $this->muser->updateUser($post['StaffName'], $post['Username'], 1, $post['GroupId'], $post['Password'],$post['UserId'], $_SESSION['userid']);
                $result = $result_user['success'];
                $UserId = $post['UserId'];
            }
            // upload photo
            if ($result && !empty($_FILES['PhotoUpload'])) {
                $config['upload_path']      = './images/bank_staff/';
                $config['allowed_types']    = 'gif|jpg|png';
                // $config['max_size']         = '100';
                $config['max_width']        = '1024';
                $config['max_height']       = '768';

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('PhotoUpload'))
                {
                    $error = array('error' => $this->upload->display_errors());
                    // echo "<pre>"; print_r($error); echo "</pre>";
                    $msg .= $error['error'];
                } else {
                    $upload_data = array('upload_data' => $this->upload->data());
                    // echo "<pre>"; print_r($data); echo "</pre>";
                    $this->mbank->updateStaffPhoto($upload_data['upload_data']['file_name'], $StaffID);
                    // delete old photo if any
                    if (!empty($post['Photo']) && file_exists($config['upload_path'].$post['Photo'])) {
                        @unlink($config['upload_path'].$post['Photo']);
                    }
                }
            }
            if ($result) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }
        }

        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response(array(
            'success'   => $result,
            'msg'       => $msg
        ), 200);
    }

    public function branch_staff_delete()
    {
        $result     = true;
        $msg        = '';
        $StaffID    = $this->delete('StaffID');
        $StaffDetail = $this->mbank->getBranchStaf($StaffID);
        $this->db->trans_start(FALSE);
        $result = $this->mbank->deleteBranchStaff($StaffID);
        if ($result) {
            $this->load->model('system/muser', 'muser');
            $result_user = $this->muser->deleteUser($StaffDetail['UserId']);
            $result = $result_user['success'];
        }
        $this->db->trans_complete();
        $this->response(array(
            'success'   => $result,
            'msg'       => $msg
        ), 200);
    }

    public function group_get()
    {
        $data = $this->mbank->getGroup();
        $this->response($data, 200);
    }

    public function farmer_summary_list_get($prov = '', $dist = '', $subd = '', $cpg = '')
    {
        $get    = $this->get(NULL);
        $data   = $this->mbank->getFarmerList($get);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $total  = $this->mbank->countFarmerList();
        $this->response(array(
            'data'      => $data,
            'total'     => $total,
        ), 200);
    }

    public function farmer_summary_post()
    {
        set_time_limit(0);
        $this->load->model('farmer/mfarmer');
        $post = $this->post(null);
        $FarmerIDs = explode(',', $post['FarmerIDs']);
        $bank = $this->mbank->getBank($post['BankID']);
        $files = array();
        foreach ($FarmerIDs as $key => $FarmerID) {
            // generate file
            $files[] = $this->mfarmer->pdf_farmer_summary_loan($FarmerID);
            // save log
            $this->mbank->saveFarmerSummaryLog($FarmerID, $post['BankID'], $post['BankBrancID'], $post['Desc']);
        }

        $this->response(array(
            'success'   => true,
            'url'       => base_url().'bank/farmer_summary_download/'.$bank['name'].'/'.urlencode(implode(',', $FarmerIDs))
        ),200);
    }

    public function farmer_summary_approval_post()
    {
        $post = $this->post(null);
        $result = $this->mbank->processApproval(explode(',', $post['FarmerID']), $post['ApprovalStatus'], $post['StatusNotes'], $post['LoanAmount']);
        $this->response(array(
            'success'   => $result,
        ),200);
    }

    public function farmer_summary_finalization_post()
    {
        $post = $this->post(null);
        $result = $this->mbank->processFinalization($post['FarmerID'], $post['LoanAmount']);
        $this->response(array(
            'success'   => $result,
        ),200);
    }

    public function farmer_summary_detail_get()
    {
        $post = $this->get(null);
        $result = $this->mbank->getDetail($post['FarmerID']);
        if (!empty($result['DateUpdated'])) {
            $result['DateUpdated'] = date('d F Y H:i', strtotime($result['DateUpdated']));
        }
        $this->response($result,200);
    }

    public function farmer_summary_download_get($bank, $FarmerIDs)
    {
        $bank = urldecode($bank);
        if (!empty($FarmerIDs)) {
            $FarmerIDs = explode(',', urldecode($FarmerIDs));
        }
        $path = 'pdf/';
        $this->load->library('zip');
        foreach ($FarmerIDs as $key => $FarmerID) {
            $this->zip->read_file($path.$FarmerID.'.pdf');
        }
        $datetime = date('YmdHis');
        $this->zip->download("farmer_summary_{$bank}_{$datetime}.zip");
        // echo "<pre>"; print_r($FarmerIDs); echo "</pre>"; exit;
    }

    public function geospatial_get()
    {
        $ProvinceID = $this->get('ProvinceID');
        $DistrictID = $this->get('DistrictID');
        $SubDistrictID = $this->get('SubDistrictID');
        $BranchID = $this->get('BranchID');
        $data = $this->mbank->getGeospatialBank($ProvinceID,$DistrictID,$SubDistrictID,$BranchID);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_farmer_fitted_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialFarmerFitted($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_farmer_approved_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialFarmerApproved($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_farmer_rejected_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialFarmerRejected($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_farmer_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialFarmer($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_farmer_certified_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialFarmerCertified($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_nursery_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialNursery($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_demoplot_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialDemoplot($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_farmer_organization_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialFarmerOrganization($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_warehouse_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialWarehouse($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function geospatial_trader_get()
    {
        $lat        = $this->get('lat');
        $lng        = $this->get('lng');
        $radius     = $this->get('radius')/1000;
        $data = $this->mbank->getGeospatialTrader($lat,$lng,$radius);
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        $this->response($data, 200);
    }

    public function detail_bank_get()
    {
        $id = $this->get('id');
        $data = $this->mbank->getBranch($id);
        $this->response($data, 200);
    }

    public function detail_farmer_get()
    {
        $FarmerID = $this->get('FarmerID');
        $GardenNr = $this->get('GardenNr');
        $SurveyNr = $this->get('SurveyNr');
        $data = $this->mbank->getFarmer($FarmerID,$GardenNr,$SurveyNr);
        if (!empty($data)) {
            if (!empty($data['Photo'])) {
                if (!file_exists('images/Photo/'.$data['Photo'])) {
                    $data['Photo'] = 'default-user.png';
                }
            }
        }
        $this->response($data, 200);
    }

    public function detail_farmer_certified_get()
    {
        $FarmerID = $this->get('FarmerID');
        $GardenNr = $this->get('GardenNr');
        $SurveyNr = $this->get('SurveyNr');
        $data = $this->mbank->getFarmer($FarmerID,$GardenNr,$SurveyNr);
        if (!empty($data)) {
            if (!empty($data['Photo'])) {
                if (!file_exists('images/Photo/'.$data['Photo'])) {
                    $data['Photo'] = 'default-user.png';
                }
            }
        }
        $this->response($data, 200);
    }

    public function detail_nursery_get()
    {
        $id = $this->get('id');
        $data = $this->mbank->getNursery($id);
        $this->response($data, 200);
    }

    public function detail_demoplot_get()
    {
        $id = $this->get('id');
        $data = $this->mbank->getDemoplot($id);
        $this->response($data, 200);
    }

    public function detail_farmer_organization_get()
    {
        $id = $this->get('id');
        $data = $this->mbank->getFarmerOrganization($id);
        $this->response($data, 200);
    }

    public function detail_warehouse_get()
    {
        $id = $this->get('id');
        $data = $this->mbank->getWarehouse($id);
        $this->response($data, 200);
    }

    public function detail_trader_get()
    {
        $id = $this->get('id');
        $data = $this->mbank->getTrader($id);
        $this->response($data, 200);
    }

}
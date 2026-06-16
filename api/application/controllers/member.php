<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('member/mmember');
        $this->load->model('coop/msaving');
    }

    function coop_members_get() {
        $members = $this->mmember->readMembers($this->get('key'), $this->get('status'), $this->get('start'), $this->get('limit'),$this->get('newadd'));
        if ($members)
            $this->response($members, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Members!'), 404);
    }

    function coop_farmers_get() {
        $members = $this->mmember->readFarmers($this->get('key'), $this->get('cpg'), $this->get('start'), $this->get('limit'));
        if ($members)
            // array('data' => array(), 'total' => 0);
            $this->response($members, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Farmers!'), 404);
    }

    function coop_member_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $member = $this->mmember->readMember($this->get('id'));
        if ($member)
            $this->response(array('success' => true, 'data' => $member), 200);
        else
            $this->response(array('error' => 'Member could not be found'), 404);
    }

    function coop_member_balance_book_post() {
        if (!$this->post('id'))
            $this->response(NULL, 400);
        $member = $this->mmember->readBalanceMember($this->post('id'));
        if ($member)
            $this->response(array('success' => true, 'data' => $member), 200);
        else
            $this->response(array('error' => 'Member could not be found'), 404);
    }

    function coop_farmer_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $member = $this->mmember->readFarmer($this->get('id'));
        if ($member)
            $this->response(array('success' => true, 'data' => $member), 200);
        else
            $this->response(array('error' => 'Farmer could not be found'), 404);
    }

    function coop_member_post() {
        $member = $this->mmember->createMember($this->post());
        
        if ($member) {
            $this->response($member, 200);
        } else {
            $this->response(array('error' => 'Member could not be added'), 404);
        }
    }

    function coop_member_close_put()
    {
        $memberID = $this->put('memberID');
        if (!$memberID)
        {
            $this->response(NULL, 400);
        } else {
            $member = $this->mmember->closeMember($this->put());
            if ($member) {
                $this->response($member, 200);
            } else {
                $this->response(array('error' => 'Member could not be updated'), 404);
            }
        }
    }

    function coop_member_close_get()
    {
        $id = $this->get('id');
        if ($id==null)
        {
            $this->response(NULL, 400);
        } else {
            $member = $this->mmember->readCloseMember($id);
            if ($member) {
                $this->response($member, 200);
            } else {
                $this->response(array('error' => 'Member could not be found'), 404);
            }
        }
    }

    function coop_member_image_post() {

        $pic = false;

        if(isset($this->file['memberPhoto'])) { $pic = 'memberPhoto'; }
        if(isset($this->file['memberSignature'])) { $pic = 'memberSignature'; }

        if ($pic) {

            $gambar = date('Ymdhis') . '_' . $this->file[$pic]['name'];

            $config['upload_path'] = './images/coop/members/';
            $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
            $config['max_size']	= '1000';
            // $config['max_width']  = '1024';
            // $config['max_height']  = '768';
            $config['file_name']  = $gambar;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload($pic))
            {
                    $error = array('success'=>false,'error' => $this->upload->display_errors());

                    $this->response($error, 200);
            }
            else
            {
                    $data = $this->upload->data();

                    $result['success'] = true;
                    $result['data'] = 'images/coop/members/'.$data['file_name'];
                    $result['name'] = $data['file_name'];
                    $result['path'] = $data['full_path'];

                    $this->response($result, 200);
            }
        }
    }

    function coop_member_signature_post() {
        if ($this->file['memberSignature']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['memberSignature']['name'];
            $upload = move_upload($this->file, 'images/Photo/Member/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/Photo/Member/' . $this->post('signature'));
                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function update_coop_member_post() {
        $id = $this->post('id');
        if($id){
            $update = $this->mmember->updateMember($id,$this->post());
            if ($update){
                $this->response($update, 200); // 200 being the HTTP response code
            } else {
                $this->response(array('error' => 'Member could not be edited'), 404);
            }
        }
    }

    function coop_member_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->mmember->deleteMember($this->delete('id'));
        if ($delete)
            $this->response($delete, 200);
        else
            $this->response(array('error' => 'Member could not be deleted'), 404);
    }

    function savings_get() {
//        if (!$this->get('id'))
//            $this->response(NULL, 400);
        $saving = $this->mmember->getSavingByMember($this->get('id'), $this->get('start'), $this->get('limit'));
        if ($saving)
            $this->response($saving, 200);
        else
            $this->response(array('error' => 'Saving could not be found'), 404);
    }

    function coop_setstatus_saving_post()
    {
         $saving = $this->mmember->setStatusSavingMember($this->post('memberSavingID'),$this->post('status'));
        if ($saving)
            $this->response($saving, 200);
        else
            $this->response(array('error' => 'Saving could not be found'), 404);
    }

    function coop_member_saving_get()
    {
        $saving = $this->mmember->GetSavingMember($this->get('memberID'));
        if ($saving)
            $this->response($saving, 200);
        else
            $this->response(array('error' => 'Saving could not be found'), 404);
    }

    function save_member_saving_post()
    {
        $saving = $this->mmember->SavingMember($this->post('memberID'), $this->post('savingTypeID'));
        if ($saving)
            $this->response($saving, 200);
        else
            $this->response(array('error' => 'Saving could not be found'), 404);
    }

    function saving_post() {
        if (!$this->post('memberSavingID'))
            $this->response(NULL, 400);
        $saving = $this->mmember->createSaving($this->post('memberSavingID'), $this->post('savingTypeID'), $this->post('memberSavingNo'), $this->post('memberSavingRemark'), $_SESSION['userid']);

        if ($saving) {
            $this->response($saving, 200);
        } else {
            $this->response(array('error' => 'Saving could not be added'), 404);
        }
    }

    function saving_check_get() {
        if (!$this->get('memberID'))
            $this->response(NULL, 400);
        $saving = $this->mmember->checkSaving($this->get('memberID'));

        if ($saving) {
            $this->response($saving, 200);
        } else {
            $this->response(array('error' => 'Saving could not be added'), 404);
        }
    }

    function transaction_summary_get()
    {
        $id = $this->get('memberSavingID');
        if ($id) {
            $this->response(array('total'=>number_format($this->mmember->getSummaryTrans2($id)), 200));
        } else {
             $this->response(array('total'=>0), 200);
        }
    }

    function transaction_post() {
        $transaction = $this->mmember->createTransaction(
                $this->post('memberTransactionType'),
                $this->post('memberTransactionDate'),
                $this->post('cashSourceID'),
                $this->post('memberSavingID'),
                $this->post('memberTransactionAmount'),
                $this->post('memberTransactionRemark'),
                $_SESSION['userid']);

        if ($transaction) {
            $this->response($transaction, 200);
        } else {
            $this->response(array('error' => 'Transaction could not be added'), 404);
        }
    }

    function transaction_member_get() {
        $id = $this->get('id');
        if ($id) {
            $data = $this->mmember->getDetailMember($id);
            if ($data)
                $this->response($data, 200);
            else
                $this->response(array('error' => 'Couldn\'t find any Member!'), 404);
        }
    }

    function combomembertype_get() {
        $data = $this->mmember->getComboMemberType();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Member type!'), 404);
    }

	function combogroup_get() {
        $data = $this->mmember->getComboGroup($this->get('query'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Group!'), 404);
    }

    function combodistrict_get() {
        $data = $this->mmember->getComboDistrict();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any District!'), 404);
    }

    function combosubdistrict_get() {
        $data = $this->mmember->getComboSubdistrict($this->get('district'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Subdistrict!'), 404);
    }

    function combovillage_get() {
        $data = $this->mmember->getComboVillage($this->get('sub_district'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Village!'), 404);
    }

    function comboidentity_get() {
        $data = $this->mmember->getComboIdentity();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Village!'), 404);
    }

    function combostatus_get() {
        $data = $this->mmember->getComboStatus();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Village!'), 404);
    }

    function combo_transactiontype_get() {
        $data = $this->mmember->getComboTransactionType();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transaction!'), 404);
    }

    function cancel_image_post() {

        $photo = $this->post('photo');
        $sigi = $this->post('sigi');

        if(strlen($photo) && file_exists($photo)){
            unlink($photo);
        }

        if(strlen($photo) && file_exists($sigi)){
            unlink($sigi);
        }

        $this->response(array('success' => true),200);
    }

    function update_status_post() {
        if ($this->post('id')==NULL)
            $this->response(NULL, 400);
        $post = $this->mmember->updateStatus($this->post('id'),$this->post('status'));
        if ($post)
            $this->response($post, 200);
        else
            $this->response(array('error' => 'Member could not be updated'), 404);
    }

    function cetak_blank_member_get()
    {
        $data = null;
        $this->load->view('cetak_blank_member2', $data);
    }

    function cetak_member_get()
    {
        // echo base_url();
        $MemberID = $this->get('MemberID');
        $member = $this->mmember->readMember($MemberID);
        // print_r($member);
        if($member['farmerID']!=null)
        {
             $qCpg = $this->db->query("select GroupName
                from ktv_cpg a
                join ktv_farmer b ON a.CPGid = b.CPGid
                where b.FarmerID = ".$member['farmerID']."")->row();
             $member['GroupName'] = $qCpg->GroupName;
        } else {
            $member['GroupName'] = null;
        }


        $tgl = explode('-',$member['registeredDate']);
        $data = array('d'=>$member,'regdate'=>$tgl);
        $this->load->view('cetak_blank_member3', $data);
    }

    function coop_member_import_save_post()
    {
        $post = $this->mmember->saveMemberImport(json_decode($this->post('jsonData')));
        if ($post)
            $this->response($post, 200);
        else
            $this->response(array('error' => 'Member could not be updated'), 404);
    }

    function coop_member_import_tmp_post()
    {
        // $config['upload_path'] = $this->config->item('member_import_dir');
        $config['upload_path'] = '/var/www/html/cocoatrace2/api/uploads/member/';
        $config['allowed_types'] = 'xlsx';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('file'))
        {
                $this->response(array('success'=>false,'message' => $this->upload->display_errors()), 200);
        }
        else
        {
                $data = $this->upload->data();

                $name= $data['file_name'];
                $file = $data['full_path'];

                // $this->response($result, 200);
        }

        // $file = '/var/www/cocoatrace2/api/template-import-member.xlsx';

        $this->load->library('Excel', null, 'PHPExcel');
        $excel_data = $this->PHPExcel->import2($file, false);

        $filledBlank = '<font color=red>Cannot filled blank</font>';

        unset($excel_data[0]);
        $result = array();

        //start validasi
        $nums = 0;
        $data = array();
        $status = "";
        foreach ($excel_data as $key => $value) {
            $valid = true;

            $data[$nums]['farmerID'] = $value[1];
            $data[$nums]['name'] = $value[2];

            if(isset($value[3]))
            {
                if($value[3]=='')
                {
                    $data[$nums]['typeID'] = $filledBlank;
                    $valid = false;
                } else {
                    $q = $this->db->get_where('coop_member_type',array('typeCode'=>$value[3]));
                    if($q->num_rows()<=0)
                    {
                        $data[$nums]['typeID'] = '<font color=red>Invalid Member Type Code</font>';
                        $valid = false;
                    } else {
                        $data[$nums]['typeID'] = $value[3];
                    }
                }
            } else {
                $data[$nums]['typeID'] = $filledBlank;
                $valid = false;
            }

            $data[$nums]['identityNumber'] = $value[4];
            $data[$nums]['address'] = $value[5];

            if(isset($value[6]))
            {
                if($value[6]=='')
                {
                    $data[$nums]['gender'] = $filledBlank;
                    $valid = false;
                } else {
                    if($value[6]!=1 && $value[6]!=2)
                    {
                        $data[$nums]['gender'] = '<font color=red>Invalid Gender ID</font>';
                        $valid = false;
                    } else {
                        $data[$nums]['gender'] = $value[6];
                    }
                }
            } else {
                   $data[$nums]['gender'] =  $filledBlank;
                   $valid = false;
            }

            $data[$nums]['placeOfBirth'] = $value[7];
            $data[$nums]['dateOfBirth'] = $value[8];

            if(isset($value[9]))
            {
                if($value[9]=='')
                {
                     $data[$nums]['villageID'] = $filledBlank;
                     $valid = false;
                } else {
                    $q = $this->db->get_where('ktv_village',array('VillageID'=>$value[9]));
                    if($q->num_rows()<=0)
                    {
                        $data[$nums]['villageID'] = '<font color=red>Invalid Village ID</font>';
                        $valid = false;
                    } else {
                        $data[$nums]['villageID'] = $value[9];
                    }
                }
            } else {
                   $data[$nums]['villageID'] = $filledBlank;
                   $valid = false;
            }

            $data[$nums]['phone'] = $value[10];
            $data[$nums]['job'] = $value[11];

            if(isset($value[12]))
            {
                if($value[12]=='')
                {
                    $data[$nums]['maritalStatus'] = $filledBlank;
                    $valid = false;
                } else {
                    if($value[12]!=1 && $value[12]!=2)
                    {
                         $data[$nums]['maritalStatus'] = '<font color=red>Invalid Marital Status</font>';
                         $valid = false;
                    } else {
                        $data[$nums]['maritalStatus'] = $value[12];
                    }
                }
            } else {
                    $data[$nums]['maritalStatus'] = $filledBlank;
                    $valid = false;
            }
            $data[$nums]['valid'] = $valid;
            $nums++;

        }
        //end validasi

        $this->response(array('success'=>true,'data'=>$data,'total'=>$nums), 200);
    }

    function import_member_post()
    {
        $config['upload_path'] = $this->config->item('member_import_dir');
        $config['allowed_types'] = 'xlsx';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('filexlsx'))
        {
                $this->response(array('success'=>false,'message' => $this->upload->display_errors()), 200);
        }
        else
        {
                $data = $this->upload->data();

                $name= $data['file_name'];
                $file = $data['full_path'];

                // $this->response($result, 200);
        }

        // $file = '/var/www/cocoatrace2/api/template-import-member.xlsx';

        $this->load->library('Excel', null, 'PHPExcel');
        $excel_data = $this->PHPExcel->import2($file, false);

        unset($excel_data[0]);
        $result = array();
        $valid = true;
        //start validasi
        foreach ($excel_data as $key => $value) {
            if(isset($value[3]))
            {
                if($value[3]=='')
                {
                    $this->response(array('success'=>false,'message'=>'<b>Member Type ID</b> on row <b>'.$value[0].'</b> cannot filled blank'), 200);
                    $valid = false;
                } else {
                    $q = $this->db->get_where('coop_member_type',array('typeID'=>$value[3]));
                    $this->response(array('success'=>false,'message'=>'<b>Member Type ID</b> on row <b>'.$value[0].'</b> not valid'), 200);
                }
            } else {
                $this->response(array('success'=>false,'message'=>'<b>Member Type ID</b> on row <b>'.$value[0].'</b> cannot filled blank'), 200);
                $valid = false;
            }

            if(isset($value[6]))
            {
                if($value[6]=='')
                {
                    $this->response(array('success'=>false,'message'=>'<b>Gender ID</b> on row <b>'.$value[0].'</b> cannot filled blank'), 200);
                    $valid = false;
                } else {
                    if($value[6]!=1 && $value[6]!=2)
                    {
                         $this->response(array('success'=>false,'message'=>'<b>Gender ID</b> on row <b>'.$value[0].'</b> not valid'), 200);
                    }
                }
            } else {
                    $this->response(array('success'=>false,'message'=>'<b>Gender ID</b> on row <b>'.$value[0].'</b> cannot filled blank'), 200);
                    $valid = false;
            }

            if(isset($value[9]))
            {
                if($value[9]=='')
                {
                    $this->response(array('success'=>false,'message'=>'<b>Village ID</b> on row <b>'.$value[0].'</b> cannot filled blank'), 200);
                    $valid = false;
                } else {
                    $q = $this->db->get_where('ktv_village',array('VillageID'=>$value[9]));
                    $this->response(array('success'=>false,'message'=>'<b>Village ID</b> on row <b>'.$value[0].'</b> not valid'), 200);
                }
            } else {
                    $this->response(array('success'=>false,'message'=>'<b>Village ID</b> on row <b>'.$value[0].'</b> cannot filled blank'), 200);
                    $valid = false;
            }

            if(isset($value[12]))
            {
                if($value[12]=='')
                {
                    $this->response(array('success'=>false,'message'=>'<b>Marital Status ID</b> on row <b>'.$value[0].'</b> cannot filled blank'), 200);
                    $valid = false;
                } else {
                    if($value[12]!=1 && $value[12]!=2)
                    {
                         $this->response(array('success'=>false,'message'=>'<b>Marital Status ID</b> on row <b>'.$value[0].'</b> not valid'), 200);
                    }
                }
            } else {
                    $this->response(array('success'=>false,'message'=>'<b>Marital Status ID</b> on row <b>'.$value[0].'</b> cannot filled blank'), 200);
                    $valid = false;
            }
        }
        //end validasi

        if($valid)
        {
            $this->db->trans_begin();

            foreach ($excel_data as $key => $value) {
                $this->mmember->createMemberImport($value);
            }


            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback();
                    $this->response(array('success'=>false,'message'=>'Inserting Data Failed'), 200);
            }  else
                {
                        $this->db->trans_commit();
                        $this->response(array('success'=>true,'message'=>'Success Importing Data'), 200);
                }

                $this->response($value, 200);
        }

    }

    function savingmembers_get()
    {
        if ($this->get('id')==NULL)
            $this->response(NULL, 400);
        $post = $this->mmember->savingMemberData($this->get('id'));
        if ($post)
            $this->response($post, 200);
        else
            $this->response(array('error' => 'Not Found'), 200);
    }

    function loans_get()
    {
        if ($this->get('id')==NULL)
            $this->response(NULL, 400);
        $post = $this->mmember->loanMemberData($this->get('id'));
        if ($post)
            $this->response($post, 200);
        else
            $this->response(array('error' => 'Not Found'), 200);
    }

    function loan_summary_get()
    {
         if ($this->get('id')==NULL)
            $this->response(NULL, 400);
        $post = $this->mmember->loanMemberSummary($this->get('id'));
        if ($post)
            $this->response($post, 200);
        else
            $this->response(array('error' => 'Not Found'), 200);
    }

    function tmp_cam_post()
    {
        $img = $_POST['imgBase64'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $fileData = base64_decode($img);
        //saving
        $fileName = 'photo'.date('YmdH:i:s').'.png';
        file_put_contents('./images/coop/members/'.$fileName, $fileData);

        $this->response(array('photo'=>$fileName,'imgname'=>$fileName), 200);
    }

    function setup_saving_amount_post()
    {
        $post = $this->mmember->savingSavingSetup($this->post());
        if ($post)
            $this->response($post, 200);
        else
            $this->response(array('error' => 'An error occured. Please try again later'), 200);
    }
    
    public function migrasi_get($coop = false) {
        
        $this->mmember->migrasi_member($coop);
        
    }

}

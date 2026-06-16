<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Partner extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('partner/mprogram');
    }

    function programs_get() {
        $programs = $this->mprogram->readPrograms($this->get('start'),$this->get('limit'));
        if($programs) $this->response($programs, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function program_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $program = $this->mprogram->readProgram($this->get('id'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Program could not be found'), 404);
    }

    function program_image_post() {
        $this->load->library('awsfileupload');
        if($_POST['id'] != ''){
            // echo "<pre>";
            // print_r($_POST);
            // die;

            $upload = $this->awsfileupload->upload($this->file['Photo']['tmp_name'],$this->file['Photo']['name'], AWSS3_LOGO_PARTNER, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('Photo_old')) == true) {
                    $this->awsfileupload->delete($this->post('Photo_old'));
                }else{
                    delete_file($this->post('Photo_old'));
                }
                $prosesUpdate = $this->mprogram->updatePhoto($_POST["id"],$upload['filenamepath']);
                $result['success']  = true;
                $result['message']  = lang('File uploaded');
                $result['file']     = $upload['fileurl'];
                $result['filepath'] = $upload['filenamepath'];
                $this->response($result, 200);
            } else {
                $result['success'] = false;
                $result['message'] = lang('Upload to aws failed');
                $this->response($result, 400);
            }
        }else{
            if ($this->file['Photo']['name']!='') {
                $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
                $upload = move_upload($this->file, 'images/Photo/'.$gambar);
                if (isset($upload['upload_data'])) {
                    @unlink('images/Photo/'.$this->post('Photo_old'));
                    $result['success'] = true;
                    $result['file'] = $gambar;
                    $this->response($result, 200);
                }
            }
        }
    }

	function program_image_program_post() {
        $this->load->library('awsfileupload');
        if($_POST['id'] != ''){
            // echo "<pre>";
            // print_r($_POST);
            // die;

            $upload = $this->awsfileupload->upload($this->file['PhotoProgram']['tmp_name'],$this->file['PhotoProgram']['name'], AWSS3_LOGO_PARTNER, 'images');
            if ($upload['success'] == true) {
                if($this->awsfileupload->doesObjectExist($this->post('PhotoProgram_old')) == true) {
                    $this->awsfileupload->delete($this->post('PhotoProgram_old'));
                }else{
                    delete_file($this->post('PhotoProgram_old'));
                }
                $prosesUpdate = $this->mprogram->updatePhotoProgram($_POST["id"],$upload['filenamepath']);
                $result['success']  = true;
                $result['message']  = lang('File uploaded');
                $result['file']     = $upload['fileurl'];
                $result['filepath'] = $upload['filenamepath'];
                $this->response($result, 200);
            } else {
                $result['success'] = false;
                $result['message'] = lang('Upload to aws failed');
                $this->response($result, 400);
            }
        }else{            
            if ($this->file['PhotoProgram']['name']!='') {
                $gambarProgram = date('Ymdhis').'_'.$this->file['PhotoProgram']['name'];
                $upload = move_upload($this->file, 'images/Photo/'.$gambarProgram);
                if (isset($upload['upload_data'])) {
                    @unlink('images/Photo/'.$this->post('PhotoProgram_old'));
                    $result['success'] = true;
                    $result['file'] = $gambarProgram;
                    $this->response($result, 200);
                }
            }
        }
    }

    /**
     * Note: PartnerIndustry dikomen dulu karena ada perubahan pada saat print out farmer untuk mill
     * @return [type] [description]
     */
    function program_post() {
        if(!$this->post('PartnerName')) $this->response(NULL, 400);

        $program = $this->mprogram->createProgram(
                $this->post('PartnerName'),
                /*$this->post('PartnerIndustry'),*/
                $this->post('PartnerFullName'),
                $this->post('PartnerProgramName'),
                $this->post('cmbFlagAkses'),
                $this->post('districtItemselector'),
                $this->post('selectedCPG'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Program could not be found'), 404);
    }
    function programu_post() {
        if(!$this->post('id')) $this->response(NULL, 400);
            $program = $this->mprogram->updateProgram(
                $this->post('PartnerName'),
                // $this->post('PartnerIndustry'),
                $this->post('PartnerFullName'),
                $this->post('PartnerProgramName'),
                $this->post('cmbFlagAkses'),
                $this->post('cmbDistrict'),
                $this->post('id'), // district id
                $this->post('districtItemselector'),
                $this->post('selectedCPG')
                );
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Program could not be found'), 404);
    }

    function program_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $program = $this->mprogram->deleteProgram($this->delete('id'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Program could not be delete'), 404);
    }

    function data_district_get() {
        $program = $this->mprogram->readDistrict($this->get('id'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    function data_province_get() {
        $program = $this->mprogram->readProvince($this->get('id'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    function data_districtInPartner_get() {
        $program = $this->mprogram->readDistrictInPartner($this->get('id'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    function data_cpgInPartner_get() {
        //$program = $this->mprogram->readCpgInPartner($this->get('id'));
        $program = $this->mprogram->readCpgInPartner($this->get('id'), $this->get('DistrictID'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    function list_district_get(){
        $district = $this->mprogram->readDistrictAll();
        if($district) $this->response($district, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    /*
    function listdistrict_get(){
        $district = $this->mprogram->readListDistrict($this->get('id'));

        $parent = array();
        foreach($district as $key=>$val){
            $parent[] = array(
                'text' => $key,
                'children' => $val['children']
            );
        }
        if($district) $this->response($parent, 200);

    }
    */

    public function list_district_partner_member_get(){
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        $data = $this->mprogram->readListDistrictPartnerMember();
        $this->response($data, 200);
    }
    
    public function list_partner_get() {
        $data = $this->mprogram->readPartnerMember();
        $this->response($data, 200);        
    }
    
    function save_district_partner_member_post() {
        $result = $this->mprogram->insertDistrictPartnerMember($this->post('districtid'),$this->post('partner'));
        $this->response($result, 200);        
    }
    
    function change_district_partner_member_post() {
        $result = $this->mprogram->updateDistrictPartnerMember($this->post('districtid'),$this->post('partner'));
        $this->response($result, 200);        
    }
    
    function district_partner_member_delete() {
        if (!$this->delete('districtid') && !$this->delete('partnerid'))
            $this->response(NULL, 400);
        $partner = $this->mprogram->deleteDistrictPartner($this->delete('districtid'), $this->delete('partnerid'));
        if ($partner)
            $this->response($partner, 200);
        else
            $this->response(array('error' => 'Program could not be delete'), 404);
    }

    function districtPartner_get(){
        $district = $this->mprogram->readSelectedDistrict($this->get('id'));

        $parent = array();
        foreach($district as $key=>$val){
            $parent[] = array(
                'text' => $key,
                'children' => $val['children']
            );
        }
        if($district) $this->response($parent, 200);
    }

    function districtPartners_get(){
        $district = $this->mprogram->readDistrictPartner($this->get('id'));

        $parent = array();
        foreach($district as $key=>$val){
            $parent[] = array(
                'text' => $key,
                'cls'=> 'folder',
                'expanded'=> $val['expand'],//false,
                'children' => $val['children']
            );
        }
        if($district) $this->response($parent, 200);
    }

    function programudist_put() {
        if(!$this->put('PartnerID')) $this->response(NULL, 400);
        $program = $this->mprogram->updateProgramDist($this->put('PartnerID'),$this->put('DistrictID'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function programaddDistrict_put() {
        if(!$this->put('PartnerID')) $this->response(NULL, 400);
        $program = $this->mprogram->addPartnerDistrict($this->put('PartnerID'),$this->put('DistrictID'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function list_cpg_get(){
         //$stat = ($this->get('stat') != 'null') ? $this->get('stat') : '';
        if($this->get('id') == 'empty'){
            $data = array();
            $this->response($data, 200);
        }else{
            // $program = $this->mprogram->readCpgList(
            //      $this->get('id'),
            //      $this->get('idPartner'),
            //      $this->get('stat'),
            //      $this->get('DistrictID')
            //      );
            $DistrictID = $this->get('DistrictID');
            if (empty($DistrictID)) {
                $this->response(null, 200);
            }
            $program = $this->mprogram->getCpg($DistrictID);
            if($program) $this->response($program, 200);
            else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    function district_find_get(){
        $program = $this->mprogram->findDistrict($this->get('id'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    public function district_access_get(){
        $data = $this->mprogram->getDistrictAccessCombo($this->get('PartnerID'));
        $this->response($data, 200);
    }

    public function cpg_access_get(){
        $data = $this->mprogram->getCpgAccessCombo($this->get('DistrictID'));
        $this->response($data, 200);
    }

    public function cpg_access_selected_post(){
        $data = $this->mprogram->getCpgAccessSelected($this->post('PartnerID'),$this->post('DistrictID'));
        if($data['result'])
            $this->response($data, 200);
        else{
            $this->response('Process Failed', 400);
        }
    }

    public function assign_cpg_update_post(){
        $proses = $this->mprogram->updateAssignCpg($this->post());
        if ($proses) {
            $this->response($proses, 200);
        } else {
            $this->response(array('error' => 'Process failed'), 404);
        }
    }

    public function program_ldap_detail_get(){
        $data = $this->mprogram->getLDAPdetail($this->get('id'));
        $this->response($data, 200);
    }

    public function program_ldap_post(){
        $data = $this->mprogram->updateLDAP($this->post('id'), $this->post('ad_host'), $this->post('ad_port'), $this->post('ad_basedn'), $this->post('ad_domain'), $this->post('ad_auth'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

}
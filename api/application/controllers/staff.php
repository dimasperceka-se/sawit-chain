<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('staff/mprogram');
        $this->load->model('staff/mextension');
        $this->load->model('staff/mprivatestaff');
    }

    function programs_get() {
        $programs = $this->mprogram->readPrograms($this->get('key'),$this->get('start'),$this->get('limit'));
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
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/Photo/'.$gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/Photo/'.$this->post('Photo_old'));
                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function program_post() {
        if(!$this->post('PersonNm')) $this->response(NULL, 400);
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            move_upload($this->file, 'images/Photo/'.$gambar);
        } else $gambar = $this->post('Photo_old');
        $program = $this->mprogram->createProgram($this->post('Ssn'),$this->post('ExtId'),$this->post('PersonNm'),
            $this->post('ParentNm'),$this->post('BirthDttm'),$this->post('BirthPlace'),$gambar,
            $this->post('MetaphoneNm'),$this->post('Gender'),$this->post('Address'),$this->post('Desa'),
            $this->post('ZipCd'),$this->post('Latitude'),$this->post('Email'),$this->post('ReligionCd'),
            $this->post('BloodT'),$this->post('MaritalSt'),$this->post('Education'),$this->post('Jobclass'),
            $this->post('JobAddr'),$this->post('NationalityNm'),$this->post('RaceNm'),$this->post('StatusCd'),
            $this->post('Longitude'),$this->post('Handphone'),
            $this->post('PersonNm'),$this->post('UserName'),$this->post('UserPassword'),$this->post('UserActive'),
            $this->post('UserGroupGroupId'),    $this->post('PartnerId'),$this->post('Status'),$this->post('Position'),
            $this->post('DistrictId'),
            $this->post('PrivatePhone'),$this->post('OfficialPhone'),$this->post('PrivateEmail'),$this->post('OfficialEmail'),
            $this->post('nip'),$_SESSION['userid']);
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Program could not be found'), 404);
    }

    function programu_post() {
        if(!$this->post('id')) $this->response(NULL, 400);
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            move_upload($this->file, 'images/Photo/'.$gambar);
        } else $gambar = $this->post('Photo_old');
        $program = $this->mprogram->updateProgram($this->post('Ssn'),$this->post('ExtId'),$this->post('PersonNm'),
            $this->post('ParentNm'),$this->post('BirthDttm'),$this->post('BirthPlace'),$gambar,
            $this->post('MetaphoneNm'),$this->post('Gender'),$this->post('Address'),$this->post('Desa'),
            $this->post('ZipCd'),$this->post('Latitude'),$this->post('Email'),$this->post('ReligionCd'),$this->post('BloodT'),
            $this->post('MaritalSt'),$this->post('Education'),$this->post('Jobclass'),$this->post('JobAddr'),
            $this->post('NationalityNm'),$this->post('RaceNm'),$this->post('StatusCd'),$this->post('ModifiedDttm'),
            $this->post('CreatedBy'),$this->post('ModifiedBy'),$this->post('Longitude'),$this->post('Handphone'),
            $this->post('PersonNm'),$this->post('UserName'),$this->post('UserPassword'),$this->post('UserActive'),
            $this->post('UserGroupGroupId'),    $this->post('PartnerId'),$this->post('Status'),$this->post('Position'),
            $this->post('DistrictId'),$this->post('PrivatePhone'),$this->post('OfficialPhone'),$this->post('PrivateEmail'),
            $this->post('OfficialEmail'),
            $this->post('nip'),$this->post('id'),$this->post('userid'),$_SESSION['userid']);
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Program could not be found'), 404);
    }

    function program_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $program = $this->mprogram->deleteProgram($this->delete('id'));
        if($program) $this->response($program, 200);
        else $this->response(array('error' => 'Program could not be delete'), 404);
    }

    function partnerlist_get() {
        $data = $this->mprogram->readPartnerlist();
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any RegionalCds!'), 404);
    }


    function extensions_get() {
        $extensions = $this->mextension->readExtensions($this->get('key'),$this->get('start'),$this->get('limit'));
        if($extensions) $this->response($extensions, 200);
        else $this->response(array('error' => 'Couldn\'t find any extensions!'), 404);
    }

    function extension_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $extension = $this->mextension->readExtension($this->get('id'));
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Extension could not be found'), 404);
    }

    function extension_image_post() {
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/Photo/'.$gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/Photo/'.$this->post('Photo_old'));
                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function extension_post() {
        if(!$this->post('PersonNm')) $this->response(NULL, 400);
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            move_upload($this->file, 'images/Photo/'.$gambar);
        } else $gambar = $this->post('Photo_old');
        $extension = $this->mextension->createExtension($this->post('KTP'),$this->post('ExtId'),$this->post('PersonNm'),
            $this->post('ParentNm'),$this->post('BirthDttm'),$this->post('BirthPlace'),$gambar,
            $this->post('MetaphoneNm'),$this->post('Gender'),$this->post('Address'),$this->post('Desa'),
            $this->post('ZipCd'),$this->post('Latitude'),$this->post('email'),$this->post('ReligionCd'),$this->post('BloodT'),
            $this->post('MaritalSt'),$this->post('Education'),$this->post('Jobclass'),$this->post('JobAddr'),
            $this->post('NationalityNm'),$this->post('RaceNm'),$this->post('StatusCd'),$this->post('Longitude'),
            $this->post('Handphone'),$this->post('KTP'),$this->post('GovInstitute'),$this->post('StaffPosition'),
            $_SESSION['userid']);
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Extension could not be found'), 404);
    }

    function extensionu_post() {
        if(!$this->post('id')) $this->response(NULL, 400);
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            move_upload($this->file, 'images/Photo/'.$gambar);
        } else $gambar = $this->post('Photo_old');
        $extension = $this->mextension->updateExtension($this->post('KTP'),$this->post('ExtId'),$this->post('PersonNm'),
            $this->post('ParentNm'),$this->post('BirthDttm'),$this->post('BirthPlace'),$gambar,
            $this->post('MetaphoneNm'),$this->post('Gender'),$this->post('Address'),$this->post('Desa'),
            $this->post('ZipCd'),$this->post('Latitude'),$this->post('email'),$this->post('ReligionCd'),$this->post('BloodT'),
            $this->post('MaritalSt'),$this->post('Education'),$this->post('Jobclass'),$this->post('JobAddr'),
            $this->post('NationalityNm'),$this->post('RaceNm'),$this->post('StatusCd'),$this->post('Longitude'),
            $this->post('Handphone'),$this->post('KTP'),
            $this->post('GovInstitute'),$this->post('StaffPosition'),$this->post('id'),$_SESSION['userid']);
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Extension could not be found'), 404);
    }

    function extension_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $extension = $this->mextension->deleteExtension($this->delete('id'));
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Extension could not be delete'), 404);
    }

    function InstitutionIDs_get() {
        $InstitutionIDs = $this->mextension->readInstitutionIDs();
        if($InstitutionIDs) $this->response($InstitutionIDs, 200);
        else $this->response(array('error' => 'Couldn\'t find any InstitutionIDs!'), 404);
    }

    function PositionIDs_get() {
        $InstitutionIDs = $this->mextension->readPositionIDs();
        if($InstitutionIDs) $this->response($InstitutionIDs, 200);
        else $this->response(array('error' => 'Couldn\'t find any InstitutionIDs!'), 404);
    }

    function data_districtInStaff_get() {
        $InstitutionIDs = $this->mextension->readDistrictInStaff($this->get('id'));
        if($InstitutionIDs) $this->response($InstitutionIDs, 200);
        else $this->response(array('error' => 'Couldn\'t find any InstitutionIDs!'), 404);
    }

    function programaddDistrict_put() {
        if(!$this->put('StaffID')) $this->response(NULL, 400);
        $extension = $this->mextension->AddDistrict($this->put('StaffID'),$this->put('DistrictID'));
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Couldn\'t find any InstitutionIDs'), 404);
    }

    function programdeldist_put() {
        if(!$this->put('StaffID')) $this->response(NULL, 400);
        $extension = $this->mextension->DeleteDistrict($this->put('StaffID'),$this->put('DistrictID'));
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function program_districts_get() {
        $extension = $this->mextension->districts(100);
        if($extension) $this->response($extension, 200);
        else $this->response(array('error' => 'Couldn\'t find any districts!'), 404);
    }

    function privatestaffs_get() {
        $privatestaffs = $this->mprivatestaff->readPrivatestaffs($this->get('key'),$this->get('start'),$this->get('limit'));
        if($privatestaffs) $this->response($privatestaffs, 200);
        else $this->response(array('error' => 'Couldn\'t find any private staffs!'), 404);
    }

    function privatestaff_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $privatestaff = $this->mprivatestaff->readPrivatestaff($this->get('id'));
        if($privatestaff) $this->response($privatestaff, 200);
        else $this->response(array('error' => 'Private staff could not be found'), 404);
    }

    function privatestaff_image_post() {
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            $upload =move_upload($this->file, 'images/Photo/'.$gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/Photo/'.$this->post('Photo_old'));
                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function privatestaff_post() {
        if(!$this->post('PersonNm')) $this->response(NULL, 400);
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            move_upload($this->file, 'images/Photo/'.$gambar);
        } else $gambar = $this->post('Photo_old');
        $privatestaff = $this->mprivatestaff->createPrivatestaff($this->post('ExtId'),$this->post('PartnerId'),
            $this->post('PersonNm'),$this->post('BirthDttm'),$gambar, $this->post('userid'),
            $this->post('PrivatePhone'),$this->post('OfficialPhone'),$this->post('PrivateE-mail'), $this->post('OfficialE-mail'),
            $this->post('Gender'), $this->post('PersonNm'), $this->post('UserName'),$this->post('UserPassword'),
            $this->post('UserActive'), $this->post('UserGroupGroupId'),$_SESSION['userid'],$this->post('DistrictId'));
        if($privatestaff) $this->response($privatestaff, 200);
        else $this->response(array('error' => 'Private staff could not be found'), 404);
    }

    function privatestaffu_post() {
        if(!$this->post('id')) $this->response(NULL, 400);
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            move_upload($this->file, 'images/Photo/'.$gambar);
        } else $gambar = $this->post('Photo_old');
        $privatestaff = $this->mprivatestaff->updatePrivatestaff($this->post('ExtId'),$this->post('PartnerId'),
            $this->post('PersonNm'),$this->post('BirthDttm'),$gambar, $this->post('userid'),
            $this->post('PrivatePhone'),$this->post('OfficialPhone'),$this->post('PrivateE-mail'), $this->post('OfficialE-mail'),
            $this->post('Gender'), $this->post('PersonNm'), $this->post('UserName'),$this->post('UserPassword'),
            $this->post('UserActive'), $this->post('UserGroupGroupId'), $this->post('id'),$_SESSION['userid'],
            $this->post('DistrictId'));
        if($privatestaff) $this->response($privatestaff, 200);
        else $this->response(array('error' => 'Private staff could not be found'), 404);
    }

    function privatestaff_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $privatestaff = $this->mprivatestaff->deletePrivatestaff($this->delete('id'));
        if($privatestaff) $this->response($privatestaff, 200);
        else $this->response(array('error' => 'Privatestaff could not be delete'), 404);
    }

    function cetak_extension_get($id='') {
        $data['data'] = $this->mextension->readExtension($id);
        $this->load->view('extension_cetak_blank', $data);
    }

}

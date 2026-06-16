<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Basic extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('basic/mtraining');
        $this->load->model('basic/mbatch');
        $this->load->model('basic/msurvey');
        //****//
        $this->load->model('basic/msourcefund');
        //****//
        $this->load->model('basic/mserviceprovider');
        $this->load->model('basic/mbusinesssector');
        $this->load->model('basic/mcooperativetraining');
    }

    function cpg_trainings_get() {
        $data = $this->mtraining->readTrainings($this->get('start'),$this->get('limit'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function cpg_training_get() {
        $data = $this->mtraining->readTraining($this->get('CPGTrainingsID'));
        if($data) $this->response(array('success' => true, 'data' => $data), 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function cpg_training_parent_get() {
        $root = array(array('id' => '0', 'label' => '.'));
        $data = $this->mtraining->getTrainingParent();
        if($data) $this->response(array_merge($root, $data), 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function cpg_training_post() {
        // if(!$this->post('name')) $this->response(NULL, 400);
        $data = $this->mtraining->createTraining($this->post('ParentID'),$this->post('CpgTrainings'),$this->post('CpgAbbre'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function cpg_training_put() {
        if(!$this->put('CpgTrainingsID')) $this->response(NULL, 400);
        $data = $this->mtraining->updateTraining($this->put('ParentID'),$this->put('CpgTrainings'),$this->put('CpgAbbre'),$this->put('CpgTrainingsID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function cpg_training_delete() {
        if(!$this->delete('CpgTrainingsID')) $this->response(NULL, 400);
        $children = $this->mtraining->getTrainingTree($this->delete('CpgTrainingsID'));
        if (!empty($children)) {
            $this->response(array('success' => false,'msg' => "Can not delete parent with child data."), 400);
        } 
        $data = $this->mtraining->deleteTraining($this->delete('CpgTrainingsID'));
        if($data) $this->response($data, 200);
        else $this->response(array('success' => false,'msg' => 'Data could not be deleted'), 400);
    }

    function cpg_batchs_get() {
        $data = $this->mbatch->readBatchs($this->get('start'),$this->get('limit'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function cpg_batch_post() {
        if(!$this->post('partner')) $this->response(NULL, 400);
        $data = $this->mbatch->createBatch($this->post('number'),$this->post('partner'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function cpg_batch_put() {
        if(!$this->put('id')) $this->response(NULL, 400);
        $partner_id = is_numeric($this->put('partner'))?$this->put('partner'):$this->put('partner_id');
        $data = $this->mbatch->updateBatch($this->put('number'),$partner_id,$this->put('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function cpg_batch_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mbatch->deleteBatch($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

    function cpgs_get() {
        $data = $this->mbatch->readCpgs();
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function partners_get() {
        $data = $this->mbatch->readPartners();
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function surveys_get() {
        $data = $this->msurvey->readSurveys($this->get('start'),$this->get('limit'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function survey_post() {
        if(!$this->post('name')) $this->response(NULL, 400);
        $data = $this->msurvey->createSurvey($this->post('nr'),$this->post('name'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function survey_put() {
        if(!$this->put('id')) $this->response(NULL, 400);
        $data = $this->msurvey->updateSurvey($this->put('nr'),$this->put('name'),$this->put('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function survey_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->msurvey->deleteSurvey($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }

    //****//
    function source_funds_get() {
        $data = $this->msourcefund->readSourceFunds($this->get('start'),$this->get('limit'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function coas_get() {
        $data = $this->msourcefund->readCoas();
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function source_fund_post() {
        if(!$this->post('name')) $this->response(NULL, 400);
        $data = $this->msourcefund->createSourceFund($this->post('name'),$this->post('no'),$this->post('codeName'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function source_fund_put() {
        //echo "here";
        //echo $this->put('id');
        if($this->put('id')=="") $this->response(NULL, 400);
        $data = $this->msourcefund->updateSourceFund($this->put('id'),$this->put('name'),$this->put('no'),$this->put('codeName'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be found'), 404);
    }

    function source_fund_delete() {
        if($this->delete('id')=="") $this->response(NULL, 400);
        $data = $this->msourcefund->deleteSourceFund($this->delete('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Data could not be delete'), 404);
    }
    //****//

    public function service_providers_get()
    {
        $data = $this->mserviceprovider->getData($this->get('start'),$this->get('limit'));
        $this->response($data, 200);
    }
    public function service_provider_get()
    {
        $data = $this->mserviceprovider->getDetail($this->get('id'));
        $this->response($data, 200);
    }
    public function service_provider_post()
    {
        if ($this->post('id')) {
            $result = $this->mserviceprovider->updateData($this->post('ServiceProvName'),$this->post('OfficialName'),$this->post('Abbreviation'),$this->post('BsnSectorID'),$this->post('Address'),$this->post('DistrictID'),$this->post('OfficialPhone'),$this->post('OfficialEmail'),$this->post('StatusCode'),$this->post('Remarks'),$this->post('id'));
            $id = $this->post('id');
        } else {
            $result = $this->mserviceprovider->insertData($this->post('ServiceProvName'),$this->post('OfficialName'),$this->post('Abbreviation'),$this->post('BsnSectorID'),$this->post('Address'),$this->post('DistrictID'),$this->post('OfficialPhone'),$this->post('OfficialEmail'),$this->post('StatusCode'),$this->post('Remarks'));
            if ($result !== false) {
                $id = $result;
                $result = true;
            }
        }
        if ($result && !empty($_FILES)) {
            $upload_path = './images/service_provider/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '1024';
            $config['max_width']  = '1024';
            $config['max_height']  = '768';

            $this->load->library('upload');

            $config['upload_path'] = $upload_path.'photo/';
            $config['file_name'] = $id.'_'.date('Y-m-d H:i:s');
            $this->upload->initialize($config);
            if (!empty($_FILES['Photo']['tmp_name'])) {
                if ( ! $this->upload->do_upload('Photo')){
                    $error['Photo'] = $this->upload->display_errors();
                } else {
                    $upload_data = $this->upload->data();
                    $this->mserviceprovider->updatePhoto($upload_data['file_name'], $id); 
                }
            }
            $this->upload->error_msg = array();
            $config['upload_path'] = $upload_path.'logo/';
            $config['file_name'] = $id.'_'.date('Y-m-d H:i:s');
            $this->upload->initialize($config);
            if (!empty($_FILES['Logo']['tmp_name'])) {
                if ( ! $this->upload->do_upload('Logo')){
                    $error['Logo'] = $this->upload->display_errors();
                } else {
                    $upload_data = $this->upload->data();
                    $this->mserviceprovider->updateLogo($upload_data['file_name'], $id); 
                }
            }
        }
        $this->response(array(
            'success' => $result,
            'errors' => $error
            ), 200);
        // $this->response($result, 200);
    }
    public function service_provider_put()
    {
        $result = $this->mserviceprovider->updateData($this->put('ServiceProvName'),$this->put('OfficialName'),$this->put('Abbreviation'),$this->put('BsnSectorID'),$this->put('Address'),$this->put('DistrictID'),$this->put('OfficialPhone'),$this->put('OfficialEmail'),$this->put('Photo'),$this->put('Logo'),$this->put('StatusCode'),$this->put('Remarks'),$this->put('id'));
        $this->response($result, 200);
    }
    public function service_provider_delete()
    {
        $result = $this->mserviceprovider->deleteData($this->delete('id'));
        $this->response(true, 200);
    }
    public function service_provider_list_get()
    {
        $data = $this->mserviceprovider->listServiceProvider();
        $this->response($data, 200);
    }

    public function business_sectors_get()
    {
        $data = $this->mbusinesssector->getData($this->get('start'),$this->get('limit'));
        $this->response($data, 200);
    }
    public function business_sector_get()
    {
        $data = $this->mbusinesssector->getDetail($this->get('id'));
        $this->response($data, 200);
    }
    public function business_sector_post()
    {
        $result = $this->mbusinesssector->insertData($this->post('BsnSectorName'),$this->post('StatusCode'),$this->post('Remarks'));
        $this->response($result, 200);
    }
    public function business_sector_put()
    {
        $result = $this->mbusinesssector->updateData($this->put('BsnSectorName'),$this->put('StatusCode'),$this->put('Remarks'),$this->put('id'));
        $this->response($result, 200);
    }
    public function business_sector_delete()
    {
        $result = $this->mbusinesssector->deleteData($this->delete('id'));
        $this->response(array(
            'success' => $result,
            'message' => '',
            ), 200);
    }
    public function business_sector_list_get()
    {
        $data = $this->mbusinesssector->listSector();
        $this->response($data, 200);
    }

    public function cooperative_trainings_get()
    {
        $data = $this->mcooperativetraining->getData($this->get('start'),$this->get('limit'));
        $this->response($data, 200);
    }
    public function cooperative_training_get()
    {
        $data = $this->mcooperativetraining->getDetail($this->get('id'));
        $this->response($data, 200);
    }
    public function cooperative_training_post()
    {
        $result = $this->mcooperativetraining->insertData($this->post('CoopTrainingName'),$this->post('AltName'),$this->post('Abbreviation'),$this->post('StatusCode'),$this->post('Remarks'));
        $this->response($result, 200);
    }
    public function cooperative_training_put()
    {
        $result = $this->mcooperativetraining->updateData($this->put('CoopTrainingName'),$this->put('AltName'),$this->put('Abbreviation'),$this->put('StatusCode'),$this->put('Remarks'),$this->put('id'));
        $this->response($result, 200);
    }
    public function cooperative_training_delete()
    {
        $result = $this->mcooperativetraining->deleteData($this->delete('id'));
        $this->response(array(
            'success' => $result,
            'message' => '',
            ), 200);
    }
    public function cooperative_training_list_get()
    {
        $data = $this->mcooperativetraining->listSector();
        $this->response($data, 200);
    }

    public function kmls_get()
    {
        $this->load->model('basic/mkml');
        $data = $this->mkml->getKmls($this->get('ProvinceID'), $this->get('CategoryID'), $this->get('key'), $this->get('start'), $this->get('limit'));
        $this->response($data, 200);
    }

    public function kml_get()
    {
        $this->load->model('basic/mkml');
        $data = $this->mkml->getKml($this->get('ID'));
        $this->response(['success' => true, 'data' => $data], 200);
    }

    public function kml_post()
    {
        $this->load->model('basic/mkml');
        $post = $this->post(null);
        $config['upload_path']   = './files/kml';
        $config['allowed_types'] = '*';
        $config['max_size']      = ini_get('upload_max_filesize')*1024;
        // echo "<pre>"; print_r($_FILES); echo "</pre>";exit;
        if($post["CategoryID"] != '8'){
            if(!$post["ProvinceID"] OR $post["ProvinceID"] == ''){
                $this->response(array("success"=>false,"msg"=>lang("Province can't be empty")), 200);
                return;
            }
        }

        if (!isset($_FILES)) {
            $this->response(array('success' => false, 'msg' => lang('Invalid file, maximum file size is '.$config['max_size'].'Kb')), 200);
        } else {
            if (empty($_FILES['kml']['tmp_name'])) {
                $status = $this->mkml->updateKML($post);
                $this->response($status, 200);
            } else {
                $ext = pathinfo($_FILES['kml']['name'], PATHINFO_EXTENSION);

                if ($ext !== 'kml') {
                    $this->response(array('success' => false, 'msg' => lang('Invalid file type: '.$ext)), 200);
                }

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('kml')) {
                    $this->response(array('success' => false, 'msg' => $this->upload->display_errors()), 200);
                } else {
                    $data = $this->upload->data();
                    $status = $this->mkml->uploadKML($post, $data);
                    $this->response($status, 200);
                }

            }
        }
    }

    public function kml_delete()
    {
        $return['success'] = true;
        $return['msg'] = '';
        $this->load->model('basic/mkml');
        $ID = $this->delete('ID');
        $detail = $this->mkml->getKml($ID);
        $result = $this->mkml->deleteKml($ID);
        if ($result !== false) {
            delete_file($detail['FilePath']);
        } else {
            $return['success'] = false;
            $return['msg'] = "Can not delete data";
        }
        $this->response($return, 200);
    }

    public function kml_category_get()
    {
        $this->load->model('basic/mkml');
        $data = $this->mkml->getCategory();
        $this->response($data, 200);
    }
}

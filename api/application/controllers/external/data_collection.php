<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_collection extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('external/mexternal');
    }

    private function checkPartnerID($apiPartner) {
        $resultFalse['success'] = false;
        $resultFalse['message'] = lang('No access on this API');

        switch($apiPartner) {
            case 'sta':
                if($_SESSION['PartnerID'] == '159') {
                    return true;
                } else {
                    $this->response($resultFalse,400);
                }
            break;
            default:
                $this->response($resultFalse,400);
            break;
        }
    }

    public function sta_basic_farmer_get(){
        ini_set('memory_limit', '-1');
        $this->checkPartnerID('sta');
        $data = $this->mexternal->basic_farmer();
        $this->response($data,200);
    }

    public function sta_labour_get(){
        ini_set('memory_limit', '-1');
        $this->checkPartnerID('sta');
        $data = $this->mexternal->labour();
        $this->response($data,200);
    }

    public function sta_household_get(){
        ini_set('memory_limit', '-1');
        $this->checkPartnerID('sta');
        $data = $this->mexternal->household();
        $this->response($data,200);
    }

    public function sta_garden_join_get(){
        ini_set('memory_limit', '-1');
        $this->checkPartnerID('sta');
        $data = $this->mexternal->garden_join();
        $this->response($data,200);
    }

    public function sta_sme_get(){
        ini_set('memory_limit', '-1');
        $this->checkPartnerID('sta');
        $data = $this->mexternal->sme();
        $this->response($data,200);
    }

    public function sta_sme_garden_get(){
        ini_set('memory_limit', '-1');
        $this->checkPartnerID('sta');
        $data = $this->mexternal->sme_garden();
        $this->response($data,200);
    }
}
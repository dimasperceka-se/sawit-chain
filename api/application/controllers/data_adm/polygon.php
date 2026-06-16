<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Polygon extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('data_adm/mpolygon');
        $this->load->model('muserprofile');
    }

    public function farmer_polygon_get()
    {
        $ProvinceID     = $this->get('ProvinceID');
        $DistrictID     = $this->get('DistrictID');
        $SubDistrictID  = $this->get('SubDistrictID');
        $Keyword        = $this->get('Keyword');
        $this->response($this->mpolygon->getFarmerPolygon($this->muserprofile->getUserProfile(), $ProvinceID, $DistrictID, $SubDistrictID, $Keyword), 200);
    }

    public function farmer_polygon_post()
    {
        // echo '<pre>'; print_r($this->post(null)); echo '</pre>'; 
        $MemberID       = $this->post('MemberID');
        $PlotNr       = $this->post('PlotNr');
        $SurveyNr       = $this->post('SurveyNr');
        $Last_revision  = $this->post('Revision');
        $area           = $this->post('area');
        if ($this->mpolygon->updateFarmerPolygon($MemberID,$PlotNr,$SurveyNr,$Last_revision,$area)) {
            $return = $Last_revision + 1;
        } else {
            $return = $Last_revision;
        }
        $this->response($return, 200);
    }

    public function garden_detail_get()
    {
        $MemberID  = $this->get('MemberID');
        $PlotNr  = $this->get('PlotNr');
        $SurveyNr  = $this->get('SurveyNr');
        $Revision  = $this->get('Revision');
        $this->response($this->mpolygon->getGardenDetail($MemberID, $PlotNr, $SurveyNr, $Revision), 200);
    }

    public function province_list_get()
    {
        // $this->load->model('muserprofile');
        $data = $this->mpolygon->listProvince($this->muserprofile->getUserProfile());
        $this->response($data, 200);
    }

    public function district_list_get()
    {
        // $this->load->model('muserprofile');
        $data = $this->mpolygon->listDistrict($this->get('ProvinceID'), $this->muserprofile->getUserProfile());
        $this->response($data, 200);
    }

    public function subdistrict_list_get()
    {
        // $this->load->model('muserprofile');
        $data = $this->mpolygon->listSubDistrict($this->get('DistrictID'), $this->muserprofile->getUserProfile());
        $this->response($data, 200);
    }

}

/* End of file polygon.php */
/* Location: ./application/controllers/polygon.php */
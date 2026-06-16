<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf Sutana
 */
class m_farmer extends CI_Model {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
    
    public function get_data_farmer($PartnerID, $SupplychainID){
        $return = array('data' => array(), 'total' => 0);

        $this->db->select(' b.MemberID, b.MemberDisplayID, b.MemberName, b.Nin, b.DateOfBirth, b.Gender, b.Address, b.Handphone, b.Photo, b.GapoktanID, g.GapoktanName, b.FarmerGroupID, h.GroupName, e.ProvinceID, f.Province, e.DistrictID, e.District, d.SubDistrictID, d.SubDistrict, b.VillageID, c.Village ');
        $this->db->from('ktv_access_partner_member a');
        $this->db->join('ktv_members b', 'a.apmMemberID=b.MemberID');
        $this->db->join('ktv_village c', 'b.VillageID=c.VillageID', 'left');
        $this->db->join('ktv_subdistrict d', 'c.SubDistrictID=d.SubDistrictID', 'left');
        $this->db->join('ktv_district e', 'e.DistrictID=d.DistrictID', 'left');
        $this->db->join('ktv_province f', 'f.ProvinceID=e.ProvinceID', 'left');
        $this->db->join('ktv_gapoktan g', 'b.GapoktanID=g.GapoktanID', 'left');
        $this->db->join('ktv_farmer_group h', 'b.FarmerGroupID=h.FarmerGroupID', 'left');
        $this->db->join('ktv_survey_plot i', 'b.MemberID=i.MemberID');
        $this->db->where('a.apmPartnerID', $PartnerID);
        $this->db->where('b.StatusCode', 'active');
        $this->db->group_by('b.MemberID');

        $Q = $this->db->get() ;

        if($Q->num_rows() > 0){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val->Photo = is_null($val->Photo) ? "" : 'api/images/member/'.$val->ProvinceID.'/'.$val->Photo;
                $val = $this->check_isNull($val);
                $val->plantation = array();

                $getPlan = $this->db->select(' a.PlotNr as PlantationNr, 
                                               a.SurveyNr, 
                                               a.VillageID, 
                                               a.Latitude, 
                                               a.Longitude, 
                                               c.Village ')
                ->from('ktv_survey_plot a')
                //->join('ref_tc_farming_type b', 'a.FarmingType=b.FarmingTypeID', 'left')
                ->join('ktv_village c', 'a.VillageID=c.VillageID', 'left')
                ->where('a.StatusCode', 'active')
                ->where('a.MemberID', $val->MemberID)
                ->get();

                if($getPlan->num_rows()){
                    $d_plan = $getPlan->result();
                    foreach($d_plan as $k => $v){
                        $v = $this->check_isNull($v);

                    }
                    $val->plantation = $d_plan;
                }
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        return $data;
    }
    private function check_isNull($v){
        foreach($v as $key => $value){
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }
}

?>

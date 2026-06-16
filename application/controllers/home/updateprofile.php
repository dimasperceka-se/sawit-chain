<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Updateprofile extends SS_Controller {

    public function __construct() {
        parent::__construct(1);
        $this->lang->load('updateprofile');
        $this->api = $this->config->item('api');
        $url = (index_page())?(base_url().index_page()):rtrim(base_url(),'/');
        $this->titlet = lang('Update Profile').' > ';
    }

    public function index() {
        $sql['get_profile'] = "
        SELECT 
        a.*,
        b.GroupName,
        b.GroupId,
        c.UnitId,
        c.UnitName,
        d.PrivateStaffID as private_staff_id,
        f.StaffID as program_staff_id,
        GROUP_CONCAT(e.DistrictId, '##', e.District) AS daerah,
        GROUP_CONCAT(h.DistrictId, '##', h.District) AS daerah_access,
        i.FlagAccess,
        IFNULL(d.PartnerID, f.PartnerID) AS PartnerID,
        IFNULL(d.OfficialStaffEmail, '-') AS official_email,
        IFNULL(d.PrivateStaffEmail, '-') AS private_email,
        IFNULL(d.OfficialCellphone, '-') AS official_phone,
        IFNULL(d.PrivateCellphone, '-') AS private_phone,
        IFNULL(d.OfficialStaffEmail, IFNULL(d.PrivateStaffEmail, '-')) AS email,
        IFNULL(d.OfficialCellphone, IFNULL(d.PrivateCellphone, '-')) AS phone,
        IFNULL(i.PartnerName, '-') AS partner_name,
        IFNULL(i.PartnerFullName, '-') AS partner_full_name,
        i.Photo AS partner_photo,
        GROUP_CONCAT(IFNULL(e.`District`,IFNULL(h.District, '-')) SEPARATOR ', ') AS district,
        IFNULL(d.StaffBirth, IFNULL(j.BirthDttm,'-')) AS birthdate,
        IFNULL(d.StaffGender,IFNULL(j.Gender,'-')) AS gender,
        IFNULL(d.Photo, j.Photo) AS user_photo,
        IFNULL(d.Location, '-') AS location,
        IFNULL(f.Position, '-') AS `position`,
        IFNULL(f.WorkArea, '') AS workarea,
        f.StatusCd,
        f.PersonID,
        j.* 
        FROM
        sys_user a 
        LEFT JOIN sys_user_group ON UserGroupUserId = a.UserId AND UserGroupIsDefault = '1' 
        LEFT JOIN sys_group b ON UserGroupGroupId = b.GroupId 
        LEFT JOIN sys_unit c ON b.GroupUnitId = c.UnitId 
        LEFT JOIN ktv_private_staff d ON a.UserId = d.UserId 
        LEFT JOIN ktv_program_staff f ON f.UserId = a.UserId 
        LEFT JOIN ktv_persons j ON j.PersonID = f.PersonID 
        LEFT JOIN ktv_district_partner z ON IFNULL(d.PartnerID, f.PartnerID) = z.PartnerID 
        LEFT JOIN ktv_district e ON z.DistrictID = e.DistrictID 
        LEFT JOIN ktv_access_staff g ON f.StaffId = g.StaffId 
        LEFT JOIN ktv_district h ON g.DistrictID = h.DistrictID 
        LEFT JOIN ktv_program_partner i ON i.PartnerID = IFNULL(d.PartnerID, f.PartnerID)        
        WHERE a.UserName=?";
        $data = array();
        $data['profile'] = $this->system->GetSql($sql['get_profile'], array($_SESSION['username']));
        $data['partner_photo'] = $this->config->item('api_base_url').'images/Photo/'.$data['profile'][0]['partner_photo'];
        $data['user_photo'] = $this->config->item('api_base_url').'images/Photo/'.$data['profile'][0]['user_photo'];

        if(!$this->is_url_exist($data['partner_photo']) || empty($data['profile'][0]['partner_photo'])){
            $data['partner_photo'] = $this->config->item('api_base_url').'images/Photo/default-partner.png';
        }
        if(!$this->is_url_exist($data['user_photo']) || empty($data['profile'][0]['user_photo'])){
            $data['user_photo'] = $this->config->item('api_base_url').'images/Photo/default-user.png';
        }
        $template='updateprofile';
        $data['js'] = 'profile_new';
        $data['action'] = array('data'=>$this->api.'/user/myprofile');

        $data['titlet'] = lang('Update Profile');
        $this->LoadView($data, $template);
    }

    public function set_lang($lang,$mod) {
        $_SESSION['language'] = $lang;
        redirect(str_replace('-','/',$mod), 'location');
    }

    function is_url_exist($url){
        $ch = curl_init($url);    
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
            $status = true;
        }else{
            $status = false;
        }
        curl_close($ch);
        return $status;
    }
}


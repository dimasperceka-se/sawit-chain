<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends SS_Controller {

	public function __construct() {
		parent::__construct(1);
		$this->lang->load('profile');
		$this->api = $this->config->item('api');
		$url = (index_page())?(base_url().index_page()):rtrim(base_url(),'/');
		$this->titlet = lang('Profile').' > ';
	}

	public function index() {
        $this->load->model('mprofile');
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
        IFNULL(d.StaffBirth, IFNULL(j.BirthDate,'-')) AS birthdate,
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
        LEFT JOIN ktv_access_staff g ON a.UserId = g.UserId
        LEFT JOIN ktv_district h ON g.DistrictID = h.DistrictID
        LEFT JOIN ktv_program_partner i ON i.PartnerID = IFNULL(d.PartnerID, f.PartnerID)
      WHERE a.UserName=?";
        $data = array();
        $data['profile'] = $this->system->GetSql($sql['get_profile'], array($_SESSION['username']));
        $staffid = $this->system->getStaffId($_SESSION['userid']);
        $roleAccess = $this->system->getFormUser($staffid);
        $accessArea = $this->system->getListAccessStaffApp($_SESSION['userid']);
        $Roles = [];
        $accessName = [];
        for ($i=0; $i < count($roleAccess['data']['groups']); $i++) {
            $this->db->where('GroupId', $roleAccess['data']['groups'][$i]);
            $this->db->select('GroupName');
            $Roles[$i] = $this->db->get('sys_group')->row_array();
        }

        for ($x=0; $x < count($roleAccess['data']['access']); $x++) {
            $this->db->where('DistrictID', $roleAccess['data']['access'][$x]);
            $this->db->select('District');
            $accessName[$x] = $this->db->get('ktv_district')->row_array();
        }

        $data['profile']['additonal'] = array($roleAccess['data'],'roles'=>$Roles,'access'=>$accessName);
        // for ($i=0; $i < count($roleAccess['data']['groups']); $i++) {
        //     $this->db->where('GroupId', $roleAccess['data']['groups'][$i]);
        //     $this->db->select('GroupName');
        //     $GroupName[$i] = $this->db->get('sys_group')->row_array();
        // }
         // url: m_api + '/basic_staff/image_staff',
        $data['profile']['profile'] = $this->system->getFullName($_SESSION['username']);
        if($data['profile'][0]['gender']==1){ $data['profile'][0]['gender']=lang('Male'); }else{ $data['profile'][0]['gender']=lang('Female'); }
        $data['partner_photo'] = $this->config->item('api_base_url').'images/Photo/'.$data['profile'][0]['partner_photo'];

        /*
        $data['user_photo'] = $this->config->item('api_base_url').'images/Photo/'.$data['profile'][0]['user_photo'];
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
        if(!is_url_exist($data['partner_photo']) || empty($data['profile'][0]['partner_photo'])){
          $data['partner_photo'] = $this->config->item('api_base_url').'images/Photo/default-partner.png';
        }
        if(!is_url_exist($data['user_photo']) || empty($data['profile'][0]['user_photo'])){
          $data['user_photo'] = $this->config->item('api_base_url').'images/Photo/default-user.png';
        }*/

        $data['fotoProfile'] = "";
        if($_SESSION['Photo_staff'] != ""){
            $data['fotoProfile'] = $this->config->item('api_base_url').'images/staff/'.$_SESSION['Photo_staff'];
        }else{
            if($_SESSION['Gender'] == "f"){
                $data['fotoProfile'] = $this->config->item('api_base_url').'images/default_photo/female-business.jpg';
            }else{
                $data['fotoProfile'] = $this->config->item('api_base_url').'images/default_photo/male-business.jpg';
            }
        }


        $data['last_access']        = $this->mprofile->getLastAccess();
        $data['last_page_access']   = $this->mprofile->getLastPageAccess();
        $data['login_history']   = $this->mprofile->getHistoryLogin($data['profile'][0]['UserId']);
        $template ='profile';
    	// $data['js'] = 'profile_new';
    	$data['action'] = array('data'=>$this->api.'/profile/profile');

    	$data['titlet'] = lang('Profile');
      $data['url_awss3'] = $this->config->item('CTCDN');

        $data['js'] = 'changepassword';
        $data['action'] = array(
            'password'=>$this->api.'/user/mypassword',
            'profile'=>$this->api.'/user/myprofile'
        );

        $data['breadcrumb_1'] = lang('User');
        $data['breadcrumb_2'] = lang('Profile');
        $this->LoadView($data, $template);
	}

	public function set_lang($lang,$mod) {
		$_SESSION['language'] = $lang;
		redirect(str_replace('-','/',$mod), 'location');
	}
}


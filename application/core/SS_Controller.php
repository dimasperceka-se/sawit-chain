<?php

class SS_Controller extends CI_Controller
{

    public function __construct($modId = '')
    {
        session_start();
        parent::__construct();
        $this->autentication($modId);
        $this->setPageAccessLog();
        if (isset($_REQUEST['ajax'])) {
            $_SESSION['ajax'] = '1';
        }

        if ($_SESSION['language'] == '') {
            $_SESSION['language'] = 'indonesia';
        }

        $this->config->set_item('language', strtolower($_SESSION['language']));
        $this->params = $this->GetParam();

        $this->config->load('coop');

        // print_r($_SESSION);
    }
    public function autentication($modId)
    {
        $func = $this->uri->segment(3);
        if ($func == '') {
            $func = 'index';
        }

        // $fun    = explode('_', $func);
        // $func   = $fun[0];
        $mod    = $this->uri->segment(2);
        $module = ($mod != '' ? $this->uri->segment(1) . '/' . $mod : $this->router->routes['default_controller']);
        $param  = $this->uri->segment(4);
        $sql    = "SELECT GroupMenuSegmen as id FROM sys_group_menu_act WHERE GroupMenuGroupId=? and GroupMenuSegmen=?";
        if ($modId == '0') {
            return;
        }

        $this->mmod = $module . '/' . $func . ($param != '' ? '/' . $param : '');
        $cek        = $this->system->GetSql($sql, array($_SESSION['groupid'], $this->mmod));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        if (($modId == '1' and $_SESSION['userid'] != '') or $cek[0]['id'] != '') {
            //$this->setAccessLog();
            return;
        } elseif ($_SESSION['userid'] == '') {
            if ($this->input->is_ajax_request()) {
                show_error('Session Timeout', 401);
            }
            redirect('system/login/index/#' . $this->uri->uri_string(), 'location');

        } else {
            // show_404('unavailable');
            show_error('No Access', 200);
            // exit('no access');
        }
    }

    public function setAccessLog()
    {
        // start transaction
        $this->load->helper('security');
        $this->db->trans_start(false);
        $result = $this->insertAccessLog($_SESSION['userid'], ip_address(), user_agent());
        // end transactinon
        if ($result == true) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
    }

    public function setPageAccessLog()
    {
        if ($_SESSION['userid'] != '') {
            $this->load->helper('security');
            $this->load->helper('url');
            // start transaction
            $this->db->trans_start(false);
            $result = $this->insertPageAccessLog($_SESSION['userid'], ip_address(), current_full_url());
            // end transactinon
            if ($result == true) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }
        }
    }

    private function insertAccessLog($user_id, $ip_address, $user_agent)
    {
        $sql = <<<SQL
INSERT INTO `sys_log_access` (
  `UserID`,
  `SessionIP`,
  `UserAgent`
)VALUES(
      ?,
      ?,
      ?
)
SQL;
        $result = $this->db->query($sql, array($user_id, $ip_address, $user_agent));
        return $result;
    }

    private function insertPageAccessLog($user_id, $ip_address, $page)
    {
        $sql = <<<SQL
INSERT INTO `sys_log_page_access` (
  `UserID`,
  `SessionIP`,
  `Page`
)VALUES(
    ?,
    ?,
    ?
)
SQL;
        $result = $this->db->query($sql, array($user_id, $ip_address, $page));
        return $result;
    }

    public function LoadView($data = null, $template = 'common_content')
    {
        $this->lang->load('common');
        $this->lang->load('menu');
        $this->lang->load($this->router->fetch_class());
        // $this->load->view('common_lang', array('lang' => $this->lang->all_lines()));
        $sqlm = "SELECT
   p.MenuName AS parent,
   m.MenuName AS menu
FROM sys_menu m
LEFT JOIN sys_menu p ON p.MenuId = m.MenuParentId
WHERE
m.MenuModule = ?
AND (m.MenuParam = ? OR '' = ?) AND m.MenuShow = 'Yes'";
        $patterns        = array();
        $patterns[0]     = '/\/index/';
        $patterns[1]     = '/\/\d+/';
        $replacements    = array();
        $replacements[0] = '';
        $replacements[1] = '';
        preg_match('/\d+/', $this->mmod, $param);
        $param = empty($param[0]) ? '' : $param[0];
        $mmod  = preg_replace($patterns, $replacements, $this->mmod);
        // echo '<pre>'; print_r($this->mmod); echo '</pre>';
        // echo '<pre>'; print_r($mmod); echo '</pre>';
        // exit;
        if (strpos($mmod, 'home/home') !== false) {
            $param = '';
        }
        $sqlmd = $this->system->GetSql($sqlm, array($mmod, $param, $param));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
        // echo '<pre>'; print_r($data); echo '</pre>'; exit;
        if (empty($data['titlet'])) {
            if (!empty($sqlmd)) {
                if($_SESSION["PartnerID"] == "194" OR $_SESSION["PartnerID"] == "14"){
                    if($sqlmd[0]['menu'] == "Farmer Training"){
                        $sqlmd[0]['menu'] = "Member Training";
                    }
                    if($sqlmd[0]['menu'] == "Kader Training"){
                        $sqlmd[0]['menu'] = "Group Training";
                    }
                    if($sqlmd[0]['menu'] == "Program KPI"){
                        $sqlmd[0]['menu'] = "Wild Asia Malaysia KPI";
                    }
                }


                $data['titlet']       = lang($sqlmd[0]['menu']);
                $data['breadcrumb_1'] = !empty($sqlmd[0]['parent']) ? lang($sqlmd[0]['parent']) : lang($sqlmd[0]['menu']);
                $data['breadcrumb_2'] = lang($sqlmd[0]['menu']);
            } else {
                $data['titlet'] = lang('Dashboard');
            }
        }
        $segmen          = $mmod . '/index';
        $sql['province'] = "SELECT
   p.ProvinceID AS id,
   p.Province AS name
FROM sys_group_menu_act ga
LEFT JOIN ktv_province p ON p.ProvinceID = SUBSTRING_INDEX(ga.GroupMenuSegmen,'/',-1)
WHERE
   p.ProvinceID
   AND ga.GroupMenuGroupId = ?
   AND ga.GroupMenuSegmen LIKE '{$segmen}%'
      ";
        $data['province_access'] = $this->system->GetSql($sql['province'], array($_SESSION['groupid']));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
        $data['province_current'] = $data['breadcrumb_2'];
        // echo '<pre>'; print_r($data['province_current']); echo '</pre>';
        $data['segmen'] = $segmen;

        $sql['get_org'] = "SELECT StaffID id from ktv_supplychain_staff
         /*left join ktv_supplychain_org_view on SupplychainID=StaffSupplychainID*/
         where UserID=? /*and OrgType in ('Organisasi Petani','Pedagang','Kelompok Petani')*/";
        $data_header['org'] = $this->system->GetSql($sql['get_org'], array($_SESSION['userid']));

        // if ($template == 'common_content_region') {
        if ($template != 'common_content') {
            $this->load->model('mprofile');

            $prov    = !empty($this->input->get('prov')) ? $this->input->get('prov') : '';
            $dist    = !empty($this->input->get('dist')) ? $this->input->get('dist') : '';
            $subdist = !empty($this->input->get('subdist')) ? $this->input->get('subdist') : '';


            $user_access = $this->mprofile->getUserAccess($prov, $dist, $subdist,$data['mentokDistrict']);
            if ($user_access !== false) {
                $data['action']['class']         = !empty($user_access['class']) ? $user_access['class'] : '';
                $data['action']['ProvinceID']    = !empty($user_access['prov']) ? $user_access['prov'] : '';
                $data['action']['DistrictID']    = !empty($user_access['dist']) ? $user_access['dist'] : '';
                $data['action']['SubDistrictID'] = !empty($user_access['subdist']) ? $user_access['subdist'] : '';
                $data['action']['Province']      = !empty($user_access['prov_name']) ? $user_access['prov_name'] : '';
                $data['action']['District']      = !empty($user_access['dist_name']) ? $user_access['dist_name'] : '';
                $data['action']['SubDistrict']   = !empty($user_access['subdist_name']) ? $user_access['subdist_name'] : '';
                $data['filter_region']           = $user_access['data'];
                $data['current_region']          = $user_access['current_region'];
            } else {
                show_error('No Access', 200);
                exit;
            }
        }
        if (isset($_SESSION['ajax'])) {
            //$this->load->view('common_lang', array('lang' => $this->lang->all_lines()));
            $this->load->view($template, $data);
            unset($_SESSION['ajax']);
        } else {
            /*
            Kode dibawah ini tidak digunakan lagi, karena param province di menu sudah tidak ada
             */
            /*if ($_SESSION['daerah'] != '') {
            $dae = explode(',', $_SESSION['daerah']);
            for ($i = 0; $i < sizeof($dae); $i++) {
            $da  = explode('##', $dae[$i]);
            $d[] = $da[0];
            }
            $sql_left  = "LEFT JOIN ktv_district ON ProvinceID=mm.MenuParam";
            $sql_where = " and (DistrictID in (" . implode(',', $d) . ") OR DistrictID is null)";
            } elseif ($_SESSION['daerah_access'] != '') {
            // $dae = explode(',', $_SESSION['daerah_access']);
            // for ($i = 0; $i < sizeof($dae); $i++) {
            //    $da  = explode('##', $dae[$i]);
            //    $d[] = $da[0];
            // }
            $sql_left  = "LEFT JOIN ktv_district ON ProvinceID=mm.MenuParam";
            $sql_where = " and (DistrictID in (" . $_SESSION['daerah_access'] . ") OR DistrictID is null)";
            }*/
            $sql['get_parent_menu'] = "SELECT m.MenuName, m.MenuId, m.MenuParentId, m.MenuModule, m.MenuIcon, m.MenuParam, mm.MenuModule AS child_module, MIN(mm.MenuParam) AS child_param, IF(mm2.MenuParentId, 1, 0) AS has_granchild
            FROM sys_group_menu_act
            LEFT JOIN sys_menu_act ON GroupMenuMenuAksiId=MenuAksiId
            LEFT JOIN sys_menu m ON MenuAksiMenuId=MenuId
            LEFT JOIN (
               SELECT
                  m.MenuParentId,
                  m.MenuModule,
                  m.MenuParam
               FROM sys_group_menu_act ga
               JOIN sys_menu_act ma ON ma.MenuAksiId = ga.GroupMenuMenuAksiId
               JOIN sys_menu m ON m.MenuId = ma.MenuAksiMenuId
               WHERE
                  m.MenuShow='Yes' AND m.MenuParam
                  AND ga.GroupMenuGroupId = ?
               ORDER BY m.MenuId
            ) mm ON mm.MenuParentId = m.MenuId
            LEFT JOIN (
               SELECT
                  m.MenuParentId
               FROM sys_group_menu_act ga
               JOIN sys_menu_act ma ON ma.MenuAksiId = ga.GroupMenuMenuAksiId
               JOIN sys_menu m ON m.MenuId = ma.MenuAksiMenuId
               JOIN sys_menu m2 ON m2.MenuParentId = m.MenuId
               WHERE m.MenuShow='Yes'
                  AND m.MenuParentId
                  AND ga.GroupMenuGroupId = ?
                  ORDER BY m.MenuId
            ) mm2 ON mm2.MenuParentId = m.MenuId
            %s
            WHERE m.MenuParentId = 0 AND GroupMenuGroupId=? AND m.MenuShow='Yes' %s
            GROUP BY MenuId
            ORDER BY m.MenuParentId,m.MenuOrder,m.MenuName";
            $sql['get_child_menu'] = "SELECT mm.MenuName, mm.MenuId, mm.MenuParentId,mm.MenuModule,mm.MenuIcon,mm.MenuParam,mm2.MenuModule AS child_module, MIN(mm2.MenuParam) AS child_param, IFNULL(mm.MenuTypeFlag, mm3.MenuName) AS child_flag_type
            FROM sys_group_menu_act
            LEFT JOIN sys_menu_act ON GroupMenuMenuAksiId=MenuAksiId
            LEFT JOIN sys_menu mm ON MenuAksiMenuId=mm.MenuId
            LEFT JOIN sys_menu mm3 ON mm3.MenuId = mm.MenuParentId
            LEFT JOIN (
               SELECT
                  m.MenuParentId,
                  m.MenuModule,
                  m.MenuParam
               FROM sys_group_menu_act ga
               JOIN sys_menu_act ma ON ma.MenuAksiId = ga.GroupMenuMenuAksiId
               JOIN sys_menu m ON m.MenuId = ma.MenuAksiMenuId
               WHERE
                  m.MenuShow='Yes' AND m.MenuParam
                  AND ga.GroupMenuGroupId = ?
               ORDER BY m.MenuId
            ) mm2 ON mm2.MenuParentId = mm.MenuId
            %s
            WHERE GroupMenuGroupId=? AND mm.MenuParentId = ? AND mm.MenuShow='Yes' %s
            GROUP BY mm.MenuId
            ORDER BY mm.MenuParentId,mm.MenuOrder,mm.MenuName";
            $menu = $this->system->GetSql(sprintf($sql['get_parent_menu'], $sql_left, $sql_where), array($_SESSION['groupid'], $_SESSION['groupid'], $_SESSION['groupid']));
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
            foreach ($menu as $key => $value) {
                if (!empty($value['child_module']) and !empty($value['child_param'])) {
                    $menu[$key]['child'] = array();
                } else /*if(!empty($value['has_granchild']))*/{
                    $menu[$key]['child'] = $this->system->GetSql(sprintf($sql['get_child_menu'], $sql_left, $sql_where), array($_SESSION['groupid'], $_SESSION['groupid'], $value['MenuId']));
                    // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; exit;
                }
            }
            $data_header['menus'] = $menu;
            // echo '<pre>'; print_r($menu); echo '</pre>'; exit;


            // list group
            $sql_group = "SELECT
 g.GroupId AS id
 , g.GroupName AS `name`
 , UserGroupIsDefault  AS is_default
FROM
 sys_user_group ug
JOIN sys_group g ON g.GroupId = ug.UserGroupGroupId
WHERE
 ug.UserGroupUserId = ?
         ";
            $query                 = $this->db->query($sql_group, array($_SESSION['userid']));
            $data_header['groups'] = $query->num_rows() > 0 ? $query->result_array() : array();
            $sql_group             = "SELECT
 g.GroupId AS id
 , g.GroupName AS `name`
FROM sys_group g
WHERE
 g.GroupId = ?
         ";
            $query                        = $this->db->query($sql_group, array($_SESSION['groupid']));
            $data_header['current_group'] = $query->num_rows() > 0 ? $query->row_array(0) : array();

            //proses untuk Proyek ================================ (begin)
                //get list proyek yg tersedia utk user ini
                $sql="SELECT
                        b.StaffID
                        , c.`ProjID`
                        , d.`ProjName`
                        , c.`ProjDefault`
                    FROM
                        ktv_persons a
                        INNER JOIN ktv_staffs b ON a.PersonID = b.PersonID
                        LEFT JOIN ktv_staffs_project c ON b.StaffID = c.`StaffID`
                        LEFT JOIN ktv_program_partner_project d ON c.`ProjID` = d.`ProjID`
                    WHERE
                        a.UserId = {$_SESSION['userid']}";
                $query = $this->db->query($sql);
                $dataUserProject = $query->result_array();
            //proses untuk Proyek ================================ (end)

            $filter_by = $_SESSION['filter_by'];
            switch ($filter_by) {
                case '1':
                    $filter_label = 'partner';
                    $filter_title = lang('Partner');
                    $filter_list  = $this->system->GetSql("SELECT PartnerID AS id , PartnerName AS label FROM ktv_program_partner WHERE StatusCode = 'active' ORDER BY label", array());
                    break;
                case '2':
                    $filter_label = 'sce';
                    $filter_title = lang('SCE');
                    $filter_list  = $this->system->GetSql("SELECT SceID AS id, CONCAT(f.`FarmerID`,' - ',f.FarmerName) AS label FROM sce_farmer s JOIN ktv_farmer f ON f.FarmerID = s.FarmerID WHERE s.StatusCode = 'active' ORDER BY label", array());
                    break;
                case '3':
                    $filter_label = 'trader';
                    $filter_title = lang('Trader');
                    $filter_list  = $this->system->GetSql("SELECT TraderID AS id, TraderName AS label FROM ktv_traders WHERE StatusCode = 'active' ORDER BY label", array());
                    break;
                case '4':
                    $filter_label = 'cooperative';
                    $filter_title = lang('Cooperative');
                    $filter_list  = $this->system->GetSql("SELECT CoopID AS id, CoopName AS label FROM ktv_cooperatives WHERE StatusCode = 'active' ORDER BY label", array());
                    break;
                case '5':
                    $filter_label = 'warehouse';
                    $filter_title = lang('Warehouse');
                    $filter_list  = $this->system->GetSql("SELECT WarehouseID AS id, WarehouseName AS label FROM ktv_warehouse  WHERE StatusCode = 'active' ORDER BY label", array());
                    break;
                default:
                    $filter_label = '';
                    $filter_title = lang('Filter By');
                    $filter_list  = array();
                    break;
            }
            $data_header['filter_by']    = $filter_by;
            $data_header['filter_label'] = $filter_label;
            $data_header['filter_title'] = $filter_title;
            $data_header['filter_list']  = $filter_list;
            $data_header['dataUserProject']  = $dataUserProject;

            //Load lang JS dibedakan
            $data_header['lang'] = LoadLangJs(strtolower($_SESSION['language']));

            $this->load->view('common_header', $data_header);
            $data['action']['url'] = (index_page()) ? (base_url() . index_page()) : rtrim(base_url(), '/');
            $data['action']['api'] = $this->api = $this->config->item('api');
            $this->load->view($template, $data);
            $this->load->view('common_footer');
        }
    }

    public function GetParam()
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
        if ($this->method == 'PUT') {
            // <-- Have to jump through hoops to get PUT data
            $raw         = '';
            $httpContent = fopen('php://input', 'r');
            while ($kb = fread($httpContent, 1024)) {
                $raw .= $kb;
            }
            fclose($httpContent);
            $params = array();
            parse_str($raw, $params);

            if (isset($params['data'])) {
                $this->params = json_decode(stripslashes($params['data']));
            } else {
                $params       = json_decode(stripslashes($raw));
                $this->params = $params->data;
            }
        } else {
            // grab JSON data if there...
            $this->params = (isset($_REQUEST['data'])) ? json_decode(stripslashes($_REQUEST['data'])) : null;

            if (isset($_REQUEST['data'])) {
                $this->params = json_decode(stripslashes($_REQUEST['data']));
            } else {
                $raw         = '';
                $httpContent = fopen('php://input', 'r');
                while ($kb = fread($httpContent, 1024)) {
                    $raw .= $kb;
                }
                $params = json_decode(stripslashes($raw));
                if ($params) {
                    $this->params = $params->data;
                }
            }
        }
        return $this->params;
    }

    public function last_synced()
    {
        $q = $this->db->query("select DateTimeSync from coop_sync where CoopID = 1");
        if ($q->num_rows() > 0) {
            $r = $q->row();
            return "Sinkronisasi Data Terakhir Pada : " . $r->DateTimeSync;
        } else {
            return "";
        }
    }

}

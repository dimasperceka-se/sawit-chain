<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_dashboard extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('user_dashboard/muser_dashboard', 'mu_dash');
    }

    public function grid_main_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = $sorting[0]->property;
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = $sorting[0]->direction;
        else
            $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $pSearch = array();
        $pSearch['KeySearch'] = filter_var($this->get('KeySearch'), FILTER_SANITIZE_STRING);

        //echo '<pre>'; print_r($pSearch); exit;
        $data = $this->mu_dash->GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function user_dashboard_form_open_get() {
        $DashID = (int) $this->get('DashID');
        $data = $this->mu_dash->UserDashFormOpen($DashID);
        $this->response($data, 200);
    }

    public function data_input_post() {
        $return = array();
        $varPost = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_UserDashboard_MainForm-Form-", '', $key);
            if ($value == "") {
                $value = null;
            }
            $paramPost[$keyNew] = $value;
        }
        //echo '<pre>'; print_r($paramPost); exit;

        if ($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->mu_dash->InsertUserDash($paramPost);
        } else {
            $proses = $this->mu_dash->UpdateUserDash($paramPost);
        }

        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_input_delete() {
        $DashID = (int) $this->delete('DashID');

        $proses = $this->mu_dash->DeleteUserDash($DashID);
        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function view_dashboard_get($DashID) {
        $DashID = (int) $DashID;
        $data = array();
        $data = $this->mu_dash->UserDash($DashID);
        /* $METABASE_SITE_URL = "https://analytics.koltiva.com";
        $METABASE_SECRET_KEY = "c16bfe1664ffb9cff812e859044147f7d3064d02cfdd59b424874791fb9befa0"; */

        $METABASE_SITE_URL = "https://koltiva.metabaseapp.com";
        $METABASE_SECRET_KEY = "d60016f0c76ff89a52bfae965746af3bffa679154c4fc3be078ce339738a9c07";


        # php >= 5.6
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $token = (new \Lcobucci\JWT\Builder())
                ->withClaim('resource', [
                    'dashboard' => (int) $data['data']['BoardID'],
                ])
                ->withClaim('params', (object) [])
                ->getToken($signer, new Lcobucci\JWT\Signer\Key($METABASE_SECRET_KEY));

        $url_iframe = $METABASE_SITE_URL . "/embed/dashboard/" . $token . "#bordered=false";

        $data['url'] = $url_iframe;
        $this->response($data, 200);
    }

    public function user_sharing_grid_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = $sorting[0]->property;
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = $sorting[0]->direction;
        else
            $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $pSearch = array();
        $pSearch['DashID'] = (int) $this->get('DashID');
        $pSearch['KeySearch'] = filter_var($this->get('KeySearch'), FILTER_SANITIZE_STRING);

        //echo '<pre>'; print_r($pSearch); exit;
        $data = $this->mu_dash->GetUserDashGridMain($pSearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function select_staff_multiple_main_grid_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = $sorting[0]->property;
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = $sorting[0]->direction;
        else
            $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $pSearch = array();
        $pSearch['DashID'] = (int) $this->get('DashID');
        $pSearch['CmbGroup'] = (int) $this->get('CmbGroup');
        $pSearch['KeySearch'] = filter_var($this->get('TxtSearchLabel'), FILTER_SANITIZE_STRING);

        //echo '<pre>'; print_r($pSearch); exit;
        $data = $this->mu_dash->GetSelectStaffGridMain($pSearch, $start, $limit, $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function user_sharing_post() {
        $UserIDs = $this->post('UserIDs');
        $DashID = (int) $this->post('DashID');

        $proses = $this->mu_dash->AddUserSharing($DashID, $UserIDs);
        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function user_sharing_delete() {
        $DashSetID = (int) $this->delete('DashSetID');

        $proses = $this->mu_dash->DeleteUserSharing($DashSetID);
        if ($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

}

<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

//write excel
require_once 'application/third_party/Spout/Autoloader/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;


class Menu_pull_engine_check extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('system/mmenu_pull_engine_check');
    }

    public function grid_main_pull_engine_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else
            $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $data = $this->mmenu_pull_engine_check->GetGridMainPullEngine($start, $limit, 'limit', $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function grid_main_sys_setting_get() {
        //sort
        $sorting = json_decode($this->get('sort'));
        if (isset($sorting[0]->property))
            $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : '';
        else
            $sortingField = null;
        if (isset($sorting[0]->direction))
            $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : '';
        else
            $sortingDir = null;
        $start = (int) $this->get('start');
        $limit = (int) $this->get('limit');

        $data = $this->mmenu_pull_engine_check->GetGridMainSysSetting($start, $limit, 'limit', $sortingField, $sortingDir);
        $this->response($data, 200);
    }

    public function update_value_setting_post()
    {
        $return    = array();
        $varPost   = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Menu_pull_engine_check_PanelSysSetting-GridSysSetting-Form-", '', $key);
            $paramPost[$keyNew] = $value;
        }

        $proses = $this->mmenu_pull_engine_check->UpdateValueSetting($paramPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }
}
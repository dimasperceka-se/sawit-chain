<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Aug 21 2019
 *  File : migrationfiles3.php
 *******************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrationfiles3 extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('tools/mmigrations3');
    }
    
    public function migrate_farmer_photo_get() {
        // echo 'udahproses, biar tidak terproses lagi'; exit;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $proses = $this->mmigrations3->MigrateFarmerPhoto();
        $this->response($proses, 200);
    }
    
    public function migrate_farmer_plot_photo_get() {
        // echo 'udahproses, biar tidak terproses lagi'; exit;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $proses = $this->mmigrations3->MigrateFarmerPlotPhoto();
        $this->response($proses, 200);
    }

    public function migrate_sme_photo_get() {
        // echo 'udahproses, biar tidak terproses lagi'; exit;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $proses = $this->mmigrations3->MigrateSMEPhoto();
        $this->response($proses, 200);
    }

    public function migrate_mill_photo_get() {
        // echo 'udahproses, biar tidak terproses lagi'; exit;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $proses = $this->mmigrations3->MigrateMillPhoto();
        $this->response($proses, 200);
    }
}
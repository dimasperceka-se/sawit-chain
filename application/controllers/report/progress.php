<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Progress extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'progress';
        $api = $this->config->item('api');
        $data['action'] = array('crud' => $api . '/report/progress',
            'Kabupaten' => $api . '/report/Kabupatens',
            'Provinsi' => $api . '/report/Provinsis',
            'Cpg' => $api . '/report/cpgs',
            'Batch' => $api . '/report/batches',
            'detail' => $api . '/report/details',
            'export_details_progress' => $api . '/report/export_excel_detailsp/',
            'activity_detail' => $api . '/report/activity_detail',
            'cetak_activity_detail' => $api . '/report/activity_detail_excel',
            'act_index' => ! $this->system->CekAksi('index'));
        $data['style'] = "
         .x-toolbar-footer{background-color:#FFFFFF !important}";
        $this->LoadView($data);
    }

}


<?php
/**
 * @Author: Gitandi Nadzari
 * @Date:   2018-09-19 15:30:00
 * @Last Modified by:   Gitandi Nadzari
 * @Last Modified time: 2018-09-19 15:30:00
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sawit_terampil extends SS_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js']     = 'report/sawit_terampil';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'api_base_url' => $this->config->item('api_base_url'),
            // 'combo_years'=> $api.'/report_utz_certification/combo_years',
            // 'combo_months'=> $api.'/report_utz_certification/combo_months',
            'combo_monthyears'=> $api.'/report_sawit_terampil/combo_monthyears',
            'classification'=> $api.'/report_sawit_terampil/classification',
            'report'=> $api.'/report_sawit_terampil/certification',
            'do_kpicalc'=> $api.'/report_sawit_terampil/do_kpicalculation',
            'wave_jb' => $api.'/report_sawit_terampil/wave_jb',
            // 'do_testcalc'=> $api.'/report_utz_certification/do_cekinsert',
            'act_update'=> !$this->system->CekAksi('update')?false:true,
            'act_calculate_kpi' => !$this->system->CekAksi('calculate_kpi')
        );
        
        $this->LoadView($data);
    }
}
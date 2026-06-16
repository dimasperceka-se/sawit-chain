<?php
/**
 * @Author: nikolius
 * @Date:   2017-02-09 15:52:53
 */
class Sql_view extends SS_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['js'] = 'report/sql_view';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'api_base_url'          => $this->config->item('api_base_url'),
            'userid'                => $_SESSION['userid'],
            'is_admin'              => $_SESSION['is_admin'],
            'act_index'             => !$this->system->CekAksi('index'),
            'act_add'               => !$this->system->CekAksi('add'),
            'act_update'            => !$this->system->CekAksi('update'),
            'act_delete'            => !$this->system->CekAksi('delete'),
            'run_query'             => !$this->system->CekAksi('run_query'),
            'sql_view_share'        => !$this->system->CekAksi('sql_view_share'),
            'sql_view_filter'        => !$this->system->CekAksi('sql_view_filter'),
            'sql_view_export_excel' => !$this->system->CekAksi('sql_view_export_excel'),
            'sql_view_export_csv'   => !$this->system->CekAksi('sql_view_export_csv'),
            'export_excel_utilities'   => !$this->system->CekAksi('export_excel_utilities')
        );
        $this->LoadView($data);
    }
}

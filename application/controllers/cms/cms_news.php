<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 14 2018
 *  File : cms_news.php
 *******************************************/

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cms_news extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = '')
    {
        $data['js'] = 'cms_news';
        $api        = $this->config->item('api');

        $data['action'] = array(
            'api_base_url'                          => $this->config->item('api_base_url'),
            'base_url'                              => base_url(),            
            'act_add'                               => !$this->system->CekAksi('add'),
            'act_update'                            => !$this->system->CekAksi('update'),
            'act_delete'                            => !$this->system->CekAksi('delete'),
        );
        $this->LoadView($data, 'common_content');
    }
}
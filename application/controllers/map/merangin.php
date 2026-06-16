<?php

/** 
 * @Last Modified by:   Aprianto 
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Merangin extends SS_Controller
{

    public function __construct()
    {
        parent::__construct(1);
        $this->lang->load('dashboard');
        $this->lang->load('home');
        $this->api    = $this->config->item('api');
        $url          = (index_page()) ? (base_url() . index_page()) : rtrim(base_url(), '/');

        $this->action = array(
            'url'      => $url,
            'api'      => $this->api,
            'district' => $this->api . '/dashboard/district',
            'daer'     => $_SESSION['daerah'],
            'partner'  => $_SESSION['FlagAccess'] == 1 ? $_SESSION['PartnerID'] : '',
            'path'     => (index_page() ? index_page() . '/' : '') . 'progre_dboard/' . $this->router->fetch_class() . '/index/',
        );
        $this->titlet = lang('Dashboard') . ' > ';
    }

    public function index($prov = '', $kab = '')
    {
        $data          = array('prov' => $prov, 'private' => $private, 'daer' => $_SESSION['daerah'], 'petani' => $_GET['petani']); 
        $data['title'] = $this->titlet . lang('Sinarmas Merangin');
        $this->LoadView($data, 'progre_dboard/sinarmas_merangin');
    }
	
	function sinarmas_merangin()
	{
		$this->load->view('SinarMas_Merangin');
	}

}

?>
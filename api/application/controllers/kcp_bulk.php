<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-03 15:32:37
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//write excel
require_once 'application/third_party/Spout3/Autoloader/autoload.php';


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
//use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class Kcp_bulk extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('kcp/mkcp');
        $this->load->library('awsfileupload');
    }

    public function grid_main_get(){
        //set bahasa
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        //get param
        $pSearch = array(
            'prov' => $this->get('prov'),
            'kab' => $this->get('kab'),
            'kec' => $this->get('kec'),
            'textSearch' => $this->get('textSearch'),
            'rowStatusPerusahaan' => $this->get('rowStatusPerusahaan'),
            'cmbStatusPerusahaan' => $this->get('cmbStatusPerusahaan'),
            'rowTahunTerbentuk' => $this->get('rowTahunTerbentuk'),
            'cmbOpTahunTerbentuk' => $this->get('cmbOpTahunTerbentuk'),
            'textTahunTerbentuk' => $this->get('textTahunTerbentuk'),
            'rowPhone' => $this->get('rowPhone'),
            'textPhone' => $this->get('textPhone'),
            'rowHavePhoto' => $this->get('rowHavePhoto'),
            'cmbHavePhoto' => $this->get('cmbHavePhoto'),
            'rowTotalPermanentEmployee' => $this->get('rowTotalPermanentEmployee'),
            'cmbOpTotalPermanentEmployee' => $this->get('cmbOpTotalPermanentEmployee'),
            'textTotalPermanentEmployee' => $this->get('textTotalPermanentEmployee')
        );

        $data = $this->mkcp->getGridMainMill($pSearch,$this->get('start'),$this->get('limit'),$sortingField,$sortingDir);
        $this->response($data, 200);
    }

    public function data_post(){
        if($this->post('Koltiva_view_KCP_FormMainKCPBulk-FormBasicData-KCPID') == ""){
            //insert
            $proses = $this->mkcp->insertKCPBulk($this->post());
        }else{
            //update
            $proses = $this->mkcp->updateKCPBulk($this->post());
        }
        $this->response($proses, 200);
    }

    public function data_delete(){
        $KCPID = (int) $this->delete('KCPID');
        $proses = $this->mkcp->deleteKCPBulk($KCPID);
        $this->response($proses, 200);
    }

    public function data_get(){
        $KCPID = (int) $this->get('KCPID');
        $proses = $this->mkcp->getKCPBulk($KCPID);
        $this->response($proses, 200);
    }
}
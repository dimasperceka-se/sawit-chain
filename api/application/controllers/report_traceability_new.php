<?php defined('BASEPATH') or exit('No direct script access allowed');

class Report_traceability_new extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('report/mreport_traceability_new');
    }

    public function combo_partner_get()
    {
        $data = $this->mreport_traceability_new->ReadComboPartner($_SESSION['PartnerID']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }
    }
    
    public function combo_mill_get()
    {
        $data = $this->mreport_traceability_new->ReadComboMill($this->get('PartnerID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
        }

    }
    
    public function combo_do_get(){
        $this->response($this->mreport_traceability_new->ReadComboDO($this->get('start'),$this->get('end'),$this->get('PartnerID')), 200);
    }
    
    public function combo_agent_get()
    {
        $this->response($this->mreport_traceability_new->ReadComboAgent($this->get('start'),$this->get('end'),$this->get('PartnerID')), 200);
    }
    
    public function grid_mill_get(){
        $data = $this->mreport_traceability_new->ReadGridMill(
            $this->get('partnerid'),
            $this->get('startd'),
            $this->get('end'),
            $this->get('mill'),
            $this->get('do'),
            $this->get('agent'),
            $this->get('start'),
            $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }

    public function grid_do_get(){
        $data = $this->mreport_traceability_new->ReadGridDO(
            $this->get('partnerid'),
            $this->get('startd'),
            $this->get('end'),
            $this->get('mill'),
            $this->get('do'),
            $this->get('agent'),
            $this->get('start'),
            $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }

    public function grid_agent_get(){
        $data = $this->mreport_traceability_new->ReadGridAgent(
            $this->get('partnerid'),
            $this->get('startd'),
            $this->get('end'),
            $this->get('mill'),
            $this->get('do'),
            $this->get('agent'),
            $this->get('start'),
            $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }

    public function grid_farmer_get(){
        $data = $this->mreport_traceability_new->ReadGridFarmer(
            $this->get('partnerid'),
            $this->get('startd'),
            $this->get('end'),
            $this->get('mill'),
            $this->get('do'),
            $this->get('agent'),
            $this->get('start'),
            $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    
    public function store_transaction_ch_get(){
        $data = $this->mreport_traceability_new->ReadStoreTransactionCH(
            $this->get('startd'),
            $this->get('end'),
            $this->get('wh'),
            $this->get('ch'),
            $this->get('bs'),
            $this->get('sert'),
            $this->get('BatchID'),
            $this->get('start'),
            $this->get('limit'),
            $this->get('BatchIDWH')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    
    public function store_transaction_bs_get(){
        $data = $this->mreport_traceability_new->ReadStoreTransactionBS(
            $this->get('startd'),
            $this->get('end'),
            $this->get('wh'),
            $this->get('ch'),
            $this->get('bs'),
            $this->get('sert'),
            $this->get('BatchID'),
            $this->get('start'),
            $this->get('limit'),
            $this->get('BatchIDWH'),
            $this->get('BatchIDCH')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    
    public function store_transaction_farmer_get(){
        $data = $this->mreport_traceability_new->ReadStoreTransactionFarmer(
            $this->get('startd'),
            $this->get('end'),
            $this->get('wh'),
            $this->get('ch'),
            $this->get('bs'),
            $this->get('sert'),
            $this->get('BatchID'),
            $this->get('start'),
            $this->get('limit'),
            $this->get('BatchIDWH'),
            $this->get('BatchIDCH'),
            $this->get('BatchIDBS')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    
    public function print_transaction_farmer_get(){
        $startd = $this->get('startd');
        $end = $this->get('end');
        $wh = $this->get('wh');
        $ch = $this->get('ch');
        $bs = $this->get('bs');
        $sert = $this->get('sert');
        $BatchID = $this->get('BatchID');
        $data = $this->mreport_traceability_new->ReadStoreTransactionFarmer(
            $startd,
            $end,
            $wh == 'null' ? '' : $wh,
            $ch == 'null' ? '' : $ch,
            $bs == 'null' ? '' : $bs,
            $sert == 'null' ? '' : $sert,
            $BatchID == 'null' ? '' : $BatchID,
            0,
            10000000
        );
        $view = "print_report_traceability_traceability_new_farmer";
        ini_set('memory_limit',-1);
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=Print_Report_Farmer_Transaction_".$this->get('startd')."_to_".$this->get('end').".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //echo "<pre>".print_r($data['data'],1);exit;
        //var_dump($data);die();
        $this->load->view($view, $data);
    }
} //end of class

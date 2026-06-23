<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Batching extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('traceability_api/m_batching');
        // $this->load->helper('traceability_neo');
    }

    public function grid_main_get() 
    {
        //sort
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');
        
        $pSearch = array(
            'ArrFilter'                      => $this->get('ArrFilter'),
            'TextFilterSupplyBatchNumber'    => filter_var($this->get('TextFilterSupplyBatchNumber'), FILTER_SANITIZE_STRING),
            'TextFilterSupplyBatchStatusID'  => filter_var($this->get('TextFilterSupplyBatchStatusID'), FILTER_SANITIZE_STRING),
            'TextFilterStartSupplyBatchDate' => filter_var(preg_replace("([^0-9-])","",$this->get('TextFilterStartSupplyBatchDate'))),
            'TextFilterEndSupplyBatchDate'   => filter_var(preg_replace("([^0-9-])","",$this->get('TextFilterEndSupplyBatchDate'))),
        );

        $data = $this->m_batching->GetGridMain($pSearch,$start,$limit,$sortingField,$sortingDir);

        $this->response($data, 200);
    }

    public function data_supplychain_batch_post()
    {
        $varPost = $this->post();
        $paramPost = array();
        
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_Batching_MainForm-FormBasicData-", '', $key);
            $paramPost[$keyNew] = $value;
        }

        $proses = $this->m_batching->InsertSupplychainBatch($paramPost);
        
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function supplychain_batch_form_open_get()
    {
        $SupplyBatchID = (int) $this->get('SupplyBatchID');
        $data          = $this->m_batching->SupplychainBatchFormOpen($SupplyBatchID);

        $this->response($data, 200);
    }

    public function data_supplychain_batch_transaction_main_grid_get()
    {
        $SupplyBatchID = (int) $this->get('SupplyBatchID');
        if(!empty($SupplyBatchID)) {
            $data                  = $this->m_batching->GetSupplychainBatchTransaction($SupplyBatchID);
        } 
       
        $this->response($data, 200);
    }

    public function grid_transaction_get() 
    {
        //sort
        $sorting      = json_decode($this->get('sort'));
        if (isset($sorting[0]->property)) $sortingField = isset($sorting[0]->property) ? $sorting[0]->property : ''; else $sortingField = null;
        if (isset($sorting[0]->direction)) $sortingDir = isset($sorting[0]->direction) ? $sorting[0]->direction : ''; else $sortingDir = null;
        $start        = (int) $this->get('start');
        $limit        = (int) $this->get('limit');
        
        $pSearch = array(
            // 'TransTypeName'        => filter_var($this->get('TransTypeName'), FILTER_SANITIZE_STRING),
            'MemberName'           => filter_var($this->get('MemberName'), FILTER_SANITIZE_STRING),
            'StartTransactionDate' => filter_var(preg_replace("([^0-9-])","",$this->get('StartTransactionDate'))),
            'EndTransactionDate'   => filter_var(preg_replace("([^0-9-])","",$this->get('EndTransactionDate'))),
            'SupplyType'           => filter_var($this->get('SupplyType'), FILTER_SANITIZE_STRING),
        );

        $data = $this->m_batching->GetGridTransactionMain($pSearch,$start,$limit,$sortingField,$sortingDir);
        
        $this->response($data, 200);
    }

    public function data_batch_transaction_post()
    {
        $varPost = $this->post();

        $paramPost = array();
        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_Batching_MainForm-FormBasicData-", '', $key);
            $paramPost[$keyNew] = $value;
        }
        
        $proses = $this->m_batching->InsertBatchTransaction($paramPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_supplychain_batch_transaction_delete()
    {
        $varDelete = $this->delete();
        
        $proses = $this->m_batching->DeleteSupplychainBatchTransaction($varDelete);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_supplychain_batch_processing_main_grid_get()
    {
        $SupplyBatchID = (int) $this->get('SupplyBatchID');

        $data          = $this->m_batching->GetSupplychainBatchProcessing($SupplyBatchID);

        $this->response($data, 200);
    }

    public function data_supplychain_batch_processing_form_open_get()
    {
        $SupplyBatchProcessingID = (int) $this->get('SupplyBatchProcessingID');
        $data                    = $this->m_batching->SupplychainBatchProcessingFormOpen($SupplyBatchProcessingID);

        $this->response($data, 200);
    }

    public function data_supplychain_batch_processing_post()
    {
        $return    = array();
        $varPost   = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Processing_neo_WinFormDataProcessingStep-Form-", '', $key);

            $paramPost[$keyNew] = $value;
            
        }

        if($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->m_batching->InsertSupplychainBatchProcessing($paramPost);
        } else {
            $proses = $this->m_batching->UpdateSupplychainBatchProcessing($paramPost);
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_supplychain_batch_processing_delete()
    {
        $varDelete = $this->delete();

        $proses = $this->m_batching->DeleteSupplychainBatchProcessing($varDelete);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_supplychain_batch_close_post()
    {
        $return    = array();
        $varPost   = $this->post();

        $proses = $this->m_batching->UpdateSupplychainBatchClose($varPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_supplychain_batch_complete_post()
    {
        $return    = array();
        $varPost   = $this->post();

        $proses = $this->m_batching->UpdateSupplychainBatchComplete($varPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_supplychain_batch_delete()
    {
        $SupplyBatchID = (int) $this->delete('SupplyBatchID');

        $proses = $this->m_batching->DeleteSupplychainBatch($SupplyBatchID);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function delete_supplychain_batch_api_post()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $post = json_decode(json_encode($this->post()), true);
        if(empty($data)) {
            $post = json_decode(file_get_contents('php://input'), true);
            $data = $post;
        }
        
        $name = $data['SupplyBatchID'] . '-' . strtotime(date('YmdHis'));
        $dir = FCPATH . 'backup_traceability_delete_batch';
        if(!is_dir($dir)) {
          make_directory($dir, 0777, true);
        }
        if(!write_file($dir.'/'.$name.'.json',json_encode($data))) {} else {}
        
        $data = $this->post(null);
        if ($data) {
            $batch = $this->m_batching->DeleteSupplychainBatchAPI($data);
            if ($batch) {
                return $this->response($batch);
            }
        } else {
            return $this->response(array('success' => false, 'error' => 'Data post empty !'), 401);
        }
    }

    public function batch_status_get()
    {
        $data = $this->m_batching->GetBatchStatus();

        $this->response($data, 200);
    }

    public function processing_step_get()
    {
        $data = $this->m_batching->GetProcessingStep();

        $this->response($data, 200);
    }

    public function processing_step_group_get()
    {
        $data = $this->m_batching->GetProcessingStepGroup();

        $this->response($data, 200);
    }

    public function roaster_get()
    {
        $data = $this->m_batching->getRoaster();

        $this->response($data, 200);
    }

    public function check_batch_get()
    {
        if ($_SESSION['is_admin'] == "1") {
            $getSettings = 1;
        } else {
            $getSettings = getSettings($_SESSION['ObjID']);
        }

        if (!empty($getSettings)) {
            $results['success'] = true;
            $results['status']  = 1;

            if ($_SESSION['is_admin'] != "1") {
                $batch = $getSettings['settingTransaction']->PembelianBatch;

                $results['message'] = $batch != 0 ? "" : lang("Sorry, Batch not allowed");
                $results['status']  = $batch != 0 ? 1 : 2;
            }
        }

        $this->response($results, 200);
    }
}
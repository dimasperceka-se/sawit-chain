<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Transaction_new extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('traceability_new/mtransaction');
        $this->load->model('traceability_new/m_delivery');
        $this->load->helper('traceability_delivery');
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
            'TextFilterTransTypeName'        => filter_var($this->get('TextFilterTransTypeName'), FILTER_SANITIZE_STRING),
            'TextFilterTransSupplyID'        => filter_var($this->get('TextFilterTransSupplyID'), FILTER_SANITIZE_STRING),
            'TextFilterMemberName'           => filter_var($this->get('TextFilterMemberName'), FILTER_SANITIZE_STRING),
            'TextFilterStartDateTransaction' => filter_var(preg_replace("([^0-9-])","",$this->get('TextFilterStartDateTransaction'))),
            'TextFilterEndDateTransaction'   => filter_var(preg_replace("([^0-9-])","",$this->get('TextFilterEndDateTransaction'))),
        );

        $data = $this->m_transaction_new->GetGridMain($pSearch,$start,$limit,$sortingField,$sortingDir);

        $this->response($data, 200);
    }


    public function farmers_get() 
    {
        $search = $this->get();
        $getData = $this->m_transaction_new->GetFarmers($search)->result_array();
        $pushData = [];

        if (!empty($getData)) {
            foreach ($getData as $key => $value) {
                if ($value['DateOfBirth']) {
                    $dateNow   = new DateTime('today');
                    $birtDate  = new DateTime($value['DateOfBirth']);
                    $calculate = $birtDate->diff($dateNow)->y;

                    $value['Age'] = $calculate;
                }

                switch ($value['Gender']) {
                    case 'm':
                        $gender = 'male';
                        break;
                    default:
                        $gender = 'female';
                        break;
                }

                if ($value['PartnerID'] == NULL) {
                    $value['PartnerID'] = '-';
                }

                unset($value['Gender']);
                $value['Gender'] = $gender;

                array_push($pushData, $value);
            }
        }

        $data['data'] = $pushData;

        $this->response($data, 200);
    }

    public function save_supplychain_transaction_post()
    {
        $varPost = $this->post();

        $proses = $this->m_transaction_new->InsertSupplychainTransaction($varPost);
        
        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function transaction_form_open_get()
    {
        $SupplyTransID     = (int) $this->get('SupplyTransID');
        $data          = $this->m_transaction_new->TransactionFormOpen($SupplyTransID);

        $this->response($data, 200);
    }

    public function unit_get()
    {
        $data = $this->m_transaction_new->GetUnit();

        $this->response($data, 200);
    }

    public function data_weight_unit_main_grid_get()
    {
        $SupplyTransID = (int) $this->get('SupplyTransID');
        $data          = $this->m_transaction_new->GetWeightUnitTransaction($SupplyTransID);

        $this->response($data, 200);
    }

    public function data_weight_unit_form_open_get()
    {
        $TransDetailID = (int) $this->get('TransDetailID');
        $data          = $this->m_transaction_new->GetWeightUnitTransactionDetail($TransDetailID);

        $this->response($data, 200);
    }

    public function data_transaction_detail_input_post()
    {
        $return    = array();
        $varPost   = $this->post();
        $paramPost = array();

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Transaction_neo_WinFormDataUnit-Form-", '', $key);
            $paramPost[$keyNew] = $value;
        }

        if($paramPost['OpsiDisplay'] == 'insert') {
            $proses = $this->m_transaction_new->InsertTransactionDetail($paramPost);
        } else {
            $proses = $this->m_transaction_new->UpdateTransactionDetail($paramPost);
        }

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_transaction_detail_delete() 
    {
        $varDelete = $this->delete();

        $proses = $this->m_transaction_new->DeleteTransactionDetail($varDelete);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }

    }

    public function data_post()
    {
        $return    = array();
        $varPost   = $this->post();
        $paramPost = array();

        if ($varPost['OpsiDisplay'] == 'update') {
            unset($varPost['SupplyTransID']);  
        } else {
            unset($varPost['Koltiva_view_Traceability_Transaction_neo_MainForm-FormBasicData-SupplyTransID']);
            $paramPost['SupplyTransID'] = $varPost;
        }

        foreach ($varPost as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_Transaction_neo_MainForm-FormBasicData-", '', $key);
            $paramPost[$keyNew] = $value;
        }

        $proses = $this->m_transaction_new->UpdateTransaction($paramPost);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }
    }

    public function data_delete() 
    {
        $SupplyTransID = (int) $this->delete('SupplyTransID');

        $proses = $this->m_transaction_new->DeleteTransaction($SupplyTransID);

        if($proses['success'] == true) {
            $this->response($proses, 200);
        } else {
            $this->response($proses, 400);
        }

    }

    public function seaweed_type_get()
    {
        $data = $this->m_transaction_new->GetSeaweedType($this->get('SupplyTransID'));

        $this->response($data, 200);
    }

    public function unit_id_get()
    {
        $data = $this->m_transaction_new->GetUnitID();

        $this->response($data, 200);
    }

    public function trans_type_id_get()
    {
        $data = $this->m_transaction_new->GetTransTypeID();

        $this->response($data, 200);
    }

    public function quality_id_get()
    {
        $data = $this->m_transaction_new->GetQualityID();

        $this->response($data, 200);
    }

    public function comboPackage_get()
    {
        $data = $this->mdelivery_neo->ListPackage();

        $this->response($data, 200);
    }
}
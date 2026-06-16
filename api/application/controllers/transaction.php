<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('coop/mtransaction');
    }

    function coop_transactions_get() {
        $key = $this->get('key');
        $transactions = $this->mtransaction->readTransactions($key, $this->get('start'), $this->get('limit'));
        if ($transactions)
            $this->response($transactions, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transactions!'), 404);
    }

    function coop_transaction_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $transaction = $this->mtransaction->readTransaction($this->get('id'));
        if ($transaction)
            $this->response($transaction, 200);
        else
            $this->response(array('error' => 'Transaction could not be found'), 404);
    }

    function coop_transaction_post() {

        $transaction = $this->mtransaction->createTransaction($this->post('memberTransactionType'), $this->post('memberTransactionDate'), $this->post('cashSourceID'), $this->post('memberSavingID'), $this->post('memberTransactionAmount'), $this->post('memberTransactionRemark'), $_SESSION['userid']);

        if ($transaction) {
            $this->response($transaction, 200);
        } else {
            $this->response(array('error' => 'Transaction could not be added'), 404);
        }
    }

    function coop_transaction_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $update = $this->mtransaction->updateTransaction($this->put('memberTransactionType'), $this->put('memberTransactionDate'), $this->put('cashSourceID'), $this->put('memberSavingID'), $this->put('memberTransactionAmount'), $this->put('memberTransactionRemark'), $_SESSION['userid'], $this->put('id'));
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Transaction could not be edited'), 404);
    }

    function coop_transaction_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $delete = $this->mtransaction->deleteTransaction($this->delete('id'));
        if ($delete)
            $this->response($delete, 200);
        else
            $this->response(array('error' => 'Transaction could not be deleted'), 404);
    }

    function coop_transaction_member_get() {
        $id = $this->get('id');
        if ($id) {
            $data = $this->mtransaction->getDetailMember($id);
            if ($data)
                $this->response($data, 200);
            else
                $this->response(array('error' => 'Couldn\'t find any Member!'), 404);
        }
    }

    function combo_transactiontype_get() {
        $data = $this->mtransaction->getComboTransactionType();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transaction!'), 404);
    }

    function combo_cashsource_get() {
        $data = $this->mtransaction->getComboCashsource();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Cashsource!'), 404);
    }

    function combo_membersaving_get() {
        $data = $this->mtransaction->getComboMemberSaving($this->get('MemberID'),$this->get('savingTypeID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Cashsource!'), 404);
    }

    function get_trans_number_get()
    {
        $n = getTransactionNumber2(1,$this->get('id'));
        $this->response(array('number'=>$n), 200);
    }

    function get_saving_member_get()
    {
          $data = $this->mtransaction->getSavingMember($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    function get_cek_paid_get()
    {

    }
    
    function get_trans_detail_get() {
        $data = $this->mtransaction->getComboMemberSaving();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Cashsource!'), 404);
    }
    
    function combo_transtype_get() {
        $data = $this->mtransaction->getComboTransType();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Cashsource!'), 404);
    }

    function add_deposit_post() {
        
        $form = $this->post();
        $form['remark'] = '';
        
        if($this->post('initial_saving_cb')=='on')
        {
            $exe = $this->mtransaction->createInitialPayment($form);
        } else  if($form['memberLoanID'] > 0){
            $this->response($form, 200);
            $exe = $this->mtransaction->createLoanInstallment($form);
        } else if(intval($form['depositMemberSavingID'])==99) {
            //uang pangkal
            $exe = $this->mtransaction->createUangPangkal($form);
        } else {
            $form['memberSavingID'] = $this->post('depositMemberSavingID');
            $exe = $this->mtransaction->createTransaction($form,1);
        }

            //die;
        if ($exe['success']) {
            $this->response($exe, 200);
        } else {
            $this->response(array('error' => 'Failed to add!'), 404);
        }
    }
    
    function add_withdrawal_post() {
        $data = $this->post();
        $form = array(
            // 'memberID' => NULL,
            'memberSavingID' => $data['memberSavingID'],
            'name' => NULL,
			'MemberTransactionDate' => $data['MemberTransactionDate'],
            'identityNumber' => NULL,
            'address' => NULL,
            'remark' => $data['remark'],
            'ApprovedBy' => isset($data['UserId']) ? $data['UserId'] : null,
            'memberTransactionAmount' => str_replace(',','',$data['amount'])
        );
        $exe = $this->mtransaction->createTransaction($form,2);
        if ($exe) {
            $this->response($exe, 200);
        } else {
            $this->response(array('error' => 'Failed to add!'), 404);
        }
    }
    
    function getmembersaving_get() {
        $id = $this->get('id');
        
        $data = $this->mtransaction->getComboMemberSaving($id);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Cashsource!'), 404);
    }
    
    function getmemberloan_get() {
        $id = $this->get('id');
        
        $data = $this->mtransaction->getComboMemberLoan($id);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Cashsource!'), 404);
    }
    
    function cancel_trans_post() {
        if (!$this->post('id')) {
            $this->response(NULL, 400);
        }
        
        $delete = $this->mtransaction->cancelTransaction($this->post('id'));
        
        if ($delete) {
             $this->response($delete, 200); 
        } else {
            $this->response(array('error' => 'Transaction can not be canceled'), 404);
        }
    }
    
    function lasttrans_get() {
        
        //saving account id
        $id = $this->get('id');
        $MemberTransactionType = $this->get('MemberTransactionType');
        
        $data = $this->mtransaction->getMemberLastTrans($id,$MemberTransactionType);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Cashsource!'), 404);
    }
    
    function paymentplan_get() {
        
        //saving account id
        $id = $this->get('id');
        
        $data = $this->mtransaction->getLoanPaymentPlan($id);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }
    
    
    /**
     * Fungsi-fungsi untuk record booking
     */
    function add_deposit_recbook_post() {
        $form = $this->post();
        if($form['source'] === 'cash') { $form['source'] = 1; }
        
        $exe = $this->mtransaction->createRecBook($form,1);
        die;
        if ($exe) {
            $this->response($exe, 200);
        } else {
            $this->response(array('error' => 'Failed to add!'), 404);
        }
    }
    
    function add_withdrawal_recbook_post() {
        $form = $this->post();
        if($form['to'] === 'cash') { $form['to'] = 1; }
        $exe = $this->mtransaction->createRecBook($form,2);
        
        if ($exe) {
            $this->response($exe, 200);
        } else {
            $this->response(array('error' => 'Failed to add!'), 404);
        }
    }
    
    function todayrec_get($type = 1 ) {
        $key = $this->get('key');
        $transactions = $this->mtransaction->readRecTransactions($type, $key, $this->get('start'), $this->get('limit'));
        if ($transactions)
            $this->response($transactions, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transactions!'), 404);
    }

    function actualbalance_get()
    {
        //cash count cashier module
        $d = explode('-', $this->get('date'));
        $transactions = $this->mtransaction->readActualBalanceTrans($this->get('userid'),$d[2].'-'.$d[1].'-'.$d[0]);
        $this->response($transactions, 200);

    }

    function coop_limit_get()
    {   
        $amount = $this->get('amount');
        $transactions = $this->mtransaction->readLimit($_SESSION['userid'],$amount);
        $this->response($transactions, 200);
    }

    function coop_approval_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');
        $approve = $this->mtransaction->approval($username,$password);
        $this->response($approve, 200);
    }
    
    function combo_coa_get() {
        $type = $this->get('type');
        $trans = $this->get('trans'); //1. CASH 2. NON CASH
        $payment = $this->get('payment');
        $search = $this->get('query');
        $data = $this->mtransaction->getComboCoa($type,$trans,$payment,$search);
        // echo $this->db->last_query();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Accounts!'), 404);
    }
}

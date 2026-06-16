<?php

//defined('BASEPATH') OR exit('No direct script access allowed');

class Loan extends REST_Controller {

    public $_output = array('success' => false, 'msg' => 'Data is not valid'); //response data
    public $_num = 401; //response header

    public function __construct() {
        parent::__construct();
        $this->load->model('loan/mloantype', '_typemodel');
        $this->load->model('loan/mloan', '_model');
    }

    public function add_post() {
        $data = $this->post();
        $add = $this->_model->add($data,$_SESSION['userid']);

        if($add){
            $this->_num = 200;
            $this->_output = $add;
        }
        return $this->response($this->_output,  $this->_num);
    }

    /**
     *
     * Fungsi untuk Loan Type
     */

    public function getDataType_get() {

        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = 'loanTypeName';
        $dir = 'DESC';

        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array();

        $data = $this->_typemodel->getData($start,$limit,$sort,$dir,$filter);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    function getTypeLoanByID_get() {
        $id = $this->get('id');
        $data = $this->_typemodel->getById($id);
        if($data){
            $this->_num = 200;
            $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));
        }
        return $this->response($this->_output,  $this->_num);
    }

    function getLoanTypeMemberByID_get()
    {
        $id = $this->get('id');
        $data = $this->_typemodel->getLoanTypeMember($id);
        if($data){
            $this->_num = 200;
            $this->_output = array('success' => true, 'data' => $data);
        }
        return $this->response($this->_output,  $this->_num);
    }

    public function getTypeByID_get() {
        $id = $this->get('id');
        $data = $this->_typemodel->getById($id);
        if($data){
            $this->_num = 200;
            $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));
        }
        return $this->response($this->_output,  $this->_num);
    }

    public function addloantype_post() {
        $data = $this->post();
//        var_dump($data);
        $add = $this->_typemodel->add($data);
        // print_r($data);exit;
//        $add=$data;
        if($add){
            $this->_num = 200;
            $this->_output = $add;
        }
        return $this->response($this->_output,  $this->_num);
    }

    public function addtype_post() {
        $data = $this->post();
        $add = $this->_typemodel->add($data);
        // print_r($data);exit;
        if($add){
            $this->_num = 200;
            $this->_output = $add;
        }
        return $this->response($this->_output,  $this->_num);
    }

    public function edittype_put($id) {
        $data = $this->put();
        $edit = $this->_typemodel->edit($id,$data);
        if($edit){
            $this->_num = 200;
            $this->_output = $edit;
        }
        return $this->response($this->_output,  $this->_num);
    }

    public function editloantype_put($id)
    {
        $data = $this->put();
        $edit = $this->_typemodel->editLoanType($data);
        if($edit){
            $this->_num = 200;
            $this->_output = $edit;
        }
        return $this->response($this->_output,  $this->_num);
    }

    function getLoanData_get()
    {
        $id = $this->get('id');

        $qLoan = $this->db->get_where('coop_member_loan',array('MemberLoanID'=>$id))->row();
        $angsuran = ($qLoan->MemberLoanProposedAmount/$qLoan->MemberLoanTotalTenor);
        $d = array(
                'cicilanKe'=>1,
                'angsuran'=>number_format($angsuran),
                'denda'=>number_format(0)
            );
        $d['total'] = $angsuran+$d['denda'];
        return $this->response($d,  200);
    }

    public function delete_delete($id) {
        $data = $this->delete();
        $execute = $this->_model->delete($id);
        if($execute){
            $this->_num = 200;
            $this->_output = $execute;
        }
        return $this->response($this->_output,  $this->_num);
    }

    /**
     * Fungsi untuk member loan
     */
    public function getData_get() {

        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = '';
        $dir = 'DESC';

        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array();

        $data = $this->_model->getData($start,$limit,$sort,$dir,$filter);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    public function getMemberLoan_get() {
                ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = '';
        $dir = 'DESC';

        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array();

        $data = $this->_model->getMemberLoan($start,$limit,$sort,$dir,$filter);
        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    public function getMemberLoanProposal_get() {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = '';
        $dir = 'DESC';
        
        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }
        
        $filter = array();
        if($this->input->get('memberName') != ''){ $filter['coop_member.name'] = $this->input->get('memberName'); }
        if($this->input->get('loanStatus') != ''){ $filter['coop_member_loan.MemberLoanStatus'] = $this->input->get('loanStatus'); }
        if($this->input->get('loanType') != ''){ $filter['coop_member_loan.LoanTypeID'] = $this->input->get('loanType'); }
        
        $data = $this->_model->getMemberLoanProposal($start,$limit,$sort,$dir,$filter);
        
        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);
        
        return $this->response($this->_output,  $this->_num);
    }
    
    public function getProposedMemberLoan_get() {

        $start = $this->get('start');
        $limit = $this->get('limit');
        $no_pinjaman = $this->get('no_pinjaman');
        $sort = '';
        $dir = 'DESC';

        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array();

        $data = $this->_model->getProposedMemberLoan($start,$limit,$sort,$dir,$filter,$no_pinjaman);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    function getapprovedmemberloan_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $no_pinjaman = $this->get('no_pinjaman');
        $sort = '';
        $dir = 'DESC';

        if($this->get('sort')){
            $sort = json_decode($this->get('sort'),true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array();

        $data = $this->_model->getApprovedMemberLoan($start,$limit,$sort,$dir,$filter,$no_pinjaman);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    function GetNotifHeader_get()
    {
        $data = $this->_model->GetNotifHeader();
        $this->response($data, 200);
    }

    public function SetReadLoanNotif_post($idNotif=null,$memberLoanID=null)
    {
        //set notif from unread to read
        $memberLoanID = $this->input->post('memberLoanID');
        $qnotif = $this->db->get_where('ktv_notifikasi',array('memberLoanID'=>$memberLoanID));
        if($qnotif->num_rows()>0)
        {
            $rNotif = $qnotif->row();

             //set to read
            $this->db->where('memberLoanID',$memberLoanID);
            $this->db->update('ktv_notifikasi',array('Status'=>1)); //read

            //redirect to
            if($rNotif->Type==1)
            {
                //loan approval
               // redirect(site_url().'/loan/loan_member/index');
            } else {

            }

        } else {
            echo 'data not found';
        }

    }

    public function getLoanType_get() {

        $id = $this->get('id');
        $data = $this->_typemodel->getById($id);
        if($data){
            $this->_num = 200;
            $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));
        }
        return $this->response($this->_output,  $this->_num);
    }

    public function getcombomember_get() {

        $data = $this->_model->getComboMember();

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any member!'), 404);
    }

    public function getcombotype_get() {

        $data = $this->_model->getComboLoanType();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any type!'), 404);
    }

    public function getMemberData_get() {

        $id = $this->get('id');
        $data = $this->_model->getMemberById($id);
        if($data){
            $this->_num = 200;
            $this->_output = array('success' => true, 'data' => $data, 'total' => count($data));
        }
        return $this->response($this->_output,  $this->_num);
    }

    public function runSimulator_post()
    {
        $loanTypeID = $this->post('loanTypeIDSimulator');
        $amount = $this->post('loanMemberAmountSimulator');
        $tenor = $this->post('loanMemberTotalTenorSimulator');
        $duedateLoan = explode("-", $this->post('duedateLoan')); //21-10-2015
        $tgl = $duedateLoan[2].'-'.$duedateLoan[1].'-'.$duedateLoan[0];

        $q = $this->db->get_where('coop_loan_type',array('loanTypeID'=>$loanTypeID))->row();
//        var_dump($q);

        if($amount<$q->loanTypeMinAmount)
        {
            $this->_num = 200;
            $this->_output = array('success' => false, 'message'=> 'Minimal Loan Amount is '.number_format($q->loanTypeMinAmount),'data' => null);
            return $this->response($this->_output,  $this->_num);
        }

        if($amount>$q->loanTypeMaxAmount)
        {
            $this->_num = 200;
            $this->_output = array('success' => false, 'message'=> 'Limit Loan Amount is '.number_format($q->loanTypeMaxAmount),'data' => null);
            return $this->response($this->_output,  $this->_num);
        }

        if($tenor<$q->loanTypeMinTenor)
        {
            $this->_num = 200;
            $this->_output = array('success' => false, 'message'=> 'Min Tenor is '.number_format($q->loanTypeMinTenor).' Month(s)','data' => null);
            return $this->response($this->_output,  $this->_num);
        }

        if($tenor>$q->loanTypeMaxTenor)
        {
            $this->_num = 200;
            $this->_output = array('success' => false, 'message'=> 'Max Tenor is '.number_format($q->loanTypeMaxTenor).' Month(s)','data' => null);
            return $this->response($this->_output,  $this->_num);
        }

        $margin = 0;
        $loanTypeClientSharing = 0;
        $loanTypeCooperativeSharing = 0;

        if($q->loanTypeIslamic==1)
        {
            //syariah
            if($q->loanTypeProfitType==1)
            {
                //Profit Sharing
                 $pokok = $amount/$tenor;
                 $pinjaman = $amount;
                 $data = array();
                 for($i=1;$i<=$tenor;$i++)
                        {
//                            $interest = ($amount*($q->loanTypeInterestAmount/100))/$tenor;
                            $duedate = date('d-m-Y', strtotime("+ $i MONTHS", strtotime($tgl)));
                            $pinjaman = $pinjaman-$pokok;
                            $d = array(
                                'term'=>$i,
                                'loan'=>number_format($pinjaman),
                                'amount'=>number_format($pokok),
                                'interest'=>0,
                                'installment'=>number_format($pokok),
                                'due'=>$duedate
                            );
                            array_push($data, $d);
                        }

                $loanTypeClientSharing = $q->loanTypeClientSharing;
                $loanTypeCooperativeSharing = $q->loanTypeCooperativeSharing;
            } else if($q->loanTypeProfitType==2)
                {
                    //Fixed Margin
                    $profit = $q->loanTypeInterestAmount/100;
                    $margin = $amount*$profit;
                    $pinjaman = $amount+$margin;
                    $pokok = $amount/$tenor;
                    $angsuran = $pinjaman/$tenor;
                    $interest = $margin/$tenor;
                    $data = array();
                    for($i=1;$i<=$tenor;$i++)
                        {
//                            $interest = ($amount*($q->loanTypeInterestAmount/100))/$tenor;
                            $duedate = date('d-m-Y', strtotime("+ $i MONTHS", strtotime($tgl)));
                            $pinjaman = $pinjaman-$angsuran;
                            $d = array(
                                'term'=>$i,
                                'loan'=>number_format($pinjaman),
                                'amount'=>number_format($pokok),
                                'interest'=>number_format($interest),
                                'installment'=>number_format($angsuran),
                                'due'=>$duedate
                            );
                            array_push($data, $d);
                        }

                    $margin = number_format($margin);
                }
        } else {
            //konvelsional
                    if($q->interestTypeID==1)
                    {
                        //Bunga Tetap
            //Bunga per bulan = Jumlah pinjaman x Suku bunga per tahun / 12
            //Total Bunga = Jumlah pinjaman x (Suku bunga per tahun / 12) x Lama meminjam dalam bulan
            //            $i=0;
                        $amountMonth = $amount/$tenor;
                        $data = array();
                        $pinjaman = $amount;
                        for($i=1;$i<=$tenor;$i++)
                        {
                            $interest = ($amount*($q->loanTypeInterestAmount/100))/$tenor;
                            $duedate = date('d-m-Y', strtotime("+ $i MONTHS", strtotime($tgl)));
                            $pinjaman = $pinjaman-($amountMonth);
                            $d = array(
                                'term'=>$i,
                                'loan'=>number_format($pinjaman),
                                'amount'=>number_format($amountMonth),
                                'interest'=>number_format($interest),
                                'installment'=>number_format($amountMonth+$interest),
                                'due'=>$duedate
                            );
                            array_push($data, $d);
                        }

                    } else if($q->interestTypeID==2)
                        {
                            //Bunga Efektif

//                            berhutang Rp 100.000.000,- dengan bunga efektif 12% per tahun, dengan cicilan pokok Rp 10.000.000,- per bulan.
//                            Maka:
//                            Bulan ke-1 bunganya 1% x Rp 100.000.000,- = Rp 1.000.000,-
//                            Bulan ke-2 bunganya 1% x Rp 90.000.000,- = Rp 900.000,-
//                            Bulan ke-3 bunganya 1% x Rp 80.000.000,- = Rp 800.000,-
//                            dan seterusnya..
                            $data = array();
                            $hutang = $amount;
                            $angsuranpokok = $amount/$tenor;

                            $duedate = date('d-m-Y', strtotime("+ 1 MONTHS", strtotime($tgl)));

                            $interest = $hutang*($q->loanTypeInterestAmount/100);
                            $instal = $angsuranpokok+$interest;
                            $d = array(
                                'term'=>1,
                                'loan'=>number_format($amount),
                                'amount'=>number_format($angsuranpokok),
                                'interest'=>number_format($interest),
                                'installment'=>number_format($instal),
                                'due'=>$duedate
                            );
                            array_push($data, $d);

                            $hutang = $hutang-$angsuranpokok;
//                            $sisa = $hutang-$angsuranpokok;
                            for($i=2;$i<=$tenor;$i++)
                            {
                                $interest = $hutang*($q->loanTypeInterestAmount/100);
                                $duedate = date('d-m-Y', strtotime("+ $i MONTHS", strtotime($tgl)));

                                $instal = $angsuranpokok+$interest;
                                $hutang = $hutang-$angsuranpokok;

                                $d = array(
                                   'term'=>$i,
                                   'loan'=>number_format($hutang),
                                   'amount'=>number_format($angsuranpokok),
                                   'interest'=>number_format($interest),
                                   'installment'=>number_format($instal),
                                   'due'=>$duedate
                               );
                               array_push($data, $d);
//                               break;
                            }
                        } else if($q->interestTypeID==3)
                            {
                                $data = array();
                                $sisahutang = $amount;

                                if($q->loanTypeInterestDuration==1)
                                {
                                    //suku bunga bulanan
                                    $bunga = $q->loanTypeInterestAmount/100;
                                    $bungpokok = $bunga;
                                } else if($q->loanTypeInterestDuration==2){
                                        //suku bunga tahunan
                                        $bunga = ($q->loanTypeInterestAmount/12)/100;
                                        $bungpokok = $q->loanTypeInterestAmount/100;
                                   } else {
                                        $bunga = $q->loanTypeInterestAmount/100;
                                        $bungpokok = $bunga;
                                    }

                                //anuitas
                                    $jumlahpinjaman = $amount;
                                for($i=1;$i<=$tenor;$i++)
                                {
                                    $interest = ($sisahutang*$bungpokok)/12;
//                                     P x I/12 x 1/(1-(1+i/12)m)
                                    $angsuran = ($jumlahpinjaman*$bunga) / (1-1/exp($tenor * log((1+$bunga))  ));
                                    $duedate = date('d-m-Y', strtotime("+ $i MONTHS", strtotime($tgl)));

//                                    echo $sisahutang.' '.$interest.' '.$bungpokok.' '.$angsuran.' '.$tenor.' - ';

                                    $sisahutang = $sisahutang-($angsuran-$interest);
//                                    1	265,983.94	150,000.00	415,983.94	11,734,016.06

//                                    echo $sisahutang;
                                    $d = array(
                                        'term'=>$i,
                                        'loan'=>number_format($sisahutang),
                                        'amount'=>number_format($angsuran-$interest),
                                        'interest'=>number_format($interest),
                                        'installment'=>number_format($angsuran),
                                        'due'=>$duedate
                                    );
                                    array_push($data, $d);
//                                    print_r($data);
//                                    if($i==2)
//                                    exit;
                                }
                            }
        }



//        $data = true;
        if($data){
            $this->_num = 200;
            $this->_output = array('success' => true, 'total'=>$i, 'data' => $data,'loanname'=>$q->loanTypeName,
                'margin'=>$margin,
                'loanTypeClientSharing'=> $loanTypeClientSharing,
                'loanTypeCooperativeSharing'=> $loanTypeCooperativeSharing);
        }
        return $this->response($this->_output,  $this->_num);
    }

    public function approve_post() {

        $id = $this->post('MemberLoanID');
        $amount   = str_replace(',', '', $this->post('MemberLoanApprovedAmount'));
        $tenor    = str_replace(',', '', $this->post('MemberLoanTotalTenor'));
        $apprisal = str_replace(',', '', $this->post('MemberLoanApprisal'));

        $installment = $this->_model->getLoanInstallment($id,$amount,$tenor,date('Y-m-d'));
        foreach($installment as $keys => $values) {
            $this->db->insert('coop_loan_installment',array(
                'LoanInstallmentDueDate' => $values['due'],
                'MemberLoanID' => $id,
                'LoanInstallmentValue' => $values['installment'],
                'LoanInstallmentTop' => $values['term']
            ));
        }

        $this->db->where('MemberLoanID',$id);
        $this->db->update('coop_member_loan',array('MemberLoanStatus'=>1));
        array('success' => true);

        return $this->response($this->_output,  $this->_num);
    }

    public function reject_post() {

        $id = $this->post('memberLoanID');
    }

    public function loaddetailmemberloan_post() {

        $data = $this->_model->getMemberLoanDetail($this->post('id'));

        if($this->post('plain') == true){
            return $this->response(array('success' => true, 'data' => $data),  200);
        } else {
            echo $this->load->view('member_loan',array('data' => $data));
        }

    }
}

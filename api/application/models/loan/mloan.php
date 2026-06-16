<?php

class Mloan extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($start = 0, $limit = 20, $sort = 'journalDate', $dir = 'DESC', $query = array()) {
        $this->db->select(array(
            'loanTypeID',
            'loanTypeName',
            'loanTypeInterestRate',
            'interestTypeName',
            'loanMinTenor',
            'loanMaxTenor'), FALSE
        );
        $this->db->from('coop_loan_type');
        $this->db->join('coop_interest_type', 'coop_interest_type.interestTypeID=coop_loan_type.interestTypeID', 'left');

        $total = $this->db->_compile_select();

        $total = $this->db->query($total)->num_rows();

        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }

    public function getMemberLoan($start = 0, $limit = 20, $sort = 'memberLoanNo', $dir = 'DESC', $query = array()) {
        $this->db->select(array(
            '*'), FALSE
        );
        $this->db->from('coop_member_loan');
        $this->db->join('coop_member','coop_member.memberID=coop_member_loan.memberID','left');
        $this->db->join('coop_loan_type','coop_loan_type.loanTypeID=coop_member_loan.loanTypeID','left');
        $this->db->join('coop_interest_type','coop_interest_type.InterestTypeID=coop_loan_type.InterestTypeID','left');

        //[21] where MemberLoanStatus = 'Awaiting Approval'
        $this->db->where('coop_member_loan.MemberLoanStatus', NULL);
        
        $total = $this->db->_compile_select();

        $total = $this->db->query($total)->num_rows();

        $this->db->limit($limit, $start);
        //$this->db->order_by($sort, $dir);

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }

    public function getMemberLoanProposal($start = 0, $limit = 20, $sort = 'memberLoanNo', $dir = 'DESC', $filter = array()) {
        $this->db->select(array(
            '*'), FALSE
        );
        $this->db->from('coop_member_loan');
        $this->db->join('coop_member','coop_member.memberID = coop_member_loan.memberID', 'left');
        $this->db->join('coop_loan_type','coop_loan_type.loanTypeID = coop_member_loan.loanTypeID','left');
        $this->db->join('coop_interest_type','coop_interest_type.InterestTypeID = coop_loan_type.InterestTypeID','left');

        $this->db->like($filter);
        
        $total = $this->db->_compile_select();

        $total = $this->db->query($total)->num_rows();

        $this->db->limit($limit, $start);

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }

    function getApprovedMemberLoan($start = 0, $limit = 20, $sort = 'memberLoanNo', $dir = 'DESC', $query = array(), $no_pinjaman=null)
    {
         $this->db->select(array(
            '*'), FALSE
        );
        $this->db->from('coop_member_loan');
        $this->db->join('coop_member','coop_member.memberID=coop_member_loan.memberID','left');
        $this->db->join('coop_loan_type','coop_loan_type.loanTypeID=coop_member_loan.loanTypeID','left');
        $this->db->where('coop_member_loan.memberLoanStatus',1);

        if($no_pinjaman!=null)
        {
            $this->db->like('MemberLoanNo', $no_pinjaman);
        }

        $total = $this->db->_compile_select();

        $total = $this->db->query($total)->num_rows();

        $this->db->limit($limit, $start);
        //$this->db->order_by($sort, $dir);

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }

    public function getProposedMemberLoan($start = 0, $limit = 20, $sort = 'memberLoanNo', $dir = 'DESC', $query = array(), $no_pinjaman=null) {
        $this->db->select(array(
            '*'), FALSE
        );
        $this->db->from('coop_member_loan');
        $this->db->join('coop_member','coop_member.memberID=coop_member_loan.memberID','left');
        $this->db->join('coop_loan_type','coop_loan_type.loanTypeID=coop_member_loan.loanTypeID','left');
        $this->db->where('coop_member_loan.memberLoanStatus',3);

        if($no_pinjaman!=null)
        {
            $this->db->like('MemberLoanNo', $no_pinjaman);
        }

        $total = $this->db->_compile_select();

        $total = $this->db->query($total)->num_rows();

        $this->db->limit($limit, $start);
        //$this->db->order_by($sort, $dir);

        $Q = $this->db->get();

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }

    public function getById($id) {
        $this->db->select(
                array(
                    '*'
                )
        );
        $this->db->from('coop_loan_type');
        $this->db->join('coop_interest_type','coop_interest_type.interestTypeID=coop_loan_type.interestTypeID','left');
        $this->db->where('loanTypeID', $id);

        $Q = $this->db->get();
        if ($Q->num_rows()) {
            return $Q->row_array();
        }

        return false;
    }

    public function getMemberById($id) {
        $this->db->select(
                array(
                    '*'
                )
        );
        $this->db->from('coop_member');
        $this->db->join('coop_member_type','coop_member_type.typeID=coop_member.typeID','left');
        $this->db->where('memberID', $id);

        $Q = $this->db->get();
        if ($Q->num_rows()) {
            return $Q->row_array();
        }

        return false;
    }

    public function add($data,$userid) {

        $number = getLoanNumber($data['loanTypeID']);
        $this->db->insert('coop_member_loan',array(
            'memberID' => $data['MemberID'],
            'memberLoanNo' => $number,
            'loanTypeID' => $data['loanTypeID'],
            'memberLoanProposedDate' => date('Y-m-d'),
            'memberLoanProposedAmount' => str_replace(',','', $data['MemberLoanProposedAmount']),
            'memberLoanTotalTenor' => $data['MemberLoanTotalTenor']
        ));

        if($this->db->_error_number()){
            if($this->db->_error_number() == 1062){
                return array('success' => false, 'msg' => 'Record already exist');
            }

            return array('success' => false, 'msg' => 'Failed add record');
        }

        //notifikasi
        //insertNotifLoan($this->db->insert_id(),$data['memberID'],$userid);

        return array('success' => true, 'msg' => 'Successfully add record');;
    }

    public function edit($id,$data) {
        $this->db->where('loanTypeID',$id);
        $this->db->update('coop_loan_type',$data);

        if($this->db->_error_number()){
            if($this->db->_error_number() == 1062){
                return array('success' => false, 'msg' => 'Record already exist');
            }

            return array('success' => false, 'msg' => 'Failed update record');
        }

        return array('success' => true, 'msg' => 'Successfully update record');;
    }

    public function delete($id) {

        $this->db->where('memberLoanID',$id);
        $this->db->delete('coop_member_loan');

        if($this->db->_error_number()){

            return array('success' => false, 'msg' => 'Failed delete record');
        }

        return array('success' => true, 'msg' => 'Successfully delete record');;
    }

    public function getComboMember() {
        $sql = "
            SELECT
                memberID,
                primaryNo,
                name
            FROM
                coop_member
            left join coop_member_type ON coop_member_type.typeID=coop_member.typeID
            where coop_member_type.coopID = ?
            AND coop_member.status = 1
        ";
        $query = $this->db->query($sql,array(getCoopID()));
        $result['data'] = $query->result_array();

        return $result;
    }

    public function getComboLoanType() {
        $sql = "
            SELECT
                loanTypeID,
                loanTypeName
            FROM
                coop_loan_type
            where active = 1 and coopID = ".getCoopID();
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        return $result;
    }

    public function getLoanTypeID($id) {

        $this->db->select('loanTypeID');
        $this->db->from('coop_member_loan');
        $this->db->where('memberLoanID',$id);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return $row->loanTypeID;
        }

        return false;
    }

    public function getLoanInstallment($id,$amount,$tenor,$date)
    {

        $loanTypeID = $this->getLoanTypeID($id);

        $duedateLoan = explode("-", $date); //21-10-2015
        $tgl = $duedateLoan[2].'-'.$duedateLoan[1].'-'.$duedateLoan[0];

        $q = $this->db->get_where('coop_loan_type',array('loanTypeID'=>$loanTypeID))->row();
//        var_dump($q);

        if($amount<$q->loanTypeMinAmount)
        {
            $this->_num = 200;
            $this->_output = array('success' => false, 'message'=> 'Minimal Loan Amount is '.number_format($q->loanTypeMinAmount),'data' => null);
            return $this->_output;
        }

        if($amount>$q->loanTypeMaxAmount)
        {
            $this->_num = 200;
            $this->_output = array('success' => false, 'message'=> 'Limit Loan Amount is '.number_format($q->loanTypeMaxAmount),'data' => null);
            return $this->_output;
        }

        if($tenor<$q->loanTypeMinTenor)
        {
            $this->_num = 200;
            $this->_output = array('success' => false, 'message'=> 'Min Tenor is '.number_format($q->loanTypeMinTenor).' Month(s)','data' => null);
            return $this->_output;
        }

        if($tenor>$q->loanTypeMaxTenor)
        {
            $this->_num = 200;
            $this->_output = array('success' => false, 'message'=> 'Max Tenor is '.number_format($q->loanTypeMaxTenor).' Month(s)','data' => null);
            return $this->_output;
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
                            $duedate = date('Y-m-d', strtotime("+ $i MONTHS", strtotime($tgl)));
                            $pinjaman = $pinjaman-$pokok;
                            $d = array(
                                'term'=>$i,
                                'loan'=>($pinjaman),
                                'amount'=>($pokok),
                                'interest'=>0,
                                'installment'=>($pokok),
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
                            $duedate = date('Y-m-d', strtotime("+ $i MONTHS", strtotime($tgl)));
                            $pinjaman = $pinjaman-$angsuran;
                            $d = array(
                                'term'=>$i,
                                'loan'=>($pinjaman),
                                'amount'=>($pokok),
                                'interest'=>($interest),
                                'installment'=>($angsuran),
                                'due'=>$duedate
                            );
                            array_push($data, $d);
                        }

                    $margin = ($margin);
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
                            $duedate = date('Y-m-d', strtotime("+ $i MONTHS", strtotime($tgl)));
                            $pinjaman = $pinjaman-($amountMonth);
                            $d = array(
                                'term'=>$i,
                                'loan'=>($pinjaman),
                                'amount'=>($amountMonth),
                                'interest'=>($interest),
                                'installment'=>($amountMonth+$interest),
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

                            $duedate = date('Y-m-d', strtotime("+ 1 MONTHS", strtotime($tgl)));

                            $interest = $hutang*($q->loanTypeInterestAmount/100);
                            $instal = $angsuranpokok+$interest;
                            $d = array(
                                'term'=>1,
                                'loan'=>($amount),
                                'amount'=>($angsuranpokok),
                                'interest'=>($interest),
                                'installment'=>($instal),
                                'due'=>$duedate
                            );
                            array_push($data, $d);

                            $hutang = $hutang-$angsuranpokok;
//                            $sisa = $hutang-$angsuranpokok;
                            for($i=2;$i<=$tenor;$i++)
                            {
                                $interest = $hutang*($q->loanTypeInterestAmount/100);
                                $duedate = date('Y-m-d', strtotime("+ $i MONTHS", strtotime($tgl)));

                                $instal = $angsuranpokok+$interest;
                                $hutang = $hutang-$angsuranpokok;

                                $d = array(
                                   'term'=>$i,
                                   'loan'=>($hutang),
                                   'amount'=>($angsuranpokok),
                                   'interest'=>($interest),
                                   'installment'=>($instal),
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
                                    $duedate = date('Y-m-d', strtotime("+ $i MONTHS", strtotime($tgl)));

//                                    echo $sisahutang.' '.$interest.' '.$bungpokok.' '.$angsuran.' '.$tenor.' - ';

                                    $sisahutang = $sisahutang-($angsuran-$interest);
//                                    1	265,983.94	150,000.00	415,983.94	11,734,016.06

//                                    echo $sisahutang;
                                    $d = array(
                                        'term'=>$i,
                                        'loan'=>($sisahutang),
                                        'amount'=>($angsuran-$interest),
                                        'interest'=>($interest),
                                        'installment'=>($angsuran),
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
        return $data;
    }

    public function getMemberLoanDetail($id) {

        $this->db->select('*');
        $this->db->from('coop_member_loan');
        $this->db->join('coop_member','coop_member.memberID=coop_member_loan.memberID','left');
        $this->db->join('coop_member_type','coop_member_type.typeID=coop_member.typeID','left');
        $this->db->join('coop_loan_type','coop_loan_type.loanTypeID=coop_member_loan.loanTypeID','left');
        $this->db->join('coop_interest_type','coop_interest_type.interestTypeID=coop_loan_type.interestTypeID','left');
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->row_array();
            return $result;
        }
        return false;
    }

    function GetNotifHeader()
    {
        $qNum = $this->db->query('select count(*) as total from ktv_notifikasi where Status=0')->row();

        $qList = $this->db->query('select Action,Message,NotifID,memberLoanID from ktv_notifikasi where Status=0');
        return array('total'=>$qNum->total,'data'=>$qList->result_array());
    }
}

?>

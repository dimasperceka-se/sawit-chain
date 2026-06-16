<?php

class Mtransaction extends CI_Model {
    
    public $coop;
    
    public function __construct() {
        parent::__construct();
        $this->coop = getCoopID();
    }
    
    function readTransactions($key, $start, $limit) {
        $sql = "
            SELECT 
                SQL_CALC_FOUND_ROWS 
                a.`memberTransactionID` AS id,
                b.`memberSavingNo`,
                a.`memberTransactionNumber`,
                a.`memberTransactionName`,
                if(a.`memberTransactionType` = 1, 'Setor', if(a.`memberTransactionType` = 2,'Tarik',if(a.`memberTransactionType` = 3,'Canceled',''))) AS memberTransactionType,
                if(a.`memberTransactionType` = 1, a.`memberTransactionAmount`,0) AS debet,
                if(a.`memberTransactionType` = 2, a.`memberTransactionAmount`,0) AS credit,
                a.`memberTransactionDate`,
                a.`memberTransactionNumber`,
                a.`memberTransactionAmount`,
                a.`memberTransactionRemark`
            FROM
                `coop_member_transaction` a
            LEFT JOIN coop_member_saving b ON b.`memberSavingID` = a.`memberSavingID`";
        
        if(strlen($key) > 0){
            $sql .= " WHERE d.name LIKE ?";
        }
        
        $sql .=" LIMIT ".$start."," .$limit;
        
        $query = $this->db->query(sprintf($sql, ''), array("%$key%", (int) $start, (int) $limit));
        
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $result['total'] = $row->row_array()['total'];

        return $result;
    }

    function readTransaction($id) {
        $sql = "
            SELECT 
                SQL_CALC_FOUND_ROWS 
                a.`memberTransactionID` AS id,
                a.`memberTransactionType`,
                a.`memberTransactionDate`,
                a.`cashSourceID`,
                b.`cashSourceName`,
                a.`memberSavingID`,
                CONCAT(d.`name`,' - ',c.`memberSavingNo`) AS memberSaving,
                a.`memberTransactionAmount`,
                a.`memberTransactionRemark` 
            FROM
                `coop_member_transaction` a
            LEFT JOIN coop_cash_source b ON b.`cashSourceID` = a.`cashSourceID`
            LEFT JOIN coop_member_saving c ON c.`memberSavingID` = a.`memberSavingID`
            LEFT JOIN coop_member d ON d.`memberID` = c.`memberID`
            WHERE 
                a.memberTransactionID = ?
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function createInitialPayment($data)
    {
        $this->db->trans_begin();

        $pangkal = $data['pangkal_form_deposit']!='0' ? str_replace(',','',$data['pangkal_form_deposit']) : 0;
        $pokok = $data['pokok_form_deposit']!='0' ? str_replace(',','',$data['pokok_form_deposit']) : 0;
        $wajib = $data['wajib_form_deposit']!='0' ? str_replace(',','',$data['wajib_form_deposit']) : 0;
        $sukarela = $data['sukarela_form_deposit']!='0' ? str_replace(',','',$data['sukarela_form_deposit']) : 0;
        
        
        $memberID = $data['receiverID'];

        $amount = str_replace(',','',$data['memberTransactionAmount']);

        $this->load->library('Jurnal');


        $transdate = strtotime($data['MemberTransactionDate']) == false? date('Y-m-d'):date('Y-m-d',strtotime($data['MemberTransactionDate']));

        
        //uanga pangkal
        if($pangkal!=0)
        {
            $valPangkal = array(
                'CoopID' => getCoopID(),
                'MemberID' => $memberID,
                'TransactionType' => '1',
                'TransactionNumber' => getRecBookNumber(1),
                'TransactionDate' => $transdate,
                'TransactionName' => $data['nameTmp'],
                'TransactionIdentity' => $data['identityNumber'],
                'TransactionAddress' => $data['addressTmp'],
                'TransactionAmount' => $pangkal,
                'TransactionRemark' => $data['remark'],
                'CreatedDate' => date('Y-m-d H:i:s'),
                'CreatedBy' => $_SESSION['userid']
            );
            $query = $this->db->insert('coop_transactions', $valPangkal);
            
            $this->jurnal->uangpangkal($pangkal,getCoopID(),$_SESSION['userid']);
        }
        
        //simpanan pokok
        if($pokok!=0)
        {
            $number = getTransactionNumber(1);
            
            $qms = $this->db->query("select a.memberSavingID,b.savingTypeID
                from coop_member_saving a
                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                where memberId = $memberID and b.savingTypeSHU = 1"
            );

            if($qms->num_rows()>0)
            {
                $rms = $qms->row();
                    $current = $this->getCurrentBalance($rms->memberSavingID);
                    $valuesPokok = array(
                        'memberTransactionType' => 1,
                        'memberID' => $memberID,
                        'CoopID' => getCoopID(),
                        'memberSavingID' => $rms->memberSavingID,
                        'memberTransactionNumber' => $number,
                        'memberTransactionDate' => $transdate,
                        'memberTransactionName' => $data['name'],
                        'memberTransactionIdentity' => isset($data['identityNumber']) ? $data['identityNumber'] : null,
                        'memberTransactionAddress' => isset($data['address']) ? $data['address'] : null,
                        'memberTransactionAmount' => $pokok,
                        'memberTransactionCurrentBalance' => $current + $pokok,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'CreatedBy' => $_SESSION['userid']
                    );
                    // return $valuesPokok;
                    $query = $this->db->insert('coop_member_transaction', $valuesPokok);

                    $this->jurnal->deposit($pokok,$rms->memberSavingID,getCoopID(),$_SESSION['userid']);
            }
        }
        
        //simpanan wajib
        if($wajib!=0)
        {
            $number = getTransactionNumber(1);

            $qms = $this->db->query("select a.memberSavingID,b.savingTypeID
                from coop_member_saving a
                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                where memberId = $memberID and b.savingTypeSHU = 2"
            );

            if($qms->num_rows()>0)
            {
                $rms = $qms->row();
                $current = $this->getCurrentBalance($rms->memberSavingID);
                $valuesWajib = array(
                    'memberTransactionType' => 1,
                    'CoopID' => getCoopID(),
                    'memberID' => $memberID,
                    'memberSavingID' => $rms->memberSavingID,
                    'memberTransactionNumber' => $number,
                    'memberTransactionDate' => $transdate,
                    'memberTransactionName' => $data['name'],
                    'memberTransactionIdentity' => isset($data['identityNumber']) ? $data['identityNumber'] : null,
                    'memberTransactionAddress' => isset($data['address']) ? $data['address'] : null,
                    'memberTransactionAmount' => $wajib,
                    'memberTransactionCurrentBalance' => $current + $wajib,
                    'CreatedDate' => date('Y-m-d H:i:s'),
                    'CreatedBy' => $_SESSION['userid']
                 );
                 
                $query = $this->db->insert('coop_member_transaction', $valuesWajib);

                $this->jurnal->deposit($wajib,$rms->memberSavingID,getCoopID(),$_SESSION['userid']);
            }

        }

        if($sukarela!=0)
        {
            $number = getTransactionNumber(1);

            $qms = $this->db->query("select a.memberSavingID,b.savingTypeID
                from coop_member_saving a
                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                where memberId = $memberID and b.savingTypeSHU = 4"
            );
            
            if($qms->num_rows()>0)
            {
                $rms = $qms->row();
                $current = $this->getCurrentBalance($rms->memberSavingID);
                $valuesSukarela = array(
                     'memberTransactionType' => 1,
                     'memberID' => $memberID,
                     'memberSavingID' => $rms->memberSavingID,
                     'memberTransactionNumber' => $number,
                     'CoopID' => getCoopID(),
                     'memberTransactionDate' => $transdate,
                     'memberTransactionName' => $data['name'],
                     'memberTransactionIdentity' => isset($data['identityNumber']) ? $data['identityNumber'] : null,
                     'memberTransactionAddress' => isset($data['address']) ? $data['address'] : null,
                     'memberTransactionAmount' => $sukarela,
                     'memberTransactionCurrentBalance' => $current + $sukarela,
                     'CreatedDate' => date('Y-m-d H:i:s'),
                     'CreatedBy' => $_SESSION['userid']
                 );
                 // return $valuesSukarela;
                 $query = $this->db->insert('coop_member_transaction', $valuesSukarela);

                 $this->jurnal->deposit($sukarela,$rms->memberSavingID,getCoopID(),$_SESSION['userid']);
            }

        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        else
        {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "data created.";
        }
        
        return $results;
    }

    function createTransaction($data,$type,$UserId=null) {
        
        $number = getTransactionNumber($type);
        $current = $this->getCurrentBalance($data['memberSavingID']);
        $amount = str_replace(',','',$data['memberTransactionAmount']);
        
        switch($type){
            case 1:
                $balance = $current + str_replace(',','',$data['memberTransactionAmount']);
                break;
            case 2:
                $balance = $current - str_replace(',','',$data['memberTransactionAmount']);
                break;
        }
        
        //jurnal
        $this->load->library('Jurnal');
        if($type==1)
        {
            //deposit
            $this->jurnal->deposit($amount,$data['memberSavingID'],getCoopID(),$_SESSION['userid']);

            $qms = $this->db->get_where('coop_member_saving',array('MemberSavingID'=>$data['memberSavingID']))->row();
            $memberID = $qms->MemberID;
        } else {
            //withdrawal
            $this->jurnal->withdrawal($amount,$data['memberSavingID'],getCoopID(),$_SESSION['userid']);
            $memberID = isset($data['memberID'])?$data['memberID']:NULL;
        }
		$transdate = strtotime($data['MemberTransactionDate']) == false? date('Y-m-d'):date('Y-m-d',strtotime($data['MemberTransactionDate']));
		
        $values = array(
            'memberTransactionType' => $type,
            'CoopID' => getCoopID(),
            'memberID' => $memberID,
            'memberSavingID' => $data['memberSavingID'],
            'memberTransactionNumber' => $number,
            'memberTransactionDate' => $transdate,
            'memberTransactionName' => $data['name'],
            'memberTransactionIdentity' => isset($data['identityNumber']) ? $data['identityNumber'] : null,
            'memberTransactionAddress' => isset($data['address']) ? $data['address'] : null,
            'memberTransactionAmount' => $amount,
            'memberTransactionCurrentBalance' => $balance,
            'memberTransactionRemark' => $data['remark'],
            'CreatedDate' => date('Y-m-d H:i:s'),
            'CreatedBy' => $_SESSION['userid']
        );
        // return $values;
        $query = $this->db->insert('coop_member_transaction', $values);
        
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        
        return $results;
    }
    
    function createLoanInstallment($data) {
        
        //$number = getTransactionNumber($type);
        
        //get latest installment
        $this->db->select('loanInstallmentID');
        $this->db->from('coop_loan_installment');
        $this->db->where('memberLoanID',$data['memberLoanID']);
        $this->db->where('loanInstallmentStatus',2);
        $this->db->order_by('loanInstallmentTop','ASC');
        $this->db->limit(1);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            
            $this->db->where('loanInstallmentID',$row->loanInstallmentID);
            
            $query = $this->db->update('coop_loan_installment', array(
                'loanInstallmentStatus' => 1,
                'loanInstallmentReceivedFrom' => $data['name'],
                'loanInstallmentPaidDate' => date('Y-m-d'),
                'loanInstallmentPaidValue' => str_replace(',', '', $data['memberTransactionAmount'])
            ));

            if ($query) {
                $results['success'] = true;
                $results['message'] = "data updated.";
            } else {
                $results['success'] = false;
                $results['message'] = "Failed to update record";
            }

            return $results;
        }
        
        
    }
    
    function getCurrentBalance($id) {
        
        $this->db->select('memberTransactionCurrentBalance');
        $this->db->from('coop_member_transaction');
        $this->db->where('memberSavingID',$id);
        $this->db->order_by('memberTransactionID','DESC');
        $this->db->limit(1);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $row = $Q->row();
            return $row->memberTransactionCurrentBalance;
        }
        
        return 0;
    }
    
    function updateTransaction($memberTransactionType, $memberTransactionDate, $cashSourceID, $memberSavingID, $memberTransactionAmount, $memberTransactionRemark, $userid, $id) {
        $sql = "
            UPDATE 
                `coop_member_transaction` 
            SET
                `memberTransactionType` = ?,
                `memberTransactionDate` = ?,
                `cashSourceID` = ?,
                `memberSavingID` = ?,
                `memberTransactionAmount` = ?,
                `memberTransactionRemark` = ?,
                UpdatedBy = ?,
                UpdatedDate = NOW()
            WHERE `memberTransactionID` = ?
        ";
        $query = $this->db->query($sql, array($memberTransactionType, $memberTransactionDate, $cashSourceID, $memberSavingID, $memberTransactionAmount, $memberTransactionRemark, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteTransaction($id) {
        $sql = "
            DELETE FROM
                `coop_member_transaction` 
            WHERE `memberTransactionID` = ?
        ";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    function getDetailMember($id) {
        $sql = "
            SELECT 
                a.`memberID`,
                b.`memberSavingNo`,
                c.`savingTypeName`,
                a.`farmerID`,
                a.`primaryNo`,
                a.`registeredDate`,
                a.`typeID`,
                a.`name`,
                a.`identityType`,
                a.`identityNumber`,
                a.`gender`,
                a.`placeOfBirth`,
                a.`dateOfBirth`,
                a.`address`,
                a.`villageID`,
                a.`phone`,
                a.`maritalStatus`,
                a.`education`,
                a.`status`,
                a.`remark`,
                a.`photo`,
                a.`familyName`,
                a.`familyRelation`,
                a.`familyIdentityType`,
                a.`familyIdentityNumber`,
                a.`familyAddress`,
                a.`familyPhone`
            FROM
                `coop_member` a
            LEFT JOIN coop_member_saving b ON a.memberID = b.memberID
            LEFT JOIN coop_saving_type c ON c.savingTypeID = b.savingTypeID
            WHERE b.memberSavingID = ?
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        return $result[0];
    }

    function getComboTransactionType() {
        $result['data'] = array(
            array(
                'id' => '1',
                'label' => 'Setoran'
            ),
            array(
                'id' => '2',
                'label' => 'Tarikan'
            )
        );

        return $result;
    }

    function getComboCashsource() {
        $sql = "
            SELECT 
                `cashSourceID` AS id,
                `cashSourceName` AS label
            FROM
                `coop_cash_source`
        ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        return $result;
    }

    function getComboMemberSaving($id = NULL,$savingTypeID = NULL) {
         $sql = "SELECT 
                a.`memberSavingID` AS id,
                c.`savingTypeName` AS label,
                a.`memberSavingNo` AS number,
                uangPangkal,
                IFNULL((SELECT IFNULL(FORMAT(memberTransactionCurrentBalance,2),FORMAT(0,2)) from coop_member_transaction WHERE memberSavingID
                = a.memberSavingID ORDER BY memberTransactionID DESC LIMIT 1),0) AS current
            FROM
                `coop_member_saving` a
            LEFT JOIN coop_member b ON a.`memberID` = b.`memberID` 
            LEFT JOIN coop_saving_type c ON a.`savingTypeID` = c.`savingTypeID` WHERE TRUE and savingTypeName is not null";
        // $sql = "SELECT 
        //         a.`memberSavingID` AS id,
        //         c.`savingTypeName` AS label,
        //         a.`memberSavingNo` AS number,
        //         IFNULL((SELECT IFNULL(FORMAT(memberTransactionCurrentBalance,2),FORMAT(0,2)) from coop_member_transaction WHERE memberSavingID
        //         = a.memberSavingID ORDER BY memberTransactionID DESC LIMIT 1),0) AS current
        //     FROM
        //         `coop_member_saving` a
        //     LEFT JOIN coop_member b ON a.`memberID` = b.`memberID` 
        //     LEFT JOIN coop_saving_type c ON a.`savingTypeID` = c.`savingTypeID` WHERE c.savingTypeSHU !=1 ";
        
        if(!is_null($id)){
            $sql .= " and a.memberID = '".$id."'";
        }
        
        if(!is_null($savingTypeID)){
            $sql .= " AND a.savingTypeID = " . $savingTypeID . " ";
        }
        
        $sql .= "";
        
        $sql .= "ORDER BY b.name ASC";
        
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        $num = $query->num_rows();
        $result['data'][$num]['id'] = '99';
        $result['data'][$num]['label'] = 'Uang Pangkal';
        // $result['data'][$num]['number'] = '123143';
        $result['data'][$num]['current'] = $result['data'][$num-1]['uangPangkal']==null ? 0 : $result['data'][$num-1]['uangPangkal'];
        // print_r( $result['data']);
        
        return $result;
    }

    function createUangPangkal($data)
    {
        // $number = getRecBookNumber(1);
        // echo $number;
        
        $qTo = $this->db->query("select CoaID from accounting_coa where CoaCode = '1.1.1.1'")->row(); //aktiva - uang kas
        $qSource = $this->db->query("select CoaID from accounting_coa where CoaCode = '3.1.1.5'")->row(); //ekuitas - uang pangkal
     
        
        $values = array(
            'transactionType' => 1,
            'TransactionNumber' => 99,
            'TransactionDate' => date('Y-m-d'),
            'TransactionName' => 'Deposit Uang Pangkal',
            'CoaCode' => $qTo->CoaID,
            'CashSourceID' => $qSource->CoaID,
            'TransactionAmount' => str_replace(',','',$data['memberTransactionAmount']),
            'TransactionRemark' => null,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'CreatedBy' => $_SESSION['userid']
        );
        
        $query = $this->db->insert('coop_transactions', $values);

        // //jurnal
        $this->load->library('Jurnal');
        $r = $this->jurnal->recordbooking_in($data['memberTransactionAmount'],$qSource->CoaID,$qTo->CoaID,getCoopID(),$_SESSION['userid']);

        $this->db->where('memberID',$data['receiverID']);
        $query = $this->db->update('coop_member', array('uangPangkal'=>$values['TransactionAmount']));

        $query = true;
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        
        return $results;
    }
    
    function getComboMemberLoan($id = NULL) {
        $sql = "
            SELECT 
                a.`memberLoanID` AS id,
                c.`loanTypeName` AS label,
                a.`memberLoanNo` AS number
            FROM
                `coop_member_loan` a
            LEFT JOIN coop_member b ON a.`memberID` = b.`memberID` 
            LEFT JOIN coop_loan_type c ON a.`loanTypeID` = c.`loanTypeID`";
        
        if(!is_null($id)){
            $sql .= " WHERE a.memberID = '".$id."'";
        }
        
        $sql .= "ORDER BY b.name ASC";
        
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        return $result;
    }
    
    function getComboTransType() {
        
        $result = false;
        
        $this->db->select(array(
            'savingTypeID AS typeID',
            'savingTypeName AS typeName'
        ));
        
        $this->db->from('coop_saving_type');
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $result = $query->result_array();
        }
        
        return $result;
    }
    
    function cancelTransaction($id) {
        
        switch($type){
            case 1:
                
                break;
            case 2:
                $balance = $current - str_replace(',','',$data['memberTransactionAmount']);
                break;
        }
        
        //get the ruin data
        $this->db->select('memberTransactionType,memberTransactionName,memberTransactionIdentity,memberTransactionAddress,memberID,cashSourceID,memberSavingID,memberTransactionAmount,memberTransactionRemark,memberTransactionCurrentBalance');
        $this->db->from('coop_member_transaction');
        $this->db->where('memberTransactionID',$id);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->row_array();
            
            if($result['memberTransactionType'] == 1){
                $result['memberTransactionType'] = 2;
                $balance = $result['memberTransactionCurrentBalance'] - $result['memberTransactionAmount'];
            } else {
                $result['memberTransactionType'] = 1;
                $balance = $result['memberTransactionCurrentBalance'] + $result['memberTransactionAmount'];
            }
            
            $result['correctionID'] = $id;
            $result['memberTransactionCurrentBalance'] = $balance;
            $result['memberTransactionDate'] = date('Y-m-d H:i:s');
            $result['memberTransactionNumber'] = getTransactionNumber($result['memberTransactionType']);
            $result['createdDate'] = date('Y-m-d H:i:s');
            $result['createdBy'] = $_SESSION['userid'];
            $result['CoopID'] = getCoopID();
            //insert normalize data
            $this->db->insert('coop_member_transaction',$result);
            
            if($this->db->insert_id()){
                
                //update correctionID
                $this->db->where('memberTransactionID',$id);
                $this->db->set('correctionID',$this->db->insert_id());
                $this->db->set('updatedBy',$_SESSION['userid']);
                $this->db->set('updatedDate',date('Y-m-d H:i:s'));
                
                $this->db->update('coop_member_transaction');
            }
        }
        
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete data";
        }
        return $results;
    }

    function getOutStandingSaving($memberSavingID)
    {

        $q = $this->db->query("select a.memberID,a.savingTypeID,a.memberSavingRegisteredDate,b.savingTypeName,a.memberSavingID,b.savingTypeMonthlyFee
                                from coop_member_saving a
                                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                                where memberSavingID=$memberSavingID");
        if($q->num_rows()>0)
        {
            $r = $q->row();
            $stardate = $r->memberSavingRegisteredDate;
            $savingTypeMonthlyFee = $r->savingTypeMonthlyFee;
            $memberSavingRegisteredDate = $r->memberSavingRegisteredDate;

            // $qCurrent = $this->getComboMemberSaving($r->memberID,$r->savingTypeID); //current balance

            $enddate = date('Y-m-d');

            $d1 = strtotime($stardate);
            $d2 = strtotime($enddate);
            $min_date = min($d1, $d2);
            $max_date = max($d1, $d2);
            $numMonth = 0;

            while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
                $numMonth++;
            }
           // return $numMonth;

           $date = $stardate;
           $dueMonth = 0;
           $dueAmount = 0;
           for($i=1;$i<=$numMonth;$i++)
           {
                $strDate = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
                $date = date("Y-m-d",$strDate);
                // echo $date.'<br>';

                $dateArr = explode('-', $date);

                $sql = "select memberTransactionAmount
                        from coop_member_transaction
                        where memberSavingID=$memberSavingID and (memberTransactionDate between '".$dateArr[0]."-".$dateArr[1]."-01' and '".$dateArr[0]."-".$dateArr[1]."-".cal_days_in_month(CAL_GREGORIAN, $dateArr[1], $dateArr[0])."')";
                $q = $this->db->query($sql);
                if($q->num_rows()>0)
                {
                    $rr = $q->row();
                    if($rr->memberTransactionAmount==null || $rr->memberTransactionAmount==0)
                    {
                        $dueMonth++;
                        $dueAmount+=$savingTypeMonthlyFee;
                    } else {

                    }
                } else {
                    $dueMonth++;
                    $dueAmount+=$savingTypeMonthlyFee;
                }
           }

           return array('memberSavingRegisteredDate'=>$memberSavingRegisteredDate,'dueMonth'=>$dueMonth,'dueAmount'=>$dueAmount);
        } else {
            return false;
        }

    }

    function getMemberUnpaidFee($no_member)
    {
       

        
         $this->db->select('memberID');
         $q = $this->db->get_where('coop_member',array('primaryNo'=>$no_member));
         if($q->num_rows()>0)
         {  
            $rq = $q->row();
            $i=0;

            $qpokok = $this->db->query("select b.savingTypeMinAmount,a.memberID,AmountSaving,c.memberSavingID,a.typeID,d.RegistrationFee,a.uangPangkal
                                        from coop_member a join coop_member_saving c ON a.memberID = c.memberID 
                                        left join coop_saving_type b ON c.savingTypeID = b.savingTypeID 
                                        left join coop_member_type d ON a.typeID = d.typeID                           
                                        where a.memberID = ".$rq->memberID." and b.savingTypeID = 1");
             if($qpokok->num_rows()>0)
             {
                $rPokok = $qpokok->row();

                //uang pangkal
                $paidPangkal = $rPokok->uangPangkal == null ? 0 : $rPokok->uangPangkal;
                $balance = $rPokok->RegistrationFee-$paidPangkal;

                if($balance>0)
                {
                    $results[$i] = array(
                        'feeName'=>'Uang Pangkal',
                        'amountPaid'=>number_Format($paidPangkal),
                        'amountUnpaid'=>number_Format($balance)
                    );
                    $i++;
                }
                //end uang pangkal

                //simpanan pokok
                $qT = $this->db->query("select MemberTransactionAmount
                                        from coop_member_transaction
                                        where memberSavingID = ".$rPokok->memberSavingID." and MemberTransactionType = 1");
                if($qT->num_rows()>0)
                {
                    $rqt = $qT->row();
                    $paid = $rqt->MemberTransactionAmount;
                } else {
                    $paid = 0;
                }
                
                $balance = $rPokok->savingTypeMinAmount-$paid;
                
                if($balance>0)
                {
                    $results[1] = array(
                        'feeName'=>'Simpanan Pokok',
                        'amountPaid'=>number_Format($paid),
                        'amountUnpaid'=>number_Format($balance)
                    );
                }
                //end simpanan pokok

             } else {
                $results = array();
             }
         }

         
         
         return array('data' => $results, 'total' => 2);
    }
    
    function getMemberLastTrans($id,$MemberTransactionType=null) {
        
        $this->db->select('DATE_FORMAT(memberTransactionDate,"%d-%b-%Y") as date,memberTransactionType as type,FORMAT(memberTransactionAmount,0) as amount',FALSE);
        $this->db->from('coop_member_transaction');
        $this->db->where('memberSavingID',$id);

        if($MemberTransactionType!=null)
        {
            $this->db->where('MemberTransactionType',$MemberTransactionType);
        }

        $this->db->limit(3);
        $this->db->order_by('MemberTransactionDate','DESC');
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();

            if($id==99)
            {
                //uang pangkal
            }


            //cek outstanding simpanan
            $dataOutstanding = $this->getOutStandingSaving($id);

            $q = $this->db->query("select a.memberID,a.savingTypeID
                                from coop_member_saving a
                                where memberSavingID=$id");
            if($q->num_rows()>0)
            {
                $r = $q->row();
                $qCurrent = $this->getComboMemberSaving($r->memberID,$r->savingTypeID); //current balance
            }

            return array('data' => $result, 'total' => 2,'dataOutstanding'=>$dataOutstanding,'currentBalance'=>str_replace('.00', '', $qCurrent['data'][0]['current']));
        }
        
        return array('data' => array(), 'total' => 0,'dataOutstanding'=>false);
    }
    
    function getLoanPaymentPlan($id) {
        
        $this->db->select('DATE_FORMAT(loanInstallmentDueDate,"%d-%b-%Y") as due,DATE_FORMAT(loanInstallmentPaidDate,"%d-%b-%Y") as paid,FORMAT(loanInstallmentValue,2) as amount,DATEDIFF(loanInstallmentDueDate,loanInstallmentPaidDate) as arrear',FALSE);
        $this->db->from('coop_loan_installment');
        $this->db->where('memberLoanID',$id);
        $Q = $this->db->get();
        if($Q->num_rows() > 0){
            $result = $Q->result_array();
            return array('data' => $result, 'total' => $Q->num_rows());
        }
        
        return array('data' => array(), 'total' => 0);
    }

    function getCoaID($CoaCode)
    {
        $q = $this->db->query("select CoaID from accounting_coa where CoaCode = '$CoaCode' and CoopID = ".getCoopID()."")->row();
        return $q->CoaID;
    }
    
    /**
     * Fungsi-fungsi untuk record booking
     */
    function createRecBook($data,$type) {
        
        $number = getRecBookNumber($type);

        $values = array(
            'TransactionType' => $type,
            'TransactionNumber' => $number,
            'TransactionDate' => date('Y-m-d'),
            'TransactionName' => $data['name'],
            'CoaCode' => $data['to'],
            'CashSourceID' => $data['source'],
            'TransactionAmount' => str_replace(',','',$data['amount']),
            'TransactionRemark' => $data['remark'],
            'CreatedDate' => date('Y-m-d H:i:s'),
            'CreatedBy' => $_SESSION['userid'],
        );
        
        $query = $this->db->insert('coop_transactions', $values);

        //jurnal
        $this->load->library('Jurnal');
        if($type==1)
        {
            $this->jurnal->recordbooking_in(str_replace(',','',$data['amount']),$data['source'],$data['to'],getCoopID(),$_SESSION['userid']);
        } else {
            $this->jurnal->recordbooking_out(str_replace(',','',$data['amount']),$data['source'],$data['to'],getCoopID(),$_SESSION['userid']);
        }
        
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        
        return $results;
    }
    
    function readRecTransactions($type, $key, $start, $limit) {
        $sql = "
            SELECT DISTINCT 
                TransactionAmount as amount,
                TransactionName as name,
                CoaTitle as account
            FROM
                `coop_transactions` a
            LEFT JOIN accounting_coa b ON b.CoaCode=a.CoaCode
             WHERE a.TransactionType = " . $type;
        
        if(strlen($key) > 0){
            $sql .= " AND WHERE b.TransactionName LIKE ?";
        }
        
        $sql .=" LIMIT ".$start."," .$limit;
        
        $query = $this->db->query(sprintf($sql, ''), array("%$key%", (int) $start, (int) $limit));
        
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $total = $row->row();
        $result['total'] = $total->total;

        return $result;
    }

    function readActualBalanceTrans($userid,$date)
    {
        $q = $this->db->query("select sum(memberTransactionAmount) as total
            from coop_member_transaction
            where memberTransactionDate ='$date'");
        if($q->num_rows()>0)
        {
            $r = $q->row();
            if($r->total==null)
            {
                return array('total'=>0);
            } else {
                return array('total'=>number_format($r->total,0));
            }
        } else {
            return array('total'=>0);
        }
    }

    function readLimit($userid,$amount)
    {
        
        $status=true;
        /*
        $q = $this->db->get_where('ktv_cooperative_staff',array('UserId'=>$userid));
        if($q->num_rows()>0)
        {
            $r = $q->row();
            $qcoop = $this->db->get_where('ktv_cooperatives',array('CoopID'=>$r->CoopID));
            if($qcoop->num_rows()>0)
            {
                $r2 = $qcoop->row();
                $lmt = $r2->LimitTransaction;
            } else {
                $lmt = 0;
            }
        } else {
            $lmt = 0;
        }
        
        if($amount>$lmt)
        {
            $status=false; //melebihi limit
        }
        */
        return array('status'=>$status);
    }

    function approval($username,$password)
    {
        $sql = "select ApprovalRights,a.UserId 
            from sys_user a
            join ktv_cooperative_staff b ON a.UserId = b.UserId
            where a.UserName = '".$username."' and a.UserPassword = '".md5($password)."' ";
        $q = $this->db->query($sql);
        if($q->num_rows()>0)
        {
            $r = $q->row();
            if($r->ApprovalRights==1)
            {
                return array('approved'=>true,'UserId'=>$r->UserId);
            } else {
                return array('approved'=>false);
            }
        } else {
            return array('approved'=>false);
        }
    }
    
    function getComboCoa($type,$trans,$payment,$search = '') {

        $sql = "SELECT
                    a.CoaCode AS id ,
                    CONCAT(a.coaCode , ' - ' , a.coaTitle) AS label
                FROM
                    accounting_coa a
                WHERE a.CoaStatus = 1 AND a.CoopID = " . $this->coop;
        
        if($type=='received')
        {
            // $sql.=" AND (a.coaGroupID = 1 or a.CoaGroupID = 5) AND CoaForReceived = 1";
            //$sql.=" AND CoaForReceived = 1";
        }

        if($type=='spent')
        {
            // $sql.=" AND (a.coaGroupID = 1 or a.CoaGroupID = 5) AND CoaForSpent = 1";
            //$sql.=" AND CoaForSpent = 1";
        }

        if($trans==1)
        {
            //cash
            //$sql.=" AND a.CoaForCash = 1";
        } else if($trans==2)
            {
                //non cash
                //$sql.=" AND a.CoaForNonCash = 1";
            }
        // echo $sql;
        // $this->db->select('CoaCode AS id,CONCAT("[",CoaCode,"] ",CoaTitle) AS label',false);
        // $this->db->from('accounting_coa');
        // $Q = $this->db->get();
        if(strlen($search) > 0) {
            $sql .= " AND (a.coaCode LIKE '%".$search."%' OR coaTitle LIKE '%".$search."%')";
        }
        
        $Q = $this->db->query($sql);

        if($Q->num_rows() > 0){
            return array('data' => $Q->result_array());
        }
        
        return array('data' => array());
    }

    function getSavingMember($id)
    { 
//         ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
        $qPangkal = $this->db->query("select UangPangkal from coop_member a where MemberID = $id")->row();
        
        $pangkal = $qPangkal->UangPangkal == null ?  0 : $qPangkal->UangPangkal;

        $qPokok = $this->db->query(" select a.memberSavingID,a.memberID,a.memberSavingNo,savingTypeName,
                        case
                            when a.AmountSaving = 0 THEN b.savingTypeMinAmount
                            else a.AmountSaving
                        END as savingTypeMinAmountPokok
                        from coop_member_saving a
                        join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                        where MemberID = $id and b.savingTypeSHU = 1")->row();

        $pokok = $qPokok->savingTypeMinAmountPokok == null ? 0 : $qPokok->savingTypeMinAmountPokok;

        $qWajib = $this->db->query("select a.memberSavingID,a.memberID,a.memberSavingNo,savingTypeName,
                case
                    when a.AmountSaving = 0 THEN b.savingTypeMinAmount
                    else a.AmountSaving
                END as savingTypeMinAmountWajib
                from coop_member_saving a
                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                where MemberID = $id and b.savingTypeSHU = 2");

        $wajib = $qPokok->savingTypeMinAmountWajib == null ? 0 : $qPokok->savingTypeMinAmountWajib;

        $qWajib = $this->db->query("select a.memberSavingID,a.memberID,a.memberSavingNo,savingTypeName,
                case
                    when a.AmountSaving = 0 THEN b.savingTypeMinAmount
                    else a.AmountSaving
                END as savingTypeMinAmountSukarela
                from coop_member_saving a
                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                where MemberID = $id and b.savingTypeSHU = 3");

        $sukarela = $qPokok->savingTypeMinAmountSukarela == null ? 0 : $qPokok->savingTypeMinAmountSukarela;

        return array('pangkal'=>$pangkal,'pokok'=>$pokok,'wajib'=>$wajib,'sukarela'=>$sukarela);
    }

}

?>

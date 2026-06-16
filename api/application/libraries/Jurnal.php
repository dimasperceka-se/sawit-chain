<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library purpose:
 * Create automatic journal book whenever at following event occur:
 * 	1. Update status member to Active : Kas [D] <> Simpanan Pokok [K]
 * 	2. Deposit : Kas [D] <> Account saving product [K] . get config from Configuration->Savings
 * 	3. Withdrawal : Account saving product [D] <> Kas [K]
 * 	4. Record Booking
 * 		a. Received : from account [K] <> to account [D]
 * 		b. Spent : from account [K] <> to account [D]
 * 	5. Accumulated interest saving product at the end of month/clossing book:
 *   6. Sales
 *   7. Purchase
 * 	8. Receivable
 * 	9. Payable
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	    Cooperatives
 * @author        	imamteguh1@gmail.com
 */
class Jurnal {

    protected $_ci;     //instansce ci

    function __construct() {
        ini_set('display_errors', true);
        $this->_ci = & get_instance();
    }

    function member_active($CoopID, $MemberID, $userid) {
        if (!$this->autoJournalConf($CoopID)) {
            return true;
        }

        // 1. Update status member to Active : Kas [D] <> Simpanan Pokok [K]

        if ($coopID == null || $userid == null) {
            return false;
        }
        $this->_ci->db->select('savingTypeMonthlyFee,CoaCode');
        $this->_ci->db->join('accounting_coa', 'accounting_coa.CoaID = coop_saving_type.CoaID');
        $q = $this->_ci->db->get_where('coop_saving_type', array('coopID' => $coopID, 'savingTypeCode' => 'SP'));
        $config = $q->row();

        $this->_ci->db->trans_begin();

        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'JournalTypeCode' => 'JP',
            'CoopID' => $CoopID,
            'JournalMemo' => 'Penerimaan Simpanan Pokok Member Baru ID:' . $MemberID,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        //KAS [D]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '1.1.1.1',
            'JournalDetailSum' => $config->savingTypeMonthlyFee,
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($config->savingTypeMonthlyFee, '1111', 1, $CoopID);

        //Simpanan Pokok [K]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $config->CoaCode,
            'JournalDetailSum' => $config->savingTypeMonthlyFee,
            'JournalDetailType' => 2
        );
        $this->_ci->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($config->savingTypeMonthlyFee, $config->CoaCode, 1, $CoopID);

        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return false;
        } else {
            $this->_ci->db->trans_commit();
            return true;
        }
    }

    function deposit($amount, $memberSavingID, $CoopID, $userid) {
        if (!$this->autoJournalConf($CoopID)) {
            //return true;
        }

        // 2. Deposit : Kas [D] <> Account saving product [K] . get config from Configuration->Savings
        $memberSavingQ = $this->_ci->db->get_where('coop_member_saving', array('memberSavingID' => $memberSavingID));
        $rMS = $memberSavingQ->row();

        $this->_ci->db->select('CoaCode');
        $this->_ci->db->join('accounting_coa', 'accounting_coa.CoaID = coop_saving_type.CoaID');
        $savingTypeQ = $this->_ci->db->get_where('coop_saving_type', array('SavingTypeID' => $rMS->SavingTypeID));
        $rST = $savingTypeQ->row();

        $coaCode = isset($rST->CoaCode) ? $rST->CoaCode : '3111';

        $this->_ci->db->trans_begin();

        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'CoopID' => $CoopID,
            'JournalTypeCode' => 'JU',
            'JournalMemo' => 'Deposit Member ID:' . $rMS->MemberID,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        //KAS [D]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '1111',
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,'1111',1,$CoopID);
        //Account saving product [K]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $coaCode,
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 2
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,$coaCode,1,$CoopID);


        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return false;
        } else {
            $this->_ci->db->trans_commit();
            return true;
        }
    }
    
    function uangpangkal($amount, $CoopID, $userid) {
        
        $coaCode = $this->_ci->config->item('uangpangkal');

        $this->_ci->db->trans_begin();

        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'CoopID' => $CoopID,
            'JournalTypeCode' => 'JU',
            'JournalMemo' => 'Uang Pangkal ',
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        //KAS [D]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '1111',
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,'1111',1,$CoopID);
        //Account saving product [K]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $coaCode,
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 2
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,$coaCode,1,$CoopID);


        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return false;
        } else {
            $this->_ci->db->trans_commit();
            return true;
        }
    }

    function withdrawal($amount, $memberSavingID, $CoopID, $userid) {
        if (!$this->autoJournalConf($CoopID)) {
            //return true;
        }

        // 3. Withdrawal : Account saving product [D] <> Kas [K]

        $memberSavingQ = $this->_ci->db->get_where('coop_member_saving', array('memberSavingID' => $memberSavingID));
        $rMS = $memberSavingQ->row();
        
        
        $this->_ci->db->select('CoaCode');
        $this->_ci->db->join('accounting_coa', 'accounting_coa.CoaID = coop_saving_type.CoaID');
        $savingTypeQ = $this->_ci->db->get_where('coop_saving_type', array('savingTypeID' => $rMS->SavingTypeID));
        $rST = $savingTypeQ->row();
        $coacode = isset($rST->CoaCode) ? $rST->CoaCode : '3111';
        $this->_ci->db->trans_begin();

        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'CoopID' => $CoopID,
            'JournalTypeCode' => 'JU',
            'JournalMemo' => 'Withdrawal Member ID:' . $rMS->MemberID,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        // //Account saving product [D]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $coacode,
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,$rST->CoaCode,2,$CoopID);
        //Account saving product [K]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '1111',
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 2
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,'1.1.1.1',2,$CoopID);


        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return false;
        } else {
            $this->_ci->db->trans_commit();
            return true;
        }
    }

    function recordbooking_in($amount, $source, $to, $CoopID, $userid, $memo = null) {
        if (!$this->autoJournalConf($CoopID)) {
            //return true;
        }

        //to param is CoaCode
        // from account [K] <> to account [D]

        $this->_ci->db->trans_begin();

        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'CoopID' => $CoopID,
            'JournalTypeCode' => 'JO',
            'JournalMemo' => $memo == null ? 'Record Booking In' : null,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        $qcoa = $this->_ci->db->get_where('accounting_coa', array('CoaID' => $to, 'CoopID' => $CoopID))->row();
        //to account [D]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $to,
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,$to,1,$CoopID);
        //from account [K]
        $qcoa = $this->_ci->db->get_where('coop_cash_source', array('CashSourceID' => $source, 'CoopID' => $CoopID))->row();

        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $qcoa->CoaCode,
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 2
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,$qcoa->CoaCode,1,$CoopID);

        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return array('success' => false, 'JournalID' => null);
        } else {
            $this->_ci->db->trans_commit();
            return array('success' => true, 'JournalID' => $id);
        }
    }

    function recordbooking_out($amount, $source, $to, $CoopID, $userid, $memo = null) {
        if (!$this->autoJournalConf($CoopID)) {
            return true;
        }

        //to param is CoaCode
        // Spent : from account [K] <> to account [D]

        $this->_ci->db->trans_begin();

        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'JournalTypeCode' => 'JO',
            'CoopID' => $CoopID,
            'JournalMemo' => $memo == null ? 'Record Booking Out' : null,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        //to account [D]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $to,
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,$to,2,$CoopID);
        //from account [K]
        $qcoa = $this->_ci->db->get_where('coop_cash_source', array('CashSourceID' => $source))->row();

        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $qcoa->CoaCode,
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 2
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        //$this->updateBalance($amount,$qcoa->CoaCode,2,$CoopID);

        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return false;
        } else {
            $this->_ci->db->trans_commit();
            return true;
        }
    }

    function saving_interest($interest, $CoaInterestID, $CoopID, $userid, $savingTypeName) {
        if (!$this->autoJournalConf($CoopID)) {
            return true;
        }

        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'JournalTypeCode' => 'JP',
            'CoopID' => $CoopID,
            'JournalMemo' => 'Beban Bunga ' . $savingTypeName,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        //Saving product [K]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => $config->CoaCode,
            'JournalDetailSum' => $interest,
            'JournalDetailType' => 2
        );
        $this->_ci->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($interest, $config->CoaCode, 2, $CoopID);

        //KAS [K]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '1.1.1.1',
            'JournalDetailSum' => $interest,
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($interest, '1.1.1.1', 2, $CoopID);

        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return false;
        } else {
            $this->_ci->db->trans_commit();
            return true;
        }
    }

    function updateBalance($amount, $CoaCode, $Type, $CoopID) {
        $qcoa = $this->_ci->db->get_where('accounting_coa', array('coopID' => $CoopID, 'CoaCode' => $CoaCode))->row();
        if (!$qcoa) {
            // echo 'DEBUG:'.$this->_ci->db->last_query();
            $CoaID = 17;
        } else {
            $CoaID = $qcoa->CoaID;
        }


        $this->_ci->db->select_max('CoaBalanceID');
        $q = $this->_ci->db->get_where('accounting_coa_balance', array('CoaID' => $CoaID));
        if ($q->num_rows() > 0) {
            $r = $q->row();

            $this->_ci->db->select('CoaBalanceAmount');
            $qBalance = $this->_ci->db->get_where('accounting_coa_balance', array('CoaBalanceID' => $r->CoaBalanceID));
            if ($qBalance->num_rows() > 0) {
                $r = $qBalance->row();
                $actual = $r->CoaBalanceAmount;
            } else {
                $actual = 0;
            }
        } else {
            $actual = 0;
        }

        if ($Type == 1) {
            //increase
            $this->_ci->db->insert('accounting_coa_balance', array('CoaID' => $CoaID, 'DateCreated' => date('Y-m-d'), 'CoaBalanceAmount' => $actual + $amount));
        } else {
            //decrease
            $this->_ci->db->insert('accounting_coa_balance', array('CoaID' => $CoaID, 'DateCreated' => date('Y-m-d'), 'CoaBalanceAmount' => $actual - $amount));
        }
    }

    function purchase($memo, $grandtotal, $bayar, $sisa, $CoopID, $userid) {
        if (!$this->autoJournalConf($CoopID)) {
            return true;
        }

        //input pembelian
        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'JournalTypeCode' => 'JP',
            'CoopID' => $CoopID,
            'JournalMemo' => '[PEMBELIAN] ' . $memo,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        if ($sisa == 0) {
            //LUNAS 
            //KAS [D]
            $dJurnalDetail = array(
                'JournalID' => $id,
                'CoaCode' => '1.1.1.1',
                'JournalDetailSum' => $bayar,
                'JournalDetailType' => 1
            );
            $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

            $this->updateBalance($bayar, '1.1.1.1', 2, $CoopID);

            //pembelian/asset [K]
            $sale = $sisa + $bayar;
            $dJurnalDetail = array(
                'JournalID' => $id,
                'CoaCode' => '4.1.0.1',
                'JournalDetailSum' => $bayar,
                'JournalDetailType' => 2
            );
            $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

            $this->updateBalance($bayar, '1.1.2.3', 1, $CoopID);
        } else {
            //belum lunas, ada hutang
            //KAS [D]
            $dJurnalDetail = array(
                'JournalID' => $id,
                'CoaCode' => '1.1.1.1',
                'JournalDetailSum' => $bayar,
                'JournalDetailType' => 1
            );
            $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

            $this->updateBalance($bayar, '1.1.1.1', 2, $CoopID);

            //HUTANG [D]
            $dJurnalDetail = array(
                'JournalID' => $id,
                'CoaCode' => '1.1.1.1',
                'JournalDetailSum' => $sisa,
                'JournalDetailType' => 1
            );
            $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

            $this->updateBalance($sisa, '1.1.1.1', 1, $CoopID);

            //PEMBELIAN/ASSET [K]
            $dJurnalDetail = array(
                'JournalID' => $id,
                'CoaCode' => '4.1.0.1',
                'JournalDetailSum' => $bayar + $sisa,
                'JournalDetailType' => 2
            );
            $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

            $this->updateBalance(($bayar + $sisa), '1.1.2.3', 1, $CoopID);
        }

        return $id;
    }

    function purchase_payable($data, $userid, $coopid, $memo) {
        if (!$this->autoJournalConf($coopid)) {
            return true;
        }

        //pembayaran hutang
        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'JournalTypeCode' => 'JP',
            'CoopID' => $coopid,
            'JournalMemo' => '[AP] ' . $memo,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        //Kas [K]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '1.1.1.1',
            'JournalDetailSum' => $data['pelunasan'],
            'JournalDetailType' => 2
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($data['pelunasan'], '1.1.1.1', 2, $coopid);

        //hutang [D]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '2.1.5.1',
            'JournalDetailSum' => $data['pelunasan'],
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($data['pelunasan'], '2.1.5.1', 2, $coopid);
    }

    function sale($memo, $bayar, $sisa, $CoopID, $userid) {
        if (!$this->autoJournalConf($CoopID)) {
            return true;
        }

        //input penjualan
        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'JournalTypeCode' => 'JP',
            'CoopID' => $CoopID,
            'JournalMemo' => '[PENJUALAN] ' . $memo,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        //Kas [D]
        if ($bayar > 0) {
            $dJurnalDetail = array(
                'JournalID' => $id,
                'CoaCode' => '1.1.1.1',
                'JournalDetailSum' => $bayar,
                'JournalDetailType' => 1
            );
            $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

            $this->updateBalance($bayar, '1.1.1.1', 1, $CoopID);
        }

        if (intval($sisa) != 0) {
            //piutang [D]
            $dJurnalDetail = array(
                'JournalID' => $id,
                'CoaCode' => '1.1.1.3',
                'JournalDetailSum' => $sisa,
                'JournalDetailType' => 1
            );
            $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

            $this->updateBalance($bayar, '1.1.1.3', 1, $CoopID);
        }

        //penjualan [K]
        $sale = $sisa + $bayar;
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '4.1.1.1',
            'JournalDetailSum' => $sale,
            'JournalDetailType' => 2
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($sale, '4.1.1.1', 1, $CoopID);

        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return false;
        } else {
            $this->_ci->db->trans_commit();
            return $id;
        }
    }

    function sale_receivable($amount, $userid, $coopid, $memo) {
        if (!$this->autoJournalConf($coopid)) {
            return true;
        }

        //penenerimaan piutang
        $dJurnal = array(
            'JournalDate' => date('Y-m-d'),
            'JournalTypeCode' => 'JP',
            'CoopID' => $coopid,
            'JournalMemo' => '[AR] ' . $memo,
            'JournalCRBY' => $userid,
            'JournalCRDT' => date('Y-m-d H:m:s')
        );
        $this->_ci->db->insert('accounting_journal', $dJurnal);

        $id = $this->_ci->db->insert_id();

        //Kas [K]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '1.1.1.1',
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 2
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($amount, '1.1.1.1', 2, $coopid);

        //hutang [D]
        $dJurnalDetail = array(
            'JournalID' => $id,
            'CoaCode' => '2.1.5.1',
            'JournalDetailSum' => $amount,
            'JournalDetailType' => 1
        );
        $this->_ci->db->insert('accounting_journal_detail', $dJurnalDetail);

        $this->updateBalance($amount, '2.1.5.1', 2, $coopid);

        if ($this->_ci->db->trans_status() === FALSE) {
            $this->_ci->db->trans_rollback();
            return false;
        } else {
            $this->_ci->db->trans_commit();
            return $id;
        }
    }

    function autoJournalConf($coopid) {
        $q = $this->_ci->db->query("select AutoJournal from ktv_cooperatives where CoopID = $coopid")->row();
        if ($q->AutoJournal == null || $q->AutoJournal == 1) {
            return true;
        } else {
            return false;
        }
    }

    function insert_sync($JournalID, $CoopID) {
        // $this->db->where(array('JournalID'=>$JournalID,'CoopID'=>$CoopID));
        // $this->db->update('')
    }

}

?>

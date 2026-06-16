<?php

/**
 *
 * @author imamteguh1@gmail.com
 */
class mlaporan extends CI_Model {

    function __construct() {
        parent::__construct();
        ini_set('display_errors',true);
        error_reporting(E_ALL);
    }

    function getDataNeraca($sd,$nd,$gid,$type='group')
    {
    	if($type=='class')
    	{
    		$qCoa = $this->db->query("select a.CoaID,a.CoaCode,a.CoaTitle
							from accounting_coa a
							join accounting_coa_group b oN a.CoaGroupID = b.CoaGroupID
							where coopID = ".getCoopID()." and b.coaClassId = $gid");
    	} else {
    		$qCoa = $this->db->query("select a.CoaID,a.CoaCode,a.CoaTitle
							from accounting_coa a
							where coopID = ".getCoopID()." and a.CoaGroupID = $gid");
    	}
    	

    	// $qCoa = $this->db->query($s);
        
        $arr = array();
        $i = 0;
        $total=0;
        foreach ($qCoa->result() as $r) {

        	$qBal = $this->db->query("select totSetor,totTarik
										from (
											select sum(transactionAmount) as totSetor
											from coop_transactions 
											where cashSourceID = '".$r->CoaCode."' AND (transactionDate between '".$sd."' and '".$nd."') and transactionType = 1
										) a,
										( 
											select sum(transactionAmount) as totTarik
											from coop_transactions 
											where cashSourceID = '".$r->CoaCode."' AND (transactionDate between '".$sd."' and '".$nd."') and transactionType = 2
										) b");
        	if($qBal->num_rows()>0)
        	{
        		$rBal = $qBal->row();
        		$balance = $rBal->totSetor-$rBal->totTarik;
        	} else {
        		$balance = 0;
        	}

        	$arr[$i]['CoaID'] = $r->CoaID;
            $arr[$i]['CoaCode'] = $r->CoaCode;
            $arr[$i]['CoaTitle'] = $r->CoaTitle;
            $arr[$i]['balance'] = $balance;

            $i++;
            $total+=$balance;           
        }

        return array('data'=>$arr,'total'=>$total);
    }

    function getDataCoa($sd,$nd,$gid,$type='group')
    {
    	if($type=='class')
    	{
    		$qCoa = $this->db->query("select a.CoaID,a.CoaCode,a.CoaTitle
							from accounting_coa a
							join accounting_coa_group b oN a.CoaGroupID = b.CoaGroupID
							where coopID = ".getCoopID()." and b.coaClassId = $gid");
    	} else {
    		$qCoa = $this->db->query("select a.CoaID,a.CoaCode,a.CoaTitle
							from accounting_coa a
							where coopID = ".getCoopID()." and a.CoaGroupID = $gid");
    	}
    	

    	// $qCoa = $this->db->query($s);
        
        $arr = array();
        $i = 0;
        $total=0;
        foreach ($qCoa->result() as $r) {

        	$qBal = $this->db->query("select totSetor,totTarik
										from (
											select sum(transactionAmount) as totSetor
											from coop_transactions 
											where cashSourceID = '".$r->CoaCode."' AND (transactionDate between '".$sd."' and '".$nd."') and transactionType = 1
										) a,
										( 
											select sum(transactionAmount) as totTarik
											from coop_transactions 
											where cashSourceID = '".$r->CoaCode."' AND (transactionDate between '".$sd."' and '".$nd."') and transactionType = 2
										) b");
        	if($qBal->num_rows()>0)
        	{
        		$rBal = $qBal->row();
        		$balance = $rBal->totSetor-$rBal->totTarik;
        	} else {
        		$balance = 0;
        	}

        	$arr[$i]['CoaID'] = $r->CoaID;
            $arr[$i]['CoaCode'] = $r->CoaCode;
            $arr[$i]['CoaTitle'] = $r->CoaTitle;
            $arr[$i]['balance'] = $balance;

            $i++;
            $total+=$balance;           
        }

        return array('data'=>$arr,'total'=>$total);
    }

    function getDataLabaRugi($sd,$nd,$gid,$type='group')
    {

    	if($type=='class')
    	{
    		$qCoa = $this->db->query("select a.CoaID,a.CoaCode,a.CoaTitle
							from accounting_coa a
							join accounting_coa_group b oN a.CoaGroupID = b.CoaGroupID
							where coopID = ".getCoopID()." and b.coaClassId = $gid");
    	} else {
    		$qCoa = $this->db->query("select a.CoaID,a.CoaCode,a.CoaTitle
							from accounting_coa a
							where coopID = ".getCoopID()." and a.CoaGroupID = $gid");
    	}
    	

    	// $qCoa = $this->db->query($s);
        
        $arr = array();
        $i = 0;
        $total=0;
        foreach ($qCoa->result() as $r) {

        }

        return array('data'=>$arr,'total'=>$total);
    }

}
?>
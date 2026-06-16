<?php 
// if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coop_interest extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

 	function tes_cli()
    {
        echo 'hahaha';
    }

    function calc_interest()
    {
        //calculate interest saving product based on end of month/year and create journal. run from php cli/cronjob.
        $data = array();
        $qSavingType = $this->db->get_where('coop_saving_type',array('CoopID'=>getCoopID()));
        foreach ($qSavingType->result() as $r) {

            if($r->savingTypeInterestPayment==1)
            {
                //monthly
                $end_month = date("Y-m-t", strtotime(date('Y-m-d')));
                if($end_month==date('Y-m-d'))
                {
                    $this->saving_interest($r->typeID,$r->savingTypeInterestRate,$r->CoaInterestID,$r->savingTypeName);
                }
            } else {
                //yearly
                $d = date('Y').'-12-'.date('d');
                $end_year = date("Y-m-t", strtotime($d));
                if($end_year==date('Y-m-d'))
                {
                    $this->saving_interest($r->typeID,$r->savingTypeInterestRate,$r->CoaInterestID,$r->savingTypeName);
                }
            }
        }
    }

    function saving_interest($typeID,$savingTypeInterestRate,$CoaInterestID,$savingTypeName)
    {
        $qMSaving = $this->db->query("select sum(AmountSaving) as TotalSaving
                from coop_member_saving a 
                join coop_member_type b ON a.typeID = b.typeID
                where b.typeID=".$typeID."");
        $rMSaving = $qMSaving->row();
        $interest = $rMSaving->TotalSaving*($savingTypeInterestRate/100);

        //jurnal
        $this->load->library('Jurnal');
        $this->jurnal->saving_interest($interest,$CoaInterestID,getCoopID(),$_SESSION['userid'],$savingTypeName);
    }

}
?>
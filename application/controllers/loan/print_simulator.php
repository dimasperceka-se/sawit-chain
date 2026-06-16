<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_simulator extends CI_Controller {
    
   public function cetak($loanTypeIDSimulator,$loanMemberAmountSimulator,$loanMemberTotalTenorSimulator,$MonthsSimulator,$duedateLoan)
   {
       $service_url = $this->config->item('api').'/loan/runSimulator';
       
       $curl = curl_init($service_url);
       $curl_post_data = array(
            "loanTypeIDSimulator" => $loanTypeIDSimulator,
            "loanMemberAmountSimulator"=>$loanMemberAmountSimulator,
            "loanMemberTotalTenorSimulator" => $loanMemberTotalTenorSimulator,
            "MonthsSimulator" => $loanMemberTotalTenorSimulator,
            "duedateLoan" => $duedateLoan
       );
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($curl, CURLOPT_POST, true);
       curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
       $curl_response = curl_exec($curl);
       curl_close($curl);
       
       $dt = json_decode($curl_response);
//       print_r($dt);
//       exit;
//       
//       foreach ($dt->data as $key => $value) {
//           echo $value['installment'];
//           print_r($value);
//       }
       $curl_post_data['simdata'] = $dt->data;
       $curl_post_data['loanname'] = $dt->loanname;
       $curl_post_data['tenor'] = $loanMemberTotalTenorSimulator;
       $curl_post_data['amount'] = $loanMemberAmountSimulator;
       $curl_post_data['margin'] = $dt->margin;
       $curl_post_data['ClientSharing'] = $dt->loanTypeClientSharing;
       $curl_post_data['CooperativeSharing'] = $dt->loanTypeCooperativeSharing;
//       $xml = new SimpleXMLElement($curl_response);
//       print_r($xml);
       $this->load->view('printsimulator',$curl_post_data);
   }
}
?>
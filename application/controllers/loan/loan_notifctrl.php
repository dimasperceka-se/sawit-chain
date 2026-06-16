<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* moved to api */


//
//class Loan_notifctrl extends CI_Controller {
//
//   public function __construct() {
//      parent::__construct();
//   }
//   
//   public function LoanApproval($idNotif=null,$memberLoanID=null)
//    {
//        $idNotif = $this->input->post('notifid');
//        $qnotif = $this->db->get_where('ktv_notifikasi',array('NotifID'=>$idNotif));
//        if($qnotif->num_rows()>0)
//        {
//            $rNotif = $qnotif->row();
//            
//             //set to read
//            $this->db->where('NotifID',$idNotif);
//            $this->db->update('ktv_notifikasi',array('Status'=>1)); //read
//
//            //redirect to 
//            if($rNotif->Type==1)
//            {
//                //loan approval
//               // redirect(site_url().'/loan/loan_member/index');
//            } else {
//                
//            }
//            
//        } else {
//            echo 'data not found';
//        }
//       
//    }
//   
//}
?>
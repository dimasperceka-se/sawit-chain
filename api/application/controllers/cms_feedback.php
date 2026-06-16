<?php
/**
 * @Author: nikolius
 * @Date:   2016-06-01 15:25:37
 */
defined('BASEPATH') OR exit('No direct script access allowed');

//class phpmailer
require APPPATH.'third_party/phpmailer/class.phpmailer.php';

class Cms_feedback extends REST_Controller {
   public function __construct() {
      parent::__construct();
      $this->load->model('cms_feedback/mcms_feedback');
   }

   public function insert_feedback_post(){
      $proses = true;
      $prosesInsert = $this->mcms_feedback->prosesInsertFeedback($this->post('titleFeedback'),$this->post('contentFeedback'),$_SESSION['userid']);
      $proses = $proses && $prosesInsert;

      //proses kirim email ==================== (begin)
      $dataUser = $this->mcms_feedback->getInfoUser($_SESSION['userid']);

      $objMail = new PHPMailer();
      $objMail->IsSMTP(); // telling the class to use SMTP
      //$objMail->SMTPDebug = 2;
      $objMail->SMTPAuth = true; // enable SMTP authentication
      $objMail->Host = $this->config->item('smtp_host'); // sets the SMTP server
      $objMail->Port = $this->config->item('smtp_port'); // set the SMTP port for the GMAIL server
      $objMail->Username = $this->config->item('smtp_user'); // SMTP account username
      $objMail->Password = $this->config->item('smtp_pass'); // SMTP account password
      $objMail->Priority = 1;
      $objMail->SetFrom($this->config->item('email_from'), 'PalmoilTrace');

      $arrVarEmail = array(
         "nama" => $dataUser['nama'],
         "email" => $dataUser['email'],
         "title" => $this->post('titleFeedback'),
         "content" => $this->post('contentFeedback')
      );

      $bodyMsg = $this->getBodyMsg($arrVarEmail,'toAdmin','[PalmoilTrace] Feedback from PalmoilTrace User');

      //kirim email
      $objMail->Subject = '[PalmoilTrace] Feedback from CocaTrace User';
      $objMail->Body = $bodyMsg;
      $objMail->IsHTML(true);
      //$objMail->AddAddress('n1colius.lau@gmail.com', 'Nikolius Lau');
      $objMail->AddAddress('contact@koltiva.com', 'Koltiva Contact');
      $objMail->AddCC('info@koltiva.com', 'Koltiva Info');
      $objMail->AddCC('zaenal.arifin@koltiva.com','Zaenal Arifin');
      $objMail->AddCC('ainu.rofiq@koltiva.com','Ainu Rofiq');
      $objMail->AddCC('furqonuddin.ramdhani@koltiva.com','Furqonuddin Ramdhani');

      $prosesSendEmail = $objMail->Send();
      $proses = $proses && $prosesSendEmail;
      //echo '<pre>'; print_r($prosesSendEmail); exit;

      $objMail->ClearAddresses();
      $objMail->IsHTML(false);

      if($dataUser['email'] != ""){
         $bodyMsg = $this->getBodyMsg($arrVarEmail,'toUser','[PalmoilTrace] Thank you for filling out our feedback form');

         //kirim email
         $objMail->Subject = '[PalmoilTrace] Thank you for filling out our feedback form';
         $objMail->Body = $bodyMsg;
         $objMail->IsHTML(true);
         $objMail->AddAddress($dataUser['email'], $dataUser['nama']);

         $prosesSendEmail = $objMail->Send();
         $proses = $proses && $prosesSendEmail;
         //echo '<pre>'; print_r($prosesSendEmail); exit;

         $objMail->ClearAddresses();
         $objMail->IsHTML(false);
      }
      //proses kirim email ==================== (end)

      if($proses) $this->response($proses, 200); else $this->response($proses, 404);
   }

   private function getBodyMsg($arrVarEmail,$opsi,$subjectEmail){
      switch ($opsi) {
         case 'toAdmin':
$body = "
<!DOCTYPE html>
<html style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
<head>
<meta name=\"viewport\" content=\"width=device-width\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<title>$subjectEmail</title>
</head>
<body bgcolor=\"#f6f6f6\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; -webkit-font-smoothing: antialiased; height: 100%; -webkit-text-size-adjust: none; width: 100% !important; margin: 0; padding: 0;\">

<!-- body -->
<table class=\"body-wrap\" bgcolor=\"#f6f6f6\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: 100%; margin: 0; padding: 20px;\"><tr style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
<td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\"></td>
    <td class=\"container\" bgcolor=\"#FFFFFF\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; display: block !important; max-width: 600px !important; Margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0;\">

      <!-- content -->
      <div class=\"content\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; display: block; max-width: 600px; margin: 0 auto; padding: 0;\">
      <table style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: 100%; margin: 0; padding: 0;\"><tr style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
<td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
            <p style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.2em; font-weight: normal; margin: 0px; padding: 0;\">Hi Admin CocoaTrace,</p>
            <p style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.2em; font-weight: normal; margin: 0px; padding: 0;\">&nbsp;</p>
            <p style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.2em; font-weight: normal; margin: 0px; padding: 0;\">Here are the detailed feedback submitted from CocoaTrace Application.</p>
            <br />

            <table width=\"100%\">
               <tr>
                  <td width=\"22%\">From</td>
                  <td width=\"4%\">:</td>
                  <td>".$arrVarEmail["nama"]."</td>
               </tr>
               <tr>
                  <td>Email</td>
                  <td>:</td>
                  <td>".$arrVarEmail["email"]."</td>
               </tr>
               <tr>
                  <td>Title</td>
                  <td>:</td>
                  <td>".$arrVarEmail["title"]."</td>
               </tr>
               <tr>
                  <td valign=\"top\">Content</td>
                  <td valign=\"top\">:</td>
                  <td>".nl2br($arrVarEmail["content"])."</td>
               </tr>
            </table>

            <br /><br /><br /><br />
            <p style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;font-style: italic;\">Auto generated email from Cocoatrace Application</p>
          </td>
        </tr></table>
</div>
      <!-- /content -->

    </td>
    <td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\"></td>
  </tr></table>
<!-- /body --><!-- footer --><table class=\"footer-wrap\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; width: 100%; margin: 0; padding: 0;\"><tr style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
<td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\"></td>
    <td class=\"container\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; display: block !important; max-width: 600px !important; margin: 0 auto; padding: 0;\">

    </td>
    <td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\"></td>
  </tr></table>
<!-- /footer -->
</body>
</html>";
         break;

         case 'toUser':
$body = "
<!DOCTYPE html>
<html style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
<head>
<meta name=\"viewport\" content=\"width=device-width\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<title>$subjectEmail</title>
</head>
<body bgcolor=\"#f6f6f6\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; -webkit-font-smoothing: antialiased; height: 100%; -webkit-text-size-adjust: none; width: 100% !important; margin: 0; padding: 0;\">

<!-- body -->
<table class=\"body-wrap\" bgcolor=\"#f6f6f6\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: 100%; margin: 0; padding: 20px;\"><tr style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
<td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\"></td>
    <td class=\"container\" bgcolor=\"#FFFFFF\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; display: block !important; max-width: 600px !important; Margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0;\">

      <!-- content -->
      <div class=\"content\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; display: block; max-width: 600px; margin: 0 auto; padding: 0;\">
      <table style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: 100%; margin: 0; padding: 0;\"><tr style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
<td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
            <p style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.2em; font-weight: normal; margin: 0px; padding: 0;\">Hi ".$arrVarEmail['nama'].",</p>
            <p style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.2em; font-weight: normal; margin: 0px; padding: 0;\">&nbsp;</p>
            <p style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.2em; font-weight: normal; margin: 0px; padding: 0;\">Thank you for your submission. We have successfully received the information you provided to us, and we will get back to you within 2 business days. In the meanwhile you can email us at contact@koltiva.com or reach us at:</p>

            <br />

            PT KOLTIVA<br />
            Level 3A Wisma Metropolitan I<br />
            Jl. Jend Sudirman  Kav. 29-31<br />
            DKI Jakarta, Indonesia<br />
            Fast Response : +62811-1878-900<br />

            <br /><br /><br /><br />
            <p style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;font-style: italic;\">Auto generated email from Cocoatrace</p>
          </td>
        </tr></table>
</div>
      <!-- /content -->

    </td>
    <td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\"></td>
  </tr></table>
<!-- /body --><!-- footer --><table class=\"footer-wrap\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; width: 100%; margin: 0; padding: 0;\"><tr style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\">
<td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\"></td>
    <td class=\"container\" style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; clear: both !important; display: block !important; max-width: 600px !important; margin: 0 auto; padding: 0;\">

    </td>
    <td style=\"font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;\"></td>
  </tr></table>
<!-- /footer -->
</body>
</html>
";
         break;
      }

      return $body;
   }

}

?>
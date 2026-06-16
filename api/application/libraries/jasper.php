<?php

require_once 'jasper/vendor/autoload.php';

use Jaspersoft\Client\Client;

/**
 * Description of Jasper
 *
 * @author Koltiva
 */
class Jasper {
    
    private $serv;
    public  $client;
    private $username;
    private $password;
    public  $CI;
    
    public function __construct() {
        
        $this->username = "";//"koltivabi";
        $this->password = "";//"Bismillah-123";
        $this->CI =& get_instance();
        //$this->serv = $this->CI->config->item('jasper-server');
        $this->serv = 'https://bi.koltiva.com/jasperserver';
        $this->client = new Client(
            $this->serv,
            $this->username,
            $this->password
        );
        
        /*
         * author ardi
         * cek dulu apakah user sudah login ato belum, soalnya API bisa di akses tanpa login
         */
        if(array_key_exists('username', $_SESSION)){
            $this->getSessionUser();
        }
    }
    
    protected function getSessionUser() {
        
        //$this->username = $_SESSION['username'];
        //$this->password = '12345';
        /*if(array_key_exists('username', $_SESSION)){
            if(strlen($_SESSION['username']) > 0 && $_SESSION['username'] != 'admin'){
                $this->username = 'swisscontact';
                $this->password = '12345';
            }
        }*/
		$sql = "SELECT PartnerID FROM ktv_private_staff WHERE UserId=?";
		$query = $this->CI->db->query($sql, array($_SESSION['userid']));
		$PartnerID = @$query->row()->PartnerID;
		$this->password = 'cocoatracejasper';
		if($PartnerID=='2'){
			$this->username = 'cocoatrace_seco';
		}else if($PartnerID=='3'){
			$this->username = 'cocoatrace_idh';
		}else if($PartnerID=='4'){
			$this->username = 'cocoatrace_ekn';
		}else if($PartnerID=='5'){
			$this->username = 'cocoatrace_nestle';
		}else if($PartnerID=='6'){
			$this->username = 'cocoatrace_armajaro';
		}else if($PartnerID=='7'){
			$this->username = 'cocoatrace_adm';
		}else if($PartnerID=='8'){
			$this->username = 'cocoatrace_cargil';
		}else if($PartnerID=='9'){
			$this->username = 'cocoatrace_mars';
		}else if($PartnerID=='10'){
			$this->username = 'cocoatrace_utz';
		}else if($PartnerID=='11'){
			$this->username = 'cocoatrace_barry_callebout';
		}else if($PartnerID=='12'){
			$this->username = 'cocoatrace_iccri';
		}else if($PartnerID=='13'){
			$this->username = 'cocoatrace_ecom';
		}else if($PartnerID=='14'){
			$this->username = '';
		}else if($PartnerID=='15'){
			$this->username = 'cocoatrace_cocoa_care';
		}else if($PartnerID=='16'){
			$this->username = 'cocoatrace_rainforest_alliance';
		}else if($PartnerID=='17'){
			$this->username = 'cocoatrace_mondelez';
		}else if($PartnerID=='18'){
			$this->username = 'cocoatrace_pisagro';
		}else if($PartnerID=='19'){
			$this->username = 'cocoatrace_ifad';
		}else if($PartnerID=='20'){
			$this->username = 'cocoatrace_veco';
		}else if($PartnerID=='21'){
			$this->username = 'cocoatrace_BT_cocoa';
		}else if($PartnerID=='22'){
			$this->username = 'cocoatrace_JB_cocoa';
		}else if($PartnerID=='24'){
			$this->username = 'cocoatrace_mcai';
		}else{
			$this->username = 'cocoatrace_admin';
		}
    }
    
    private function jasper_hash($string) {
        
        $salt = 'Bismillah-123';
        
        $output = '';
        
        return $output;
    }
    
    public function runReport($report,$type = 'html',$pages = null) {
        
        return $this->serv->reportService()->runReport($report,$type,$pages);
    }
    
    public function drawReportViewer($report = false, $return = false) {
        
        $output  = '';
        
        
        if(is_string($report) && $report !== ''){
            $output .= '<iframe src="'.$this->serv.'/flow.html?_flowId=viewReportFlow&reportUnit='.$report.'&j_username='.$this->username.'&j_password='.$this->password.'" style="width:100%; height:1145px !important; border:none;" frameborder="0" scrolling="no"></iframe>';
        } else {
            $output .= '<iframe src="'.$this->serv.'/flow.html?_flowId=searchFlow&j_username='.$this->username.'&j_password='.$this->password.'" style="width:100%; height:1145px !important; border:none;" frameborder="0" scrolling="no"></iframe>';
        }
        
        
        //$output .= '<iframe src="'.base_url().'common/tiny" style="width:100%; height:1145px !important; border:none;" frameborder="0" scrolling="no"></iframe>';
        
        if($return){
            return $output;
        }
        
        echo $output;
    }
    //put your code here
}

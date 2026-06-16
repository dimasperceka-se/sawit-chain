  <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  class Cashier_print extends CI_Controller {


  	function cetak($nominal,$total,$date,$actual,$cashbox,$difference,$remarks,$name)
  	{
  		$nom = explode('.', $nominal);
  		
  		$data = array(
  				'nameCashier'=>$name,
  				'Lembar100'=>$nom[0] == 'null' ? 0 : $nom[0],
  				'Lembar50'=>$nom[1] == 'null' ? 0 : $nom[1],
  				'Lembar20'=>$nom[2] == 'null' ? 0 : $nom[2],
  				'Lembar10'=>$nom[3] == 'null' ? 0 : $nom[3],
  				'Lembar5'=>$nom[4] == 'null' ? 0 : $nom[4],
  				'Lembar2'=>$nom[5] == 'null' ? 0 : $nom[5],
  				'Lembar1'=>$nom[6] == 'null' ? 0 : $nom[6],
  				'Koin1'=>$nom[7] == 'null' ? 0 : $nom[7],
  				'Koin5rts'=>$nom[8] == 'null' ? 0 : $nom[8],
  				'Koin2rts'=>$nom[9] == 'null' ? 0 : $nom[9],
  				'Koin1rts'=>$nom[10] == 'null' ? 0 : $nom[10],
  				'Koin50'=>$nom[11] == 'null' ? 0 : $nom[11],
  				'TotalCashBox'=>$total,
  				'dateCashCount'=>$date,
  				'ActualBalance'=>$actual,
  				'TotalCashBoxRight'=>$cashbox,
  				'balanceCashCount'=>$difference,
  				'remarks'=>$remarks
  			);
  		// print_r($data);
  		$this->load->view('print_cashbox',$data);
  	}

  }
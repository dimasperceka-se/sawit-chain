<style>
    .summary, .groupuser {
        background: #99bbe8 none repeat scroll 0 0;
        color: #4f6b72;
        font: 11px arial,helvetica,tahoma,sans-serif;
        width: 100%;
        border: 1px solid #99BBE8;
    }
    .summary #title, .groupuser #title .summary #title {
        background: #dfe8f6 none repeat scroll 0 0;
        color: #416aa3;
        font-weight: normal;
        padding: 3px 3px 3px 10px;
        width: 40%;
    }
    .summary #title1, .groupuser #title1 .summary #title1 {
        background: #dfe8f6 none repeat scroll 0 0;
        color: #416aa3;
        font-weight: normal;
        padding: 3px 3px 3px 10px;
        border: 1px solid #99BBE8;
    }
    .summary #cont, .groupuser #cont {
        background-color: #fff;
        color: #000000;
        padding: 3px 3px 3px 10px;
        border: 1px solid #99BBE8;
    }

</style>
<?php
	  function bulan($periode){
		switch($periode){
			case 1:
				$bulan = 'Januari';
				break;
			case 2:
				$bulan = 'Februari';
				break;
			case 3:
				$bulan = 'Maret';
				break;
			case 4:
				$bulan = 'April';
				break;
			case 5:
				$bulan = 'Mei';
				break;
			case 6:
				$bulan = 'Juni';
				break;
			case 7:
				$bulan = 'Juli';
				break;
			case 8:
				$bulan = 'Agustus';
				break;
			case 9:
				$bulan = 'September';
				break;
			case 10:
				$bulan = 'Oktober';
				break;
			case 11:
				$bulan = 'November';
				break;
			case 12:
				$bulan = 'Desember';
				break;
			default:
				$bulan = '';
		}
		
		return $bulan;
	  }
	  ?>
<table width="100%" cellspacing="1" cellpadding="1" class="summary"  border="<?php echo $border; ?>">
  <tr>
    <td colspan="2" id="title1"><div align="center"><b>Profit Loss</b></div></td>
  </tr>
  <tr>
    <td width="13%" id="title1"><b>Print Out Date</b></td>
    <td id="cont"><b><?php echo date("d M Y G:i:s");?></b></td>
  </tr>
</table>


<br />

<table cellspacing="1" cellpadding="1" class="summary" border="<?php echo $border; ?>">
  <tr>
      <td id="title1" width="150" rowspan="2"><div align="center"><b>Account</b></div></td>
      <td id="title1" width="350" rowspan="2"><div align="center"><b>Account Name</b></div></td>
      <td id="title1" width="150" rowspan="2"><div align="center"><b>Budget</b></div></td>
    <?php foreach($months as $periode){ ?>
      <td id="title1" colspan="2" align="center"><b><?php echo bulan($periode); ?></b></td>
    <?php } ?>
      <td id="title1" width="150" rowspan="2"><div align="center"><b>Net Budget</b></div></td>
  </tr>
  <tr>
    <?php foreach($months as $headers){ ?>
      <td id="title1" align="center"><b>DEBET</b></td>
      <td id="title1" align="center"><b>KREDIT</b></td>
    <?php } ?>
  </tr>
  <?php
    foreach ($months as $balance) {
        $ctotaldebet[$balance] = 0;
        $ctotalkredit[$balance] = 0;
        $ctotal[$balance] = 0;
    }
    foreach ($output as $value) { 
		$total_debet = 0;
		$total_kredit = 0;
        foreach ($value as $coa => $child) {
			$total = 0;
			if(count($child['CHILDREN']) > 0){
				foreach($child['CHILDREN'] as $childkey => $childval){
				
					if(array_key_exists('VALUE', $childval)){
						foreach($months as $balance){
							$total += $childval['VALUE'][$balance]['DEBET'] + $childval['VALUE'][$balance]['KREDIT'];
						}
					}	
				}
			}
			
			if($total > 0){
				foreach ($months as $balance) {
					$gtotaldebet[$balance] = 0;
					$gtotalkredit[$balance] = 0;
					$gtotal[$balance] = 0;
				}
				echo '<tr><td id="cont" colspan="'.(count($months) * 2 + 4).'"><b>'.$child['GROUP'].'</b></td></tr>';
				foreach($child['CHILDREN'] as $childkey => $childval){
					if(array_key_exists('VALUE', $childval)){
						$rowtotal = 0;
						foreach($months as $balance){
							if($childval['VALUE'][$balance]['DEBET'] > 0 || $childval['VALUE'][$balance]['KREDIT'] > 0){
								$ctotaldebet[$balance] += $childval['VALUE'][$balance]['DEBET'];
                                $ctotalkredit[$balance] += $childval['VALUE'][$balance]['KREDIT'];
								$gtotaldebet[$balance] += $childval['VALUE'][$balance]['DEBET'];
                                $gtotalkredit[$balance] += $childval['VALUE'][$balance]['KREDIT'];
                                $ctotal[$balance] = $ctotalkredit[$balance] - $ctotaldebet[$balance];
                                $rowtotal += $ctotalkredit[$balance] - $ctotaldebet[$balance];
							}
							
						}
					}
					
					if(abs($rowtotal) > 0){
						if(array_key_exists('VALUE', $childval)){
							echo '<tr>'
							. '<td id="cont">'.$childval['coaCode'].'&nbsp;</td>'
							. '<td id="cont">'.$childval['coaTitle'].'&nbsp;</td>'
                            . '<td id="cont" align="right">'.number_format($childval['BUDGET'],2).'</td>';
                            $totalbudget = $childval['BUDGET'];
							foreach($months as $balance){
                                $totalbudget -= ($childval['VALUE'][$balance]['DEBET'] - $childval['VALUE'][$balance]['KREDIT']);
                                echo '<td id="cont" width="180" align="right">'.number_format($childval['VALUE'][$balance]['DEBET'],2).'</td>'
								. '<td id="cont" width="180" align="right">'.number_format($childval['VALUE'][$balance]['KREDIT'],2).'</td>';
							}
                            echo '<td id="cont" width="180" align="right">'.number_format($totalbudget,2).'</td>';
							echo '</tr>';
						}
					}
				}
				
				echo '<tr><td id="cont" colspan="3" style="border-bottom:2px solid #98bcfa"><b>Total '.$child['GROUP'].'</b></td>';
					
				foreach($months as $balance){								
					echo '<td id="cont" width="180" align="right" style="border-bottom:2px solid #98bcfa"><b>'.number_format($gtotaldebet[$balance],2).'</b></td>'
					. '<td id="cont" width="180" align="right" style="border-bottom:2px solid #98bcfa"><b>'.number_format($gtotalkredit[$balance],2).'</b></td>';
				}
                echo '<td id="cont" width="180" align="right" style="border-bottom:2px solid #98bcfa"><b>'.number_format($gtotalkredit[$balance],2).'</b></td>';
				echo '</tr>';
			}
			
			
        }
    }
  ?>
  <tr>
      <td id="title1" width="150" colspan="2"><div align="center"><b>Total</b></div></td>
      <td id="title1" width="350" ><div align="center"><b></b></div></td>
    <?php foreach($months as $headers => $totalmonths){ ?>
      <td id="title1" align="right"><b><?php echo number_format($ctotaldebet[$totalmonths], 2); ?></b></td>
	  <td id="title1" align="right"><b><?php echo number_format($ctotalkredit[$totalmonths], 2); ?></b></td>
    <?php } ?>

      <td id="title1" width="350" ><div align="center"><b></b></div></td>
  </tr>
  <tr>
      <td id="title1" width="150" colspan="2"><div align="center"><b>Total Net Profit/Loss</b></div></td>
    <?php foreach($months as $headers => $totalmonths){ ?>
      <td id="title1" align="right"><b>&nbsp;</b></td>
	  <td id="title1" align="right"><b><?php echo number_format($ctotal[$totalmonths], 2); ?></b></td>
    <?php } ?>
      <td id="title1" width="350" ><div align="center"><b></b></div></td>
      <td id="title1" width="350" ><div align="center"><b></b></div></td>
  </tr>
  </table>
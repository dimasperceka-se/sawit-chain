<?php

// print_r($data);
// echo 'a'.$memberData;
$memberData = $data->memberData[0];
?>
<html>
<body onload="window.print()">
<center><h1>BALANCE STATEMENT</h1></center>

<table border='0'>
    <tr>
        <td>No Anggota</td>
        <td>:</td>
        <td width="80%"><?=$memberData->primaryNo?></td>
    </tr>
    <tr>
        <td>Nama Anggota</td>
        <td>:</td>
        <td width="80%"><?=$memberData->name?></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>

<table border='1' width="100%" style="border-spacing:1;">
    <tr>
        <td rowspan="2"><b>NO</b></td>
        <td rowspan="2"><center><b>TANGGAL</b></center></td>
        <td rowspan="2"><center><b>KODE</b></center></td>
        <td  rowspan="2" width="6%"><center><b>TIPE</b></center></td>
        <td colspan="2"><center><b>MUTASI</b></center></td>
        <td rowspan="2"><center><b>SALDO</b></center></td>
    </tr>
    <tr>
	        <!-- <td></td> -->
	        <!-- <td></td> -->
	        <!-- <td></td> -->
	        <!-- <td></td> -->
	        <td><center><b>DEBIT</b></center></td>
	        <td><center><b>KREDIT</b></center></td>
	        <!-- <td></td> -->
	    </tr>
    <?php
    $no=1;
    $saldo=0;
    foreach ($data->data as $key => $value) {
    	// print_r($value->memberTransactionDate);
    	//memberTransactionType : 1: Setoran; 2: Tarikan
    	?>
    	<tr>
	        <td><?=$no?></td>
	        <td><?=$value->memberTransactionDate?></td>
	        <td><?=$value->savingTypeCode?></td>

	        <?php
	        if($value->memberTransactionType==1) //setoran
	        {
	        	//kredit
	        	?>
	        		<td>DEPOSIT</td>
	        		<td align="right"></td>
	        		<td align="right"><?=number_format($value->memberTransactionAmount)?></td>
	        	<?php
	        	$saldo+=$value->memberTransactionAmount;
	        } else {
	        	//debit
				?>	<td>WITHDRAWAL</td>
	        		<td align="right"><?=number_format($value->memberTransactionAmount)?></td>
	        		<td align="right"></td>
	        	<?php
	        	$saldo-=$value->memberTransactionAmount;
	        }
	        ?>
	        <td align="right"><?=number_format($saldo)?></td>
	    </tr>
    	<?php
    	$no++;
    }
    ?>
    <tr>
    	<td  align="center" colspan="6"><b>ENDING BALANCE</b></td>
    	<td align="right"><?=number_format($saldo)?></td>
    </tr>
</table>

</body>
</html>
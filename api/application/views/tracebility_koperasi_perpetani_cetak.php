<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8">
	<title>Tracebility</title>

<style>
 body {
    margin: 0;
    padding: 0;
    background-color: #FAFAFA;
    font: 9pt "verdana";
}
* {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}
.page {
    width: 27.5cm;
    height:21cm;
    padding: 1.5cm;
    margin: 0.2cm auto;
    border: 1px #D3D3D3 solid;
    border-radius: 5px;
    background: white;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

@page {
    size: A4;
    margin: 0;
}

@media print {
    .page {
        margin: initial;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
        background-color: initial;

        padding-bottom:0;
    }
   .header{border:90px #cccccc;}
}

input {border: 1px solid;background-color: #FFF}
input:disabled {color: #000000;}
.line{border: 1px solid black;}
td{vertical-align:top}
@media all {
   .header{  font-size: 10pt;font-family: verdana;padding: 0px;margin: 0px;border: 1px solid;font-weight: bold;}
   .body{font-size: 9pt;font-family: verdana;font-weight: normal;padding: 0px;margin: 0px;border: 1px solid;}
   .header_div{width: 100%; height: 20px; margin-bottom: -28px; border-top: 28px solid #cccccc;}
}     
</style>
</head>
<body>
<div class="page">

   <table width="100%" cellspacing="0">
      <tr><td height="60"  width="25%" rowspan="2" align="center" style="vertical-align:middle;">
            <!--<img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:100%; max-height:100%;">--></td>
         <td height="60" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;">DATA Tracebility</td>
         <td height="60" rowspan="2" width="25%" align="center" style="vertical-align:middle;">
            <!--<img src="<?=base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;">--></td></tr>
   </table>

   <div class="header_div" style="margin-bottom:-20px;border-top:20px solid #cccccc;height:5px"></div>
  
   <table width="100%" cellspacing="0" class="body">
   <tr><th>No</th><th>IP Petani</th><th>Nama Petani</th><th>Tanggal Penjualan</th><th>Batch No</th>
      <th>Nama Pedagang/Koperasi</th><th>Survey</th><th>Bruto</th><th>Netto</th><th>Sisa</th><!--<th>Harga</th>--></tr>
   <?$no=1;$a=0;
   for ($i=0;$i<sizeof($detail);$i++) {$a++;$aa=false;
      $row = 1;
      for ($j=$i+1;$j<sizeof($detail);$j++) {
         if ($detail[$i]['FarmerID']==$detail[$j]['FarmerID']) $row++;
         else break;
      }
      $bruto += $detail[$i]['FAQVolumeBruto'];
      $netto += $detail[$i]['FAQVolumeNetto'];
      $survey += $detail[$i]['survey'];
      if($detail[$i]['FarmerID']!=$detail[$i-1]['FarmerID']) {$roww=1;?>
      <?if($i>0){$a++;$aa=true;?>
      <tr><td colspan="6" align="center">
         <div class="header_div" style="margin-bottom:-17px;border-top:17px solid #eeeeee;height:5px"></div>
         TOTAL</td>
         <td align="right">
         <div class="header_div" style="margin-bottom:-17px;border-top:17px solid #eeeeee;height:5px"></div>
         <?=number_format($survey,2,',','.')?></td>
         <td align="right">
         <div class="header_div" style="margin-bottom:-17px;border-top:17px solid #eeeeee;height:5px"></div>
         <?=number_format($bruto,2,',','.')?></td>
         <td align="right">
         <div class="header_div" style="margin-bottom:-17px;border-top:17px solid #eeeeee;height:5px"></div>
         <?=number_format($netto,2,',','.')?></td>
         <td align="right">
         <div class="header_div" style="margin-bottom:-17px;border-top:17px solid #eeeeee;height:5px"></div>
         <?=number_format($survey,2,',','.')?></td></tr>
      <?$bruto=$netto=$survey=0;}?>
      <tr>
         <td rowspan="<?=$row?>"><?=$no?></td><td rowspan="<?=$row?>"><?=$detail[$i]['FarmerID']?></td>
         <td rowspan="<?=$row?>"><?=$detail[$i]['FarmerName']?></td>
         <td><?=SetTanggal($detail[$i]['DateTransaction'])?></td><td><?=$detail[$i]['SupplyBatchNumber']?></td>
         <td><?=$detail[$i]['Name']?></td><td rowspan="<?=$row?>" align="right"><?=number_format($detail[$i]['survey'],2,',','.')?></td>
         <td align="right"><?=number_format($detail[$i]['FAQVolumeBruto'],2,',','.')?></td>
         <td align="right"><?=number_format($detail[$i]['FAQVolumeNetto'],2,',','.')?></td>
         <!--<td align="right"><?=number_format($detail[$i]['FAQNetPrice'],2,',','.')?></td>--></tr>
      <?$no++;} else {?>
      <tr>
         <?if($roww>1){echo '<td></td><td></td><td></td>';}?>
         <td><?=SetTanggal($detail[$i]['DateTransaction'])?></td><td><?=$detail[$i]['SupplyBatchNumber']?></td>
         <td><?=$detail[$i]['Name']?></td><?if($roww>1){echo '<td></td>';$roww--;}?>
         <td align="right"><?=number_format($detail[$i]['FAQVolumeBruto'],2,',','.')?></td>
         <td align="right"><?=number_format($detail[$i]['FAQVolumeNetto'],2,',','.')?></td>
         <!--<td align="right"><?=number_format($detail[$i]['FAQNetPrice'],2,',','.')?></td>--></tr>
      <?}
      if (($a+1) % 30==0 OR ($aa and $a % 30==0)) {$roww=$row?></table></div>
<div class="page">

   <table width="100%" cellspacing="0">
      <tr><td height="60"  width="25%" rowspan="2" align="center" style="vertical-align:middle;">
            <!--<img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:100%; max-height:100%;">--></td>
         <td height="60" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;">DATA Tracebility</td>
         <td height="60" rowspan="2" width="25%" align="center" style="vertical-align:middle;">
            <!--<img src="<?=base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;">--></td></tr>
   </table>

   <div class="header_div" style="margin-bottom:-20px;border-top:20px solid #cccccc;height:5px"></div>
  
   <table width="100%" cellspacing="0" class="body">
   <tr><th>No</th><th>IP Petani</th><th>Nama Petani</th><th>Tanggal Penjualan</th><th>Batch No</th>
      <th>Nama Pedagang/Koperasi</th><th>Survey</th><th>Bruto</th><th>Netto</th><!--<th>Harga</th>--><th>Sisa</th></tr>
      <?}
   }?>
   </table>

</div>
</body>
</html>

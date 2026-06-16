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
    width: 21cm;
    height:27.5cm;
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

   <div class="header_div" style="margin-bottom:-20px;border-top:20px solid #cccccc"></div> 
   <table width="100%" cellspacing="0" class="body">
   <tr><th>No</th><th>Petani</th><th>Bruto</th><th>Netto</th><th>Premium (<?=$detail[0]['PersenPetani']?>%)</th>
      <th>Total Premium</th></tr>
   <?for ($i=0;$i<sizeof($detail);$i++) {
      $premium = $detail[0]['PersenPetani']/100*$detail[$i]['Rupiah'];?>
      <tr>
         <td><?=$i+1?></td><td><?=$detail[$i]['Name']?></td>
         <td align="right"><?=number_format($detail[$i]['bruto'],2,',','.')?></td>
         <td align="right"><?=number_format($detail[$i]['netto'],2,',','.')?></td>
         <td align="right"><?=number_format($premium,2,',','.')?></td>
         <td align="right"><?=number_format($detail[$i]['netto']*$premium/1000,2,',','.')?></td>
      </tr>
   <?}?>
   </table>

</div>
</body>
</html>

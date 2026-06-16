<style>
 body {
    margin: 0;
    padding: 0;
    background-color: #FAFAFA;
    font: 10pt "verdana";
}
* {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}
.page {
    width: 21cm;
    height:27.7cm;
    padding: 2cm;
    margin: 1cm auto;
    border: 1px #D3D3D3 solid;
    border-radius: 5px;
    background: white;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}
.subpage {
    padding: 1cm;
    border: 5px red solid;
    height: 256mm;
    outline: 2cm #FFEAEA solid;
}

@page {
    size: A4;
    margin: 0;
}
@media print {
    .page {
        margin: 0;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
    }
    .page-break	{ display: block; page-break-before: always; }
}
    h4 {
        text-align: center;
        font-size: 10pt;
        font-family: verdana;
    }
    
    .title {
        font-size: 10pt;
        font-family: verdana;
        font-weight: normal;
    }
    
    .table-print {
        font-size: 10pt;
        font-family: verdana;
        font-weight: normal;
        padding: 0px;
        margin: 0px;
        border-top: 1.5px solid #333333;
        border-left: 1.5px solid #333333;
        border-collapse: collapse;
    }
    
    .table-print th {
        text-align: center;
        border-right: 1.5px solid #333333;
        border-bottom: 1.5px solid #333333;
        padding: 5px;
        margin:0px;


    }
    
    .table-print td {
        text-align: left;
        border-right: 1.5px solid #333333;
        border-bottom: 1.5px solid #333333;
        padding: 1px;
        margin:0px;
        font-weight: normal;
    }
    td{
      font-size:11px;
    }
</style>
<body>
<?$detail = $detail['data'];?>
<div class="page">
   <div style="text-align:right;width:100%;font-size:9px">1 - lembar petani</div>
   <br>
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="15%" align="center"><!--<img src="<?=base_url()?>images/LogoCargill CocoaPromise.jpg">--></td>
            <td align="center"><?=$data['District'].', '.SetTanggal($data['DateTransaction']).'<br>
               Nama Pedagang : '.$data['Name'].'<br>Nama Petani : ['.$data['FarmerID'].'] '.$data['farnam'].
               ($data['GroupName']!=''?'<br>Nama CPG :'.$data['GroupName']:'')?></td>
            <td width="15%" align="center"><!--<img src="<?=base_url()?>images/LogoCargill.jpg">--></td>
         </tr>
         <tr><td colspan="3" style="text-align: center; font-weight: bold;"><br>NO KUITANSI: <?=$data['SupplyTransID']?><br><br></td>
      </table>
   </div>   
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th rowspan="2">Uraian</th><th colspan="2">Satuan</th><th colspan="2">Harga Satuan</th><th colspan="2">Jumlah</th></tr>
      <tr><th>FF</th><th>FAQ</th><th>FF</th><th>FAQ</th><th>FF</th><th>FAQ</th></tr>
      <tr><td>Berat</td><td><?=$data['FFVolumeNetto']?> Kg</td><td><?=$data['FAQVolumeNetto']?> Kg</td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FFNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FFNetPrice']*$data['FFVolumeNetto'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['FAQVolumeNetto'],2,',','.')?></td></tr>
      <tr><td>KA</td><td><?for ($j=0;$j<sizeof($detail);$j++) if ($detail[$j]['Type']=='FF') {echo $detail[$j]['Moisture'];break;}?> %</td>
         <td><?for ($j=0;$j<sizeof($detail);$j++) if ($detail[$j]['Type']=='FAQ') {echo $detail[$j]['Moisture'];break;}?> %</td></tr>
      <tr><td>Sampah</td><td></td><td></td></tr>
      <tr><td>Jamur</td><td></td><td></td></tr>
      <tr><td>BC/Berat Biji</td><td></td><td></td></tr>
      <tr><th colspan="5">Total</th>
         <td colspan="2" style="text-align: center"><?=number_format(($data['FFNetPrice']*$data['FFVolumeNetto'])+($data['FAQNetPrice']*$data['FAQVolumeNetto']),2,',','.')?></td></tr>
   </table>         
   <span>Hormat Kami</span>
   <br><br><br>
   
   <div style="text-align:right;width:100%;font-size:9px">
   -----------------------------------------------------------------------------------------------------------------------------------------------------------<br>
   2 - lembar pedagang</div>
   <br>
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="15%" align="center"><!--<img src="<?=base_url()?>images/LogoCargill CocoaPromise.jpg">--></td>
            <td align="center"><?=$data['District'].', '.SetTanggal($data['DateTransaction']).'<br>
               Nama Pedagang : '.$data['Name'].'<br>Nama Petani : ['.$data['FarmerID'].'] '.$data['farnam'].
               ($data['GroupName']!=''?'<br>Nama CPG :'.$data['GroupName']:'')?></td>
            <td width="15%" align="center"><!--<img src="<?=base_url()?>images/LogoCargill.jpg">--></td>
         </tr>
         <tr><td colspan="3" style="text-align: center; font-weight: bold;"><br>NO KUITANSI: <?=$data['SupplyTransID']?><br><br></td>
      </table>
   </div>   
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th rowspan="2">Uraian</th><th colspan="2">Satuan</th><th colspan="2">Harga Satuan</th><th colspan="2">Jumlah</th></tr>
      <tr><th>FF</th><th>FAQ</th><th>FF</th><th>FAQ</th><th>FF</th><th>FAQ</th></tr>
      <tr><td>Berat</td><td><?=$data['FFVolumeNetto']?> Kg</td><td><?=$data['FAQVolumeNetto']?> Kg</td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FFNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FFNetPrice']*$data['FFVolumeNetto'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['FAQVolumeNetto'],2,',','.')?></td></tr>
      <tr><td>KA</td><td><?for ($j=0;$j<sizeof($detail);$j++) if ($detail[$j]['Type']=='FF') {echo $detail[$j]['Moisture'];break;}?> %</td>
         <td><?for ($j=0;$j<sizeof($detail);$j++) if ($detail[$j]['Type']=='FAQ') {echo $detail[$j]['Moisture'];break;}?> %</td></tr>
      <tr><td>Sampah</td><td></td><td></td></tr>
      <tr><td>Jamur</td><td></td><td></td></tr>
      <tr><td>BC/Berat Biji</td><td></td><td></td></tr>
      <tr><th colspan="5">Total</th>
         <td colspan="2" style="text-align: center"><?=number_format(($data['FFNetPrice']*$data['FFVolumeNetto'])+($data['FAQNetPrice']*$data['FAQVolumeNetto']),2,',','.')?></td></tr>
   </table>         
   <span>Hormat Kami</span>
   <br><br><br>
   <div style="text-align:right;width:100%;font-size:9px">
   -----------------------------------------------------------------------------------------------------------------------------------------------------------<br>
   3 - lembar warehouse</div>
   <br>
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="15%" align="center"><!--<img src="<?=base_url()?>images/LogoCargill CocoaPromise.jpg">--></td>
            <td align="center"><?=$data['District'].', '.SetTanggal($data['DateTransaction']).'<br>
               Nama Pedagang : '.$data['Name'].'<br>Nama Petani : ['.$data['FarmerID'].'] '.$data['farnam'].
               ($data['GroupName']!=''?'<br>Nama CPG :'.$data['GroupName']:'')?></td>
            <td width="15%" align="center"><!--<img src="<?=base_url()?>images/LogoCargill.jpg">--></td>
         </tr>
         <tr><td colspan="3" style="text-align: center; font-weight: bold;"><br>NO KUITANSI: <?=$data['SupplyTransID']?><br><br></td>
      </table>
   </div>   
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th rowspan="2">Uraian</th><th colspan="2">Satuan</th><th colspan="2">Harga Satuan</th><th colspan="2">Jumlah</th></tr>
      <tr><th>FF</th><th>FAQ</th><th>FF</th><th>FAQ</th><th>FF</th><th>FAQ</th></tr>
      <tr><td>Berat</td><td><?=$data['FFVolumeNetto']?> Kg</td><td><?=$data['FAQVolumeNetto']?> Kg</td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FFNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FFNetPrice']*$data['FFVolumeNetto'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['FAQVolumeNetto'],2,',','.')?></td></tr>
      <tr><td>KA</td><td><?for ($j=0;$j<sizeof($detail);$j++) if ($detail[$j]['Type']=='FF') {echo $detail[$j]['Moisture'];break;}?> %</td>
         <td><?for ($j=0;$j<sizeof($detail);$j++) if ($detail[$j]['Type']=='FAQ') {echo $detail[$j]['Moisture'];break;}?> %</td></tr>
      <tr><td>Sampah</td><td></td><td></td></tr>
      <tr><td>Jamur</td><td></td><td></td></tr>
      <tr><td>BC/Berat Biji</td><td></td><td></td></tr>
      <tr><th colspan="5">Total</th>
         <td colspan="2" style="text-align: center"><?=number_format(($data['FFNetPrice']*$data['FFVolumeNetto'])+($data['FAQNetPrice']*$data['FAQVolumeNetto']),2,',','.')?></td></tr>
   </table>         
   <span>Hormat Kami</span>
</div>
</body>

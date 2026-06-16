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
<div class="page">
   <div style="text-align:right;width:100%;font-size:9px">1 - lembar koperasi</div>
   <br>
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr>
            <td rowspan="4" width="15%" align="center">
              <?php if($data['logo_koperasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_koperasi']?>" width="70" height="50">
              <? } ?>
            </td>
            <td rowspan="4" width="20%" align="center"><?=$data['Name']?><br><?=$data['District']?></td>
            <td rowspan="4" width="15%" align="center">
              <?php if($data['logo_sertifikasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_sertifikasi']?>" width="70" height="50">
              <? } ?>
            </td>
            <td colspan="2">Kolaka Timur, <?=SetTanggal($data['DateTransaction'])?></td>
         </tr>
         <tr>
            <td width="20%">Nama Petani</td><td>: <?=$data['farnam']?></td>
         </tr>
         <tr>
            <td>ID Petani</td><td>: <?=$data['FarmerID']?></td>
         </tr>
         <tr>
            <td>Nama Gapoktan</td><td>: <?=$data['GroupName']?></td>
         </tr>
         
         <tr><td colspan="3" style="height: 30px; text-align: center; font-weight: bold;">NOTA PEMBELIAN</td>
            <td colspan="2" style="text-align: center; font-weight: bold;">NO: <?=$data['SupplyTransID']?></td>
      </table>
   </div>   
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th>Uraian</th><th>Satuan</th><th>Harga Satuan</th><th>Jumlah</th></tr>
      <tr><td>Berat</td><td><?=$detail['data'][0]['Weight']?> Kg</td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['Weight'],2,',','.')?></td></tr>
      <tr><td>KA</td><td><?=$detail['data'][0]['Moisture']?> %</td></tr>
      <tr><td>Sampah</td><td></td></tr>
      <tr><td>Jamur</td><td></td></tr>
      <tr><td>BC/Berat Biji</td><td></td></tr>
      <tr><td colspan="2" style="border-left: 1px solid #fff; border-bottom: none;"></td><th>Total</th>
         <td style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['Weight'],2,',','.')?></td></tr>
   </table>         
   <span>Hormat Kami</span>
   <br><br><br><br>
   
   <div style="text-align:right;width:100%;font-size:9px">
   -----------------------------------------------------------------------------------------------------------------------------------------------------------<br>
   2 - lembar perwakilan</div>
   <br>
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr>
            <td rowspan="4" width="15%" align="center">
              <?php if($data['logo_koperasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_koperasi']?>" width="70" height="50">
              <? } ?>
            </td>
            <td rowspan="4" width="20%" align="center"><?=$data['Name']?><br><?=$data['District']?></td>
            <td rowspan="4" width="15%" align="center">
              <?php if($data['logo_sertifikasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_sertifikasi']?>" width="70" height="50">
              <? } ?>
            </td>
            <td colspan="2">Kolaka Timur, <?=SetTanggal($data['DateTransaction'])?></td>
         </tr>
         <tr>
            <td width="20%">Nama Petani</td><td>: <?=$data['farnam']?></td>
         </tr>
         <tr>
            <td>ID Petani</td><td>: <?=$data['FarmerID']?></td>
         </tr>
         <tr>
            <td>Nama Gapoktan</td><td>: <?=$data['GroupName']?></td>
         </tr>
         
         <tr><td colspan="3" style="height: 30px; text-align: center; font-weight: bold;">NOTA PEMBELIAN</td>
            <td colspan="2" style="text-align: center; font-weight: bold;">NO: <?=$data['SupplyTransID']?></td>
      </table>
   </div>   
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th>Uraian</th><th>Satuan</th><th>Harga Satuan</th><th>Jumlah</th></tr>
      <tr><td>Berat</td><td><?=$detail['data'][0]['Weight']?> Kg</td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['FAQVolumeNetto'],2,',','.')?></td></tr>
      <tr><td>KA</td><td><?=$detail['data'][0]['Moisture']?> %</td></tr>
      <tr><td>Sampah</td><td></td></tr>
      <tr><td>Jamur</td><td></td></tr>
      <tr><td>BC/Berat Biji</td><td></td></tr>
      <tr><td colspan="2" style="border-left: 1px solid #fff; border-bottom: none;"></td><th>Total</th>
         <td style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['FAQVolumeNetto'],2,',','.')?></td></tr>
   </table>         
   <span>Hormat Kami</span>
   <br><br><br><br>
   <div style="text-align:right;width:100%;font-size:9px">
   -----------------------------------------------------------------------------------------------------------------------------------------------------------<br>
   3 - lembar warehouse</div>
   <br>
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr>
            <td rowspan="4" width="15%" align="center">
              <?php if($data['logo_koperasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_koperasi']?>" width="70" height="50">
              <? } ?>
            </td>
            <td rowspan="4" width="20%" align="center"><?=$data['Name']?><br><?=$data['District']?></td>
            <td rowspan="4" width="15%" align="center">
              <?php if($data['logo_sertifikasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_sertifikasi']?>" width="70" height="50">
              <? } ?>
            </td>
            <td colspan="2">Kolaka Timur, <?=SetTanggal($data['DateTransaction'])?></td>
         </tr>
         <tr>
            <td width="20%">Nama Petani</td><td>: <?=$data['farnam']?></td>
         </tr>
         <tr>
            <td>ID Petani</td><td>: <?=$data['FarmerID']?></td>
         </tr>
         <tr>
            <td>Nama Gapoktan</td><td>: <?=$data['GroupName']?></td>
         </tr>
         
         <tr><td colspan="3" style="height: 30px; text-align: center; font-weight: bold;">NOTA PEMBELIAN</td>
            <td colspan="2" style="text-align: center; font-weight: bold;">NO: <?=$data['SupplyTransID']?></td>
      </table>
   </div>   
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th>Uraian</th><th>Satuan</th><th>Harga Satuan</th><th>Jumlah</th></tr>
      <tr><td>Berat</td><td><?=$detail['data'][0]['Weight']?> Kg</td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice'],2,',','.')?></td>
         <td rowspan="5" style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['FAQVolumeNetto'],2,',','.')?></td></tr>
      <tr><td>KA</td><td><?=$detail['data'][0]['Moisture']?> %</td></tr>
      <tr><td>Sampah</td><td></td></tr>
      <tr><td>Jamur</td><td></td></tr>
      <tr><td>BC/Berat Biji</td><td></td></tr>
      <tr><td colspan="2" style="border-left: 1px solid #fff; border-bottom: none;"></td><th>Total</th>
         <td style="text-align: center"><?=number_format($data['FAQNetPrice']*$data['FAQVolumeNetto'],2,',','.')?></td></tr>
   </table>         
   <span>Hormat Kami</span>
</div>
</body>

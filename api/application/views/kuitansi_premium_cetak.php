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
      font-size:10pt;
    }
</style>
<body>
<div class="page">
   <?for ($j=1;$j<3;$j++) {?>
   <div style="text-align:right;width:100%;font-size:9px">lembar <?=$j?></div>
   <br>
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="25%" align="left" rowspan="2">
              <?php if($data['logo_koperasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_koperasi']?>" width="<?php if($data['logo_koperasi']=='coop/20160825120221_Logo Koperasi Sekata.png'){echo 150;}else{echo 80;}?>" height="80">
              <?php } ?>
            </td>
            <td align="center" style="text-align: center; font-weight: bold;"><?=$data['nama']?><br><?=$data['keterangan']?></td>
            <td width="25%" align="right" rowspan="2">
              <?php if($data['logo_koperasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_sertifikasi']?>" width="80" height="80"></td>            
              <?php } ?>
         </tr>
         <tr><td align="center" style="height: 30px; text-align: center; font-weight: bold;">
            KUITANSI<br>NO: <?=$data['nomor']?></td>
      </table>
   </div>
   <table class="table-print" cellpadding="0" width="100%" style="border-left:none;">
      <tr><td style="border:none;text-align:left;width:140px"><br><br>Terima Dari</td>
         <td style="border:none;text-align:left;width:1px"><br><br>:</td><td style="border:none;"><br><br><b><?=$data['nama']?></b></td></tr>
      <tr><td style="border:none;text-align:left">Sejumlah</td><td style="border:none;width:1px">:</td>
         <td style="border:none;text-align:left"><b><?=number_format($data['total'],2,',','.')?></b></td></tr>
      <tr><td style="border:none;text-align:left;vertical-align:top">Terbilang</td><td style="border:none;width:1px;vertical-align:top">:</td>
         <td style="border:none;text-align:left"><?=ucfirst(strtolower(number_to_words(number_format($data['total'],2,',','')))).' rupiah'?></td></tr>
      <tr><td style="border:none;text-align:left">Untuk Pembayaran</td><td style="border:none;width:1px">:</td>
         <td style="border:none;text-align:left">Pembayaran premium penjualan biji kakao seberat <?=number_format($data['PaymentNetto'],2,',','.')?> KG</td></tr>
   </table>        
   <br><br> 
   <span><?=$data['District']?>, <?=SetTanggal($data['PaymentDate'])?></span>
   <br><br>
   <div style="text-align: center;width: 25%;">Penerima
   <br><br><br><br>
	<?php 
		if (strpos($data['StaffID'], ']') !== false) {
			$staff = explode(']',$data['StaffID']);
			echo '<u>'.$staff[1].'</u><br>';
			echo '('.$staff[0].'])';
		}else{
	?>	
	<u><?=$data['StaffName']?></u><br>(<?=$data['StaffID']?>)
	<?php		
		}
	?>
   </div>
   <?if($j<2){?><br><br><br><br><div style="width:100%;border:dashed 1px"></div><?}?>
   <?}?>
</div>
<div class="page-break"></div>
</body>

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
   <?
   $detail = $detail['data'];
   $lembar = array('petani','perwakilan','warehouse');
   for ($i=0;$i<2;$i++) {?>
<!--   <div style="text-align:right;width:100%;font-size:9px"><?=$i+1?> - lembar <?=$lembar[$i]?></div>-->
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr>
            <td width="15%" align="center">
              <?php if($data['logo_koperasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_koperasi']?>" width="70" height="50">
              <? } ?>
            </td>
            <td align="center">
               <span style="font-size:14px"><b><?=$data['type_parent']=='Organisasi Petani'?'Buying Station '.$data['name_parent']:$data['Name']?></b></span><br><br>
               <!--<span style="font-size:12px"><b>(Pidie Jaya-Bireuen-Aceh Tamiang)</b></span><br><br>-->
               <span style="font-size:10px">Alamat:<?=$data['Address']?> Desa <?=$data['Desa']?> Kec. <?=$data['Kec']?> Kabupaten <?=$data['Kab']?></span></td>
            <td width="15%" align="center">
              <?php if($data['logo_sertifikasi']!=''){ ?>
              <img src="<?=base_url()?>images/<?=$data['logo_sertifikasi']?>" width="70" height="50">
              <? } ?>
            </td>
         </tr>
         <tr>
            <td colspan="3">&nbsp;</td>
         </tr>
      </table>
   </div>
   <table class="table-print" cellpadding="0" width="100%">
      <tr><td colspan="4">Nama Petani : <?=$data['farnam']?></td><th rowspan="2"><i><b><span style="font-size:28px">Faktur</span>
         <span style="font-size:12px">Pembelian</span></b></i></th></tr>
      <tr><td colspan="4">ID Petani : <?=$data['FarmerID']?></td></tr>
      <tr><td colspan="4">CPG : <?=$data['GroupName']?></td><th rowspan="2">No : <?=$data['SupplyTransID']?></th></tr>
      <tr><td colspan="4">No Buying Station : <?=$data['OrgID']?></td></tr>
      
      <tr><td colspan="5">Jenis Barang : <span style="font-size:16px"><i><b>Biji Kakao Sertifikasi</b></i></span></td></tr>
      
      <tr><td colspan="2"><b>Harga Dasar kakao</b></td><td>Standard JeBe Koko</td><td>Quality  dari Petani</td><td style="text-align:right">
         <?=number_format($data['FFTotalPayment']>0?$data['FFContractPrice']:$data['FAQContractPrice'],2,',','.')?></td></tr>

		<tr>
			<th>#</th>
			<th>Keterangan</th>
			<th></th><th></th>
			<th>Potongan</th>
		</tr>
		<?php $no=1; foreach($quality->result() as $row){ ?>
			<tr>
				<td><?=$no?></td>
				<td><?=$row->Name?></td>
				<td style="text-align:right"><?=$row->FFStandard!=''?$row->FFStandard:$row->FAQStandard?></td>
				<td style="text-align:right"><?=$row->FFResult!=''?$row->FFResult:$row->FAQResult?></td>
				<td></td>
			</tr>
		<?php $no++; } ?>
      <tr><td colspan="4">Total Potongan</td><td></td></tr>
      <tr><td colspan="4">Potongan Karung</td><td></td></tr>
      <tr><td colspan="4">Harga Setelah Potongan</td><td style="text-align:right">
         <?=number_format($data['FFTotalPayment']>0?$data['FFNetPrice']:$data['FAQNetPrice'],2,',','.')?></td></tr>
      <tr><td colspan="4">Jumlah Biji Kakao (kg)</td><td style="text-align:right">
         <?=number_format($data['FFVolumeNetto']>0?$data['FFVolumeNetto']:$data['FAQVolumeNetto'],2,',','.')?></td></tr>
      <tr><td colspan="4">Total Harga Petani</td><td style="text-align:right">
         <?$total = $data['FAQTotalPayment']>0?$data['FAQTotalPayment']:$data['FFTotalPayment']?>
         <?=number_format($total,2,',','.')?></td></tr>
      
      <tr><td colspan="5">Sudah Terima Dari: 
         <b><?=$data['type_parent']=='Organisasi Petani'?'Buying Station '.$data['name_parent']:$data['Name']?></b></td></tr>
      <tr><td colspan="5">Banyak Uang: <?=ucfirst(strtolower(number_to_words(number_format($total,2,',','')))).' rupiah'?></td></tr>
      <tr><td colspan="5">Catatan: </td></tr>
   </table><br>
   <table width="100%" cellspacing="0" cellpadding="0">
      <tr><td colspan="5"><?=$data['Kab']?>, <?=SetTanggal($data['DateTransaction'])?></td></tr>
      <tr><td colspan="4">Hormat Kami,</td><td rowspan="2">Diterima Oleh</td></tr>
      <tr><td colspan="4">Bagian Penjualan</td></tr>

   </table>         
   <br><br><br>
   <?if($i<1){?>
	</div>
   <div class="page-break"></div>
   <div class="page">
   <?}?>
   <?}?>
</div>
</body>

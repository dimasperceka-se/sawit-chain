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
   <?for ($j=1;$j<2;$j++) {?>
   <!--<div style="text-align:right;width:100%;font-size:9px">lembar <?=$j?></div>
   <br>-->
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="15%" align="right" rowspan="2"><!--<img src="<?=base_url()?>images/<?=$data['logo']?>">--></td>
            <td align="center" style="text-align: center; font-weight: bold;">
               <span style="font-size: 20px;">Buying Station <?=$data['nama']?></span><br>
               <span>(Pidiae Jaya - Bireun - Aceh Tamiang)</span><br><br>
               <span style="font-size: 10px;">alamat:<?=$data['keterangan']?></span></td>
            <td width="15%" align="right" rowspan="2"><!--<img src="<?=base_url()?>images/utz.jpg">--></td>            
         </tr>
      </table>
   </div>
   <table class="table-print" cellpadding="0" width="100%" style="border-left:none;">
      <tr><td style="border:none;text-align:center;width:140px" align="center" colspan="3"><b>
         <span style="font-size: 14px;">INVOICE</span><br>NO: <?=$data['nomor']?></b></td></tr>
      <tr><td style="border:none;text-align:left;width:140px"><br><br>Kepada</td>
         <td style="border:none;text-align:left;width:1px"><br><br>:</td><td style="border:none;"><br><br><b><?=$data['kepada']?></b></td></tr>
      <tr><td style="border:none;text-align:left">Alamat</td><td style="border:none;width:1px">:</td>
         <td style="border:none;text-align:left"><b><?=$data['alamat']?></b></td></tr>
      <tr><td style="border:none;text-align:left">Telepon</td><td style="border:none;width:1px">:</td>
         <td style="border:none;text-align:left"><b><?=$data['telepon']?></b></td></tr>
      <tr><td style="border:none;text-align:left" colspan="3"><br>
         Dengan hormat,<br>
         Bersama invoice ini kami lampirkan 1(satu) dokumen data quality biji kakao pengiriman pada tanggal 
         <?=SetTanggal($detail[0]['tanggal'])?> ke gudang <?=$data['kepada']?></td></tr>
   </table>
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th>No</th><th>Keterangan</th><th>Jumlah Karung</th><th>Berat (Kg)</th><th>Harga per Kg (Rp)</th><th>Jumlah (Rp)</th></tr>
      <?for ($i=0;$i<sizeof($detail);$i++) {
         $total_karung += $detail[$i]['karung'];
         $total_berat += $detail[$i]['DetailBerat'];
         $total_jumlah += $detail[$i]['DetailBerat']*$detail[$i]['DetailHarga'];?>
      <tr><td><?=$i+1?></td><td>Biji Kakao Sertifikasi UTZ DCC <?=$detail[$i]['keterangan']?></td>
         <td style="text-align:center"><?=$detail[$i]['karung']?></td>
         <td style="text-align:right"><?=$detail[$i]['DetailBerat']?></td>
         <td style="text-align:right"><?=number_format($detail[$i]['DetailHarga'],2,',','.')?></td>
         <td style="text-align:right"><?=number_format($detail[$i]['DetailBerat']*$detail[$i]['DetailHarga'],2,',','.')?></td>
      <?}?>
      <tr><td></td><td>Total</td><td><?=$total_karung?></td><td><?=$total_berat?></td><td></td><td><?=number_format($total_jumlah,2,',','.')?></td></tr>
      <tr><td colspan="5" align="right">Dibulatkan</td><td><?=number_format(round($total_jumlah),2,',','.')?></td></tr>
   </table>
   <table width="100%" cellspacing="0" cellpadding="0">
      <tr><td style="border:none;text-align:left;vertical-align:top">Terbilang</td><td>:</td><td>#
         <?=ucfirst(strtolower(number_to_words(number_format(round($total_jumlah),2,',','')))).' rupiah'?></td></tr>
      <tr><td style="border:none;text-align:left" colspan="3">Pembayaran tagihan ini agar di transfer ke Rekening 
         Koperasi/Buying Station:</td></tr>
   </table>
   <table width="100%" cellspacing="0" cellpadding="0">
      <tr><td style="border:none;text-align:left;vertical-align:top" width="20%">Nama Bank</td><td width="1%">:</td><td><?=$data['bank_nama']?></td></tr>
      <tr><td style="border:none;text-align:left;vertical-align:top">Nomor Rekening</td><td>:</td><td><?=$data['bank_rek']?></td></tr>
      <tr><td style="border:none;text-align:left;vertical-align:top">An. Nama</td><td>:</td><td><?=$data['bank_an']?></td></tr>
      <tr><td style="border:none;text-align:left" colspan="3">Demikian invoice ini kami ajukan, atas kerjasamanya kami 
      ucapkan terima kasih.</td></tr>
   </table>
   
   <br><br> 
   <span><?=$data['District']?>, <?=SetTanggal($data['InvoiceDate'])?></span>
   <br><br>
   <div style="text-align: center;width: 25%;">Ketua Koperasi 
   <br><br><br><br>
   <u><?=$data['StaffName']?></u><br>(<?=$data['StaffID']?>)</div>
   <?if($j<2){?><br><br><br><br><div style="width:100%;border:dashed 1px"></div><?}?>
   <?}?>
</div>
</body>

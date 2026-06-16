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
   <?for ($j=1;$j<2;$j++) {?>
   <div style="text-align:right;width:100%;font-size:9px">lembar <?=$j?></div>
   <br>
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="15%" align="right"><img src="<?=base_url()?>images/<?=$data['logo']?>"></td>
            <td align="center" style="text-align: center; font-weight: bold;"><?=$data['nama']?><br><?=$data['keterangan']?></td>
            <td width="15%" align="right"><img src="<?=base_url()?>images/utz.jpg"></td>            
         </tr>
         <tr><td align="center" colspan="3" style="height: 30px; text-align: center; font-weight: bold;">
            KWITANSI<br>NO: <?=$data['nomor']?></td>
      </table>
   </div>
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th colspan="2">Rincian</th></tr>
      <tr><th colspan="2">
         <table class="table-print" cellpadding="0" width="100%">
            <tr><th>No</th><th>ID Transaksi</th><th>Tanggal</th><th>Berat</th><th>Premium</th></tr>
            <?for ($i=0;$i<sizeof($detail);$i++) {
               $total += $detail[$i]['berat'];
               $premium += $detail[$i]['premium']?>
               <tr><td><?=$i+1?></td><td><?=$detail[$i]['id']?></td><td><?=SetTanggal($detail[$i]['tanggal'])?></td>
                  <td style="text-align:right"><?=number_format($detail[$i]['berat'],0,',','.')?></td>
                  <td style="text-align:right"><?=number_format($detail[$i]['premium'],2,',','.')?></td></tr>
            <?if ($i>0 and ($i+1)%40==0) {?>
         </table>
      </th></tr>
   </table>
</div>
<div class="page">            
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="15%" align="right"><img src="<?=base_url()?>images/Photo/<?=$data['logo']?>"></td>
            <td align="center" style="text-align: center; font-weight: bold;"><?=$data['nama']?><br><?=$data['keterangan']?></td>
            <td width="15%" align="right"><img src="<?=base_url()?>images/utz.jpg"></td>            
         </tr>
         <tr><td align="center" colspan="3" style="height: 30px; text-align: center; font-weight: bold;">
            KWITANSI<br>NO: <?=$data['nomor']?></td>
      </table>
   </div>
   <table class="table-print" cellpadding="0" width="100%">
      <tr><th colspan="2">Rincian</th></tr>
      <tr><th colspan="2">
         <table class="table-print" cellpadding="0" width="100%">
            <tr><th>No</th><th>ID Transaksi</th><th>Tanggal</th><th>Berat</th><th>Premium</th></tr>
            <?}
            }?>
            <tr><th colspan="3">Total</th><td style="text-align:right"><?=number_format($total,0,',','.')?></td>
               <td style="text-align:right"><?=number_format($premium,2,',','.')?></td></tr>
         </table>
      </th></tr>
   </table>        
   <br><br> 
   <span><?=$data['District']?>, <?=SetTanggal($data['PaymentDate'])?></span>
   <br><br>
   <div style="text-align: center;width: 25%;">Penerima
   <br><br><br><br>
   <u><?=$data['StaffName']?></u><br>(<?=$data['StaffID']?>)</div>
   <?}?>
</div>
</body>

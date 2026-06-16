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
   <div class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="15%" align="right" rowspan="2"><img src="<?=base_url()?>images/<?=$data['logo']?>"></td>
            <td align="center" style="text-align: center; font-weight: bold;"><?=$data['nama']?><br><?=$data['keterangan']?></td>
            <td width="15%" align="right" rowspan="2"><img src="<?=base_url()?>images/utz.jpg"></td>            
         </tr>
         <tr><td align="center" style="height: 30px; text-align: center; font-weight: bold;">
            KUITANSI<br>NO: <?=$data['Number']?></td>
      </table>
   </div>
   <table class="table-print" cellpadding="0" width="100%" style="border-left:none;">
      <?for ($i=0;$i<sizeof($detail);$i++) {?>
      <tr><td><?=$i+1?></td><td><?='['.$detail[$i]['Number'].'] '.$detail[$i]['Name']?></td><td><?=$detail[$i]['Qty']?></td>
         <td><?=$detail[$i]['SellingPrice']?></td><td><?=$detail[$i]['Qty']*$detail[$i]['SellingPrice']?></td></tr>
      <?}?>
   </table>
      <table width="100%" cellspacing="0" cellpadding="0">
         <tr><td>Total</td><td><?=number_format($data['Total'],2,',','.')?></td></tr>
         <tr><td>Pembayaran</td><td><?=number_format($data['Pembayaran'],2,',','.')?></td></tr>
         <tr><td>Kembalian</td><td><?=number_format($data['Pembayaran']-$data['Total'],2,',','.')?></td></tr>
   <br><br> 
   <span><?=$data['District']?>, <?=SetTanggal($data['Date'])?></span>
</div>
</body>

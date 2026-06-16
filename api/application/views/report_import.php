<style>
    .summary, .groupuser {
        background: #99bbe8 none repeat scroll 0 0;
        color: #4f6b72;
        font: 11px arial,helvetica,tahoma,sans-serif;
        width: 100%;
        border: 1px solid #99BBE8;
    }
    .summary #title, .groupuser #title .summary #title {
        background: #dfe8f6 none repeat scroll 0 0;
        color: #416aa3;
        font-weight: normal;
        padding: 3px 3px 3px 10px;
        width: 40%;
    }
    .summary #title1, .groupuser #title1 .summary #title1 {
        background: #dfe8f6 none repeat scroll 0 0;
        color: #416aa3;
        font-weight: normal;
        padding: 3px 3px 3px 10px;
        border: 1px solid #99BBE8;
    }
    .summary #cont, .groupuser #cont {
        background-color: #fff;
        color: #000000;
        padding: 3px 3px 3px 10px;
        border: 1px solid #99BBE8;
    }

</style>

<table width="100%" cellspacing="1" cellpadding="1" class="summary" border="<?php echo $border; ?>">
  <tr>
    <td colspan="4" id="title1"><div align="center"><b>Daftar Import data <?=SetTanggal($start)?> s.d. <?=SetTanggal($end)?></b></div></td>
  </tr>
</table>
<table cellspacing="1" cellpadding="1" class="summary" border="<?php echo $border; ?>">
  <tr>
      <td id="title1" width="50"><div align="center"><b>No</b></div></td>
      <td id="title1" width="350"><div align="center"><b>PO</b></div></td>
      <td id="title1" width="350"><div align="center"><b>Tanggal</b></div></td>
      <td id="title1" width="250"><div align="center"><b>Status</b></div></td>
  </tr>
  <?php
  for ($i=0;$i<sizeof($data);$i++) {?>
   <tr><td><?=$i+1?></td><td><?=$data[$i]['PO']?></td><td><?=SetTanggal($data[$i]['Date'])?></td><td><?=$data[$i]['Status']?></td></tr>
  <?}?>
</table>

<br />
<br />

<table width="100%" cellspacing="1" cellpadding="1" class="summary" border="<?php echo $border; ?>">
  <tr>
    <td colspan="11" id="title1"><div align="center"><b>Daftar transaksi petani non sertifikasi</b></div></td>
  </tr>
</table>
<table cellspacing="1" cellpadding="1" class="summary" border="<?php echo $border; ?>">
  <tr>
      <td id="title1" width="50"><div align="center"><b>No</b></div></td>
      <td id="title1" width="350"><div align="center"><b>PO</b></div></td>
      <td id="title1" width="350"><div align="center"><b>Tanggal</b></div></td>

      <td id="title1" width="350"><div align="center"><b>District</b></div></td>
      <td id="title1" width="250"><div align="center"><b>Koperasi ID</b></div></td>
      <td id="title1" width="350"><div align="center"><b>Koperasi Name</b></div></td>
      
      <td id="title1" width="250"><div align="center"><b>BS ID</b></div></td>
      <td id="title1" width="350"><div align="center"><b>BS Name</b></div></td>
      <td id="title1" width="250"><div align="center"><b>Farmer ID</b></div></td>
      <td id="title1" width="350"><div align="center"><b>Farmer Name</b></div></td>
      <td id="title1" width="350"><div align="center"><b>Berat</b></div></td>
  </tr>
  <?php
  for ($i=0;$i<sizeof($detail);$i++) {?>
   <tr><td><?=$i+1?></td><td><?=$detail[$i]['PO']?></td><td><?=$detail[$i]['Date']?></td>
      <td><?=$detail[$i]['District']?></td><td><?=$detail[$i]['CoopID']?></td><td><?=$detail[$i]['CoopName']?></td>
      <td><?=$detail[$i]['OrgID']?></td><td><?=$detail[$i]['Name']?></td><td><?=$detail[$i]['FarmerID']?></td>
      <td><?=$detail[$i]['FarmerName']?></td><td><?=$detail[$i]['Weight']?></td></tr>
  <?}?>
</table>

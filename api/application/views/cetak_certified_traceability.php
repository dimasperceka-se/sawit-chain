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
    <td colspan="4" id="title1"><div align="center"><b></b></div></td>
  </tr>
</table>
<table cellspacing="1" cellpadding="1" class="summary" border="<?php echo $border; ?>">
  <tr>
      <td id="title1" width="50"><div align="center"><b>No</b></div></td>
      <td id="title1" width="350"><div align="center"><b>PO</b></div></td>
      <td id="title1" width="350"><div align="center"><b>Tanggal</b></div></td>
      <td id="title1" width="350"><div align="center"><b>Koperasi</b></div></td>
      <td id="title1" width="250"><div align="center"><b>BS</b></div></td>
      <td id="title1" width="250"><div align="center"><b>Bruto</b></div></td>
      <td id="title1" width="250"><div align="center"><b>Netto</b></div></td>
      <td id="title1" width="250"><div align="center"><b>Premium</b></div></td>
      <td id="title1" width="250"><div align="center"><b>Jumlah</b></div></td>
      <td id="title1" width="250"><div align="center"><b>Status</b></div></td>
  </tr>
  <?php
  for ($i=0;$i<sizeof($data);$i++) {?>
   <tr><td><?=$i+1?></td><td><?=$data[$i]['po']?></td>
      <td><?=SetTanggal($data[$i]['tanggal'])?></td>
      <td><?=$data[$i]['koperasi']?></td>
      <td><?=$data[$i]['bs']?></td>
      <td><?=$data[$i]['bruto']?></td>
      <td><?=$data[$i]['netto']?></td>
      <td><?=$data[$i]['premium']?></td>
      <td><?=$data[$i]['jumlah']?></td>
      <td><?=$data[$i]['status']?></td></tr>
  <?}?>
</table>

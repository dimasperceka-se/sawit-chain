<body  onload="window.print()">
    
<table width="566" border="0">
  <tr>
    <td width="136">Loan Type</td>
    <td width="12">:</td>
    <td width="426"><?=$loanname?></td>
  </tr>
  <tr>
    <td>Loan Amount</td>
    <td>:</td>
    <td><?=  number_format($amount)?></td>
  </tr>
  <tr>
    <td>Term</td>
    <td>:</td>
    <td><?=$tenor?> months</td>
  </tr>
  <?php
  if($margin!=0)
  {
      ?>
      <tr>
    <td>Profit Margin</td>
    <td>:</td>
    <td><?=$margin?></td>
  </tr>
  <?php
  }
  ?>
    <?php
  if($ClientSharing!=0)
  {
      ?>
      <tr>
    <td>Client Sharing</td>
    <td>:</td>
    <td><?=$ClientSharing?> %</td>
  </tr>
  <?php
  }
  ?>
  
      <?php
  if($CooperativeSharing!=0)
  {
      ?>
      <tr>
    <td>Cooperative Sharing</td>
    <td>:</td>
    <td><?=$CooperativeSharing?> %</td>
  </tr>
  <?php
  }
  ?>
</table>

<p>&nbsp;</p>

<table width="100%" border="1">
  <tr>
      <td width="6%" align="center"><b>Term</b></td>
    <td width="15%" align="right"><b>Amount Principal</b></td>
    <td width="15%" align="right"><b>Amount Interest</b></td>
    <td width="15%" align="right"><b>Amount Installment</b></td>
    <td width="15%" align="right"><b>Remaining Debt</b></td>
    <td width="13%" align="center"><b>Due Date</b></td>
  </tr>
  <?php
  foreach ($simdata as $value) {
  ?>
  <tr>
    <td align="center"><?=$value->term?></td>
    <td align="right"><?=  $value->amount?></td>
    <td align="right"><?=  $value->interest?></td>
    <td align="right"><?=  $value->installment?></td>
    <td align="right"><?=  $value->loan?></td>
    <td align="center"><?=$value->due?></td>
  </tr>
  <?php
  }
  ?>
</table>

</body>
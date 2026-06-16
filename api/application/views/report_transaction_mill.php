<table border="1">
  <tr>
    <td><?=lang("Batch Number")?></td>
    <td><?=lang("Transaction Date")?></td>
    <td><?=lang("Transaction ID")?></td>
    <td><?=lang("Agent")?></td>
    <td><?=lang("Nett Weight")?> (kg)</td>
    <td><?=lang("Batch Status")?></td>
    <td><?=lang("Driver")?></td>
    <td><?=lang("PO Number")?></td>
    <td><?=lang("Destination")?></td>
  </tr>
  <?php
    
    foreach($data as $key => $value) { ?>
        
        <tr>
			<td><?php echo $value['SupplyBatchNumber']; ?></td>
			<td><?php echo $value['DateTransaction']; ?></td>
			<td><?php echo $value['TransNumber']; ?></td>
			<td><?php echo $value['BatchFrom']; ?></td>
			<td><?php echo $value['VolumeNetto']; ?></td>
			<td><?php echo $value['Status']; ?></td>
			<td><?php echo $value['DestDriver']; ?></td>
			<td><?php echo $value['DestPO']; ?></td>
			<td><?php echo $value['BatchTo']; ?></td>
        </tr>
	<?php } ?>
</table>

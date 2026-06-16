<table border="1">
  <tr>
    <td>Transaction Date</td>
    <td>Transaction ID</td>
    <td>Transaction Number</td>
	<td>Farmer ID</td>
	<td>Farmer Name</td>
	<td>Certified</td>
	<td>District</td>
	<td>Province</td>
	<td>Nett Weight (kg)</td>
	<td>Price per Kg</td>
	<td>Total Payment</td>
	<td>Agent</td>
	<td>Batch Status </td>
	<td>Batch Number</td>
	<td>Destination</td>
	<td>Total Hectare</td>
	<td>Total Hectare Polygon</td>
  </tr>
  <?php
    
    foreach($data as $key => $value) { ?>
        
        <tr>
			<td><?php echo $value['DateTransaction']; ?></td>
			<td><?php echo $value['SupplyTransID']; ?></td>
			<td><?php echo $value['TransNumber']; ?></td>
			<td><?php echo $value['FarmerID']; ?></td>
			<td><?php echo $value['FarmerName']; ?></td>
			<td><?php echo $value['isCertified']; ?></td>
			<td><?php echo $value['District']; ?></td>
			<td><?php echo $value['Province']; ?></td>
			<td><?php echo $value['VolumeNetto']; ?></td>
			<td><?php echo $value['NetPrice']; ?></td>
			<td><?php echo $value['TotalPayment']; ?></td>
			<td><?php echo $value['Agent']; ?></td>
			<td><?php echo $value['Status']; ?></td>
			<td><?php echo $value['SupplyBatchNumber']; ?></td>
			<td><?php echo $value['Destination']; ?></td>
			<td><?php echo $value['GardenAreaHa']; ?></td>
			<td><?php echo $value['GardenAreaPolygon']; ?></td>
        </tr>
	<?php } ?>
</table>

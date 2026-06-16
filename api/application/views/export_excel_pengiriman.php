<!DOCTYPE html>
<html>
<body>
	<style type="text/css">
	body{
		font-family: sans-serif;
	}
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
 
	}
	a{
		background: blue;
		color: #fff;
		padding: 8px 10px;
		text-decoration: none;
		border-radius: 2px;
	}
	</style>
 
	<?php
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Deliveries.xls");

	?>
 
	<center>
		<h1>DELIVERIES</h1>
	</center>
 
	<table border="1">
		<tr>
			<th>No.</th>
			<th>Status</th>
			<th>Batch Number</th>
			<th>Destination Name</th>
            <th>Delivery Date</th>
            <th>Dest Weight</th>
            <th>Dest Package</th>
            <th>Driver</th>
		</tr>
		<?php
		$no=0;
		foreach ($delivery['data'] as $key) {
		?>
			<tr>
				<td><?php echo ++$no;?></td>
				<td><?php echo$key->SupplyBatchStatus;?></td>
				<td><?php echo$key->SupplyBatchNumber;?></td>
				<td><?php echo$key->SupplyDestOrgName;?></td>

				<td><?php echo date('d F Y H:i',strtotime($key->DeliveryDate));?></td>
				<td align="right"><?php echo$key->DestWeight;?></td>
				<td align="right"><?php echo number_format($key->DestNumberPackage);?></td>
				<td><?php echo$key->DestDriver;?></td>
			</tr>
		<?php
		}
		?>
		
		
	</table>
</body>
</html>
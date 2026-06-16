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
		header("Content-Disposition: attachment; filename=Transactions.xls");

	?>
 
	<center>
		<h1>Transactions</h1>
	</center>
 
	<table border="1">
		<tr>
			<th>No.</th>
			<th>Status</th>
			<th>Trans Number</th>
			<th>Supply Type</th>
            <th>Transaction Date</th>
            <th>Janjang</th>
            <th>Gross</th>
            <th>Netto</th>
		</tr>
		<?php
		$no=0;
		foreach ($transaction['data'] as $key) {
		?>
			<tr>
				<td><?php echo ++$no;?></td>
				<td><?php echo$key->SupplyStatus;?></td>
				<td><?php echo$key->TransNumber;?></td>
				<td><?php echo$key->SupplyType;?></td>

				<td><?php echo date('d F Y H:i',strtotime($key->DateTransaction));?></td>
				<td align="right"><?php echo$key->PackageNumber;?></td>
				<td align="right"><?php echo number_format($key->VolumeBruto);?></td>
				<td align="right"><?php echo number_format($key->VolumeNetto);?></td>
			</tr>
		<?php
		}
		?>
		
		
	</table>
</body>
</html>
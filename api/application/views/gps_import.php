<!DOCTYPE html>
<html>
<head>
	<title>GPS Import</title>
</head>
<body>
	<h1>Errors</h1>
	<table style="border: 1px solid; cellspacing: 0;">
		<thead>
			<tr>
				<th>No</th>
				<th>FarmerID</th>
				<th>GardenNr</th>
			</tr>
		</thead>
		<tbody>
		<?php if (!empty($errors)): ?>
			<?php $no = 1; ?>
			<?php foreach ($errors as $key => $error): ?>
				<tr>
					<td><?php echo $no; $no++; ?></td>
					<td><?php echo $error['FarmerID'] ?></td>
					<td><?php echo $error['GardenNr'] ?></td>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
		</tbody>
	</table>

	<h1>Success</h1>
	<table style="border: 1px solid; cellspacing: 0;">
		<thead>
			<tr>
				<th>No</th>
				<th>FarmerID</th>
				<th>GardenNr</th>
			</tr>
		</thead>
		<tbody>
		<?php if (!empty($success)): ?>
			<?php $no = 1; ?>
			<?php foreach ($success as $key => $val): ?>
				<tr>
					<td><?php echo $no; $no++; ?></td>
					<td><?php echo $val['FarmerID'] ?></td>
					<td><?php echo $val['GardenNr'] ?></td>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
		</tbody>
	</table>
</body>
</html>
<?php exit; ?>
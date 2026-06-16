<table>
	
	<tr>	
		<th rowspan="6" style="width:100px">
			<img src="<?php echo $url_photo.'no-user.jpg' ?>" alt="" style="width:100%; padding:7px">
		</th>
	</tr>
	<tr>
		<td style="width:130px"><?php echo lang('ID Petani') ?></td>
		<td><?php echo $FarmerID ?></td>
	</tr>
	<tr>
		<td><?php echo lang('Nama') ?></td>
		<td><?php echo $FarmerName ?></td>
	</tr>
	<tr>
		<td><?php echo lang('Luas Lahan') ?></td>
		<td><?php echo $totalProduksi ?></td>
	</tr>
	<tr>
		<td><?php echo lang('Pohon') ?></td>
		<td><?php echo $Pohon ?></td>
	</tr>
	<tr>
		<td><?php echo lang('Produktivitas') ?></td>
		<td><?php echo $Produktivitas ?> (Kg/Ha/Tahun)</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="2"><?php echo $Area ?> (Kg/Ha/Tahun)</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center" colspan="3">
			<a style="line-height: 14px;" class="btn" onclick="displayBeforeCetak('<?php echo $FarmerID ?>')" href="#"> <?php echo lang('cetak') ?></a>
			<a style="line-height: 14px;" class="btn" onclick="display_area('<?php echo $FarmerID ?>')" href="#"> <?php echo lang('area') ?></a>
		</td>
	</tr>
</table>
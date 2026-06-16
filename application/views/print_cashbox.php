

<table width="700">
	<tr>
		<td colspan="6"><center><h1>CASH BOX SUMMARY</h1></center></td>
	</tr>
	<tr>
		<td colspan="3" style="background-color:green;"><center>Cashbox</center></td>
		<td></td>
		<td colspan="2" style="background-color:green;"><center>Cash Info</center></td>
	</tr>
	<tr>
		<td>Lembar 100.000</td>
		<td>x</td>
		<td><?=$Lembar100?></td>
		<td></td>
		<td>Date</td>
		<td><?=date('d-m-Y')?></td>
	</tr>
	<tr>
		<td>Lembar 50.000</td>
		<td>x</td>
		<td><?=$Lembar50?></td>
		<td></td>
		<td>Money which should be in your cash box</td>
		<td align="right"><?=$ActualBalance?></td>
	</tr>
	<tr>
		<td>Lembar 20.000</td>
		<td>x</td>
		<td><?=$Lembar20?></td>
		<td></td>
		<td>Total money in your cash box</td>
		<td align="right"><?=$TotalCashBoxRight?></td>
	</tr>
	<tr>
		<td>Lembar 10.000</td>
		<td>x</td>
		<td><?=$Lembar10?></td>
		<td></td>
		<td>Difference</td>
		<td align="right"><?=$balanceCashCount?></td>
	</tr>
	<tr>
		<td>Lembar 5.000</td>
		<td>x</td>
		<td><?=$Lembar5?></td>
		<td></td>
		<td>Remarks</td>
		<td><?=$remarks?></td>
	</tr>
	<tr>
		<td>Lembar 2.000</td>
		<td>x</td>
		<td><?=$Lembar2?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Lembar 1.000</td>
		<td>x</td>
		<td><?=$Lembar1?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Koin 1.000</td>
		<td>x</td>
		<td><?=$Koin1?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Koin 500</td>
		<td>x</td>
		<td><?=$Koin5rts?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Koin 200</td>
		<td>x</td>
		<td><?=$Koin2rts?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Koin 100</td>
		<td>x</td>
		<td><?=$Koin1rts?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Koin 50</td>
		<td>x</td>
		<td><?=$Koin50?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><center><b>TOTAL</b></center></td>
		<td></td>
		<td><?=$TotalCashBox?></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>

<script type="text/javascript">
window.onload = function() { window.print(); }
</script>
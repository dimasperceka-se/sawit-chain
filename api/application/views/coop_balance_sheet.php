<!-- <link href="../assets/css/reporting.css" rel="stylesheet"> -->
<?php
// if($option!='print')
// {
//     $borderstyle = "border-bottom: #E6E8E6; background-color: #EDF4F7;  border-bottom-width: thin; border-bottom-style: dotted; ";
// } else {
    $borderstyle = null;
// }
?>
<style>
    td {        
        font-size: <?=$fontsize?>px;
    }
</style>

<?php
foreach ($data as $key => $report) {

?>
<center>
    <table class='tablereport' style="line-height: <?=$lineheight?>px;" width='60%' border='0' padding="0">
    <tr>
        <td colspan="2"> 
            <center>                
               <br> <h1>NERACA</h1>        
        </td>
    </tr>
<tr>
    <td colspan="2"><b>HARTA</b></td>
</tr>

<?php
$value = isset($report['value']) ? $report['value'] : null;
$identpx=0;
$total = 0;
foreach ($report['arrHarta'] as $k => $v) {
    // echo $k['total'];
    $startindent = 40;
    $total = 0;
    echo "<tr><td class='indent20 head' colspan='2'>$v</td></tr>";

    foreach ($report[$k] as $key => $value) {
        $identpx = $startindent . "px";
        echo "<tr><td><div style='margin-left: $identpx;font-size: ".$fontsize."px; '>".$value['CoaCode']." " . $value['CoaTitle'] . '</td>';
        echo "<td><div class='balance'>" . number_format($value['balance']) . "</div></td>";
        echo "</tr>";
        $total += $value['balance'];
        // if ($value['child'] != 'false') {
        //     $total +=echoChild($value['child'], $startindent + 20,$total);
        // }
        
    }
    // print_r($$k);
//    echo "<tr><td class='total'><div>Total " . $v . ':</td>';
//    echo "<td class='subtotal'><div class='subtotal2'>" . number_format($total) . "</div></td>";
     echo "<tr style='$borderstyle color: #0D1973;  '>
            <td colspan=2>
                <b>
                    <div style='margin-left: $identpx; font-size: ".$fontsize."px; '>Total :<div style='float:right'>" . number_format($total) . "</div></div>
                    
                </b>
            </td>
           </tr>";
     // echo "<td style='$borderstyle color: #0D1973;  '><div style='float:right; font-size: ".$fontsize."px;'>" . number_format($total) . "</b></div></td>";
}
?>
    <tr>
        <td style="background-color:#E6FAE7;" colspan="2">
            <b><div style="float:left;">Total Harta:</div> 
            <div style="float:right;"><?=number_format($report['totalharta'])?></div></b></td>
        <!-- <td style="background-color:#E6FAE7;"><div class='total2'><?=number_format($totalharta)?></div></td> -->
    </tr>
    
    <tr>
        <td colspan="2"><p>&nbsp</p></td>
    </tr>
    <!--///////////////////////////////////////////////////////////////////-->
    
<tr>
    <td colspan="2"><b>Kewajiban</b></td>
</tr>

<?php
// exit;
$totalkewajiban = 0;
foreach ($report['arrKewajiban'] as $k => $v) {

    $startindent = 40;
    $total = 0;
    echo "<tr><td class='indent20 head' colspan='2'>$v</td></tr>";

    foreach ($report[$k] as $key => $value) {
        $identpx = $startindent . "px";
        echo "<tr><td><div style='margin-left: $identpx;font-size: ".$fontsize."px; '>".$value['CoaCode']." " . $value['CoaTitle'] . '</td>';
        echo "<td><div class='balance'>" . number_format($value['balance']) . "</div></td>";
        echo "</tr>";
        $total += $value['balance'];
        // if ($value['child'] != 'false') {
        //     $total +=echoChild($value['child'], $startindent + 20,$total);
        // }
    }

  echo "<tr style='$borderstyle color: #0D1973;  '>
            <td colspan=2>
                <b>
                    <div style='margin-left: $identpx; font-size: ".$fontsize."px; $borderstyle color: #0D1973;  '>Total :<div style='float:right'>" . number_format($total) . "</div></div>
                    
                </b>
            </td>
           </tr>";
           $totalkewajiban+=$total;
}
?>
 <tr>
        <td style="background-color:#E6FAE7;" colspan="2">
            <b><div style="float:left;">Total Kewajiban:</div> 
            <div style="float:right;"><?=number_format($totalkewajiban)?></div></b></td>
    </tr>
    
   <tr>
        <td colspan="2"><p>&nbsp</p></td>
    </tr>
    
    <tr>
    <td colspan="2"><b>Modal</b></td>
</tr>

<?php
$total = 0;

foreach ($report['arrModal'] as $k => $v) {

    $startindent = 40;
    $total = 0;
    echo "<tr><td class='indent20 head' colspan='2'>$v</td></tr>";

    foreach ($report[$k] as $key => $value) {
        $identpx = $startindent . "px";
        echo "<tr><td><div style='margin-left: $identpx;font-size: ".$fontsize."px; '>".$value['CoaCode']." " . $value['CoaTitle'] . '</td>';
        echo "<td><div class='balance'>" . number_format($value['balance']) . "</div></td>";
        echo "</tr>";
        $total += $value['balance'];
        // if ($value['child'] != 'false') {
        //     $total +=echoChild($value['child'], $startindent + 20,$total);
        // }
    }

   echo "<tr style='$borderstyle color: #0D1973;  '>
            <td colspan=2>
                <b>
                    <div style='margin-left: $identpx; font-size: ".$fontsize."px; $borderstyle color: #0D1973;  '>Total :<div style='float:right'>" . number_format($total) . "</div></div>
                    
                </b>
            </td>
           </tr>";
}
?>
 <tr>
        <td style="background-color:#E6FAE7;" colspan="2">
            <b><div style="float:left;">Total Modal:</div> 
            <div style="float:right;"><?=number_format($total)?></div></b></td>
    </tr>
    
    
</table>
</center>

<?php
} //end foreach

if(isset($print_page))
{
    echo "<body onload=\"window.print()\">";
}
?>
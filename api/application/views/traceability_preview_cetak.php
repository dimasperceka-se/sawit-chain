<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Tracebility</title>

    <?php if ($jenis !== 'pdf'): ?>
    <link href="<?php echo base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" media="all">
    <style>
        @media all {
            .page-break	{ display: none; }
        }

        @media print {
            @page { margin: 0.5cm; padding:0cm; <?php echo ($jenis == 'coop' || $jenis == 'bu') ? 'size: landscape;' : ''; ?>};
            .page-break  { display: block; page-break-before: always; }
            .page-break-after { display: block; page-break-after: always; }

        }
        body {
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
            font: 9pt "verdana";
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            width:<?php echo ($jenis == 'coop' || $jenis == 'bu') ? '27.5cm' : '21cm'; ?>;
            height:<?php echo ($jenis == 'coop'  || $jenis == 'bu') ? '21cm' : '27.5cm'; ?>;
            padding: 1.5cm 1cm;
            margin: 0.2cm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            .page {
                margin: initial;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                background-color: initial;

                padding-bottom:0;
            }
            .header{border:90px #cccccc;}
        }

        input {border: 1px solid;background-color: #FFF}
        input:disabled {color: #000000;}
        .line{border: 1px solid black;}
        td{vertical-align:top}
        @media all {
            .header{  font-size: 10pt;font-family: verdana;padding: 0px;margin: 0px;border: 1px solid;font-weight: bold;}
            .body{font-size: 9pt;font-family: verdana;font-weight: normal;padding: 0px;margin: 0px;border: 1px solid;}
            .header_div{width: 100%; height: 20px; margin-bottom: -28px; border-top: 28px solid #cccccc;}
        }     
    </style>
    <?php else: ?>
    <style>
        <?php echo file_get_contents('assets/css/bootstrap.min.css') ?>
    </style>
    <?php endif ?>
</head>
<body>
    <div class="page">

        <table width="100%" cellspacing="0">
            <tr>
                <td height="60"  width="25%" rowspan="2" align="center" style="vertical-align:middle;">
                    <?php if($logo['logo_koperasi']!=''){ ?>
                      <img src="<?=base_url()?>images/<?=$logo['logo_koperasi']?>" width="70" height="50">
                    <? } ?>
                </td>
                <td height="60" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;">
                    <?php echo $title; ?>
                </td>
                <td height="60" rowspan="2" width="25%" align="center" style="vertical-align:middle;">
                    <?php if($logo['logo_sertifikasi']!=''){ ?>
                      <img src="<?=base_url()?>images/<?=$logo['logo_sertifikasi']?>" width="70" height="50">
                    <? } ?>
                </td>
            </tr>
        </table>
        <?php
            $bruto = 0;
            $netto = 0;
            if($jenis=='wh'){
                echo "<table class='table table-bordered'>
                        <tr class='active'>
                            <th><center>No</center></th>
                            <th><center>Transaction Date</center></th>
                            <th><center>PO Number</center></th>
                            <th><center>Batch Number</center></th>
                            <th><center>Gross (Kg)</center></th>
                            <th><center>Netto (Kg)</center></th>
                        </tr>";                 
                $i=1;
                foreach($details as $key=>$value){
                    $bruto = $bruto + $value['bruto'];
                    $netto = $netto + $value['netto'];
                    echo "<tr>";
                        echo "<td>".$i."</td>";
                        echo "<td>".$value['datetransaction']."</td>";
                        echo "<td>".$value['po']."</td>";
                        echo "<td>".$value['batchnumber']."</td>";
                        echo "<td style='text-align:right;'>".number_format($value['bruto'],2,',','.')."</td>";
                        echo "<td style='text-align:right;'>".number_format($value['netto'],2,',','.')."</td>";
                    echo "</tr>";
                    
                    if($i == 25 OR ($i>25 and ($i-25)%25==0)){
                        echo "</table>";
                        echo "</div><div class='page-break'></div>";
                        echo "<div class='page'>";
                        echo "<table class='table table-bordered'>
                                <tr class='active'>
                                    <th><center>No</center></th>
                                    <th><center>Transaction Date</center></th>
                                    <th><center>PO Number</center></th>
                                    <th><center>Batch Number</center></th>
                                    <th><center>Gross (Kg)</center></th>
                                    <th><center>Netto (Kg)</center></th>
                                </tr>"; 
                    }
                    $i++;
                }
                echo "<tr>";
                    echo "<td colspan='4'><center><b>Total</b></center></td>";
                    echo "<td style='text-align:right;'><b>".number_format($bruto,2,',','.')."</b></td>";
                    echo "<td style='text-align:right;'><b>".number_format($netto,2,',','.')."</b></td>";
                echo "</tr>";
                echo "</table>";
            }else if($jenis=='coop'){
                echo "<table class='table table-bordered'>
                        <tr class='active'>
                            <th><center>No</center></th>
                            <th><center>Transaction Date</center></th>
                            <th><center>PO Number</center></th>
                            <th><center>Batch Number</center></th>
                            <th><center>Batch Status</center></th>
                            <th><center>Destination</center></th>
                            <th><center>Gross (Kg)</center></th>
                            <th><center>Netto (Kg)</center></th>
                        </tr>";                 
                $i=1;
                $name = "";
                foreach($details as $key=>$value){
                    $bruto = $bruto + $value['bruto'];
                    $netto = $netto + $value['netto'];
                    if($name!=$value['name']){
                        $name = $value['name'];
                        echo "<tr><td colspan='8'><b>".$value['name']."</b></td></tr>";
                    }
                    echo "<tr>";
                        echo "<td>".$i."</td>";
                        echo "<td>".$value['datetransaction']."</td>";
                        echo "<td>".$value['po']."</td>";
                        echo "<td>".$value['batchnumber']."</td>";
                        echo "<td>".$value['batchstatus']."</td>";
                        echo "<td>".$value['destination']."</td>";
                        echo "<td style='text-align:right;'>".number_format($value['bruto'],2,',','.')."</td>";
                        echo "<td style='text-align:right;'>".number_format($value['netto'],2,',','.')."</td>";
                    echo "</tr>";
                    
                    if($i == 15 OR ($i>15 and ($i-15)%15==0)){
                        echo "</table>";
                        echo "</div><div class='page-break'></div>";
                        echo "<div class='page'>";
                        echo "<table class='table table-bordered'>
                                <tr class='active'>
                                    <th><center>No</center></th>
                                    <th><center>Transaction Date</center></th>
                                    <th><center>PO Number</center></th>
                                    <th><center>Batch Number</center></th>
                                    <th><center>Batch Status</center></th>
                                    <th><center>Destination</center></th>
                                    <th><center>Gross (Kg)</center></th>
                                    <th><center>Netto (Kg)</center></th>
                                </tr>"; 
                    }
                    $i++;
                }
                echo "<tr>";
                    echo "<td colspan='6'><center><b>Total</b></center></td>";
                    echo "<td style='text-align:right;'><b>".number_format($bruto,2,',','.')."</b></td>";
                    echo "<td style='text-align:right;'><b>".number_format($netto,2,',','.')."</b></td>";
                echo "</tr>";
                echo "</table>";
            }else if($jenis=='bu'){
                echo "<table class='table table-bordered'>
                        <tr class='active'>
                            <th><center>No</center></th>
                            <th><center>Transaction Date</center></th>
                            <th><center>PO Number</center></th>
                            <th><center>Batch Number</center></th>
                            <th><center>Batch Status</center></th>
                            <th><center>Destination</center></th>
                            <th><center>Gross (Kg)</center></th>
                            <th><center>Netto (Kg)</center></th>
                        </tr>";                 
                $i=1;
                $name = "";
                foreach($details as $key=>$value){
                    $bruto = $bruto + $value['bruto'];
                    $netto = $netto + $value['netto'];
                    if($name!=$value['name']){
                        $name = $value['name'];
                        echo "<tr><td colspan='8'><b>".$value['name']."</b></td></tr>";
                    }
                    echo "<tr>";
                        echo "<td>".$i."</td>";
                        echo "<td>".$value['datetransaction']."</td>";
                        echo "<td>".str_replace(".", ". ", $value['po'])."</td>";
                        echo "<td>".$value['batchnumber']."</td>";
                        echo "<td>".$value['batchstatus']."</td>";
                        echo "<td>".$value['destination']."</td>";
                        echo "<td style='text-align:right;'>".number_format($value['bruto'],2,',','.')."</td>";
                        echo "<td style='text-align:right;'>".number_format($value['netto'],2,',','.')."</td>";
                    echo "</tr>";
                    
                    if($i == 10 OR ($i>10 and ($i-10)%12==0)){
                        echo "</table>";
                        echo "</div><div class='page-break'></div>";
                        echo "<div class='page'>";
                        echo "<table class='table table-bordered'>
                                <tr class='active'>
                                    <th><center>No</center></th>
                                    <th><center>Transaction Date</center></th>
                                    <th><center>PO Number</center></th>
                                    <th><center>Batch Number</center></th>
                                    <th><center>Batch Status</center></th>
                                    <th><center>Destination</center></th>
                                    <th><center>Gross (Kg)</center></th>
                                    <th><center>Netto (Kg)</center></th>
                                </tr>"; 
                    }
                    $i++;
                }
                echo "<tr>";
                    echo "<td colspan='6'><center><b>Total</b></center></td>";
                    echo "<td style='text-align:right;'><b>".number_format($bruto,2,',','.')."</b></td>";
                    echo "<td style='text-align:right;'><b>".number_format($netto,2,',','.')."</b></td>";
                echo "</tr>";
                echo "</table>";
            }else if($jenis=='farmer'){
                echo "<table class='table table-bordered'>
                        <tr class='active'>
                            <th><center>No</center></th>
                            <th><center>Transaction Date</center></th>
                            <th><center>PO Number</center></th>
                            <th><center>Batch Number</center></th>
                            <th><center>Batch Status</center></th>
                            <th><center>Destination</center></th>
                            <th><center>Gross (Kg)</center></th>
                            <th><center>Netto (Kg)</center></th>
                        </tr>";                 
                $i=1;
                $name = "";
                foreach($details as $key=>$value){
                    $bruto = $bruto + $value['bruto'];
                    $netto = $netto + $value['netto'];
                    if($name!=$value['name']){
                        $name = $value['name'];
                        echo "<tr><td colspan='8'><b>".$value['name']." | Survey Production: ".number_format($value['survey'],2,',','.')." Kg | Quota:".number_format($value['quota'],2,',','.')." Kg</b></td></tr>";
                    }
                    echo "<tr>";
                        echo "<td>".$i."</td>";
                        echo "<td>".$value['datetransaction']."</td>";
                        echo "<td>".str_replace(".", ". ", $value['po'])."</td>";
                        echo "<td>".$value['batchnumber']."</td>";
                        echo "<td>".$value['batchstatus']."</td>";
                        echo "<td>".$value['destination']."</td>";
                        echo "<td style='text-align:right;'>".number_format($value['bruto'],2,',','.')."</td>";
                        echo "<td style='text-align:right;'>".number_format($value['netto'],2,',','.')."</td>";
                    echo "</tr>";
                    
                    if($i == 10 OR ($i>10 and ($i-10)%12==0)){
                        echo "</table>";
                        echo "</div><div class='page-break'></div>";
                        echo "<div class='page'>";
                        echo "<table class='table table-bordered'>
                                <tr class='active'>
                                    <th><center>No</center></th>
                                    <th><center>Transaction Date</center></th>
                                    <th><center>PO Number</center></th>
                                    <th><center>Batch Number</center></th>
                                    <th><center>Batch Status</center></th>
                                    <th><center>Destination</center></th>
                                    <th><center>Gross (Kg)</center></th>
                                    <th><center>Netto (Kg)</center></th>
                                </tr>"; 
                    }
                    $i++;
                }
                echo "<tr>";
                    echo "<td colspan='6'><center><b>Total</b></center></td>";
                    echo "<td style='text-align:right;'><b>".number_format($bruto,2,',','.')."</b></td>";
                    echo "<td style='text-align:right;'><b>".number_format($netto,2,',','.')."</b></td>";
                echo "</tr>";
                echo "</table>";
            }
        ?>
    </div>
</body>
</html>

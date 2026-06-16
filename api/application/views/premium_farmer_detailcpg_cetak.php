<?php
    /*if($type=='summary'){
        $grup = 'ids';
    }else{
        $grup = 'cpg_id';
    }
    $i = 0;
    $j = 0;
    $check = '';
    foreach ($details['data'] as $key => $val) {
        if($check!=$val[$grup]){
            $i++;
            if($i>1){
                $j++;
            }
            $report[$j]['survey'] = $val['survey'];
            $report[$j]['quota'] = $val['quota'];
            $report[$j]['bruto'] = $val['bruto'];
        }else{

        }
        $report[$i]
        
    }*/
    $group_by = '';
    $counter = -1;
    foreach ($details['data'] as $key => $val) {
        
            $check = $val['ids'];
            $name = $val['name'];
            if($group_by!=$check){ //baru
                $counter++;
                $group_by = $check;
                $bruto = $val['bruto'];
                $netto = $val['netto'];
                $balance = $val['balance'];
                $totalidr = $val['totalidr'];
                $totalusd = $val['totalusd'];
                $paidkg = $val['paidkg'];
                $unpaidkg = $val['unpaidkg'];
                $paidusd = $val['paidusd'];
                $unpaidusd = $val['unpaidusd'];
            }else{ //jumlahkan
                $bruto = $bruto + $val['bruto'];
                $netto = $netto + $val['netto'];
                $balance = $balance - $val['netto'];
                $totalidr = $totalidr + $val['totalidr'];
                $totalusd = $totalusd + $val['totalusd'];
                $paidkg = $paidkg + $val['paidkg'];
                $unpaidkg = $unpaidkg + $val['unpaidkg'];
                $paidusd = $paidusd + $val['paidusd'];
                $unpaidusd = $unpaidusd + $val['unpaidusd'];
            }
        

        $report[$counter]['name'] = $name;
        $report[$counter]['survey'] = $val['survey'];
        $report[$counter]['quota'] = $val['quota'];
        $report[$counter]['bruto'] = $bruto;
        $report[$counter]['netto'] = $netto;
        $report[$counter]['balance'] = $balance;
        $report[$counter]['totalidr'] = $totalidr;
        $report[$counter]['totalusd'] = $totalusd;
        $report[$counter]['paidkg'] = $paidkg;
        $report[$counter]['unpaidkg'] = $unpaidkg;
        $report[$counter]['paidusd'] = $paidusd;
        $report[$counter]['unpaidusd'] = $unpaidusd;
    }
    //echo "<pre>".print_r($report,1);
    //echo SUM($report['netto']);
    $surveys = 0;
    $quotas = 0;
    $brutos = 0;
    $nettos = 0;
    $totalidrs = 0;
    $totalusds = 0;
    $paidkgs = 0;
    $unpaidkgs = 0;
    $paidusds = 0;
    $unpaidusds = 0;
    foreach ($report as $key => $val) {
        $surveys = $surveys + $val['survey'];
        $quotas = $quotas + $val['quota'];
        $brutos = $brutos + $val['bruto'];
        $nettos = $nettos + $val['netto'];
        $totalidrs = $totalidrs + $val['totalidr'];
        $totalusds = $totalusds + $val['totalusd'];
        $paidkgs = $paidkgs + $val['paidkg'];
        $unpaidkgs = $unpaidkgs + $val['unpaidkg'];
        $paidusds = $paidusds + $val['paidusd'];
        $unpaidusds = $unpaidusds + $val['unpaidusd'];
    }
?>

<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Tracebility</title>
    <link href="<?php echo base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" media="all">

    <?php if ($jenis !== 'pdf'): ?>
    <style>
        body {
            margin: 0;
            padding: 0;
            /*background-color: #FAFAFA;*/
            font: 9pt "verdana";
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            width: 21.5cm;
            /*height:27.5cm;*/
            padding: 1.5cm 0.5cm;
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
                <td height="60" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;">Detail Transaction Farmer per CPG</td>
                <td height="60" rowspan="2" width="25%" align="center" style="vertical-align:middle;">
                    <?php if($logo['logo_sertifikasi']!=''){ ?>
                      <img src="<?=base_url()?>images/<?=$logo['logo_sertifikasi']?>" width="70" height="50">
                    <? } ?>
                </td>
            </tr>
            <tr>
                <td height="30" style="text-align:center">
                    Premium : IDR <?php echo number_format($rp,0,'.',',') ?> | USD <?php echo $usd ?>
                </td>
            </tr>
        </table>

        <table class="table table-bordered">
            <tr class="active">
                <th>Name</th>
                <th style="width:80px;">Survey Volume (Kg)</th>
                <th style="width:80px;">Quota (Survey+10%)</th>
                <th style="width:80px;">Bruto (Kg)</th>
                <th style="width:80px;">Netto (Kg)</th>
                <th style="width:80px;">Balance (Kg)</th>
                <th style="width:100px;">TOTAL (IDR)</th>
                <th style="width:70px;">TOTAL (USD)</th>

                <th style="width:70px;">PAID (Kg)</th>
                <th style="width:70px;">UNPAID (Kg)</th>
                <th style="width:70px;">PAID (USD)</th>
                <th style="width:70px;">UNPAID (USD)</th>
            </tr>
            <?php 
            $page   = 1;
            $no     = 1;
            
            ?>

            <?php
                foreach ($details['data'] as $key => $value) {
                    $check = $details['data'][$key]['cpg_id'];
                    if($group_by!=$check){ //baru
                        $counter++;
                        $name = $details['data'][$key]['cpg_name'];
                        //$object->getActiveSheet()->mergeCells("A$counter:L$counter");
                        //$object->getActiveSheet()->setCellValue('A'.$counter, $name);
                        echo "<tr>";
                            echo "<td colspan='12'>".$name."</td>";
                        echo "</tr>";
                        $group_by = $check;
                    }
                    $check_details = $details['data'][$key]['ids'];
                    $name = $details['data'][$key]['name'];
                    if($group_by_details!=$check_details){ //baru
                        $counter++;
                        $group_by_details = $check_details;
                        $bruto = $details['data'][$key]['bruto'];
                        $netto = $details['data'][$key]['netto'];
                        $balance = $details['data'][$key]['balance'];
                        $totalidr = $details['data'][$key]['totalidr'];
                        $totalusd = $details['data'][$key]['totalusd'];
                        $paidkg = $details['data'][$key]['paidkg'];
                        $unpaidkg = $details['data'][$key]['unpaidkg'];
                        $paidusd = $details['data'][$key]['paidusd'];
                        $unpaidusd = $details['data'][$key]['unpaidusd'];
                    }else{ //jumlahkan
                        $bruto = $bruto + $details['data'][$key]['bruto'];
                        $netto = $netto + $details['data'][$key]['netto'];
                        $balance = $balance - $details['data'][$key]['netto'];
                        $totalidr = $totalidr + $details['data'][$key]['totalidr'];
                        $totalusd = $totalusd + $details['data'][$key]['totalusd'];
                        $paidkg = $paidkg + $details['data'][$key]['paidkg'];
                        $unpaidkg = $unpaidkg + $details['data'][$key]['unpaidkg'];
                        $paidusd = $paidusd + $details['data'][$key]['paidusd'];
                        $unpaidusd = $unpaidusd + $details['data'][$key]['unpaidusd'];
                    }

                    

                    echo '<tr>';
                        echo '<td>'.$name.'</td>';
                        echo '<td style="text-align:right;">'.number_format($details['data'][$key]['survey'],2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($details['data'][$key]['quota'],2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($bruto,2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($netto,2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($balance,2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($totalidr,2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($totalusd,2,',','.').'</td>';

                        echo '<td style="text-align:right;">'.number_format($paidkg,2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($unpaidkg,2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($paidusd,2,',','.').'</td>';
                        echo '<td style="text-align:right;">'.number_format($unpaidusd,2,',','.').'</td>';
                    echo '</tr>'; 

                    /*$object->getActiveSheet()->setCellValue('A'.$counter, $name);
                    $object->getActiveSheet()->setCellValue('B'.$counter, number_format($details['data'][$key]['survey'],2,'.',''));
                    $object->getActiveSheet()->setCellValue('C'.$counter, number_format($details['data'][$key]['quota'],2,'.',''));
                    $object->getActiveSheet()->setCellValue('D'.$counter, number_format($bruto,2,'.',''));
                    $object->getActiveSheet()->setCellValue('E'.$counter, number_format($netto,2,'.',''));
                    $object->getActiveSheet()->setCellValue('F'.$counter, number_format($balance,2,'.',''));
                    $object->getActiveSheet()->setCellValue('G'.$counter, number_format($totalidr,2,'.',''));
                    $object->getActiveSheet()->setCellValue('H'.$counter, number_format($totalusd,2,'.',''));
                    $object->getActiveSheet()->setCellValue('I'.$counter, number_format($paidkg,2,'.',''));
                    $object->getActiveSheet()->setCellValue('J'.$counter, number_format($unpaidkg,2,'.',''));
                    $object->getActiveSheet()->setCellValue('K'.$counter, number_format($paidusd,2,'.',''));
                    $object->getActiveSheet()->setCellValue('L'.$counter, number_format($unpaidusd,2,'.',''));
                    $object->getActiveSheet()->setCellValue('M'.$counter, $details['data'][$key]['nopo']);*/
                }
                    echo '<tr>';
                        echo '<td><center><b>Total</center></td>';
                        echo '<td style="text-align:right;"><b>'.number_format($surveys,2,',','.').'</td>';
                        echo '<td style="text-align:right;"><b>'.number_format($quotas,2,',','.').'</td>';
                        echo '<td style="text-align:right;"><b>'.number_format($brutos,2,',','.').'</td>';
                        echo '<td style="text-align:right;"><b>'.number_format($nettos,2,',','.').'</td>';
                        echo '<td style="text-align:right;"><b> - </td>';
                        echo '<td style="text-align:right;"><b>'.number_format($totalidrs,2,',','.').'</td>';
                        echo '<td style="text-align:right;"><b>'.number_format($totalusds,2,',','.').'</td>';

                        echo '<td style="text-align:right;"><b>'.number_format($paidkgs,2,',','.').'</td>';
                        echo '<td style="text-align:right;"><b>'.number_format($unpaidkgs,2,',','.').'</td>';
                        echo '<td style="text-align:right;"><b>'.number_format($paidusds,2,',','.').'</td>';
                        echo '<td style="text-align:right;"><b>'.number_format($unpaidusds,2,',','.').'</td>';
                    echo '</tr>'; 
            ?>
        </table>

    </div>
</body>
</html>

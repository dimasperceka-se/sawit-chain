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
            width: 21cm;
            /*height:27.5cm;*/
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
                <td height="60" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;">Detail Transaction Farmer</td>
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
                <th>Date</th>
                <!--<th style="width:80px;">Survey Volume (Kg)</th>-->
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
            $survey = 0;
            $quota = 0;
            $bruto = 0;
            $netto = 0;
            $totalidr = 0;
            $totalusd = 0;
            $paidkg = 0;
            $unpaidkg = 0;
            $paidusd = 0;
            $unpaidusd = 0;
            ?>
            <?php foreach ($detail as $bu => $farmers): ?>
                <tr class="active">
                    <th colspan="10" style="text-align:left"><?php echo $bu ?> <!--, Survey Volume : <?php echo number_format($val['survey'],2,',','.') ?> Kg--></th>
                </tr>
                <?php foreach ($farmers as $name => $farmer): ?>
                    <?php
                        $quota = $quota + $farmer[0]['quota'];
                    ?>  
                  <tr>
                    <th colspan="10" style="text-align:left"><?php echo $name ?> , Survey Volume : <?php echo number_format($farmer[0]['quota'],2,',','.') ?> Kg</th>
                  </tr>
                  <?php foreach ($farmer as $key => $val): ?>
                  <?php 
                    // increment nettto
                    if ($key == 0) {
                      $Balance = $val['balance'];
                    } else {
                      $Balance -= $val['netto'];
                    }
                  ?>
                  <tr>
                      <td><?php echo $val['datetransaction'] ?></td>
                      <!--<td style="text-align:right; width:80px;"><?php echo number_format($val['survey'],2,',','.') ?></td>-->
                      <td style="text-align:right; width:100px;"><?php echo number_format($val['bruto'],2,',','.') ?></td>
                      <td style="text-align:right; width:100px;"><?php echo number_format($val['netto'],2,',','.') ?></td>
                      <td style="text-align:right; width:100px;"><?php echo number_format($Balance,2,',','.') ?></td>
                      <td style="text-align:right; width:100px;"><?php echo number_format($val['totalidr'],2,',','.') ?></td>
                      <td style="text-align:right; width:70px;"><?php echo number_format($val['totalusd'],2,',','.') ?></td>

                    <td style="text-align:right; width:70px;"><?=number_format($val['paidkg'],2,',','.')?></td>
                    <td style="text-align:right; width:70px;"><?=number_format($val['unpaidkg'],2,',','.')?></td>
                    <td style="text-align:right; width:70px;"><?=number_format($val['paidusd'],2,',','.')?></td>
                    <td style="text-align:right; width:70px;"><?=number_format($val['unpaidusd'],2,',','.')?></td>
                  </tr>                
                <?php
                    $bruto = $bruto + $val['bruto'];
                    $netto = $netto + $val['netto'];
                    $totalidr = $totalidr + $val['totalidr'];
                    $totalusd = $totalusd + $val['totalusd'];
                    $paidkg = $paidkg + $val['paidkg'];
                    $unpaidkg = $unpaidkg + $val['unpaidkg'];
                    $paidusd = $paidusd + $val['paidusd'];
                    $unpaidusd = $unpaidusd + $val['unpaidusd'];
                ?>  

                <?php 
                  if ($page == 1 && $no >= 20) {
                    $page++;
                    $no = 1;
                ?>
                </table>
                </div>
                  <p style="page-break-after:always;"></p>
                <div class="page">
                <table class="table table-bordered">
                <?php 
                  } elseif ($page > 1 && $no >= 28) {
                    $page++;
                    $no = 1;
                ?>
                
                </table>
                </div>
                  <p style="page-break-after:always;"></p>
                <div class="page">
                <table class="table table-bordered">
                <?php  
                  }
                ?>                
                  <?php $no++; endforeach; ?>
                <?php $no++; endforeach; ?>
            <?php $no++; endforeach; ?>
            <tr>
                <th colspan="10">Total Survey Volume : <?php echo number_format($quota,2,',','.');; ?></th>
            </tr>
            <tr>
                <th colspan="10"><center>TOTAL</center></th>
            </tr>
            <tr class="active">
                <th>Date</th>
                <!--<th style="width:80px;">Survey Volume (Kg)</th>-->
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
            <tr>
                <td> - </td>
                <td style="text-align:right; width:100px;"><?php echo number_format($bruto,2,',','.'); ?></td>
                <td style="text-align:right; width:100px;"><?php echo number_format($netto,2,',','.'); ?></td>
                <td style="text-align:right; width:100px;"> - </td>
                <td style="text-align:right; width:100px;"><?php echo number_format($totalidr,2,',','.'); ?></td>
                <td style="text-align:right; width:70px;"><?php echo number_format($totalusd,2,',','.'); ?></td>
                <td style="text-align:right; width:70px;"><?php echo number_format($paidkg,2,',','.'); ?></td>
                <td style="text-align:right; width:70px;"><?php echo number_format($unpaidkg,2,',','.'); ?></td>
                <td style="text-align:right; width:70px;"><?php echo number_format($paidusd,2,',','.'); ?></td>
                <td style="text-align:right; width:70px;"><?php echo number_format($unpaidusd,2,',','.'); ?></td>
            </tr>
        </table>

    </div>
</body>
</html>

<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Tracebility</title>

    <?php if ($jenis !== 'pdf'): ?>
    <link href="<?php echo base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" media="all">
    <style>
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
            width: 21cm;
            height:27.5cm;
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
                    Laporan Penjualan per Koperasi
                </td>
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
                <th>Koperasi</th>
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
            <?php foreach ($detail as $key => $value): ?>
            <tr>
                <td><?php echo $value['name']?></td>
                <td style="text-align:right;"><?=number_format($value['survey'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['quota'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['bruto'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['netto'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['balance'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['totalidr'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['totalusd'],2,',','.')?></td>

                <td style="text-align:right;"><?=number_format($value['paidkg'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['unpaidkg'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['paidusd'],2,',','.')?></td>
                <td style="text-align:right;"><?=number_format($value['unpaidusd'],2,',','.')?></td>
            </tr>
            <?php
                $survey = $survey + $value['survey'];
                $quota = $quota + $value['quota'];
                $netto = $netto + $value['netto'];
                $bruto = $bruto + $value['bruto'];
                $totalidr = $totalidr + $value['totalidr'];
                $totalusd = $totalusd + $value['totalusd'];
                $paidkg = $paidkg + $value['paidkg'];
                $unpaidkg = $unpaidkg + $value['unpaidkg'];
                $paidusd = $paidusd + $value['paidusd'];
                $unpaidusd = $unpaidusd + $value['unpaidusd'];
            ?>

            <?php endforeach ?>
            <tr>
                <td> Total </td>
                <td style="text-align:right; width:100px;"><?php echo number_format($survey,2,',','.'); ?></td>
                <td style="text-align:right; width:100px;"><?php echo number_format($quota,2,',','.'); ?></td>
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

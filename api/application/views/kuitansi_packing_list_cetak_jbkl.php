<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <title>Farmer</title>

        <style type="text/css">

            @media all {
                .page-break { display: none; }
            }

            @media print {
                @page { margin: 0.1cm; padding:0cm; }
                .page-break  { display: block; page-break-before: always; }
                .page-break-after { display: block; page-break-after: always; }
                .page {
                    margin: 0;
                    border: initial;
                    border-radius: initial;
                    width: initial;
                    min-height: initial;
                    box-shadow: initial;
                    background: initial;
                    page-break-after: always;
                }
                .page_landscape {
                    margin: 0;
                    border: initial;
                    border-radius: initial;
                    width: initial;
                    min-height: initial;
                    box-shadow: initial;
                    background: initial;
                    page-break-after: always;
                }

            }

            body {
                margin:0;
                padding:0;
                line-height: 1.5em;
                font-family: "Trebuchet MS", Verdana, Helvetica, Arial;
                font-size: 14px;
                color: #000000;
                background-color: #ffffff;
            }
            a:link, a:visited { color: #0066CC; text-decoration: none} 
            a:active, a:hover { color: #008800; text-decoration: underline}

            #templatemo_container_wrapper {
                /*background: url(images/templatemo_side_bg.gif) repeat-x;*/
                background: #ffffff;
            }
            #templatemo_container {
                margin: 0px auto;
                /*background: url(images/templatemo_content_bg.gif);*/
                background: #FFFFFF;
            }
            #templatemo_top {
                clear: left;
                height: 25px;   /* 'padding-top' + 'height' must be equal to the 'background image height' */
                padding-top: 42px;
                padding-left: 30px;
                background: url(images/templatemo_top_bg.gif) no-repeat bottom;
            }
            #templatemo_header {
                clear: left;
                padding-top: 2px;
                height: 60px;
                text-align: center;
                font-weight: bold;
                font-size: 24px;
                color: #000000;
                /*background: url(images/templatemo_header_bg.gif) no-repeat;*/
            }
            #inner_header {
                height: 30px;
                background: url(images/templatemo_header.jpg) no-repeat center center;
            }
            #templatemo_left_column {
                clear: left;
                float: left;
                width: 100%; 
            }
            #templatemo_right_column {
                float: right;
                width: 216px;
                padding-right: 15px;
            }
            #templatemo_footer {
                clear: both;
                /*padding-top: 18px;*/
                height: 15px;
                text-align: center;
                font-size: 11px;
                /*background: url(images/templatemo_footer_bg.gif) no-repeat;*/
                color: #ffffff;
            }
            #templatemo_footer a {
                color: #666666;
            }
            #templatemo_site_title {
                padding-top: 65px;
                font-weight: bold;
                font-size: 28px;
                color: #000000;
            }
            #templatemo_site_slogan {
                padding-top: 14px;
                font-weight: bold;
                font-size: 13px;
                color: #AAFFFF;
            }
            .templatemo_spacer {
                clear: left;
                height: 18px;
            }
            .templatemo_pic {
                float: left;
                margin-right: 10px;
                margin-bottom: 10px;
                border: 1px solid #000000;
            }
            .section_box {
                margin: 10px;
                padding: 10px;
                border: 1px dashed #ffffff;
                background: #FFFFFF;
                border: 1px solid #000000;
            }
            .section_box2 {
                clear: left;
                margin-top: 10px;
                background: #ffffff;
                color: #000000;
                font-weight: bold;
                border: 1px solid #000000;
            }
            .section_box3 {
                clear: left;
                margin-top: 10px;
                background: #ffffff;
                color: #000000;
                border: 1px;
            }
            .text_area {
                padding: 10px;
            }
            .publish_date {
                clear: both;
                margin-top: 10px;
                color: #999999;
                font-size: 11px;
                font-weight: bold;
            }
            .title {
                padding-bottom: 12px;
                font-size: 18px;
                font-weight: bold;
                color: #000000;
            }
            .subtitle {
                padding-bottom: 6px;
                font-size: 14px;
                font-weight: bold;
                color: #666666;
            }
            .post_title_main {
                padding: 6px;
                padding-left: 10px;
                background: #cccccc;
                font-size: 14px;
                font-weight: bold;
                color: #000000;
                border-bottom: 1px solid #000000;
                text-align:left;
            }
            .post_title {
                padding: 6px;
                padding-left: 10px;
                background: #cccccc;
                font-size: 14px;
                font-weight: bold;
                color: #000000;
                border-bottom: 1px solid #000000;
                text-align:left;
            }
            .templatemo_menu {
                list-style-type: none;
                margin: 10px;
                margin-top: 0px;
                padding: 0px;
                width: 195px;
            }
            .templatemo_menu li a{
                background: #F4F4F4 url(images/button_default.gif) no-repeat;
                font-size: 13px;
                font-weight: bold;
                color: #000000;
                display: block;
                width: auto;
                margin-bottom: 2px;
                padding: 5px;
                padding-left: 12px;
                text-decoration: none;
            }
            * html .templatemo_menu li a{ 
                width: 190px;
            }
            .templatemo_menu li a:visited, .templatemo_menu li a:active{
                color: #000000;
            }
            .templatemo_menu li a:hover{
                background: #EEEEEE url(images/button_active.gif) no-repeat;
                color: #FF3333;
            }#templatemo_container_wrapper #templatemo_container #templatemo_left_column .text_area .section_box2 .post_title_main strong td {
                color: #000000;
            }
            #templatemo_container_wrapper #templatemo_container #templatemo_left_column .text_area .section_box2 .post_title_main {
                color: #000000;
            }
            div {
                color: #000000;
            }
            .page {
                width: 21cm;
                height:27.7cm;
                padding: 2cm;
                margin: 1cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .page_landscape {
                width: 28cm;
                height:21cm;
                padding: 2cm;
                margin: 1cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .table-print {
                font-size: 10pt;
                font-family: verdana;
                font-weight: normal;
                padding: 0px;
                margin: 0px;
                border-top: 1.5px solid #333333;
                border-left: 1.5px solid #333333;
                border-collapse: collapse;
            }

            .table-print th {
                text-align: center;
                border-right: 1.5px solid #333333;
                border-bottom: 1.5px solid #333333;
                padding: 5px;
                margin:0px;


            }

            .table-print td {
                text-align: left;
                border-right: 1.5px solid #333333;
                border-bottom: 1.5px solid #333333;
                padding: 1px;
                margin:0px;
                font-weight: normal;
            }

        </style>
    </head>
    <body>
        <div class="page">
            <div id="templatemo_container_wrapper">
                <div id="templatemo_container">
                    <div id="templatemo_header">
                        <table width="100%">
                            <tr><td height="60" width="200px" align="center" style="vertical-align:middle;">
                                  <?php if($batch['logo_koperasi']!=''){ ?>
                                  <img src="<?=base_url()?>images/<?=$batch['logo_koperasi']?>" width="70" height="50">
                                  <? } ?></td>
                                <td height="60"  align="center" style="vertical-align:middle;font-size: 14px;">
                                    Packing List Treacebility <?php echo  $batch['pembeli'] ?><br>
                                    <?php echo  $batch['pedagang'] ?><br>
                                    Kabupaten <?php echo  $batch['District'] ?><br>
                                </td>
                                <td height="60" width="200px" align="center" style="vertical-align:middle;">
                                    <?php if($batch['logo_sertifikasi']!=''){ ?>
                                    <img src="<?=base_url()?>images/<?=$batch['logo_sertifikasi']?>" width="70" height="50">
                                    <? } ?></td></tr>
                        </table>
                    </div><br><br>

                    <div class="text_area">
                        <table width="100%">
                            <?php 
                                $i = 0; $j = 0; $k = 0;
                                for ($i; $i<count($detail);$i++) {
                                //for ($i; $i<100;$i++) {
                            ?>
                            <tr>
                                <td>
                                    <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
                                        <thead class="post_title_main" style="text-align:center">
                                            <tr><th style="border:1px solid" width="15%">No Goni</th>
                                                <th style="border:1px solid" width="35%">Jumlah</th>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            for ($j++;$i<$j*25;$i++){?>
                                            <tr>
                                                <td style="border:1px solid"><?php echo $i + 1 ?></td>
                                                <td style="border:1px solid" align="center">
                                                    <?php 
                                                        $sub = $detail[$i]['NettoDelivery'];
                                                        $subtotal[$j] += $sub;
                                                        echo $sub>0?$sub:0;
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr><th>Sub Total <?php echo $j; ?></th><th><?php echo  $subtotal[$j];  ?></th></tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
                                        <thead class="post_title_main" style="text-align:center">
                                            <tr><th style="border:1px solid" width="15%">No Goni</th>
                                                <th style="border:1px solid" width="35%">Jumlah</th>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            for ($j++;$i<$j*25;$i++){?>
                                            <tr>
                                                <td style="border:1px solid"><?php echo $i + 1 ?></td>
                                                <td style="border:1px solid" align="center">
                                                    <?php 
                                                        $sub = $detail[$i]['NettoDelivery'];
                                                        $subtotal[$j] += $sub;
                                                        echo $sub>0?$sub:0;
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr><th>Sub Total <?php echo $j; ?></th><th><?php echo  $subtotal[$j] ?></th></tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
                                        <thead class="post_title_main" style="text-align:center">
                                            <tr><th style="border:1px solid" width="15%">No Goni</th>
                                                <th style="border:1px solid" width="35%">Jumlah</th>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            for ($j++;$i<$j*25;$i++){?>
                                            <tr>
                                                <td style="border:1px solid"><?php echo $i + 1 ?></td>
                                                <td style="border:1px solid" align="center">
                                                    <?php 
                                                        $sub = $detail[$i]['NettoDelivery'];
                                                        $subtotal[$j] += $sub;
                                                        echo $sub>0?$sub:0;
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr><th>Sub Total <?php echo $j; ?></th><th><?php echo  $subtotal[$j] ?></th></tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
                                        <thead class="post_title_main" style="text-align:center">
                                            <tr><th style="border:1px solid" width="15%">No Goni</th>
                                                <th style="border:1px solid" width="35%">Jumlah</th>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            for ($j++;$i<$j*25;$i++){?>
                                            <tr>
                                                <td style="border:1px solid"><?php echo $i + 1 ?></td>
                                                <td style="border:1px solid" align="center">
                                                    <?php 
                                                        $sub = $detail[$i]['NettoDelivery'];
                                                        $subtotal[$j] += $sub;
                                                        echo $sub>0?$sub:0;
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr><th>Sub Total <?php echo $j; ?></th><th><?php echo  $subtotal[$j] ?></th></tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <?php 
                                    if( ($i == 100 AND count($detail) > $i) OR ($i>100 AND ($i-100)%100==0 AND $i < count($detail)) ){
                                        echo "</table></div></div></div></div>";
                                        echo    '<div class="page">
                                                <div id="templatemo_container_wrapper">
                                                    <div id="templatemo_container">
                                                        <div id="templatemo_header">
                                                            <table width="100%">
                                                                <tr><td height="60" width="200px" align="center" style="vertical-align:middle;">
                                                                      <!--<img src="'.base_url().'images/LogoCargill CocoaPromise.jpg">--></td>
                                                                    <td height="60"  align="center" style="vertical-align:middle;font-size: 14px;">
                                                                        Packing List Treacebility Cargill<br>
                                                                        Nama Pedagang '.$batch["pedagang"].'<br>
                                                                        Kabupaten '.$batch["District"].'<br>
                                                                    </td>
                                                                    <td height="60" width="200px" align="center" style="vertical-align:middle;">
                                                                    <!--<img src="'.base_url().'images/LogoCargill.jpg">--></td></tr>
                                                            </table>
                                                        </div><br><br>
                                                        <div class="text_area">
                                                            <table width="100%">';
                                        $i = $i-1;
                                    }
                            
                                } 
                            
                            ?>
                        </table>
                        <br>
                        <table width="100%">
                            <tr>
                                <td width="15%">Berat Bersih</td>
                                <td>: 
                                    <?php
                                        $total = 0;
                                        for($x=1;$x<count($subtotal);$x++){
                                            echo $subtotal[$x];
                                            if($x != count($subtotal) - 1){
                                                echo ' + ';
                                            }
                                            $total = $total + $subtotal[$x];
                                        }
                                        echo ' = <b>'.$total.' Kg</b>';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="15%">Jumlah Karung</td>
                                <td>: <?php echo count($detail); ?>
                                </td>
                            </tr>
                        </table>
                        <br><br>
                        <table width="100%" style="text-align:center">
                            <tr><td colspan="2">Pedagang</td></tr>
                            <tr><td colspan="2"><br><br><br><br></td></tr>
                            <tr><td colspan="2">(<?php echo  $batch['pedagang'] ?>)</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($batch['LabelKarung']=='1') {?>
            <?php 
                $karung = $batch['DestKarungEnd']-$batch['DestKarungStart']+1;
                $a = 0;
                function karung($i,$sisa=0,$trans,$batch,$cpg='') {
                    $berat = ($trans[$i]['FAQVolumeBruto']/$batch['VolumeBruto']*$batch['DestWeight']) + $sisa;
                    $data['cpg'] = $cpg;
                    $data['cpg_berat'] = $cpg_berat;
                    if ($berat<62.5 and $trans[$i+1]['FAQVolumeBruto']>0) {
                        $data['desa'] = $trans[$i]['Village'];
                        $data['kec'] = $trans[$i]['SubDistrict'];
                        $data['kab'] = $trans[$i]['District'];
                        $data['cpg'][] = $trans[$i]['FarmerID'];
                        $data['cpg_berat'][] = $berat-$sisa;
                        return karung($i+1,$berat,$trans,$batch,$data['cpg']);
                    } elseif ($trans[$i]['FAQVolumeBruto']=='') {
                        $data['desa'] = $trans[$i-1]['Village'];
                        $data['kec'] = $trans[$i-1]['SubDistrict'];
                        $data['kab'] = $trans[$i-1]['District'];
                        $data['cpg'][0] = $trans[$i-1]['FarmerID'];
                    } else {
                        $data['desa'] = $trans[$i]['Village'];
                        $data['kec'] = $trans[$i]['SubDistrict'];
                        $data['kab'] = $trans[$i]['District'];
                        $data['cpg'][] = $trans[$i]['FarmerID'];
                        $data['cpg_berat'][] = 62.5-$sisa;
                        if ($berat>62.5) {
                            $data['cpg_a'][0] = $trans[$i]['FarmerID'];
                            $data['cpg_berat_a'][0] = $berat-62.5;
                        }
                        $data['i'] = $i+1;
                        $data['sisa'] = $berat-62.5;
                    }
                    $data['berat'] = $berat>62.5?62.5:$berat;
                    /*
                    $berat = ($trans[$i]['FAQVolumeBruto']/$batch['VolumeBruto']*$batch['DestWeight']) + $sisa;
                    $data['cpg'] = $cpg;
                    $data['cpg'][] = $trans[$i]['FarmerID'];
                    if ($berat<62.5 and $trans[$i+1]['FAQVolumeBruto']>0) return karung($i+1,$berat,$trans,$batch,$data['cpg']);
                    else {
                    $data['desa'] = $trans[$i]['Village'];
                    $data['kec'] = $trans[$i]['SubDistrict'];
                    $data['kab'] = $trans[$i]['District'];
                    $data['berat'] = $berat>62.5?62.5:$berat;
                    $data['i'] = $i+1;
                    $data['sisa'] = $berat-62.5;
                    if ($data['berat']<62.5) {
                    $data['desa'] = $trans[$i-1]['Village'];
                    $data['kec'] = $trans[$i-1]['SubDistrict'];
                    $data['kab'] = $trans[$i-1]['District'];
                    $data['cpg'][0] = $trans[$i-1]['FarmerID'];
                    }
                    }*/
                    return $data;
                }
                for ($i=0;$i<$karung;$i++){ ?>
                    <div class="page_landscape">
                        <div class="header" style="width: 49%;float: left;">
                            <table width="100%" cellspacing="0" cellpadding="0" border="1">
                                <tr><td rowspan="4" width="15%" align="center"><!--<img src="<?php echo  base_url() ?>images/LogoCargill CocoaPromise.jpg">--></td>
                                    <td rowspan="4" width="20%" align="center">BIJI KAKAO <br><?php echo  $batch['Name'] ?></td>
                                    <td rowspan="4" width="15%" align="center"><!--<img src="<?php echo  base_url() ?>images/LogoCargill.jpg">--></td>
                                </tr>
                            </table>
                            <table class="table-print" cellpadding="0" width="100%">
                                <tr><td colspan="2"></td></tr>
                                <tr><td width="35%">Jenis Kakao</td><td><?php echo  ($detail[$i]['Type'] == 'FF' ? 'Fermentasi' : 'Asalan'); ?></td></tr>
                                <tr><td>No Identitas Karung</td><td>KGG <?php echo  str_pad($batch['DestKarungStart'] + $i, 5, '0', STR_PAD_LEFT); ?></td></tr>
                                <tr><td>No Urut Karung</td><td><?php echo  $i + 1; ?></td></tr>
                                <tr><td>Total Jumlah Karung</td><td><?php echo  sizeof($detail); ?></td></tr>
                                <?php 
                                //echo $a;
                                $data = karung($a,$data['sisa'],$trans,$batch,$data['cpg_a']);
                                //print_r($data);
                                $a = $data['i'];?>
                                <tr><td>ID Petani</td><td><?php echo  implode(', ', $data['cpg']); ?></td></tr>
                                <tr><td>Jumlah Petani</td><td><?php echo  sizeof($data['cpg']); ?></td></tr>
                                <tr><td>Desa</td><td><?php echo  $data['desa']; ?></td></tr>
                                <tr><td>Kecamatan</td><td><?php echo  $data['kec']; ?></td></tr>
                                <tr><td>Kabupaten</td><td><?php echo  $data['kab']; ?></td></tr>
                                <tr><td>Total Jumlah (Kg)</td><td><?php echo  $detail[$i]['Weight']; ?></td></tr>

                                <tr><td>Unit Pembelian</td><td><?php echo  $batch['SubDistrict']; ?></td></tr>
                                <tr><td>Penanggungjawab</td><td><?php echo  $batch['perwakilan']; ?></td></tr>
                                <tr><td>Tanggal Pengiriman</td><td><?php echo  SetTanggal($batch['DeliveryDate']); ?></td></tr>
                                <tr><td>Nama Pembeli</td><td><?php echo  $batch['pembeli']; ?></td></tr>
                                <tr><td>Alamat Pembeli</td><td><?php echo  $batch['pembeli_alamat']; ?></td></tr>
                                <tr><td colspan="2"></td></tr>
                                <tr><td style="text-align:center">Unit Pembelian</td><td style="text-align:center">Traceability</td></tr>
                                <tr><td><br /><br /><br /></td><td></td></tr>
                                <tr><td style="text-align:center"><?php echo  $batch['SubDistrict']; ?></td>
                                    <td style="text-align:center"><?php echo  $batch['DestICS']; ?></td></tr>
                            </table>         
                        </div>   

                        <?php $i++;
                        if ($detail[$i]['Weight']>0) {?>

                        <div class="header" style="width: 49%;float: right;">
                            <table width="100%" cellspacing="0" cellpadding="0" border="1">
                               <tr><td rowspan="4" width="15%" align="center"><!--<img src="<?php echo  base_url() ?>images/LogoCargill CocoaPromise.jpg">--></td>
                                    <td rowspan="4" width="20%" align="center">BIJI KAKAO <br><?php echo  $batch['Name'] ?></td>
                                    <td rowspan="4" width="15%" align="center"><!--<img src="<?php echo  base_url() ?>images/LogoCargill.jpg">--></td>
                                </tr>
                            </table>
                            <table class="table-print" cellpadding="0" width="100%">
                                <tr><td colspan="2"></td></tr>
                                <tr><td width="35%">Jenis Kakao</td><td><?php echo  ($detail[$i]['Type'] == 'FF' ? 'Fermentasi' : 'Asalan'); ?></td></tr>
                                <tr><td>No Identitas Karung</td><td>KGG <?php echo  str_pad($batch['DestKarungStart'] + $i, 5, '0', STR_PAD_LEFT); ?></td></tr>
                                <tr><td>No Urut Karung</td><td><?php echo  $i + 1; ?></td></tr>
                                <tr><td>Total Jumlah Karung</td><td><?php echo  sizeof($detail); ?></td></tr>
                                <?php 
                                //echo $a;
                                $data = karung($a,$data['sisa'],$trans,$batch,$data['cpg_a']);
                                //print_r($data);
                                $a = $data['i'];?>
                                <tr><td>ID Petani</td><td><?php echo  implode(', ', $data['cpg']); ?></td></tr>
                                <tr><td>Jumlah Petani</td><td><?php echo  sizeof($data['cpg']); ?></td></tr>
                                <tr><td>Desa</td><td><?php echo  $data['desa']; ?></td></tr>
                                <tr><td>Kecamatan</td><td><?php echo  $data['kec']; ?></td></tr>
                                <tr><td>Kabupaten</td><td><?php echo  $data['kab']; ?></td></tr>
                                <tr><td>Total Jumlah (Kg)</td><td><?php echo  $detail[$i]['Weight']; ?></td></tr>

                                <tr><td>Unit Pembelian</td><td><?php echo  $batch['SubDistrict']; ?></td></tr>
                                <tr><td>Penanggungjawab</td><td><?php echo  $batch['perwakilan']; ?></td></tr>
                                <tr><td>Tanggal Pengiriman</td><td><?php echo  SetTanggal($batch['DeliveryDate']); ?></td></tr>
                                <tr><td>Nama Pembeli</td><td><?php echo  $batch['pembeli']; ?></td></tr>
                                <tr><td>Alamat Pembeli</td><td><?php echo  $batch['pembeli_alamat']; ?></td></tr>
                                <tr><td colspan="2"></td></tr>
                                <tr><td style="text-align:center">Unit Pembelian</td><td style="text-align:center">Traceability</td></tr>
                                <tr><td><br /><br /><br /></td><td></td></tr>
                                <tr><td style="text-align:center"><?php echo  $batch['SubDistrict']; ?></td>
                                    <td style="text-align:center"><?php echo  $batch['DestICS']; ?></td></tr>
                            </table>         
                        </div>   
                        <?php }?>
                    </div>
                <?php }?>
        <?php }?>
        <!-- daftar transaksi -->
        <?php //if(count($po) > 0) { ?>
        <!--<div class="page">
           <div id="templatemo_container_wrapper">
              <div id="templatemo_container">
                 <div id="templatemo_header">
                 <table width="100%">
                    <tr><td height="60" width="200px" align="center" style="vertical-align:middle;">
                          </td>
                       <td height="60"  align="center" style="vertical-align:middle;font-size: 14px;">
                       Daftar Transaksi <br>
                       </td>
                       <td height="60" width="200px" align="center" style="vertical-align:middle;">
                       </td></tr>
                 </table>
                 </div><br><br>
                 
                 <div class="text_area">
                  <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
                    <thead class="post_title_main" style="text-align:center">
                       <tr>
                            <th style="border:1px solid" width="5%">No</th>
                            <th style="border:1px solid" width="15%">No PO</th>
                            <th style="border:1px solid" width="35%">Jumlah</th>
                       </tr>
                    </thead>
                    <tbody>
        <?php //foreach($po as $pokeys => $poval) { ?>
                            <tr>
                                <td style="border:1px solid; text-align: right; padding-right: 3px" width="5%"><?php //echo $pokeys+1;  ?></th>
                                <td style="border:1px solid; text-align: center;" width="25%"><?php //echo $poval['DestPO'];  ?></th>
                                <td style="border:1px solid; text-align: right; padding-right: 3px" width="10%"><?php //echo $poval['VolumeNetto'];  ?> Kg</th>
                            </tr>
        <?php //}
        ?>
                    </tbody>
                </table>
                <br><br>
                <table width="100%" style="text-align:center">
                    <tr><td colspan="2">Pedagang</td></tr>
                    <tr><td colspan="2"><br><br><br><br></td></tr>
                    <tr><td colspan="2">(<?php //=$batch['pedagang']?>)</td></tr>
                </table>
                 </div>
              </div>
           </div>
        </div>-->
        <?php //}  ?>
        <div class="page">
            <div id="templatemo_container_wrapper">
                <div id="templatemo_container">
                    <div id="templatemo_header">
                        <table width="100%">
                            <tr><td width="200px" align="center" style="vertical-align:middle;">
                                  <!--<img src="<?php echo  base_url() ?>images/LogoCargill CocoaPromise.jpg">--></td>
                                <td height="60"  align="center" style="vertical-align:middle;font-size: 14px;">
                                    Pedagang <?php echo  $batch['pedagang'] ?><br>
                                    Kabupaten <?php echo  $batch['District'] ?><br><br>
                                </td>
                                <td width="200px" align="center" style="vertical-align:middle;">
                                <!--<img src="<?php echo  base_url() ?>images/LogoCargill.jpg">--></td></tr>
                            <tr><td colspan="3" style="border-top: 3px solid; padding-top: 15px;">SURAT JALAN</td></tr>
                        </table>
                    </div><br><br><br><br><br>

                    <div class="text_area">
                        Kami yang bertanda tangan di bawah ini :<br><br>
                        <table width="100%">
                            <tr><td width="20%">Nama</td><td>: <?php echo  $batch['koperasi_nama'] ?></td></tr>
                            <tr><td>Alamat</td><td>: <?php echo  $batch['koperasi_alamat'] ?></td></tr>
                        </table><br>
                        Menerangkan bahwa yang bersangkutan di bawah ini adalah :<br><br>
                        <table width="100%">
                            <tr><td width="20%">Nama</td><td>: <?=$batch['DestDriver']?></td></tr>
                            <tr><td>Jabatan</td><td>: <?=$batch['DestDriverJabatan']?></td></tr>
                            <tr><td>Alamat</td><td>: <?=$batch['DestDriverAddress']?></td></tr>
                        </table><br>
                        Membawa Kakao Traceability dari <?php echo  $batch['Name'] . ', ' . $batch['District'] ?> dengan berat <?php echo  $batch['DestWeight'] ?> Kg.
                        Kendaraan yang digunakan <?=$batch['DestTransport']?> No Polisi <?=$batch['DestNoPolisi']?> dengan tujuan :<br><br>
                        <table width="100%">
                            <tr><td width="20%">Nama Pembeli</td><td>: <?php echo  $batch['pembeli'] ?></td></tr>
                            <tr><td>Alamat</td><td>: <?php echo  $batch['pembeli_alamat'] ?></td></tr>
                            <!--<tr><td>No Telp/HP</td><td>: <?php ?></td></tr>-->
                        </table>


                        <br><br>
                        <table class="table-print" width="100%" style="text-align:center">
                            <tr><td colspan="2"><?php echo  $batch['District'] . ', ' . SetTanggal($batch['DeliveryDate']) ?></td></tr>
                            <tr><td width="50%">Pedagang</td><td>Sopir</td></tr>
                            <tr><td><br><br><br><br><?php echo  $batch['pedagang'] ?></td>
                                <td><br><br><br><br><?php echo  $batch['DestDriver'] ?></td></tr>

                            <tr><td colspan="2" align="center">Telah diterima oleh </td></tr>
                            <tr><td colspan="2" align="center"><br><br><br><br>...........................</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="page">
            <div id="templatemo_container_wrapper">
                <div id="templatemo_container">
                    <div id="templatemo_header">
                        <table width="100%">
                            <tr>
                                <td width="200px" align="center" style="vertical-align:middle;"><!--<img src="<?php echo  base_url() ?>images/LogoCargill CocoaPromise.jpg">--></td>
                                <td height="60"  align="center" style="vertical-align:middle;font-size: 14px;">
                                    Pedagang <?php echo  $batch['pedagang'] ?><br>
                                    Kabupaten <?php echo  $batch['District'] ?><br><br>
                                </td>
                                <td width="200px" align="center" style="vertical-align:middle;"><!--<img src="<?php echo  base_url() ?>images/LogoCargill.jpg">--></td>
                            </tr>
                            <tr><td colspan="3" style="border-top: 3px solid; padding-top: 15px;">DAFTAR LIST TRANSAKSI</td></tr>
                        </table>
                    </div>
                    <br><br><br><br><br>
                    <div class="text_area">
                        <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
                            <thead class="post_title_main" style="text-align:center">
                                <tr>
                                    <th style="border:1px solid" width="5%">No</th>
                                    <th style="border:1px solid">No. PO</th>
                                    <th style="border:1px solid">Petani</th>
                                    <th style="border:1px solid">Buying Unit</th>
                                    <th style="border:1px solid" width="15%">Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                if ($transaksi->num_rows() > 0) {
                                    $no = 1;
                                    foreach ($transaksi->result() as $row) { ?> 
                                        <tr>
                                            <td style="border:1px solid"><center><?php echo $no; ?></center></td>
                                            <td style="border:1px solid"><center><?php echo $row->nopo; ?></center></td>
                                            <td style="border:1px solid"><?php echo $row->label; ?></td>
                                            <td style="border:1px solid"><?php echo $row->unit; ?></td>
                                            <td style="border:1px solid;" align="right"><?php echo $row->berat; ?></td>
                                        </tr>
                                    <?php 
                                        if($no == 30 OR ($no>30 and ($no-30)%30==0) and $no < $transaksi->num_rows()){
                                            echo "</tbody></table></div></div></div></div>";
                                            echo '<div class="page">
                                                    <div id="templatemo_container_wrapper">
                                                        <div id="templatemo_container">
                                                            <div id="templatemo_header">
                                                                <table width="100%">
                                                                    <tr>
                                                                        <td width="200px" align="center" style="vertical-align:middle;"><!--<img src="'.base_url().'images/LogoCargill CocoaPromise.jpg">--></td>
                                                                        <td height="60"  align="center" style="vertical-align:middle;font-size: 14px;">
                                                                            Pedagang '.$batch['pedagang'].'<br>
                                                                            Kabupaten '.$batch['District'].'<br><br>
                                                                        </td>
                                                                        <td width="200px" align="center" style="vertical-align:middle;"><!--<img src="'.base_url().'images/LogoCargill.jpg">--></td>
                                                                    </tr>
                                                                    <tr><td colspan="3" style="border-top: 3px solid; padding-top: 15px;">DAFTAR LIST TRANSAKSI</td></tr>
                                                                </table>
                                                            </div>
                                                            <br><br><br><br><br>
                                                            <div class="text_area">
                                                                <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
                                                                    <thead class="post_title_main" style="text-align:center">
                                                                        <tr>
                                                                            <th style="border:1px solid" width="5%">No</th>
                                                                            <th style="border:1px solid">No. PO</th>
                                                                            <th style="border:1px solid">Petani</th>
                                                                            <th style="border:1px solid">Buying Unit</th>
                                                                            <th style="border:1px solid" width="15%">Berat</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>';
                                        }
                                    ?>
                                <?php $no++; } ?>

                            <?php
                                } else {
                                    echo "<tr><td colspan='3'><center>Data transaksi kosong.</center></td></tr>";
                                } ?>
                            </tbody>
                        </table>
                        <br><br>
                        <table width="100%" style="text-align:center">
                            <tr><td colspan="2">Pedagang</td></tr>
                            <tr><td colspan="2"><br><br><br><br></td></tr>
                            <tr><td colspan="2">(<?php echo  $batch['pedagang'] ?>)</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

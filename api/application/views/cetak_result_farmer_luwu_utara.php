<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <title>Farmer</title>
        <style type="text/css">
            body {
                margin: 0;
                padding: 0;
                font-family: Tahoma, Verdana, Helvetica, Arial;
                font-size: 10px;
                color: #000000;
                background-color: #ffffff;
            }
            * {
                box-sizing: border-box;
                -moz-box-sizing: border-box;
            }
            .page {
                width: 21cm;
                height:27.5cm;
                padding-top: 0.5cm;
                padding-bottom: 0.5cm;
                padding-right: 0.5cm;
                padding-left: 0.5cm;
                margin: 0.2cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }

            @page {
                size: A4 ;
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

            textarea {
                font-family: Tahoma, Verdana, Helvetica, Arial;
                padding: 0;
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
                height: 50px;
                text-align: center;
                font-weight: bold;
                font-size: 20px;
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
                font-size: 10px;
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
                margin-right: 8px;
                margin-bottom: 8px;
                border: 1px solid #000000;
            }
            .section_box {
                margin: 8px;
                padding: 8px;
                border: 1px dashed #ffffff;
                background: #FFFFFF;
                border: 1px solid #000000;
            }
            .section_box2 {
                clear: left;
                margin-top: 8px;
                background: #ffffff;
                color: #000000;
                font-weight: bold;
                border: 1px solid #000000;
            }
            .section_box3 {
                clear: left;
                margin-top: 8px;
                background: #ffffff;
                color: #000000;
                border: 1px;
            }
            .text_area {
                padding: 0px 2px 0px 2px;
                font-size: 10px;
            }
            .publish_date {
                clear: both;
                margin-top: 8px;
                color: #999999;
                font-size: 10px;
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
                padding: 2px;
                padding-left: 8px;
                background: #cccccc;
                font-size: 12px;
                font-weight: bold;
                color: #000000;
              border-bottom: 1px solid #000000;
              text-align:left;
            }
            .post_title {
                padding: 2px;
                padding-left: 8px;
                background: #cccccc;
                font-size: 12px;
                font-weight: bold;
                color: #000000;
              border-bottom: 1px solid #000000;
              text-align:left;
            }
            .templatemo_menu {
                list-style-type: none;
                margin: 8px;
                margin-top: 0px;
                padding: 0px;
                width: 195px;
            }
            .templatemo_menu li a{
                background: #F4F4F4 url(images/button_default.gif) no-repeat;
                font-size: 12px;
                font-weight: bold;
                color: #000000;
                display: block;
                width: auto;
                margin-bottom: 2px;
                padding: 5px;
                padding-left: 12px;
                text-decoration: none;
            }
            .box {
              border:1px solid #000000;
              font-family: Tahoma, Verdana, Helvetica, Arial;
              color: #000000;
              font-size: 10px;
            }
            .box13 {
                border:1px solid #000000;
                font-size: 10px;
            }
            .box_disabled {
                border:1px solid #000000;
                background-color:#CCCCCC;
            }
            .font11 {
              font-size: 10px;
            }
            .font12 {
                font-size: 10px;
            }
            .font13 {
                font-size: 10px;
            }
            .fontred {
                font-size: 12px;
                color:#F00;
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
            font_red {
                color: #F00;
            }
            input{
                border: 1px solid #000000;
                background-color: #FFF;
                font-size: 10px;
                padding: 0;
            }
            .page-break {
                page-break-before: always;
            }
        </style>
    </head>
    <body>
        <div id="templatemo_container_wrapper">
            <div class="page">
                <div id="templatemo_container">
                    <div id="templatemo_header" class="logos" style="height:100px; margin-top:-15px;">
                        <table width="100%" border="0" cellpadding="2">
                            <tr>
                                <?php if ($SpecLuwuUtara != "1") {?>
                            <td width="20%" align="left" style="vertical-align:middle;">
                                <img src="<?=base_url()?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
                            </td>
                        <?php }
?>
                        <?php
for ($i = 0; $i < count($logos); $i++) {
    if ($logos[$i]['Photo'] != '') {
        ?>
                            <td height="60px" width="20%" align="left" style="vertical-align:middle;">
                                <img src="<?=base_url()?>images/Photo/<?=$logos[$i]['Photo']?>" style="max-width:90%; max-height:90%; max-width:120px;">
                            </td>
                        <?php
}
}
?>
                        <?php if ($SpecLuwuUtara != "1") {?>
                            <td width="20%" align="right" style="vertical-align:middle;">
                                <img src="<?=base_url()?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
                            </td>
                        <?php }
?>
                        <td width="20%" align="right" style="vertical-align:middle;">
                            <img src="<?=base_url()?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
                        </td>
                            </tr>
                        </table>
                        <table width="100%" border="0" cellpadding="2" style="margin-top:-15px;">
                            <tr>
                                <td align="center" style="vertical-align:middle;text-decoration:underline;"><?php echo lang('P1 - Cocoa Farmer Basic Data')?></td>
                            </tr>
                        </table>
                    </div>
                    <div id="templatemo_left_column" style="margin-top:-20px;">
                        <div class="text_area" align="center">
                            <div class="section_box2" align="center">
                                <div class="post_title_main"><strong>
                                        <table width="100%">
                                            <tr>
                                                <td width="40%" align="left"><?php echo lang('Data Umum Petani Kakao')?></td>
                                                <td width="30%" align="center" class="fontred"><?php echo lang('CPG')?> - <?=$data['CPGid']?></td>
                                                <td width="30%" align="right" class="fontred"><?php echo lang('PK')?> - <?=$data['FarmerID']?></td>
                                            </tr>
                                        </table>
                                    </strong>
                                </div>
                                <div class="text_area">
                                    <table width="100%" cellspacing="1">
                                        <tr>
                                            <td width="30%"><?php echo lang('Nama Petani')?></td>
                                            <td colspan="2" class="box">
                                                <input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['PersonNm']?>" size="51"/>
                                            </td>
                                            <td width="30%" rowspan="10" class="box">
                                                <img class="photoPetani" src="<?=base_url() . 'images/Photo/' . $data['Photo']?>" height="195px" style="-webkit-user-select: none"></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Alamat')?></td>
                                            <td colspan="2" class="box" style="height: 40px"><?=$data['alamat']?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Desa')?></td>
                                            <td colspan="2" class="box">
                                                <input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['Desa']?>" size="51"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Kecamatan')?></td>
                                            <td colspan="2" class="box">
                                                <input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['Kecamatan']?>" size="51"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Kabupaten')?></td>
                                            <td colspan="2" class="box">
                                                <input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['Kabupaten']?>" size="51"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Provinsi')?></td>
                                            <td colspan="2" class="box">
                                                <input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['Provinsi']?>" size="51"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Jenis Kelamin')?></td>
                                            <td colspan="2" class="box"><label>
                                                    <input disabled type="radio" name="RadioGroup5<?=$data['FarmerID']?>" value="radio" id="RadioGroup5_0" <?=$data['Gender'] == '1' ? 'checked="checked"' : ''?>/>
                                                    <?php echo lang('Laki-laki')?></label>
                                                <label>
                                                    <input disabled type="radio" name="RadioGroup5<?=$data['FarmerID']?>" value="radio" id="RadioGroup5_1" <?=$data['Gender'] == '2' ? 'checked="checked"' : ''?> />
                                                    <?php echo lang('Perempuan')?></label></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Status Perkawinan')?></td>
                                            <td colspan="2" class="box"><label>
                                                    <input disabled type="radio" name="RadioGroup6<?=$data['FarmerID']?>" value="radio" id="RadioGroup6_0" <?=$data['MaritalSt'] == '1' ? 'checked="checked"' : ''?>/>
                                                    1) <?php echo lang('Menikah')?></label>
                                                <label>
                                                    <input disabled type="radio" name="RadioGroup6<?=$data['FarmerID']?>" value="radio" id="RadioGroup6_1" <?=$data['MaritalSt'] == '2' ? 'checked="checked"' : ''?>/>
                                                    2) <?php echo lang('Single')?></label>
                                                <label>
                                                    <input disabled type="radio" name="RadioGroup6<?=$data['FarmerID']?>" value="radio" id="RadioGroup6_2" <?=$data['MaritalSt'] == '3' ? 'checked="checked"' : ''?>/>
                                                    3) <?php echo lang('Janda/Duda')?></label></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Tanggal Lahir')?></td>
                                            <td width="26%" class="box">
                                                <input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['BirthDttm']?>" size="20"/>
                                            </td>
                                            <td width="44%" class="fontred">mis: 1971-05-03</td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Nomor Handphone')?></td>
                                            <td colspan="2" class="box">
                                                <input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['HandPhone']?>" size="51"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Pendidikan Terakhir')?></td>
                                            <td colspan="3" class="box">
                                                <table width="100%">
                                                    <tr class="font11">
                                                        <td width="32%"><label>
                                                                <input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_0" <?=$data['Education'] == '1' ? 'checked="checked"' : ''?>/>
                                                                1) <?php echo lang('Tidak pernah sekolah')?></label></td>
                                                        <td width="32%"><label>
                                                                <input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_1" <?=$data['Education'] == '3' ? 'checked="checked"' : ''?>/>
                                                                3) <?php echo lang('SD, tidak melanjutkan')?></label></td width="36%">
                                                        <td><label>
                                                                <input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_2" <?=$data['Education'] == '5' ? 'checked="checked"' : ''?>/>
                                                                5) <?php echo lang('Tamat SMA/Sederajat')?></label></td>
                                                    </tr>
                                                    <tr class="font11">
                                                        <td><label>
                                                                <input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_3" <?=$data['Education'] == '2' ? 'checked="checked"' : ''?>/>
                                                                2) <?php echo lang('Tidak tamat SD')?></label></td>
                                                        <td><label>
                                                                <input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_4" <?=$data['Education'] == '4' ? 'checked="checked"' : ''?>/>
                                                                4) <?php echo lang('Tamat SMP')?></label></td>
                                                        <td><label>
                                                                <input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_5" <?=$data['Education'] == '6' ? 'checked="checked"' : ''?>/>
                                                                6) <?php echo lang('Tamat perguruan tinggi')?></label></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo lang('Status Petani')?>
                                            </td>
                                            <td colspan="3" class="box">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="32%"><label><input disabled type="radio" name="status<?=$data['StatusFarmer']?>" value="radio" id="status_0" <?=$data['StatusFarmer'] == '1' ? 'checked="checked"' : ''?>/>1) <?php echo lang('Aktif')?></label></td>
                                                        <td><label><input disabled type="radio" name="status<?=$data['StatusFarmer']?>" value="radio" id="status_0" <?=$data['StatusFarmer'] == '2' ? 'checked="checked"' : ''?>/>2) <?php echo lang('Tidak Aktif')?></label></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!-- Status Garden -->
                            <div align="center" class="section_box2">
                                <div class="post_title"><strong><?php echo lang('Status Kebun Kakao')?></strong></div>
                                <div class="text_area">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="1">
                                        <tbody>
                                        <tr align="center" class="post_title2">
                                            <td width="12%"><?php echo lang('Kebun Nr')?></td>
                                            <td width="12%"><?php echo lang('Ukuran')?> (Ha)</td>
                                            <td width="15%"><?php echo lang('Status Kebun')?></td>
                                            <td><?php echo lang('Keterangan')?></td>
                                        </tr>
                                        <?php foreach ($garden_status as $key => $value): ?>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo $value['GardenNr']?>" size="10" style="border: 1px solid #000000;background-color: #FFF;text-align:center"disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="<?php echo $value['GardenHaUnCertified']?>" size="10" style="border: 1px solid #000000;background-color: #FFF;text-align:center"disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="
                                                <?php
switch ($value['GardenStatus']) {
    case '2':
        echo lang('Moved/left the area');
        break;
    case '3':
        echo lang('Switched to other crop');
        break;
    case '4':
        echo lang('Sold the land');
        break;
    case '5':
        echo lang('Gave the land to family member');
        break;
    case '6':
        echo lang('Force Major');
        break;
}
?>
                                                " size="22" style="border: 1px solid #000000;background-color: #FFF" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="<?php echo $value['Remarks']?>" size="45px" style="border: 1px solid #000000;background-color: #FFF" disabled="">
                                            </td>
                                        </tr>
                                        <?php endforeach?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <?php echo lang('Keterangan : Status Kebun Kakao diisi jika ada kebun yang Tidak Aktif. Pilihan status kebun, antara lain : Pindah/Beralih Ke Lahan Lain, Beralih Ke Komoditas Lain, Lahan Dijual, Diwariskan Ke Anggota Keluarga, atau Terkena Hal Yang Tidak Terduga / Bencana. Jika Beralih Ke Komoditas Lain, maka Keterangan diisi dengan jenis komoditasnya, yakni : Jagung, Sawit, Karet, Cengkeh, Padi, Buah-Buahan, Kayu-Kayuan, Kosong, Dll.')?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <!-- Komoditas Lain -->
                            <div align="center" class="section_box2">
                                <div class="post_title"><strong><?php echo lang('Komoditas Lain')?></strong></div>
                                <div class="text_area">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="1">
                                        <tbody>
                                        <tr align="center" class="post_title2">
                                            <td width="25%"><?php echo lang('Komoditas')?></td>
                                            <td>Luas (Ha)</td>
                                        </tr>
                                        <?php if (!empty($other_land)): ?>
                                        <?php foreach ($other_land as $key => $value): ?>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo $value['Commodity_label']?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="<?php echo $value['GardenHa']?>" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <?php endforeach?>
                                        <?php else: ?>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo lang('Jagung')?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo lang('Sawit')?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo lang('Karet')?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo lang('Cengkeh')?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo lang('Padi')?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <!-- <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="Kosong" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr> -->
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo lang('Buah-buahan')?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo lang('Kayu-kayuan')?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <tr class="font12">
                                            <td align="center">
                                                <input type="text" value="<?php echo lang('Dll')?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                            <td align="center">
                                                <input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
                                            </td>
                                        </tr>
                                        <?php endif?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="post_title"><strong><?php echo lang('Data Keluarga')?></strong></div>
                                <div class="text_area">
                                    <table width="100%" border="1" cellspacing="0" cellpadding="0">
                                        <tr class="post_title2" align="center">
                                            <td width="21%"><?php echo lang('Nama Anggota Keluarga')?></td>
                                            <td width="32%"><?php echo lang('Hubungan Keluarga')?></td>
                                            <td width="10%"><?php echo lang('Tahun Lahir')?></td>
                                            <td width="24%"><?php echo lang('Jenis Kelamin')?></td>
                                            <td width="24%"><?php echo lang('Sedang Sekolah')?></td>
                                        </tr>
                                        <?php for ($i = 0; $i < 5; $i++) {?>
                                            <tr class="font12">
                                                <td>
                                                    <input disabled style="border: 1px solid #000000;background-color: #FFF" type="text" size="21" value="<?=$anggota[$i]['AnggotaName']?>"/>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input disabled type="radio" value="radio" id="RadioGroup1_0" <?=$anggota[$i]['HubunganKeluarga'] == '1' ? 'checked="checked"' : ''?>><?php echo lang('Suami/Istri')?></label>
                                                    <label>
                                                        <input disabled type="radio" value="radio" id="RadioGroup1_1" <?=$anggota[$i]['HubunganKeluarga'] == '2' ? 'checked="checked"' : ''?>><?php echo lang('Anak')?></label>
                                                    <label>
                                                        <input disabled type="radio" value="radio" id="RadioGroup1_2" <?=$anggota[$i]['HubunganKeluarga'] == '3' ? 'checked="checked"' : ''?>><?php echo lang('Lain-lain')?></label>
                                                </td>
                                                <td align="center">
                                                    <input disabled style="border: 1px solid #000000;background-color: #FFF" type="text" size="7" value="<?=$anggota[$i]['AnggotaAge']?>"/>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input disabled type="radio" value="radio" id="RadioGroup2_0" <?=$anggota[$i]['AnggotaGender'] == '1' ? 'checked="checked"' : ''?>><?php echo lang('Laki-laki')?></label>
                                                    <label>
                                                        <input disabled type="radio" value="radio" id="RadioGroup2_1" <?=$anggota[$i]['AnggotaGender'] == '2' ? 'checked="checked"' : ''?>><?php echo lang('Perempuan')?></label>
                                                </td>
                                                <td align="center">
                                                    <label>
                                                        <input disabled type="radio" value="radio" id="RadioGroup3_0" <?=$anggota[$i]['StatusSekolah'] == '1' ? 'checked="checked"' : ''?>><?php echo lang('Ya')?></label>
                                                    <label>
                                                        <input disabled type="radio" value="radio" id="RadioGroup3_1" <?=$anggota[$i]['StatusSekolah'] == '2' ? 'checked="checked"' : ''?>><?php echo lang('Tidak')?></label>
                                                </td>
                                            </tr>
                                        <?}
?>
                                    </table>
                                </div>
                            </div>
                            <div class="section_box2" align="center">
                                <div class="post_title"><strong><?php echo lang('Data Pendukung')?></strong></div>
                                <div class="text_area">
                                    <table width="100%" cellspacing="1">
                                        <!-- <tr>
                                            <td width="60%">Apakah anda seorang pedagang Kakao</td>
                                            <td width="40%" class="box">
                                                <label>
                                                    <input disabled type="radio" name="RadioGroup4" value="radio" id="RadioGroup4_0" <?=$data['Muge'] == '1' ? 'checked="checked"' : ''?>/>
                                                    Ya</label>
                                                <label>
                                                    <input disabled type="radio" name="RadioGroup4" value="radio" id="RadioGroup4_1" <?=$data['Muge'] == '2' ? 'checked="checked"' : ''?>/>
                                                    Tidak</label></td>
                                        </tr>
                                        <tr>
                                            <td width="60%">Apakah Anda anggota aktif dalam Koperasi</td>
                                            <td width="40%" class="box">
                                                <label>
                                                    <input disabled type="radio" name="RadioGroup41" value="radio" id="RadioGroup4_0" <?=$data['ActiveMemberCooperation'] == '1' ? 'checked="checked"' : ''?>/>
                                                    Ya</label>
                                                <label>
                                                    <input disabled type="radio" name="RadioGroup41" value="radio" id="RadioGroup4_1" <?=$data['ActiveMemberCooperation'] == '2' ? 'checked="checked"' : ''?>/>
                                                    Tidak</label></td>
                                        </tr> -->
                                        <tr>
                                            <td><?php echo lang('Cocoa Production Group Nr')?></td>
                                            <td class="box">&nbsp;<?=$data['CPGid']?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Apa nama kelompok tani anda')?></td>
                                            <td class="box">&nbsp;<?=$data['GroupName']?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <?php if (!empty($cert) and $SurveyNr > 0): ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-break"></div>
            <div class="page">
                <div id="templatemo_container">
                    <div id="templatemo_left_column">
                        <div class="text_area" align="center">
                            <div align="center" class="section_box2">
                                <div class="post_title"><strong><?php echo lang('Data Rekening Bank')?></strong></div>
                                <div class="text_area">
                                    <table width="100%" cellspacing="1">
                                        <tbody>
                                        <tr>
                                            <td width="30%"><?php echo lang('Nama Pemegang Rekening')?></td>
                                            <td width="" class="box"><?php echo $data['AccountBeneficiary']?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%"><?php echo lang('Nama Bank')?></td>
                                            <td width="" class="box"><?php echo $data['BankName']?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Cabang Bank')?></td>
                                            <td class="box"><?php echo $data['BankBranch']?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Nomor Rekening')?></td>
                                            <td class="box"><?php echo $data['AccountNumber']?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endif?>
                            <!-- <div class="section_box2" align="center">
                                <div class="post_title"><strong>Lahan Pertanian</strong></div>
                                <div class="text_area">
                                    <table width="100%" cellspacing="1">
                                        <tr>
                                            <td width="79%">Luas Kebun Kakao yang dimiliki</td>
                                            <td width="16%" class="box">&nbsp;<?=$data['LahanKakao']?></td>
                                            <td width="5%">
                                                hektar
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Berapa titik Kebun Kakao yang dimiliki <span class="font12"> Setiap Kebun Kakao harus diisi pada halaman 3 � 4</span>
                                            </td>
                                            <td class="box">&nbsp;<?=$data['KebunKakao']?></td>
                                            <td>
                                                kebun
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Luas Tanaman lainnya selain Kakao</td>
                                            <td class="box">&nbsp;<?=$data['LahanProduksiLain']?></td>
                                            <td>
                                                hektar
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Kepemilikan Lahan kosong</td>
                                            <td class="box">&nbsp;<?=$data['LahanKosong']?></td>
                                            <td>
                                                hektar
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah total Lahan Pertanian yang dimiliki</td>
                                            <td class="box">&nbsp;<?=$data['TotalLahan']?></td>
                                            <td>
                                                hektar
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div> -->
                            <div class="section_box2">
                                <div class="post_title"><strong><?php echo lang('Tanggal Wawancara dan Menandatangani')?></strong></div>
                                <div class="text_area">
                                    <table width="100%" cellspacing="1">
                                        <tr>
                                            <td width="40%"><?php echo lang('Tanggal Wawancara')?> (mis: 3-Mei-2012)</td>
                                            <td width="20%" rowspan="4">&nbsp;</td>
                                            <td width="40%" class="box">&nbsp;<?=$harvest['DateCollection2']?></td>
                                        </tr>
                                        <tr>
                                            <td height="40">&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td><?php echo lang('Nama Petani')?></td>
                                            <td><?php echo lang('Nama Pewawancara')?></td>
                                        </tr>
                                        <tr>
                                            <td class="box"><strong>
                                                    <?=$harvest['PersonNm']?></strong></td>
                                            <td class="box">&nbsp;</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-break"></div>

        </div>
    </body>
</html>
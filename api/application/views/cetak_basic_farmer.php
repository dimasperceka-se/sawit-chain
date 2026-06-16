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
				font-size: 8px;
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
				height: 25px;	/* 'padding-top' + 'height' must be equal to the 'background image height' */
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
				font-size: 8px;
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
				font-size: 8px;
			}
			.publish_date {
				clear: both;
				margin-top: 8px;
				color: #999999;
				font-size: 8px;
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
			  font-size: 8px;
			}
			.box13 {
				border:1px solid #000000;
				font-size: 8px;
			}
			.box_disabled {
				border:1px solid #000000;
				background-color:#CCCCCC;
			}
			.font11 {
			  font-size: 8px;
			}
			.font12 {
				font-size: 8px;
			}
			.font13 {
				font-size: 8px;
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
				font-size: 8px;
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
								<td width="20%" align="left" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
								</td>
								<?php 								
									for($i=0;$i<count($logos);$i++){
										if($logos[$i]['Photo']!=''){
								?>
									<td height="60px" width="20%" align="left" style="vertical-align:middle;">
										<img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
									</td>
								<?php
										}
									}
								?>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
							</tr>
						</table>
						<table width="100%" border="0" cellpadding="2" style="margin-top:-15px;">
							<tr>
								<td align="center" style="vertical-align:middle;text-decoration:underline;"><?php echo lang('P1 - Cocoa Farmer Basic Data') ?></td>
							</tr>
						</table>
					</div>
					<div id="templatemo_left_column" style="margin-top:-20px;">
						<div class="text_area" align="center">
							<div class="section_box2" align="center">
								<div class="post_title_main"><strong>
										<table width="100%">
											<tr>
												<td width="40%" align="left"><?php echo lang('Data Umum Petani Kakao') ?></td>
												<td width="30%" align="center" class="fontred"><?php echo lang('CPG') ?> - <?=$data['CPGid']?></td>
												<td width="30%" align="right" class="fontred"><?php echo lang('PK') ?> - <?=$data['FarmerID']?></td>
											</tr>
										</table>
									</strong>
								</div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="30%"><?php echo lang('Nama Petani') ?></td>
											<td colspan="2" class="box">
												<input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['PersonNm']?>" size="51"/>
											</td>
											<td width="30%" rowspan="10" class="box">
												<img class="photoPetani" src="<?=base_url() . 'images/Photo/' . $data['Photo']?>" height="195px" style="-webkit-user-select: none"></td>
										</tr>
										<tr>
											<td><?php echo lang('Alamat') ?></td>
											<td colspan="2" class="box" style="height: 40px"><?=$data['alamat']?></td>
										</tr>
										<tr>
											<td><?php echo lang('Desa') ?></td>
											<td colspan="2" class="box">
												<input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['Desa']?>" size="51"/>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Kecamatan') ?></td>
											<td colspan="2" class="box">
												<input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['Kecamatan']?>" size="51"/>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Kabupaten') ?></td>
											<td colspan="2" class="box">
												<input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['Kabupaten']?>" size="51"/>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Provinsi') ?></td>
											<td colspan="2" class="box">
												<input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['Provinsi']?>" size="51"/>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Jenis Kelamin') ?></td>
											<td colspan="2" class="box"><label>
													<input disabled type="radio" name="RadioGroup5<?=$data['FarmerID']?>" value="radio" id="RadioGroup5_0" <?=$data['Gender'] == '1' ? 'checked="checked"' : ''?>/>
													<?php echo lang('Laki-laki') ?></label>
												<label>
													<input disabled type="radio" name="RadioGroup5<?=$data['FarmerID']?>" value="radio" id="RadioGroup5_1" <?=$data['Gender'] == '2' ? 'checked="checked"' : ''?> />
													<?php echo lang('Perempuan') ?></label></td>
										</tr>
										<tr>
											<td><?php echo lang('Status Perkawinan') ?></td>
											<td colspan="2" class="box"><label>
													<input disabled type="radio" name="RadioGroup6<?=$data['FarmerID']?>" value="radio" id="RadioGroup6_0" <?=$data['MaritalSt'] == '1' ? 'checked="checked"' : ''?>/>
													1) <?php echo lang('Menikah') ?></label>
												<label>
													<input disabled type="radio" name="RadioGroup6<?=$data['FarmerID']?>" value="radio" id="RadioGroup6_1" <?=$data['MaritalSt'] == '2' ? 'checked="checked"' : ''?>/>
													2) <?php echo lang('Single') ?></label>
												<label>
													<input disabled type="radio" name="RadioGroup6<?=$data['FarmerID']?>" value="radio" id="RadioGroup6_2" <?=$data['MaritalSt'] == '3' ? 'checked="checked"' : ''?>/>
													3) <?php echo lang('Janda/Duda') ?></label></td>
										</tr>
										<tr>
											<td><?php echo lang('Tanggal Lahir') ?></td>
											<td width="26%" class="box">
												<input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['BirthDttm']?>" size="20"/>
											</td>
											<td width="44%" class="fontred">mis: 1971-05-03</td>
										</tr>
										<tr>
											<td><?php echo lang('Nomor Handphone') ?></td>
											<td colspan="2" class="box">
												<input disabled style="border: 1px solid #FFFFFF;background-color: #FFF" type="text" value="<?=$data['HandPhone']?>" size="51"/>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Pendidikan Terakhir') ?></td>
											<td colspan="3" class="box">
												<table width="100%">
													<tr class="font11">
														<td width="32%"><label>
																<input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_0" <?=$data['Education'] == '1' ? 'checked="checked"' : ''?>/>
																1) <?php echo lang('Tidak pernah sekolah') ?></label></td>
														<td width="32%"><label>
																<input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_1" <?=$data['Education'] == '3' ? 'checked="checked"' : ''?>/>
																3) <?php echo lang('SD, tidak melanjutkan') ?></label></td width="36%">
														<td><label>
																<input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_2" <?=$data['Education'] == '5' ? 'checked="checked"' : ''?>/>
																5) <?php echo lang('Tamat SMA/Sederajat') ?></label></td>
													</tr>
													<tr class="font11">
														<td><label>
																<input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_3" <?=$data['Education'] == '2' ? 'checked="checked"' : ''?>/>
																2) <?php echo lang('Tidak tamat SD') ?></label></td>
														<td><label>
																<input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_4" <?=$data['Education'] == '4' ? 'checked="checked"' : ''?>/>
																4) <?php echo lang('Tamat SMP') ?></label></td>
														<td><label>
																<input disabled type="radio" name="pendidikan_terakhir<?=$data['FarmerID']?>" value="radio" id="pendidikan_terakhir_5" <?=$data['Education'] == '6' ? 'checked="checked"' : ''?>/>
																6) <?php echo lang('Tamat perguruan tinggi') ?></label></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo lang('Status Petani') ?>
											</td>
											<td colspan="3" class="box">
												<table width="100%">
													<tr>
														<td width="32%"><label><input disabled type="radio" name="status<?=$data['StatusFarmer']?>" value="radio" id="status_0" <?=$data['StatusFarmer'] == '1' ? 'checked="checked"' : ''?>/>1) <?php echo lang('Aktif') ?></label></td>
														<td><label><input disabled type="radio" name="status<?=$data['StatusFarmer']?>" value="radio" id="status_0" <?=$data['StatusFarmer'] == '2' ? 'checked="checked"' : ''?>/>2) <?php echo lang('Tidak Aktif') ?></label></td>
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
								<div class="post_title"><strong><?php echo lang('Status Kebun Kakao') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="0" cellpadding="0" border="1">
										<tbody>
										<tr align="center" class="post_title2">
											<td width="12%"><?php echo lang('Kebun Nr') ?></td>
											<td width="12%"><?php echo lang('Ukuran') ?> (Ha)</td>
											<td width="15%"><?php echo lang('Status Kebun') ?></td>
											<td><?php echo lang('Keterangan') ?></td>
										</tr>
										<?php foreach ($garden_status as $key => $value): ?>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo $value['GardenNr'] ?>" size="10" style="border: 1px solid #000000;background-color: #FFF;text-align:center"disabled="">
											</td>
											<td align="center">
												<input type="text" value="<?php echo $value['GardenHaUnCertified'] ?>" size="10" style="border: 1px solid #000000;background-color: #FFF;text-align:center"disabled="">
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
												<input type="text" value="<?php echo $value['Remarks'] ?>" size="45px" style="border: 1px solid #000000;background-color: #FFF" disabled="">
											</td>
										</tr>
										<?php endforeach ?>
										</tbody>
										<tfoot>
											<tr>
												<td colspan="4">
													<?php echo lang('Keterangan : Status Kebun Kakao diisi jika ada kebun yang Tidak Aktif. Pilihan status kebun, antara lain : Pindah/Beralih Ke Lahan Lain, Beralih Ke Komoditas Lain, Lahan Dijual, Diwariskan Ke Anggota Keluarga, atau Terkena Hal Yang Tidak Terduga / Bencana. Jika Beralih Ke Komoditas Lain, maka Keterangan diisi dengan jenis komoditasnya, yakni : Jagung, Sawit, Karet, Cengkeh, Padi, Buah-Buahan, Kayu-Kayuan, Kosong, Dll.') ?>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							<!-- Komoditas Lain -->
							<div align="center" class="section_box2">
								<div class="post_title"><strong><?php echo lang('Komoditas Lain') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="0" cellpadding="0" border="1">
										<tbody>
										<tr align="center" class="post_title2">
											<td width="25%"><?php echo lang('Komoditas') ?></td>
											<td>Luas (Ha)</td>
										</tr>
										<?php if (!empty($other_land)): ?>
										<?php foreach ($other_land as $key => $value): ?>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo $value['Commodity_label'] ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
											<td align="center">
												<input type="text" value="<?php echo $value['GardenHa'] ?>" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
										</tr>
										<?php endforeach ?>
										<?php else: ?>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo lang('Jagung') ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
											<td align="center">
												<input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
										</tr>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo lang('Sawit') ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
											<td align="center">
												<input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
										</tr>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo lang('Karet') ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
											<td align="center">
												<input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
										</tr>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo lang('Cengkeh') ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
											<td align="center">
												<input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
										</tr>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo lang('Padi') ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
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
												<input type="text" value="<?php echo lang('Buah-buahan') ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
											<td align="center">
												<input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
										</tr>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo lang('Kayu-kayuan') ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
											<td align="center">
												<input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
										</tr>
										<tr class="font12">
											<td align="center">
												<input type="text" value="<?php echo lang('Dll') ?>" size="25" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
											<td align="center">
												<input type="text" value="" size="71" style="border: 1px solid #000000;background-color: #FFF;text-align:center" disabled="">
											</td>
										</tr>
										<?php endif ?>
										</tbody>
									</table>
								</div>
								<div class="post_title"><strong><?php echo lang('Data Keluarga') ?></strong></div>
								<div class="text_area">
									<table width="100%" border="1" cellspacing="0" cellpadding="0">
										<tr class="post_title2" align="center">
											<td width="21%"><?php echo lang('Nama Anggota Keluarga') ?></td>
											<td width="32%"><?php echo lang('Hubungan Keluarga') ?></td>
											<td width="10%"><?php echo lang('Tahun Lahir') ?></td>
											<td width="24%"><?php echo lang('Jenis Kelamin') ?></td>
											<td width="24%"><?php echo lang('Sedang Sekolah') ?></td>
										</tr>
										<?php for ($i = 0; $i < 5; $i++) { ?>
											<tr class="font12">
												<td>
													<input disabled style="border: 1px solid #000000;background-color: #FFF" type="text" size="21" value="<?=$anggota[$i]['AnggotaName']?>"/>
												</td>
												<td>
													<label>
														<input disabled type="radio" value="radio" id="RadioGroup1_0" <?=$anggota[$i]['HubunganKeluarga'] == '1' ? 'checked="checked"' : ''?>><?php echo lang('Suami/Istri') ?></label>
													<label>
														<input disabled type="radio" value="radio" id="RadioGroup1_1" <?=$anggota[$i]['HubunganKeluarga'] == '2' ? 'checked="checked"' : ''?>><?php echo lang('Anak') ?></label>
													<label>
														<input disabled type="radio" value="radio" id="RadioGroup1_2" <?=$anggota[$i]['HubunganKeluarga'] == '3' ? 'checked="checked"' : ''?>><?php echo lang('Lain-lain') ?></label>
												</td>
												<td align="center">
													<input disabled style="border: 1px solid #000000;background-color: #FFF" type="text" size="7" value="<?=$anggota[$i]['AnggotaAge']?>"/>
												</td>
												<td>
													<label>
														<input disabled type="radio" value="radio" id="RadioGroup2_0" <?=$anggota[$i]['AnggotaGender'] == '1' ? 'checked="checked"' : ''?>><?php echo lang('Laki-laki') ?></label>
													<label>
														<input disabled type="radio" value="radio" id="RadioGroup2_1" <?=$anggota[$i]['AnggotaGender'] == '2' ? 'checked="checked"' : ''?>><?php echo lang('Perempuan') ?></label>
												</td>
												<td align="center">
													<label>
														<input disabled type="radio" value="radio" id="RadioGroup3_0" <?=$anggota[$i]['StatusSekolah'] == '1' ? 'checked="checked"' : ''?>><?php echo lang('Ya') ?></label>
													<label>
														<input disabled type="radio" value="radio" id="RadioGroup3_1" <?=$anggota[$i]['StatusSekolah'] == '2' ? 'checked="checked"' : ''?>><?php echo lang('Tidak') ?></label>
												</td>
											</tr>
										<? } ?>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Data Pendukung') ?></strong></div>
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
											<td><?php echo lang('Cocoa Production Group Nr') ?></td>
											<td class="box">&nbsp;<?=$data['CPGid']?></td>
										</tr>
										<tr>
											<td><?php echo lang('Apa nama kelompok tani anda') ?></td>
											<td class="box">&nbsp;<?=$data['GroupName']?></td>
										</tr>
									</table>
								</div>
							</div>
							<?php if (!empty($cert) AND $SurveyNr > 0): ?>
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
								<div class="post_title"><strong><?php echo lang('Data Rekening Bank') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tbody>
										<tr>
											<td width="30%"><?php echo lang('Nama Pemegang Rekening') ?></td>
											<td width="" class="box"><?php echo $data['AccountBeneficiary'] ?></td>
										</tr>
										<tr>
											<td width="30%"><?php echo lang('Nama Bank') ?></td>
											<td width="" class="box"><?php echo $data['BankName'] ?></td>
										</tr>
										<tr>
											<td><?php echo lang('Cabang Bank') ?></td>
											<td class="box"><?php echo $data['BankBranch'] ?></td>
										</tr>
										<tr>
											<td><?php echo lang('Nomor Rekening') ?></td>
											<td class="box"><?php echo $data['AccountNumber'] ?></td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
							<?php endif ?>
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
								<div class="post_title"><strong><?php echo lang('Tanggal Wawancara dan Menandatangani') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="40%"><?php echo lang('Tanggal Wawancara') ?> (mis: 3-Mei-2012)</td>
											<td width="20%" rowspan="4">&nbsp;</td>
											<td width="40%" class="box">&nbsp;<?=$harvest['DateCollection2']?></td>
										</tr>
										<tr>
											<td height="40">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><?php echo lang('Nama Petani') ?></td>
											<td><?php echo lang('Nama Pewawancara') ?></td>
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
			<div class="page">
				<div id="templatemo_container">
					<? for ($i = 0; $i < sizeof($garden); $i++) { ?>
					<div id="templatemo_header" class="logos" style="height:100px; margin-top:-15px;">
						<table width="100%" border="0" cellpadding="2">
							<tr>								
								<td width="20%" align="left" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
								</td>
								<?php 								
									for($i=0;$i<count($logos);$i++){
										if($logos[$i]['Photo']!=''){
								?>
									<td height="60px" width="20%" align="left" style="vertical-align:middle;">
										<img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
									</td>
								<?php
										}
									}
								?>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
							</tr>
						</table>
						<table width="100%" border="0" cellpadding="2" style="margin-top:-20px;">
							<tr>
								<td align="center" style="vertical-align:middle;text-decoration:underline;font-size:16px;">P1 - Cocoa Farmer Basic Data</td>
							</tr>
						</table>
					</div>
					<div id="templatemo_left_column" style="margin-top:-30px;">
							<div class="text_area" align="center">
								<div class="section_box2" align="center">
									<div class="post_title">
										<table width="100%" cellspacing="1">
											<tr>
												<td width="280"><?php echo lang('P1 - Kebun kakao') ?></td>
												<td width="170px" class="fontred"><?php echo lang('CPG') ?> - <?=$data['CPGid']?></td>
												<td width="170px" class="fontred"><?php echo lang('PK') ?> - <?=$data['FarmerID']?></td>
												<td align="right" class="fontred"><?php echo lang('Kebun Nr') ?> - <?=$garden[$i]['GardenNr']?></td>
										</table>
									</div>
									<div class="text_area">
										<table width="100%" cellspacing="1">
											<tr>
												<td width="45%"><?php echo lang('Tanggal wawancara') ?> (mis: 3-Mei-2012)</td>
												<td width="30%" class="box">&nbsp;<?=$garden[$i]['DateCollection']?></td>
												<td width="10%" align="right"><?php echo lang('Survey Nr') ?>.</td>
												<td width="5%" class="box">&nbsp;<?=$SurveyNr?></td>
											</tr>
										</table>
									</div>
									<!--<div class="post_title"><strong>Data Umum Kebun Kakao</strong></div>-->
									<div class="text_area">
										<table width="100%" cellspacing="1">
											<tr>
												<td width="25%"><?php echo lang('Kondisi Jalan ke kebun kakao') ?></td>
												<td width="75%" class="box13">
													<label>
														<input disabled type="radio" id="RadioGroup11_0" <?=$garden[$i]['RoadCondition'] == '1' ? 'checked="yes"' : ''?>>1)
														<?php echo lang('Jalan Aspal') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_1" <?=$garden[$i]['RoadCondition'] == '2' ? 'checked="yes"' : ''?>>2)
														<?php echo lang('Jalan Pengerasan') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_2" <?=$garden[$i]['RoadCondition'] == '3' ? 'checked="yes"' : ''?>>3)
														<?php echo lang('Jalan Tanah') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_3" <?=$garden[$i]['RoadCondition'] == '4' ? 'checked="yes"' : ''?>>4)
														<?php echo lang('Tidak ada Jalan') ?></label>
												</td>
											</tr>
											<tr>
												<td><?php echo lang('Status kepemilikan tanah') ?></td>
												<td class="box13">
													<label>
														<input disabled type="radio" id="RadioGroup11_0" <?=$garden[$i]['OwnershipCocoa'] == '1' ? 'checked="yes"' : ''?>>1)
														<?php echo lang('Pemilik Penggarap') ?> </label>
													<label>
														<input disabled type="radio" id="RadioGroup11_1" <?=$garden[$i]['OwnershipCocoa'] == '2' ? 'checked="yes"' : ''?>>2)
														<?php echo lang('Petani Bagi Hasil') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_2" <?=$garden[$i]['OwnershipCocoa'] == '3' ? 'checked="yes"' : ''?>>3)
														<?php echo lang('Petani Penyewa') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_3" <?=$garden[$i]['OwnershipCocoa'] == '4' ? 'checked="yes"' : ''?>>4)
														<?php echo lang('Lain-lain') ?></label></td>
											</tr>
											<tr>
												<td><?php echo lang('Siapa yang memiliki Tanah') ?></td>
												<td class="box13">
													<label>
														<input disabled type="radio" id="RadioGroup111_0" <?=$garden[$i]['LandOwner'] == '1' ? 'checked="yes"' : ''?>>1)
														<?php echo lang('Saya Sendiri') ?> </label>
													<label>
														<input disabled type="radio" id="RadioGroup111_1" <?=$garden[$i]['LandOwner'] == '2' ? 'checked="yes"' : ''?>>2)
														<?php echo lang('Anggota Keluarga') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup111_2" <?=$garden[$i]['LandOwner'] == '3' ? 'checked="yes"' : ''?>>3)
														<?php echo lang('Orang Lain') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup111_3" <?=$garden[$i]['LandOwner'] == '4' ? 'checked="yes"' : ''?>>4)
														<?php echo lang('Tidak Tahu') ?></label></td>
											</tr>
											<!--<tr><td>Ada sertifikat kepemilikan tanah</td><td class="box13"> <label>
						<input disabled type="radio" id="RadioGroup11_0" <?=$garden[$i]['LandCertificate'] == '1' ? 'checked="yes"' : ''?>>1) Tidak ada</label>
						<label>
						<input disabled type="radio" id="RadioGroup11_1" <?=$garden[$i]['LandCertificate'] == '2' ? 'checked="yes"' : ''?>>2) Akte Notaris/BPN</label>
						<label>
						<input disabled type="radio" id="RadioGroup11_2" <?=$garden[$i]['LandCertificate'] == '3' ? 'checked="yes"' : ''?>>3) SKKT (Camat)</label>
						<label>
						<input disabled type="radio" id="RadioGroup11_3" <?=$garden[$i]['LandCertificate'] == '4' ? 'checked="yes"' : ''?>>4) Desa/Lurah</label>
						<label>
						<input disabled type="radio" id="RadioGroup11_4" <?=$garden[$i]['LandCertificate'] == '5' ? 'checked="yes"' : ''?>>5) Tidak Tahu</label></td></tr>-->
											<tr>
												<td><?php echo lang('Ada sertifikat kepemilikan tanah') ?></td>
												<td class="box13"><label>
														<input disabled type="radio" id="RadioGroup11_0" <?=$garden[$i]['LandCertificate'] == '1' ? 'checked="yes"' : ''?>>1)
														<?php echo lang('Tidak ada') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_1" <?=$garden[$i]['LandCertificate'] == '2' ? 'checked="yes"' : ''?>>2)
														<?php echo lang('Akte Notaris/BPN') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_2" <?=$garden[$i]['LandCertificate'] == '3' ? 'checked="yes"' : ''?>>3)
														<?php echo lang('SKKT (Camat)') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_3" <?=$garden[$i]['LandCertificate'] == '4' ? 'checked="yes"' : ''?>>4)
														<?php echo lang('Desa/Lurah') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup11_4" <?=$garden[$i]['LandCertificate'] == '5' ? 'checked="yes"' : ''?>>5)
														<?php echo lang('Tidak Tahu') ?></label></td>
											</tr>
										</table>
									</div>

									<div class="post_title"><strong><?php echo lang('Lokasi dan Luas Kebun Kakao') ?></strong></div>
									<div class="text_area">
										<table width="100%" cellspacing="1">
											<tr>
												<td width="10%"><?php echo lang('Latitude') ?></td>
												<td width="11%" colspan="3">
													<input disabled type="text" name="textfield" id="textfield" size="15" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['Latitude']?>"/>
												</td>
												<td width="6%">&nbsp;</td>
												<td width="29%"><?php echo lang('Elevation') ?></td>
												<td width="7%">
													<input disabled type="text" name="textfield2" id="textfield2" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['Elevation']?>"/>
												</td>
												<td width="7%">mdpl</td>
												<td><?php echo lang('Ukuran kebun') ?></td>
												<td>
													<input disabled type="text" name="textfield4" id="textfield4" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['GardenHaUnCertified']?>">
												</td>
												<td><?php echo lang('hektar') ?></td>
											</tr>
											<tr>
											<tr>
												<td><?php echo lang('Longitude') ?></td>
												<td colspan="3">
													<input disabled type="text" name="textfield21" id="textfield21" size="15" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['Longitude']?>"/>
												</td>
												<td>&nbsp;</td>
												<td><?php echo lang('Jarak kebun kakao dari rumah') ?></td>
												<td>
													<input disabled type="text" name="textfield3" id="textfield3" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['GardenDistance']?>">
												</td>
												<td>m</td>
												<td colspan="3"></td>
											</tr>
										</table>
									</div>

									<div class="post_title"><strong><?php echo lang('Jumlah tanaman Kakao') ?></strong></div>
									<div class="text_area">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td width="40">
													<table width="100%" border="0" cellspacing="0" cellpadding="1">
														<tr>
															<td width="70%"><?php echo lang('Tahun tanam kakao') ?></td>
															<td width="30%">
																<input disabled type="text" name="textfield5" id="textfield5" size="10"
																	   value="<?=$garden[$i]['TahunTanamanCocoa']?>" style="border: 1px solid #000000;background-color: #FFF">
															</td>
														</tr>
														<tr>
															<td><?php echo lang('TBM - Tanaman belum menghasilkan') ?></td>
															<td>
																<input disabled type="text" name="textfield6" id="textfield6" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PohonTBM']?>">
																<?php echo lang('pohon') ?>
															</td>
														</tr>
														<tr>
															<td><?php echo lang('TM - Tanaman menghasilkan') ?></td>
															<td>
																<input disabled type="text" name="textfield7" id="textfield7" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PohonTM']?>">
																<?php echo lang('pohon') ?>
															</td>
														</tr>
														<tr>
															<td><?php echo lang('TR - Tanaman rusak') ?></td>
															<td>
																<input disabled type="text" name="textfield8" id="textfield8" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PohonRehab']?>">
																<?php echo lang('pohon') ?>
															</td>
														</tr>
														<tr>
															<td style="border-top:1px solid #000000;"><?php echo lang('Jumlah pohon total') ?></td>
															<td style="border-top:1px solid #000000;">
																<input disabled type="text" name="textfield9" id="textfield9" size="4" style="border: 1px solid #000000;">
																<?php echo lang('pohon') ?>
															</td>
														</tr>
													</table>
												</td>
												<td width="60" style="border-left:1px solid #000000;">
													<table width="100%" border="0" cellspacing="0" cellpadding="1">
														<tr>
															<td colspan="2"></td>
															<td><?php echo lang('Pohon') ?></td>
															<td><?php echo lang('Tahun') ?></td>
														</tr>
														<!--<tr>
						   <td colspan="2">Pohon Rehab</td>
						   <td width="15%" align="right"><input disabled type="text" name="textfield10" id="textfield10" size="5"  value="<?=$garden[$i]['RehabTrees']?>" style = "border: 1px solid #000000;background-color: #FFF"></td>
						   <td width="15%" ><input disabled type="text" name="textfield11" id="textfield11" size="5" style = "border: 1px solid #000000;background-color: #FFF"  value="<?=$garden[$i]['RehabTreesTahun']?>"></td>
						   </tr>
						   <tr>
						   <td colspan="2">Jumlah Pohon Tanam Ulang/Sisip</td>
						   <td width="15%" align="right"><input disabled type="text" name="textfield10" id="textfield10" size="5"  value="<?=$garden[$i]['InsetTrees']?>" style = "border: 1px solid #000000;background-color: #FFF"></td>
						   <td width="15%"><input disabled type="text" name="textfield11" id="textfield11" size="5" style = "border: 1px solid #000000;background-color: #FFF"  value="<?=$garden[$i]['InsetTreesTahun']?>"></td>
						   </tr>-->
														<tr>
															<td colspan="2" style=""><?php echo lang('Pohon sambung samping/sambung pucuk tunas air') ?>:
															</td>
															<td width="15%" style="">
																<input disabled type="text" name="textfield10" id="textfield10" size="5" value="<?=$garden[$i]['GraftedTrees']?>" style="border: 1px solid #000000;background-color: #FFF">
															</td>
															<td width="15%" style="">
																<input disabled type="text" name="textfield11" id="textfield11" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['GraftedTreesTahun']?>">
															</td>
														</tr>
														<!--<tr>
						   <td colspan="2">Jumlah pohon tanam ulang:</td>
						   <td>Pohon</td>
						   <td>Tahun</td>
						   </tr>-->
														<tr>
															<td colspan="2"><?php echo lang('Penanaman ulang dari sambung pucuk dan biji') ?></td>
															<td>
																<input disabled type="text" name="textfield12" id="textfield12" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['TopGraftedTrees']?>">
															</td>
															<td>
																<input disabled type="text" name="textfield13" id="textfield13" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['TopGraftedTreesTahun']?>">
															</td>
														</tr>
														<tr>
															<td colspan="2"><?php echo lang('Penanaman ulang sisipan') ?></td>
															<td>
																<input disabled type="text" name="textfield14" id="textfield14" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['ReplantedTrees']?>">
															</td>
															<td>
																<input disabled type="text" name="textfield26" id="textfield26" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['ReplantedTreesTahun']?>">
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</div>

									<div class="post_title"><strong><?php echo lang('Varietas Tanaman Kakao') ?></strong></div>
									<div class="text_area">
										<fieldset>
											<legend><?php echo lang('Jenis dan jumlah Klon kakao (pilihan ganda)') ?></legend>
											<table width="100%" class="font11">
												<tr class="font12">
													<td><label>
															<input disabled type="checkbox" name="TSH858" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['TSH858'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('TSH 858') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['TSH858Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="RCC73" value="checkbox" id="CheckboxGroup1_1" <?=$garden[$i]['RCC73'] == '1' ? 'checked="yes"' : ''?> />
															<?php echo lang('RCC 73') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['RCC73Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="ICRRI3" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['ICRRI3'] == '1' ? 'checked="yes"' : ''?> />
															<?php echo lang('ICRRI 3') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['ICRRI3Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="M01" value="checkbox" id="CheckboxGroup1_3" <?=$garden[$i]['M01'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('M01') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['M01Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="RCC70" value="checkbox" id="CheckboxGroup1_4" <?=$garden[$i]['RCC70'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('RCC 70') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['RCC70Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="Lokal" value="checkbox" id="CheckboxGroup1_5" <?=$garden[$i]['Hybrid'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('Lokal') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['LokalNr']?>"></label></td>
												</tr>
												<tr>
													<td><label>
															<input disabled type="checkbox" name="ICRRI4" value="checkbox" id="CheckboxGroup1_6 <?=$garden[$i]['ICRRI4'] == '1' ? 'checked="yes"' : ''?>"/>
															<?php echo lang('ICRRI 4') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['ICRRI4Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="M06" value="checkbox" id="CheckboxGroup1_7" <?=$garden[$i]['M06'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('M06') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['M06Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="RCC71" value="checkbox" id="CheckboxGroup1_8" <?=$garden[$i]['RCC71'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('RCC 71') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['RCC71Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="S1" value="checkbox" id="CheckboxGroup1_9" <?=$garden[$i]['S1'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('S1') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['S1Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="ICRRI5" value="checkbox" id="CheckboxGroup1_10" <?=$garden[$i]['ICRRI5'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('ICRRI 5') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['ICRRI5Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="THR" value="checkbox" id="CheckboxGroup1_11" <?=$garden[$i]['THR'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('THR') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['THRNr']?>"></label></td>
												</tr>
												<tr>
													<td><label>
															<input disabled type="checkbox" name="RCC72" value="checkbox" id="CheckboxGroup1_12" <?=$garden[$i]['RCC72'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('RCC 72') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['RCC72Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="S2" value="checkbox" id="CheckboxGroup1_13" <?=$garden[$i]['S2'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('S2') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['S2Nr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="RCL" value="checkbox" id="CheckboxGroup1_14" <?=$garden[$i]['RCL'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('RCL') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['RCLNr']?>"></label></td>
													<td><label>
															<input disabled type="checkbox" name="J45" value="checkbox" id="CheckboxGroup1_15" <?=$garden[$i]['J45'] == '1' ? 'checked="yes"' : ''?>/>
															<?php echo lang('45') ?></td>
													<td><input disabled size="1" value="<?=$garden[$i]['J45Nr']?>"></label></td>
													<td colspan="2"></td>
												</tr>
												<tr>
													<td><?php echo lang('Lain lain') ?></td>
													<td colspan="7" class="box"></td>
													<td colspan="2">
														<input disabled size="10" value="<?=$garden[$i]['CloneLainNr']?>"></td>
													<td><?php echo lang('Jumlah') ?></td>
													<td colspan="2"><input disabled size="10"></td>
												</tr>
											</table>
										</fieldset>
									</div>

									<div class="post_title"><strong><?php echo lang('Jenis dan jumlah Tanaman Lain dalam Kebun Kakao') ?></strong></div>
									<div class="text_area">
										<table width="100%" class="font12">
											<tr>
												<td width="63%">
													<fieldset style="padding:0px">
														<legend><?php echo lang('Tanaman Perkebunan Selain Kakao') ?></legend>
														<table width="100%" class="font12" cellpadding="0">
															<tr>
																<td width="20%"><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Kelapa'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Kelapa') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['KelapaNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Sawit'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Sawit') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['SawitNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Pinang'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Pinang') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['PinangNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Aren'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Aren') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['ArenNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Karet'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Karet') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['KaretNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Pala'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Pala') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['PalaNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Cengkeh'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Cengkeh') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['CengkehNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Kemiri'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Kemiri') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['KemiriNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['JambuMente'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Mente') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['JambuMenteNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Kapok'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Kapok') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['KapokNr']?>">
																</td>
																<td colspan="4"></td>
															</tr>
														</table>
													</fieldset>
													<fieldset style="padding:0px">
														<legend><?php echo lang('Kayu Keras') ?></legend>
														<table width="100%" class="font12" cellpadding="0">
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Mahoni'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Mahoni') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['MahoniNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Uru'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Uru') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['UruNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Jati'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Jati') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['JatiNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Jabon'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Jabon') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['JabonNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Biti'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Biti') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['BitiNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Sengon'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Sengon') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['SengonNr']?>">
																</td>
															</tr>
														</table>
													</fieldset>
													<fieldset style="padding:0px">
														<legend><?php echo lang('Leguminosa') ?></legend>
														<table width="100%" class="font12" cellpadding="0">
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Gamal'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Gamal') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['GamalNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Petai'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Petai') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['PetaiNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Lamtoro'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Lamtoro') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['LamtoroNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Jengkol'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Jengkol') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['JengkolNr']?>">
																</td>
															</tr>
														</table>
													</fieldset>
												</td>
												<td style="vertical-align:top">
													<fieldset style="padding:0px">
														<legend><?php echo lang('Pohon Buah buahan') ?></legend>
														<table width="100%" class="font12" cellpadding="0">
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['JackFruitNr'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Nangka') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['JackFruitNrNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Alpukat'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Alpukat') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['AlpukatNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Pisang'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Pisang') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['PisangNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Sukun'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Sukun') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['SukunNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Rambutan'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Rambutan') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['RambutanNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Pepaya'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Pepaya') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['PepayaNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Mangga'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Mangga') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['ManggaNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['KelaManggispa'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Manggis') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['ManggisNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Langsat'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Langsat') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['LangsataNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Jambu'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Jambu') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['JambuaNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Kedondong'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Kedondong') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['KedondongNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Cempedak'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Cempedak') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['CempedakNr']?>">
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_2" <?=$garden[$i]['Jeruk'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Jeruk') ?></label></td>
																<td>
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['JerukNr']?>">
																</td>
																<td><label>
																		<input disabled type="checkbox" name="CheckboxGroup1" value="checkbox" id="CheckboxGroup1_0" <?=$garden[$i]['Durian'] == '1' ? 'checked="yes"' : ''?>>
																		<?php echo lang('Durian') ?></label></td>
																<td colspan="3">
																	<input disabled type="input disabled" size="1" value="<?=$garden[$i]['DurianNr']?>">
																</td>
															</tr>
														</table>
													</fieldset>
													<table width="100%" class="font12">
														<!--<tr>
						   <td>Lainnya</td>
						   <td><input disabled size="25"  value="<?=$garden[$i]['ShadeLain']?>"></td>
						   <td><input disabled size="2"  value="<?=$garden[$i]['ShadeLainNr']?>"></td>
						   </tr>-->
														<tr>
															<td colspan="2"><?php echo lang('Jumlah Tanaman') ?></td>
															<td><input disabled size="2"></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</div>					
									<div class="post_title"><strong><?php echo lang('Estimasi Produksi Kakao/Tahun') ?></strong></div>
									<div class="text_area">
										<table width="100%" cellspacing="1">
											<tbody>
											<tr>
												<td width="30%"><?php echo lang('Estimasi Produksi (kg/tahun)') ?></td>
												<td class="box"><?php echo ($garden[$i]['PanenTrekMonths'] * $garden[$i]['PanenTrekPanenMonth'] * $garden[$i]['PanenTrekKg']) + ($garden[$i]['PanenBiasaMonths'] * $garden[$i]['PanenBiasaPanenMonth'] * $garden[$i]['PanenBiasaKg']) + ($garden[$i]['PanenRayaMonths'] * $garden[$i]['PanenRayaPanenMonth'] * $garden[$i]['PanenRayaKg']) ?></td>
											</tr>

											</tbody>
										</table>
									</div>
									<div class="post_title"><strong><?php echo lang('Produksi Kakao/Tahun (Jual Kering)') ?></strong></div>
									<div class="text_area">
										<table width="100%" cellspacing="0" cellpadding="1">
											<tr>
												<td width="10%" style="border-bottom:1px solid #000000;"><?php echo lang('Musim') ?></td>
												<td width="10%" style="border-bottom:1px solid #000000;"><?php echo lang('Berapa Bulan') ?></td>
												<td colspan="2" style="border-bottom:1px solid #000000;"><?php echo lang('Interval Panen') ?></td>
												<td width="10%" style="border-bottom:1px solid #000000;"><?php echo lang('Kg/panen') ?></td>
												<td width="10%" style="border-bottom:1px solid #000000;"><?php echo lang('Kg/tahun') ?></td>
											</tr>
											<tr>
												<td><?php echo lang('Panen Trek') ?></td>
												<td>
													<input disabled type="text" name="textfield27" id="textfield27" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PanenTrekMonths']?>">
												</td>
												<td colspan="2" class="box11" width="47%">
													<label>
														<input disabled type="radio" id="RadioGroup12_0" <?=$garden[$i]['PanenTrekPanenMonth'] == '0' ? 'checked="yes"' : ''?>><?php echo lang('Tidak ada') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_1" <?=$garden[$i]['PanenTrekPanenMonth'] == '4' ? 'checked="yes"' : ''?>><?php echo lang('1 kali/minggu') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_2" <?=$garden[$i]['PanenTrekPanenMonth'] == '2' ? 'checked="yes"' : ''?>><?php echo lang('1 Kali/2 minggu') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_3" <?=$garden[$i]['PanenTrekPanenMonth'] == '1' ? 'checked="yes"' : ''?>><?php echo lang('1 Kali/bulan') ?></label></td>
												<td>
													<input disabled type="text" name="textfield31" id="textfield31" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PanenTrekKg']?>">
												</td>
												<td>
													<input disabled type="text" name="textfield35" id="textfield35" size="5" style="border: 1px solid #000000;" value="<?=$garden[$i]['PanenTrekMonths'] * $garden[$i]['PanenTrekPanenMonth'] * $garden[$i]['PanenTrekKg']?>">
												</td>
											</tr>
											<tr>
												<td><?php echo lang('Panen Biasa') ?></td>
												<td>
													<input disabled type="text" name="textfield28" id="textfield28" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PanenBiasaMonths']?>">
												</td>
												<td colspan="2" class="box13"><label>
														<input disabled type="radio" id="RadioGroup12_0" <?=$garden[$i]['PanenBiasaPanenMonth'] == '0' ? 'checked="yes"' : ''?>><?php echo lang('Tidak ada') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_1" <?=$garden[$i]['PanenBiasaPanenMonth'] == '4' ? 'checked="yes"' : ''?>><?php echo lang('1 kali/minggu') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_2" <?=$garden[$i]['PanenBiasaPanenMonth'] == '2' ? 'checked="yes"' : ''?>><?php echo lang('1 Kali/2 minggu') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_3" <?=$garden[$i]['PanenBiasaPanenMonth'] == '1' ? 'checked="yes"' : ''?>><?php echo lang('1 Kali/bulan') ?></label></td>
												<td>
													<input disabled type="text" name="textfield32" id="textfield32" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PanenBiasaKg']?>">
												</td>
												<td>
													<input disabled type="text" name="textfield34" id="textfield34" size="5" style="border: 1px solid #000000;" value="<?=$garden[$i]['PanenBiasaMonths'] * $garden[$i]['PanenBiasaPanenMonth'] * $garden[$i]['PanenBiasaKg']?>">
												</td>
											</tr>
											<tr>
												<td style="border-bottom:1px solid #000000;"><?php echo lang('Panen Raya') ?></td>
												<td style="border-bottom:1px solid #000000;">
													<input disabled type="text" name="textfield29" id="textfield29" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PanenRayaMonths']?>">
												</td>
												<td colspan="2" class="box13" style="border-bottom:1px solid #000000;">
													<label>
														<input disabled type="radio" id="RadioGroup12_0" <?=$garden[$i]['PanenRayaPanenMonth'] == '0' ? 'checked="yes"' : ''?>><?php echo lang('Tidak ada') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_1" <?=$garden[$i]['PanenRayaPanenMonth'] == '4' ? 'checked="yes"' : ''?>><?php echo lang('1 kali/minggu') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_2" <?=$garden[$i]['PanenRayaPanenMonth'] == '2' ? 'checked="yes"' : ''?>><?php echo lang('1 Kali/2 minggu') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup12_3" <?=$garden[$i]['PanenRayaPanenMonth'] == '1' ? 'checked="yes"' : ''?>><?php echo lang('1 Kali/bulan') ?></label></td>
												<td style="border-bottom:1px solid #000000;">
													<input disabled type="text" name="textfield33" id="textfield33" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['PanenRayaKg']?>">
												</td>
												<td style="border-bottom:1px solid #000000;">
													<input disabled type="text" name="textfield37" id="textfield37" size="5" style="border: 1px solid #000000;" value="<?=$garden[$i]['PanenRayaMonths'] * $garden[$i]['PanenRayaPanenMonth'] * $garden[$i]['PanenRayaKg']?>">
												</td>
											</tr>
											<tr>
												<td><?php echo lang('Total bulan') ?></td>
												<td>
													<input disabled type="text" name="textfield30" id="textfield30" size="5" style="border: 1px solid #000000;">
												</td>
												<td width="30%" class="fontred"><?php echo lang('NB: Jumlah harus 12 bulan') ?></td>
												<td colspan="2" align="right"><?php echo lang('Total produksi kakao [kg kering/tahun]') ?></td>
												<td>
													<input disabled type="text" name="textfield36" id="textfield36" size="5" style="border: 1px solid #000000;">
												</td>
											</tr>
										</table>
									</div>

									<div class="post_title"><strong><?php echo lang('Cara panen & Sanitasi Kakao') ?></strong></div>
									<div class="text_area">
										<table width="100%" cellspacing="1">
											<tr>
												<td width="35%"><?php echo lang('Cara Panen (pilihan ganda)') ?></td>
												<td width="65%">
													<label>
														<input disabled type="checkbox" name="CheckboxGroup2" value="checkbox" id="CheckboxGroup2_0" <?=$garden[$i]['HarvestAwal'] == '1' ? 'checked="yes"' : ''?>>
														<?php echo lang('Buah masak awal') ?></label>
													<label>
														<input disabled type="checkbox" name="CheckboxGroup2" value="checkbox" id="CheckboxGroup2_1" <?=$garden[$i]['HarvestMasak'] == '1' ? 'checked="yes"' : ''?>>
														<?php echo lang('Buah masak') ?></label>
													<label>
														<input disabled type="checkbox" name="CheckboxGroup2" value="checkbox" id="CheckboxGroup2_2" <?=$garden[$i]['HarvestHama'] == '1' ? 'checked="yes"' : ''?>>
														<?php echo lang('Buah terserang hama penyakit') ?></label>
												</td>
											</tr>
										</table>
										<table width="100%" cellspacing="1">
											<tr>
												<td>
													<fieldset style="padding:0;">
														<legend><?php echo lang('Apakah yang Anda lakukan pada kulit buah setelah pembelahan buah?') ?>
														</legend>
														<table width="100%" class="font12" cellpadding="0">
															<tr>
																<td width="27%"><label>
																		<input disabled type="radio" name="RadioGroup13" value="radio" id="RadioGroup13_0" <?=$garden[$i]['HowToCleanSkin'] == '1' ? 'checked="yes"' : ''?>><span class="font12">1) <?php echo lang('Ditumpuk di kebun Kakao') ?></span></label>
																</td>
																<td width="27%"><label>
																		<input disabled type="radio" name="RadioGroup13" value="radio" id="RadioGroup13_1" <?=$garden[$i]['HowToCleanSkin'] == '2' ? 'checked="yes"' : ''?>><span class="font12">2) <?php echo lang('Dikuburkan') ?></span></label>
																</td>
																<td width="24%"><label>
																		<input disabled type="radio" name="RadioGroup13" value="radio" id="RadioGroup13_2" <?=$garden[$i]['HowToCleanSkin'] == '3' ? 'checked="yes"' : ''?>><span class="font12">3) <?php echo lang('Ditumpuk diluar kebun') ?></span></label>
																</td>
																<td width="22%"><label>
																		<input disabled type="radio" name="RadioGroup13" value="radio" id="RadioGroup13_3" <?=$garden[$i]['HowToCleanSkin'] == '4' ? 'checked="yes"' : ''?>><span class="font12">4) <?php echo lang('Dibakar') ?></span></label>
																</td>
															</tr>
															<tr>
																<td><label>
																		<input disabled type="radio" name="RadioGroup13" value="radio" id="RadioGroup13_4" <?=$garden[$i]['HowToCleanSkin'] == '5' ? 'checked="yes"' : ''?>><span class="font12">5) <?php echo lang('Ditumpuk &amp; ditutup plastik') ?></span></label>
																</td>
																<td><label>
																		<input disabled type="radio" name="RadioGroup13" value="radio" id="RadioGroup13_5" <?=$garden[$i]['HowToCleanSkin'] == '6' ? 'checked="yes"' : ''?>><span class="font12">6) <?php echo lang('Ditumbuk jadi pakan ternak') ?></span></label>
																</td>
																<td><label>
																		<input disabled type="radio" name="RadioGroup13" value="radio" id="RadioGroup13_6" <?=$garden[$i]['HowToCleanSkin'] == '7' ? 'checked="yes"' : ''?>><span class="font12">7) <?php echo lang('Diolah Menjadi Kompos') ?></span></label>
																</td>
																<td><label>
																		<input disabled type="radio" name="RadioGroup13" value="radio" id="RadioGroup13_7" <?=$garden[$i]['HowToCleanSkin'] == '8' ? 'checked="yes"' : ''?>><span class="font12">8) <?php echo lang('Dibuang di sungai') ?></span></label>
																</td>
															</tr>
														</table>
													</fieldset>
												</td>
											</tr>
										</table>
									</div>

									<div class="post_title"><strong><?php echo lang('Data Pemangkasan Tanaman Kakao') ?></strong></div>
									<div class="text_area">
										<table width="100%" cellspacing="1">
											<tr>
												<td width="35%"><?php echo lang('Pemangkasan tanaman kakao') ?></td>
												<td width="20" class="box">
													<label>
														<input disabled type="radio" id="RadioGroup14_0" <?=$garden[$i]['PruningPlants'] == '1' ? 'checked="yes"' : ''?>>
														<?php echo lang('Ya') ?></label>
													<label>
														<input disabled type="radio" id="RadioGroup14_1" <?=$garden[$i]['PruningPlants'] == '2' ? 'checked="yes"' : ''?>>
														<?php echo lang('Tidak') ?></label>
												</td>
												<td colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td><?php echo lang('Tinggi pemangkasan tanaman kakao') ?></td>
												<td class="box"><?=$garden[$i]['HighPruning']?></td>
												<td width="25%" align="right"><?php echo lang('Frekuensi pemangkasan') ?></td>
												<td width="20%">
													<input disabled type="text" name="textfield15" id="textfield15" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrequentPruning']?>">
													<?php echo lang('kali/tahun') ?>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
				</div>
			</div>
					
			<div class="page-break"></div>
			<div class="page">
				<div id="templatemo_container">
					<div id="templatemo_header" class="logos" style="height:100px; margin-top:-15px;">
						<table width="100%" border="0" cellpadding="2">
							<tr>								
								<td width="20%" align="left" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
								</td>
								<?php 								
									for($i=0;$i<count($logos);$i++){
										if($logos[$i]['Photo']!=''){
								?>
									<td height="60px" width="20%" align="left" style="vertical-align:middle;">
										<img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
									</td>
								<?php
										}
									}
								?>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
							</tr>
						</table>
						<table width="100%" border="0" cellpadding="2" style="margin-top:-15px;">
							<tr>
								<td align="center" style="vertical-align:middle;text-decoration:underline;"><?php echo lang('P1 - Cocoa Farmer Basic Data') ?></td>
							</tr>
						</table>
					</div>
					<div id="templatemo_left_column" style="margin-top:-20px;">
						<div class="text_area" align="center">
							<div class="section_box2" align="center">
								<div class="post_title">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="280"><?php echo lang('Data Pemangkasan Pohon Pelindung') ?></td>
											<td width="170px"><?php echo lang('CPG') ?> - <?=$data['FarmerGroupID']?></td>
											<td width="170px"><?php echo lang('PK') ?> - <?=$data['FarmerID']?></td>
											<td align="right"><?php echo lang('Kebun Nr') ?> - <?=$garden[$i]['GardenNr']?></td>
										</tr>
									</table>
								</div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="35%"><?php echo lang('Pemangkasan pohon pelindung') ?></td>
											<td width="20%" class="box">
												<label>
													<input disabled type="radio" id="RadioGroup14_0" <?=$garden[$i]['PruningProtectPlants'] == '1' ? 'checked="yes"' : ''?> >
													<?php echo lang('Ya') ?></label>
												<label>
													<input disabled type="radio" id="RadioGroup14_1" <?=$garden[$i]['PruningProtectPlants'] == '2' ? 'checked="yes"' : ''?> >
													<?php echo lang('Tidak') ?></label>
											</td>
											<td width="25%" align="right"><?php echo lang('Frekuensi pemangkasan') ?></td>
											<td width="20%"><label for="textfield17"></label>
												<input disabled type="text" name="textfield16" id="textfield16" size="5" style="border: 1px solid #000000;background-color: #FFF">
												<?php echo lang('kali/tahun') ?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Data Pemupukan kompos dan organik') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="45%"><?php echo lang('Apakah anda memakai pupuk kompos dan/atau organik') ?></td>
											<td><?php echo lang('Jika Ya, isilah data berikut') ?></td>
											<td><?php echo lang('Kali/tahun') ?></td>
											<td><?php echo lang('Dosis/pohon/kali') ?></td>
											<td><?php echo lang('Unit') ?></td>
										</tr>
										<tr>
											<td class="box"><label>
													<input disabled type="radio" name="PakaiKompos1" value="radio" id="RadioGroup14_0" <?=$garden[$i]['PakaiKompos'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
													<?php echo lang('Ya') ?></label>
												<label>
													<input disabled type="radio" name="PakaiKompos2" value="radio" id="RadioGroup14_1" <?=$garden[$i]['PakaiKompos'] == '2' ? 'checked="yes"' : ''?> disabled="true"/>
													<?php echo lang('Tidak') ?></label></td>
											<td><?php echo lang('Kompos') ?></td>
											<td>
												<input disabled type="text" name="textfield18" id="textfield18" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrequentFertilizationKompos']?>">
											</td>
											<td>
												<input disabled type="text" name="textfield18" id="textfield18" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoseFertilizerKompos']?>">
											</td>
											<td>Kg</td>
										</tr>
										<tr>
											<td><?php echo lang('Pohon mana yang dipupuk Kompos dan/atau organik') ?></td>
											<td><?php echo lang('Pupuk Kandang') ?></td>
											<td>
												<input disabled type="text" name="textfield18" id="textfield18" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrKomposKandang']?>">
											</td>
											<td>
												<input disabled type="text" name="textfield18" id="textfield18" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoseKomposKandang']?>">
											</td>
											<td>Kg</td>
										</tr>
										<tr>
											<td class="box">
												<label>
													<input disabled type="checkbox" name="KomposTBM" value="checkbox" id="CheckboxGroup3_0" <?=$garden[$i]['KomposTBM'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
													TBM</label>
												<label>
													<input disabled type="checkbox" name="KomposTM" value="checkbox" id="CheckboxGroup3_1" <?=$garden[$i]['KomposTM'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
													TM</label>
												<label>
													<input disabled type="checkbox" name="KomposTR" value="checkbox" id="CheckboxGroup3_2" <?=$garden[$i]['KomposTR'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
													TR</label></td>
											<td><?php echo lang('Pupuk Cair') ?></td>
											<td>
												<input disabled type="text" name="textfield18" id="textfield18" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrKomposCair']?>">
											</td>
											<td>
												<input disabled type="text" name="textfield18" id="textfield18" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoseKomposCair']?>">
											</td>
											<td><?php echo lang('liter') ?></td>
										</tr>
										<tr>
											<td></td>
											<td><?php echo lang('Pupuk Granula/Padat') ?></td>
											<td>
												<input disabled type="text" name="textfield18" id="textfield18" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrKomposGranula']?>">
											</td>
											<td>
												<input disabled type="text" name="textfield18" id="textfield18" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoseKomposGranula']?>">
											</td>
											<td>gram</td>
										</tr>
										<tr>
											<td colspan="5">
												<?php echo lang('Keterangan : Nilai maksimum hasil untuk Kompos dan Pupuk Kandang adalah tidak boleh lebih dari 10 kg/pohon/tahun. Sedangkan untuk Pupuk Cair dan Pupuk Granula/Padat, tidak memiliki nilai batasan.') ?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Data Pemupukan Tidak Organik/Anorganik/kimia') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td colspan="7"><?php echo lang('Apakah Anda memakai pupuk non organik/kimia?') ?>
												<input disabled type="radio" <?=$garden[$i]['TidakMemakaiKimia'] == '1' ? 'checked="yes"' : ''?>>
												<?php echo lang('Ya') ?>
												<input disabled type="radio" <?=$garden[$i]['TidakMemakaiKimia'] == '2' ? 'checked="yes"' : ''?>>
												<?php echo lang('Tidak') ?>
											</td>
											<td rowspan="5" align="left" style="border-left:1px solid #000000;"><?php echo lang('Jika tidak pakai pupuk yang tidak organic, kenapa?') ?><br>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup4" value="checkbox" id="CheckboxGroup4_0" <?=$garden[$i]['KimiaDana'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Tidak ada dana') ?></label><br>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup5" value="checkbox" id="CheckboxGroup5_0" <?=$garden[$i]['KimiaSupplier'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Tidak menemukan supplier') ?></label><br>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup6" value="checkbox" id="CheckboxGroup6_0" <?=$garden[$i]['KimiaDilatih'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Belum dilatih') ?></label><br>
												<label>
													<input disabled type="checkbox" name="KimiaDilatih" value="checkbox" id="CheckboxGroup6_0" <?=$garden[$i]['KimiaTidakSuka'] == '1' ? 'checked="yes"' : ''?>/>
													<?php echo lang('Tidak suka menggunakan pupuk kimia') ?></label><br>
												<label>
													<input disabled type="checkbox" name="KimiaDilatih" value="checkbox" id="CheckboxGroup6_0" <?=$garden[$i]['KimiaTidakTersedia'] == '1' ? 'checked="yes"' : ''?>/>
													<?php echo lang('Pupuk tidak tersedia') ?></label><br>
												<label>
													<input disabled type="checkbox" name="KimiaDilatih" value="checkbox" id="CheckboxGroup6_0" <?=$garden[$i]['KimiaLain'] == '1' ? 'checked="yes"' : ''?>/>
													<?php echo lang('Lain-lain') ?></label>
											</td>
										</tr>

										<tr>
											<td></td>
											<td width="5%" align="center">Urea</td>
											<td width="5%" align="center">ZA</td>
											<td width="5%" align="center">TSP</td>
											<td width="5%" align="center">NPK</td>
											<td width="5%" align="center">KCL</td>
											<td width="5%" align="center">Foliar</td>
										</tr>
										<tr>
											<td><?php echo lang('Frekuensi pemupukan [Kali/tahun]') ?></td>
											<td align="center">
												<input disabled type="text" name="textfield20" id="textfield20" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrUrea']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield38" id="textfield38" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrZa']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield39" id="textfield39" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrTsp']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield40" id="textfield40" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrNpk']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield40" id="textfield40" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrKcl']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield40" id="textfield40" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrFoliar']?>">
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Dosis pemupukan [Gram/pohon/kali]') ?></td>
											<td align="center">
												<input disabled type="text" name="textfield41" id="textfield41" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoUrea']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield42" id="textfield42" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoZa']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield43" id="textfield43" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoTsp']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield44" id="textfield44" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoNpk']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield40" id="textfield40" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoKcl']?>">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield40" id="textfield40" size="4" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoFoliar']?>">
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Total pemupukan [Gram/pohon/tahun]') ?></td>
											<td align="center">
												<input disabled type="text" name="textfield45" id="textfield45" size="4" style="border: 1px solid #000000;">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield46" id="textfield46" size="4" style="border: 1px solid #000000;">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield47" id="textfield47" size="4" style="border: 1px solid #000000;">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield48" id="textfield48" size="4" style="border: 1px solid #000000;">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield40" id="textfield40" size="4" style="border: 1px solid #000000;">
											</td>
											<td align="center">
												<input disabled type="text" name="textfield40" id="textfield40" size="4" style="border: 1px solid #000000;">
											</td>
										</tr>
										<tr>
											<td colspan="4" style="border-top:1px solid #000000;"><?php echo lang('Pohon mana yang dipupuk Tidak-Organik/Anorganik/Kimia') ?>
											</td>
											<td colspan="4" style="border-top:1px solid #000000;">
												<label>
													<input disabled type="checkbox" name="CheckboxGroup7" value="checkbox" id="CheckboxGroup7_0" <?=$garden[$i]['PupukTBM'] == '1' ? 'checked="yes"' : ''?>>
													TBM</label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup7" value="checkbox" id="CheckboxGroup7_1" <?=$garden[$i]['PupukTM'] == '1' ? 'checked="yes"' : ''?>>
													TM</label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup7" value="checkbox" id="CheckboxGroup7_2" <?=$garden[$i]['PupukTR'] == '1' ? 'checked="yes"' : ''?>>
													TR</label>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Data Hama dan penyakit Utama yang menyerang Tanaman Kakao') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="22%" style="border-bottom:1px solid #000000;">Hama Utama kakao</td>
											<td width="78%" style="border-bottom:1px solid #000000;"><label>
													<input disabled type="checkbox" name="CheckboxGroup8" value="checkbox" id="CheckboxGroup8_0" <?=$garden[$i]['HamaBPK'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Penggerek Buah Kakao') ?></label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup8" value="checkbox" id="CheckboxGroup8_1" <?=$garden[$i]['HamaHelopeltis'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Helopeltis') ?></label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup8" value="checkbox" id="CheckboxGroup8_2" <?=$garden[$i]['HamaBatang'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Penggerek Batang atau Ranting') ?></label>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Penyakit Utama kakao') ?></td>
											<td>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup9" value="checkbox" id="CheckboxGroup9_0" <?=$garden[$i]['PenyakitKanker'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Kanker Batang') ?></label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup9" value="checkbox" id="CheckboxGroup9_1" <?=$garden[$i]['PenyakitBusuk'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Busuk Buah') ?></label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup9" value="checkbox" id="CheckboxGroup9_2" <?=$garden[$i]['PenyakitUpas'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Jamur Upas') ?></label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup9" value="checkbox" id="CheckboxGroup9_3" <?=$garden[$i]['PenyakitAkar'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Jamur Akar') ?></label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup9" value="checkbox" id="CheckboxGroup9_4" <?=$garden[$i]['PenyakitVSD'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('VSD') ?></label>
												<label>
													<input disabled type="checkbox" name="CheckboxGroup9" value="checkbox" id="CheckboxGroup9_5" <?=$garden[$i]['PenyakitAntraknose'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Antraknose') ?></label>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Data pemakaian Pestisida') ?></strong>
									<span class="fontred">NB: 1 liter = 1 kg = 1.000 ml = 1.000 gram</span></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="22%"></td>
											<td align="center" width="25%"><?php echo lang('Herbisida') ?></td>
											<td align="center" width="26%"><?php echo lang('Insektisida') ?></td>
											<td align="center" width="27%"><?php echo lang('Fungisida') ?></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
											<td align="center" class="box"><label>
													<input disabled type="radio" id="RadioGroup14_0" <?=$garden[$i]['Herbisida'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Ya') ?></label>
												<label>
													<input disabled type="radio" id="RadioGroup14_1" <?=$garden[$i]['Herbisida'] == '2' ? 'checked="yes"' : ''?>>
													<?php echo lang('Tidak') ?></label></td>
											<td align="center" class="box"><label>
													<input disabled type="radio" id="RadioGroup14_0" <?=$garden[$i]['Insectisida'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Ya') ?></label>
												<label>
													<input disabled type="radio" id="RadioGroup14_1" <?=$garden[$i]['Insectisida'] == '2' ? 'checked="yes"' : ''?>>
													<?php echo lang('Tidak') ?></label></td>
											<td align="center" class="box"><label>
													<input disabled type="radio" id="RadioGroup14_0" <?=$garden[$i]['Fungisida'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Ya') ?></label>
												<label>
													<input disabled type="radio" id="RadioGroup14_1" <?=$garden[$i]['Fungisida'] == '2' ? 'checked="yes"' : ''?>>
													<?php echo lang('Tidak') ?></label></td>
										</tr>
										<tr>
											<td><?php echo lang('Frekuensi Penggunaan') ?></td>
											<td align="left" style="vertical-align:bottom;">
												<input disabled type="text" name="textfield49" id="textfield49" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrequentHerbisida']?>">
												<?php echo lang('kali/tahun') ?>
											</td>
											<td align="left">
												<input disabled type="text" name="textfield51" id="textfield51" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrequentInsectisida']?>">
												<span style="vertical-align:bottom;"><?php echo lang('kali/tahun') ?></span></td>
											<td align="left"><span style="vertical-align:bottom;">
												<input disabled type="text" name="textfield55" id="textfield55" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['FrequentFungisida']?>"><?php echo lang('kali/tahun') ?></span>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Dosis pestisida') ?></td>
											<td align="left">
												<input disabled type="text" name="textfield50" id="textfield50" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoseHerbisida']?>">
												<?php echo lang('ml/kali/kebun') ?>
											</td>
											<td align="left">
												<input disabled type="text" name="textfield52" id="textfield52" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoseInsectisida']?>">
												<?php echo lang('ml/kali/kebun') ?>
											</td>
											<td align="left">
												<input disabled type="text" name="textfield54" id="textfield54" size="5" style="border: 1px solid #000000;background-color: #FFF" value="<?=$garden[$i]['DoseFungisida']?>">
												<?php echo lang('ml/kali/kebun') ?>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Merek pestisida') ?><br> (<?php echo lang('pilihanganda') ?>)
											</td align="center">
											<td align="center" class="box">
												<table width="100%" align="center" cellpadding="0">
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_0" <?=$garden[$i]['Herbisida1'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Round Up') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_1" <?=$garden[$i]['Herbisida2'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Basmilang') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_2" <?=$garden[$i]['Herbisida3'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Pilar Up') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_3" <?=$garden[$i]['Herbisida4'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Sun Up') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_4" <?=$garden[$i]['Herbisida5'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Gramoxone') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_5" <?=$garden[$i]['Herbisida6'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Supremo') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_6" <?=$garden[$i]['Herbisida7'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Sapurata') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_7" <?=$garden[$i]['Herbisida8'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Rambo') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_8" <?=$garden[$i]['Herbisida9'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Para Special') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_9" <?=$garden[$i]['Herbisida10'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Noxone') ?></label></td>
													</tr>
												</table>
											</td>
											<td align="center" class="box">
												<table width="100%" align="center" cellpadding="0">
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_0" <?=$garden[$i]['Insectisida1'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Alika') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_1" <?=$garden[$i]['Insectisida2'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Matador') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_2" <?=$garden[$i]['Insectisida3'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Capture') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_3" <?=$garden[$i]['Insectisida4'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Bento') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_4" <?=$garden[$i]['Insectisida5'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Regent') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_5" <?=$garden[$i]['Insectisida6'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Drusban') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_6" <?=$garden[$i]['Insectisida7'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Penalti') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_7" <?=$garden[$i]['Insectisida8'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Nurelle') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_8" <?=$garden[$i]['Insectisida9'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Cloromit') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_9" <?=$garden[$i]['Insectisida10'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Decis') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup11" value="checkbox" id="CheckboxGroup10_10" <?=$garden[$i]['Insectisida11'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Organik') ?></label></td>
														<td colspan="2"></td>
													</tr>
												</table>
											</td>
											<td align="center" class="box">
												<table width="100%" align="center" cellpadding="0">
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_0" <?=$garden[$i]['Fungisida1'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Nordox') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_1" <?=$garden[$i]['Fungisida2'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Dithane') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_2" <?=$garden[$i]['Fungisida3'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Amistartop') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_3" <?=$garden[$i]['Fungisida4'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Scorpio') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_4" <?=$garden[$i]['Fungisida5'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Rhidomil') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_5" <?=$garden[$i]['Fungisida6'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Antila') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_6" <?=$garden[$i]['Fungisida7'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Antracol') ?></label></td>
														<!--<tr>
					   <td><label>
					   <input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_7">
					   Capture</label></td>
					   </tr>-->
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_8" <?=$garden[$i]['Fungisida9'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Polidor') ?></label></td>
													</tr>
													<tr>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup10" value="checkbox" id="CheckboxGroup10_9" <?=$garden[$i]['Fungisida10'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Cozeb') ?></label></td>
														<td><label>
																<input disabled type="checkbox" name="CheckboxGroup11" value="checkbox" id="CheckboxGroup10_10" <?=$garden[$i]['Fungisida11'] == '1' ? 'checked="yes"' : ''?>>
																<?php echo lang('Organik') ?></label></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td><?php echo lang('Merek pestisida lain-lain') ?></td>
											<td align="center" class="box">&nbsp;<?=$garden[$i]['MerekHerbisida']?></td>
											<td align="center" class="box">&nbsp;<?=$garden[$i]['MerekInsectisida']?></td>
											<td align="center" class="box">&nbsp;<?=$garden[$i]['MerekFungisida']?></td>
										</tr>
										<tr>
											<td colspan="2"><?php echo lang('Menggunakan pakaian perlindungan diri (PPD)') ?></td>
											<td colspan="2" class="box">
												<label>
													<input disabled type="radio" name="RadioGroup17" value="radio" id="RadioGroup17_0" <?=$garden[$i]['APD'] == '1' ? 'checked="yes"' : ''?> >
													<?php echo lang('Ya') ?></label>
												<label>
													<input disabled type="radio" name="RadioGroup17" value="radio" id="RadioGroup17_1" <?=$garden[$i]['APD'] == '2' ? 'checked="yes"' : ''?> >
													<?php echo lang('Tidak') ?></label>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="text_area">
									<table width="100%">
										<tr>
											<td width="50%" colspan="2">
												<fieldset>
													<legend><?php echo lang('Dimana anda menyimpan pestisida sebelum dan selama pemakaian') ?>
													</legend>
													<p style="margin-top:-3px;margin-bottom:-3px;">
														<label>
															<input disabled type="radio" name="RadioGroup15" value="radio" id="RadioGroup15_0" <?=$garden[$i]['TempatSimpanPestisida'] == '1' ? 'checked="yes"' : ''?>>
															1) <?php echo lang('Di dalam rumah') ?></label>
														<br>
														<label>
															<input disabled type="radio" name="RadioGroup15" value="radio" id="RadioGroup15_1" <?=$garden[$i]['TempatSimpanPestisida'] == '2' ? 'checked="yes"' : ''?>>
															2) <?php echo lang('Tempat khusus pestisida') ?></label>
														<br>
														<label>
															<input disabled type="radio" name="RadioGroup15" value="radio" id="RadioGroup15_2" <?=$garden[$i]['TempatSimpanPestisida'] == '3' ? 'checked="yes"' : ''?>>
															3) <?php echo lang('Di luar rumah (kawasan rumah)') ?></label>
														<br>
														<label>
															<input disabled type="radio" name="RadioGroup15" value="radio" id="RadioGroup15_3" <?=$garden[$i]['TempatSimpanPestisida'] == '4' ? 'checked="yes"' : ''?>>
															4) <?php echo lang('Di rumah kebun') ?></label>
														<br>
														<label>
															<input disabled type="radio" name="RadioGroup15" value="radio" id="RadioGroup15_4" <?=$garden[$i]['TempatSimpanPestisida'] == '5' ? 'checked="yes"' : ''?>>
															5) <?php echo lang('Lain-Lain') ?></label>
														<br>
													</p>
												</fieldset>
											</td>
											<td width="50%" colspan="2">
												<fieldset>
													<legend><?php echo lang('Apa yang anda lakukan dengan kemasan pestisida setelah pemakaian') ?>
													</legend>
													<p style="margin-top:-3px;margin-bottom:-3px;">
														<label>
															<input disabled type="radio" name="RadioGroup16" value="radio" id="RadioGroup16_0" <?=$garden[$i]['BuangKemasanPestisida'] == '1' ? 'checked="yes"' : ''?>>
															1) <?php echo lang('Di buang sembarangan (di kebun / di sekitar rumah)') ?></label>
														<br>
														<label>
															<input disabled type="radio" name="RadioGroup16" value="radio" id="RadioGroup16_1" <?=$garden[$i]['BuangKemasanPestisida'] == '2' ? 'checked="yes"' : ''?>>2) <?php echo lang('Digunakan untuk menyimpan sesuatu') ?></label>
														<br>
														<label>
															<input disabled type="radio" name="RadioGroup16" value="radio" id="RadioGroup16_2" <?=$garden[$i]['BuangKemasanPestisida'] == '3' ? 'checked="yes"' : ''?>>3) <?php echo lang('Dicuci dengan bersih dan dikubur') ?></label>
														<br>
														<label>
															<input disabled type="radio" name="RadioGroup16" value="radio" id="RadioGroup16_3" <?=$garden[$i]['BuangKemasanPestisida'] == '4' ? 'checked="yes"' : ''?>>4) <?php echo lang('Dibakar') ?></label>
														<br>
														<label>
															<input disabled type="radio" name="RadioGroup16" value="radio" id="RadioGroup16_4" <?=$garden[$i]['BuangKemasanPestisida'] == '5' ? 'checked="yes"' : ''?>>5) <?php echo lang('Lain-Lain') ?></label>
														<br>
													</p>
												</fieldset>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Comment tambahan tentang Kebun Kakao') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr height="25">
											<td>&nbsp;</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="page-break"></div>
			<div class="page">
				<div id="templatemo_container">
					<? } ?>

					<div id="templatemo_header" class="logos" style="height:100px; margin-top:-15px;">
						<table width="100%" border="0" cellpadding="2">
							<tr>								
								<td width="20%" align="left" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
								</td>
								<?php 								
									for($i=0;$i<count($logos);$i++){
										if($logos[$i]['Photo']!=''){
								?>
									<td height="60px" width="20%" align="left" style="vertical-align:middle;">
										<img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
									</td>
								<?php
										}
									}
								?>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
							</tr>
						</table>
						<table width="100%" border="0" cellpadding="2" style="margin-top:-15px;">
							<tr>
								<td align="center" style="vertical-align:middle;text-decoration:underline;"><?php echo lang('P1 - Cocoa Farmer Basic Data') ?></td>
							</tr>
						</table>
					</div>
					<div id="templatemo_left_column" style="margin-top:-20px;">
						<div class="text_area" align="center">
							<div class="section_box2" align="center">
								<div class="post_title_main"><strong>
										<table width="100%" cellspacing="1">
											<tr>
												<td width="40%"><?php echo lang('P1 - Penerapan Pasca Panen') ?></td>
												<td width="30%" class="fontred"><?php echo lang('CPG') ?> - <?=$data['CPGid']?></td>
												<td width="30%" align="right" class="fontred">PK - <?=$data['FarmerID']?></td>
											</tr>
										</table>
									</strong>
								</div>
								<div class="text_area">
									<table width="100%" cellspacing="0">
										<tr>
											<td width="45%"><?php echo lang('Tanggal wawancara') ?> (mis: 3-Mei-2012)</td>
											<td width="30%" class="box">&nbsp;</td>
											<td width="10%" align="right"><?php echo lang('Survey Nr') ?>.</td>
											<td width="5%" class="box">&nbsp;<?=$SurveyNr?></td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="70%"><?php echo lang('Apakah sudah ada produksi') ?></td>
											<td colspan="2" class="box">
												<label>
													<input disabled type="radio" value="radio" id="RadioGroup71_0" <?=$harvest['AdaProduksi'] == '1' ? 'checked="yes"' : ''?>>
													<?php echo lang('Ada') ?></label>
												<label>
													<input disabled type="radio" value="radio" id="RadioGroup71_1" <?=$harvest['AdaProduksi'] == '2' ? 'checked="yes"' : ''?>>
													<?php echo lang('Tidak') ?></label></td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Fermentasi') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="70%"><?php echo lang('Apakah anda melakukan fermentasi biji kakao sebelum menjemur (fermentasi min. 4 hari)') ?></td>
											<td colspan="2" class="box">
												<label>
													<input disabled type="radio" id="RadioGroup7_0">
													<?php echo lang('Ya') ?></label>
												<label>
													<input disabled type="radio" id="RadioGroup7_1">
													<?php echo lang('Tidak') ?></label></td>
										</tr>
										<tr>
											<td width="70%" style="vertical-align:top;"><?php echo lang('Jika Ya, berapa hari fermentasi biji dilakukan') ?>
											</td>
											<td width="25%" class="box">&nbsp;</td>
											<td width="5%">
												<?php echo lang('hari') ?>
											</td>
										</tr>
										<tr>
											<td colspan="3">
												<fieldset style="padding:0;">
													<legend><?php echo lang('Jika tidak mengapa') ?></legend>
													<table width="100%">
														<tr>
															<td>
																<table width="100%" cellpadding="0">
																	<tr>
																		<td width="33%"><label>
																				<input disabled type="radio" name="RadioGroup8" value="radio" id="RadioGroup8_0" <?=$harvest['NoFermentation'] == '1' ? 'checked="yes"' : ''?>>
																				1) <?php echo lang('Tidak punya cukup waktu') ?></label></td>
																		<td width="34%"><label>
																				<input disabled type="radio" name="RadioGroup8" value="radio" id="RadioGroup8_1" <?=$harvest['NoFermentation'] == '2' ? 'checked="yes"' : ''?>>
																				2) <?php echo lang('Tidak punya alat') ?></label></td>
																		<td width="33%"><label>
																				<input disabled type="radio" name="RadioGroup8" value="radio" id="RadioGroup8_2" <?=$harvest['NoFermentation'] == '3' ? 'checked="yes"' : ''?>>
																				3) <?php echo lang('Tidak tahu caranya') ?></label></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td>
																<table width="100%" cellpadding="0">
																	<tr>
																		<td width="33%"><label>
																				<input disabled type="radio" name="RadioGroup8" value="radio" id="RadioGroup8_0" <?=$harvest['NoFermentation'] == '4' ? 'checked="yes"' : ''?>>
																				4) <?php echo lang('Tidak Menguntungkan') ?></label></td>
																		<td width="34%"><label>
																				<input disabled type="radio" name="RadioGroup8" value="radio" id="RadioGroup8_1" <?=$harvest['NoFermentation'] == '5' ? 'checked="yes"' : ''?>>
																				5) <?php echo lang('Malas') ?></label></td>
																		<td width="33%"><label>
																				<input disabled type="radio" name="RadioGroup8" value="radio" id="RadioGroup8_2" <?=$harvest['NoFermentation'] == '6' ? 'checked="yes"' : ''?>>
																				6) <?php echo lang('Lain lain') ?></label></td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Penjemuran') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="69%"><?php echo lang('Apakah anda menjemur biji kakao sebelum menjualnya') ?></td>
											<td width="31%" class="box"><label>
													<input disabled type="radio" id="RadioGroup7_0"><?php echo lang('Ya') ?> </label>
												<label>
													<input disabled type="radio" id="RadioGroup7_1">
													<?php echo lang('Tidak/jual basah') ?></label></td>
										</tr>
										<tr>
											<td colspan="2">
												<fieldset style="padding:0;">
													<legend><?php echo lang('Jika ya') ?>...</legend>
													<table width="100%" cellpadding="0">
														<tr>
															<td width="70%"><?php echo lang('Berapa hari anda mengeringkan biji kakao') ?></td>
															<td width="25%" class="box">&nbsp;</td>
															<td width="5%">hari</td>
														</tr>
														<tr>
															<td><?php echo lang('Pengeringan pada lantai penjemuran') ?></td>
															<td colspan="2" class="box"><label>
																	<input disabled type="radio" id="RadioGroup7_0">
																	<?php echo lang('Ya') ?></label>
																<label>
																	<input disabled type="radio" id="RadioGroup7_1">
																	<?php echo lang('Tidak') ?></label></td>
														</tr>
														<tr>
															<td><?php echo lang('Pengeringan di atas aspal') ?></td>
															<td colspan="2" class="box"><label>
																	<input disabled type="radio" id="RadioGroup7_0">
																	<?php echo lang('Ya') ?></label>
																<label>
																	<input disabled type="radio" id="RadioGroup7_1">
																	<?php echo lang('Tidak') ?></label></td>
														</tr>
														<tr>
															<td><?php echo lang('Pengeringan dengan alat (solar dryer, blower, para-para, dll)') ?></td>
															<td colspan="2" class="box"><label>
																	<input disabled type="radio" id="RadioGroup7_0">
																	<?php echo lang('Ya') ?></label>
																<label>
																	<input disabled type="radio" id="RadioGroup7_1">
																	<?php echo lang('Tidak') ?></label></td>
														</tr>
														<tr>
															<td><?php echo lang('Pengeringan dengan menggunakan alas (terpal, plastik,anyaman daun kelapa)') ?>
															</td>
															<td colspan="2" class="box"><label>
																	<input disabled type="radio" id="RadioGroup7_0">
																	<?php echo lang('Ya') ?></label>
																<label>
																	<input disabled type="radio" id="RadioGroup7_1">
																	<?php echo lang('Tidak') ?></label></td>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<fieldset style="padding:0;">
													<legend><?php echo lang('Jika tidak, mengapa anda tidak menjemur biji kakao') ?></legend>
													<table width="100%" cellpadding="0">
														<tr>
															<td width="60%"><label>
																	<input disabled type="radio" id="RadioGroup9_0">
																	1) <?php echo lang('Lebih menguntungkan menjual biji basah') ?></label></td>
															<td width="40%"><label>
																	<input disabled type="radio" id="RadioGroup9_1">
																	2) <?php echo lang('Lebih mudah dikerjakan') ?></label></td>
														</tr>
														<tr>
															<td><label>
																	<input disabled type="radio" id="RadioGroup9_2">
																	3) <?php echo lang('Lebih cepat memperoleh uang') ?></label></td>
															<td class="font13"><label>
																	<input disabled type="radio" id="RadioGroup9_3">
																	4) <?php echo lang('Sulit menjemur karena musim hujan') ?></label></td>
														</tr>
														<tr>
															<td><label>
																	<input disabled type="radio" id="RadioGroup9_4">
																	5) <?php echo lang('Tidak cukup waktu dan perlu bantuan tenaga kerja') ?></label></td>
															<td><label>
																	<input disabled type="radio" id="RadioGroup9_5">
																	6) <?php echo lang('Lain lain') ?></label></td>
														</tr>
													</table>
												</fieldset>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Sortasi dan Penjualan') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr valign="bottom">
											<td width="40%">
												<table width="100%" cellspacing="1">
													<tr valign="bottom">
														<td width="80%"><?php echo lang('Memisahkan biji berkualitas bagus dan biji berkualitas jelek atau rendah sebelum menjualnya') ?>
														</td>
														<td width="20%" class="box">
															<label><input disabled type="radio" id="RadioGroup7_0">

																<?php echo lang('Ya') ?></label>
															<label>
																<input disabled type="radio" id="RadioGroup7_1">
																<?php echo lang('Tidak') ?></label>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<fieldset style="padding:0;">
																<legend><?php echo lang('Jika tidak, mengapa anda tidak melakukan pemisahan biji') ?>
																</legend>
																<table width="100%" cellpadding="0">
																	<tr>
																		<td width="50%"><label>
																				<input disabled type="radio" id="RadioGroup10_0">
																				1) <?php echo lang('Tidak ada perbedaan harga') ?></label></td>
																		<td width="50%"><label>
																				<input disabled type="radio" id="RadioGroup10_1">
																				2) <?php echo lang('Terlalu banyak menghabiskan waktu') ?></label></td>
																	</tr>
																	<tr>
																		<td><label>
																				<input disabled type="radio" id="RadioGroup10_2">>
																				3) <?php echo lang('Tidak banyak biji berkualitas bagus') ?></label></td>
																		<td><label>
																				<input disabled type="radio" id="RadioGroup10_3">
																				4) <?php echo lang('Tidak tahu cara memisahkan biji') ?></label></td>
																	</tr>
																</table>
															</fieldset>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<fieldset style="padding:0;">
																<legend><?php echo lang('Penjualan biji kakao kepada') ?>
																</legend>
																<table width="100%" cellpadding="0">
																	<tr>
																		<td width="50%"><label>
																				<input disabled type="radio" id="RadioGroup10_0">
																				1) <?php echo lang('Pedagang pengumpul di Kampung') ?></label></td>
																		<td width="50%"><label>
																				<input disabled type="radio" id="RadioGroup10_1">
																				2) <?php echo lang('Pedagang pengumpul Kecamatan') ?></label></td>
																	</tr>
																	<tr>
																		<td><label>
																				<input disabled type="radio" id="RadioGroup10_2">
																				3) <?php echo lang('Pedagang Kabupaten/Eksportir') ?></label></td>
																		<td><label>
																				<input disabled type="radio" id="RadioGroup10_3">
																				4) <?php echo lang('Kelompok Petani') ?></label></td>
																	</tr>
																	<tr>
																		<td colspan="2"><label><?php echo lang('Apakah Anda mengantar kakao sendiri') ?>?
																				<input disabled type="radio"> <?php echo lang('Ya') ?>
																				<input disabled type="radio"> <?php echo lang('Tidak') ?><br>
																				<?php echo lang('Jika Ya, berapa jarak dari rumah Anda (m)') ?>
																				<input disabled name="Jarak"><label></td>
																	</tr>
																</table>
															</fieldset>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Tenaga Kerja') ?></strong></div>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr valign="bottom">
											<td width="80%"><?php echo lang('Jumlah anggota keluarga berusia lebih dari 15 tahun yang bekerja di kebun') ?>
											</td>
											<td width="15%" class="box">&nbsp;</td>
											<td width="5%"><?php echo lang('orang') ?></td>
										</tr>
										<tr>
											<td><?php echo lang('Pekerja yang dibayar secara musiman/paruh waktu (per tahun)') ?></td>
											<td class="box">&nbsp;</td>
											<td><?php echo lang('orang') ?></td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Jika dibayar, berapa jumlah bayarannya (Rp)') ?></td>
											<td class="box">&nbsp;</td>
											<td></td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Jika bagi hasil, berapa prosentasenya (%)') ?></td>
											<td class="box">&nbsp;</td>
											<td></td>
										</tr>
										<tr>
											<td><?php echo lang('Pekerja dibayar penuh (per tahun)') ?></td>
											<td class="box">&nbsp;</td>
											<td>
												<?php echo lang('orang') ?>
											</td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Jika dibayar, berapa jumlah bayarannya (Rp)') ?></td>
											<td class="box">&nbsp;</td>
											<td></td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Jika bagi hasil, berapa prosentasenya (%)') ?></td>
											<td class="box">&nbsp;</td>
											<td></td>
										</tr>
									</table>
								</div>
							</div>
							<div class="section_box2" align="center">
								<div class="post_title"><strong><?php echo lang('Komentar') ?></strong></div>
								<div class="text_area"><textarea cols="97"></textarea></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="page-break"></div>
			<div class="page">
				<div id="templatemo_container">
					<!--   <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>-->

					<div id="templatemo_header" class="logos" style="height:100px; margin-top:-15px;">
						<table width="100%" border="0" cellpadding="2">
							<tr>								
								<td width="20%" align="left" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
								</td>
								<?php 								
									for($i=0;$i<count($logos);$i++){
										if($logos[$i]['Photo']!=''){
								?>
									<td height="60px" width="20%" align="left" style="vertical-align:middle;">
										<img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
									</td>
								<?php
										}
									}
								?>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
								<td width="20%" align="right" style="vertical-align:middle;">
									<img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
							</tr>
						</table>
						<table width="100%" border="0" cellpadding="2" style="margin-top:-15px;">
							<tr>
								<td align="center" style="vertical-align:middle;text-decoration:underline;"><?php echo lang('P1 - Cocoa Farmer Basic Data') ?></td>
							</tr>
						</table>
					</div>
					<div id="templatemo_left_column" style="margin-top:-20px;">
						<div class="text_area" align="center">
							<div class="section_box2" align="center">
								<div class="post_title">
									<table width="100%" cellspacing="1">
										<tr>
											<td width="280"><?php echo lang('Sertifikasi') ?></td>
											<td width="170px"><?php echo lang('CPG') ?> - <?=$data['FarmerGroupID']?></td>
											<td width="170px"><?php echo lang('PK') ?> - <?=$data['FarmerID']?></td>
											<td align="right"><?php echo lang('Kebun Nr') ?> - <?=$garden[0]['GardenNr']?></td>
										</tr>
									</table>
								</div>
								<?php
								if ($partnerid == 9 AND FALSE) {
									?>
									<div class="section_box2" align="center">
										<div class="post_title"><strong><?php echo lang('Pemenuhan Standar Sertifikasi') ?></strong></div>
										<div class="text_area">
											<table width="100%">
												<tr>
													<td width="83%"><?php echo lang('Apakah anda menyetujui untuk mengkuti program sertifikasi secara sukarela') ?>
													</td>
													<td width="16%" class="box">
														<input disabled type="radio" value="radio" id="RadioGroup11_9">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup11_9">
														<?php echo lang('Tidak') ?>
													</td>
													<td width="1%">&nbsp;</td>
												</tr>
												<tr>
													<td width="83%"><?php echo lang('Anda mencegah pencampuran biji kakao sertifikasi dengan non sertifikasi dengan mengerti sistem penelusuran dan penjualan biji kakao sertifikasi beserta persyaratannya dan menjalankannya') ?>
													</td>
													<td width="16%" class="box">
														<input disabled type="radio" value="radio" id="RadioGroup1_9">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_9">
														<?php echo lang('Tidak') ?>
													</td>
													<td width="1%">&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Anda telah berupaya mengurangi konsumsi energi tidak terbarukan minimal dengan mengurangi konsumsi listrik (menggunakan seperlunya) dirumah, mengurangi penggunaan') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_10">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_10">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Anda menjaga ekosistem alam (air dan tanah) khususnya yang ada disekitar kebun dengan minimal tidak menebang pohon, tidak membuang sampah plastik di kebun, badan air sekitar kebun') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_11">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_11">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Kebun anda sebelumnya bukan merupakan hutan yang dibuka menjadi kebun setelah 1 November 2005') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_12">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_12">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Anda melarang perburuan, penangkapan, penangkaran dan penjualan satwa liar di kebun dengan minimal memasang tanda-tanda larangan untuk hal tersebut') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_13">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_13">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Anda tidak membuang limbah/sampah organik maupun non-organik kedalam badan air dengan minimal memasang tanda pelarangan untuk hal tersebut') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_14">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_14">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Jika Anda memperkerjakan orang, Anda tidak membeda-bedakan orang/pekerja berdasarkan suku, ras, agama, warna kulit, status sosial') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_15">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_15">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Jika Anda memperkerjakan orang, Anda tidak menahan-nahan upah pejerha atau memperkerjakannya diluar batas atau melecehkannya') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_16">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_16">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Anda sebagai petani anggota group administrator harus menghindari pengenalan, penanaman tanaman transgenik di kebun') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_17">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_17">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Jika Anda membuka kebun baru, maka kebun tersebut harus didaerah yang sesuai secara iklim, kondisi tanah dan memang sesuai untuk ditanam kakao') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_18">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup1_18">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Anda memahami bagaimana penanganan pestisida dan pasca penyemprotan pestisida') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup12_18">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup12_18">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><?php echo lang('Anda memahami bagaimana penanganan pertolongan pertama pada kecelakaan dikebun') ?>
													</td>
													<td class="box"><input disabled type="radio" value="radio" id="RadioGroup13_18">
														<?php echo lang('Ya') ?>
														<input disabled type="radio" value="radio" id="RadioGroup13_18">
														<?php echo lang('Tidak') ?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
												</tr>
											</table>
										</div>
									</div>
									<?php
								}
								?>
								<div class="text_area">
									<table width="100%" cellspacing="1">
										<tr>
											<td colspan="3"><?php echo lang('Candidate Selection') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('ICS Date') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('External Date') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('Year') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('Jenis Sertifikasi') ?></td>
											<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_6">
												<?php echo lang('UTZ') ?>
												<input disabled type="radio" value="radio" id="RadioGroup1_6">
												<?php echo lang('Rainforest') ?>
												<input disabled type="radio" value="radio" id="RadioGroup1_6">
												<?php echo lang('Fairtrade') ?>
												<input disabled type="radio" value="radio" id="RadioGroup1_6">
												<?php echo lang('Organik') ?>
											</td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('Penilaian dari internal inspektor') ?></td>
											<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_7">
												<?php echo lang('Tidak Lolos Audit') ?>

												<input disabled type="radio" value="radio" id="RadioGroup1_7"> <?php echo lang('Lolos Audit') ?>
											</td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('Jenis Pemegang Sertifikasi') ?></td>
											<td class="box"><input disabled type="radio" value="radio" id="RadioGroup1_8">
												<?php echo lang('Trader') ?>
												<input disabled type="radio" value="radio" id="RadioGroup1_8">
												<?php echo lang('Koperasi') ?>
												<input disabled type="radio" value="radio" id="RadioGroup1_8">
												<?php echo lang('Warehouse') ?>
											</td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('Pemegang Sertifikasi') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('Tidak lolos audit, paling lambat perbaikan tanggal') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td colspan="3" height="37"><?php echo lang('Komentar') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td height="37" colspan="3"><?php echo lang('Rekomendasi') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td colspan="3"><?php echo lang('Inspektor') ?></td>
											<td class="box"></td>
										</tr>
										<tr>
											<td colspan="4">
												<table style="width: 100%">
													<tr>
														<td><?php echo lang('Apakah anda menyetujui untuk mengkuti program sertifikasi secara sukarela') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion11'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion11'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Anda mencegah pencampuran biji kakao sertifikasi dengan non sertifikasi dengan mengerti sistem penelusuran dan penjualan biji kakao sertifikasi beserta persyaratannya dan menjalankannya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion1'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion1'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Anda telah berupaya mengurangi konsumsi energi tidak terbarukan minimal dengan mengurangi konsumsi listrik (menggunakan seperlunya) dirumah, mengurangi penggunaan') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion2'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion2'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Anda menjaga ekosistem alam (air dan tanah) khususnya yang ada disekitar kebun dengan minimal tidak menebang pohon, tidak membuang sampah plastik di kebun, badan air sekitar kebun') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion3'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion3'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Kebun anda sebelumnya bukan merupakan hutan yang dibuka menjadi kebun setelah 1 November 2005') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion4'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion4'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Anda melarang perburuan, penangkapan, penangkaran dan penjualan satwa liar di kebun dengan minimal memasang tanda-tanda larangan untuk hal tersebut') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion5'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion5'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Anda tidak membuang limbah/sampah organik maupun non-organik kedalam badan air dengan minimal memasang tanda pelarangan untuk hal tersebut') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion6'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion6'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Jika Anda memperkerjakan orang, Anda tidak membeda-bedakan orang/pekerja berdasarkan suku, ras, agama, warna kulit, status sosial') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion7'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion7'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Jika Anda memperkerjakan orang, Anda tidak menahan-nahan upah pejerha atau memperkerjakannya diluar batas atau melecehkannya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion8'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion8'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Anda sebagai petani anggota group administrator harus menghindari pengenalan, penanaman tanaman transgenik di kebun') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion9'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion9'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Jika Anda membuka kebun baru, maka kebun tersebut harus didaerah yang sesuai secara iklim, kondisi tanah dan memang sesuai untuk ditanam kakao') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion10'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion10'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Anda memahami bagaimana penanganan pestisida dan pasca penyemprotan pestisida') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion12'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion12'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Anda memahami bagaimana penanganan pertolongan pertama pada kecelakaan di kebun') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion13'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion13'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="4">
												<table style="width: 100%">
													<tr>
														<td><?php echo lang('Apakah di kebun Bapak/Ibu ada salah satu atau beberapa sumber air berikut ini') ?></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Sungai') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion14A'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion14A'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Sumur') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion14B'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion14B'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Mata air') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion14C'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion14C'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Lainnya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion14D'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion14D'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Apakah kebun Bapak/Ibu berbatasan dengan sumber air') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion15'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion15'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Apakah Bapak/Ibu melakukan penyemprotan pestisida di/dekat badan air') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion16'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion16'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Apakah Bapak/Ibu melakukan pemupukan organik/inorganik dekat badan air') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion17'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion17'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>	
								</div>
							</div>	
						</div>
					</div>
				</div>	
			</div>	
			<div class="page-break"></div>
			<div class="page">
				<div id="templatemo_container">
					<div id="templatemo_left_column" style="">
						<div class="text_area" align="center">
							<div class="section_box2" align="center">
								<div class="text_area">
									<table width="100%" cellspacing="1">							
										<tr>
											<td colspan="4">
												<table style="width: 100%">
													<tr>
														<td><?php echo lang('Apakah Bapak/Ibu mempekerjakan anak di bawah usia 17 tahun') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion18'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion18'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Berapa orang') ?></td>
														<td colspan="2" style="width:200px; text-align:center;"><input type="text" value="<?php echo $certification['RACertQuestion19'] ?>"></td>
													</tr>
													<tr>
														<td><?php echo lang('Berapa tahun usia pekerja tersebut') ?></td>
														<td colspan="2" style="width:200px; text-align:center;"><input type="text" value="<?php echo $certification['RACertQuestion20'] ?>"></td>
													</tr>
													<tr>
														<td><?php echo lang('Berapa jam pekerja anak tersebut bekerja dalam sehari') ?></td>
														<td colspan="2" style="width:200px; text-align:center;"><input type="text" value="<?php echo $certification['RACertQuestion21'] ?>"></td>
													</tr>
													<tr>
														<td><?php echo lang('Apakah ada dari pekerja anak tersebut yang bekerja hingga malam hari (setelah jam 6 sore)') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion22'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion22'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td><?php echo lang('Apakah karena pekerjaannya, pekerja anak tersebut berhubungan dengan') ?></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Bensin') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23A'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23A'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Gas') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23B'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23B'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Pisau dan alat pangkas tajam lainnya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23C'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23C'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Pestisida') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23E'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23E'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Pupuk') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23D'] == 1 ? 'checked=""':'' ?> ><?php echo lang('Ya') ?></td>
														<td style="width:100px; text-align:center;"><input type="radio" disabled="" <?php echo $certification['RACertQuestion23D'] == 2 ? 'checked=""':'' ?> ><?php echo lang('Tidak') ?></td>
													</tr>
													<!-- <tr>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sebutkan</td>
														<td colspan="2" style="width:200px; text-align:center;"><input type="text" value="<?php echo $certification['RACertQuestion23DText'] ?>"></td>
													</tr> -->
												</table>
											</td>
										</tr>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td align="right">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td colspan="3">
												<center>
													<?php echo lang('Tanda Tangan Petani') ?><br>
													<textarea name="textarea" cols="45" rows="5" id="textarea"></textarea>
												</center>
											</td>
											<td>
												<center><?php echo lang('Tanda Tangan Inspektor') ?><br>
													<textarea name="textarea2" cols="45" rows="5" id="textarea2"></textarea>
												</center>
											</td>
										</tr>
										<tr>
											<td colspan="3">
												<center>
													<?php echo lang('Tanda Tangan Komite') ?><br>
													<textarea name="textarea" cols="45" rows="5" id="textarea"></textarea>
												</center>
											</td>
											<td>
												<center><?php echo lang('Tanda Tangan IMS Manager') ?><br>
													<textarea name="textarea2" cols="45" rows="5" id="textarea2"></textarea>
												</center>
											</td>
										</tr>
										<tr>
											<td width="24%">&nbsp;</td>
											<td width="1%">&nbsp;</td>
											<td width="24%" align="right">&nbsp;</td>
											<td width="51%">&nbsp;</td>
										</tr>
									</table>
								</div>
							</div>

						</div>
					</div>

				</div>
			</div>
		</div>
	</body>
</html>
  

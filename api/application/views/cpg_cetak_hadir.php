<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir</title>

    <style type="text/css">
		body {
			margin: 0;
			padding: 0;
			background-color: #FAFAFA;
		}
		* {
			box-sizing: border-box;
			-moz-box-sizing: border-box;
		}
		.page {
			width: 21cm;
			height:27.5cm;
			padding-top: 1cm;
			padding-bottom: 1cm;
			padding-right: 0.5cm;
			padding-left: 0.5cm;
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
		}
		.section_box3 {
			clear: left;
			margin-top: 10px;
			background: #ffffff;
			color: #000000;
			border: 1px;
		}
		.text_area {
			padding: 0px 2px 0px 2px;
			font-size: 14px;
			line-height:20px;
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
			padding: 2px;
			padding-left: 10px;
			background: #cccccc;
			font-size: 12px;
			font-weight: bold;
			color: #000000;
		  border-bottom: 1px solid #000000;
		  text-align:left;
		}
		.post_title {
			padding: 2px;
			padding-left: 10px;
			background: #cccccc;
			font-size: 12px;
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
		  font-size: 11px;
		}
		.box13 {
			border:1px solid #000000;
			font-size: 11px;
		}
		.box_disabled {
			border:1px solid #000000;
			background-color:#CCCCCC;
		}
		.font11 {
		  font-size: 11px;
		}
		.font12 {
			font-size: 11px;
		}
		.font13 {
			font-size: 11px;
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
		input{border: 1px solid #000000;background-color: #FFF}
		.page-break {
			page-break-before: always;
		}
    </style>

</head>
	<body id="page">
		<div class="page">
			<div id="templatemo_container_wrapper">
				<div id="templatemo_container">
					<div id="templatemo_header" style="height:110px; margin-top:-15px;">
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
						<table width="100%" border="0" cellpadding="2" style="font-size: 16px; margin-top: -10px;">
							<tr>
								<td align="center" style="vertical-align:middle;"><span style="text-decoration:underline;">Daftar Hadir</span><br/>
                                    <span style=""><?php echo $data['CpgTrainings']?></span>&nbsp;<span style="font-size:12px;"><?php echo $data['CpgTrainingsSubTopic']?></span>
                                </td>
							</tr>
						</table>
					</div>
					<!--<div id="templatemo_header">
						<table width="100%" cellspacing="0">
							<tr>
								<td height="60px" width="150px" align="center" style="vertical-align:middle;">
									<img src="<?php //echo base_url()?>images/Photo/<?php //echo $logo['Photo']?>" style="max-width:100%; max-height:100%;">
								</td>
								<td height="60px" align="center" style="vertical-align:middle;">Daftar
									Hadir<br/><span style="text-decoration:underline;"><?php //echo $data['CpgTrainings']?></span></td>
								<td height="60px" width="150px" align="center" style="vertical-align:middle;">
									<img src="<?php //echo base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;"></td>
							</tr>
						</table>
					</div>-->
					<div id="templatemo_left_column" style="font-size: 16px; margin-top: -20px;">
						<div class="text_area" align="center">
							<div class="section_box2" align="center">
								<div class="text_area">
									<br>
									<table width="100%" cellspacing="0" style="border : 1px solid #000000;">
										<tr style="background-color: #CCCCCC;padding:2px;">
											<td width="50%" colspan="2">CPG ID
												KELOMPOK <?php echo ($data['CPGid'] != '') ? $data['CPGid'] : $data['OldCPGid']?></td>
											<td width="50%" colspan="2">TRAINING BATCH <?php echo $data['BatchNumber']?></td>
										</tr>
										<tr style="padding:4px;">
											<td>Nama Kelompok</td>
											<td>
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['GroupName']?>" disabled size="20">
											</td>
											<td>Desa</td>
											<td>
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['Desa']?>" disabled size="20">
											</td>
										</tr>
										<tr style="padding:4px;">
											<td>Petani Andalan</td>
											<td>
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['pemandu']?>" disabled size="20">
											</td>
											<td>Kecamatan</td>
											<td>
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['Kecamatan']?>" disabled size="20">
											</td>
										</tr>
										<tr style="padding:4px;">
											<td>Petugas Lapangan</td>
											<td>
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo (trim($data['koordinator']) != '') ? $data['koordinator'] : $data['PrivateStaffName']?>" disabled size="20">
											</td>
											<td>Kabupaten</td>
											<td>
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['Kabupaten']?>" disabled size="20">
											</td>
										</tr>
										<tr style="padding:4px;">
											<td>Penyuluh</td>
											<td>
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['penyuluh']?>" disabled size="20">
											</td>
											<td>Provinsi</td>
											<td>
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['Provinsi']?>" disabled size="20">
											</td>
										</tr>
										<tr style="padding:4px;">
											<td style="vertical-align:top;" width="30%">Tanggal Pertama Pelatihan</td>
											<td style="vertical-align:top;" width="20%">
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['TrainingStart']?>" disabled size="20">
											</td>
											<td style="vertical-align:top;" width="30%">Tanggal Terakhir Pelatihan</td>
											<td style="vertical-align:top;" width="20%">
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['TrainingEnd']?>" disabled size="20">
											</td>
										</tr>
										<tr style="padding:4px;">
											<td style="vertical-align:top;" width="30%">Hari Pelatihan Ke-</td>
											<td style="vertical-align:top;" width="20%">
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php echo $data['DayNumber']?>" disabled size="20">
											</td>
											<td style="vertical-align:top;" width="30%">Tanggal Pelatihan</td>
											<td style="vertical-align:top;" width="20%">
												<input style="border: 1px solid #999;background-color: #FFF; padding:2px" type="text" value="<?php if($data['TrainingDate']!=""){echo date('d - M - Y',strtotime($data['TrainingDate']));}?>" disabled size="20">
											</td>
										</tr>
									</table>
								</div>
							</div>

							<div class="section_box3" align="center">
								<div class="text_area">
									<table width="100%" border="1" cellspacing="0" cellpadding="0" style="border:1px solid #000000;font-size:12px;">
										<tr style="background-color: #CCCCCC;border:1px solid #000000;" align="center">
											<td><strong>Nomor</strong></td2>
											<td><strong>Nama Petani</strong></td>
											<td><strong>L/P</strong></td>
											<td><strong>Desa</strong></td>
                                            <?php
                                            if($data['TrainingDayStatus'] == "full"){
                                                echo '<td><strong>Kehadiran Pagi</strong></td>
                                                <td><strong>Kehadiran Siang</strong></td>';
                                            }else{
                                                echo '<td colspan="2"><strong>Kehadiran</strong></td>';
                                            }
                                            ?>
										</tr>
										<?
											$j = 0;
											for ($i = 0; $i < sizeof($peserta); $i++) {
										?>
										<!-- Start For -->
										<tr style="border:1px solid #000000;">
											<td align="center" rowspan="2" height="50"><?php echo $peserta[$i]['pFarmerID']?></td>
											<td height="25">&nbsp;&nbsp;<?php echo $peserta[$i]['PersonNm']?></td>
											<td align="center" height="25"><?php echo ($peserta[$i]['Gender'] == '1') ? 'L' : 'P'?></td>
											<td rowspan="2">&nbsp;&nbsp;<?php echo $peserta[$i]['Desa']?></td>
                                            <?php
                                            if($data['TrainingDayStatus'] == "full"){ ?>
                                                <td rowspan="2">
                                                    <center>
                                                    <?php
                                                        if($peserta[$i]['Attendance1']=="1" && $peserta[$i]['SignAttendance1']!=""){
                                                            echo '<img width="100px" height="40px" src="'.base_url().'images/attendance_list_sign/'.$peserta[$i]['SignAttendance1'].'">';
                                                        }
                                                        if($peserta[$i]['Attendance1']=="1" && $peserta[$i]['SignAttendance1']=="" && $peserta[$i]['LearningContractSign']!=""){
                                                            echo '<img width="100px" height="40px" src="'.base_url().'images/learning_contract_sign/'.$peserta[$i]['LearningContractSign'].'">';
                                                        }
                                                    ?>
                                                    </center>
                                                </td>
                                                <td rowspan="2">
                                                    <center>
                                                    <?php
                                                        if($peserta[$i]['Attendance2']=="1" && $peserta[$i]['SignAttendance2']!=""){
                                                            echo '<img width="100px" height="40px" src="'.base_url().'images/attendance_list_sign/'.$peserta[$i]['SignAttendance2'].'">';
                                                        }
                                                        if($peserta[$i]['Attendance2']=="1" && $peserta[$i]['SignAttendance2']=="" && $peserta[$i]['LearningContractSign']!=""){
                                                            echo '<img width="100px" height="40px" src="'.base_url().'images/attendance_list_sign/'.$peserta[$i]['LearningContractSign'].'">';
                                                        }
                                                    ?>
                                                    </center>
                                                </td>
                                            <?php }else{ ?>
                                                <td rowspan="2" colspan="2">
                                                    <center>
                                                    <?php
                                                        if($peserta[$i]['Attendance1']=="1" && $peserta[$i]['SignAttendance1']!=""){
                                                            echo '<img width="100px" height="40px" src="'.base_url().'images/attendance_list_sign/'.$peserta[$i]['SignAttendance1'].'">';
                                                        }
                                                        if($peserta[$i]['Attendance1']=="1" && $peserta[$i]['SignAttendance1']=="" && $peserta[$i]['LearningContractSign']!=""){
                                                            echo '<img width="100px" height="40px" src="'.base_url().'images/learning_contract_sign/'.$peserta[$i]['LearningContractSign'].'">';
                                                        }
                                                    ?>
                                                    </center>
                                                </td>
                                            <?php } ?>
										</tr>
										<tr style="border:1px solid #000000;">
											<td>&nbsp;&nbsp;<?php echo $peserta[$i]['AnggotaName']?></td>
											<td align="center"><?php echo (trim($peserta[$i]['AnggotaName']) != '') ? (($peserta[$i]['AnggotaGender'] == '1') ? 'L' : 'P') : ''?></td>
										</tr>
											<?
												if (($i == 13 OR ($i > 13 and ($i - 13) % 18 == 0 and $i < sizeof($peserta))) ) {
													$j = $i;
											?>
											<!-- Start IF -->
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="page-break"></div>
		<div class="page">
			<div id="templatemo_container_wrapper">
				<div id="templatemo_container">
					<div id="templatemo_left_column">
												<?
													if($i == $j && $i !=0 && $i != sizeof($peserta) - 1){
												?>
												<!-- Start Last break -->
						<div class="text_area" align="center">
							<div class="section_box3" align="center">
								<div class="text_area">
									<table width="100%" border="1px" cellspacing="0">
										<tr style="background-color: #CCCCCC;" align="center">
											<td><strong>Nomor</strong></td2>
											<td><strong>Nama Petani</strong></td>
											<td><strong>L/P</strong></td>
											<td><strong>Desa</strong></td>
                                            <?php
                                            if($data['TrainingDayStatus'] == "full"){
                                                echo '<td><strong>Kehadiran Pagi</strong></td>
                                                <td><strong>Kehadiran Siang</strong></td>';
                                            }else{
                                                echo '<td colspan="2"><strong>Kehadiran</strong></td>';
                                            }
                                            ?>
										</tr>
												<!-- End Last break -->
												<? } ?>
											<!-- End IF -->
											<? } ?>
										<!-- End For -->
										<? } ?>
									</table>
								</div>
							</div>
						</div>
						<?php
							$x = (sizeof($peserta) -13) % 18;
							if( $x > 16 OR $x == 0){
						?>
						<!-- Start sisa Div -->
					</div>
				</div>
			</div>
		</div>
		<div class="page-break"></div>

		<div class="page">
			<div id="templatemo_container_wrapper">
				<div id="templatemo_container">
					<div id="templatemo_left_column">
						<!-- End sisa Div -->
						<? } ?>
						<div class="text_area" align="center">
							<div class="section_box3" align="center">
								<div class="text_area">
									<table width="100%" cellspacing="0">
										<tr>
											<td>Hari dan Tanggal Pelatihan :</td>
											<td colspan="3"></td>
										</tr>
										<tr>
											<td width="255px" align="center">Koordinator Lapangan</td>
											<td width="260px" align="center">Petani Andalan/Pemandu</td>
											<td width="255px" align="center">Penyuluh</td>
										</tr>
										<tr>
											<td height="80">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td width="255px" align="center" style="text-decoration: underline">
												<strong><?php echo (trim($data['koordinator']) != '') ? $data['koordinator'] : $data['PrivateStaffName']?></strong>
											</td>
											<td width="260px" align="center" style="text-decoration: underline">
												<strong><?php echo ($data['PetaniKakao'] === '1') ? $data['pemandu'] : $data['KeyFarmerFamily']?></strong>
											</td>
											<td width="255px" align="center" style="text-decoration: underline">
												<strong><?php echo $data['penyuluh']?></strong></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="page-break"></div>

		<div class="page">
			<div id="templatemo_container_wrapper">
				<div id="templatemo_container">
					<div id="templatemo_header" style="height:110px; margin-top:-15px;">
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
						<table width="100%" border="0" cellpadding="2" style="font-size: 16px; margin-top: -10px;margin-bottom:5px;">
							<tr>
								<td align="center" style="vertical-align:middle;">
                                    <span style="text-decoration:underline;">Checklist Daftar Hadir</span><br/>
								    <span style=""><?php echo $data['CpgTrainings']?></span>&nbsp;<span style="font-size:12px;"><?php echo $data['CpgTrainingsSubTopic']?></span>
                                </td>
							</tr>
						</table>
					</div>
					<!--<div id="templatemo_header">
						<table width="100%" cellspacing="0">
							<tr>
								<td height="60px" width="200px" align="center" style="vertical-align:middle;">
									<img src="<?php //echo base_url()?>images/Photo/<?php //echo $logo['Photo']?>" style="max-width:100%; max-height:100%;">
								</td>
								<td height="60px" align="center" style="vertical-align:middle;">Checklist Daftar Hadir
									<br/><span style="text-decoration:underline;"><?php //echo $data['CpgTrainings']?></span></td>
								<td height="60px" width="200px" align="center" style="vertical-align:middle;">
									<img src="<?php //echo base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;"></td>
							</tr>
						</table>
					</div>-->

					<div id="templatemo_left_column">
						<div class="text_area" align="center">
							<div class="section_box3" align="center">
								<div class="text_area">
									<table width="100%" cellspacing="0" style="border : 1px solid #000000;">
										<tr style="background-color: #CCCCCC;padding:2px;">
											<td width="50%" colspan="2"><strong>CPG ID KELOMPOK <?php echo ($data['CPGid'] != '') ? $data['CPGid'] : $data['OldCPGid']?></strong></td>
											<td width="50%" colspan="2"><strong>TRAINING BATCH <?php echo $data['BatchNumber']?></strong></td>
										</tr>
									</table>
									<table width="100%" border="1" cellspacing="0" cellpadding="0" style="border:1px solid #000000;font-size:12px;">
										<tr style="background-color: #CCCCCC;border:1px solid #000000;" align="center">
											<td><strong>Nomor</strong></td>
											<td><strong>Nama Petani</strong></td>
											<td><strong>L/P</strong></td>
											<td><strong>Desa</strong></td>
											<?php foreach ($attendance[0] as $key => $value): ?>
												<td><strong><?php echo $value['DayNumber'] ?></strong></td>
											<?php endforeach ?>
										</tr>
										<?php
											$i = 0;
											foreach ($peserta as $key => $value):
										?>
										<tr style="border:1px solid #000000;">
											<td align="center"><?php echo $value['pFarmerID']?></td>
											<td>&nbsp;<?php echo $value['PersonNm']?></td>
											<td align="center"><?php echo ($value['Gender'] == '1') ? 'L' : 'P'?></td>
											<td>&nbsp;<?php echo $value['Desa']?></td>
											<?php foreach ($attendance[$key] as $k => $v): ?>
												<td style="text-align:center; width:40px;"></td>
											<?php endforeach ?>
										</tr>
										<?
											if (($i == 36 OR ($i > 36 and ($i - 36) % 40 == 0 and $i < sizeof($peserta))) ) {
												$j = $i;
										?>
										<!-- Start IF -->
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="page-break"></div>
		<div class="page">
			<div id="templatemo_container_wrapper">
				<div id="templatemo_container">
					<div id="templatemo_left_column">
						<div class="text_area" align="center">
							<div class="section_box3" align="center">
												<?
													if($i == $j && $i !=0 && $i != sizeof($peserta) - 1){
												?>
												<!-- Start Last break -->
								<div class="text_area">
									<table width="100%" cellspacing="0" style="border : 1px solid #000000;">
										<tr style="background-color: #CCCCCC;padding:2px;">
											<td width="50%" colspan="2"><strong>CPG ID KELOMPOK <?php echo ($data['CPGid'] != '') ? $data['CPGid'] : $data['OldCPGid']?></strong></td>
											<td width="50%" colspan="2"><strong>TRAINING BATCH <?php echo $data['BatchNumber']?></strong></td>
										</tr>
									</table>
									<table width="100%" border="1" cellspacing="0" cellpadding="0" style="border:1px solid #000000;font-size:12px;">
										<tr style="background-color: #CCCCCC;border:1px solid #000000;" align="center">
											<td><strong>Nomor</strong></td>
											<td><strong>Nama Petani</strong></td>
											<td><strong>L/P</strong></td>
											<td><strong>Desa</strong></td>
											<?php foreach ($attendance[0] as $key => $value): ?>
												<td><strong><?php echo $value['DayNumber'] ?></strong></td>
											<?php endforeach ?>
										</tr>
												<!-- End Last break -->
												<? } ?>
										<!-- End IF -->
											<? } ?>
										<?php
												$i++;
											endforeach
										?>
									</table>
								</div>
							</div>
						</div>
						<?php
							$x = (sizeof($peserta) - 38) % 40;
							if( $x > 36 OR $x == 0){
						?>
						<!-- Start sisa Div -->
					</div>
				</div>
			</div>
		</div>
		<div class="page-break"></div>
		<div class="page">
			<div id="templatemo_container_wrapper">
				<div id="templatemo_container">
					<div id="templatemo_left_column">
						<!-- End sisa Div -->
						<? } ?>
						<div class="text_area" align="center">
							<div class="section_box3" align="center">
								<div class="text_area">
									<table width="100%" cellspacing="0">
										<tr>
											<td>Hari dan Tanggal Pelatihan :</td>
											<td colspan="3"></td>
										</tr>
										<tr>
											<td width="255px" align="center">Koordinator Lapangan</td>
											<td width="260px" align="center">Petani Andalan/Pemandu</td>
											<td width="255px" align="center">Penyuluh</td>
										</tr>
										<tr>
											<td height="80">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td width="255px" align="center" style="text-decoration: underline">
												<strong><?php echo (trim($data['koordinator']) != '') ? $data['koordinator'] : $data['PrivateStaffName']?></strong>
											</td>
											<td width="260px" align="center" style="text-decoration: underline">
												<strong><?php echo ($data['PetaniKakao'] === '1') ? $data['pemandu'] : $data['KeyFarmerFamily']?></strong>
											</td>
											<td width="255px" align="center" style="text-decoration: underline">
												<strong><?php echo $data['penyuluh']?></strong></td>
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

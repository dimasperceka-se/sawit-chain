<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir</title>

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
			/*padding: 0px 2px 0px 2px;*/
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
<body id="page">
	<div id="templatemo_container_wrapper">
		<div class="page">
			<div id="templatemo_container">
				<div id="templatemo_header" class="logos" style="height:100px; margin-top:-15px;">
					<table width="100%" border="0" cellpadding="2">
						<tr>								
							<?php if (count($logos['private_sector']) == 1): ?>
								<?php $logo = $logos['private_sector'][0]; ?>
								<?php 
									if($logo['Photo']!=''){
										$file = base_url().'images/Photo/'.$logo['Photo'];
										//if (@getimagesize($file)) {
								?>
								<td height="60px" width="20%" align="left" style="vertical-align:middle;">
									<img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
								<?php } //} ?>
								<?php 
									if($logo['PhotoProgram']!=''){
										$file = base_url().'images/Photo/'.$logo['PhotoProgram'];
										//if (@getimagesize($file)) {
								?>
								<td height="60px" width="20%" align="left" style="vertical-align:middle;">
									<img src="<?=base_url()?>images/Photo/<?=$logo['PhotoProgram']?>" style="max-width:90%; max-height:90%; max-width:120px;">
								</td>
								<?php } //} ?>
								<?php ?>
							<?php elseif (count($logos['private_sector']) == 2): ?>
								<?php foreach ($logos['private_sector'] as $key => $logo): ?>
									<?php
										if($logo['Photo']!=''){
											$file = base_url().'images/Photo/'.$logo['Photo'];
											//if (@getimagesize($file)) {
									?>
									<td height="60px" align="left" style="vertical-align:middle;">
										<img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:90%; max-height:90%; max-width:120px;">
									</td>
									<?php } //} ?>								
								<?php endforeach ?>
							<?php endif ?>
							<?php if (count($logos['donor']) > 0): ?>
								<?php foreach ($logos['donor'] as $key => $logo): ?>
									<?php
										if($logo['Photo']!=''){
											$file = base_url().'images/Photo/'.$logo['Photo'];
											//if (@getimagesize($file)) {
									?>
									<td height="60px" align="left" style="vertical-align:middle;">
										<img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:90%; max-height:90%; max-width:120px;">
									</td>
									<?php } //} ?>
								<?php endforeach ?>
							<?php endif ?>
							
							<td width="20%" align="right" style="vertical-align:middle;">
								<img src="<?=base_url()?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
							</td>
						</tr>
					</table>
					<table width="100%" border="0" cellpadding="2" style="margin-top:-15px;">
						<tr>
							<td align="center" style="vertical-align:middle;">Kartu Penjualan Biji Kakao Petani</td>
						</tr>
					</table>
				</div>
				<div id="templatemo_left_column" style="margin-top:-20px;">
					<div class="text_area" align="center">
						<div class="" align="center">
							<div class="text_area">
								<br/>
								<table width="100%" cellspacing="0" style="border:1px solid #000000;">
									<tbody>
										<tr style="padding: 4px;">
											<td>Nama Petani</td>
											<td>
												: <input type="text" size="34" disabled="" value="<?=$data['FarmerName']?>" style="border:1px solid #999;background-color: #fff;"/>
											</td>
											<td>Desa</td>
											<td>
												: <input type="text" size="34" disabled="" value="<?=$data['Village']?>" style="border:1px solid #999;background-color: #fff;"/>
											</td>
											<td>Quota</td>
											<td>
												: <input type="text" size="34" disabled="" value="<?=number_format($data['survey'])?>" style="border:1px solid #999;background-color: #fff;"/>
											</td>
										</tr>
										<tr style="padding: 4px;">
											<td>ID Petani</td>
											<td>
												: <input type="text" size="34" disabled="" value="<?=$data['FarmerID']?>" style="border:1px solid #999;background-color: #fff;"/>
											</td>
											<td>Kecamatan</td>
											<td>
												: <input type="text" size="34" disabled="" value="<?=$data['SubDistrict']?>" style="border:1px solid #999;background-color: #fff;"/>
											</td>
											<td>CPG</td>
											<td>
												: <input type="text" size="34" disabled="" value="<?=$data['GroupName']?>" style="border:1px solid #999;background-color: #fff;"/>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<!-- text box 3 -->
						<div class="section_box3" align="center">
							<div class="text_area">
								<table width="100%" cellspacing="0" cellpadding="0" border="1" style="border:1px solid #000000; font-size: 12px;">
									<tbody>
										<tr align="center" style="background-color: #ccc;border:1px solid #000;">
											<td align="center" height="50" rowspan="2"><strong>No</strong></td>
											<td rowspan="2"><strong>Tanggal Penjualan</strong></td>
											<td rowspan="2"><strong>Buying Unit</strong></td>
											<td rowspan="2"><strong>FF/FAQ</strong></td>
											<td rowspan="2"><strong>Berat Kotor(Kg)</strong></td>
											<td align="center" height="25"><strong>Quality</strong></td>
											<td rowspan="2"><strong>Berat Bersih(Kg)</strong></td>
											<td rowspan="2"><strong>Harga(Rp)</strong></td>
											<td rowspan="2"><strong>Total Harga(Rp)</strong></td>
											<td rowspan="2"><strong>Paraf</strong></td>
										</tr>
										<tr align="center" style="background-color: #ccc;border:1px solid #000;">
											<td align="center"><strong>Kadar Air(7%)</strong></td>
										</tr>
										<?php 
										if(count($records)>0){
											for ($i=0;$i<sizeof($records);$i++){
												$n = $i+1;
										?>
											<tr align="left" style="background-color: #fff;border:1px solid #000;">
												<td height="25" style="padding-left: 2px;"><?=$n?></td>
												<td style="padding-left: 5px;"><?=$records[$i]['DateTransaction']?></td>
												<td style="padding-left: 5px;"><?=$records[$i]['BuyingUnit']?></td>
												<td align="right" style="padding-right: 5px;"><?=$records[$i]['FF_FAQ']?></td>
												<td align="right" style="padding-right: 20px;"><?=$records[$i]['Bruto']?></td>
												<td align="right" style="padding-right: 20px;"><?=$records[$i]['Moisture']?></td>
												<td align="right" style="padding-right: 20px;"><?=number_format($records[$i]['Netto'], 2, '.', ',')?></td>
												<td align="right" style="padding-right: 5px;"><?=number_format($records[$i]['FAQNetPrice'], 2, '.', ',')?></td>
												<td align="right" style="padding-right: 5px;"><?=number_format($records[$i]['FAQTotalPayment'], 2, '.', ',')?></td>
												<td></td>
											</tr>
										<?php	
											if (($i == 29 OR ($i > 29 and ($i - 29) % 30 == 0 )) ) { 
										?>
									</tbody>
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
				<div id="templatemo_left_column">
					<div class="text_area" align="center">
						<div class="section_box3" align="center">
							<div class="text_area">
								<table width="100%" cellspacing="0" cellpadding="0" border="1" style="border:1px solid #000000; font-size: 12px;">
									<tbody>
										<tr align="center" style="background-color: #ccc;border:1px solid #000;">
											<td align="center" height="50" rowspan="2"><strong>No</strong></td>
											<td rowspan="2"><strong>Tanggal Penjualan</strong></td>
											<td rowspan="2"><strong>Buying Unit</strong></td>
											<td rowspan="2"><strong>FF/FAQ</strong></td>
											<td rowspan="2"><strong>Berat Kotor(Kg)</strong></td>
											<td align="center" height="25"><strong>Quality</strong></td>
											<td rowspan="2"><strong>Berat Bersih(Kg)</strong></td>
											<td rowspan="2"><strong>Harga(Rp)</strong></td>
											<td rowspan="2"><strong>Total Harga(Rp)</strong></td>
											<td rowspan="2"><strong>Paraf</strong></td>
										</tr>
										<tr align="center" style="background-color: #ccc;border:1px solid #000;">
											<td align="center"><strong>Kadar Air(7%)</strong></td>
										</tr>
										<?php
											}
										?>	
										<?php } 
										}else{
											for ($i=0;$i<50;$i++){
												$n = $i+1;
										?>
											<tr align="left" style="background-color: #fff;border:1px solid #000;">
												<td height="25" style="padding-left: 2px;"><?=$n?></td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
										<?php	
											if (($i == 29 OR ($i > 29 and ($i - 29) % 30 == 0 )) ) { 
										?>
									</tbody>
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
				<div id="templatemo_left_column">
					<div class="text_area" align="center">
						<div class="section_box3" align="center">
							<div class="text_area">
								<table width="100%" cellspacing="0" cellpadding="0" border="1" style="border:1px solid #000000; font-size: 12px;">
									<tbody>
										<tr align="center" style="background-color: #ccc;border:1px solid #000;">
											<td align="center" height="50" rowspan="2"><strong>No</strong></td>
											<td rowspan="2"><strong>Tanggal Penjualan</strong></td>
											<td rowspan="2"><strong>Buying Unit</strong></td>
											<td rowspan="2"><strong>FF/FAQ</strong></td>
											<td rowspan="2"><strong>Berat Kotor(Kg)</strong></td>
											<td align="center" height="25"><strong>Quality</strong></td>
											<td rowspan="2"><strong>Berat Bersih(Kg)</strong></td>
											<td rowspan="2"><strong>Harga(Rp)</strong></td>
											<td rowspan="2"><strong>Total Harga(Rp)</strong></td>
											<td rowspan="2"><strong>Paraf</strong></td>
										</tr>
										<tr align="center" style="background-color: #ccc;border:1px solid #000;">
											<td align="center"><strong>Kadar Air(7%)</strong></td>
										</tr>
										<?php
											}
										?>	
										<?php
											}
										}
										?>
									</tbody>
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

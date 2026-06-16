<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
	<head>
		<meta charset="utf-8">
		<title>Learning Contract</title>
		<style>
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
			padding: 1.5cm;
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
		   .page-break {
			   	display: block;
			   	page-break-before: always;
		   }
		}
		.page-break {
            display: block;
            page-break-before: always;
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

		@-moz-document url-prefix() {
			.logos {
				margin-left:-40px;
			}
		}
		</style>
	</head>
	<body>
		<div class="page">
			<div id="templatemo_container_wrapper">
				<div id="templatemo_container">
					<!--<div id="templatemo_header">
						<table width="100%" border="0" cellpadding="2">
							<tr>
								<td height="50" width="150px" align="center" style="vertical-align:bottom;"><img src="<?=base_url()?>images/Photo/20131213040539_SCPP Logo.jpg" style="max-width:100%; max-height:100%;"></td>
								<td height="50"  align="center" style="vertical-align:bottom;text-decoration:underline;">Surat Pernyataan<br>Keikutsertaan Sekolah Lapang</td>
								<td height="50" width="150px" align="center" style="vertical-align:bottom;"><img src="<?=base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;"></td>
							</tr>
						</table>
					</div>-->
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
						<table width="100%" border="0" cellpadding="2" style="font-size: 16px; margin-top: -10px;">
							<tr>
								<td align="center" style="vertical-align:middle;text-decoration:underline;font-size: 22px;">Surat Pernyataan<br>Keikutsertaan Sekolah Lapang</td>
							</tr>
						</table>
					</div>

					<div id="templatemo_left_column" style="margin-top:-30px;">
						<div class="text_area">
							<div class="text_area">
								<br><br><br><br>
								Yang bertanda tangan dibawah ini:
								<table width="100%" cellspacing="1">
									<tr><td width="20%">Nomor ID</td><td>: <?=$data['FarmerID']?></td></tr>
									<tr><td>Nama</td><td>: <?=$data['PersonNm']?></td></tr>
									<tr><td>Tgl. Lahir</td><td>: <?=$data['BirthDttm']?></td></tr>
									<tr><td>Jenis Kelamin</td><td>: <?=$data['Gender']=='1'?'Laki-laki':'Perempuan'?></td></tr>
									<tr><td>Alamat</td><td>: <?=$data['alamat'].', '.$data['Desa'].', '.$data['Kecamatan'].', '.$data['Kabupaten'].', '.$data['Provinsi']?></td></tr>
									<tr><td>Kelompok Tani</td><td>: <?=$data['CPGid'].' - '.$data['GroupName']?></td></tr>
								</table>
								<br>
								Dengan ini menyatakan:
								<br>
								<ol>
									<li>Bersedia dengan sungguh-sungguh dan secara proaktif mengikuti kegiatan sekolah lapang yang dilakukan selama 3 (tiga) bulan dan kegiatan lanjutan komponen program lainnya;</li>
									<li>Bersedia menerapkan dan mempraktekkan hasil yang di dapat selama kegiatan sekolah lapang di kebun masing-masing baik selama maupun pasca kegiatan sekolah lapang serta berbagi cerita sukses di kebun;</li>
									<li>Berkomitmen untuk memperbaiki kebun dan meningkatkan produktivitas minimal 1 ton/ha/tahun;</li>
									<li>Bersedia untuk berbagi pengetahuan yang didapat dalam sekolah lapang dengan petani lain di luar sekolah lapang;</li>
									<li>Bersedia memberikan informasi progres pelatihan, data kebun, praktek kebun, produktivitas kebun, kendala-kendala selama berkebun serta data petani dan keluarganya;</li>
									<li>Bersedia untuk mendukung keperluan dokumentasi program seperti penyediaan foto profil peserta, video kegiatan di kebun, serta kegiatan dokumentasi lainnya dimana data ini dapat dipublikasikan untuk mendukung Sustainable Cocoa Production Program SCPP;</li>
									<li>Memberikan persetujuan penggunaan dan publikasi seluruh data dan informasi petani yang didapat selama maupun setelah program pelatihan kepada pihak lain sepanjang digunakan untuk peningkatan kesejahteraan petani dan keberlangsungan manfaat SCPP dalam jangka panjang.</li>
								</ol>
								<br>
								Demikian surat pernyataan ini dibuat setelah memperoleh penjelasan sebelumnya, tanpa paksaan dari pihak manapun, serta agar dapat digunakan sebagaimana mestinya.
								<br><br><br>
								Yang Membuat Pernyataan,
								<br>
								<?=$data['Kabupaten'].', '.SetTanggal(date('Y-m-d'))?><br>
								<?php $external_link=base_url()."images/learning_contract_sign/".$data['LearningContractSign'];?>
								<?php if($data['LearningContractSign']!='' && @getimagesize($external_link)){ ?>
								<img width="200px" height="100px" src="<?=base_url()."images/learning_contract_sign/".$data['LearningContractSign']?>"><br>
								<?php }else{ ?>
								<br><br><br><br><br><br>
								<?php } ?>
								&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; (<?=$data['PersonNm']?>)
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="page-break"></div>
	</body>
</html>

<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Nutrisi</title>
		<style type="text/css">
			body {
				margin: 0;
				padding: 0;
				background-color: #FAFAFA;
				font: Tahoma, Geneva, sans-serif;
				font-size: 10px;
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
			.page-break {
				page-break-before: always;
			}
			.header {
				background-color: #CCCCCC;
				font-weight: bold;
			}

			.form_content {
				font-weight: bold;
			}

			.table {
				border: 1px solid #000000;
				padding: 4;
				font-size: 10px;
				background-color: #FFFFFF;
			}

			.table tr {
				border: 1px solid #000000;
			}

			.table td {
				font-weight: bold;
			}

			.box {
				border: 1px solid #000000;
			}

			.box_disabled {
				border: 1px solid #000000;
				background-color: #CCCCCC;
			}

			.table2 {
				background-color: #FFFFFF;
				border: 1px solid #000000;
				padding: 2px;
				font-size: 10px;
			}

			.table2 td {
				font-weight: normal;
				padding: 1px;
				border-bottom: 1px solid #000000;
			}

			.page_box {
				background-color: #FFFFFF;
				padding: 5px;
				color: #000000;
			}

			.subject_box {
				background-color: #FFFFFF;
				margin-top: 5px;
				color: #000000;
			}

			#title_header_box {
				background-color: #FFFFFF;
				clear: left;
				padding-top: 12px;
				/*height: 60px;*/
				text-align: center;
				font-weight: bold;
				font-size: 20px;
				color: #000000;
				/*background: url(images/templatemo_header_bg.gif) no-repeat;*/

			}

		</style>
	</head>
	<body>
		<div class="page">
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
						<td align="center" style="vertical-align:middle;text-decoration:underline;font-size:24px;"><b><?php echo lang('N1 - Nutrition Basic Data') ?></b></td>
					</tr>
				</table>
			</div>
			<div class="subject_box">
				<table width="100%" class="table" cellspacing="0" cellpadding="2">
					<tr>
						<td colspan="2" class="header" align="left"><?php echo lang('Data Umum Petani Kakao') ?></td>
						<td class="header" align="center"><?php echo lang('CPG') ?>
							- <?=($data['CPGid'] != '') ? $data['CPGid'] : $data['CPGid']?></td>
						<td class="header" align="right"><?php echo lang('PK') ?>
							- <?=($data['FarmerID'] != '') ? $data['FarmerID'] : $data['FarmerID']?></td>
					</tr>
					<tr>
						<td><?php echo lang('Nama Petani') ?></td>
						<td colspan="3">
							<input type="text" class="box" size="50" value="<?=$data['FarmerName']?>"/></td>
					</tr>
					<tr>
						<td><?php echo lang('Alamat') ?></td>
						<td colspan="3"><input type="text" class="box" size="50" value="<?=$data['Address']?>"/></td>
					</tr>
					<tr>
						<td><?php echo lang('Desa') ?></td>
						<td><input type="text" class="box" size="25" value="<?=$data['Desa']?>"/></td>
						<td><?php echo lang('Kecamatan') ?></td>
						<td><input type="text" class="box" size="25" value="<?=$data['Kecamatan']?>"/></td>
					</tr>
					<tr>
						<td><?php echo lang('Kabupaten') ?></td>
						<td><input type="text" class="box" size="25" value="<?=$data['Kabupaten']?>"/></td>
						<td><?php echo lang('Provinsi') ?></td>
						<td><input type="text" class="box" size="25" value="<?=$data['Provinsi']?>"/></td>
					</tr>
				</table>
			</div>
			<div class="subject_box" id="DataUmumPeserta">
				<table class="table" width="100%" border="0" cellspacing="2" cellpadding="2">
					<tr>
						<td colspan="2" class="header" style="padding:0;margin:0;"><?php echo lang('Data umum peserta pelatihan nutrisi') ?></td>
					</tr>
					<tr>
						<td><?php echo lang('Tanggal survey') ?></td>
						<td style="padding:0;margin:0;"><input type="text" class="box" size="25"
															   value="<?=$nutrition['DateInterview']?>"/></td>
					</tr>
					<tr>
						<td><?php echo lang('Orang yang sama dengan petani kakao?') ?></td>
						<td class="box"><input type="radio" name="FamilyID1" id="Orang1"
											   value="radio" <?=$family['FamilyID'] == null ? 'checked="checked"' : ''?>
											   disabled="true"/>
							<label for="radio"><?php echo lang('Ya') ?>
								<input type="radio" name="Orang" id="FamilyID2"
									   value="radio2" <?=$family['FamilyID'] = ! null ? '' : 'checked="checked"'?>
									   disabled="true"/>
								<?php echo lang('Tidak') ?></label></td>
					</tr>
					<tr>
						<td><?php echo lang('Hubungan Keluarga') ?></td>
						<td class="box"><input type="radio" name="HubunganKeluarga1" id="Hubungan1"
											   value="radio" <?=$family['HubunganKeluarga'] == '1' ? 'checked="checked"' : ''?>
											   disabled="true"/>
							<label for="radio3"><?php echo lang('Suami/Istri') ?>
								<input type="radio" name="HubunganKeluarga2" id="Hubungan2"
									   value="radio2" <?=$family['HubunganKeluarga'] == '2' ? 'checked="checked"' : ''?>
									   disabled="true"/>
								<?php echo lang('Anak') ?>
								<input type="radio" name="HubunganKeluarga3" id="Hubungan3"
									   value="radio2" <?=$family['HubunganKeluarga'] == '3' ? 'checked="checked"' : ''?>
									   disabled="true"/>
								<?php echo lang('Lain-lain') ?>
							</label></td>
					</tr>
					<tr>
						<td><?php echo lang('Nama Peserta Pelatihan Nutrisi') ?></td>
						<td style="padding:0;margin:0;"><input type="text" class="box" size="25"
															   value="<?=$family['AnggotaName']?>"/></td>
					</tr>
					<tr>
						<td><?php echo lang('Jenis Kelamin') ?></td>
						<td class="box"><input type="radio" name="AnggotaGender1" id="Kelamin1"
											   value="radio" <?=$family['AnggotaGender'] == '1' ? 'checked="checked"' : ''?>
											   disabled="true"/>
							<label for="radio6"><?php echo lang('Laki-laki') ?>
								<input type="radio" name="AnggotaGender2" id="Kelamin2"
									   value="radio2" <?=$family['AnggotaGender'] == '2' ? 'checked="checked"' : ''?>
									   disabled="true"/>
								<?php echo lang('Perempuan') ?>
							</label></td>
					</tr>
					<tr>
						<td><?php echo lang('Tahun Lahir') ?></td>
						<td style="padding:0;margin:0;"><input type="text" class="box" size="25"
															   value="<?=$family['AnggotaAge']?>"/></td>
					</tr>
				</table>
			</div>
			<div class="subject_box" id="KebunKeluarga">
				<table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
					<tr>
						<td class="header"><?php echo lang('Kebun Keluarga') ?></td>
					</tr>
					<tr>
						<td style="border-bottom:1px solid #000000;"><?php echo lang('Ukuran kebun sayuran : Panjang (m)') ?>
							<input type="text" class="box" size="15" value="<?=$nutrition['KebunPanjang']?>"/>
							<?php echo lang('Lebar (m)') ?>
							<input type="text" class="box" size="15" value="<?=$nutrition['KebunLebar']?>"/>
							m2
							<input type="text" disabled="disabled" class="box_disabled" size="15"
								   value="<?=($nutrition['KebunPanjang'] * $nutrition['KebunLebar'])?>"/></td>
					</tr>
					<tr>
						<td><?php echo lang('Jenis sayur (yang sudah direkomendasikan) yang sudah ditanam di kebun (pilihan ganda)') ?> :</td>
					</tr>
					<tr>
						<td style="border-bottom:1px solid #000000;">
							<input type="checkbox" name="KbBayam"
								   id="checkbox" <?=$nutrition['KbBayam'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox"><?php echo lang('Bayam') ?></label>
							&nbsp;<input type="checkbox" name="KbCabai"
										 id="checkbox" <?=$nutrition['KbCabai'] == '1' ? 'checked="yes"' : ''?>
										 disabled="true"/>
							<label for="checkbox"><?php echo lang('Cabai') ?></label>
							&nbsp;<input type="checkbox" name="KbKacangPanjang"
										 id="checkbox" <?=$nutrition['KbKacangPanjang'] == '1' ? 'checked="yes"' : ''?>
										 disabled="true"/>
							<label for="checkbox"><?php echo lang('Kacang Panjang') ?></label>
							&nbsp;<input type="checkbox" name="KbKangkung"
										 id="checkbox" <?=$nutrition['KbKangkung'] == '1' ? 'checked="yes"' : ''?>
										 disabled="true"/>
							<label for="checkbox"><?php echo lang('Kangkung') ?></label>
							&nbsp;<input type="checkbox" name="KbSawi"
										 id="checkbox" <?=$nutrition['KbSawi'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox"><?php echo lang('Sawi') ?></label>
							&nbsp;<input type="checkbox" name="KbTerong"
										 id="checkbox" <?=$nutrition['KbTerong'] == '1' ? 'checked="yes"' : ''?>
										 disabled="true"/>
							<label for="checkbox"><?php echo lang('Terong') ?></label>
							&nbsp;<input type="checkbox" name="KbTomat"
										 id="checkbox" <?=$nutrition['KbTomat'] == '1' ? 'checked="yes"' : ''?>
										 disabled="true"/>
							<label for="checkbox"><?php echo lang('Tomat') ?></label>
							&nbsp;</td>
					</tr>
					<tr>
						<td><?php echo lang('Jenis Hewan peliharaan') ?> :&nbsp;
							<input type="checkbox" name="KbKambing"
								   id="checkbox2" <?=$nutrition['KbKambing'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox2"><?php echo lang('Kambing') ?></label>
							&nbsp; <input type="checkbox" name="KbSapi"
										  id="checkbox2" <?=$nutrition['KbSapi'] == '1' ? 'checked="yes"' : ''?>
										  disabled="true"/>
							<label for="checkbox2"><?php echo lang('Sapi') ?></label>
							&nbsp; <input type="checkbox" name="KbBebek"
										  id="checkbox2" <?=$nutrition['KbBebek'] == '1' ? 'checked="yes"' : ''?>
										  disabled="true"/>
							<label for="checkbox2"><?php echo lang('Bebek') ?></label>
							&nbsp; <input type="checkbox" name="KbAyam"
										  id="checkbox2" <?=$nutrition['KbAyam'] == '1' ? 'checked="yes"' : ''?>
										  disabled="true"/>
							<label for="checkbox2"><?php echo lang('Ayam') ?></label>
							&nbsp; <input type="checkbox" name="KbIkan"
										  id="checkbox2" <?=$nutrition['KbIkan'] == '1' ? 'checked="yes"' : ''?>
										  disabled="true"/>
							<label for="checkbox2"><?php echo lang('Ikan') ?></label>
							&nbsp;</td>
					</tr>
				</table>
			</div>
			<div class="subject_box" id="KebunKomersil">
				<table width="100%" cellspacing="0" cellpadding="2" border="0" class="table">
					<tbody>
					<tr>
						<td class="header"><?php echo lang('Kebun Komersial') ?></td>
					</tr>
					<tr>
						<td style="border-bottom:1px solid #000000;"><?php echo lang('Ukuran kebun sayuran : Panjang (m)') ?>
							<input type="text" class="box" size="15" value="<?php echo $nutrition['ComKebunPanjang'] ?>">
							<?php echo lang('Lebar (m)') ?>
							<input type="text" class="box" size="15" value="<?php echo $nutrition['ComKebunLebar'] ?>">
							m2
							<input type="text" disabled="disabled" class="box_disabled" size="15" value="0"></td>
					</tr>
					<tr>
						<td><?php echo lang('Jenis sayur (yang sudah direkomendasikan) yang sudah ditanam di kebun (pilihan ganda)') ?> :</td>
					</tr>
					<tr>
						<td style="border-bottom:1px solid #000000;">
							<input type="checkbox" <?php echo $nutrition['ComKbBayam'] == '1' ? 'checked="checked"' : ''?> name="KbBayam" id="checkbox" disabled="true"><label for="checkbox"><?php echo lang('Bayam') ?></label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbCabai'] == '1' ? 'checked="checked"' : ''?> name="KbCabai" id="checkbox" disabled="true"><label for="checkbox"><?php echo lang('Cabai') ?></label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbKacangPanjang'] == '1' ? 'checked="checked"' : ''?> name="KbKacangPanjang" id="checkbox" disabled="true"><label for="checkbox"><?php echo lang('Kacang Panjang') ?></label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbKangkung'] == '1' ? 'checked="checked"' : ''?> name="KbKangkung" id="checkbox" disabled="true"><label for="checkbox"><?php echo lang('Kangkung') ?></label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbSawi'] == '1' ? 'checked="checked"' : ''?> name="KbSawi" id="checkbox" disabled="true"><label for="checkbox"><?php echo lang('Sawi') ?></label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbTerong'] == '1' ? 'checked="checked"' : ''?> name="KbTerong" id="checkbox" disabled="true"><label for="checkbox"><?php echo lang('Terong') ?></label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbTomat'] == '1' ? 'checked="checked"' : ''?> name="KbTomat" id="checkbox" disabled="true"><label for="checkbox"><?php echo lang('Tomat') ?></label>
							&nbsp;
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="subject_box" id="IDDSScorecard">
				<table class="table2" width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="7" class="header" style="font-weight:bold;"><?php echo lang('IDDS Scorecard - apa yang anda konsumsi hari kemarin (pilihan ganda)') ?> ?
						</td>
					</tr>
					<tr>
						<td colspan="2" style="font-weight:bold;"><?php echo lang('Karbohidrat') ?></td>
						<td width="13%"><span>
			  <input type="checkbox" name="aSagu" id="checkbox3" <?=$nutrition['aSagu'] == '1' ? 'checked="yes"' : ''?>
					 disabled="true"/>
			  <label for="checkbox3"><?php echo lang('Sagu') ?></label>
		  &nbsp;</td>
						<td width="21%">
							<input type="checkbox" name="aNasi"
								   id="checkbox4" <?=$nutrition['aNasi'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox4"><?php echo lang('Nasi') ?></label>
							&nbsp;</td>
						<td width="17%">
							<input type="checkbox" name="aMie"
								   id="checkbox5" <?=$nutrition['aMie'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox5"><?php echo lang('Mie') ?></label>
							&nbsp;</td>
						<td width="11%">
							<input type="checkbox" name="aJagung"
								   id="checkbox6" <?=$nutrition['aJagung'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox6"><?php echo lang('Jagung') ?></label>
							&nbsp;</td>
						<td width="9%">
							<input type="checkbox" name="aRoti"
								   id="checkbox7" <?=$nutrition['aRoti'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox7"><?php echo lang('Roti') ?></label>
							&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" style="font-weight:bold;"><?php echo lang('Umbi-umbian (Vitamin A)') ?></td>
						<td>
							<input type="checkbox" name="bUbiJalarKuning"
								   id="checkbox8" <?=$nutrition['bUbiJalarKuning'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<label for="checkbox8"><?php echo lang('Ubi Jalar Kuning') ?></label>
							&nbsp;</td>
						<td>
							<input type="checkbox" name="bSingkongKuning"
								   id="checkbox9" <?=$nutrition['bSingkongKuning'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<label for="checkbox9"><?php echo lang('Singkong Kuning') ?></label>
							&nbsp;</td>
						<td>
							<input type="checkbox" name="bWortel"
								   id="checkbox10" <?=$nutrition['bWortel'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox10"><?php echo lang('Wortel') ?></label>
							&nbsp;</td>
						<td>
							<input type="checkbox" name="bLabu"
								   id="checkbox11" <?=$nutrition['bLabu'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox11"><?php echo lang('Labu') ?></label>
							&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" style="font-weight:bold;"><?php echo lang('Umbi-umbian') ?></td>
						<td>
							<input type="checkbox" name="cUbiJalarPutih"
								   id="checkbox12" <?=$nutrition['cUbiJalarPutih'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<label for="checkbox12"><?php echo lang('Ubi Jalar Putih') ?></label>
						</td>
						<td>
							<input type="checkbox" name="cSingkongPutih"
								   id="checkbox13" <?=$nutrition['cSingkongPutih'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<label for="checkbox13"><?php echo lang('Singkong Putih') ?></label></td>
						<td>
							<input type="checkbox" name="cTalas"
								   id="checkbox14" <?=$nutrition['cTalas'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox14"><?php echo lang('Talas') ?> </label></td>
						<td>
							<input type="checkbox" name="cKentang"
								   id="checkbox15" <?=$nutrition['cKentang'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<label for="checkbox15"><?php echo lang('Kentang') ?></label></td>
					</tr>
					<tr>
						<td width="17%" style="font-weight:bold;"><?php echo lang('Sayur hijau') ?></td>
						<td width="12%">
							<input type="checkbox" name="dBayam"
								   id="checkbox16" <?=$nutrition['dBayam'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Bayam') ?>
						</td>
						<td>
							<input type="checkbox" name="dDaunMelinjo"
								   id="checkbox17" <?=$nutrition['dDaunMelinjo'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<?php echo lang('Daun Melinjo') ?>
						</td>
						<td>
							<input type="checkbox" name="dDaunPepaya"
								   id="checkbox18" <?=$nutrition['dDaunPepaya'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<?php echo lang('Daun Pepaya') ?>
						</td>
						<td>
							<input type="checkbox" name="dDaunSingkong"
								   id="checkbox19" <?=$nutrition['dDaunSingkong'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<?php echo lang('Daun Singkong') ?>
						</td>
						<td>
							<input type="checkbox" name="dKangkung"
								   id="checkbox20" <?=$nutrition['dKangkung'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Kangkung') ?>
						</td>
						<td>
							<input type="checkbox" name="dSawi"
								   id="checkbox21" <?=$nutrition['dSawi'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Sawi') ?>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;"><?php echo lang('Sayur yang lain') ?></td>
						<td>
							<input type="checkbox" name="eKacangPanjang"
								   id="checkbox22" <?=$nutrition['eKacangPanjang'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<?php echo lang('Kacang Panjang') ?>
						</td>
						<td>
							<input type="checkbox" name="eTomat"
								   id="checkbox23" <?=$nutrition['eTomat'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Tomat') ?>
						</td>
						<td>
							<input type="checkbox" name="eTerong"
								   id="checkbox24" <?=$nutrition['eTerong'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Terong') ?>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;"><?php echo lang('Buah-buah (Vitamin A)') ?></td>
						<td>
							<input type="checkbox" name="fJambuMerah"
								   id="checkbox25" <?=$nutrition['fJambuMerah'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<?php echo lang('Jambu Merah') ?>
						</td>
						<td>
							<input type="checkbox" name="fMangga"
								   id="checkbox26" <?=$nutrition['fMangga'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Mangga') ?>
						</td>
						<td>
							<input type="checkbox" name="fPepaya"
								   id="checkbox27" <?=$nutrition['fPepaya'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Pepaya') ?>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">Buah-buah yang lain</td>
						<td>
							<input type="checkbox" name="gJambuAir"
								   id="checkbox28" <?=$nutrition['gJambuAir'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Jambu Air') ?>
						</td>
						<td>
							<input type="checkbox" name="gKelapa"
								   id="checkbox29" <?=$nutrition['gKelapa'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Kelapa') ?>
						</td>
						<td>
							<input type="checkbox" name="gPisang"
								   id="checkbox30" <?=$nutrition['gPisang'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Pisang') ?>
						</td>
						<td>
							<input type="checkbox" name="gRambutan"
								   id="checkbox31" <?=$nutrition['gRambutan'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Rambutan') ?>
						</td>
						<td>
							<input type="checkbox" name="gSemangka"
								   id="checkbox32" <?=$nutrition['gSemangka'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Semangka') ?>
						</td>
						<td>
							<input type="checkbox" name="gSalak"
								   id="checkbox33" <?=$nutrition['gSalak'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Salak') ?>
						</td>
					</tr>
					<tr>
						<td colspan="5" style="font-weight:bold;">Daging jeroan (zat besi)</td>
						<td>
							<input type="checkbox" name="hJeroan"
								   id="checkbox34" <?=$nutrition['hJeroan'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Jeroan') ?>
						</td>
						<td>
							<input type="checkbox" name="hHati"
								   id="checkbox35" <?=$nutrition['hHati'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Hati') ?>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;"><?php echo lang('Daging') ?></td>
						<td>
							<input type="checkbox" name="iAyam"
								   id="checkbox36" <?=$nutrition['iAyam'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Ayam') ?>
						</td>
						<td>
							<input type="checkbox" name="iBebek"
								   id="checkbox37" <?=$nutrition['iBebek'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Bebek') ?>
						</td>
						<td>
							<input type="checkbox" name="iKambing"
								   id="checkbox38" <?=$nutrition['iKambing'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Kambing') ?>
						</td>
						<td>
							<input type="checkbox" name="iKerbau"
								   id="checkbox39" <?=$nutrition['iKerbau'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Kerbau') ?>
						</td>
						<td>
							<input type="checkbox" name="iSapi"
								   id="checkbox40" <?=$nutrition['iSapi'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Sapi') ?>
						</td>
						<td>
							<input type="checkbox" name="iLainnya"
								   id="checkbox41" <?=$nutrition['iLainnya'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Lainnya') ?>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="font-weight:bold;"><?php echo lang('Telur') ?></td>
						<td>
							<input type="checkbox" name="jAyam"
								   id="checkbox42" <?=$nutrition['jAyam'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Ayam') ?>
						</td>
						<td>
							<input type="checkbox" name="jBebek"
								   id="checkbox43" <?=$nutrition['jBebek'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Bebek') ?>
						</td>
						<td>
							<input type="checkbox" name="jEntok"
								   id="checkbox44" <?=$nutrition['jEntok'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Entok') ?>
						</td>
						<td>
							<input type="checkbox" name="jPuyuh"
								   id="checkbox45" <?=$nutrition['jPuyuh'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Puyuh') ?>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;"><?php echo lang('Ikan dan hasil laut') ?></td>
						<td>
							<input type="checkbox" name="kCumiCumi"
								   id="checkbox46" <?=$nutrition['kCumiCumi'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Cumi Cumi') ?>
						</td>
						<td>
							<input type="checkbox" name="kIkan"
								   id="checkbox47" <?=$nutrition['kIkan'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Ikan') ?>
						</td>
						<td>
							<input type="checkbox" name="kIkanTeri"
								   id="checkbox48" <?=$nutrition['kIkanTeri'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('IkanTeri') ?>
						</td>
						<td>
							<input type="checkbox" name="kKepiting"
								   id="checkbox49" <?=$nutrition['kKepiting'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Kepiting') ?>
						</td>
						<td>
							<input type="checkbox" name="kKerang"
								   id="checkbox50" <?=$nutrition['kKerang'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Kerang') ?>
						</td>
						<td>
							<input type="checkbox" name="kUdang"
								   id="checkbox51" <?=$nutrition['kUdang'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Udang') ?>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;"><?php echo lang('Kacang-kacangan') ?></td>
						<td>
							<input type="checkbox" name="lAirTahuSusuKedelai"
								   id="checkbox52" <?=$nutrition['lAirTahuSusuKedelai'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<?php echo lang('Air Tahu Susu Kedelai') ?>
						</td>
						<td>
							<input type="checkbox" name="lSausKacang"
								   id="checkbox53" <?=$nutrition['lSausKacang'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<?php echo lang('Saus Kacang') ?>
						</td>
						<td>
							<input type="checkbox" name="lTahu"
								   id="checkbox54" <?=$nutrition['lTahu'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Tahu') ?>
						</td>
						<td>
							<input type="checkbox" name="lTempe"
								   id="checkbox55" <?=$nutrition['lTempe'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Tempe') ?>
						</td>
						<td>
							<input type="checkbox" name="checkbox55"
								   id="checkbox55" <?=$nutrition['lKacang'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Kacang Tanah/Mente') ?>
						</td>
						<td>
							<input type="checkbox" name="checkbox55"
								   id="checkbox55" <?=$nutrition['lKwaci'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Kwaci') ?>
						</td>
					</tr>
					<tr>
						<td colspan="5" style="font-weight:bold;"><?php echo lang('Susu dan produk olahannya') ?></td>
						<td>
							<input type="checkbox" name="mKeju"
								   id="checkbox56" <?=$nutrition['mKeju'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Keju') ?>
						</td>
						<td>
							<input type="checkbox" name="mSusu"
								   id="checkbox57" <?=$nutrition['mSusu'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Susu') ?>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;"><?php echo lang('Minyak/lemak') ?></td>
						<td>
							<input type="checkbox" name="nMinyakGoreng"
								   id="checkbox58" <?=$nutrition['nMinyakGoreng'] == '1' ? 'checked="yes"' : ''?>
								   disabled="true"/>
							<?php echo lang('Minyak Goreng') ?>
						</td>
						<td>
							<input type="checkbox" name="nMentega"
								   id="checkbox59" <?=$nutrition['nMentega'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Mentega') ?>
						</td>
						<td>
							<input type="checkbox" name="nSantan"
								   id="checkbox60" <?=$nutrition['nSantan'] == '1' ? 'checked="yes"' : ''?> disabled="true"/>
							<?php echo lang('Santan') ?>
						</td>
					</tr>
					<tr>
						<td colspan="7" style="font-weight:bold;"><?php echo lang('Total Nilai (Satu kelompok pertanyaan yang dijawab mendapatkan nilai satu, maks 9)')?>
							<input type="text" class="box" size="25" value="<?=$nutrition['Score']?>"/>
						</td>
					</tr>
				</table>

			</div>
		</div>
		<div class="page-break"></div>
		<div class="page">
			<div class="subject_box" id="Mother">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table2">
					<tbody>
					<tr>
						<td colspan="7" class="header" style="font-weight:bold;"><?php echo lang('Mothers Who Have Childrens Below 5 Years Old') ?>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;" colspan="2"><?php echo lang('Do You Have Children bellow 5 Years Old') ?> ?</td>
						<td width="25%" colspan="1">
							<input type="radio" <?php echo $nutrition['HaveChildren'] == '1' ? 'checked="checked"' : ''?> name="aJagung" id="radio" disabled="true">
							<label for="checkbox6"><?php echo lang('Punya') ?></label>
							&nbsp;</td>
						<td width="25%" colspan="1">
							<input type="radio" <?php echo $nutrition['HaveChildren'] == '2' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
							<label for="checkbox7"><?php echo lang('Tidak Punya') ?></label>
							&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">1. <?php echo lang('Makanan apa yang diberikan kepada anak dari usia 0-6 bulan') ?>
							?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) <?php echo lang('Hanya diberikan ASI') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) <?php echo lang('ASI dan Bubur bayi/makanan lainnya') ?></label>
									</td>
								</tr>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '3' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">c) <?php echo lang('ASI dan Susu Formula') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '4' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">d) <?php echo lang('Susu Formula') ?></label>
									</td>
								</tr>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '5' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">e) <?php echo lang('Hanya Bubur/Pisang') ?></label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;" colspan="4">2. <?php echo lang('Berapa lama anak Ibu yang terakhir diberikan ASI') ?> ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '1' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">a) <?php echo lang('Tidak pernah') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '2' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">b) <?php echo lang('0-3 bulan') ?></label>
									</td>
								</tr>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '3' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">c) <?php echo lang('0-6 bulan') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '4' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">d) <?php echo lang('0-12 bulan') ?></label>
									</td>
								</tr>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '5' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">e) <?php echo lang('0-24 bulan') ?></label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">3. <?php echo lang('Jika tidak pernah atau kurang dari 3 bulan, kenapa') ?> ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['Children3MonthASI'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) <?php echo lang('Saya dilarang untuk memberikan ASI') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['Children3MonthASI'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) <?php echo lang('ASI saya tidak cukup/tidak ada') ?></label>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['Children3MonthASI'] == '3' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">c) <?php echo lang('ASI tidak penting bagi bayi') ?></label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;" colspan="4">4. <?php echo lang('Berapa kali Ibu memberikan ASI kepada anak yang berumur 0-6 bulan') ?>
							 ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveASI'] == '1' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">a) <?php echo lang('0-2 kali') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveASI'] == '2' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">b) <?php echo lang('3-4 kali') ?></label>
									</td>
								</tr>
								<tr>
									<td style="border:0px!important" colspan="2">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveASI'] == '3' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">c) <?php echo lang('lebih dari 5 kali') ?></label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">5. <?php echo lang('Berapa kali Ibu memberikan makanan kepada anak yang berumur 6-24 bulan') ?>
							 ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveMeal'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) <?php echo lang('0-2 kali') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveMeal'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) <?php echo lang('3-4 kali') ?></label>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveMeal'] == '3' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">c) <?php echo lang('lebih dari 5 kali') ?></label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;" colspan="4">6. <?php echo lang('Apakah Ibu memberikan kolestrum kepada bayi sejam setelah Bayi lahir') ?>
							 ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenGiveKolestrum'] == '1' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">a) <?php echo lang('Ya') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenGiveKolestrum'] == '2' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">b) <?php echo lang('Tidak') ?></label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">7. <?php echo lang('Apakah Ibu pernah hamil dalam 2 tahun terakhir') ?> ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['MotherPregnant2Years'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) <?php echo lang('Ya') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['MotherPregnant2Years'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) <?php echo lang('Tidak') ?></label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">8. <?php echo lang('Waktu Ibu hamil, apakah Ibu selalu makan seperti biasanya') ?> ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['MotherPregnantEat'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) <?php echo lang('Ya') ?></label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['MotherPregnantEat'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) <?php echo lang('Tidak') ?></label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="subject_box">
				<table width="100%" border="0" class="table">
					<tr>
						<td><p>&nbsp;</p>

							<p>&nbsp;</p></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><?php echo lang('Nama Petani/Peserta') ?></td>
						<td><?php echo lang('Nama Pewawancara') ?></td>
					</tr>
					<tr>
						<td><?=$data['FarmerName']?><?=$family['AnggotaName'] == '' ?: ' / ' . $family['AnggotaName']?></td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>

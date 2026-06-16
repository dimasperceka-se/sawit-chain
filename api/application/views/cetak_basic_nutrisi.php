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
			font: 8pt "verdana";
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
            font-size: 9px;
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
            font-size: 9px;
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
            padding-top: 9px;
            /*height: 60px;*/
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            color: #000000;
            /*background: url(images/templatemo_header_bg.gif) no-repeat;*/

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
		.page-break {
			page-break-before: always;
		}
    </style>
</head>
	<body>
		<div class="page">
			<div id="title_header_box" class="logos" style="height:100px; margin-top:-15px;">
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
						<td align="center" style="vertical-align:middle;text-decoration:underline;">N1 - Nutrition Basic Data</td>
					</tr>
				</table>
			</div>
			<div class="subject_box">
				<table width="100%" class="table" cellspacing="0" cellpadding="2">
					<tr>
						<input style="" type="text" class="box" size="46" value="<?=$data['FarmerName']?>"/>
						<input style="float:left;" type="text" class="box" size="45" value="<?=$data['FarmerName']?>"/>
						<td colspan="2" class="header" align="left">Data Umum Petani Kakao</td>
						<td class="header" align="center">CPG
							- <?=($data['CPGid'] != '') ? $data['CPGid'] : $data['CPGid']?></td>
						<td class="header" align="right">PK
							- <?=($data['FarmerID'] != '') ? $data['FarmerID'] : $data['FarmerID']?></td>
					</tr>
					<tr>
						<td>Nama Petani</td>
						<td colspan="3">
							<input type="text" class="box" size="50" value="<?=$data['FarmerName']?>"/></td>
					</tr>
					<tr>
						<td>Alamat</td>
						<td colspan="3"><input type="text" class="box" size="50" value="<?=$data['Address']?>"/></td>
					</tr>
					<tr>
						<td>Desa</td>
						<td><input type="text" class="box" size="25" value="<?=$data['Desa']?>"/></td>
						<td>Kecamatan</td>
						<td><input type="text" class="box" size="25" value="<?=$data['Kecamatan']?>"/></td>
					</tr>
					<tr>
						<td>Kabupaten</td>
						<td><input type="text" class="box" size="25" value="<?=$data['Kabupaten']?>"/></td>
						<td>Provinsi</td>
						<td><input type="text" class="box" size="25" value="<?=$data['Provinsi']?>"/></td>
					</tr>
				</table>
			</div>
			<div class="subject_box" id="DataUmumPeserta">
				<table class="table" width="100%" border="0" cellspacing="2" cellpadding="2">
					<tr>
						<td colspan="2" class="header" style="padding:0;margin:0;">Data Umum Peserta Pelatihan Nutrisi</td>
					</tr>
					<tr>
						<td>Tanggal survey</td>
						<td style="padding:0;margin:0;"><input type="text" class="box" size="25"
															   value="<?=$nutrition['DateInterview']?>"/></td>
					</tr>
					<tr>
						<td>Orang yang sama dengan petani kakao?</td>
						<td class="box"><input type="radio" name="Orang<?=$data['FarmerID']?>" id="Orang1"
											   value="radio" <?=$family['FamilyID'] == '' ? 'checked="checked"' : ''?>/>
							<label for="radio">Ya
								<input type="radio" name="Orang<?=$data['FarmerID']?>" id="Orang2"
									   value="radio2" <?=$family['FamilyID'] == '' ? '' : 'checked="checked"'?>/>
								Tidak</label></td>
					</tr>
					<tr>
						<td>Hubungan Keluarga</td>
						<td class="box"><input type="radio" name="Hubungan<?=$data['FarmerID']?>" id="Hubungan1"
											   value="radio" <?=$family['HubunganKeluarga'] == 1 ? 'checked="checked"' : ''?>/>
							<label for="radio3">Suami/Istri
								<input type="radio" name="Hubungan<?=$data['FarmerID']?>" id="Hubungan2"
									   value="radio2" <?=$family['HubunganKeluarga'] == 2 ? 'checked="checked"' : ''?>/>
								Anak
								<input type="radio" name="Hubungan<?=$data['FarmerID']?>" id="Hubungan3"
									   value="radio2" <?=$family['HubunganKeluarga'] == 3 ? 'checked="checked"' : ''?>/>
								Lain-lain
							</label></td>
					</tr>
					<tr>
						<td>Nama Peserta Pelatihan Nutrisi</td>
						<td style="padding:0;margin:0;"><input type="text" class="box" size="25"
															   value="<?=$family['AnggotaName']?>"/></td>
					</tr>
					<tr>
						<td>Jenis Kelamin</td>
						<td class="box"><input type="radio" name="Kelamin<?=$data['FarmerID']?>" id="Kelamin1"
											   value="radio" <?=$family['AnggotaGender'] == '1' ? 'checked="checked"' : ''?>/>
							<label for="radio6">Laki-laki
								<input type="radio" name="Kelamin<?=$data['FarmerID']?>" id="Kelamin2"
									   value="radio2" <?=$family['AnggotaGender'] == '2' ? 'checked="checked"' : ''?>/>
								Perempuan
							</label></td>
					</tr>
					<tr>
						<td>Tahun Lahir</td>
						<td style="padding:0;margin:0;"><input type="text" class="box" size="25"
															   value="<?=$family['AnggotaAge']?>"/></td>
					</tr>
				</table>
			</div>
			<div class="subject_box" id="KebunKeluarga">
				<table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
					<tr>
						<td class="header">Kebun Keluarga</td>
					</tr>
					<tr>
						<td style="border-bottom:1px solid #000000;">Ukuran kebun sayuran : Panjang (m)
							<input type="text" class="box" size="15"/>
							&nbsp;Lebar (m)
							<input type="text" class="box" size="15"/>
							&nbsp; m2
							<input type="text" disabled="disabled" class="box_disabled" size="15"/></td>
					</tr>
					<tr>
						<td>Jenis sayur (yang direkomendasikan) yang sudah ditanam di kebun (pilihan ganda) :</td>
					</tr>
					<tr>
						<td style="border-bottom:1px solid #000000;"><input type="checkbox" name="checkbox" id="checkbox"/>
							<label for="checkbox">Bayam</label>&nbsp;<input type="checkbox" name="checkbox" id="checkbox"/>
							<label for="checkbox">Cabai</label>
							&nbsp;<input type="checkbox" name="checkbox" id="checkbox"/>
							<label for="checkbox">Kacang Panjang</label>
							&nbsp;<input type="checkbox" name="checkbox" id="checkbox"/>
							<label for="checkbox">Kangkung</label>
							&nbsp;<input type="checkbox" name="checkbox" id="checkbox"/>
							<label for="checkbox">Sawi</label>
							&nbsp;<input type="checkbox" name="checkbox" id="checkbox"/>
							<label for="checkbox">Terong</label>
							&nbsp;<input type="checkbox" name="checkbox" id="checkbox"/>
							<label for="checkbox">Tomat</label>
							&nbsp;</td>
					</tr>
					<tr>
						<td>Jenis hewan peliharaan :&nbsp;
							<input type="checkbox" name="checkbox2" id="checkbox2"/>
							<label for="checkbox2">Kambing</label>
							&nbsp; <input type="checkbox" name="checkbox2" id="checkbox2"/>
							<label for="checkbox2">Sapi</label>
							&nbsp; <input type="checkbox" name="checkbox2" id="checkbox2"/>
							<label for="checkbox2">Bebek</label>
							&nbsp; <input type="checkbox" name="checkbox2" id="checkbox2"/>
							<label for="checkbox2">Ayam</label>
							&nbsp; <input type="checkbox" name="checkbox2" id="checkbox2"/>
							<label for="checkbox2">Ikan</label>
							&nbsp;</td>
					</tr>
				</table>
			</div>
			<div class="subject_box" id="KebunKomersil">
				<table width="100%" cellspacing="0" cellpadding="2" border="0" class="table">
					<tbody>
					<tr>
						<td class="header">Kebun Komersil</td>
					</tr>
					<tr>
						<td style="border-bottom:1px solid #000000;">Ukuran kebun sayuran : Panjang (m)
							<input type="text" class="box" size="15" value="<?php echo $nutrition['ComKebunPanjang'] ?>">
							Lebar (m)
							<input type="text" class="box" size="15" value="<?php echo $nutrition['ComKebunLebar'] ?>">
							m2
							<input type="text" disabled="disabled" class="box_disabled" size="15" value="0"></td>
					</tr>
					<tr>
						<td>Jenis sayur (yang direkomendasikan) yang sudah ditanam di kebun (pilihan ganda) :</td>
					</tr>
					<tr>
						<td style="border-bottom:1px solid #000000;">
							<input type="checkbox" <?php echo $nutrition['ComKbBayam'] == '1' ? 'checked="checked"' : ''?> name="KbBayam" id="checkbox" disabled="true"><label for="checkbox">Bayam</label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbCabai'] == '1' ? 'checked="checked"' : ''?> name="KbCabai" id="checkbox" disabled="true"><label for="checkbox">Cabai</label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbKacangPanjang'] == '1' ? 'checked="checked"' : ''?> name="KbKacangPanjang" id="checkbox" disabled="true"><label for="checkbox">Kacang Panjang</label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbKangkung'] == '1' ? 'checked="checked"' : ''?> name="KbKangkung" id="checkbox" disabled="true"><label for="checkbox">Kangkung</label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbSawi'] == '1' ? 'checked="checked"' : ''?> name="KbSawi" id="checkbox" disabled="true"><label for="checkbox">Sawi</label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbTerong'] == '1' ? 'checked="checked"' : ''?> name="KbTerong" id="checkbox" disabled="true"><label for="checkbox">Terong</label>
							&nbsp;
							<input type="checkbox" <?php echo $nutrition['ComKbTomat'] == '1' ? 'checked="checked"' : ''?> name="KbTomat" id="checkbox" disabled="true"><label for="checkbox">Tomat</label>
							&nbsp;
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="subject_box" id="IDDSScorecard">
				<table class="table2" width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="7" class="header" style="font-weight:bold;">IDDS Scorecard - apa yang anda konsumsi hari
							kemarin (pilihan ganda)?
						</td>
					</tr>
					<tr>
						<td colspan="2" style="font-weight:bold;">Karbohidrat</td>
						<td width="13%"><span>
			  <input type="checkbox" name="checkbox3" id="checkbox3"/>
			  <label for="checkbox3">Sagu</label>
		  &nbsp;</td>
						<td width="21%">
							<input type="checkbox" name="checkbox4" id="checkbox4"/>
							<label for="checkbox4">Nasi</label>
							&nbsp;</td>
						<td width="17%">
							<input type="checkbox" name="checkbox5" id="checkbox5"/>
							<label for="checkbox5">Mie</label>
							&nbsp;</td>
						<td width="11%">
							<input type="checkbox" name="checkbox6" id="checkbox6"/>
							<label for="checkbox6">Jagung</label>
							&nbsp;</td>
						<td width="9%">
							<input type="checkbox" name="checkbox7" id="checkbox7"/>
							<label for="checkbox7">Roti</label>
							&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" style="font-weight:bold;">Umbi-umbian (Vitamin A)</td>
						<td>
							<input type="checkbox" name="checkbox8" id="checkbox8"/>
							<label for="checkbox8">Ubi jalar (kuning/orange)</label>
							&nbsp;</td>
						<td>
							<input type="checkbox" name="checkbox9" id="checkbox9"/>
							<label for="checkbox9">Singkong (kuning)</label>
							&nbsp;</td>
						<td>
							<input type="checkbox" name="checkbox10" id="checkbox10"/>
							<label for="checkbox10">Wortel</label>
							&nbsp;</td>
						<td>
							<input type="checkbox" name="checkbox11" id="checkbox11"/>
							<label for="checkbox11">Labu</label>
							&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" style="font-weight:bold;">Umbi-umbian</td>
						<td>
							<input type="checkbox" name="checkbox12" id="checkbox12"/>
							<label for="checkbox12">Ubi jalar (putih/ungu)</label>
						</td>
						<td>
							<input type="checkbox" name="checkbox13" id="checkbox13"/>
							<label for="checkbox13">Singkong (putih)</label></td>
						<td>
							<input type="checkbox" name="checkbox14" id="checkbox14"/>
							<label for="checkbox14">Talas </label></td>
						<td>
							<input type="checkbox" name="checkbox15" id="checkbox15"/>
							<label for="checkbox15">Kentang</label></td>
					</tr>
					<tr>
						<td width="17%" style="font-weight:bold;">Sayur hijau</td>
						<td width="12%">
							<input type="checkbox" name="checkbox16" id="checkbox16"/>
							Bayam
						</td>
						<td>
							<input type="checkbox" name="checkbox17" id="checkbox17"/>
							Daun Melinjo
						</td>
						<td>
							<input type="checkbox" name="checkbox18" id="checkbox18"/>
							Daun Pepaya
						</td>
						<td>
							<input type="checkbox" name="checkbox19" id="checkbox19"/>
							Daun Singkong
						</td>
						<td>
							<input type="checkbox" name="checkbox20" id="checkbox20"/>
							Kangkung
						</td>
						<td>
							<input type="checkbox" name="checkbox21" id="checkbox21"/>
							Sawi
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">Sayur yang lain</td>
						<td>
							<input type="checkbox" name="checkbox22" id="checkbox22"/>
							Kacang Panjang
						</td>
						<td>
							<input type="checkbox" name="checkbox23" id="checkbox23"/>
							Tomat
						</td>
						<td>
							<input type="checkbox" name="checkbox24" id="checkbox24"/>
							Terong
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">Buah-buah (Vitamin A)</td>
						<td>
							<input type="checkbox" name="checkbox25" id="checkbox25"/>
							Jambu (merah)
						</td>
						<td>
							<input type="checkbox" name="checkbox26" id="checkbox26"/>
							Mangga
						</td>
						<td>
							<input type="checkbox" name="checkbox27" id="checkbox27"/>
							Pepaya
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">Buah-buah yang lain</td>
						<td>
							<input type="checkbox" name="checkbox28" id="checkbox28"/>
							Jambu air
						</td>
						<td>
							<input type="checkbox" name="checkbox29" id="checkbox29"/>
							Kelapa
						</td>
						<td>
							<input type="checkbox" name="checkbox30" id="checkbox30"/>
							Pisang
						</td>
						<td>
							<input type="checkbox" name="checkbox31" id="checkbox31"/>
							Rambutan
						</td>
						<td>
							<input type="checkbox" name="checkbox32" id="checkbox32"/>
							Semangka
						</td>
						<td>
							<input type="checkbox" name="checkbox33" id="checkbox33"/>
							Salak
						</td>
					</tr>
					<tr>
						<td colspan="5" style="font-weight:bold;">Daging jeroan (zat besi)</td>
						<td>
							<input type="checkbox" name="checkbox34" id="checkbox34"/>
							Jeroan
						</td>
						<td>
							<input type="checkbox" name="checkbox35" id="checkbox35"/>
							Hati
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">Daging</td>
						<td>
							<input type="checkbox" name="checkbox36" id="checkbox36"/>
							Ayam
						</td>
						<td>
							<input type="checkbox" name="checkbox37" id="checkbox37"/>
							Bebek
						</td>
						<td>
							<input type="checkbox" name="checkbox38" id="checkbox38"/>
							Kambing
						</td>
						<td>
							<input type="checkbox" name="checkbox39" id="checkbox39"/>
							Kerbau
						</td>
						<td>
							<input type="checkbox" name="checkbox40" id="checkbox40"/>
							Sapi
						</td>
						<td>
							<input type="checkbox" name="checkbox41" id="checkbox41"/>
							Lainnya
						</td>
					</tr>
					<tr>
						<td colspan="3" style="font-weight:bold;">Telur</td>
						<td>
							<input type="checkbox" name="checkbox42" id="checkbox42"/>
							Ayam
						</td>
						<td>
							<input type="checkbox" name="checkbox43" id="checkbox43"/>
							Bebek
						</td>
						<td>
							<input type="checkbox" name="checkbox44" id="checkbox44"/>
							Entok
						</td>
						<td>
							<input type="checkbox" name="checkbox45" id="checkbox45"/>
							Puyuh
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">Ikan dan hasil laut</td>
						<td>
							<input type="checkbox" name="checkbox46" id="checkbox46"/>
							Cumi-cumi
						</td>
						<td>
							<input type="checkbox" name="checkbox47" id="checkbox47"/>
							Ikan
						</td>
						<td>
							<input type="checkbox" name="checkbox48" id="checkbox48"/>
							Ikan teri
						</td>
						<td>
							<input type="checkbox" name="checkbox49" id="checkbox49"/>
							Kepiting
						</td>
						<td>
							<input type="checkbox" name="checkbox50" id="checkbox50"/>
							Kerang
						</td>
						<td>
							<input type="checkbox" name="checkbox51" id="checkbox51"/>
							Udang
						</td>
					</tr>
					<tr>
						<td colspan="1" style="font-weight:bold;">Kacang-kacangan</td>
						<td>
							<input type="checkbox" name="checkbox52" id="checkbox52"/>
							Air tahu/susu kedelai
						</td>
						<td>
							<input type="checkbox" name="checkbox53" id="checkbox53"/>
							Saus Kacang
						</td>
						<td>
							<input type="checkbox" name="checkbox54" id="checkbox54"/>
							Tahu
						</td>
						<td>
							<input type="checkbox" name="checkbox55" id="checkbox55"/>
							Tempe
						</td>
						<td>
							<input type="checkbox" name="checkbox55" id="checkbox55"/>
							Kacang Tanah/Mente
						</td>
						<td>
							<input type="checkbox" name="checkbox55" id="checkbox55"/>
							Kwaci
						</td>
					</tr>
					<tr>
						<td colspan="5" style="font-weight:bold;">Susu dan produk olahannya</td>
						<td>
							<input type="checkbox" name="checkbox56" id="checkbox56"/>
							Keju
						</td>
						<td>
							<input type="checkbox" name="checkbox57" id="checkbox57"/>
							Susu
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">Minyak/lemak</td>
						<td>
							<input type="checkbox" name="checkbox58" id="checkbox58"/>
							Minyak Goreng
						</td>
						<td>
							<input type="checkbox" name="checkbox59" id="checkbox59"/>
							Mentega
						</td>
						<td>
							<input type="checkbox" name="checkbox60" id="checkbox60"/>
							Santan
						</td>
					</tr>
					<tr>
						<td colspan="7" style="font-weight:bold;">Total Nilai
							<input type="text" class="box" size="25"/> Satu kelompok pertanyaan yang dijawab mendapatkan nilai
							satu (max. 14)
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
						<td colspan="7" class="header" style="font-weight:bold;">Pertanyaan Khusus untuk Ibu yang Memiliki Anak
							Dengan Usia Dibawah 5 Tahun
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;" colspan="2">Apakah Anda Memiliki Anak Dengan Usia Dibawah 5 Tahun ?</td>
						<td width="25%" colspan="1">
							<input type="radio" <?php echo $nutrition['HaveChildren'] == '1' ? 'checked="checked"' : ''?> name="aJagung" id="radio" disabled="true">
							<label for="checkbox6">Punya</label>
							&nbsp;</td>
						<td width="25%" colspan="1">
							<input type="radio" <?php echo $nutrition['HaveChildren'] == '2' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
							<label for="checkbox7">Tidak Punya</label>
							&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">1. Makanan apa yang diberikan kepada anak dari usia 0-6 bulan
							?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) Hanya diberikan ASI</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) ASI dan Bubur bayi/makanan lainnya</label>
									</td>
								</tr>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '3' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">c) ASI dan Susu Formula</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '4' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">d) Susu Formula</label>
									</td>
								</tr>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenMeal'] == '5' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">e) Hanya Bubur/Pisang</label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;" colspan="4">2. Berapa lama anak Ibu yang terakhir diberikan ASI ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '1' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">a) Tidak Pernah</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '2' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">b) 0-3 bulan</label>
									</td>
								</tr>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '3' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">c) 0-6 bulan</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '4' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">d) 0-12 bulan</label>
									</td>
								</tr>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenASI'] == '5' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">e) 0-24 bulan</label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">3. Jika tidak pernah atau kurang dari 3 bulan, kenapa ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['Children3MonthASI'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) Saya dilarang untuk memberikan ASI</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['Children3MonthASI'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) ASI saya tidak cukup/tidak ada</label>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['Children3MonthASI'] == '3' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">c) ASI tidak penting bagi bayi</label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;" colspan="4">4. Berapa kali Ibu memberikan ASI kepada anak yang berumur 0-6
							bulan ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveASI'] == '1' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">a) 0-2 kali</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveASI'] == '2' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">b) 3-4 kali</label>
									</td>
								</tr>
								<tr>
									<td style="border:0px!important" colspan="2">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveASI'] == '3' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">c) lebih dari 5 kali</label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">5. Berapa kali Ibu memberikan makanan kepada anak yang berumur
							6-24 bulan ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveMeal'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) 0-2 kali</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveMeal'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) 3-4 kali</label>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenNrGiveMeal'] == '3' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">c) lebih dari 5 kali</label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;" colspan="4">6. Apakah Ibu memberikan kolestrum kepada bayi sejam setelah
							Bayi lahir ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenGiveKolestrum'] == '1' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">a) Ya</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['ChildrenGiveKolestrum'] == '2' ? 'checked="checked"' : ''?> name="aRoti" id="checkbox7" disabled="true">
										<label for="checkbox7">b) Tidak</label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">7. Apakah Ibu pernah hamil dalam 2 tahun terakhir ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['MotherPregnant2Years'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) Ya</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['MotherPregnant2Years'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) Tidak</label>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="font-weight:bold;">8. Waktu Ibu hamil, apakah Ibu selalu makan seperti biasanya ?<br>
							<table width="100%">
								<tbody>
								<tr>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['MotherPregnantEat'] == '1' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">a) Ya</label>
									</td>
									<td width="50%" style="border:0px!important">
										<input type="radio" <?php echo $nutrition['MotherPregnantEat'] == '2' ? 'checked="checked"' : ''?> disabled="true" id="checkbox7" name="aRoti">
										<label for="checkbox7">b) Tidak</label>
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
						<td>Nama Petani/Peserta</td>
						<td>Nama Pewawancara</td>
					</tr>
					<tr>
						<td><?=$data['FarmerName']?><?=$family['AnggotaName'] == '' ? '' : ' / ' . $family['AnggotaName']?></td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
			<div class="page-break"></div>
		</div>
	</body>
</html>

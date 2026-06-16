<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>PPI Score 2012</title>
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
				background-color:#CCCCCC;
				font-weight:bold;
				padding:4;
			}
			.form_content {
				font-weight:bold;
			}
			.table {
				border:1px solid #000000;
				padding:4;
				font-size:9px;
			  background-color: #ffffff;

			}
			.table tr {
				border:1px solid #000000;
			}
			.table td {
				font-weight:bold;
			}
			.box {
				border:1px solid #000000;
			}
			.box_disabled {
				border:1px solid #000000;
				background-color:#CCCCCC;
			}

			.table2 {
				border:1px solid #000000;
				padding:2px;
				font-size:9px;
			  background-color: #ffffff;

			}
			.table2 td {
				font-weight:bold;
				padding:2px;
				border-bottom:1px solid #000000;
			}
			 #templatemo_header {
						clear: left;
						padding-top: 12px;
						padding-right: 5px;
						height: 100px;
						text-align: center;
						font-weight: bold;
						font-size: 18px;
						color: #000000;
						/*background: url(images/templatemo_header_bg.gif) no-repeat;*/
					}
				
			.subject_box {
			  background-color:#FFFFFF;
			  margin-top:5px;
			  color: #000000;
			}
			 #title_header_box {
				background-color:#FFFFFF;
				clear: left;
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
						<td align="center" style="vertical-align:middle;text-decoration:underline;font-size:16px">PPI 2012</span><br /><span style="text-decoration:underline;">Cocoa Farmer Poverty Scorecard</span></td>
					</tr>
				</table>
			</div>
			<div class="subject_box" id="DataUmumPetaniKakao" style="margin-top:15px;">
				<table width="100%" class="table" cellspacing="0" cellpadding="2" border="0">
				  <tr class="header">
					<td  width="20%">Data Umum</td><td align="left" width="30%">PK - <?=$data['FarmerID']!=''?$data['FarmerID']:$data['FarmerID']?></td>
					  <td width="20%">CPG - <?=$data['CPGid']!=''?$data['CPGid']:$data['CPGid']?></td><td  align="right" width="30%">Tanggal:<?=$ppi2012['DateInterview']?></td>
					</tr>
				  <tr>
					<td>Nama Petani</td>
					<td>
					  <input type="text" class="box" size="30" value="<?=$data['PersonNm']?>"/></td>
					<td>Kecamatan</td>
					<td><input type="text" class="box" size="30" value="<?=$data['Kecamatan']?>" /></td>
					</tr>
				  <tr>
					<td>Provinsi</td>
					<td><input type="text" class="box" size="30" value="<?=$data['Provinsi']?>"/></td>
					<td>Desa</td>
					<td><input type="text" class="box" size="30" value="<?=$data['Desa']?>"/></td>
					</tr>
				  <tr>
					<td>Kabupaten</td>
					<td><input type="text" class="box" size="30" value="<?=$data['Kabupaten']?>"/></td>
					<td>Alamat</td>
					<td><input type="text" class="box" size="30" value="<?=$data['alamat']?>"/></td>
				  </tr>
				</table>
				</div>
				<div class="subject_box" id="DataSurvey">
				<table width="100%" border="0" class="table" cellpadding="2" cellspacing="0">
				  <tr>
					<td class="header">Survey</td><td colspan="2" class="header"><?=$ppi2012['label_survey']?></td>
				  </tr>
				  <tr>
					<td width="52%" style="vertical-align:top;">1. Berapa jumlah anggota rumah tangga?</td>
					<td width="24%" style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;"><input type="radio" name="radio" id="radio" value="radio" />
					<label for="radio">A. Enam atau lebih<br />
					  <input type="radio" name="radio" id="radio2" value="radio" />
					B. Lima<br />
					<input type="radio" name="radio" id="radio3" value="radio" />
					C. Empat</label></td>
					<td width="24%" style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;"><input type="radio" name="radio" id="radio4" value="radio" />
					  <label for="radio4">D. Tiga<br />
						<input type="radio" name="radio" id="radio5" value="radio" />
						E. Dua<br />
				  <input type="radio" name="radio" id="radio6" value="radio" />
					F. Satu</label></td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">2. Apakah semua anggota rumah tangga      yang berusia 6 – 18 tahun masih    bersekolah?</td>
					<td colspan="2" style="border:1px solid #000000;"><input type="radio" name="radio" id="radio7" value="radio" />
					  <label for="radio7">A.Tidak ada anak berusia 6 – 18 tahun<br />
						<input type="radio" name="radio" id="radio8" value="radio" />
						B.Tidak<br />
				  <input type="radio" name="radio" id="radio9" value="radio" />
					B. Ya</label></td>
				  </tr>
				   <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">3. Apa tingkat pendidikan terakhir yang      diselesaikan oleh kepala rumah tangga    perempuan/istri?</td>
					<td colspan="2" style="border:1px solid #000000;"><p>
					  <input type="radio" name="radio" id="radio10" value="radio" />
					  <label for="radio10">A. Belum pernah bersekolah<br />
						<input type="radio" name="radio" id="radio11" value="radio" />
						B. SD/SDLB, Madrasah Ibtidaiyah, atau Paket A<br />
						<input type="radio" name="radio" id="radio12" value="radio" />
						C. SMP/SMPLB, Madrasah Tsanawiyah, atau Paket B</label><br />
					  <input type="radio" name="radio" id="radio13" value="radio" />
					  <label for="radio13">D. Tidak ada kepala rumah tangga perempuan/ istri<br />
						<input type="radio" name="radio" id="radio14" value="radio" />
						E. SMK<br />
				  <input type="radio" name="radio" id="radio15" value="radio" />
						F. SMA/SMLB, Madrasah Aliyah, atau Paket C</label><br />
				  <input type="radio" name="radio" id="radio15" value="radio" />
						G. D1, D2, D3/Sarjana Muda, D4, S1, S2, atau S3</p></td>
				  </tr>
				   <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">4. Apa status pekerjaan utama dari kepala      rumah tangga laki-laki/suami di minggu    terakhir?</td>
					<td colspan="2" style="border:1px solid #000000;"><input type="radio" name="radio" id="radio16" value="radio" />
					  <label for="radio16">A. Tidak ada kepala rumah tangga laki-laki/suami<br />
						<input type="radio" name="radio" id="radio17" value="radio" />
						B. Tidak bekerja atau pekerja keluarga/ pekerja tidak dibayar<br />
				  <input type="radio" name="radio" id="radio18" value="radio" />
						C. Pekerja bebas</label><br />
					  <input type="radio" name="radio" id="radio19" value="radio" />
					  <label for="radio19">D. Berusaha sendiri atau berusaha dibantu buruh tidak        tetap/buruh tidak dibayar<br />
						<input type="radio" name="radio" id="radio20" value="radio" />
						E. Buruh/karyawan/pengawai<br />
				  <input type="radio" name="radio" id="radio21" value="radio" />
					F. Berusaha dibantu buruh tetap/buruh dibayar</label><br /></td>
				  </tr>
				   <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">5. Jenis lantai terluas?</td>
					<td colspan="2" style="border:1px solid #000000;"><input type="radio" name="radio" id="radio22" value="radio" />
					  A. Tanah atau bambu
					  <label for="radio22"><br />
						<input type="radio" name="radio" id="radio23" value="radio" />
					B. Bukan tanah/bambu</label></td>
				  </tr>
				   <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">6. Jenis kloset/WC yang rumah tangga anda    miliki?</td>
					<td colspan="2" style="border:1px solid #000000;"><input type="radio" name="radio" id="radio24" value="radio" />
					  <label for="radio24">A. Tidak ada atau jamban cemplung/cubluk<br />
						<input type="radio" name="radio" id="radio25" value="radio" />
						B. Ada kloset, tapi tidak tersambung ke septic tank        (plengsengan)<br />
				  <input type="radio" name="radio" id="radio26" value="radio" />
					C. Leher angsa</label></td>
				  </tr>
				   <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">7. Apa jenis bahan bakar utama rumah    tangga?</td>
					<td colspan="2" style="border:1px solid #000000;"><input type="radio" name="radio" id="radio27" value="radio" />
				A. Kayu bakar, arang, briket<br />
				<input type="radio" name="radio" id="radio28" value="radio" />
				B. Gas/elpiji, minyak tanah, listrik atau lainnya</td>
				  </tr>
				   <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">8. Apakah rumah tangga memiliki tabung    gas 12kg atau lebih?</td>
					<td style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;"><input type="radio" name="radio" id="radio29" value="radio" />
				A. Tidak</td>
					<td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;"><input type="radio" name="radio" id="radio30" value="radio" />
					  B. Ya</td>
				  </tr>
				   <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">9. Apakah rumah tangga memiliki    kulkas/lemari es?</td>
					<td style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;"><input type="radio" name="radio" id="radio31" value="radio" />
				A. Tidak</td>
					<td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;"><input type="radio" name="radio" id="radio32" value="radio" />
					B. Ya</td>
				  </tr>
				   <tr>
					<td>&nbsp;</td>
					<td colspan="3">&nbsp;</td>
				  </tr>
				  <tr>
					<td style="vertical-align:top;">10. Apakah rumah tangga memiliki sepeda motor atau perahu motor?</td>
					<td style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;"><input type="radio" name="radio" id="radio33" value="radio" />
				A. Tidak</td>
					<td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;"><input type="radio" name="radio" id="radio34" value="radio" />
					B. Ya</td>
				  </tr>
				</table>
			</div>
		</div>
		<div class="page-break"></div>
	</body>
</html>

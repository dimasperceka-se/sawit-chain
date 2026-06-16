<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
            height: 27.5cm;
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
            padding: 4;
        }

        .form_content {
            font-weight: bold;
        }

        .table {
            border: 1px solid #000000;
            padding: 4;
            font-size: 9px;
            background-color: #ffffff;

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
            border: 1px solid #000000;
            padding: 2px;
            font-size: 9px;
            background-color: #ffffff;

        }

        .table2 td {
            font-weight: bold;
            padding: 2px;
            border-bottom: 1px solid #000000;
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
            background-color: #FFFFFF;
            margin-top: 5px;
            color: #000000;
        }

        #title_header_box {
            background-color: #FFFFFF;
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

                padding-bottom: 0;
            }

            .header {
                border: 90px #cccccc;
            }
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
                <td align="center" style="vertical-align:middle;text-decoration:underline;font-size:16px">PPI
                    2012</span>
                    <br/><span style="text-decoration:underline;"><?php echo lang('Cocoa Farmer Poverty Scorecard') ?></span>
                </td>
            </tr>
        </table>
    </div>
    <div class="subject_box" id="DataUmumPetaniKakao" style="margin-top:15px;">
        <table width="100%" class="table" cellspacing="0" cellpadding="2" border="0">
            <tr class="header">
                <td width="20%"><?php echo lang('Data Umum') ?></td>
                <td align="left" width="30%"><?php echo lang('PK') ?>
                    - <?= $data['FarmerID'] != '' ? $data['FarmerID'] : $data['FarmerID'] ?></td>
                <td width="20%"><?php echo lang('CPG') ?>
                    - <?= $data['CPGid'] != '' ? $data['CPGid'] : $data['CPGid'] ?></td>
                <td align="right" width="30%"><?php echo lang('Tanggal') ?>:<?= $data['InterviewDate'] ?></td>
            </tr>
            <tr>
                <td><?php echo lang('Nama Petani') ?></td>
                <td>
                    <input type="text" class="box" size="30" value="<?= $data['PersonNm'] ?>"/></td>
                <td><?php echo lang('Kecamatan') ?></td>
                <td><input type="text" class="box" size="30" value="<?= $data['Kecamatan'] ?>"/></td>
            </tr>
            <tr>
                <td><?php echo lang('Provinsi') ?></td>
                <td><input type="text" class="box" size="30" value="<?= $data['Provinsi'] ?>"/></td>
                <td><?php echo lang('Desa') ?></td>
                <td><input type="text" class="box" size="30" value="<?= $data['Desa'] ?>"/></td>
            </tr>
            <tr>
                <td><?php echo lang('Kabupaten') ?></td>
                <td><input type="text" class="box" size="30" value="<?= $data['Kabupaten'] ?>"/></td>
                <td><?php echo lang('Alamat') ?></td>
                <td><input type="text" class="box" size="30" value="<?= $data['alamat'] ?>"/></td>
            </tr>
        </table>
    </div>
    <div class="subject_box" id="DataSurvey">
        <table width="100%" border="0" class="table" cellpadding="2" cellspacing="0">
            <tr>
                <td class="header"><?php echo lang('Survey') ?></td>
                <td colspan="2" class="header"><?= $ppi2012['label_survey'] ?></td>
            </tr>
            <tr>
                <td width="52%" style="vertical-align:top;"><?php echo lang('1. Berapa jumlah anggota rumah tangga') ?>
                    ?
                </td>
                <td width="24%" style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;">
                    <input type="radio" name="Householdmembers1" id="radio" value="radio" <?= $ppi2012['Householdmembers'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <label for="radio"><?php echo lang('A. Enam atau lebih') ?><br/>
                        <input type="radio" name="Householdmembers2" id="radio2" value="radio" <?= $ppi2012['Householdmembers'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('B. Lima') ?><br/>
                        <input type="radio" name="Householdmembers3" id="radio3" value="radio" <?= $ppi2012['Householdmembers'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('C. Empat') ?></td>
                <td width="24%" style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;">
                    <input type="radio" name="Householdmembers4" id="radio4" value="radio" <?= $ppi2012['Householdmembers'] == '4' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <label for="radio4"><?php echo lang('D. Tiga') ?><br/>
                        <input type="radio" name="Householdmembers5" id="radio5" value="radio" <?= $ppi2012['Householdmembers'] == '5' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('E. Dua') ?><br/>
                        <input type="radio" name="Householdmembers6" id="radio6" value="radio" <?= $ppi2012['Householdmembers'] == '6' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('F. Satu') ?></label></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('2. Apakah semua anggota rumah tangga yang berusia 6 sampai 18 tahun masih bersekolah') ?>
                </td>
                <td colspan="2" style="border:1px solid #000000;">
                    <input type="radio" name="Schooling1" id="radio7" value="radio" <?= $ppi2012['Schooling'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <label for="radio7"><?php echo lang('A. Tidak ada anak usia 6-19 tahun') ?><br/>
                        <input type="radio" name="Schooling2" id="radio8" value="radio" <?= $ppi2012['Schooling'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                        B. <?php echo lang('Tidak') ?><br/>
                        <input type="radio" name="Schooling3" id="radio9" value="radio" <?= $ppi2012['Schooling'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
                        C. <?php echo lang('Ya') ?></label>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('3. Apa tingkat pendidikan terakhir yang diselesaikan oleh kepala rumah tangga perempuan/istri ') ?>?
                </td>
                <td colspan="2" style="border:1px solid #000000;"><p>
                        <input type="radio" name="Education1" id="radio10" value="radio" <?= $ppi2012['Education'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <label for="radio10"><?php echo lang('A. Belum pernah bersekolah') ?><br/>
                            <input type="radio" name="Education2" id="radio11" value="radio" <?= $ppi2012['Education'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                            <?php echo lang('B. SD/SDLB, Madrasah Ibtidaiyah, atau Paket A') ?><br/>
                            <input type="radio" name="Education3" id="radio12" value="radio" <?= $ppi2012['Education'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
                            <?php echo lang('C. SMP/SMPLB, Madrasah Tsanawiayh, atau Paket B') ?></label><br/>
                        <input type="radio" name="Education4" id="radio13" value="radio" <?= $ppi2012['Education'] == '4' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <label for="radio13"><?php echo lang('D. Tidak ada kepala rumah tangga perempuan/istri') ?><br/>
                            <input type="radio" name="Education5" id="radio14" value="radio" <?= $ppi2012['Education'] == '5' ? 'checked="checked"' : '' ?> disabled="true"/>
                            <?php echo lang('E. SMK') ?><br/>
                            <input type="radio" name="Education6" id="radio15" value="radio" <?= $ppi2012['Education'] == '6' ? 'checked="checked"' : '' ?> disabled="true"/>
                            <?php echo lang('F. SMA/SMALB, Madrasah Aliyah, atau Paket C') ?></label><br/>
                        <input type="radio" name="Education7" id="radio15" value="radio" <?= $ppi2012['Education'] == '7' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('G. D1, D2, D3/Sarjana Muda, D4, S1, S2, S3') ?></p></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('4. Apa status pekerjaan utama dari kepala rumah tangga laki-laki/suami di minggu terakhir') ?>?</td>
                <td colspan="2" style="border:1px solid #000000;">
                    <input type="radio" name="Employment1" id="radio16" value="radio" <?= $ppi2012['Employment'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <label for="radio16"><?php echo lang('A. Tidak ada kepala rumah tangga laki-laki/suami') ?><br/>
                        <input type="radio" name="Employment2" id="radio17" value="radio" <?= $ppi2012['Employment'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('B. Tidak bekerja atau pekerja keluarga/pekerja tidak dibayar') ?><br/>
                        <input type="radio" name="Employment3" id="radio18" value="radio" <?= $ppi2012['Employment'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('C. Pekerja bebas') ?></label>
                        <br/>
                    <input type="radio" name="Employment4" id="radio19" value="radio" <?= $ppi2012['Employment'] == '4' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <label for="radio19"><?php echo lang('D. Berusaha sendiri atau berusaha dibantu buruh tidak tetap/buruh tidak dibayar') ?><br/>
                        <input type="radio" name="Employment5" id="radio20" value="radio" <?= $ppi2012['Employment'] == '5' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('E. Buruh/karyawan/pegawai') ?><br/>
                        <input type="radio" name="Employment6" id="radio21" value="radio" <?= $ppi2012['Employment'] == '6' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('F. Berusaha dibantu buruh tetap/buruh dibayar') ?></label>
                        <br/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('5. Jenis lantai terluas') ?>?</td>
                <td colspan="2" style="border:1px solid #000000;">
                    <input type="radio" name="HouseFloor1" id="radio22" value="radio" <?= $ppi2012['HouseFloor'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('A. Tanah atau bambu') ?>
                    <label for="radio22"><br/>
                        <input type="radio" name="HouseFloor2" id="radio23" value="radio" <?= $ppi2012['HouseFloor'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('B. Bukan tanah/bambu') ?></label>
                        </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('6. Jenis kloset/WC yang rumah tangga anda miliki') ?>?</td>
                <td colspan="2" style="border:1px solid #000000;">
                    <input type="radio" name="ToiletFacility1" id="radio24" value="radio" <?= $ppi2012['ToiletFacility'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <label for="radio24"><?php echo lang('A. Tidak ada atau jamban cemplung/cebluk') ?><br/>
                        <input type="radio" name="ToiletFacility2" id="radio25" value="radio" <?= $ppi2012['ToiletFacility'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('B. Ada kloset, tapi tidak tersambung ke septic tank (plengsengan)') ?><br/>
                        <input type="radio" name="ToiletFacility3" id="radio26" value="radio" <?= $ppi2012['ToiletFacility'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('C. Leher Angsa') ?></label>
                        </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('7. Apa jenis bahan bakar utama rumah tangga') ?>?</td>
                <td colspan="2" style="border:1px solid #000000;">
                    <input type="radio" name="CookingFuel1" id="radio27" value="radio" <?= $ppi2012['CookingFuel'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('A. Kayu bakar, arang, briket') ?><br/>
                    <input type="radio" name="CookingFuel2" id="radio28" value="radio" <?= $ppi2012['CookingFuel'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('B. Gas/elpiji, minyak tanah, listrik, atau lainnya') ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('8. Apakah rumah tangga memiliki tabung gas 12 Kg atau lebih') ?>?</td>
                <td style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;">
                    <input type="radio" name="GasCylinder1" id="radio29" value="radio" <?= $ppi2012['GasCylinder'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    A. <?php echo lang('Tidak') ?>
                </td>
                <td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;">
                    <input type="radio" name="GasCylinder2" id="radio30" value="radio" <?= $ppi2012['GasCylinder'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    B. <?php echo lang('Ya') ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('9. Apakah rumah tangga memiliki kulkas/lemari es') ?>?</td>
                <td style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;">
                    <input type="radio" name="Refrigerator1" id="radio31" value="radio" <?= $ppi2012['Refrigerator'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    A. <?php echo lang('Tidak') ?>
                </td>
                <td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;">
                    <input type="radio" name="Refrigerator2" id="radio32" value="radio" <?= $ppi2012['Refrigerator'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    B. <?php echo lang('Ya') ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><?php echo lang('10. Apakah rumah tangga memiliki sepeda motor atau perahu motor') ?>?</td>
                <td style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;">
                    <input type="radio" name="Motorcycle1" id="radio33" value="radio" <?= $ppi2012['Motorcycle'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    A. <?php echo lang('Tidak') ?>
                </td>
                <td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;">
                    <input type="radio" name="Motorcycle2" id="radio34" value="radio" <?= $ppi2012['Motorcycle'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    B. <?php echo lang('Ya') ?>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>

<?php
/**
 * @Author: nikolius
 * @Date:   2017-03-03 13:19:58
 */
$baseurlnya = base_url();
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8"/>
    <title><?php echo lang('PPI Score')?></title>

    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/nutrition/nutrition.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/nutrition/nutrition-media.css" media="print"/>
    <script src="<?php echo $baseurlnya; ?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
</head>
<body>

<div class="page"> <!-- Halaman 1 Start -->


    <!-- LOGO ATAS (BEGIN) -->
    <table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
       <tr>
          <td width="20%" align="center" style="vertical-align:middle;">
             <img src="<?php echo base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
          for ($i=0;$i<count($logos);$i++) {
              if ($logos[$i]['Photo']!='') {
                  ?>
          <td height="60px" width="20%" align="center" style="vertical-align:middle;">
             <img src="<?php echo base_url() ?>images/Photo/<?php echo $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php

              }
          }
        ?>
       <td width="20%" align="center" style="vertical-align:middle;">
          <img src="<?php echo base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
       </td>
    </tr>
    </table>
    <br /><br />
    <!-- LOGO ATAS (END) -->


    <div style="background-color:#23BAB1;padding:8px;">
        <table class="noBorder tabelJudul" width="100%">
        <tr>
            <td width="35%" style="padding-right:5px;border-right: 1px dashed white;">
                <table width="100%">
                    <tr>
                        <td width="30%"><?php echo strtoupper(lang('Survey Nr'))?></td>
                        <td>
                            <?php
                                if($SurveyNr == "") $SurveyNr = "-";
                            ?>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $SurveyNr;?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper(lang('Date'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php if (!empty($survey['InterviewDate'])) echo date('Y-m-d', strtotime($survey['InterviewDate']))?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper(lang('Interviewer'))?></td>
                        <td>
                            <input type="text" style="width:100%;border: 1px solid white;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper(lang('Job Title'))?></td>
                        <td>
                            <input type="text" style="width:100%;border: 1px solid white;" />
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align:top;padding-left:14px;">
                <h2 class="mainTitle"><?php echo lang('Cocoa Farmer Poverty Scorecard')?></h2>
                <table width="100%">
                    <tr>
                        <td width="45%">
                            <span style="font-size:9.5px;"><?php echo strtoupper(lang('Farmer Group ID'))?></span><br />
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['CPGid']?>" />
                        </td>
                        <td width="55%">
                            <span style="font-size:9.5px;"><?php echo strtoupper(lang('Name Of Farmer Group'))?></span><br />
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['GroupName']?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-size:9.5px;"><?php echo strtoupper(lang('Farmer ID'))?></span><br />
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['FarmerID']?>" />
                        </td>
                        <td></td>
                    </tr>
                </table>
            </td>
            <td width="15%">
                <img src="<?php echo base_url() ?>index.php/farmer/qrcode_generator/<?php echo $farmer['FarmerID'];?>/" style="width:100%;" />
            </td>
        </tr>
        </table>
    </div>
    <br />

    <table class="noBorder" width="100%" style="margin-bottom:6px;">
    <tr>
        <td width="6%">
            <img src="<?php echo base_url()?>assets/css/nutrition/icon-farmer-data.png" width="35" />
        </td>
        <td width="94%" style="border-bottom: 1px dashed #23BAB1;">
            <h2 class="judulTabel"><?php echo strtoupper(lang('Farmer Basic Data'))?></h2>
        </td>
    </tr>
    </table>
    <table class="noBorder" width="100%">
    <tr>
        <td width="16%">
            <img src="<?php echo base_url().'image_process/resizeOtf?imagenya='.urlencode($farmer['Photo']).'&width=150&height=150'; ?>" />
        </td>
        <td width="22%" style="padding-left:7px;vertical-align: top;">
            <table width="100%">
            <tr>
                <td>
                    <span style="font-size:9px;"><?php echo lang('Farmer Name')?></span><br />
                    <input type="text" style="width:100%;" value="<?php echo $farmer['FarmerName']?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:9px;"><?php echo lang('Province')?></span><br />
                    <input type="text" style="width:100%;" value="<?php echo $farmer['Provinsi']?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:9px;"><?php echo lang('SubDistrict')?></span><br />
                    <input type="text" style="width:100%;" value="<?php echo $farmer['Kecamatan']?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:9px;"><?php echo lang('Dusun')?></span><br />
                    <input type="text" style="width:100%;" value="<?php echo $farmer['alamat']?>" />
                </td>
            </tr>
            </table>
        </td>
        <td width="20%" style="padding-left:4px;vertical-align: top;">
            <table width="100%">
            <tr>
                <td>
                    <?php
                    //pemisahan tgl lahir
                    $arrTemp = explode("-",$farmer['Birthdate']);
                    $tahunL = $arrTemp[0];
                    $bulanL = $arrTemp[1];
                    $tglL = $arrTemp[2];
                    ?>
                    <span style="font-size:9px;"><?php echo lang('Tanggal Lahir')?></span><br />
                    <!--<input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" />-->
                    <input type="text" style="width:40px;" value="<?php echo $tglL?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    //pemisahan kode2 lokasi
                    $provKode = substr($farmer['VillageID'],0,2);
                    $kabKode = substr($farmer['VillageID'],0,4);
                    $kecKode = substr($farmer['VillageID'],0,7);
                    ?>
                    <span style="font-size:9px;"><?php echo lang('Province Code')?></span><br />
                    <!--<input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" />-->
                    <input type="text" style="width:30px;" value="<?php echo $provKode?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:9px;"><?php echo lang('Sub District Code')?></span><br />
                    <!-- <input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" /> -->
                    <input type="text" style="width:90px;" value="<?php echo $kecKode?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:9px;"><?php echo lang('RT / RW')?></span><br />
                    <!-- <input type="text" class="inputSatuan" />&nbsp;|&nbsp;<input type="text" class="inputSatuan" /> -->
                    <input type="text" style="width:67px;" value="<?php echo $farmer['RtRw']?>" />
                </td>
            </tr>
            </table>
        </td>
        <td width="42%" style="vertical-align: top;">
            <table width="100%">
            <tr>
                <td width="48%">
                    <span style="font-size:9px;"><?php echo lang('Bulan Lahir')?></span><br />
                    <!-- <input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" /> -->
                    <input type="text" style="width:40px;" value="<?php echo $bulanL?>" />
                </td>
                <td width="2%"></td>
                <td width="50%">
                    <span style="font-size:9px;"><?php echo lang('Tahun Lahir')?></span><br />
                    <!-- <input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" /> -->
                    <input type="text" style="width:80px;" value="<?php echo $tahunL?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:9px;"><?php echo lang('District')?></span><br />
                    <input type="text" style="width:100%" value="<?php echo $farmer['Kabupaten']?>" />
                </td>
                <td></td>
                <td>
                    <span style="font-size:9px;"><?php echo lang('District Code')?></span><br />
                    <!-- <input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" /> -->
                    <input type="text" style="width:60px;" value="<?php echo $kabKode?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size:9px;"><?php echo lang('Village')?></span><br />
                    <input type="text" style="width:100%" value="<?php echo $farmer['Desa']?>" />
                </td>
                <td></td>
                <td>
                    <span style="font-size:9px;"><?php echo lang('Village Code')?></span><br />
                    <!-- <input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" />&nbsp;&nbsp;<input type="text" class="inputSatuan" /> -->
                    <input type="text" style="width:110px;" value="<?php echo $farmer['VillageID']?>" />
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3">
                    <span style="font-size:9px;"><?php echo lang('Mobile Phone')?></span><br />
                    <!-- <input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" />&nbsp;<input type="text" class="inputSatuanKecil" /> -->
                    <input type="text" style="width:50%;" value="<?php echo $farmer['HandPhone']?>" />
                </td>
            </tr>
            </table>
        </td>
    </tr>
    </table>
    <br />

    <table class="noBorder" width="100%" style="margin-bottom:6px;">
    <tr>
        <td width="6%">
            <img src="<?php echo base_url()?>assets/css/nutrition/icon-nutrition-garden.png" width="35" />
        </td>
        <td width="94%" style="border-bottom: 1px dashed #23BAB1;">
            <h2 class="judulTabel"><?php echo strtoupper(lang('Survey'))?></h2>
        </td>
    </tr>
    </table>

    <table width="100%" border="0" class="table" cellpadding="2" cellspacing="0">
        <tr>
            <td width="52%" style="vertical-align:top;"><?php echo lang('1. Berapa jumlah anggota rumah tangga') ?>
                ?
            </td>
            <td width="24%" style="border-top:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;">
                <input type="radio" name="Householdmembers1" id="radio" value="radio" <?php echo $survey['Householdmembers'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                <label for="radio"><?php echo lang('A. Enam atau lebih') ?><br/>
                    <input type="radio" name="Householdmembers2" id="radio2" value="radio" <?php echo $survey['Householdmembers'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('B. Lima') ?><br/>
                    <input type="radio" name="Householdmembers3" id="radio3" value="radio" <?php echo $survey['Householdmembers'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('C. Empat') ?></td>
            <td width="24%" style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;">
                <input type="radio" name="Householdmembers4" id="radio4" value="radio" <?php echo $survey['Householdmembers'] == '4' ? 'checked="checked"' : '' ?> disabled="true"/>
                <label for="radio4"><?php echo lang('D. Tiga') ?><br/>
                    <input type="radio" name="Householdmembers5" id="radio5" value="radio" <?php echo $survey['Householdmembers'] == '5' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('E. Dua') ?><br/>
                    <input type="radio" name="Householdmembers6" id="radio6" value="radio" <?php echo $survey['Householdmembers'] == '6' ? 'checked="checked"' : '' ?> disabled="true"/>
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
                <input type="radio" name="Schooling1" id="radio7" value="radio" <?php echo $survey['Schooling'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                <label for="radio7"><?php echo lang('A. Tidak ada anak usia 6-19 tahun') ?><br/>
                    <input type="radio" name="Schooling2" id="radio8" value="radio" <?php echo $survey['Schooling'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    B. <?php echo lang('Tidak') ?><br/>
                    <input type="radio" name="Schooling3" id="radio9" value="radio" <?php echo $survey['Schooling'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
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
                    <input type="radio" name="Education1" id="radio10" value="radio" <?php echo $survey['Education'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <label for="radio10"><?php echo lang('A. Belum pernah bersekolah') ?><br/>
                        <input type="radio" name="Education2" id="radio11" value="radio" <?php echo $survey['Education'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('B. SD/SDLB, Madrasah Ibtidaiyah, atau Paket A') ?><br/>
                        <input type="radio" name="Education3" id="radio12" value="radio" <?php echo $survey['Education'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('C. SMP/SMPLB, Madrasah Tsanawiayh, atau Paket B') ?></label><br/>
                    <input type="radio" name="Education4" id="radio13" value="radio" <?php echo $survey['Education'] == '4' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <label for="radio13"><?php echo lang('D. Tidak ada kepala rumah tangga perempuan/istri') ?><br/>
                        <input type="radio" name="Education5" id="radio14" value="radio" <?php echo $survey['Education'] == '5' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('E. SMK') ?><br/>
                        <input type="radio" name="Education6" id="radio15" value="radio" <?php echo $survey['Education'] == '6' ? 'checked="checked"' : '' ?> disabled="true"/>
                        <?php echo lang('F. SMA/SMALB, Madrasah Aliyah, atau Paket C') ?></label><br/>
                    <input type="radio" name="Education7" id="radio15" value="radio" <?php echo $survey['Education'] == '7' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('G. D1, D2, D3/Sarjana Muda, D4, S1, S2, S3') ?></p></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="vertical-align:top;"><?php echo lang('4. Apa status pekerjaan utama dari kepala rumah tangga laki-laki/suami di minggu terakhir') ?>?</td>
            <td colspan="2" style="border:1px solid #000000;">
                <input type="radio" name="Employment1" id="radio16" value="radio" <?php echo $survey['Employment'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                <label for="radio16"><?php echo lang('A. Tidak ada kepala rumah tangga laki-laki/suami') ?><br/>
                    <input type="radio" name="Employment2" id="radio17" value="radio" <?php echo $survey['Employment'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('B. Tidak bekerja atau pekerja keluarga/pekerja tidak dibayar') ?><br/>
                    <input type="radio" name="Employment3" id="radio18" value="radio" <?php echo $survey['Employment'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('C. Pekerja bebas') ?></label>
                    <br/>
                <input type="radio" name="Employment4" id="radio19" value="radio" <?php echo $survey['Employment'] == '4' ? 'checked="checked"' : '' ?> disabled="true"/>
                <label for="radio19"><?php echo lang('D. Berusaha sendiri atau berusaha dibantu buruh tidak tetap/buruh tidak dibayar') ?><br/>
                    <input type="radio" name="Employment5" id="radio20" value="radio" <?php echo $survey['Employment'] == '5' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('E. Buruh/karyawan/pegawai') ?><br/>
                    <input type="radio" name="Employment6" id="radio21" value="radio" <?php echo $survey['Employment'] == '6' ? 'checked="checked"' : '' ?> disabled="true"/>
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
                <input type="radio" name="HouseFloor1" id="radio22" value="radio" <?php echo $survey['HouseFloor'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                <?php echo lang('A. Tanah atau bambu') ?>
                <label for="radio22"><br/>
                    <input type="radio" name="HouseFloor2" id="radio23" value="radio" <?php echo $survey['HouseFloor'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('B. Bukan tanah/bambu') ?></label>
                    </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
        </tr>
    </table>
    

    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="50%"><?php echo lang('Page')?> - 1</td>
                <td class="kolomKanan" align="right">
                    <?php echo lang('Cocoa Farmer Poverty Scorecard')?>
                </td>
            </tr>
        </table>
    </footer>
</div>
<div class="page">
    <table width="100%" border="0" class="table" cellpadding="2" cellspacing="0">
        <tr>
            <td width="52%" style="vertical-align:top;"><?php echo lang('6. Jenis kloset/WC yang rumah tangga anda miliki') ?>?</td>
            <td colspan="2" style="border:1px solid #000000;">
                <input type="radio" name="ToiletFacility1" id="radio24" value="radio" <?php echo $survey['ToiletFacility'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                <label for="radio24"><?php echo lang('A. Tidak ada atau jamban cemplung/cebluk') ?><br/>
                    <input type="radio" name="ToiletFacility2" id="radio25" value="radio" <?php echo $survey['ToiletFacility'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                    <?php echo lang('B. Ada kloset, tapi tidak tersambung ke septic tank (plengsengan)') ?><br/>
                    <input type="radio" name="ToiletFacility3" id="radio26" value="radio" <?php echo $survey['ToiletFacility'] == '3' ? 'checked="checked"' : '' ?> disabled="true"/>
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
                <input type="radio" name="CookingFuel1" id="radio27" value="radio" <?php echo $survey['CookingFuel'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                <?php echo lang('A. Kayu bakar, arang, briket') ?><br/>
                <input type="radio" name="CookingFuel2" id="radio28" value="radio" <?php echo $survey['CookingFuel'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
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
                <input type="radio" name="GasCylinder1" id="radio29" value="radio" <?php echo $survey['GasCylinder'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                A. <?php echo lang('Tidak') ?>
            </td>
            <td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;">
                <input type="radio" name="GasCylinder2" id="radio30" value="radio" <?php echo $survey['GasCylinder'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
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
                <input type="radio" name="Refrigerator1" id="radio31" value="radio" <?php echo $survey['Refrigerator'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                A. <?php echo lang('Tidak') ?>
            </td>
            <td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;">
                <input type="radio" name="Refrigerator2" id="radio32" value="radio" <?php echo $survey['Refrigerator'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
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
                <input type="radio" name="Motorcycle1" id="radio33" value="radio" <?php echo $survey['Motorcycle'] == '1' ? 'checked="checked"' : '' ?> disabled="true"/>
                A. <?php echo lang('Tidak') ?>
            </td>
            <td style="border-top:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;">
                <input type="radio" name="Motorcycle2" id="radio34" value="radio" <?php echo $survey['Motorcycle'] == '2' ? 'checked="checked"' : '' ?> disabled="true"/>
                B. <?php echo lang('Ya') ?>
            </td>
        </tr>
    </table>
    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="50%"><?php echo lang('Page')?> - 2</td>
                <td class="kolomKanan" align="right">
                    <?php echo lang('Cocoa Farmer Poverty Scorecard')?>
                </td>
            </tr>
        </table>
    </footer>
</div>
</body>
</html>

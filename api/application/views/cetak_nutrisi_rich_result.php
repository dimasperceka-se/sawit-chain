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
    <title><?php echo lang('GNP')?></title>

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
             <img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
          for($i=0;$i<count($logos);$i++){
             if($logos[$i]['Photo']!=''){
        ?>
          <td height="60px" width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
             }
          }
        ?>
       <td width="20%" align="center" style="vertical-align:middle;">
          <img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
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
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $nutrition['tglInterview']?>" />
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
                <h2 class="mainTitle"><?php echo lang('N1. Nutrition Basic Data')?></h2>
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
            <h2 class="judulTabel"><?php echo strtoupper(lang('Nutrition Garden'))?></h2>
        </td>
    </tr>
    </table>
    <table width="100%">
    <tr class="bgAbu">
        <td width="37%">
            <?php echo lang('Do you have a vegetable garden ?')?>
        </td>
        <td width="63%" colspan="2" class="tdWithInput">
                <?php
                switch ($nutrition['HaveVegetableGarden']) {
                    case '1':
                        $HaveVegetableGarden1 = 'checked=""';
                        $HaveVegetableGarden2 = '';
                    break;
                    case '2':
                        $HaveVegetableGarden1 = '';
                        $HaveVegetableGarden2 = 'checked=""';
                    break;
                    default:
                        $HaveVegetableGarden1 = '';
                        $HaveVegetableGarden2 = '';
                    break;
                }
                ?>
                <input name="HaveVegetableGarden" <?php echo $HaveVegetableGarden1?> type="radio" />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="HaveVegetableGarden" <?php echo $HaveVegetableGarden2?> type="radio" />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgIjo">
        <td rowspan="2">
            <strong><?php echo lang('Type of garden')?></strong>
        </td>
        <td width="25%"><strong><?php echo lang('Family Garden')?></strong></td>
        <td width="25%"><strong><?php echo lang('Kebun Komersial')?></strong></td>
    </tr>
    <tr class="bgAbu">
        <td class="tdWithInput">
            <?php
            switch ($nutrition['IsFamilyGarden']) {
                case '1':
                    $IsFamilyGarden1 = 'checked=""';
                    $IsFamilyGarden2 = '';
                break;
                case '2':
                    $IsFamilyGarden1 = '';
                    $IsFamilyGarden2 = 'checked=""';
                break;
                default:
                    $IsFamilyGarden1 = '';
                    $IsFamilyGarden2 = '';
                break;
            }
            ?>
            <input <?php echo $IsFamilyGarden1?> type="radio" name="IsFamilyGarden" />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input <?php echo $IsFamilyGarden2?> type="radio" name="IsFamilyGarden" />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['IsCommmercialGarden']) {
                case '1':
                    $IsCommmercialGarden1 = 'checked=""';
                    $IsCommmercialGarden2 = '';
                break;
                case '2':
                    $IsCommmercialGarden1 = '';
                    $IsCommmercialGarden2 = 'checked=""';
                break;
                default:
                    $IsCommmercialGarden1 = '';
                    $IsCommmercialGarden2 = '';
                break;
            }
            ?>
            <input <?php echo $IsCommmercialGarden1?> type="radio" name="IsCommmercialGarden" />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input <?php echo $IsCommmercialGarden2?> type="radio" name="IsCommmercialGarden" />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            <?php echo lang('Size of garden: length (m)')?>
        </td>
        <td><?php echo $nutrition['KebunPanjang']?></td>
        <td><?php echo $nutrition['ComKebunPanjang']?></td>
    </tr>
    <tr class="bgAbu">
        <td><?php echo lang('Size of garden: width (m)')?></td>
        <td><?php echo $nutrition['KebunLebar']?></td>
        <td><?php echo $nutrition['ComKebunLebar']?></td>
    </tr>
    <tr class="bgAbu">
        <td><?php echo lang('Size of garden: area (m2)')?></td>
        <td><?php echo $nutrition['KebunArea']?></td>
        <td><?php echo $nutrition['ComKebunArea']?></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="3">
            <strong><?php echo lang('Grown vegetables :')?></strong>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Spinach')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbBayam']) {
                case '1':
                    $KbBayam1 = 'checked=""';
                    $KbBayam2 = '';
                break;
                case '2':
                    $KbBayam1 = '';
                    $KbBayam2 = 'checked=""';
                break;
                default:
                    $KbBayam1 = '';
                    $KbBayam2 = '';
                break;
            }
            ?>
            <input <?php echo $KbBayam1?> type="radio" name="KbBayam" />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input <?php echo $KbBayam2?> type="radio" name="KbBayam" />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbBayam']) {
                case '1':
                    $ComKbBayam1 = 'checked=""';
                    $ComKbBayam2 = '';
                break;
                case '2':
                    $ComKbBayam1 = '';
                    $ComKbBayam2 = 'checked=""';
                break;
                default:
                    $ComKbBayam1 = '';
                    $ComKbBayam2 = '';
                break;
            }
            ?>
            <input type="radio" <?php echo $ComKbBayam1?> name="ComKbBayam" />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" <?php echo $ComKbBayam2?> name="ComKbBayam" />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Water spinach')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbKangkung']) {
                case '1':
                    $KbKangkung1 = 'checked=""';
                    $KbKangkung2 = '';
                break;
                case '2':
                    $KbKangkung1 = '';
                    $KbKangkung2 = 'checked=""';
                break;
                default:
                    $KbKangkung1 = '';
                    $KbKangkung2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbKangkung" <?php echo $KbKangkung1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbKangkung" <?php echo $KbKangkung2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbKangkung']) {
                case '1':
                    $ComKbKangkung1 = 'checked=""';
                    $ComKbKangkung2 = '';
                break;
                case '2':
                    $ComKbKangkung1 = '';
                    $ComKbKangkung2 = 'checked=""';
                break;
                default:
                    $ComKbKangkung1 = '';
                    $ComKbKangkung2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbKangkung" <?php echo $ComKbKangkung1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbKangkung" <?php echo $ComKbKangkung2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Longbeans')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbKacangPanjang']) {
                case '1':
                    $KbKacangPanjang1 = 'checked=""';
                    $KbKacangPanjang2 = '';
                break;
                case '2':
                    $KbKacangPanjang1 = '';
                    $KbKacangPanjang2 = 'checked=""';
                break;
                default:
                    $KbKacangPanjang1 = '';
                    $KbKacangPanjang2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbKacangPanjang" <?php echo $KbKacangPanjang1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbKacangPanjang" <?php echo $KbKacangPanjang2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbKacangPanjang']) {
                case '1':
                    $ComKbKacangPanjang1 = 'checked=""';
                    $ComKbKacangPanjang2 = '';
                break;
                case '2':
                    $ComKbKacangPanjang1 = '';
                    $ComKbKacangPanjang2 = 'checked=""';
                break;
                default:
                    $ComKbKacangPanjang1 = '';
                    $ComKbKacangPanjang2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbKacangPanjang" <?php echo $ComKbKacangPanjang1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbKacangPanjang" <?php echo $ComKbKacangPanjang2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Chilli')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbCabai']) {
                case '1':
                    $KbCabai1 = 'checked=""';
                    $KbCabai2 = '';
                break;
                case '2':
                    $KbCabai1 = '';
                    $KbCabai2 = 'checked=""';
                break;
                default:
                    $KbCabai1 = '';
                    $KbCabai2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbCabai" <?php echo $KbCabai1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbCabai" <?php echo $KbCabai2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbCabai']) {
                case '1':
                    $ComKbCabai1 = 'checked=""';
                    $ComKbCabai2 = '';
                break;
                case '2':
                    $ComKbCabai1 = '';
                    $ComKbCabai2 = 'checked=""';
                break;
                default:
                    $ComKbCabai1 = '';
                    $ComKbCabai2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbCabai" <?php echo $ComKbCabai1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbCabai" <?php echo $ComKbCabai2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Eggplant')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbTerong']) {
                case '1':
                    $KbTerong1 = 'checked=""';
                    $KbTerong2 = '';
                break;
                case '2':
                    $KbTerong1 = '';
                    $KbTerong2 = 'checked=""';
                break;
                default:
                    $KbTerong1 = '';
                    $KbTerong2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbTerong" <?php echo $KbTerong1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbTerong" <?php echo $KbTerong2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbTerong']) {
                case '1':
                    $ComKbTerong1 = 'checked=""';
                    $ComKbTerong2 = '';
                break;
                case '2':
                    $ComKbTerong1 = '';
                    $ComKbTerong2 = 'checked=""';
                break;
                default:
                    $ComKbTerong1 = '';
                    $ComKbTerong2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbTerong" <?php echo $ComKbTerong1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbTerong" <?php echo $ComKbTerong2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Leaf mustard')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbSawi']) {
                case '1':
                    $KbSawi1 = 'checked=""';
                    $KbSawi2 = '';
                break;
                case '2':
                    $KbSawi1 = '';
                    $KbSawi2 = 'checked=""';
                break;
                default:
                    $KbSawi1 = '';
                    $KbSawi2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbSawi" <?php echo $KbSawi1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbSawi" <?php echo $KbSawi2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbSawi']) {
                case '1':
                    $ComKbSawi1 = 'checked=""';
                    $ComKbSawi2 = '';
                break;
                case '2':
                    $ComKbSawi1 = '';
                    $ComKbSawi2 = 'checked=""';
                break;
                default:
                    $ComKbSawi1 = '';
                    $ComKbSawi2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbSawi" <?php echo $ComKbSawi1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbSawi" <?php echo $ComKbSawi2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Tomato')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbTomat']) {
                case '1':
                    $KbTomat1 = 'checked=""';
                    $KbTomat2 = '';
                break;
                case '2':
                    $KbTomat1 = '';
                    $KbTomat2 = 'checked=""';
                break;
                default:
                    $KbTomat1 = '';
                    $KbTomat2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbTomat" <?php echo $KbTomat1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbTomat" <?php echo $KbTomat2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbTomat']) {
                case '1':
                    $ComKbTomat1 = 'checked=""';
                    $ComKbTomat2 = '';
                break;
                case '2':
                    $ComKbTomat1 = '';
                    $ComKbTomat2 = 'checked=""';
                break;
                default:
                    $ComKbTomat1 = '';
                    $ComKbTomat2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbTomat" <?php echo $ComKbTomat1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbTomat" <?php echo $ComKbTomat2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Moringa')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbKelor']) {
                case '1':
                    $KbKelor1 = 'checked=""';
                    $KbKelor2 = '';
                break;
                case '2':
                    $KbKelor1 = '';
                    $KbKelor2 = 'checked=""';
                break;
                default:
                    $KbKelor1 = '';
                    $KbKelor2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbKelor" <?php echo $KbKelor1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbKelor" <?php echo $KbKelor2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbKelor']) {
                case '1':
                    $ComKbKelor1 = 'checked=""';
                    $ComKbKelor2 = '';
                break;
                case '2':
                    $ComKbKelor1 = '';
                    $ComKbKelor2 = 'checked=""';
                break;
                default:
                    $ComKbKelor1 = '';
                    $ComKbKelor2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbKelor" <?php echo $ComKbKelor1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbKelor" <?php echo $ComKbKelor2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Cassava')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbSingkong']) {
                case '1':
                    $KbSingkong1 = 'checked=""';
                    $KbSingkong2 = '';
                break;
                case '2':
                    $KbSingkong1 = '';
                    $KbSingkong2 = 'checked=""';
                break;
                default:
                    $KbSingkong1 = '';
                    $KbSingkong2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbSingkong" <?php echo $KbSingkong1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbSingkong" <?php echo $KbSingkong2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbSingkong']) {
                case '1':
                    $ComKbSingkong1 = 'checked=""';
                    $ComKbSingkong2 = '';
                break;
                case '2':
                    $ComKbSingkong1 = '';
                    $ComKbSingkong2 = 'checked=""';
                break;
                default:
                    $ComKbSingkong1 = '';
                    $ComKbSingkong2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbSingkong" <?php echo $ComKbSingkong1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbSingkong" <?php echo $ComKbSingkong2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Pumpkin')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbLabu']) {
                case '1':
                    $KbLabu1 = 'checked=""';
                    $KbLabu2 = '';
                break;
                case '2':
                    $KbLabu1 = '';
                    $KbLabu2 = 'checked=""';
                break;
                default:
                    $KbLabu1 = '';
                    $KbLabu2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbLabu" <?php echo $KbLabu1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbLabu" <?php echo $KbLabu2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbLabu']) {
                case '1':
                    $ComKbLabu1 = 'checked=""';
                    $ComKbLabu2 = '';
                break;
                case '2':
                    $ComKbLabu1 = '';
                    $ComKbLabu2 = 'checked=""';
                break;
                default:
                    $ComKbLabu1 = '';
                    $ComKbLabu2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbLabu" <?php echo $ComKbLabu1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbLabu" <?php echo $ComKbLabu2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgAbu">
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo lang('Katuk leaf')?>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbKatuk']) {
                case '1':
                    $KbKatuk1 = 'checked=""';
                    $KbKatuk2 = '';
                break;
                case '2':
                    $KbKatuk1 = '';
                    $KbKatuk2 = 'checked=""';
                break;
                default:
                    $KbKatuk1 = '';
                    $KbKatuk2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbKatuk" <?php echo $KbKatuk1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbKatuk" <?php echo $KbKatuk2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ComKbKatuk']) {
                case '1':
                    $ComKbKatuk1 = 'checked=""';
                    $ComKbKatuk2 = '';
                break;
                case '2':
                    $ComKbKatuk1 = '';
                    $ComKbKatuk2 = 'checked=""';
                break;
                default:
                    $ComKbKatuk1 = '';
                    $ComKbKatuk2 = '';
                break;
            }
            ?>
            <input type="radio" name="ComKbKatuk" <?php echo $ComKbKatuk1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ComKbKatuk" <?php echo $ComKbKatuk2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td class="bgIjo">
            <strong><?php echo lang('Vegetable utilization')?></strong>
        </td>
        <td class="bgAbu">
            <?php
            switch ($nutrition['VegetableUtilization']) {
                case '1':
                    $VegetableUtilization1 = 'checked=""';
                    $VegetableUtilization2 = '';
                    $VegetableUtilization3 = '';
                break;
                case '2':
                    $VegetableUtilization1 = '';
                    $VegetableUtilization2 = 'checked=""';
                    $VegetableUtilization3 = '';
                break;
                case '3':
                    $VegetableUtilization1 = '';
                    $VegetableUtilization2 = '';
                    $VegetableUtilization3 = 'checked=""';
                break;
                default:
                    $VegetableUtilization1 = '';
                    $VegetableUtilization2 = '';
                    $VegetableUtilization3 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="VegetableUtilization" <?php echo $VegetableUtilization1?> /><label><?php echo lang('Own Consumption')?></label></li>
                <li class="tdWithInput"><input type="radio" name="VegetableUtilization" <?php echo $VegetableUtilization2?> /><label><?php echo lang('Own Consumption and Sell')?></label></li>
                <li class="tdWithInput"><input type="radio" name="VegetableUtilization" <?php echo $VegetableUtilization3?> /><label><?php echo lang('Only Sell')?></label></li>
            </ol>
        </td>
        <td class="bgAbu">
            <?php
            switch ($nutrition['ComVegetableUtilization']) {
                case '1':
                    $ComVegetableUtilization1 = 'checked=""';
                    $ComVegetableUtilization2 = '';
                    $ComVegetableUtilization3 = '';
                break;
                case '2':
                    $ComVegetableUtilization1 = '';
                    $ComVegetableUtilization2 = 'checked=""';
                    $ComVegetableUtilization3 = '';
                break;
                case '3':
                    $ComVegetableUtilization1 = '';
                    $ComVegetableUtilization2 = '';
                    $ComVegetableUtilization3 = 'checked=""';
                break;
                default:
                    $ComVegetableUtilization1 = '';
                    $ComVegetableUtilization2 = '';
                    $ComVegetableUtilization3 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="ComVegetableUtilization" <?php echo $ComVegetableUtilization1?> /><label><?php echo lang('Own Consumption')?></label></li>
                <li class="tdWithInput"><input type="radio" name="ComVegetableUtilization" <?php echo $ComVegetableUtilization2?> /><label><?php echo lang('Own Consumption and Sell')?></label></li>
                <li class="tdWithInput"><input type="radio" name="ComVegetableUtilization" <?php echo $ComVegetableUtilization3?> /><label><?php echo lang('Only Sell')?></label></li>
            </ol>
        </td>
    </tr>
    </table>

    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="65%"><?php echo lang('Page')?> - 1</td>
                <td class="kolomKanan" width="35%" align="right">
                    <?php echo lang('N1. Nutrition Basic Data')?>
                </td>
            </tr>
        </table>
    </footer>
</div> <!-- Halaman 1 End -->

<div class="page"> <!-- Halaman 2 Start -->

    <!-- LOGO ATAS (BEGIN) -->
    <table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
       <tr>
          <td width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
          for($i=0;$i<count($logos);$i++){
             if($logos[$i]['Photo']!=''){
        ?>
          <td height="60px" width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
             }
          }
        ?>
       <td width="20%" align="center" style="vertical-align:middle;">
          <img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
       </td>
    </tr>
    </table>
    <br /><br />
    <!-- LOGO ATAS (END) -->

    <div style="background-color:#23BAB1;padding:8px;">
        <table class="noBorder tabelJudul" width="100%">
        <tr>
            <td width="42%" style="padding-right:5px;border-right: 1px dashed white;">
                <table width="100%">
                    <tr>
                        <td width="30%"><?php echo strtoupper(lang('Survey Nr'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $SurveyNr;?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper(lang('Farmer ID'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['FarmerID']?>" />
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align:top;padding-left:14px;">
                <h2 class="mainTitle"><?php echo lang('N1. Nutrition Basic Data')?></h2>
            </td>
            <td width="8%">&nbsp;</td>
        </tr>
        </table>
    </div>
    <br />

    <table class="noBorder" width="100%" style="margin-bottom:6px;">
    <tr>
        <td width="6%">
            <img src="<?php echo base_url()?>assets/css/nutrition/icon-livestock-fishery.png" width="35" />
        </td>
        <td width="94%" style="border-bottom: 1px dashed #23BAB1;">
            <h2 class="judulTabel"><?php echo strtoupper(lang('Livestock and Fishery'))?></h2>
        </td>
    </tr>
    </table>
    <table width="100%">
    <tr class="bgIjo">
        <td colspan="4">
            <strong><?php echo lang('Existing livestock')?></strong>
        </td>
    </tr>
    <tr>
        <td width="25%"><?php echo lang('Chicken')?></td>
        <td width="25%" class="tdWithInput">
            <?php
            switch ($nutrition['KbAyam']) {
                case '1':
                    $KbAyam1 = 'checked=""';
                    $KbAyam2 = '';
                break;
                case '2':
                    $KbAyam1 = '';
                    $KbAyam2 = 'checked=""';
                break;
                default:
                    $KbAyam1 = '';
                    $KbAyam2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbAyam" <?php echo $KbAyam1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbAyam" <?php echo $KbAyam2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td width="25%"><?php echo lang('Goat')?></td>
        <td width="25%" class="tdWithInput">
            <?php
            switch ($nutrition['KbKambing']) {
                case '1':
                    $KbKambing1 = 'checked=""';
                    $KbKambing2 = '';
                break;
                case '2':
                    $KbKambing1 = '';
                    $KbKambing2 = 'checked=""';
                break;
                default:
                    $KbKambing1 = '';
                    $KbKambing2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbKambing" <?php echo $KbKambing1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbKambing" <?php echo $KbKambing2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Duck')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbBebek']) {
                case '1':
                    $KbBebek1 = 'checked=""';
                    $KbBebek2 = '';
                break;
                case '2':
                    $KbBebek1 = '';
                    $KbBebek2 = 'checked=""';
                break;
                default:
                    $KbBebek1 = '';
                    $KbBebek2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbBebek" <?php echo $KbBebek1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbBebek" <?php echo $KbBebek2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Sheep')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbDomba']) {
                case '1':
                    $KbDomba1 = 'checked=""';
                    $KbDomba2 = '';
                break;
                case '2':
                    $KbDomba1 = '';
                    $KbDomba2 = 'checked=""';
                break;
                default:
                    $KbDomba1 = '';
                    $KbDomba2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbDomba" <?php echo $KbDomba1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbDomba" <?php echo $KbDomba2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Fish')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbIkan']) {
                case '1':
                    $KbIkan1 = 'checked=""';
                    $KbIkan2 = '';
                break;
                case '2':
                    $KbIkan1 = '';
                    $KbIkan2 = 'checked=""';
                break;
                default:
                    $KbIkan1 = '';
                    $KbIkan2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbIkan" <?php echo $KbIkan1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbIkan" <?php echo $KbIkan2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Buffalo')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbKerbau']) {
                case '1':
                    $KbKerbau1 = 'checked=""';
                    $KbKerbau2 = '';
                break;
                case '2':
                    $KbKerbau1 = '';
                    $KbKerbau2 = 'checked=""';
                break;
                default:
                    $KbKerbau1 = '';
                    $KbKerbau2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbKerbau" <?php echo $KbKerbau1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbKerbau" <?php echo $KbKerbau2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Cow')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbSapi']) {
                case '1':
                    $KbSapi1 = 'checked=""';
                    $KbSapi2 = '';
                break;
                case '2':
                    $KbSapi1 = '';
                    $KbSapi2 = 'checked=""';
                break;
                default:
                    $KbSapi1 = '';
                    $KbSapi2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbSapi" <?php echo $KbSapi1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbSapi" <?php echo $KbSapi2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Pig')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['KbBabi']) {
                case '1':
                    $KbBabi1 = 'checked=""';
                    $KbBabi2 = '';
                break;
                case '2':
                    $KbBabi1 = '';
                    $KbBabi2 = 'checked=""';
                break;
                default:
                    $KbBabi1 = '';
                    $KbBabi2 = '';
                break;
            }
            ?>
            <input type="radio" name="KbBabi" <?php echo $KbBabi1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="KbBabi" <?php echo $KbBabi2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <?php echo lang('Do you have a fish pond ?')?>
        </td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['HaveFishPond']) {
                case '1':
                    $HaveFishPond1 = 'checked=""';
                    $HaveFishPond2 = '';
                break;
                case '2':
                    $HaveFishPond1 = '';
                    $HaveFishPond2 = 'checked=""';
                break;
                default:
                    $HaveFishPond1 = '';
                    $HaveFishPond2 = '';
                break;
            }
            ?>
            <input type="radio" name="HaveFishPond" <?php echo $HaveFishPond1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="HaveFishPond" <?php echo $HaveFishPond2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Size of fish pond: length (m)')?></td>
        <td colspan="2">
            <?php echo $nutrition['FishPondLength']?>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Size of fish pond: width (m)')?></td>
        <td colspan="2"><?php echo $nutrition['FishPondWidth']?></td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Size of fish pond: area (m2)')?></td>
        <td colspan="2"><?php echo $nutrition['FishPondArea']?></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo lang('Fish species')?></strong></td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Nila')?></td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['fsNila']) {
                case '1':
                    $fsNila1 = 'checked=""';
                    $fsNila2 = '';
                break;
                case '2':
                    $fsNila1 = '';
                    $fsNila2 = 'checked=""';
                break;
                default:
                    $fsNila1 = '';
                    $fsNila2 = '';
                break;
            }
            ?>
            <input type="radio" name="fsNila" <?php echo $fsNila1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fsNila" <?php echo $fsNila2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Carp')?></td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['fsCarp']) {
                case '1':
                    $fsCarp1 = 'checked=""';
                    $fsCarp2 = '';
                break;
                case '2':
                    $fsCarp1 = '';
                    $fsCarp2 = 'checked=""';
                break;
                default:
                    $fsCarp1 = '';
                    $fsCarp2 = '';
                break;
            }
            ?>
            <input type="radio" name="fsCarp" <?php echo $fsCarp1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fsCarp" <?php echo $fsCarp2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Catfish')?></td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['fsCatfish']) {
                case '1':
                    $fsCatfish1 = 'checked=""';
                    $fsCatfish2 = '';
                break;
                case '2':
                    $fsCatfish1 = '';
                    $fsCatfish2 = 'checked=""';
                break;
                default:
                    $fsCatfish1 = '';
                    $fsCatfish2 = '';
                break;
            }
            ?>
            <input type="radio" name="fsCatfish" <?php echo $fsCatfish1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fsCatfish" <?php echo $fsCatfish2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Tilapia')?></td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['fsTilapia']) {
                case '1':
                    $fsTilapia1 = 'checked=""';
                    $fsTilapia2 = '';
                break;
                case '2':
                    $fsTilapia1 = '';
                    $fsTilapia2 = 'checked=""';
                break;
                default:
                    $fsTilapia1 = '';
                    $fsTilapia2 = '';
                break;
            }
            ?>
            <input type="radio" name="fsTilapia" <?php echo $fsTilapia1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fsTilapia" <?php echo $fsTilapia2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Others')?></td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['fsOthers']) {
                case '1':
                    $fsOthers1 = 'checked=""';
                    $fsOthers2 = '';
                break;
                case '2':
                    $fsOthers1 = '';
                    $fsOthers2 = 'checked=""';
                break;
                default:
                    $fsOthers1 = '';
                    $fsOthers2 = '';
                break;
            }
            ?>
            <input type="radio" name="fsOthers" <?php echo $fsOthers1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fsOthers" <?php echo $fsOthers2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Fish utilization')?></td>
        <td colspan="2">
            <?php
            switch ($nutrition['FishUtilization']) {
                case '1':
                    $FishUtilization1 = 'checked=""';
                    $FishUtilization2 = '';
                    $FishUtilization3 = '';
                break;
                case '2':
                    $FishUtilization1 = '';
                    $FishUtilization2 = 'checked=""';
                    $FishUtilization3 = '';
                break;
                case '3':
                    $FishUtilization1 = '';
                    $FishUtilization2 = '';
                    $FishUtilization3 = 'checked=""';
                break;
                default:
                    $FishUtilization1 = '';
                    $FishUtilization2 = '';
                    $FishUtilization3 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="FishUtilization" <?php echo $FishUtilization1?> /><label><?php echo lang('Own Consumption')?></label></li>
                <li class="tdWithInput"><input type="radio" name="FishUtilization" <?php echo $FishUtilization2?> /><label><?php echo lang('Own Consumption and Sell')?></label></li>
                <li class="tdWithInput"><input type="radio" name="FishUtilization" <?php echo $FishUtilization3?> /><label><?php echo lang('Only Sell')?></label></li>
            </ol>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('How often do you eat the raised fish ?')?></td>
        <td colspan="2">
            <?php
            switch ($nutrition['EatRaisedFish']) {
                case '1':
                    $EatRaisedFish1 = 'checked=""';
                    $EatRaisedFish2 = '';
                    $EatRaisedFish3 = '';
                    $EatRaisedFish4 = '';
                break;
                case '2':
                    $EatRaisedFish1 = '';
                    $EatRaisedFish2 = 'checked=""';
                    $EatRaisedFish3 = '';
                    $EatRaisedFish4 = '';
                break;
                case '3':
                    $EatRaisedFish1 = '';
                    $EatRaisedFish2 = '';
                    $EatRaisedFish3 = 'checked=""';
                    $EatRaisedFish4 = '';
                break;
                case '4':
                    $EatRaisedFish1 = '';
                    $EatRaisedFish2 = '';
                    $EatRaisedFish3 = '';
                    $EatRaisedFish4 = 'checked=""';
                break;
                default:
                    $EatRaisedFish1 = '';
                    $EatRaisedFish2 = '';
                    $EatRaisedFish3 = '';
                    $EatRaisedFish4 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="EatRaisedFish" <?php echo $EatRaisedFish1?> /><label><?php echo lang('1-2 per week')?></label></li>
                <li class="tdWithInput"><input type="radio" name="EatRaisedFish" <?php echo $EatRaisedFish2?> /><label><?php echo lang('3-4 per week')?></label></li>
                <li class="tdWithInput"><input type="radio" name="EatRaisedFish" <?php echo $EatRaisedFish3?> /><label><?php echo lang('5-7 per week')?></label></li>
                <li class="tdWithInput"><input type="radio" name="EatRaisedFish" <?php echo $EatRaisedFish4?> /><label><?php echo lang('1-2 per month')?></label></li>
            </ol>
        </td>
    </tr>
    </table>

    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="65%"><?php echo lang('Page')?> - 2</td>
                <td class="kolomKanan" width="35%" align="right">
                    <?php echo lang('N1. Nutrition Basic Data')?>
                </td>
            </tr>
        </table>
    </footer>
</div> <!-- Halaman 2 End -->

<div class="page"> <!-- Halaman 3 Start -->

    <!-- LOGO ATAS (BEGIN) -->
    <table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
       <tr>
          <td width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
          for($i=0;$i<count($logos);$i++){
             if($logos[$i]['Photo']!=''){
        ?>
          <td height="60px" width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
             }
          }
        ?>
       <td width="20%" align="center" style="vertical-align:middle;">
          <img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
       </td>
    </tr>
    </table>
    <br /><br />
    <!-- LOGO ATAS (END) -->

    <div style="background-color:#23BAB1;padding:8px;">
        <table class="noBorder tabelJudul" width="100%">
        <tr>
            <td width="42%" style="padding-right:5px;border-right: 1px dashed white;">
                <table width="100%">
                    <tr>
                        <td width="30%"><?php echo strtoupper(lang('Survey Nr'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $SurveyNr;?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper(lang('Farmer ID'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['FarmerID']?>" />
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align:top;padding-left:14px;">
                <h2 class="mainTitle"><?php echo lang('N1. Nutrition Basic Data')?></h2>
            </td>
            <td width="8%">&nbsp;</td>
        </tr>
        </table>
    </div>
    <br />

    <table class="noBorder" width="100%" style="margin-bottom:6px;">
    <tr>
        <td width="6%">
            <img src="<?php echo base_url()?>assets/css/nutrition/icon-wdds.png" width="35" />
        </td>
        <td width="94%" style="border-bottom: 1px dashed #23BAB1;">
            <h2 class="judulTabel"><?php echo strtoupper(lang('WDDS (Women Dietary Diversity Score)'))?></h2>
        </td>
    </tr>
    </table>
    <table width="100%">
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo lang('STARCHY STAPLES: Cereal')?></strong></td>
    </tr>
    <tr>
        <td width="25%"><?php echo lang('Rice')?></td>
        <td width="25%" class="tdWithInput">
            <?php
            switch ($nutrition['cerRice']) {
                case '1':
                    $cerRice1 = 'checked=""';
                    $cerRice2 = '';
                break;
                case '2':
                    $cerRice1 = '';
                    $cerRice2 = 'checked=""';
                break;
                default:
                    $cerRice1 = '';
                    $cerRice2 = '';
                break;
            }
            ?>
            <input type="radio" name="cerRice" <?php echo $cerRice1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="cerRice" <?php echo $cerRice2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td width="25%"><?php echo lang('Bread')?></td>
        <td width="25%" class="tdWithInput">
            <?php
            switch ($nutrition['cerBread']) {
                case '1':
                    $cerBread1 = 'checked=""';
                    $cerBread2 = '';
                break;
                case '2':
                    $cerBread1 = '';
                    $cerBread2 = 'checked=""';
                break;
                default:
                    $cerBread1 = '';
                    $cerBread2 = '';
                break;
            }
            ?>
            <input type="radio" name="cerBread" <?php echo $cerBread1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="cerBread" <?php echo $cerBread2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Noodles')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['cerNoodles']) {
                case '1':
                    $cerNoodles1 = 'checked=""';
                    $cerNoodles2 = '';
                break;
                case '2':
                    $cerNoodles1 = '';
                    $cerNoodles2 = 'checked=""';
                break;
                default:
                    $cerNoodles1 = '';
                    $cerNoodles2 = '';
                break;
            }
            ?>
            <input type="radio" name="cerNoodles" <?php echo $cerNoodles1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="cerNoodles" <?php echo $cerNoodles2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Wheat flour')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['cerWheatFlour']) {
                case '1':
                    $cerWheatFlour1 = 'checked=""';
                    $cerWheatFlour2 = '';
                break;
                case '2':
                    $cerWheatFlour1 = '';
                    $cerWheatFlour2 = 'checked=""';
                break;
                default:
                    $cerWheatFlour1 = '';
                    $cerWheatFlour2 = '';
                break;
            }
            ?>
            <input type="radio" name="cerWheatFlour" <?php echo $cerWheatFlour1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="cerWheatFlour" <?php echo $cerWheatFlour2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Corn')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['cerCorn']) {
                case '1':
                    $cerCorn1 = 'checked=""';
                    $cerCorn2 = '';
                break;
                case '2':
                    $cerCorn1 = '';
                    $cerCorn2 = 'checked=""';
                break;
                default:
                    $cerCorn1 = '';
                    $cerCorn2 = '';
                break;
            }
            ?>
            <input type="radio" name="cerCorn" <?php echo $cerCorn1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="cerCorn" <?php echo $cerCorn2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Sorghum')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['cerSorghum']) {
                case '1':
                    $cerSorghum1 = 'checked=""';
                    $cerSorghum2 = '';
                break;
                case '2':
                    $cerSorghum1 = '';
                    $cerSorghum2 = 'checked=""';
                break;
                default:
                    $cerSorghum1 = '';
                    $cerSorghum2 = '';
                break;
            }
            ?>
            <input type="radio" name="cerSorghum" <?php echo $cerSorghum1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="cerSorghum" <?php echo $cerSorghum2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Cereal bubur')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['cerCerealBubur']) {
                case '1':
                    $cerCerealBubur1 = 'checked=""';
                    $cerCerealBubur2 = '';
                break;
                case '2':
                    $cerCerealBubur1 = '';
                    $cerCerealBubur2 = 'checked=""';
                break;
                default:
                    $cerCerealBubur1 = '';
                    $cerCerealBubur2 = '';
                break;
            }
            ?>
            <input type="radio" name="cerCerealBubur" <?php echo $cerCerealBubur1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="cerCerealBubur" <?php echo $cerCerealBubur2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Millet')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['cerMillet']) {
                case '1':
                    $cerMillet1 = 'checked=""';
                    $cerMillet2 = '';
                break;
                case '2':
                    $cerMillet1 = '';
                    $cerMillet2 = 'checked=""';
                break;
                default:
                    $cerMillet1 = '';
                    $cerMillet2 = '';
                break;
            }
            ?>
            <input type="radio" name="cerMillet" <?php echo $cerMillet1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="cerMillet" <?php echo $cerMillet2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo lang('STARCHY STAPLES: White tubers and roots')?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Cassava (white)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['wtrWhiteCassava']) {
                case '1':
                    $wtrWhiteCassava1 = 'checked=""';
                    $wtrWhiteCassava2 = '';
                break;
                case '2':
                    $wtrWhiteCassava1 = '';
                    $wtrWhiteCassava2 = 'checked=""';
                break;
                default:
                    $wtrWhiteCassava1 = '';
                    $wtrWhiteCassava2 = '';
                break;
            }
            ?>
            <input type="radio" name="wtrWhiteCassava" <?php echo $wtrWhiteCassava1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="wtrWhiteCassava" <?php echo $wtrWhiteCassava2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Sweet potato')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['wtrSweetPotato']) {
                case '1':
                    $wtrSweetPotato1 = 'checked=""';
                    $wtrSweetPotato2 = '';
                break;
                case '2':
                    $wtrSweetPotato1 = '';
                    $wtrSweetPotato2 = 'checked=""';
                break;
                default:
                    $wtrSweetPotato1 = '';
                    $wtrSweetPotato2 = '';
                break;
            }
            ?>
            <input type="radio" name="wtrSweetPotato" <?php echo $wtrSweetPotato1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="wtrSweetPotato" <?php echo $wtrSweetPotato2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Taro')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['wtrTaro']) {
                case '1':
                    $wtrTaro1 = 'checked=""';
                    $wtrTaro2 = '';
                break;
                case '2':
                    $wtrTaro1 = '';
                    $wtrTaro2 = 'checked=""';
                break;
                default:
                    $wtrTaro1 = '';
                    $wtrTaro2 = '';
                break;
            }
            ?>
            <input type="radio" name="wtrTaro" <?php echo $wtrTaro1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="wtrTaro" <?php echo $wtrTaro2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Plantain')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['wtrPlantain']) {
                case '1':
                    $wtrPlantain1 = 'checked=""';
                    $wtrPlantain2 = '';
                break;
                case '2':
                    $wtrPlantain1 = '';
                    $wtrPlantain2 = 'checked=""';
                break;
                default:
                    $wtrPlantain1 = '';
                    $wtrPlantain2 = '';
                break;
            }
            ?>
            <input type="radio" name="wtrPlantain" <?php echo $wtrPlantain1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="wtrPlantain" <?php echo $wtrPlantain2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Potato')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['wtrPotato']) {
                case '1':
                    $wtrPotato1 = 'checked=""';
                    $wtrPotato2 = '';
                break;
                case '2':
                    $wtrPotato1 = '';
                    $wtrPotato2 = 'checked=""';
                break;
                default:
                    $wtrPotato1 = '';
                    $wtrPotato2 = '';
                break;
            }
            ?>
            <input type="radio" name="wtrPotato" <?php echo $wtrPotato1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="wtrPotato" <?php echo $wtrPotato2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Yam')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['wtrYam']) {
                case '1':
                    $wtrYam1 = 'checked=""';
                    $wtrYam2 = '';
                break;
                case '2':
                    $wtrYam1 = '';
                    $wtrYam2 = 'checked=""';
                break;
                default:
                    $wtrYam1 = '';
                    $wtrYam2 = '';
                break;
            }
            ?>
            <input type="radio" name="wtrYam" <?php echo $wtrYam1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="wtrYam" <?php echo $wtrYam2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Sago')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['wtrSago']) {
                case '1':
                    $wtrSago1 = 'checked=""';
                    $wtrSago2 = '';
                break;
                case '2':
                    $wtrSago1 = '';
                    $wtrSago2 = 'checked=""';
                break;
                default:
                    $wtrSago1 = '';
                    $wtrSago2 = '';
                break;
            }
            ?>
            <input type="radio" name="wtrSago" <?php echo $wtrSago1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="wtrSago" <?php echo $wtrSago2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Dark green leafy vegetables'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Spinach')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgSpinach']) {
                case '1':
                    $dgSpinach1 = 'checked=""';
                    $dgSpinach2 = '';
                break;
                case '2':
                    $dgSpinach1 = '';
                    $dgSpinach2 = 'checked=""';
                break;
                default:
                    $dgSpinach1 = '';
                    $dgSpinach2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgSpinach" <?php echo $dgSpinach1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgSpinach" <?php echo $dgSpinach2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Moringa leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgMoringaLeaf']) {
                case '1':
                    $dgMoringaLeaf1 = 'checked=""';
                    $dgMoringaLeaf2 = '';
                break;
                case '2':
                    $dgMoringaLeaf1 = '';
                    $dgMoringaLeaf2 = 'checked=""';
                break;
                default:
                    $dgMoringaLeaf1 = '';
                    $dgMoringaLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgMoringaLeaf" <?php echo $dgMoringaLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgMoringaLeaf" <?php echo $dgMoringaLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Cassava leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgCassavaLeaf']) {
                case '1':
                    $dgCassavaLeaf1 = 'checked=""';
                    $dgCassavaLeaf2 = '';
                break;
                case '2':
                    $dgCassavaLeaf1 = '';
                    $dgCassavaLeaf2 = 'checked=""';
                break;
                default:
                    $dgCassavaLeaf1 = '';
                    $dgCassavaLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgCassavaLeaf" <?php echo $dgCassavaLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgCassavaLeaf" <?php echo $dgCassavaLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Sawi')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgLeafMustard']) {
                case '1':
                    $dgLeafMustard1 = 'checked=""';
                    $dgLeafMustard2 = '';
                break;
                case '2':
                    $dgLeafMustard1 = '';
                    $dgLeafMustard2 = 'checked=""';
                break;
                default:
                    $dgLeafMustard1 = '';
                    $dgLeafMustard2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgLeafMustard" <?php echo $dgLeafMustard1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgLeafMustard" <?php echo $dgLeafMustard2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Water spinach')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgWaterSpinach']) {
                case '1':
                    $dgWaterSpinach1 = 'checked=""';
                    $dgWaterSpinach2 = '';
                break;
                case '2':
                    $dgWaterSpinach1 = '';
                    $dgWaterSpinach2 = 'checked=""';
                break;
                default:
                    $dgWaterSpinach1 = '';
                    $dgWaterSpinach2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgWaterSpinach" <?php echo $dgWaterSpinach1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgWaterSpinach" <?php echo $dgWaterSpinach2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Sweet potato leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgSweetPotatoLeaf']) {
                case '1':
                    $dgSweetPotatoLeaf1 = 'checked=""';
                    $dgSweetPotatoLeaf2 = '';
                break;
                case '2':
                    $dgSweetPotatoLeaf1 = '';
                    $dgSweetPotatoLeaf2 = 'checked=""';
                break;
                default:
                    $dgSweetPotatoLeaf1 = '';
                    $dgSweetPotatoLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgSweetPotatoLeaf" <?php echo $dgSweetPotatoLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgSweetPotatoLeaf" <?php echo $dgSweetPotatoLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Melinjo leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgMelinjoLeaf']) {
                case '1':
                    $dgMelinjoLeaf1 = 'checked=""';
                    $dgMelinjoLeaf2 = '';
                break;
                case '2':
                    $dgMelinjoLeaf1 = '';
                    $dgMelinjoLeaf2 = 'checked=""';
                break;
                default:
                    $dgMelinjoLeaf1 = '';
                    $dgMelinjoLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgMelinjoLeaf" <?php echo $dgMelinjoLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgMelinjoLeaf" <?php echo $dgMelinjoLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Pakis')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgPakis']) {
                case '1':
                    $dgPakis1 = 'checked=""';
                    $dgPakis2 = '';
                break;
                case '2':
                    $dgPakis1 = '';
                    $dgPakis2 = 'checked=""';
                break;
                default:
                    $dgPakis1 = '';
                    $dgPakis2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgPakis" <?php echo $dgPakis1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgPakis" <?php echo $dgPakis2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Papaya leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgPapayaLeaf']) {
                case '1':
                    $dgPapayaLeaf1 = 'checked=""';
                    $dgPapayaLeaf2 = '';
                break;
                case '2':
                    $dgPapayaLeaf1 = '';
                    $dgPapayaLeaf2 = 'checked=""';
                break;
                default:
                    $dgPapayaLeaf1 = '';
                    $dgPapayaLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgPapayaLeaf" <?php echo $dgPapayaLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgPapayaLeaf" <?php echo $dgPapayaLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Katuk leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgKatukLeaf']) {
                case '1':
                    $dgKatukLeaf1 = 'checked=""';
                    $dgKatukLeaf2 = '';
                break;
                case '2':
                    $dgKatukLeaf1 = '';
                    $dgKatukLeaf2 = 'checked=""';
                break;
                default:
                    $dgKatukLeaf1 = '';
                    $dgKatukLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgKatukLeaf" <?php echo $dgKatukLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgKatukLeaf" <?php echo $dgKatukLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Pumpkin leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgPumpkinLeaf']) {
                case '1':
                    $dgPumpkinLeaf1 = 'checked=""';
                    $dgPumpkinLeaf2 = '';
                break;
                case '2':
                    $dgPumpkinLeaf1 = '';
                    $dgPumpkinLeaf2 = 'checked=""';
                break;
                default:
                    $dgPumpkinLeaf1 = '';
                    $dgPumpkinLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgPumpkinLeaf" <?php echo $dgPumpkinLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgPumpkinLeaf" <?php echo $dgPumpkinLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Taro/TTania leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgTaroLeaf']) {
                case '1':
                    $dgTaroLeaf1 = 'checked=""';
                    $dgTaroLeaf2 = '';
                break;
                case '2':
                    $dgTaroLeaf1 = '';
                    $dgTaroLeaf2 = 'checked=""';
                break;
                default:
                    $dgTaroLeaf1 = '';
                    $dgTaroLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgTaroLeaf" <?php echo $dgTaroLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgTaroLeaf" <?php echo $dgTaroLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Longbean leaf')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgLongBeansLeaf']) {
                case '1':
                    $dgLongBeansLeaf1 = 'checked=""';
                    $dgLongBeansLeaf2 = '';
                break;
                case '2':
                    $dgLongBeansLeaf1 = '';
                    $dgLongBeansLeaf2 = 'checked=""';
                break;
                default:
                    $dgLongBeansLeaf1 = '';
                    $dgLongBeansLeaf2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgLongBeansLeaf" <?php echo $dgLongBeansLeaf1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgLongBeansLeaf" <?php echo $dgLongBeansLeaf2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Other')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['dgOthers']) {
                case '1':
                    $dgOthers1 = 'checked=""';
                    $dgOthers2 = '';
                break;
                case '2':
                    $dgOthers1 = '';
                    $dgOthers2 = 'checked=""';
                break;
                default:
                    $dgOthers1 = '';
                    $dgOthers2 = '';
                break;
            }
            ?>
            <input type="radio" name="dgOthers" <?php echo $dgOthers1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="dgOthers" <?php echo $dgOthers2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Other Vit. A Rich Fruits'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Mango (ripe)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rfMangoRipe']) {
                case '1':
                    $rfMangoRipe1 = 'checked=""';
                    $rfMangoRipe2 = '';
                break;
                case '2':
                    $rfMangoRipe1 = '';
                    $rfMangoRipe2 = 'checked=""';
                break;
                default:
                    $rfMangoRipe1 = '';
                    $rfMangoRipe2 = '';
                break;
            }
            ?>
            <input type="radio" name="rfMangoRipe" <?php echo $rfMangoRipe1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rfMangoRipe" <?php echo $rfMangoRipe2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Terong')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rfEggplant']) {
                case '1':
                    $rfEggplant1 = 'checked=""';
                    $rfEggplant2 = '';
                break;
                case '2':
                    $rfEggplant1 = '';
                    $rfEggplant2 = 'checked=""';
                break;
                default:
                    $rfEggplant1 = '';
                    $rfEggplant2 = '';
                break;
            }
            ?>
            <input type="radio" name="rfEggplant" <?php echo $rfEggplant1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rfEggplant" <?php echo $rfEggplant2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Papaya (ripe)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rfPapayaRipe']) {
                case '1':
                    $rfPapayaRipe1 = 'checked=""';
                    $rfPapayaRipe2 = '';
                break;
                case '2':
                    $rfPapayaRipe1 = '';
                    $rfPapayaRipe2 = 'checked=""';
                break;
                default:
                    $rfPapayaRipe1 = '';
                    $rfPapayaRipe2 = '';
                break;
            }
            ?>
            <input type="radio" name="rfPapayaRipe" <?php echo $rfPapayaRipe1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rfPapayaRipe" <?php echo $rfPapayaRipe2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Banana (dark yellow or orange flesh)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rfOrangeBanana']) {
                case '1':
                    $rfOrangeBanana1 = 'checked=""';
                    $rfOrangeBanana2 = '';
                break;
                case '2':
                    $rfOrangeBanana1 = '';
                    $rfOrangeBanana2 = 'checked=""';
                break;
                default:
                    $rfOrangeBanana1 = '';
                    $rfOrangeBanana2 = '';
                break;
            }
            ?>
            <input type="radio" name="rfOrangeBanana" <?php echo $rfOrangeBanana1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rfOrangeBanana" <?php echo $rfOrangeBanana2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Passion fruit')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rfPassionFruit']) {
                case '1':
                    $rfPassionFruit1 = 'checked=""';
                    $rfPassionFruit2 = '';
                break;
                case '2':
                    $rfPassionFruit1 = '';
                    $rfPassionFruit2 = 'checked=""';
                break;
                default:
                    $rfPassionFruit1 = '';
                    $rfPassionFruit2 = '';
                break;
            }
            ?>
            <input type="radio" name="rfPassionFruit" <?php echo $rfPassionFruit1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rfPassionFruit" <?php echo $rfPassionFruit2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('OTHER VIT. A RICH VEGETABLES AND FRUITS'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Sweet potato (yellow/orange flesh)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rvtSweetPotato']) {
                case '1':
                    $rvtSweetPotato1 = 'checked=""';
                    $rvtSweetPotato2 = '';
                break;
                case '2':
                    $rvtSweetPotato1 = '';
                    $rvtSweetPotato2 = 'checked=""';
                break;
                default:
                    $rvtSweetPotato1 = '';
                    $rvtSweetPotato2 = '';
                break;
            }
            ?>
            <input type="radio" name="rvtSweetPotato" <?php echo $rvtSweetPotato1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rvtSweetPotato" <?php echo $rvtSweetPotato2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Squash (orange and dark yellow flesh)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rvtOrangeSquash']) {
                case '1':
                    $rvtOrangeSquash1 = 'checked=""';
                    $rvtOrangeSquash2 = '';
                break;
                case '2':
                    $rvtOrangeSquash1 = '';
                    $rvtOrangeSquash2 = 'checked=""';
                break;
                default:
                    $rvtOrangeSquash1 = '';
                    $rvtOrangeSquash2 = '';
                break;
            }
            ?>
            <input type="radio" name="rvtOrangeSquash" <?php echo $rvtOrangeSquash1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rvtOrangeSquash" <?php echo $rvtOrangeSquash2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Cassava (yellow flesh)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rvtYellowCassava']) {
                case '1':
                    $rvtYellowCassava1 = 'checked=""';
                    $rvtYellowCassava2 = '';
                break;
                case '2':
                    $rvtYellowCassava1 = '';
                    $rvtYellowCassava2 = 'checked=""';
                break;
                default:
                    $rvtYellowCassava1 = '';
                    $rvtYellowCassava2 = '';
                break;
            }
            ?>
            <input type="radio" name="rvtYellowCassava" <?php echo $rvtYellowCassava1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rvtYellowCassava" <?php echo $rvtYellowCassava2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Pumpkin')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rvtPumpkin']) {
                case '1':
                    $rvtPumpkin1 = 'checked=""';
                    $rvtPumpkin2 = '';
                break;
                case '2':
                    $rvtPumpkin1 = '';
                    $rvtPumpkin2 = 'checked=""';
                break;
                default:
                    $rvtPumpkin1 = '';
                    $rvtPumpkin2 = '';
                break;
            }
            ?>
            <input type="radio" name="rvtPumpkin" <?php echo $rvtPumpkin1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rvtPumpkin" <?php echo $rvtPumpkin2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Carrot')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['rvtCarrot']) {
                case '1':
                    $rvtCarrot1 = 'checked=""';
                    $rvtCarrot2 = '';
                break;
                case '2':
                    $rvtCarrot1 = '';
                    $rvtCarrot2 = 'checked=""';
                break;
                default:
                    $rvtCarrot1 = '';
                    $rvtCarrot2 = '';
                break;
            }
            ?>
            <input type="radio" name="rvtCarrot" <?php echo $rvtCarrot1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="rvtCarrot" <?php echo $rvtCarrot2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Buah-buah yang lain'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Banana')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofBanana']) {
                case '1':
                    $ofBanana1 = 'checked=""';
                    $ofBanana2 = '';
                break;
                case '2':
                    $ofBanana1 = '';
                    $ofBanana2 = 'checked=""';
                break;
                default:
                    $ofBanana1 = '';
                    $ofBanana2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofBanana" <?php echo $ofBanana1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofBanana" <?php echo $ofBanana2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Soursop')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofSoursop']) {
                case '1':
                    $ofSoursop1 = 'checked=""';
                    $ofSoursop2 = '';
                break;
                case '2':
                    $ofSoursop1 = '';
                    $ofSoursop2 = 'checked=""';
                break;
                default:
                    $ofSoursop1 = '';
                    $ofSoursop2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofSoursop" <?php echo $ofSoursop1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofSoursop" <?php echo $ofSoursop2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Guava')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofGuava']) {
                case '1':
                    $ofGuava1 = 'checked=""';
                    $ofGuava2 = '';
                break;
                case '2':
                    $ofGuava1 = '';
                    $ofGuava2 = 'checked=""';
                break;
                default:
                    $ofGuava1 = '';
                    $ofGuava2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofGuava" <?php echo $ofGuava1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofGuava" <?php echo $ofGuava2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Kedondong')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofKedondong']) {
                case '1':
                    $ofKedondong1 = 'checked=""';
                    $ofKedondong2 = '';
                break;
                case '2':
                    $ofKedondong1 = '';
                    $ofKedondong2 = 'checked=""';
                break;
                default:
                    $ofKedondong1 = '';
                    $ofKedondong2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofKedondong" <?php echo $ofKedondong1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofKedondong" <?php echo $ofKedondong2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Coconut')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofCoconut']) {
                case '1':
                    $ofCoconut1 = 'checked=""';
                    $ofCoconut2 = '';
                break;
                case '2':
                    $ofCoconut1 = '';
                    $ofCoconut2 = 'checked=""';
                break;
                default:
                    $ofCoconut1 = '';
                    $ofCoconut2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofCoconut" <?php echo $ofCoconut1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofCoconut" <?php echo $ofCoconut2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Sawo')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofSawo']) {
                case '1':
                    $ofSawo1 = 'checked=""';
                    $ofSawo2 = '';
                break;
                case '2':
                    $ofSawo1 = '';
                    $ofSawo2 = 'checked=""';
                break;
                default:
                    $ofSawo1 = '';
                    $ofSawo2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofSawo" <?php echo $ofSawo1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofSawo" <?php echo $ofSawo2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Lemon/orange')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofLemon']) {
                case '1':
                    $ofLemon1 = 'checked=""';
                    $ofLemon2 = '';
                break;
                case '2':
                    $ofLemon1 = '';
                    $ofLemon2 = 'checked=""';
                break;
                default:
                    $ofLemon1 = '';
                    $ofLemon2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofLemon" <?php echo $ofLemon1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofLemon" <?php echo $ofLemon2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Watermelon/melon')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofWatermelon']) {
                case '1':
                    $ofWatermelon1 = 'checked=""';
                    $ofWatermelon2 = '';
                break;
                case '2':
                    $ofWatermelon1 = '';
                    $ofWatermelon2 = 'checked=""';
                break;
                default:
                    $ofWatermelon1 = '';
                    $ofWatermelon2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofWatermelon" <?php echo $ofWatermelon1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofWatermelon" <?php echo $ofWatermelon2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Water apple')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofWaterApple']) {
                case '1':
                    $ofWaterApple1 = 'checked=""';
                    $ofWaterApple2 = '';
                break;
                case '2':
                    $ofWaterApple1 = '';
                    $ofWaterApple2 = 'checked=""';
                break;
                default:
                    $ofWaterApple1 = '';
                    $ofWaterApple2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofWaterApple" <?php echo $ofWaterApple1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofWaterApple" <?php echo $ofWaterApple2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Langsat/longan/rambutan')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofLangsat']) {
                case '1':
                    $ofLangsat1 = 'checked=""';
                    $ofLangsat2 = '';
                break;
                case '2':
                    $ofLangsat1 = '';
                    $ofLangsat2 = 'checked=""';
                break;
                default:
                    $ofLangsat1 = '';
                    $ofLangsat2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofLangsat" <?php echo $ofLangsat1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofLangsat" <?php echo $ofLangsat2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Durian')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofDurian']) {
                case '1':
                    $ofDurian1 = 'checked=""';
                    $ofDurian2 = '';
                break;
                case '2':
                    $ofDurian1 = '';
                    $ofDurian2 = 'checked=""';
                break;
                default:
                    $ofDurian1 = '';
                    $ofDurian2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofDurian" <?php echo $ofDurian1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofDurian" <?php echo $ofDurian2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Mangosteen (mangis)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofMangosteen']) {
                case '1':
                    $ofMangosteen1 = 'checked=""';
                    $ofMangosteen2 = '';
                break;
                case '2':
                    $ofMangosteen1 = '';
                    $ofMangosteen2 = 'checked=""';
                break;
                default:
                    $ofMangosteen1 = '';
                    $ofMangosteen2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofMangosteen" <?php echo $ofMangosteen1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofMangosteen" <?php echo $ofMangosteen2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Avocado')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofAvocado']) {
                case '1':
                    $ofAvocado1 = 'checked=""';
                    $ofAvocado2 = '';
                break;
                case '2':
                    $ofAvocado1 = '';
                    $ofAvocado2 = 'checked=""';
                break;
                default:
                    $ofAvocado1 = '';
                    $ofAvocado2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofAvocado" <?php echo $ofAvocado1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofAvocado" <?php echo $ofAvocado2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Other')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofOthers']) {
                case '1':
                    $ofOthers1 = 'checked=""';
                    $ofOthers2 = '';
                break;
                case '2':
                    $ofOthers1 = '';
                    $ofOthers2 = 'checked=""';
                break;
                default:
                    $ofOthers1 = '';
                    $ofOthers2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofOthers" <?php echo $ofOthers1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofOthers" <?php echo $ofOthers2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Pineapple')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ofPineapple']) {
                case '1':
                    $ofPineapple1 = 'checked=""';
                    $ofPineapple2 = '';
                break;
                case '2':
                    $ofPineapple1 = '';
                    $ofPineapple2 = 'checked=""';
                break;
                default:
                    $ofPineapple1 = '';
                    $ofPineapple2 = '';
                break;
            }
            ?>
            <input type="radio" name="ofPineapple" <?php echo $ofPineapple1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ofPineapple" <?php echo $ofPineapple2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td colspan="2"></td>
    </tr>
    </table>

    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="65%"><?php echo lang('Page')?> - 3</td>
                <td class="kolomKanan" width="35%" align="right">
                    <?php echo lang('N1. Nutrition Basic Data')?>
                </td>
            </tr>
        </table>
    </footer>
</div> <!-- Halaman 3 End -->

<div class="page"> <!-- Halaman 4 Begin -->
    <!-- LOGO ATAS (BEGIN) -->
    <table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
       <tr>
          <td width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
          for($i=0;$i<count($logos);$i++){
             if($logos[$i]['Photo']!=''){
        ?>
          <td height="60px" width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
             }
          }
        ?>
       <td width="20%" align="center" style="vertical-align:middle;">
          <img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
       </td>
    </tr>
    </table>
    <br /><br />
    <!-- LOGO ATAS (END) -->

    <div style="background-color:#23BAB1;padding:8px;">
        <table class="noBorder tabelJudul" width="100%">
        <tr>
            <td width="42%" style="padding-right:5px;border-right: 1px dashed white;">
                <table width="100%">
                    <tr>
                        <td width="30%"><?php echo strtoupper(lang('Survey Nr'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $SurveyNr;?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper(lang('Farmer ID'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['FarmerID']?>" />
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align:top;padding-left:14px;">
                <h2 class="mainTitle"><?php echo lang('N1. Nutrition Basic Data')?></h2>
            </td>
            <td width="8%">&nbsp;</td>
        </tr>
        </table>
    </div>
    <br />

    <table width="100%">
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Other Vegetables'))?></strong></td>
    </tr>
    <tr>
        <td width="25%"><?php echo lang('Longbean')?></td>
        <td width="25%" class="tdWithInput">
            <?php
            switch ($nutrition['ovLongbeans']) {
                case '1':
                    $ovLongbeans1 = 'checked=""';
                    $ovLongbeans2 = '';
                break;
                case '2':
                    $ovLongbeans1 = '';
                    $ovLongbeans2 = 'checked=""';
                break;
                default:
                    $ovLongbeans1 = '';
                    $ovLongbeans2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovLongbeans" <?php echo $ovLongbeans1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovLongbeans" <?php echo $ovLongbeans2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td width="25%"><?php echo lang('Onion/garlic')?></td>
        <td width="25%" class="tdWithInput">
            <?php
            switch ($nutrition['ovOnion']) {
                case '1':
                    $ovOnion1 = 'checked=""';
                    $ovOnion2 = '';
                break;
                case '2':
                    $ovOnion1 = '';
                    $ovOnion2 = 'checked=""';
                break;
                default:
                    $ovOnion1 = '';
                    $ovOnion2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovOnion" <?php echo $ovOnion1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovOnion" <?php echo $ovOnion2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Eggplant')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovEggplant']) {
                case '1':
                    $ovEggplant1 = 'checked=""';
                    $ovEggplant2 = '';
                break;
                case '2':
                    $ovEggplant1 = '';
                    $ovEggplant2 = 'checked=""';
                break;
                default:
                    $ovEggplant1 = '';
                    $ovEggplant2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovEggplant" <?php echo $ovEggplant1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovEggplant" <?php echo $ovEggplant2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Bamboo shoots')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovBambooShoots']) {
                case '1':
                    $ovBambooShoots1 = 'checked=""';
                    $ovBambooShoots2 = '';
                break;
                case '2':
                    $ovBambooShoots1 = '';
                    $ovBambooShoots2 = 'checked=""';
                break;
                default:
                    $ovBambooShoots1 = '';
                    $ovBambooShoots2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovBambooShoots" <?php echo $ovBambooShoots1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovBambooShoots" <?php echo $ovBambooShoots2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Breadfruit')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovBreadfruit']) {
                case '1':
                    $ovBreadfruit1 = 'checked=""';
                    $ovBreadfruit2 = '';
                break;
                case '2':
                    $ovBreadfruit1 = '';
                    $ovBreadfruit2 = 'checked=""';
                break;
                default:
                    $ovBreadfruit1 = '';
                    $ovBreadfruit2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovBreadfruit" <?php echo $ovBreadfruit1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovBreadfruit" <?php echo $ovBreadfruit2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Luffa/squash with white flesh')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovLuffa']) {
                case '1':
                    $ovLuffa1 = 'checked=""';
                    $ovLuffa2 = '';
                break;
                case '2':
                    $ovLuffa1 = '';
                    $ovLuffa2 = 'checked=""';
                break;
                default:
                    $ovLuffa1 = '';
                    $ovLuffa2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovLuffa" <?php echo $ovLuffa1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovLuffa" <?php echo $ovLuffa2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Jackfruit')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovJackfruit']) {
                case '1':
                    $ovJackfruit1 = 'checked=""';
                    $ovJackfruit2 = '';
                break;
                case '2':
                    $ovJackfruit1 = '';
                    $ovJackfruit2 = 'checked=""';
                break;
                default:
                    $ovJackfruit1 = '';
                    $ovJackfruit2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovJackfruit" <?php echo $ovJackfruit1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovJackfruit" <?php echo $ovJackfruit2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Bitter melon (Paria)')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovBitterMelon']) {
                case '1':
                    $ovBitterMelon1 = 'checked=""';
                    $ovBitterMelon2 = '';
                break;
                case '2':
                    $ovBitterMelon1 = '';
                    $ovBitterMelon2 = 'checked=""';
                break;
                default:
                    $ovBitterMelon1 = '';
                    $ovBitterMelon2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovBitterMelon" <?php echo $ovBitterMelon1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovBitterMelon" <?php echo $ovBitterMelon2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Tomato')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovTomato']) {
                case '1':
                    $ovTomato1 = 'checked=""';
                    $ovTomato2 = '';
                break;
                case '2':
                    $ovTomato1 = '';
                    $ovTomato2 = 'checked=""';
                break;
                default:
                    $ovTomato1 = '';
                    $ovTomato2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovTomato" <?php echo $ovTomato1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovTomato" <?php echo $ovTomato2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Papaya/banana flower')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovPapaya']) {
                case '1':
                    $ovPapaya1 = 'checked=""';
                    $ovPapaya2 = '';
                break;
                case '2':
                    $ovPapaya1 = '';
                    $ovPapaya2 = 'checked=""';
                break;
                default:
                    $ovPapaya1 = '';
                    $ovPapaya2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovPapaya" <?php echo $ovPapaya1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovPapaya" <?php echo $ovPapaya2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('White cabbage')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovWhiteCabbage']) {
                case '1':
                    $ovWhiteCabbage1 = 'checked=""';
                    $ovWhiteCabbage2 = '';
                break;
                case '2':
                    $ovWhiteCabbage1 = '';
                    $ovWhiteCabbage2 = 'checked=""';
                break;
                default:
                    $ovWhiteCabbage1 = '';
                    $ovWhiteCabbage2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovWhiteCabbage" <?php echo $ovWhiteCabbage1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovWhiteCabbage" <?php echo $ovWhiteCabbage2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Mushrooms')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovMushrooms']) {
                case '1':
                    $ovMushrooms1 = 'checked=""';
                    $ovMushrooms2 = '';
                break;
                case '2':
                    $ovMushrooms1 = '';
                    $ovMushrooms2 = 'checked=""';
                break;
                default:
                    $ovMushrooms1 = '';
                    $ovMushrooms2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovMushrooms" <?php echo $ovMushrooms1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovMushrooms" <?php echo $ovMushrooms2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Cucumber')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovCucumber']) {
                case '1':
                    $ovCucumber1 = 'checked=""';
                    $ovCucumber2 = '';
                break;
                case '2':
                    $ovCucumber1 = '';
                    $ovCucumber2 = 'checked=""';
                break;
                default:
                    $ovCucumber1 = '';
                    $ovCucumber2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovCucumber" <?php echo $ovCucumber1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovCucumber" <?php echo $ovCucumber2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Other')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovOthers']) {
                case '1':
                    $ovOthers1 = 'checked=""';
                    $ovOthers2 = '';
                break;
                case '2':
                    $ovOthers1 = '';
                    $ovOthers2 = 'checked=""';
                break;
                default:
                    $ovOthers1 = '';
                    $ovOthers2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovOthers" <?php echo $ovOthers1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovOthers" <?php echo $ovOthers2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Chayote')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['ovChayote']) {
                case '1':
                    $ovChayote1 = 'checked=""';
                    $ovChayote2 = '';
                break;
                case '2':
                    $ovChayote1 = '';
                    $ovChayote2 = 'checked=""';
                break;
                default:
                    $ovChayote1 = '';
                    $ovChayote2 = '';
                break;
            }
            ?>
            <input type="radio" name="ovChayote" <?php echo $ovChayote1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="ovChayote" <?php echo $ovChayote2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Organ Meat'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Liver')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['omLiver']) {
                case '1':
                    $omLiver1 = 'checked=""';
                    $omLiver2 = '';
                break;
                case '2':
                    $omLiver1 = '';
                    $omLiver2 = 'checked=""';
                break;
                default:
                    $omLiver1 = '';
                    $omLiver2 = '';
                break;
            }
            ?>
            <input type="radio" name="omLiver" <?php echo $omLiver1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="omLiver" <?php echo $omLiver2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Kidney')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['omKidney']) {
                case '1':
                    $omKidney1 = 'checked=""';
                    $omKidney2 = '';
                break;
                case '2':
                    $omKidney1 = '';
                    $omKidney2 = 'checked=""';
                break;
                default:
                    $omKidney1 = '';
                    $omKidney2 = '';
                break;
            }
            ?>
            <input type="radio" name="omKidney" <?php echo $omKidney1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="omKidney" <?php echo $omKidney2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Lungs')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['omLungs']) {
                case '1':
                    $omLungs1 = 'checked=""';
                    $omLungs2 = '';
                break;
                case '2':
                    $omLungs1 = '';
                    $omLungs2 = 'checked=""';
                break;
                default:
                    $omLungs1 = '';
                    $omLungs2 = '';
                break;
            }
            ?>
            <input type="radio" name="omLungs" <?php echo $omLungs1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="omLungs" <?php echo $omLungs2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Heart')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['omHeart']) {
                case '1':
                    $omHeart1 = 'checked=""';
                    $omHeart2 = '';
                break;
                case '2':
                    $omHeart1 = '';
                    $omHeart2 = 'checked=""';
                break;
                default:
                    $omHeart1 = '';
                    $omHeart2 = '';
                break;
            }
            ?>
            <input type="radio" name="omHeart" <?php echo $omHeart1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="omHeart" <?php echo $omHeart2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Meat'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Chicken')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['meChicken']) {
                case '1':
                    $meChicken1 = 'checked=""';
                    $meChicken2 = '';
                break;
                case '2':
                    $meChicken1 = '';
                    $meChicken2 = 'checked=""';
                break;
                default:
                    $meChicken1 = '';
                    $meChicken2 = '';
                break;
            }
            ?>
            <input type="radio" name="meChicken" <?php echo $meChicken1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="meChicken" <?php echo $meChicken2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Lamb')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['meLamb']) {
                case '1':
                    $meLamb1 = 'checked=""';
                    $meLamb2 = '';
                break;
                case '2':
                    $meLamb1 = '';
                    $meLamb2 = 'checked=""';
                break;
                default:
                    $meLamb1 = '';
                    $meLamb2 = '';
                break;
            }
            ?>
            <input type="radio" name="meLamb" <?php echo $meLamb1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="meLamb" <?php echo $meLamb2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Duck')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['meDuck']) {
                case '1':
                    $meDuck1 = 'checked=""';
                    $meDuck2 = '';
                break;
                case '2':
                    $meDuck1 = '';
                    $meDuck2 = 'checked=""';
                break;
                default:
                    $meDuck1 = '';
                    $meDuck2 = '';
                break;
            }
            ?>
            <input type="radio" name="meDuck" <?php echo $meDuck1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="meDuck" <?php echo $meDuck2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Goat')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['meGoat']) {
                case '1':
                    $meGoat1 = 'checked=""';
                    $meGoat2 = '';
                break;
                case '2':
                    $meGoat1 = '';
                    $meGoat2 = 'checked=""';
                break;
                default:
                    $meGoat1 = '';
                    $meGoat2 = '';
                break;
            }
            ?>
            <input type="radio" name="meGoat" <?php echo $meGoat1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="meGoat" <?php echo $meGoat2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Wild Duck')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['meWildDuck']) {
                case '1':
                    $meWildDuck1 = 'checked=""';
                    $meWildDuck2 = '';
                break;
                case '2':
                    $meWildDuck1 = '';
                    $meWildDuck2 = 'checked=""';
                break;
                default:
                    $meWildDuck1 = '';
                    $meWildDuck2 = '';
                break;
            }
            ?>
            <input type="radio" name="meWildDuck" <?php echo $meWildDuck1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="meWildDuck" <?php echo $meWildDuck2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Buffalo')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['meBuffalo']) {
                case '1':
                    $meBuffalo1 = 'checked=""';
                    $meBuffalo2 = '';
                break;
                case '2':
                    $meBuffalo1 = '';
                    $meBuffalo2 = 'checked=""';
                break;
                default:
                    $meBuffalo1 = '';
                    $meBuffalo2 = '';
                break;
            }
            ?>
            <input type="radio" name="meBuffalo" <?php echo $meBuffalo1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="meBuffalo" <?php echo $meBuffalo2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Quail')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['meQuail']) {
                case '1':
                    $meQuail1 = 'checked=""';
                    $meQuail2 = '';
                break;
                case '2':
                    $meQuail1 = '';
                    $meQuail2 = 'checked=""';
                break;
                default:
                    $meQuail1 = '';
                    $meQuail2 = '';
                break;
            }
            ?>
            <input type="radio" name="meQuail" <?php echo $meQuail1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="meQuail" <?php echo $meQuail2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Pork')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['mePork']) {
                case '1':
                    $mePork1 = 'checked=""';
                    $mePork2 = '';
                break;
                case '2':
                    $mePork1 = '';
                    $mePork2 = 'checked=""';
                break;
                default:
                    $mePork1 = '';
                    $mePork2 = '';
                break;
            }
            ?>
            <input type="radio" name="mePork" <?php echo $mePork1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="mePork" <?php echo $mePork2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Beef')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['meBeef']) {
                case '1':
                    $meBeef1 = 'checked=""';
                    $meBeef2 = '';
                break;
                case '2':
                    $meBeef1 = '';
                    $meBeef2 = 'checked=""';
                break;
                default:
                    $meBeef1 = '';
                    $meBeef2 = '';
                break;
            }
            ?>
            <input type="radio" name="meBeef" <?php echo $meBeef1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="meBeef" <?php echo $meBeef2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Fish and seafood'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Fish')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fasFish']) {
                case '1':
                    $fasFish1 = 'checked=""';
                    $fasFish2 = '';
                break;
                case '2':
                    $fasFish1 = '';
                    $fasFish2 = 'checked=""';
                break;
                default:
                    $fasFish1 = '';
                    $fasFish2 = '';
                break;
            }
            ?>
            <input type="radio" name="fasFish" <?php echo $fasFish1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fasFish" <?php echo $fasFish2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Shellfish')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fasShellfish']) {
                case '1':
                    $fasShellfish1 = 'checked=""';
                    $fasShellfish2 = '';
                break;
                case '2':
                    $fasShellfish1 = '';
                    $fasShellfish2 = 'checked=""';
                break;
                default:
                    $fasShellfish1 = '';
                    $fasShellfish2 = '';
                break;
            }
            ?>
            <input type="radio" name="fasShellfish" <?php echo $fasShellfish1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fasShellfish" <?php echo $fasShellfish2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Squid')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fasSquid']) {
                case '1':
                    $fasSquid1 = 'checked=""';
                    $fasSquid2 = '';
                break;
                case '2':
                    $fasSquid1 = '';
                    $fasSquid2 = 'checked=""';
                break;
                default:
                    $fasSquid1 = '';
                    $fasSquid2 = '';
                break;
            }
            ?>
            <input type="radio" name="fasSquid" <?php echo $fasSquid1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fasSquid" <?php echo $fasSquid2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Shrimp')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fasShrimp']) {
                case '1':
                    $fasShrimp1 = 'checked=""';
                    $fasShrimp2 = '';
                break;
                case '2':
                    $fasShrimp1 = '';
                    $fasShrimp2 = 'checked=""';
                break;
                default:
                    $fasShrimp1 = '';
                    $fasShrimp2 = '';
                break;
            }
            ?>
            <input type="radio" name="fasShrimp" <?php echo $fasShrimp1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fasShrimp" <?php echo $fasShrimp2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Crab')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fasCrab']) {
                case '1':
                    $fasCrab1 = 'checked=""';
                    $fasCrab2 = '';
                break;
                case '2':
                    $fasCrab1 = '';
                    $fasCrab2 = 'checked=""';
                break;
                default:
                    $fasCrab1 = '';
                    $fasCrab2 = '';
                break;
            }
            ?>
            <input type="radio" name="fasCrab" <?php echo $fasCrab1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fasCrab" <?php echo $fasCrab2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Octopus')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fasOctopus']) {
                case '1':
                    $fasOctopus1 = 'checked=""';
                    $fasOctopus2 = '';
                break;
                case '2':
                    $fasOctopus1 = '';
                    $fasOctopus2 = 'checked=""';
                break;
                default:
                    $fasOctopus1 = '';
                    $fasOctopus2 = '';
                break;
            }
            ?>
            <input type="radio" name="fasOctopus" <?php echo $fasOctopus1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fasOctopus" <?php echo $fasOctopus2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Telur'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Chicken egg')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['egChickenEgg']) {
                case '1':
                    $egChickenEgg1 = 'checked=""';
                    $egChickenEgg2 = '';
                break;
                case '2':
                    $egChickenEgg1 = '';
                    $egChickenEgg2 = 'checked=""';
                break;
                default:
                    $egChickenEgg1 = '';
                    $egChickenEgg2 = '';
                break;
            }
            ?>
            <input type="radio" name="egChickenEgg" <?php echo $egChickenEgg1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="egChickenEgg" <?php echo $egChickenEgg2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Wild duck egg')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['eWildDuck']) {
                case '1':
                    $eWildDuck1 = 'checked=""';
                    $eWildDuck2 = '';
                break;
                case '2':
                    $eWildDuck1 = '';
                    $eWildDuck2 = 'checked=""';
                break;
                default:
                    $eWildDuck1 = '';
                    $eWildDuck2 = '';
                break;
            }
            ?>
            <input type="radio" name="eWildDuck" <?php echo $eWildDuck1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="eWildDuck" <?php echo $eWildDuck2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Duck egg')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['egDuckEgg']) {
                case '1':
                    $egDuckEgg1 = 'checked=""';
                    $egDuckEgg2 = '';
                break;
                case '2':
                    $egDuckEgg1 = '';
                    $egDuckEgg2 = 'checked=""';
                break;
                default:
                    $egDuckEgg1 = '';
                    $egDuckEgg2 = '';
                break;
            }
            ?>
            <input type="radio" name="egDuckEgg" <?php echo $egDuckEgg1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="egDuckEgg" <?php echo $egDuckEgg2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Quail egg')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['egQuailEgg']) {
                case '1':
                    $egQuailEgg1 = 'checked=""';
                    $egQuailEgg2 = '';
                break;
                case '2':
                    $egQuailEgg1 = '';
                    $egQuailEgg2 = 'checked=""';
                break;
                default:
                    $egQuailEgg1 = '';
                    $egQuailEgg2 = '';
                break;
            }
            ?>
            <input type="radio" name="egQuailEgg" <?php echo $egQuailEgg1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="egQuailEgg" <?php echo $egQuailEgg2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Legumes, Nuts and Seeds'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Tofu')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsTofu']) {
                case '1':
                    $lnsTofu1 = 'checked=""';
                    $lnsTofu2 = '';
                break;
                case '2':
                    $lnsTofu1 = '';
                    $lnsTofu2 = 'checked=""';
                break;
                default:
                    $lnsTofu1 = '';
                    $lnsTofu2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsTofu" <?php echo $lnsTofu1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsTofu" <?php echo $lnsTofu2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Soybean')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsSoybean']) {
                case '1':
                    $lnsSoybean1 = 'checked=""';
                    $lnsSoybean2 = '';
                break;
                case '2':
                    $lnsSoybean1 = '';
                    $lnsSoybean2 = 'checked=""';
                break;
                default:
                    $lnsSoybean1 = '';
                    $lnsSoybean2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsSoybean" <?php echo $lnsSoybean1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsSoybean" <?php echo $lnsSoybean2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Tempe')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsTempe']) {
                case '1':
                    $lnsTempe1 = 'checked=""';
                    $lnsTempe2 = '';
                break;
                case '2':
                    $lnsTempe1 = '';
                    $lnsTempe2 = 'checked=""';
                break;
                default:
                    $lnsTempe1 = '';
                    $lnsTempe2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsTempe" <?php echo $lnsTempe1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsTempe" <?php echo $lnsTempe2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Jengkol')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsJengkol']) {
                case '1':
                    $lnsJengkol1 = 'checked=""';
                    $lnsJengkol2 = '';
                break;
                case '2':
                    $lnsJengkol1 = '';
                    $lnsJengkol2 = 'checked=""';
                break;
                default:
                    $lnsJengkol1 = '';
                    $lnsJengkol2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsJengkol" <?php echo $lnsJengkol1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsJengkol" <?php echo $lnsJengkol2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Tofu water/soy milk')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsTofuWater']) {
                case '1':
                    $lnsTofuWater1 = 'checked=""';
                    $lnsTofuWater2 = '';
                break;
                case '2':
                    $lnsTofuWater1 = '';
                    $lnsTofuWater2 = 'checked=""';
                break;
                default:
                    $lnsTofuWater1 = '';
                    $lnsTofuWater2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsTofuWater" <?php echo $lnsTofuWater1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsTofuWater" <?php echo $lnsTofuWater2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Petai')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsPetai']) {
                case '1':
                    $lnsPetai1 = 'checked=""';
                    $lnsPetai2 = '';
                break;
                case '2':
                    $lnsPetai1 = '';
                    $lnsPetai2 = 'checked=""';
                break;
                default:
                    $lnsPetai1 = '';
                    $lnsPetai2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsPetai" <?php echo $lnsPetai1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsPetai" <?php echo $lnsPetai2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Peanut/peanut sauce')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsPeanutSauce']) {
                case '1':
                    $lnsPeanutSauce1 = 'checked=""';
                    $lnsPeanutSauce2 = '';
                break;
                case '2':
                    $lnsPeanutSauce1 = '';
                    $lnsPeanutSauce2 = 'checked=""';
                break;
                default:
                    $lnsPeanutSauce1 = '';
                    $lnsPeanutSauce2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsPeanutSauce" <?php echo $lnsPeanutSauce1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsPeanutSauce" <?php echo $lnsPeanutSauce2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Cowpea/pigeon pea')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsCowpea']) {
                case '1':
                    $lnsCowpea1 = 'checked=""';
                    $lnsCowpea2 = '';
                break;
                case '2':
                    $lnsCowpea1 = '';
                    $lnsCowpea2 = 'checked=""';
                break;
                default:
                    $lnsCowpea1 = '';
                    $lnsCowpea2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsCowpea" <?php echo $lnsCowpea1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsCowpea" <?php echo $lnsCowpea2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Mung bean')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsMungBean']) {
                case '1':
                    $lnsMungBean1 = 'checked=""';
                    $lnsMungBean2 = '';
                break;
                case '2':
                    $lnsMungBean1 = '';
                    $lnsMungBean2 = 'checked=""';
                break;
                default:
                    $lnsMungBean1 = '';
                    $lnsMungBean2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsMungBean" <?php echo $lnsMungBean1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsMungBean" <?php echo $lnsMungBean2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Cashew')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['lnsCashew']) {
                case '1':
                    $lnsCashew1 = 'checked=""';
                    $lnsCashew2 = '';
                break;
                case '2':
                    $lnsCashew1 = '';
                    $lnsCashew2 = 'checked=""';
                break;
                default:
                    $lnsCashew1 = '';
                    $lnsCashew2 = '';
                break;
            }
            ?>
            <input type="radio" name="lnsCashew" <?php echo $lnsCashew1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="lnsCashew" <?php echo $lnsCashew2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4"><strong><?php echo strtoupper(lang('Milk and Dairy Products'))?></strong></td>
    </tr>
    <tr>
        <td><?php echo lang('Cheese')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['mdpCheese']) {
                case '1':
                    $mdpCheese1 = 'checked=""';
                    $mdpCheese2 = '';
                break;
                case '2':
                    $mdpCheese1 = '';
                    $mdpCheese2 = 'checked=""';
                break;
                default:
                    $mdpCheese1 = '';
                    $mdpCheese2 = '';
                break;
            }
            ?>
            <input type="radio" name="mdpCheese" <?php echo $mdpCheese1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="mdpCheese" <?php echo $mdpCheese2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Yogurt')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['mdpYoghurt']) {
                case '1':
                    $mdpYoghurt1 = 'checked=""';
                    $mdpYoghurt2 = '';
                break;
                case '2':
                    $mdpYoghurt1 = '';
                    $mdpYoghurt2 = 'checked=""';
                break;
                default:
                    $mdpYoghurt1 = '';
                    $mdpYoghurt2 = '';
                break;
            }
            ?>
            <input type="radio" name="mdpYoghurt" <?php echo $mdpYoghurt1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="mdpYoghurt" <?php echo $mdpYoghurt2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Milk')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['mdpMilk']) {
                case '1':
                    $mdpMilk1 = 'checked=""';
                    $mdpMilk2 = '';
                break;
                case '2':
                    $mdpMilk1 = '';
                    $mdpMilk2 = 'checked=""';
                break;
                default:
                    $mdpMilk1 = '';
                    $mdpMilk2 = '';
                break;
            }
            ?>
            <input type="radio" name="mdpMilk" <?php echo $mdpMilk1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="mdpMilk" <?php echo $mdpMilk2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Other')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['mdpOthers']) {
                case '1':
                    $mdpOthers1 = 'checked=""';
                    $mdpOthers2 = '';
                break;
                case '2':
                    $mdpOthers1 = '';
                    $mdpOthers2 = 'checked=""';
                break;
                default:
                    $mdpOthers1 = '';
                    $mdpOthers2 = '';
                break;
            }
            ?>
            <input type="radio" name="mdpOthers" <?php echo $mdpOthers1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="mdpOthers" <?php echo $mdpOthers2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    </table>

    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="65%"><?php echo lang('Page')?> - 4</td>
                <td class="kolomKanan" width="35%" align="right">
                    <?php echo lang('N1. Nutrition Basic Data')?>
                </td>
            </tr>
        </table>
    </footer>
</div> <!-- Halaman 4 End -->

<div class="page"> <!-- Halaman 5 Begin -->
    <!-- LOGO ATAS (BEGIN) -->
    <table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
       <tr>
          <td width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
          for($i=0;$i<count($logos);$i++){
             if($logos[$i]['Photo']!=''){
        ?>
          <td height="60px" width="20%" align="center" style="vertical-align:middle;">
             <img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
          </td>
        <?php
             }
          }
        ?>
       <td width="20%" align="center" style="vertical-align:middle;">
          <img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
       </td>
    </tr>
    </table>
    <br /><br />
    <!-- LOGO ATAS (END) -->

    <div style="background-color:#23BAB1;padding:8px;">
        <table class="noBorder tabelJudul" width="100%">
        <tr>
            <td width="42%" style="padding-right:5px;border-right: 1px dashed white;">
                <table width="100%">
                    <tr>
                        <td width="30%"><?php echo strtoupper(lang('Survey Nr'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $SurveyNr;?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper(lang('Farmer ID'))?></td>
                        <td>
                            <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['FarmerID']?>" />
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align:top;padding-left:14px;">
                <h2 class="mainTitle"><?php echo lang('N1. Nutrition Basic Data')?></h2>
            </td>
            <td width="8%">&nbsp;</td>
        </tr>
        </table>
    </div>
    <br />

    <table class="noBorder" width="100%" style="margin-bottom:6px;">
    <tr>
        <td width="6%">
            <img src="<?php echo base_url()?>assets/css/nutrition/icon-question.png" width="35" />
        </td>
        <td width="94%" style="border-bottom: 1px dashed #23BAB1;">
            <h2 class="judulTabel"><?php echo strtoupper(lang('Questions for female with children under five years'))?> :</h2>
        </td>
    </tr>
    </table>
    <table width="100%">
    <tr class="bgIjo">
        <td colspan="2">
            <?php echo lang('Do you have children below 5 years old ?')?>
        </td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['HaveChildren']) {
                case '1':
                    $HaveChildren1 = 'checked=""';
                    $HaveChildren2 = '';
                break;
                case '2':
                    $HaveChildren1 = '';
                    $HaveChildren2 = 'checked=""';
                break;
                default:
                    $HaveChildren1 = '';
                    $HaveChildren2 = '';
                break;
            }
            ?>
            <input type="radio" name="HaveChildren" <?php echo $HaveChildren1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="HaveChildren" <?php echo $HaveChildren2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <?php echo lang('How many children do you have ?')?>
        </td>
        <td colspan="2"><?php echo $nutrition['NrOfChildren']?></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="4">
            <?php echo lang('If >1, choose the youngest child')?>
        </td>
    </tr>
    <tr>
        <td rowspan="2" width="25%"><?php echo lang('Child Age')?></td>
        <td width="25%"><?php echo lang('Years')?></td>
        <td colspan="2"><?php echo $nutrition['ChildAgeYear']?></td>
    </tr>
    <tr>
        <td><?php echo lang('Month')?></td>
        <td colspan="2"><?php echo $nutrition['ChildAgeMonth']?></td>
    </tr>
    <tr class="bgIjo">
        <td colspan="2">
            <?php echo lang('Have he/she ever been given breastfeed ?')?>
        </td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['GivenBreastfeed']) {
                case '1':
                    $GivenBreastfeed1 = 'checked=""';
                    $GivenBreastfeed2 = '';
                break;
                case '2':
                    $GivenBreastfeed1 = '';
                    $GivenBreastfeed2 = 'checked=""';
                break;
                default:
                    $GivenBreastfeed1 = '';
                    $GivenBreastfeed2 = '';
                break;
            }
            ?>
            <input type="radio" name="GivenBreastfeed" <?php echo $GivenBreastfeed1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="GivenBreastfeed" <?php echo $GivenBreastfeed2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('When was he/she started given breastfeed for the first time')?></td>
        <td colspan="2">
            <?php
            switch ($nutrition['StartGivenBreastfeed']) {
                case '1':
                    $StartGivenBreastfeed1 = 'checked=""';
                    $StartGivenBreastfeed2 = '';
                    $StartGivenBreastfeed3 = '';
                break;
                case '2':
                    $StartGivenBreastfeed1 = '';
                    $StartGivenBreastfeed2 = 'checked=""';
                    $StartGivenBreastfeed3 = '';
                break;
                case '3':
                    $StartGivenBreastfeed1 = '';
                    $StartGivenBreastfeed2 = '';
                    $StartGivenBreastfeed3 = 'checked=""';
                break;
                default:
                    $StartGivenBreastfeed1 = '';
                    $StartGivenBreastfeed2 = '';
                    $StartGivenBreastfeed3 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="StartGivenBreastfeed" <?php echo $StartGivenBreastfeed1?> /><label><?php echo lang('Less than one hour')?></label></li>
                <li class="tdWithInput"><input type="radio" name="StartGivenBreastfeed" <?php echo $StartGivenBreastfeed2?> /><label><?php echo lang('> 1-24 hours')?></label></li>
                <li class="tdWithInput"><input type="radio" name="StartGivenBreastfeed" <?php echo $StartGivenBreastfeed3?> /><label><?php echo lang('> 24 hours')?></label></li>
            </ol>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('What is your treatment to your colustrum (first breastfeed, usually thin, translucent and yellowish) ?')?></td>
        <td colspan="2">
            <?php
            switch ($nutrition['TreatmentColustrum']) {
                case '1':
                    $TreatmentColustrum1 = 'checked=""';
                    $TreatmentColustrum2 = '';
                    $TreatmentColustrum3 = '';
                    $TreatmentColustrum4 = '';
                break;
                case '2':
                    $TreatmentColustrum1 = '';
                    $TreatmentColustrum2 = 'checked=""';
                    $TreatmentColustrum3 = '';
                    $TreatmentColustrum4 = '';
                break;
                case '3':
                    $TreatmentColustrum1 = '';
                    $TreatmentColustrum2 = '';
                    $TreatmentColustrum3 = 'checked=""';
                    $TreatmentColustrum4 = '';
                break;
                case '4':
                    $TreatmentColustrum1 = '';
                    $TreatmentColustrum2 = '';
                    $TreatmentColustrum3 = '';
                    $TreatmentColustrum4 = 'checked=""';
                break;
                default:
                    $TreatmentColustrum1 = '';
                    $TreatmentColustrum2 = '';
                    $TreatmentColustrum3 = '';
                    $TreatmentColustrum4 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="TreatmentColustrum" <?php echo $TreatmentColustrum1?> /><label><?php echo lang('Was given fully to the baby')?></label></li>
                <li class="tdWithInput"><input type="radio" name="TreatmentColustrum" <?php echo $TreatmentColustrum2?> /><label><?php echo lang('Dump for a small amount and then it was given to the baby')?></label></li>
                <li class="tdWithInput"><input type="radio" name="TreatmentColustrum" <?php echo $TreatmentColustrum3?> /><label><?php echo lang('Dump all colustrum, and then gave the breastfeed to the baby')?></label></li>
                <li class="tdWithInput"><input type="radio" name="TreatmentColustrum" <?php echo $TreatmentColustrum4?> /><label><?php echo lang('Don’t know')?></label></li>
            </ol>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('When the baby was given a breastfeed for the first time, did he/she was given any food/water besides ASI/breastfeed ?')?></td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['GivenFoodBesidesASI']) {
                case '1':
                    $GivenFoodBesidesASI1 = 'checked=""';
                    $GivenFoodBesidesASI2 = '';
                break;
                case '2':
                    $GivenFoodBesidesASI1 = '';
                    $GivenFoodBesidesASI2 = 'checked=""';
                break;
                default:
                    $GivenFoodBesidesASI1 = '';
                    $GivenFoodBesidesASI2 = '';
                break;
            }
            ?>
            <input type="radio" name="GivenFoodBesidesASI" <?php echo $GivenFoodBesidesASI1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="GivenFoodBesidesASI" <?php echo $GivenFoodBesidesASI2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="4"><?php echo lang('What kind of food/water that was given ?')?></td>
    </tr>
    <tr>
        <td><?php echo lang('Formula Milk')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwFormulaMilk']) {
                case '1':
                    $fwFormulaMilk1 = 'checked=""';
                    $fwFormulaMilk2 = '';
                break;
                case '2':
                    $fwFormulaMilk1 = '';
                    $fwFormulaMilk2 = 'checked=""';
                break;
                default:
                    $fwFormulaMilk1 = '';
                    $fwFormulaMilk2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwFormulaMilk" <?php echo $fwFormulaMilk1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwFormulaMilk" <?php echo $fwFormulaMilk2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td width="25%"><?php echo lang('Fruit Juice')?></td>
        <td width="25%" class="tdWithInput">
            <?php
            switch ($nutrition['fwFruitJuice']) {
                case '1':
                    $fwFruitJuice1 = 'checked=""';
                    $fwFruitJuice2 = '';
                break;
                case '2':
                    $fwFruitJuice1 = '';
                    $fwFruitJuice2 = 'checked=""';
                break;
                default:
                    $fwFruitJuice1 = '';
                    $fwFruitJuice2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwFruitJuice" <?php echo $fwFruitJuice1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwFruitJuice" <?php echo $fwFruitJuice2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Non dairy formula')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwNonFormulaMilk']) {
                case '1':
                    $fwNonFormulaMilk1 = 'checked=""';
                    $fwNonFormulaMilk2 = '';
                break;
                case '2':
                    $fwNonFormulaMilk1 = '';
                    $fwNonFormulaMilk2 = 'checked=""';
                break;
                default:
                    $fwNonFormulaMilk1 = '';
                    $fwNonFormulaMilk2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwNonFormulaMilk" <?php echo $fwNonFormulaMilk1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwNonFormulaMilk" <?php echo $fwNonFormulaMilk2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Sweet Tea')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwSweetTea']) {
                case '1':
                    $fwSweetTea1 = 'checked=""';
                    $fwSweetTea2 = '';
                break;
                case '2':
                    $fwSweetTea1 = '';
                    $fwSweetTea2 = 'checked=""';
                break;
                default:
                    $fwSweetTea1 = '';
                    $fwSweetTea2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwSweetTea" <?php echo $fwSweetTea1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwSweetTea" <?php echo $fwSweetTea2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Mineral water')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwWater']) {
                case '1':
                    $fwWater1 = 'checked=""';
                    $fwWater2 = '';
                break;
                case '2':
                    $fwWater1 = '';
                    $fwWater2 = 'checked=""';
                break;
                default:
                    $fwWater1 = '';
                    $fwWater2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwWater" <?php echo $fwWater1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwWater" <?php echo $fwWater2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Honey / Honey + Water')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwHoney']) {
                case '1':
                    $fwHoney1 = 'checked=""';
                    $fwHoney2 = '';
                break;
                case '2':
                    $fwHoney1 = '';
                    $fwHoney2 = 'checked=""';
                break;
                default:
                    $fwHoney1 = '';
                    $fwHoney2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwHoney" <?php echo $fwHoney1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwHoney" <?php echo $fwHoney2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Syrup')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwSugarWater']) {
                case '1':
                    $fwSugarWater1 = 'checked=""';
                    $fwSugarWater2 = '';
                break;
                case '2':
                    $fwSugarWater1 = '';
                    $fwSugarWater2 = 'checked=""';
                break;
                default:
                    $fwSugarWater1 = '';
                    $fwSugarWater2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwSugarWater" <?php echo $fwSugarWater1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwSugarWater" <?php echo $fwSugarWater2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Mashed Banana')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwMashedBanana']) {
                case '1':
                    $fwMashedBanana1 = 'checked=""';
                    $fwMashedBanana2 = '';
                break;
                case '2':
                    $fwMashedBanana1 = '';
                    $fwMashedBanana2 = 'checked=""';
                break;
                default:
                    $fwMashedBanana1 = '';
                    $fwMashedBanana2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwMashedBanana" <?php echo $fwMashedBanana1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwMashedBanana" <?php echo $fwMashedBanana2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Starch Water')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwStarchWater']) {
                case '1':
                    $fwStarchWater1 = 'checked=""';
                    $fwStarchWater2 = '';
                break;
                case '2':
                    $fwStarchWater1 = '';
                    $fwStarchWater2 = 'checked=""';
                break;
                default:
                    $fwStarchWater1 = '';
                    $fwStarchWater2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwStarchWater" <?php echo $fwStarchWater1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwStarchWater" <?php echo $fwStarchWater2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Mashed Rice')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwMashedRice']) {
                case '1':
                    $fwMashedRice1 = 'checked=""';
                    $fwMashedRice2 = '';
                break;
                case '2':
                    $fwMashedRice1 = '';
                    $fwMashedRice2 = 'checked=""';
                break;
                default:
                    $fwMashedRice1 = '';
                    $fwMashedRice2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwMashedRice" <?php echo $fwMashedRice1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwMashedRice" <?php echo $fwMashedRice2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td><?php echo lang('Coconut Water')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwCoconutWater']) {
                case '1':
                    $fwCoconutWater1 = 'checked=""';
                    $fwCoconutWater2 = '';
                break;
                case '2':
                    $fwCoconutWater1 = '';
                    $fwCoconutWater2 = 'checked=""';
                break;
                default:
                    $fwCoconutWater1 = '';
                    $fwCoconutWater2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwCoconutWater" <?php echo $fwCoconutWater1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwCoconutWater" <?php echo $fwCoconutWater2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
        <td><?php echo lang('Other')?></td>
        <td class="tdWithInput">
            <?php
            switch ($nutrition['fwOthers']) {
                case '1':
                    $fwOthers1 = 'checked=""';
                    $fwOthers2 = '';
                break;
                case '2':
                    $fwOthers1 = '';
                    $fwOthers2 = 'checked=""';
                break;
                default:
                    $fwOthers1 = '';
                    $fwOthers2 = '';
                break;
            }
            ?>
            <input type="radio" name="fwOthers" <?php echo $fwOthers1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="fwOthers" <?php echo $fwOthers2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Since when that he/she was given any food/water besides ASI/breastfeed ?')?></td>
        <td style="vertical-align:top;">
            <?php
            switch ($nutrition['WhenGivenFoodBesidesASI']) {
                case '1':
                    $WhenGivenFoodBesidesASI1 = 'checked=""';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
                case '2':
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = 'checked=""';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
                case '3':
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = 'checked=""';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
                case '4':
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = 'checked=""';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
                case '5':
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = 'checked=""';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
                case '6':
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = 'checked=""';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
                case '7':
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = 'checked=""';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
                case '8':
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = 'checked=""';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
                case '9':
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = 'checked=""';
                break;
                default:
                    $WhenGivenFoodBesidesASI1 = '';
                    $WhenGivenFoodBesidesASI2 = '';
                    $WhenGivenFoodBesidesASI3 = '';
                    $WhenGivenFoodBesidesASI4 = '';
                    $WhenGivenFoodBesidesASI5 = '';
                    $WhenGivenFoodBesidesASI6 = '';
                    $WhenGivenFoodBesidesASI7 = '';
                    $WhenGivenFoodBesidesASI8 = '';
                    $WhenGivenFoodBesidesASI9 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI1?> /><label><?php echo lang('0 - 7 days')?></label></li>
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI2?> /><label><?php echo lang('8 - 28 days')?></label></li>
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI3?> /><label><?php echo lang('29 days - <2 months')?></label></li>
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI4?> /><label><?php echo lang('2 - <3 months')?></label></li>
            </ol>
        </td>
        <td style="vertical-align:top;">
            <ol start="5" class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI5?> /><label><?php echo lang('3 - <4 months')?></label></li>
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI6?> /><label><?php echo lang('4 - <6 months')?></label></li>
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI7?> /><label><?php echo lang('>= 6 months')?></label></li>
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI8?> /><label><?php echo lang('Don’t know')?></label></li>
                <li class="tdWithInput"><input type="radio" name="WhenGivenFoodBesidesASI" <?php echo $WhenGivenFoodBesidesASI9?> /><label><?php echo lang('Still purely breastfeed')?></label></li>
            </ol>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('If the food/water besides ASI/breastfeed was started to be given by less than 3 months, why ?')?></td>
        <td colspan="2">
            <?php
            switch ($nutrition['Children3MonthASI']) {
                case '1':
                    $Children3MonthASI1 = 'checked=""';
                    $Children3MonthASI2 = '';
                    $Children3MonthASI3 = '';
                break;
                case '2':
                    $Children3MonthASI1 = '';
                    $Children3MonthASI2 = 'checked=""';
                    $Children3MonthASI3 = '';
                break;
                case '3':
                    $Children3MonthASI1 = '';
                    $Children3MonthASI2 = '';
                    $Children3MonthASI3 = 'checked=""';
                break;
                default:
                    $Children3MonthASI1 = '';
                    $Children3MonthASI2 = '';
                    $Children3MonthASI3 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="Children3MonthASI" <?php echo $Children3MonthASI1?> /><label><?php echo lang('I was told to do that')?></label></li>
                <li class="tdWithInput"><input type="radio" name="Children3MonthASI" <?php echo $Children3MonthASI2?> /><label><?php echo lang('ASI saya tidak cukup/tidak ada')?></label></li>
                <li class="tdWithInput"><input type="radio" name="Children3MonthASI" <?php echo $Children3MonthASI3?> /><label><?php echo lang('ASI tidak penting bagi bayi')?></label></li>
            </ol>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('How often did you breast feed your up to 6 months baby (or when he/she was at those age) per day ?')?></td>
        <td colspan="2">
            <?php
            switch ($nutrition['ChildrenNrGiveASI']) {
                case '1':
                    $ChildrenNrGiveASI1 = 'checked=""';
                    $ChildrenNrGiveASI2 = '';
                    $ChildrenNrGiveASI3 = '';
                break;
                case '2':
                    $ChildrenNrGiveASI1 = '';
                    $ChildrenNrGiveASI2 = 'checked=""';
                    $ChildrenNrGiveASI3 = '';
                break;
                case '3':
                    $ChildrenNrGiveASI1 = '';
                    $ChildrenNrGiveASI2 = '';
                    $ChildrenNrGiveASI3 = 'checked=""';
                break;
                default:
                    $ChildrenNrGiveASI1 = '';
                    $ChildrenNrGiveASI2 = '';
                    $ChildrenNrGiveASI3 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="ChildrenNrGiveASI" <?php echo $ChildrenNrGiveASI1?> /><label><?php echo lang('0 - 2 times')?></label></li>
                <li class="tdWithInput"><input type="radio" name="ChildrenNrGiveASI" <?php echo $ChildrenNrGiveASI2?> /><label><?php echo lang('3 - 4 times')?></label></li>
                <li class="tdWithInput"><input type="radio" name="ChildrenNrGiveASI" <?php echo $ChildrenNrGiveASI3?> /><label><?php echo lang('More than 5 times')?></label></li>
            </ol>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('How many times did you feed your 6-24 months baby (or when he/she was at those age) per day ?')?></td>
        <td colspan="2">
            <?php
            switch ($nutrition['ChildrenNrGiveMeal']) {
                case '1':
                    $ChildrenNrGiveMeal1 = 'checked=""';
                    $ChildrenNrGiveMeal2 = '';
                    $ChildrenNrGiveMeal3 = '';
                break;
                case '2':
                    $ChildrenNrGiveMeal1 = '';
                    $ChildrenNrGiveMeal2 = 'checked=""';
                    $ChildrenNrGiveMeal3 = '';
                break;
                case '3':
                    $ChildrenNrGiveMeal1 = '';
                    $ChildrenNrGiveMeal2 = '';
                    $ChildrenNrGiveMeal3 = 'checked=""';
                break;
                default:
                    $ChildrenNrGiveMeal1 = '';
                    $ChildrenNrGiveMeal2 = '';
                    $ChildrenNrGiveMeal3 = '';
                break;
            }
            ?>
            <ol class="listDalamTd">
                <li class="tdWithInput"><input type="radio" name="ChildrenNrGiveMeal" <?php echo $ChildrenNrGiveMeal1?> /><label><?php echo lang('0 - 2 times')?></label></li>
                <li class="tdWithInput"><input type="radio" name="ChildrenNrGiveMeal" <?php echo $ChildrenNrGiveMeal2?> /><label><?php echo lang('3 - 4 times')?></label></li>
                <li class="tdWithInput"><input type="radio" name="ChildrenNrGiveMeal" <?php echo $ChildrenNrGiveMeal3?> /><label><?php echo lang('More than 5 times')?></label></li>
            </ol>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('Have you been pregnant in the past two years ?')?></td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['MotherPregnant2Years']) {
                case '1':
                    $MotherPregnant2Years1 = 'checked=""';
                    $MotherPregnant2Years2 = '';
                break;
                case '2':
                    $MotherPregnant2Years1 = '';
                    $MotherPregnant2Years2 = 'checked=""';
                break;
                default:
                    $MotherPregnant2Years1 = '';
                    $MotherPregnant2Years2 = '';
                break;
            }
            ?>
            <input type="radio" name="MotherPregnant2Years" <?php echo $MotherPregnant2Years1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="MotherPregnant2Years" <?php echo $MotherPregnant2Years2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo lang('During your pregnancy, did you change your eating habits compared to normal ?')?></td>
        <td colspan="2" class="tdWithInput">
            <?php
            switch ($nutrition['MotherPregnantEat']) {
                case '1':
                    $MotherPregnantEat1 = 'checked=""';
                    $MotherPregnantEat2 = '';
                break;
                case '2':
                    $MotherPregnantEat1 = '';
                    $MotherPregnantEat2 = 'checked=""';
                break;
                default:
                    $MotherPregnantEat1 = '';
                    $MotherPregnantEat2 = '';
                break;
            }
            ?>
            <input type="radio" name="MotherPregnantEat" <?php echo $MotherPregnantEat1?> />&nbsp;&nbsp;<label><?php echo lang('Ya')?></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="MotherPregnantEat" <?php echo $MotherPregnantEat2?> />&nbsp;&nbsp;<label><?php echo lang('Tidak')?></label>
        </td>
    </tr>
    </table>

    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="65%"><?php echo lang('Page')?> - 5</td>
                <td class="kolomKanan" width="35%" align="right">
                    <?php echo lang('N1. Nutrition Basic Data')?>
                </td>
            </tr>
        </table>
    </footer>
</div> <!-- Halaman 5 End -->

<script type="text/javascript">
//cek, jika result form, semua input disabled
<?php if($formNya == "result"){?>
    $(document).ready(function(){
        $("input").prop("readonly", true);

        $('input[type=radio]').click(function(){
            return false;
        });
    });
<?php }?>
</script>

</body>
</html>
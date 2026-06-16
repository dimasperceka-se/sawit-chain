<?php
/**
 * @Author: nikolius
 * @Date:   2017-02-20 15:04:59
 */
$baseurlnya = base_url();
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8"/>
    <title><?php echo lang('Nursery Form')?></title>

    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/nursery_form/nursery.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/nursery_form/nursery-media.css" media="print"/>
    <script src="<?php echo $baseurlnya; ?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
</head>
<body>

<div class="page"> <!-- Halaman 1 Start -->

    <!-- LOGO ATAS (BEGIN) -->
    <table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
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
          <img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
       </td>
    </tr>
    </table>
    <br /><br />
    <!-- LOGO ATAS (END) -->

    <div class="borderHitam">
        <h2 class="judulHalaman">
            <?php echo lang('Nursery Form')?> (<?php echo 'Nursery Nr : '.$nursery['NurseryNr']?>)
        </h2>
        <br />

        <div class="borderHitam" style="padding:3px">
            <?php echo lang('Pengantar')?><br />
            <strong><?php echo lang('Selamat Pagi/Siang/Sore. Nama Saya')?> </strong><br />
            <?php echo lang('Kami dari Swisscontact Indonesia ingin melakukan wawancara dengan bapak/ibu untuk mengetahui kondisi pertanian coklat, dalam rangka program pengembangan pertanian coklat di Indonesia. Maka dari itu kami mohon kesediaan bapak/ibu untuk kami wawancara.')?>
        </div>
        <br />

        <table width="100%">
        <tr>
            <td class="bgBiru" width="20%"><?php echo lang('Province')?></td>
            <td width="30%"><?php echo $dataNurseryOwner['Province']?></td>
            <td class="bgBiru" width="20%"><?php echo lang('District')?></td>
            <td width="30%"><?php echo $dataNurseryOwner['District']?></td>
        </tr>
        <tr>
            <td class="bgBiru"><?php echo lang('ID Number')?></td>
            <td><?php echo $dataNurseryOwner['id']?></td>
            <td class="bgBiru"><?php echo lang('Name')?></td>
            <td><?php echo $dataNurseryOwner['name']?></td>
        </tr>
        </table>
    </div>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="4">
            A. <?php echo lang('Informasi Umum')?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue" width="18%"><?php echo lang('Responsible Type')?></td>
        <td width="32%">
            <?php
            switch ($dataNurseryFormPrint['ResponsibleType']) {
                case 'farmer':
                    $responTypeCheckedFarmer = 'checked=""';
                    $responTypeCheckedStaff = '';
                    $responTypeCheckedOther = '';
                break;
                case 'staff':
                    $responTypeCheckedFarmer = '';
                    $responTypeCheckedStaff = 'checked=""';
                    $responTypeCheckedOther = '';
                break;
                case 'other':
                    $responTypeCheckedFarmer = '';
                    $responTypeCheckedStaff = '';
                    $responTypeCheckedOther = 'checked=""';
                break;
            }
            ?>
            <div class="flexBox"><input type="radio" <?php echo $responTypeCheckedFarmer?> name="ResponsibleType" value="" />1. <?php echo lang('Farmer')?></div>
            <div class="flexBox"><input type="radio" <?php echo $responTypeCheckedStaff?> name="ResponsibleType" value="" />2. <?php echo lang('Staff')?></div>
            <div class="flexBox"><input type="radio" <?php echo $responTypeCheckedOther?> name="ResponsibleType" value="" />3. <?php echo lang('Other')?></div>
        </td>
        <td class="leftValue" width="20%"><?php echo lang('Nursery Ceritification - BP2MB')?></td>
        <td width="30%">
            <?php
            switch ($dataNurseryFormPrint['CertificationStatus']) {
                case 'Yes':
                    $certStatusCheckedYes = 'checked=""';
                    $certStatusCheckedNo = '';
                break;
                case 'No':
                    $certStatusCheckedYes = '';
                    $certStatusCheckedNo = 'checked=""';
                break;
                default:
                    $certStatusCheckedYes = '';
                    $certStatusCheckedNo = 'checked=""';
                break;
            }
            ?>
            <div class="flexBox"><input type="radio" <?php echo $certStatusCheckedYes?> name="NursCertBp2YaTidak" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input <?php echo $certStatusCheckedNo?> type="radio" name="NursCertBp2YaTidak" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Responsible ID')?></td>
        <td><?php echo $dataNurseryFormPrint['ResponsibleID']?></td>
        <td class="leftValue"><?php echo lang('Date of Certificate Issue')?></td>
        <td><?php echo $dataNurseryFormPrint['DateCertification']?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Responsible Name')?></td>
        <td><?php echo $dataNurseryFormPrint['ResponsibleName']?></td>
        <td class="leftValue"><?php echo lang('Date Applied for Certification')?></td>
        <td><?php echo $dataNurseryFormPrint['DateAppliedCertification']?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Responsible Birthdate')?></td>
        <td><?php echo $dataNurseryFormPrint['ResponsibleBirthday']?></td>
        <td class="leftValue"><?php echo lang('Panjang (m)')?></td>
        <td><?php echo $dataNurseryFormPrint['Panjang']?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;meter</td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Responsible Phone')?></td>
        <td><?php echo $dataNurseryFormPrint['ResponsiblePhone']?></td>
        <td class="leftValue"><?php echo lang('Lebar (m)')?></td>
        <td><?php echo $dataNurseryFormPrint['Lebar']?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;meter</td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Responsible Gender')?></td>
        <td>
            <?php
            switch ($dataNurseryFormPrint['ResponsibleGender']) {
                case 'm':
                    $responTypeGenderCheckedMale = 'checked=""';
                    $responTypeGenderCheckedFemale = '';
                break;
                case 'f':
                    $responTypeGenderCheckedMale = '';
                    $responTypeGenderCheckedFemale = 'checked=""';
                break;
            }
            ?>
            <div class="flexBox"><input type="radio" <?php echo $responTypeGenderCheckedMale?> name="ResponsibleGender" value="" />1. <?php echo lang('Male')?></div>
            <div class="flexBox"><input type="radio" <?php echo $responTypeGenderCheckedFemale?> name="ResponsibleGender" value="" />2. <?php echo lang('Female')?></div>
        </td>
        <td class="leftValue"><?php echo lang('Latitude (Dec)')?></td>
        <td><?php echo $dataNurseryFormPrint['Latitude']?></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Tanggal Berdiri')?></td>
        <td><?php echo $dataNurseryFormPrint['Established']?></td>
        <td class="leftValue"><?php echo lang('Longitude (Dec)')?></td>
        <td><?php echo $dataNurseryFormPrint['Longitude']?></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="4">
            B. <?php echo lang('Nursery Checklist')?>
        </td>
    </tr>
    <tr>
        <td class="topTabelValue" width="3%">No</td>
        <td class="topTabelValue"><?php echo lang('PriNur Key Quality Attribute')?></td>
        <td width="17%" class="topTabelValue"><?php echo lang('PriNur Yes / No')?></td>
        <td width="35%" class="topTabelValue"><?php echo lang('PriNur If No, Justification')?></td>
    </tr>
    <tr>
        <td align="center">1.</td>
        <td><?php echo lang('Location with good access to main roads')?></td>
        <td>
            <?php
            switch ($nursery['LocationCloseToCommunity']) {
                case 'Yes':
                    $checkedTanya1Yes = 'checked="checked"';
                    $checkedTanya1No = '';
                break;
                case 'No':
                    $checkedTanya1Yes = '';
                    $checkedTanya1No = 'checked="checked"';
                break;
                default:
                    $checkedTanya1Yes = '';
                    $checkedTanya1No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya1Yes?> type="radio" name="LocationCloseToCommunity" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya1No?> name="LocationCloseToCommunity" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['LocationCloseToCommunityNo']?></td>
    </tr>
    <tr>
        <td align="center">2.</td>
        <td><?php echo lang('Flat, well drained and uniform land area')?></td>
        <td>
            <?php
            switch ($nursery['GoodLandArea']) {
                case 'Yes':
                    $checkedTanya2Yes = 'checked="checked"';
                    $checkedTanya2No = '';
                break;
                case 'No':
                    $checkedTanya2Yes = '';
                    $checkedTanya2No = 'checked="checked"';
                break;
                default:
                    $checkedTanya2Yes = '';
                    $checkedTanya2No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya2Yes?> type="radio" name="GoodLandArea" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="GoodLandArea" <?php echo $checkedTanya2No?> value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['GoodLandAreaNo']?></td>
    </tr>
    <tr>
        <td align="center">3.</td>
        <td><?php echo lang('Located at least 100 metres from cocoa plantations')?></td>
        <td>
            <?php
            switch ($nursery['LocationNearCocoaFarm']) {
                case 'Yes':
                    $checkedTanya3Yes = 'checked="checked"';
                    $checkedTanya3No = '';
                break;
                case 'No':
                    $checkedTanya3Yes = '';
                    $checkedTanya3No = 'checked="checked"';
                break;
                default:
                    $checkedTanya3Yes = '';
                    $checkedTanya3No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya3Yes?> type="radio" name="LocationNearCocoaFarm" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya3No?> name="LocationNearCocoaFarm" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['LocationNearCocoaFarmNo']?></td>
    </tr>
    <tr>
        <td align="center">4.</td>
        <td><?php echo lang('Continuous water supply available')?></td>
        <td>
            <?php
            switch ($nursery['ContinuousWaterSupply']) {
                case 'Yes':
                    $checkedTanya4Yes = 'checked="checked"';
                    $checkedTanya4No = '';
                break;
                case 'No':
                    $checkedTanya4Yes = '';
                    $checkedTanya4No = 'checked="checked"';
                break;
                default:
                    $checkedTanya4Yes = '';
                    $checkedTanya4No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya4Yes?> type="radio" name="ContinuousWaterSupply" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya4No?> name="ContinuousWaterSupply" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['ContinuousWaterSupplyNo']?></td>
    </tr>
    <tr>
        <td align="center">5.</td>
        <td><?php echo lang('Irrigation system installed')?></td>
        <td>
            <?php
            switch ($nursery['IrrigationInstalled']) {
                case 'Yes':
                    $checkedTanya5Yes = 'checked="checked"';
                    $checkedTanya5No = '';
                break;
                case 'No':
                    $checkedTanya5Yes = '';
                    $checkedTanya5No = 'checked="checked"';
                break;
                default:
                    $checkedTanya5Yes = '';
                    $checkedTanya5No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya5Yes?> type="radio" name="IrrigationInstalled" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya5No?> name="IrrigationInstalled" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['IrrigationInstalledNo']?></td>
    </tr>
    <tr>
        <td align="center">6.</td>
        <td><?php echo lang('Use of appropriate shading')?></td>
        <td>
            <?php
            switch ($nursery['UseShadingNet']) {
                case 'Yes':
                    $checkedTanya6Yes = 'checked="checked"';
                    $checkedTanya6No = '';
                break;
                case 'No':
                    $checkedTanya6Yes = '';
                    $checkedTanya6No = 'checked="checked"';
                break;
                default:
                    $checkedTanya6Yes = '';
                    $checkedTanya6No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya6Yes?> type="radio" name="UseShadingNet" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="UseShadingNet" <?php echo $checkedTanya6No?> value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['UseShadingNetNo']?></td>
    </tr>
    <tr>
        <td align="center">7.</td>
        <td><?php echo lang('Adequate supply of top soil or substrate for potting mix')?></td>
        <td>
            <?php
            switch ($nursery['AdequateSupplyTopSoil']) {
                case 'Yes':
                    $checkedTanya7Yes = 'checked="checked"';
                    $checkedTanya7No = '';
                break;
                case 'No':
                    $checkedTanya7Yes = '';
                    $checkedTanya7No = 'checked="checked"';
                break;
                default:
                    $checkedTanya7Yes = '';
                    $checkedTanya7No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya7Yes?> type="radio" name="AdequateSupplyTopSoil" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya7No?> name="AdequateSupplyTopSoil" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['AdequateSupplyTopSoilNo']?></td>
    </tr>
    <tr>
        <td align="center">8.</td>
        <td><?php echo lang('Improved varieties from certified seed and budwood sources')?></td>
        <td>
            <?php
            switch ($nursery['ImprovedVariety']) {
                case 'Yes':
                    $checkedTanya8Yes = 'checked="checked"';
                    $checkedTanya8No = '';
                break;
                case 'No':
                    $checkedTanya8Yes = '';
                    $checkedTanya8No = 'checked="checked"';
                break;
                default:
                    $checkedTanya8Yes = '';
                    $checkedTanya8No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya8Yes?> type="radio" name="ImprovedVariety" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya8No?> name="ImprovedVariety" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['ImprovedVarietyNo']?></td>
    </tr>
    <tr>
        <td align="center">9.</td>
        <td><?php echo lang('Correct equipment is available to operator(s)')?></td>
        <td>
            <?php
            switch ($nursery['CorrectEquipment']) {
                case 'Yes':
                    $checkedTanya9Yes = 'checked="checked"';
                    $checkedTanya9No = '';
                break;
                case 'No':
                    $checkedTanya9Yes = '';
                    $checkedTanya9No = 'checked="checked"';
                break;
                default:
                    $checkedTanya9Yes = '';
                    $checkedTanya9No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya9Yes?> type="radio" name="CorrectEquipment" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya9No?> name="CorrectEquipment" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['CorrectEquipmentNo']?></td>
    </tr>
    <tr>
        <td align="center">10.</td>
        <td><?php echo lang('Wind break installed (if needed)')?></td>
        <td>
            <?php
            switch ($nursery['WindBreakInstalled']) {
                case 'Yes':
                    $checkedTanya10Yes = 'checked="checked"';
                    $checkedTanya10No = '';
                break;
                case 'No':
                    $checkedTanya10Yes = '';
                    $checkedTanya10No = 'checked="checked"';
                break;
                default:
                    $checkedTanya10Yes = '';
                    $checkedTanya10No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya10Yes?> type="radio" name="WindBreakInstalled" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya10No?> name="WindBreakInstalled" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['WindBreakInstalledNo']?></td>
    </tr>
    <tr>
        <td align="center">11.</td>
        <td><?php echo lang('Security fence installed (if needed)')?></td>
        <td>
            <?php
            switch ($nursery['SecurityFenceInstalled']) {
                case 'Yes':
                    $checkedTanya11Yes = 'checked="checked"';
                    $checkedTanya11No = '';
                break;
                case 'No':
                    $checkedTanya11Yes = '';
                    $checkedTanya11No = 'checked="checked"';
                break;
                default:
                    $checkedTanya11Yes = '';
                    $checkedTanya11No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya11Yes?> type="radio" name="SecurityFenceInstalled" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya11No?> name="SecurityFenceInstalled" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['SecurityFenceInstalledNo']?></td>
    </tr>
    <tr>
        <td align="center">12.</td>
        <td><?php echo lang('Fertilizer used in seedling establishment')?></td>
        <td>
            <?php
            switch ($nursery['FertilizerUsed']) {
                case 'Yes':
                    $checkedTanya12Yes = 'checked="checked"';
                    $checkedTanya12No = '';
                break;
                case 'No':
                    $checkedTanya12Yes = '';
                    $checkedTanya12No = 'checked="checked"';
                break;
                default:
                    $checkedTanya12Yes = '';
                    $checkedTanya12No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya12Yes?> type="radio" name="FertilizerUsed" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="FertilizerUsed" <?php echo $checkedTanya12No?> value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['FertilizerUsedNo']?></td>
    </tr>
    <tr>
        <td align="center">13.</td>
        <td><?php echo lang('Operators possess adequate skills')?></td>
        <td>
            <?php
            switch ($nursery['OperatorAdequateTraining']) {
                case 'Yes':
                    $checkedTanya13Yes = 'checked="checked"';
                    $checkedTanya13No = '';
                break;
                case 'No':
                    $checkedTanya13Yes = '';
                    $checkedTanya13No = 'checked="checked"';
                break;
                default:
                    $checkedTanya13Yes = '';
                    $checkedTanya13No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya13Yes?> type="radio" name="OperatorAdequateTraining" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya13No?> name="OperatorAdequateTraining" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['OperatorAdequateTrainingNo']?></td>
    </tr>
    <tr>
        <td align="center">14.</td>
        <td><?php echo lang('Adequate facilities for workers, and requisite safety equipment provided')?></td>
        <td>
            <?php
            switch ($nursery['AdequateFacility']) {
                case 'Yes':
                    $checkedTanya14Yes = 'checked="checked"';
                    $checkedTanya14No = '';
                break;
                case 'No':
                    $checkedTanya14Yes = '';
                    $checkedTanya14No = 'checked="checked"';
                break;
                default:
                    $checkedTanya14Yes = '';
                    $checkedTanya14No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya14Yes?> type="radio" name="AdequateFacility" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="AdequateFacility" <?php echo $checkedTanya14No?> value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['AdequateFacilityNo']?></td>
    </tr>
    <tr>
        <td align="center">15.</td>
        <td><?php echo lang('Sustainable and rational pest and disease control')?></td>
        <td>
            <?php
            switch ($nursery['SustainablePestDisease']) {
                case 'Yes':
                    $checkedTanya15Yes = 'checked="checked"';
                    $checkedTanya15No = '';
                break;
                case 'No':
                    $checkedTanya15Yes = '';
                    $checkedTanya15No = 'checked="checked"';
                break;
                default:
                    $checkedTanya15Yes = '';
                    $checkedTanya15No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya15Yes?> type="radio" name="SustainablePestDisease" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya15No?> name="SustainablePestDisease" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['SustainablePestDiseaseNo']?></td>
    </tr>
    <tr>
        <td align="center">16.</td>
        <td><?php echo lang('Seedling culling is done')?></td>
        <td>
            <?php
            switch ($nursery['SeedlingCullingDone']) {
                case 'Yes':
                    $checkedTanya16Yes = 'checked="checked"';
                    $checkedTanya16No = '';
                break;
                case 'No':
                    $checkedTanya16Yes = '';
                    $checkedTanya16No = 'checked="checked"';
                break;
                default:
                    $checkedTanya16Yes = '';
                    $checkedTanya16No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya16Yes?> type="radio" name="SeedlingCullingDone" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya16No?> name="SeedlingCullingDone" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['SeedlingCullingDoneNo']?></td>
    </tr>
    <tr>
        <td align="center">17.</td>
        <td><?php echo lang('Proper input and sales records are maintained')?></td>
        <td>
            <?php
            switch ($nursery['ProperInputSalesRecord']) {
                case 'Yes':
                    $checkedTanya17Yes = 'checked="checked"';
                    $checkedTanya17No = '';
                break;
                case 'No':
                    $checkedTanya17Yes = '';
                    $checkedTanya17No = 'checked="checked"';
                break;
                default:
                    $checkedTanya17Yes = '';
                    $checkedTanya17No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya17Yes?> type="radio" name="ProperInputSalesRecord" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya17No?> name="ProperInputSalesRecord" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['ProperInputSalesRecordNo']?></td>
    </tr>
    <tr>
    <td align="center">18.</td>
        <td><?php echo lang('Seeds are pre-germinated before planting')?></td>
        <td>
            <?php
            switch ($nursery['SeedsPreGerminated']) {
                case 'Yes':
                    $checkedTanya18Yes = 'checked="checked"';
                    $checkedTanya18No = '';
                break;
                case 'No':
                    $checkedTanya18Yes = '';
                    $checkedTanya18No = 'checked="checked"';
                break;
                default:
                    $checkedTanya18Yes = '';
                    $checkedTanya18No = '';
                break;
            }
            ?>
            <div class="flexBox"><input <?php echo $checkedTanya18Yes?> type="radio" name="SeedsPreGerminated" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php echo $checkedTanya18No?> name="SeedsPreGerminated" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo $nursery['SeedsPreGerminatedNo']?></td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 1 <?php echo lang('from')?> 3
    </footer>
</div> <!-- Halaman 1 End -->

<div class="page"> <!-- Halaman 2 Begin -->
    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="8">
            C. <?php echo lang('Nursery Penjualan')?>
        </td>
    </tr>
    <tr>
        <td class="topTabelValue" width="3%">No</td>
        <td class="topTabelValue" width="17%"><?php echo lang('Pembeli')?></td>
        <td class="topTabelValue" width="12%"><?php echo lang('Bibit Dijual')?></td>
        <td class="topTabelValue" colspan="2"><?php echo lang('Clone Type')?></td>
        <td class="topTabelValue" width="12%"><?php echo lang('Harga Satuan')?></td>
        <td class="topTabelValue" width="12%"><?php echo lang('Total')?></td>
        <td class="topTabelValue" width="12%"><?php echo lang('Tanggal Penjualan')?></td>
    </tr>
    <tr>
        <td class="topTabelValue">&nbsp;</td>
        <td class="topTabelValue">
            <ol class="listHeaderTabel">
                <li><?php echo lang('Anggota Kelompok')?></li>
                <li><?php echo lang('Petani Lain')?></li>
                <li><?php echo lang('Traders')?></li>
                <li><?php echo lang('Pemerintah')?></li>
                <li><?php echo lang('Lainnya')?></li>
            </ol>
        </td>
        <td class="topTabelValue">&nbsp;</td>
        <td class="topTabelValue">
            <ol class="listHeaderTabel">
                <li>TSH 858</li>
                <li>RCC 70</li>
                <li>RCC 71</li>
                <li>RCC 72</li>
                <li>RCC 73</li>
                <li>Local</li>
                <li>S1</li>
                <li>S2</li>
            </ol>
        </td>
        <td class="topTabelValue">
            <ol start="9" class="listHeaderTabel">
                <li>ICRRI 3</li>
                <li>ICRRI 4</li>
                <li>ICRRI 5</li>
                <li>RCL</li>
                <li>M01</li>
                <li>M06</li>
                <li>THR</li>
                <li>45</li>
            </ol>
        </td>
        <td class="topTabelValue">&nbsp;</td>
        <td class="topTabelValue">&nbsp;</td>
        <td class="topTabelValue">&nbsp;</td>
    </tr>
    <?php
    for ($i=0; $i < 50; $i++) {
        $volumeNya = number_format($nurseryPenjualan[$i]['Volume'],0,".",",");
        if($volumeNya == 0) $volumeNya = "";

        $priceNya = number_format($nurseryPenjualan[$i]['Price'],0,".",",");
        if($priceNya == 0) $priceNya = "";

        $totalNya = number_format(($nurseryPenjualan[$i]['Volume'] * $nurseryPenjualan[$i]['Price']),0,".",",");
        if($totalNya == 0) $totalNya = "";

        echo '
        <tr>
            <td align="center">'.($i+1).'</td>
            <td>'.lang($nurseryPenjualan[$i]['Buyer']).'</td>
            <td>'.$volumeNya.'</td>
            <td colspan="2">'.$nurseryPenjualan[$i]['CloneTypeName'].'</td>
            <td>'.$priceNya.'</td>
            <td>'.$totalNya.'</td>
            <td>'.$nurseryPenjualan[$i]['DateTransaction'].'</td>
        </tr>
        ';
    }
    ?>
    </table>

    <footer>
        <?php echo lang('Page')?> 2 <?php echo lang('from')?> 3
    </footer>
</div> <!-- Halaman 2 End -->

<div class="page"> <!-- Halaman 3 Begin -->

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="4">
            D. <?php echo lang('Nursery Monitoring')?>
        </td>
    </tr>
    <tr>
        <td class="topTabelValue" width="3%">No</td>
        <td class="topTabelValue" width="14%"><?php echo lang('Tanggal Kedatangan')?></td>
        <td class="topTabelValue" width="25%"><?php echo lang('Status')?></td>
        <td class="topTabelValue"><?php echo lang('Keterangan')?></td>
    </tr>
    <tr>
        <td class="topTabelValue">&nbsp;</td>
        <td class="topTabelValue">&nbsp;</td>
        <td class="topTabelValue">
            <ol class="listHeaderTabel">
                <li><?php echo lang('Sedang di bangun/Belum selesai')?></li>
                <li><?php echo lang('Berjalan/Produktif')?></li>
                <li><?php echo lang('Tidak Berjalan')?></li>
            </ol>
        </td>
        <td class="topTabelValue">&nbsp;</td>
    </tr>
    <?php
    for ($i=0; $i < 50; $i++) {
        echo '
        <tr>
            <td align="center">'.($i+1).'</td>
            <td>'.$nurseryMonitoring[$i]['MonitoringDate'].'</td>
            <td>'.lang($nurseryMonitoring[$i]['MonitoringStatus']).'</td>
            <td>'.$nurseryMonitoring[$i]['Description'].'</td>
        </tr>
        ';
    }
    ?>
    </table>

    <footer>
        <?php echo lang('Page')?> 3 <?php echo lang('from')?> 3
    </footer>
</div> <!-- Halaman 3 End -->

</body>
</html>
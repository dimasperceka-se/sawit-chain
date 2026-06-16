<?php
/**
 * @Author: nikolius
 * @Date:   2017-02-17 16:50:43
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
        <h2 class="judulHalaman"><?php echo lang('Nursery Form')?></h2><br />

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
            <div class="flexBox"><input type="radio" name="ResponsibleType" value="" />1. <?php echo lang('Farmer')?></div>
            <div class="flexBox"><input type="radio" name="ResponsibleType" value="" />2. <?php echo lang('Staff')?></div>
            <div class="flexBox"><input type="radio" name="ResponsibleType" value="" />3. <?php echo lang('Other')?></div>
        </td>
        <td class="leftValue" width="20%"><?php echo lang('Nursery Ceritification - BP2MB')?></td>
        <td width="30%">
            <div class="flexBox"><input type="radio" name="NursCertBp2YaTidak" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="NursCertBp2YaTidak" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Responsible ID')?></td>
        <td></td>
        <td class="leftValue"><?php echo lang('Date of Certificate Issue')?></td>
        <td>&nbsp;_&nbsp;_/&nbsp;_&nbsp;_/&nbsp;_&nbsp;_</td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Responsible Name')?></td>
        <td></td>
        <td class="leftValue"><?php echo lang('Date Applied for Certification')?></td>
        <td>&nbsp;_&nbsp;_/&nbsp;_&nbsp;_/&nbsp;_&nbsp;_</td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Responsible Birthdate')?></td>
        <td>&nbsp;_&nbsp;_/&nbsp;_&nbsp;_/&nbsp;_&nbsp;_</td>
        <td class="leftValue"><?php echo lang('Panjang (m)')?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;meter</td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Responsible Phone')?></td>
        <td></td>
        <td class="leftValue"><?php echo lang('Lebar (m)')?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;meter</td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Responsible Gender')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="ResponsibleGender" value="" />1. <?php echo lang('Male')?></div>
            <div class="flexBox"><input type="radio" name="ResponsibleGender" value="" />2. <?php echo lang('Female')?></div>
        </td>
        <td class="leftValue"><?php echo lang('Latitude (Dec)')?></td>
        <td></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Tanggal Berdiri')?></td>
        <td>&nbsp;_&nbsp;_/&nbsp;_&nbsp;_/&nbsp;_&nbsp;_</td>
        <td class="leftValue"><?php echo lang('Longitude (Dec)')?></td>
        <td></td>
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
            <div class="flexBox"><input type="radio" name="LocationCloseToCommunity" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="LocationCloseToCommunity" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">2.</td>
        <td><?php echo lang('Flat, well drained and uniform land area')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="GoodLandArea" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="GoodLandArea" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">3.</td>
        <td><?php echo lang('Located at least 100 metres from cocoa plantations')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="LocationNearCocoaFarm" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="LocationNearCocoaFarm" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">4.</td>
        <td><?php echo lang('Continuous water supply available')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="ContinuousWaterSupply" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="ContinuousWaterSupply" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">5.</td>
        <td><?php echo lang('Irrigation system installed')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="IrrigationInstalled" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="IrrigationInstalled" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">6.</td>
        <td><?php echo lang('Use of appropriate shading')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="UseShadingNet" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="UseShadingNet" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">7.</td>
        <td><?php echo lang('Adequate supply of top soil or substrate for potting mix')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="AdequateSupplyTopSoil" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="AdequateSupplyTopSoil" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">8.</td>
        <td><?php echo lang('Improved varieties from certified seed and budwood sources')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="ImprovedVariety" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="ImprovedVariety" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">9.</td>
        <td><?php echo lang('Correct equipment is available to operator(s)')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="CorrectEquipment" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="CorrectEquipment" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">10.</td>
        <td><?php echo lang('Wind break installed (if needed)')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="WindBreakInstalled" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="WindBreakInstalled" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">11.</td>
        <td><?php echo lang('Security fence installed (if needed)')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="SecurityFenceInstalled" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="SecurityFenceInstalled" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">12.</td>
        <td><?php echo lang('Fertilizer used in seedling establishment')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="FertilizerUsed" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="FertilizerUsed" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">13.</td>
        <td><?php echo lang('Operators possess adequate skills')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="OperatorAdequateTraining" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="OperatorAdequateTraining" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">14.</td>
        <td><?php echo lang('Adequate facilities for workers, and requisite safety equipment provided')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="AdequateFacility" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="AdequateFacility" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">15.</td>
        <td><?php echo lang('Sustainable and rational pest and disease control')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="SustainablePestDisease" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="SustainablePestDisease" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">16.</td>
        <td><?php echo lang('Seedling culling is done')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="SeedlingCullingDone" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="SeedlingCullingDone" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td align="center">17.</td>
        <td><?php echo lang('Proper input and sales records are maintained')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="ProperInputSalesRecord" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="ProperInputSalesRecord" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
    </tr>
    <tr>
    <td align="center">18.</td>
        <td><?php echo lang('Seeds are pre-germinated before planting')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="SeedsPreGerminated" value="" />1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="SeedsPreGerminated" value="" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
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
        echo '
        <tr>
            <td align="center">'.($i+1).'</td>
            <td></td>
            <td></td>
            <td colspan="2"></td>
            <td></td>
            <td></td>
            <td></td>
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
            <td></td>
            <td></td>
            <td></td>
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
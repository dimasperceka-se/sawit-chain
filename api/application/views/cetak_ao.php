<?php
/**
 * @Author: nikolius
 * @Date:   2017-01-26 13:31:26
 */
$baseurlnya = base_url();
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8"/>
    <title><?php echo lang('Adoption Observation')?></title>

    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/farmer/gap.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/farmer/gap-media.css" media="print"/>
    <script src="<?php echo $baseurlnya; ?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
</head>
<body>
<?php
if($survey[0]['FarmerID'] != ""){
    foreach ($survey as $key => $dataSurvey) {
?>

<!-- mulai halaman pertama (BEGIN) -->
<div class="page">

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

        <h2 class="judulHalaman">Survey <?php echo lang('Adoption Observation')?></h2>
        <h2 class="judulHalaman"><?php echo lang('GardenNr_titel')?> : <?php echo $dataSurvey['GardenNr']?></h2>
        <br />

        <div class="borderHitam" style="padding:3px">
            <?php echo lang('Pengantar')?><br />
            <strong><?php echo lang('Selamat Pagi/Siang/Sore. Nama Saya')?> </strong><br />
            <?php echo lang('Kami dari Swisscontact Indonesia ingin melakukan wawancara dengan bapak/ibu untuk mengetahui kondisi pertanian coklat, dalam rangka program pengembangan pertanian coklat di Indonesia. Maka dari itu kami mohon kesediaan bapak/ibu untuk kami wawancara.')?>
        </div>
        <br />

        <table width="100%">
        <tr>
            <td class="bgBiru" width="20%"><?php echo lang('Nama Pewawancara')?></td>
            <td width="30%">&nbsp;</td>
            <td class="bgBiru" width="20%"><?php echo lang('Tanggal')?></td>
            <td width="30%">&nbsp;_&nbsp;_/&nbsp;_&nbsp;_/&nbsp;_&nbsp;_</td>
        </tr>
        <tr height="35">
            <td class="bgBiru"><?php echo lang('Survey Year')?></td>
            <td><?php echo $SurveyYear?></td>
            <td class="bgBiru"><?php echo lang('Tanda Tangan')?></td>
            <td></td>
        </tr>
        </table>
        <br />

        <table width="100%">
        <tr>
            <td class="bgBiru" width="20%"><?php echo lang('No. ID Kelompok Tani')?></td>
            <td width="30%"><?php echo $farmer['CPGid']?></td>
            <td class="bgBiru" width="20%"><?php echo lang('No. ID Petani')?></td>
            <td width="30%"><?php echo $farmer['FarmerID']?></td>
        </tr>
        <tr>
            <td class="bgBiru"><?php echo lang('Nama Kelompok Tani')?></td>
            <td><?php echo $farmer['GroupName']?></td>
            <td class="bgBiru"><?php echo lang('Nama Petani')?></td>
            <td><?php echo $farmer['FarmerName']?></td>
        </tr>
        </table>
        <br />

    </div>
    <br />

    <table width="100%">
    <tr>
        <td style="text-align:center;" width="5%" class="leftValue"><strong>No.</strong></td>
        <td style="text-align:center;" width="75%" class="leftValue"><strong><?php echo lang('Question')?></strong></td>
        <td style="text-align:center;" class="leftValue"><strong><?php echo lang('Rating')?></strong></td>
    </tr>
    <tr>
        <?php
        $question1Check_1 = '';
        $question1Check_2 = '';
        $question1Check_3 = '';
        switch ($dataSurvey['PlantingMaterial']) {
            case '1':
                $question1Check_1 = 'checked=""';
            break;
            case '2':
                $question1Check_2 = 'checked=""';
            break;
            case '3':
                $question1Check_3 = 'checked=""';
            break;
            default:
                $question1Check_1 = '';
                $question1Check_2 = '';
                $question1Check_3 = '';
            break;
        }
        ?>
        <td align="center">1.</td>
        <td>
            <?php echo lang('What is the yield potential of Planting Material used at the farm ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="PlantingMaterial<?php echo $key?>" <?php echo $question1Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="PlantingMaterial<?php echo $key?>" <?php echo $question1Check_2;?> /> <?php echo lang('Medium')?></div>
            <div class="flexBox"><input type="radio" name="PlantingMaterial<?php echo $key?>" <?php echo $question1Check_3;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question2Check_1 = '';
        $question2Check_2 = '';
        switch ($dataSurvey['FarmCondTreeDensity']) {
            case '1':
                $question2Check_1 = 'checked=""';
            break;
            case '2':
                $question2Check_2 = 'checked=""';
            break;
            default:
                $question2Check_1 = '';
                $question2Check_2 = '';
            break;
        }
        ?>
        <td align="center">2.</td>
        <td>
            <?php echo lang('Are the trees above or  below the theoretical maximum production threshold ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="FarmCondTreeDensity<?php echo $key?>" <?php echo $question2Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="FarmCondTreeDensity<?php echo $key?>" <?php echo $question2Check_2;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question3Check_1 = '';
        $question3Check_2 = '';
        switch ($dataSurvey['FarmCondTreeAge']) {
            case '1':
                $question3Check_1 = 'checked=""';
            break;
            case '2':
                $question3Check_2 = 'checked=""';
            break;
            default:
                $question3Check_1 = '';
                $question3Check_2 = '';
            break;
        }
        ?>
        <td align="center">3.</td>
        <td>
            <?php echo lang('Does the density of trees support targeted production per ha ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="FarmCondTreeAge<?php echo $key?>" <?php echo $question3Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="FarmCondTreeAge<?php echo $key?>" <?php echo $question3Check_2;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question4Check_1 = '';
        $question4Check_2 = '';
        switch ($dataSurvey['FarmCondTreeHealth']) {
            case '1':
                $question4Check_1 = 'checked=""';
            break;
            case '2':
                $question4Check_2 = 'checked=""';
            break;
            default:
                $question4Check_1 = '';
                $question4Check_2 = '';
            break;
        }
        ?>
        <td align="center">4.</td>
        <td>
            <?php echo lang('Are the trees on a farm healthy enough to support targeted yield ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="FarmCondTreeHealth<?php echo $key?>" <?php echo $question4Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="FarmCondTreeHealth<?php echo $key?>" <?php echo $question4Check_2;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question5Check_1 = '';
        $question5Check_2 = '';
        switch ($dataSurvey['DebilitatingDisease']) {
            case '1':
                $question5Check_1 = 'checked=""';
            break;
            case '2':
                $question5Check_2 = 'checked=""';
            break;
            default:
                $question5Check_1 = '';
                $question5Check_2 = '';
            break;
        }
        ?>
        <td align="center">5.</td>
        <td>
            <?php echo lang('Is the farm free of any signs of major diseases that may imperil the farm ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="DebilitatingDisease<?php echo $key?>" <?php echo $question5Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="DebilitatingDisease<?php echo $key?>" <?php echo $question5Check_2;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question6Check_1 = '';
        $question6Check_2 = '';
        $question6Check_3 = '';
        switch ($dataSurvey['Pruning']) {
            case '1':
                $question6Check_1 = 'checked=""';
            break;
            case '2':
                $question6Check_2 = 'checked=""';
            break;
            case '3':
                $question6Check_3 = 'checked=""';
            break;
            default:
                $question6Check_1 = '';
                $question6Check_2 = '';
                $question6Check_3 = '';
            break;
        }
        ?>
        <td align="center">6.</td>
        <td>
            <?php echo lang('what is the status/condition of Pruning (Tree architecture. Production pruning and maintenance pruning) for supporting or limiting the yield potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="Pruning<?php echo $key?>" <?php echo $question6Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="Pruning<?php echo $key?>" <?php echo $question6Check_2;?> /> <?php echo lang('Medium')?></div>
            <div class="flexBox"><input type="radio" name="Pruning<?php echo $key?>" <?php echo $question6Check_3;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question7Check_1 = '';
        $question7Check_2 = '';
        $question7Check_3 = '';
        switch ($dataSurvey['PestDiseaseSanitation']) {
            case '1':
                $question7Check_1 = 'checked=""';
            break;
            case '2':
                $question7Check_2 = 'checked=""';
            break;
            case '3':
                $question7Check_3 = 'checked=""';
            break;
            default:
                $question7Check_1 = '';
                $question7Check_2 = '';
                $question7Check_3 = '';
            break;
        }
        ?>
        <td align="center">7.</td>
        <td>
            <?php echo lang('What is the P&D and Sanitation condition for supporting or limiting the yield potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="PestDiseaseSanitation<?php echo $key?>" <?php echo $question7Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="PestDiseaseSanitation<?php echo $key?>" <?php echo $question7Check_2;?> /> <?php echo lang('Medium')?></div>
            <div class="flexBox"><input type="radio" name="PestDiseaseSanitation<?php echo $key?>" <?php echo $question7Check_3;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question8Check_1 = '';
        $question8Check_2 = '';
        switch ($dataSurvey['Weeding']) {
            case '1':
                $question8Check_1 = 'checked=""';
            break;
            case '2':
                $question8Check_2 = 'checked=""';
            break;
            default:
                $question8Check_1 = '';
                $question8Check_2 = '';
            break;
        }
        ?>
        <td align="center">8.</td>
        <td>
            <?php echo lang('What is the weeding condition for supporting or limiting the  yield potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="Weeding<?php echo $key?>" <?php echo $question8Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="Weeding<?php echo $key?>" <?php echo $question8Check_2;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question9Check_1 = '';
        $question9Check_2 = '';
        switch ($dataSurvey['Harvesting']) {
            case '1':
                $question9Check_1 = 'checked=""';
            break;
            case '2':
                $question9Check_2 = 'checked=""';
            break;
            default:
                $question9Check_1 = '';
                $question9Check_2 = '';
            break;
        }
        ?>
        <td align="center">9.</td>
        <td>
            <?php echo lang('What is the Harvest condition for supporting or limiting the yield potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="Harvesting<?php echo $key?>" <?php echo $question9Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="Harvesting<?php echo $key?>" <?php echo $question9Check_2;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question10Check_1 = '';
        $question10Check_2 = '';
        switch ($dataSurvey['ShadeManagement']) {
            case '1':
                $question10Check_1 = 'checked=""';
            break;
            case '2':
                $question10Check_2 = 'checked=""';
            break;
            default:
                $question10Check_1 = '';
                $question10Check_2 = '';
            break;
        }
        ?>
        <td align="center">10.</td>
        <td>
            <?php echo lang('What is the shade level for supporting or limiting the yield  potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="ShadeManagement<?php echo $key?>" <?php echo $question10Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="ShadeManagement<?php echo $key?>" <?php echo $question10Check_2;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question11Check_1 = '';
        $question11Check_2 = '';
        switch ($dataSurvey['SoilCondition']) {
            case '1':
                $question11Check_1 = 'checked=""';
            break;
            case '2':
                $question11Check_2 = 'checked=""';
            break;
            default:
                $question11Check_1 = '';
                $question11Check_2 = '';
            break;
        }
        ?>
        <td align="center">11.</td>
        <td>
            <?php echo lang('What is the  soil condition with regards to physical condition and the capacity to transfer nutrients from the soil to root systems and ensure response to fertilizer for supporting or limiting the yield potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="SoilCondition<?php echo $key?>" <?php echo $question11Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="SoilCondition<?php echo $key?>" <?php echo $question11Check_2;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question12Check_1 = '';
        $question12Check_2 = '';
        $question12Check_3 = '';
        switch ($dataSurvey['OrganicMatter']) {
            case '1':
                $question12Check_1 = 'checked=""';
            break;
            case '2':
                $question12Check_2 = 'checked=""';
            break;
            case '3':
                $question12Check_3 = 'checked=""';
            break;
            default:
                $question12Check_1 = '';
                $question12Check_2 = '';
                $question12Check_3 = '';
            break;
        }
        ?>
        <td align="center">12.</td>
        <td>
            <?php echo lang('What is the organic matter on and in the soil and how is the soil heath i.e. Worm, insect activity and microbial life  for supporting or limiting the yield potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="OrganicMatter<?php echo $key?>" <?php echo $question12Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="OrganicMatter<?php echo $key?>" <?php echo $question12Check_2;?> /> <?php echo lang('Medium')?></div>
            <div class="flexBox"><input type="radio" name="OrganicMatter<?php echo $key?>" <?php echo $question12Check_3;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question13Check_1 = '';
        $question13Check_2 = '';
        $question13Check_3 = '';
        switch ($dataSurvey['FertilizerFormulation']) {
            case '1':
                $question13Check_1 = 'checked=""';
            break;
            case '2':
                $question13Check_2 = 'checked=""';
            break;
            case '3':
                $question13Check_3 = 'checked=""';
            break;
            default:
                $question13Check_1 = '';
                $question13Check_2 = '';
                $question13Check_3 = '';
            break;
        }
        ?>
        <td align="center">13.</td>
        <td>
            <?php echo lang('What kind (formulation) of Fertilizer is used at the farm ie. nutrient content, nutrient balance and non-acidifying and does it support or limit the yield potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="FertilizerFormulation<?php echo $key?>" <?php echo $question13Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="FertilizerFormulation<?php echo $key?>" <?php echo $question13Check_2;?> /> <?php echo lang('Medium')?></div>
            <div class="flexBox"><input type="radio" name="FertilizerFormulation<?php echo $key?>" <?php echo $question13Check_3;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    <tr>
        <?php
        $question14Check_1 = '';
        $question14Check_2 = '';
        $question14Check_3 = '';
        switch ($dataSurvey['FertilizerApplication']) {
            case '1':
                $question14Check_1 = 'checked=""';
            break;
            case '2':
                $question14Check_2 = 'checked=""';
            break;
            case '3':
                $question14Check_3 = 'checked=""';
            break;
            default:
                $question14Check_1 = '';
                $question14Check_2 = '';
                $question14Check_3 = '';
            break;
        }
        ?>
        <td align="center">14.</td>
        <td>
            <?php echo lang('How is fertilizer used i.e. Dosage, timing and application technique, and does it support or limit the yield potential of the planting material ?')?>
        </td>
        <td>
            <div class="flexBox"><input type="radio" name="FertilizerApplication<?php echo $key?>" <?php echo $question14Check_1;?> /> <?php echo lang('Good')?></div>
            <div class="flexBox"><input type="radio" name="FertilizerApplication<?php echo $key?>" <?php echo $question14Check_2;?> /> <?php echo lang('Medium')?></div>
            <div class="flexBox"><input type="radio" name="FertilizerApplication<?php echo $key?>" <?php echo $question14Check_3;?> /> <?php echo lang('Bad')?></div>
        </td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 1 <?php echo lang('from')?> 3
    </footer>
</div>
<!-- mulai halaman pertama (END) -->

<?php
    }
}
?>
</body>
</html>
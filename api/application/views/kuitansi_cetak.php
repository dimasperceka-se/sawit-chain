<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8">
	<title>Farmer</title>

<style type="text/css">

@media all {
	.page-break	{ display: none; }
}

@media print {
    @page { margin: 0.1cm; padding:0cm; }
   .page-break  { display: block; page-break-before: always; }
   .page-break-after { display: block; page-break-after: always; }

}

body {
	margin:0;
	padding:0;
	line-height: 1.5em;
	font-family: "Trebuchet MS", Verdana, Helvetica, Arial;
	font-size: 14px;
	color: #000000;
    background-color: #ffffff;
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
	height: 60px;
	text-align: center;
	font-weight: bold;
	font-size: 24px;
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
	padding: 10px;
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
	padding: 6px;
	padding-left: 10px;
	background: #cccccc;
	font-size: 14px;
	font-weight: bold;
	color: #000000;
  border-bottom: 1px solid #000000;
  text-align:left;
}
.post_title {
	padding: 6px;
	padding-left: 10px;
	background: #cccccc;
	font-size: 14px;
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
	font-size: 13px;
	font-weight: bold;
	color: #000000;
	display: block;
	width: auto;
	margin-bottom: 2px;
	padding: 5px;
	padding-left: 12px;
	text-decoration: none;
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
</style>
</head>
<body>
   <div id="templatemo_container_wrapper">
      <div id="templatemo_container">
         <div id="templatemo_header">
         <table width="100%">
            <tr><td height="60" width="200px" align="center" style="vertical-align:middle;">
                  <img height="50" src="<?=base_url()?>images/Photo_trader/<?=$data['Photo']?>"></td>
               <td height="60"  align="center" style="vertical-align:middle;text-decoration:underline;">WEIGHT NOTE</td>
               <td height="60" width="200px" align="center" style="vertical-align:middle;">No : <?=$data['TransactionID']?></td></tr>
         </table>
         </div><br>
         <div class="text_area">
         <table width="100%">
            <tr><td width="10%">Nama</td><td width="40%">: <?=$data['FarmerName']?></td>
               <td width="10%">Date</td><td width="40%">: <?=$data['DateTransaction']?></td></tr>
            <tr><td>Farmer ID</td><td>: <?=$data['FarmerID']?></td><td>Group ID</td><td>: <?='['.$data['CPGid'].'] '.$data['GroupName']?></td></tr>
            <tr><td>Colly</td><td>: <?//=$data['FarmerName']?></td><td>Vehicle No</td><td>: <?//=$data['DateTransaction']?></td></tr>
         </table>
          <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
            <thead class="post_title_main" style="text-align:center">
               <tr><th style="border:1px solid" width="55%">Bag</th><th style="border:1px solid" width="15%">Type</th>
                  <th style="border:1px solid" width="15%">Weight (Kg)</th><th style="border:1px solid" width="15%">Moisture (%)</th></tr>
            </thead>
            <tbody>
            <?for ($i=0;$i<5;$i++){
               $bruto += $detail['data'][$i]['Weight'];
               $package += $detail['data'][$i]['PackageWeight']?>
               <td style="border:1px solid"><?=$detail['data'][$i]['PackageType']?>&nbsp;</td>
               <td style="border:1px solid;text-align:center"><?=$detail['data'][$i]['Type']?></td>
               <td style="border:1px solid;text-align:right"><?=$detail['data'][$i]['Weight']?></td>
               <td style="border:1px solid;text-align:center"><?=$detail['data'][$i]['Moisture']?></td></tr>
            <?}?>
            </tbody>
         </table>
         <table width="35%">
            <tr><th></th><th>FF</th><th>FAQ</th></tr>
            <tr><td width="30%">Bruto</td><td align="right"><?=$data['FFVolumeBruto']?> Kg</td>
               <td align="right"><?=$data['FAQVolumeBruto']?> Kg</td></tr>
            <tr><td>Package</td><td align="right"><?=$data['FFVolumePackage']?> Kg</td>
               <td align="right"><?=$data['FAQVolumePackage']?> Kg</td></tr>
            <tr><td>Netto</td><td align="right"><?=$data['FFVolumeNetto']?> Kg</td>
               <td align="right"><?=$data['FAQVolumeNetto']?> Kg</td></tr>
         <table>
          <table width="100%" cellspacing="0" style="border: 2px double;border-collapse:collapse;">
            <thead class="post_title_main" style="text-align:center">
               <tr><th style="border:1px solid" rowspan="2">Detail</th><th colspan="3" style="border-right: 1px solid;">FF</th><th colspan="3">FAQ</th></tr>
               <tr>
                  <th style="border:1px solid">Test Result</th>
                  <th style="border:1px solid">Standar</th>
                  <th style="border:1px solid">Reward</th>
                  
                  <th style="border:1px solid">Test Result</th>
                  <th style="border:1px solid">Standar</th>
                  <th style="border:1px solid">Reward</th>
               </tr>
            </thead>
            <tbody style="text-align:center">
               <td style="border:1px solid" align="left">Moisture</td>
                  <td style="border:1px solid"><?=$data['FFQualityKA']?></td><td style="border:1px solid"><?=$data['FFMoisture']?></td>
                     <td style="border:1px solid"><?=$data['FFRewardMoisture']?></td>
                  <td style="border:1px solid"><?=$data['FAQQualityKA']?></td><td style="border:1px solid"><?=$data['FAQMoisture']?></td>
                     <td style="border:1px solid"><?=$data['FAQRewardMoisture']?></td></tr>
               <td style="border:1px solid" align="left">Bean Count</td>
                  <td style="border:1px solid"><?=$data['FFQualityBC']?></td><td style="border:1px solid"><?=$data['FFBeanCount']?></td>
                     <td style="border:1px solid"><?=$data['FFRewardBC']?></td>
                  <td style="border:1px solid"><?=$data['FAQQualityBC']?></td><td style="border:1px solid"><?=$data['FAQBeanCount']?></td>
                     <td style="border:1px solid"><?=$data['FAQRewardBC']?></td></tr>
               <td style="border:1px solid" align="left">Mouldy</td>
                  <td style="border:1px solid"><?=$data['FFQualityMouldy']?></td><td style="border:1px solid"><?=$data['FFMouldy']?></td>
                     <td style="border:1px solid"><?=$data['FFRewardMouldy']?></td>
                  <td style="border:1px solid"><?=$data['FAQQualityMouldy']?></td><td style="border:1px solid"><?=$data['FAQMouldy']?></td>
                     <td style="border:1px solid"><?=$data['FAQRewardMouldy']?></td></tr>
               <td style="border:1px solid" align="left">Insect</td>
                  <td style="border:1px solid"><?=$data['FFQualityInsect']?></td><td style="border:1px solid"><?=$data['FFInsect']?></td>
                     <td style="border:1px solid"><?=$data['FFRewardInsect']?></td>
                  <td style="border:1px solid"><?=$data['FAQQualityInsect']?></td><td style="border:1px solid"><?=$data['FAQInsect']?></td>
                     <td style="border:1px solid"><?=$data['FAQRewardInsect']?></td></tr>
               <td style="border:1px solid" align="left">Slaty</td>
                  <td style="border:1px solid"><?=$data['FFQualitySlaty']?></td><td style="border:1px solid"><?=$data['FFSlaty']?></td>
                     <td style="border:1px solid"><?=$data['FFRewardSlaty']?></td>
                  <td style="border:1px solid"><?=$data['FAQQualitySlaty']?></td><td style="border:1px solid"><?=$data['FAQSlaty']?></td>
                     <td style="border:1px solid"><?=$data['FAQRewardSlaty']?></td></tr>
               <td style="border:1px solid" align="left">Waste</td>
                  <td style="border:1px solid"><?=$data['FFQualityWaste']?></td><td style="border:1px solid"><?=$data['FFWaste']?></td>
                     <td style="border:1px solid"><?=$data['FFRewardWaste']?></td>
                  <td style="border:1px solid"><?=$data['FAQQualityWaste']?></td><td style="border:1px solid"><?=$data['FAQWaste']?></td>
                     <td style="border:1px solid"><?=$data['FAQRewardWaste']?></td></tr>
            </tbody>
         </table>
         <strong>Reward FF: Moisture < 5%, Bean Count < 100 ( x 0,2 % ), Waste < 2 %</strong>
         <table width="75%">
            <tr><th></th><th>FF</th><th>FAQ</th></tr>
            <tr><td>Net Price</td><td>Rp <?=number_format($data['FFNetPrice'],2,',','.')?></td>
               <td>Rp <?=number_format($data['FAQNetPrice'],2,',','.')?></td></tr>
            <tr><td>Total Payment</td><td>Rp <?=number_format($data['FFNetPrice'],2,',','.').' X '.$data['FFVolumeNetto'].' = Rp '.
               (number_format($data['FFNetPrice']*$data['FFVolumeNetto'],2,',','.'))?></td>
               <td>Rp <?=number_format($data['FAQNetPrice'],2,',','.').' X '.$data['FAQVolumeNetto'].' = Rp '.
               (number_format($data['FAQNetPrice']*$data['FAQVolumeNetto'],2,',','.'))?></td></tr>
         <table>
         <br><br>
         <table width="100%" style="text-align:center">
            <tr><td>Weight By<td><td>Admin<td><td>Supervisor<td></tr>
            <tr><td><br><br><br><br><td><td><td><td><td></tr>
            <tr><td>(.............)<td><td>(.............)<td><td>(.............)<td></tr>
         <table>
         </div>
      </div>
   </div>
</body>
</html>

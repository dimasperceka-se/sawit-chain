<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Daftar Hadir</title>

<style type="text/css">

@media all {
    .page-break	{ display: none; }
}

@media print {
    @page { margin: 0.5cm; padding:0cm; }
    .page-break  { display: block; page-break-before: always; }
    .page-break-after { display: block; page-break-after: always; }

}

body {
    margin:0;
    padding:0;
    line-height: 1.5em;
    font-family: Tahoma, Verdana, Helvetica, Arial;
    font-size: 14px;
    color: #000000;
    background-color: #ffffff;
}
a:link, a:visited { color: #0066CC; text-decoration: none}
a:active, a:hover { color: #008800; text-decoration: underline}

#templatemo_container_wrapper {
    /*background: url(images/templatemo_side_bg.gif) repeat-x;*/
    background: #ffffff;
    margin:1px 15px 1px 15px;
}
#templatemo_container {
    margin: 1px auto;
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
    padding-top: 12px;
    height: 60px;
    text-align: center;
    font-weight: bold;
    font-size: 20px;
    color: #000000;
    /*background: url(images/templatemo_header_bg.gif) no-repeat;*/
}
#templatemo_header2 {
    clear: left;
    padding-top: 12px;
    height: 110px;
    text-align: center;
    font-weight: bold;
    font-size: 20px;
    color: #000000;
    /*background: url(images/templatemo_header_bg.gif) no-repeat;*/
}
#inner_header {
    height: 30px;
    background: url(images/templatemo_header.jpg) no-repeat center center;
}
#templatemo_left_column {
    width: 100%;
    page-break-after: always;
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
    margin-top: 5px;
    background: #ffffff;
    font-size: 12px;
    font-weight: bold;
}
.section_box3 {
    clear: left;
    margin-top: 10px;
    background: #ffffff;
    font-size: 12px;
}
.text_area {
    padding: 1px;
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
    font-size: 16px;
    font-weight: bold;
    color: #000000;
}
.post_title {
    padding: 6px;
    padding-left: 10px;
    background: #cccccc;
    font-size: 16px;
    font-weight: bold;
    color: #000000;
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
     color: #FFF;
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
        <div id="templatemo_header" style="height:90px !important;">
            <table width="100%" cellspacing="0">
                <tr><td height="60" width="200px" align="center" style="vertical-align:middle;"><img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:100%; max-height:100%;"></td>
                    <td height="60" align="center" style="vertical-align:middle;">Daftar Hadir<br /><span style="text-decoration:underline;"><?=$data['CpgTrainings']?></span></td>
                    <td height="60" width="200px" align="center" style="vertical-align:middle;"><img src="<?=base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;"></td>
                </tr>
                <tr>
                    <td colspan="3" style="font-size:12px;font-weight:normal;"><?php echo $data['subtopics']?></td>
                </tr>
            </table>
        </div>

        <div id="templatemo_left_column">
            <div class="text_area" align="center">
                <div class="section_box2" align="center">
                    <div class="text_area">
                        <br>
                        <table width="100%" cellspacing="0" style="border : 1px solid #000000;">
                            <tr style="background-color: #CCCCCC;padding:2px;"><td width="50%" colspan="2">TRAINING</td>
                                <td width="50%" colspan="2"></td></tr>
                            <tr style="padding:4px;"><td width="30%">Petugas Lapangan</td><td width="20%"><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?=$data['koordinator']?>" disabled size="25"></td>
                                <td width="30%">Provinsi</td><td width="20%"><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?=$data['Provinsi']?>" disabled size="25"></td></tr>
                            <tr><td>Tempat ToT</td><td><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?=$data['TotLocation']?>" disabled size="25"></td>
                                <td>Kabupaten</td><td><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?=$data['Kabupaten']?>" disabled size="25"></td></tr>
                            <tr><td>Tanggal Pertama Pelatihan</td><td><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?=$data['TrainingStart']?>" disabled size="25"></td>
                                <td>Tanggal Terakhir Pelatihan</td><td><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?=$data['TrainingEnd']?>" disabled size="25"></td></tr>
                        </table>
                    </div>
                </div>
                <div class="section_box3" align="center">
                    <div class="text_area">
                        <table width="100%" border="1" cellspacing="0" style="border:1px solid #000000;">
                            <tr style="background-color: #CCCCCC;border:1px solid #000000;" align= "center">
                                <td><strong>Nomor</strong></td2>
                                <td><strong>Nama Petani</strong></td>
                                <td><strong>L/P</strong></td>
                                <td><strong>Kecamatan</strong></td>
                                <?php
                                if($data['TrainingDayStatus'] == "full"){
                                    echo '<td><strong>Kehadiran Pagi</strong></td>
                                    <td><strong>Kehadiran Siang</strong></td>';
                                }else{
                                    echo '<td colspan="2"><strong>Kehadiran</strong></td>';
                                }
                                ?>
                                </tr>
                            <?for ($i=0;$i<sizeof($peserta);$i++)
                            {?>
                            <tr style="border:1px solid #000000;"><td align = "center" rowspan="2" height="50"><?=$peserta[$i]['pFarmerID']?></td>
                                <td height="25">&nbsp;&nbsp;<?=$peserta[$i]['PersonNm']?></td>
                                <td align = "center" height="25" style="text-align:center"><?=($peserta[$i]['Gender']=='1')?'L':'P'?></td>
                                <td rowspan="2">&nbsp;&nbsp;<?=$peserta[$i]['Kecamatan']?></td>
                                <?php if ($data['TrainingDayStatus'] == "full"): ?>
                                    <td style="text-align: center;" rowspan="2"><?php echo (!empty($peserta[$i]['Attendance1'])?lang('Ya'):'') ?></td>
                                    <td style="text-align: center;" rowspan="2"><?php echo (!empty($peserta[$i]['Attendance2'])?lang('Ya'):'') ?></td>
                                <?php else: ?>
                                    <td style="text-align: center;" rowspan="2"><?php echo (!empty($peserta[$i]['Attendance1'])?lang('Ya'):'') ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;<?=$peserta[$i]['AnggotaName']?></td>
                                <td align = "center"><?=(trim($peserta[$i]['AnggotaName'])!=='')?(($peserta[$i]['AnggotaGender']=='1')?'L':'P'):''?></td>
                            </tr>
                            <?
                            if($i == 14 OR ($i>14 and ($i-14)%18==0))
                            {?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-break"></div>
        <div class="page-break"></div>


        <!--         <div id="templatemo_header2">
                <table width="770px" cellspacing="0">
                    <tr><td height="90" rowspan="2" width="200px" align="center"><img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:100%; max-height:100%;"></td>
                        <td height="45" width="470px" align="center">Daftar Hadir</td>
                        <td height="90" rowspan="2"width="200px" align="center"><img src="<?=base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;"></td>
                    </tr>
                    <tr>
                        <td height="90" style ="text-decoration: underline"><?=$data['CpgTrainings']?></td>
                    </tr>
                </table>
            </div>
-->

        <div id="templatemo_left_column">
            <div class="text_area" align="center">
                <div class="section_box3" align="center">
                    <div class="text_area">
                        <table width="100%" border="1px" cellspacing="0">
                            <tr style="background-color: #CCCCCC;" align= "center">
                                <td><strong>Nomor</strong></td2>
                                <td><strong>Nama Petani</strong></td>
                                <td><strong>L/P</strong></td>
                                <td><strong>Kecamatan</strong></td>
                                <?php
                                if($data['TrainingDayStatus'] == "full"){
                                    echo '<td><strong>Kehadiran Pagi</strong></td>
                                    <td><strong>Kehadiran Siang</strong></td>';
                                }else{
                                    echo '<td colspan="2"><strong>Kehadiran</strong></td>';
                                }
                                ?>
                            <?}
                            }?>
                        </table>
                    </div>
                </div>

                <div class="section_box3" align="center">
                    <div class="text_area">
                        <table width="100%"  cellspacing="0">
                            <tr><td>Hari dan Tanggal Pelatihan :</td><td colspan="2"></td></tr>
                            <tr><td width="255px" align="center">Koordinator Lapangan</td>
                                <td width="255px" align="center">Fasilitator Mitra</td></tr>
                            <tr><td height="80">&nbsp;</td><td>&nbsp;</td></tr>
                            <tr><td width="255px" align="center" style ="text-decoration: underline"><strong><?=$data['koordinator']?></strong></td>
                                <td width="255px" align="center" style ="text-decoration: underline"><strong><?=$data['private_staff']?></strong></td></tr>
                            <tr><td width="255px" align="center"><strong><?=$data['Partner']?></strong></td>
                                <td width="255px" align="center""><strong><?=$data['PrivateStaffPartner']?></strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="page-break"></div> -->
</body>
</html>

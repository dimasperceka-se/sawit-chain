<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Daftar Hadir</title>
 

    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            width: 21cm;
            height:27.5cm;
            padding-top: 1cm;
            padding-bottom: 1cm;
            padding-right: 0.5cm;
            padding-left: 0.5cm;
            margin: 0.2cm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        @page {
            size: A4;
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

        input {border: 1px solid;background-color: #FFF}
        input:disabled {color: #000000;}
        .line{border: 1px solid black;}
        td{vertical-align:top}
        @media all {
           .header{  font-size: 10pt;font-family: verdana;padding: 0px;margin: 0px;border: 1px solid;font-weight: bold;}
           .body{font-size: 9pt;font-family: verdana;font-weight: normal;padding: 0px;margin: 0px;border: 1px solid;}
           .header_div{width: 100%; height: 20px; margin-bottom: -28px; border-top: 28px solid #cccccc;}
        }
        textarea {
            font-family: Tahoma, Verdana, Helvetica, Arial;
            padding: 0;
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
            height: 25px;   /* 'padding-top' + 'height' must be equal to the 'background image height' */
            padding-top: 42px;
            padding-left: 30px;
            background: url(images/templatemo_top_bg.gif) no-repeat bottom;
        }
        #templatemo_header {
            clear: left;
            padding-top: 2px;
            height: 50px;
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
        }
        .section_box3 {
            clear: left;
            margin-top: 10px;
            background: #ffffff;
            color: #000000;
            border: 1px;
        }
        .text_area {
            padding: 0px 2px 0px 2px;
            font-size: 14px;
            line-height:20px;
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
            padding: 2px;
            padding-left: 10px;
            background: #cccccc;
            font-size: 12px;
            font-weight: bold;
            color: #000000;
          border-bottom: 1px solid #000000;
          text-align:left;
        }
        .post_title {
            padding: 2px;
            padding-left: 10px;
            background: #cccccc;
            font-size: 12px;
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
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            display: block;
            width: auto;
            margin-bottom: 2px;
            padding: 5px;
            padding-left: 12px;
            text-decoration: none;
        }
        .box {
          border:1px solid #000000;
          font-family: Tahoma, Verdana, Helvetica, Arial;
          color: #000000;
          font-size: 11px;
        }
        .box13 {
            border:1px solid #000000;
            font-size: 11px;
        }
        .box_disabled {
            border:1px solid #000000;
            background-color:#CCCCCC;
        }
        .font11 {
          font-size: 11px;
        }
        .font12 {
            font-size: 11px;
        }
        .font13 {
            font-size: 11px;
        }
        .fontred {
            font-size: 12px;
            color:#F00;
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
        font_red {
            color: #F00;
        }
        input{border: 1px solid #000000;background-color: #FFF}
        .page-break {
            page-break-before: always;
        }
		
    </style>

</head>
 
<body id="page">
        <div class="page">
<div id="templatemo_container_wrapper">
    <div id="templatemo_container">
        <table width="100%" border="0" cellpadding="2">
                <tr> 
					<?php $i=0; if($logo): $cnt = count($logo); foreach($logo as $Rows): $persen = 100/$cnt; ?> 
						<td height="60px" width="<?php echo $persen;?>%" align="center" style="vertical-align:middle;">  
							<img src="<?php echo base_url() ?><?php echo $Rows->PhotoPath; ?>"  style="max-width:90%; max-height:90%; max-width:120px;">
						</td>
					<?php $i++; endforeach; endif; ?> 
                </tr>
            </table>
			 <br /><br />
        <div id="templatemo_header" style="height:110px; margin-top:-15px;">
            
            <table width="100%" border="0" cellpadding="2" style="font-size: 16px; margin-top: -10px;">
                <tr>
                    <td align="center" style="vertical-align:middle;"><span style="text-decoration:underline;">Daftar Hadir</span><br/>
                        <span style=""><?php echo $data['EventName']?> <br> <?php echo $data['CpgTrainings']?> </span> 
                    </td>
                </tr>
            </table>
        </div>
		 
        <div id="templatemo_left_column">
            <div class="text_area" align="center">
                <div class="section_box2" align="center">
                    <div class="text_area">
                        <br>
                        <table width="100%" cellspacing="0" style="border : 1px solid #000000;">
                            <tr style="background-color: #CCCCCC;padding:2px;"><td width="50%" colspan="2">SOSIALISASI BATCH <?php echo $data['BatchNumber']?></td>
                                <td width="50%" colspan="2"></td></tr>
                            <tr style="padding:4px;"><td width="30%">Petugas Lapangan</td><td width="20%"><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?php echo $data['PersonNm']?>" disabled size="25"></td>
                                <td width="30%">Provinsi</td><td width="20%"><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?php echo $data['Province']?>" disabled size="25"></td></tr>
                            <tr><td>Tempat ToT</td><td><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?php echo $data['Location']?>" disabled size="25"></td>
                                <td>Kabupaten</td><td><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?php echo $data['District']?>" disabled size="25"></td></tr>
                            <tr><td>Tanggal Absen</td><td><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?php echo @$eventDate; ?>" disabled size="25"></td>
                                <td>Hari</td><td><input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="<?php echo @$days; ?>" disabled size="25"></td></tr>
                        </table>
                    </div>
                </div>
				
				<div class="section_box3" align="center">
					<div class="text_area">
						<table width="100%" border="1" cellspacing="0" style="border:1px solid #000000;">
							<tr style="background-color: #CCCCCC;border:1px solid #000000;" align= "center">
								<td><strong>Nomor</strong></td>
								<td><strong>Nama Petani</strong></td>
								<td><strong>L/P</strong></td>
								<td><strong>Kelompok Tani</strong></td>
								<td><strong>Desa</strong></td>
								<td><strong>TTD</strong></td>
							</tr>
							<?php for ($i=0;$i < count($peserta);$i++) { ?> 
							<tr style="border:1px solid #000000;">
								<td align = "center" height="50"><?php echo  $peserta[$i]['DisplayID']?></td>
								<td height="25">&nbsp;&nbsp;<?php echo $peserta[$i]['Fullname']?></td>
								<td align = "center" height="25" style="text-align:center"><?php echo  ($peserta[$i]['Gender']=='m')?'L':'P'?></td>
								<td>&nbsp;&nbsp;<?php echo $peserta[$i]['GroupName']?></td>
								<td>&nbsp;&nbsp;<?php echo $peserta[$i]['VillageName']?></td> 
								<td>&nbsp;
									<?php 
									$path =''; 
									if(@$peserta[$i]['ParticipateInSocializationStatus'] == 1){
												
										if(@$peserta[$i]['AttendanceSign'] != '')
										{
											// $path = base_url().'files/socialization_event/'.$peserta[$i]['AttendanceSign'];  
                                            $path ='HADIR';
										}
										else
										{
                                        if ($peserta[$i]['ApplicantID'] != ''){
                                            $this->db->where('ApplicantID', $peserta[$i]['ApplicantID']);
                                            $appFarmers = $this->db->select('LearningContractSign')->from('ktv_ims_socialization_participants')->get()->row();  
                                            if($appFarmers){
                                                if($appFarmers->LearningContractSign == ''){
                                                    $path ='HADIR';
                                                }else{
                                                    // $path = base_url().'files/learning_contract/'.$appFarmers->LearningContractSign;
                                                    $path ='HADIR';
                                                }
                                            } else {
                                                $path ='HADIR';
                                            }                                                                          
                                        } else {
                                            $path ='HADIR';
                                        }
										}
									}else{
										if(@$peserta[$i]['ParticipateInSocializationStatus'] == 2) {
                                            $path = 'TIDAK HADIR'; }
                                        else { 
                                            $path ='';
                                        }
									}
									
									?>
									<?php if($path == 'HADIR' || $path == 'TIDAK HADIR') { echo $path;}else{ ?>
									<img src="<?php echo $path;?>" style="width:100px;">
									<?php } ?>
								</td>
							</tr> 
							<?php  if($i == 14 OR ($i>14 and ($i-14)%18==0)) {?>
						</table> 
					</div>
				</div>				
            </div>
        </div>
			
        <div class="page-break"></div>
        <div class="page-break"></div>


        
		<div id="templatemo_left_column">
            <div class="text_area" align="center">
                <div class="section_box3" align="center">
                    <div class="text_area">
                        <table width="100%" border="1px" cellspacing="0">
                            <tr style="background-color: #CCCCCC;border:1px solid #000000;" align= "center">
								<td><strong>Nomor</strong></td>
								<td><strong>Nama Petani</strong></td>
								<td><strong>L/P</strong></td>
								<td><strong>Kelompok Tani</strong></td>
								<td><strong>Desa</strong></td> 
								<td><strong>TTD</strong></td>								
                            <?}
                            }?>
                        </table>
                    </div>
                </div> 
            </div>
        </div>
    </div>
	
    <div id="templatemo_left_column">
    <div class="text_area" align="center"> 	
	<div class="section_box3" align="center" style="padding-top:15px;">
		<div class="text_area">
			<table width="100%" border="1" cellspacing="0" style="border:1px solid #000000;">
				<!-- <tr style="background-color: #CCCCCC;border:1px solid #000000;" align= "center">
					<td><strong>Nama Staff</strong></td> 
					<td><strong>L/P</strong></td>
					<td><strong>Telp</strong></td> 
					<td><strong>TTD</strong></td>
				</tr> -->
				<?php for ($f=0;$f < count($staff);$f++) { ?> 
				<tr style="border:1px solid #000000;">
					<td height="50"><?php echo  $staff[$f]['PersonNm']?> </td>
					<td align = "center" height="25" style="text-align:center"><?php echo @$staff[$i]['Gender']=='m' ?'L':'P'?></td>
					<td>&nbsp;&nbsp; <?php echo  $staff[$f]['OfficialPhone'] == 0 ? '-' : $staff[$f]['OfficialPhone']; ?></td>
					<td>&nbsp;&nbsp; </td>  
				</tr> 
				<?php  if($f == 14 OR ($f>14 and ($f-14)%18==0)) {?>
			</table>
		</div>
	</div>
	
	<div class="page-break"></div>
    <div class="page-break"></div>
	
	<div id="templatemo_left_column">
            <div class="text_area" align="center"> 
				 
				<div class="section_box3" align="center">
                    <div class="text_area">
                        <table width="100%" border="1px" cellspacing="0">
                            <tr style="background-color: #CCCCCC;border:1px solid #000000;" align= "center">
								<td><strong>Nama Staff</strong></td> 
								<td><strong>L/P</strong></td>
								<td><strong>Telp</strong></td> 
								<td><strong>TTD</strong></td>
							</tr>
                            <?}
                            }?>
                        </table>
                    </div>
                </div> 
				
                <div class="section_box3" align="center">
                    <div class="text_area">
                        <table width="100%"  cellspacing="0">
                            <tr>
							<td>Hari dan Tanggal Pelatihan : <?php echo @$days; ?> / <?php echo @$eventDate; ?></td>
							<td colspan="2"></td>
							</tr>
                            <tr>
								<td width="255px" align="center">Koordinator Lapangan</td> 
							</tr>
                            <tr>
								<td height="80">&nbsp;</td> 
							</tr>
                            <tr>
								<td width="255px" align="center" style ="text-decoration: underline"><strong><?php echo $data['PersonNm']?></strong></td> 
							</tr>
                             
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
	 
    <!-- <div class="page-break"></div> -->
    </div>
	 
</body>
</html>

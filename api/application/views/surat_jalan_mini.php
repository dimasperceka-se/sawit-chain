<?php 
//print_r($batch);
function angka($int){
    $hasil = number_format((float)$int, 2, '.', ','); 
    return $hasil;
}
function tanggal($date){
    $d = explode("-", $date);
    if($d[1]=='01'){
        $bulan = lang("Januari");
    }else if($d[1]=='02'){
        $bulan = lang("Februari");
    }else if($d[1]=='03'){
        $bulan = lang("Maret");
    }else if($d[1]=='04'){
        $bulan = lang("April");
    }else if($d[1]=='05'){
        $bulan = lang("Mei");
    }else if($d[1]=='06'){
        $bulan = lang("Juni");
    }else if($d[1]=='07'){
        $bulan = lang("Juli");
    }else if($d[1]=='08'){
        $bulan = lang("Agustus");
    }else if($d[1]=='09'){
        $bulan = lang("September");
    }else if($d[1]=='10'){
        $bulan = lang("Oktober");
    }else if($d[1]=='11'){
        $bulan = lang("November");
    }else if($d[1]=='12'){
        $bulan = lang("Desember");
    }else{
        $bulan = "";
    }
    $date_fix = intval($d[2])." ".$bulan." ".$d[0];
    return $date_fix;
}
function setTanggalNew($date){
    $d = explode("-", $date);
    $ret = $d[2].'/'.$d[1].'/'.$d[0];
    return $ret;
}
?>
<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <title>Preview</title>

        <style type="text/css">

            @media all {
                .page-break	{ display: none; }
            }

            @media print {
                @page { margin: 0.1cm; padding:0cm; }
                .page-break  { display: block; page-break-before: always; }
                .page-break-after { display: block; page-break-after: always; }
                .page {
                    margin: 0;
                    border: initial;
                    border-radius: initial;
                    width: initial;
                    min-height: initial;
                    box-shadow: initial;
                    background: initial;
                    page-break-after: always;
                }
                .page_landscape {
                    margin: 0;
                    border: initial;
                    border-radius: initial;
                    width: initial;
                    min-height: initial;
                    box-shadow: initial;
                    background: initial;
                    page-break-after: always;
                }
                
                thead {display: table-header-group;}

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
            .page {
                width: 21cm;
                height:27.7cm;
                padding: 2cm;
                margin: 1cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .page_mini {
                width: 8.5cm;
                height:auto;
                padding: 0cm;
                margin: 0.5cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .page_landscape {
                width: 28cm;
                height:21cm;
                padding: 2cm;
                margin: 1cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .table-print {
                font-size: 10pt;
                font-family: verdana;
                font-weight: normal;
                padding: 0px;
                margin: 0px;
                border-top: 1.5px solid #333333;
                border-left: 1.5px solid #333333;
                border-collapse: collapse;
            }

            .table-print th {
                text-align: center;
                border-right: 1.5px solid #333333;
                border-bottom: 1.5px solid #333333;
                padding: 5px;
                margin:0px;


            }

            .table-print td {
                text-align: left;
                border-right: 1.5px solid #333333;
                border-bottom: 1.5px solid #333333;
                padding: 1px;
                margin:0px;
                font-weight: normal;
            }

        </style>
    </head>
    <body>
        <div class="page_mini">
            <div id="templatemo_container_wrapper">
                <div id="templatemo_container">
                    <div class="text_area">
                        ----------------------------------------------------------
                        <div style="text-align:center">
                            <?php echo lang('DELIVERY ORDERS');?>
                        </div>
                        ----------------------------------------------------------<br>
                        <table width="100%" >
                            <tr>
                                <td valign="top" width="30%">From</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['PengirimName'] ?></td>
                            </tr>
                            <tr>
                                <td valign="top" width="30%">Batch Number</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['SupplyBatchNumber'] ?></td>
                            </tr>
                            <tr>
                                <td valign="top">PO Number</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['DestPO'] ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Trans Date</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo date("d F Y", strtotime($batch['SupplyBatchDate'])) ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Delivery Date</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo date("d F Y", strtotime($batch['DeliveryDate'])) ?></td>
                            </tr>
                            
                            <tr>
                                <td valign="top">Gross Weight</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['VolumeBruto'] ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Nett Weight</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['VolumeNetto'] ?></td>
                            </tr>
                            <tr>
                                <td valign="top" width="40%">Dest. Weight</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['DestWeight']; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Package Count</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['DestNumberPackage'] ?></td>
                            </tr>
                            <tr>
                                <td valign="top" width="40%">Driver Name</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['Driver']; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">License Plate</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['PlatNomor'] ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Vehicle Type</td>
                                <td valign="top">:</td>
                                <td valign="top"><?php echo $batch['DestTransportName'] ?></td>
                            </tr>
                           
                            <tr>
                                <td valign="top" colspan="3">
                                    ---------------------------------------------------------
                                    <div style="text-align:center">
                                        <?php echo lang('THANK YOU');?>
                                    </div>
                                    ---------------------------------------------------------
                                </td>
                               
                            </tr>
                            <?php 
                                /*$driver = explode("|", $batch['DestDriver']);
                                $driverJ = explode("|", $batch['DestDriverJabatan']);
                                $driverA = explode("|", $batch['DestDriverAddress']);
                                $nopol = explode("|", $batch['DestNoPolisi']);
                                $driverT = explode("|", $batch['DestTransport']);
                                $driverHp = explode("|", $batch['DestDriverHp']);
                                $jml_driver = count($driver);*/
                                
                                /*for($a=0;$a<$jml_driver;$a++){
                                    echo '<tr><td valign="top">Nama Sopir</td><td valign="top">:</td><td valign="top">'.$driver[$a].'</td></tr>';
                                    if($driverJ[$a]!=''){
                                        //echo '<tr><td>Jabatan</td><td valign="top">:</td><td valign="top">'.$driverJ[$a].'</td></tr>';
                                    }else{
                                        $nosopir = ($jml_driver==1) ? '' : ($a+1);
                                        //echo '<tr><td valign="top">Jabatan</td><td valign="top">:</td><td valign="top">Sopir '.$nosopir.'</td></tr>';
                                    }
                                    if($driverHp[$a]!=''){
                                        //echo '<tr><td valign="top">No. Handphone</td><td valign="top">:</td><td valign="top">'.$driverHp[$a].'</td></tr>';
                                    }
                                    if($driverA[$a]!=''){
                                        //echo '<tr><td valign="top">Alamat</td><td valign="top">:</td><td valign="top">'.$driverA[$a].'</td></tr>';
                                    }
                                    if($driverT[$a]!=''){
                                        //echo '<tr><td valign="top">Kendaraan</td><td valign="top">:</td><td valign="top">'.$driverT[$a].'</td></tr>';
                                    }
                                    if($nopol[$a]!=''){
                                        echo '<tr><td valign="top">No. Polisi</td><td valign="top">:</td><td valign="top">'.$nopol[$a].'</td></tr>';
                                    }
                                }*/
                            ?>
                        </table>
                       
                    </div>
                </div>
            </div>
        </div>
        
    </body>
</html>

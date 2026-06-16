<?php 
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
        <title>Farmer</title>

        <style type="text/css">

            @media all {
                .page-break	{ display: none; }
            }

            @media print {
                @page { margin: 0cm; padding:0cm; }
                .page-break  { display: block; page-break-before: always; }
                .page-break-after { display: block; page-break-after: always; }
                .page-break  {page-break-after: always;}
                .page {
                    margin: 0;
                    border: none;
                    border-radius: none;
                    width: initial;
                    min-height: initial;
                    box-shadow: none;
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
                
                .page {
                    -webkit-box-shadow: none;
                    -moz-box-shadow:    none;
                    box-shadow:         none; 
                }

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
             
            .page {
                width: 21cm;
                height:26.7cm;
                padding: 2cm;
                margin: 1cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .page_mini {
                width: 8.5cm;
                height:10cm;
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
                padding: 4px;
                margin: 2px;
                border: 3px solid #333333;
				border-spacing: 5px;
				border-collapse: separate; 
            } 
			.table-print h2 { text-align:center; }
            .table-print td { width:50%; padding:5px;}
			
			div [class*="underline"] { 
				display: inline-block;
				border-bottom: 1px dotted black;
				padding-bottom: 1px;
				line-height: 70%;
				width:280px;
				text-align:center;
				font-weight:bold;
			}
			 .ttd{ 
				display: inline-block;
				border-bottom: 1px dotted black;
				padding-bottom: 1px; 
				width:180px;
				text-align:center;
				font-weight:bold;
			}
        </style>
    </head>
    <body>
       
        <div class="page"> 
             <table width="100%" class="table-print" border="1">
				   <tr>
					 <td colspan="2"><br><h2> SURAT JALAN </h2><br></td>
				   </tr>
					<!--page 2-->	
				   <tr>
					 <td>
						   <br>
						   <table border="0"  width="100%" style="display: block;">
							  <tr>
								<td width="5%">Nama</td>
								<td>: <?php echo $data['PengirimName'];?> </td>
							  </tr>
							  <tr>
								<td >Nomor Mobil</td>
								<td>: <?php echo $data['Driver'];?></td>
							  </tr>
							  <tr>
								<td >Nama Supir</td>
								<td>: <?php echo $data['SupplyDestOrgName'];?></td>
							  </tr>
						   </table>
							<br>
						</td>
						<td>   
							<br>
						   <table width="100%">
							  <tr>
								<td>Kepada Yth,</td> 
							  </tr>
							  <tr>
								<td><b> <?php echo strtoupper($data['SupplyDestOrgName']);?></b></td> 
							  </tr>
							  <tr>
								<td><?php echo $data['SupplyDestOrgAddress'];?></td> 
							  </tr>
						   </table>
						   <br>
						</td>
			    </tr>
				
				<!--page 3-->
				<tr>
					<td colspan="2" style="padding:10px;"> <br>				
					   Bersama ini, saya kirimkan Sango - Sango Sebanyak <div class="underline"> <?php echo $data['DestNumberPackage'];?> </div> bal <br> <br> 
					   ( <div class="underline"> <?php echo $data['DestWeight'];?> </div> kg) Dari Kab. <div class="underline"> <?php echo $data['DistrictPengirim'];?></div>
					   <br><br>
					</td>
				</tr>
				
				<!--page 4-->
				<tr>
					<td colspan="2" style="height:250px;"> 				
					    <!--blank kotak-->
					</td>
				</tr>
				
				<!--page 5-->
				<tr>
					<td style="height:120px; padding:15px;"> 				
					      <table width="100%">
							  <tr>
								<td> Pontianak, <?php echo tanggal($data['DateCreated']);?> <div class="ttd"> </div>  </td> 
							  </tr>
							  <tr>
								<td height="100"><center><b>Yang Menyerahkan, </b></center></td> 
							  </tr>
							  <tr>
								<td>(<div class="underline"> </div>)  <br> <center> <?php echo $data['PengirimName'];?></center> </td> 
							  </tr> 
						   </table> 
					</td>
					
					<td style="height:120px;padding:15px;"> 				
					     <table width="100%">
							  <tr>
								<td> Pontianak, <div class="ttd"> </div> 20...</td> 
							  </tr>
							  <tr>
								<td height="100"><center><b>Yang Menerima, </b></center></td> 
							  </tr>
							  <tr>
								<td>(<div class="underline"> </div>) <br> <center> </center> </td> 
							  </tr> 
						   </table>
					</td>
				</tr>
				
			  </table>  
        </div>
    </body>
</html>

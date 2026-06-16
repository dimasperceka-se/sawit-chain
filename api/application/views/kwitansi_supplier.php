<style>
 body {
    margin: 0;
    padding: 0;
    background-color: #FAFAFA;
    font: 10pt "verdana";
}
* {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}
.page {
    width: 7cm;
    height:14cm;
    padding: 0.5cm;
    margin: 1cm auto;
    border: 1px #D3D3D3 solid;
    border-radius: 5px;
    background: white;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}
.subpage {
    padding: 1cm;
    border: 5px red solid;
    height: 256mm;
    outline: 2cm #FFEAEA solid;
}

@media print {
    .page {
        width: 7cm;
        height:10cm;
        padding: 0.25cm;
        margin: 1cm auto;
        box-shadow: none;
        background: none;
        border:none;
    }
    .page-break	{ display: block; page-break-before: always; }
}
    h4 {
        text-align: center;
        font-size: 10pt;
        font-family: verdana;
    }
    
    .title {
        font-size: 10pt;
        font-family: verdana;
        font-weight: normal;
    }
    
    .table-print {
        font-size: 7pt;
        font-family: verdana;
        font-weight: normal;
        padding: 0px;
        margin: 0px;
        border-top: 1.5px solid #333333;
        border-left: 1.5px solid #333333;
        border-collapse: collapse;
    }
    
    .table-print th {
        text-align: left;
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
    td{
      font-size:11px;
    }
</style>
<body>
<div class="page">
    <div>-------------------------------------</div>
    <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FFB Procured Notes            </div>
    <div>-------------------------------------</div>
    <br />
    <table> 
        <tr>
            <td>Mill</td>
            <td>: <?php echo $data['Name']; ?></td>
        </tr>
        <tr>
            <td style="width:100px;">Tanggal</td>
            <td>: <?php echo $data['DateTransaction']; ?> </td>
        </tr>
        <tr>
            <td style="width:90px;">No. Faktur</td>
            <td>: <?php echo $data['TransNumber']; ?></td>
        </tr>
        <tr>
            <td>Dari </td>
            <td>: <?php echo $data['fromName']; ?></td>
        </tr> 
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>Bruto</td>
            <td>: <?php echo floatval($data['VolumeBruto']); ?> Kg</td>
        </tr> 
        <tr>
            <td>Netto</td>
            <td>: <?php echo floatval($data['VolumeNetto']); ?> Kg</td>
        </tr>
        <tr>
            <?php
           
            $netto  = $data['VolumeNetto'];

            $outputNetto = number_format($netto);

            $resultNetto = preg_replace("/[^0-9]/", "", $outputNetto);

            $division = 20;

            $resultBuncest = $resultNetto / $division;

            ?>

            <td>Total <span>Janjang</span></td>
            <td>: <?php echo $resultBuncest ; ?></td>
        </tr> 
        <!-- <tr>
            <td>Harga Net</td>
            <td>: <?php echo number_format($data['NetPrice'],0); ?></td>
        </tr> 
		<?php if($quality){?>
		<?php foreach($quality as $r){ ?>
        <tr>
            <td><?php echo $r->Name; ?></td>
            <td>: <?php echo $r->Value; ?></td>
        </tr>
		<?php }}?>		
        <tr>
            <td>TOTAL</td>
            <td>: <?php echo number_format($data['TotalPayment'],0); ?></td>
        </tr> -->
   </table>
    <div>-----------------------------------</div>
        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TERIMA KASIH            </div>
        <div>-----------------------------------</div>
    </div>
</body>

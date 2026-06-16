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
        padding: 0.5cm;
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
    <div>-----------------------------------</div>
    <div class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I n v o i c e</div>
    <div>-----------------------------------</div>
    <br />
    <table> 
        <tr>
            <td>Trans Type</td>
            <td>: <?php echo $data['SupplyType']; ?></td>
        </tr>
        <tr>
            <td>Buyer</td>
            <td>: <?php echo $data['Name']; ?></td>
        </tr>
        <tr>
            <td style="width:100px;">Date</td>
            <td>: <?php echo $data['DateTransaction']; ?> </td>
        </tr>
        <tr>
            <td style="width:100px;">Invoice No</td>
            <td>: <?php echo $data['InvoiceNumber']; ?></td>
        </tr>
        <tr>
            <td style="width:100px;">Farmer ID</td>
            <td>: <?php echo $data['MemberDisplayID']; ?></td>
        </tr>
        <tr>
            <td>Name</td>
            <td>: <?php echo $data['Namapetani']; ?></td>
        </tr>
        <tr>
            <td>Bunches</td>
            <td>: <?php echo $data['Bunches']; ?></td>
        </tr>
        <tr>
            <td>Bruto</td>
            <td>: <?php echo floatval($data['VolumeBruto']); ?> Kg</td>
        </tr> 
        <tr>
            <td>Deduction</td>
            <td>: <?php echo floatval($data['DeductionWeight']); ?> Kg</td>
        </tr> 
        <tr>
            <td>Netto</td>
            <td>: <?php echo floatval($data['VolumeNetto']); ?> Kg</td>
        </tr>
        <tr>
            <td>Basic Price</td>
            <td>: <?php echo number_format($data['ContractPrice'],0); ?></td>
        </tr> 	
        <tr>
            <td>Total</td>
            <td>: <?php echo number_format($data['TotalPayment'],0); ?></td>
        </tr>
        <tr>
            <td>Payment Reduction</td>
            <td>: <?php echo $data['PaymentReduction']; ?></td>
        </tr>
        <tr>
            <td>Payment Amount</td>
            <td>: <?php echo $data['PaymentPaid']; ?></td>
        </tr>
   </table>
    <div>-----------------------------------</div>
        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PalmoilTrace</div>
        <div>-----------------------------------</div>
    </div>
</body>

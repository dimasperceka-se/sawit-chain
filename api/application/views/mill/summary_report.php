<?php
/**
 * @Author: nikolius
 * @Date:   2016-06-03 17:11:28
 */
$baseurlnya = base_url();
// $baseurlnya = str_replace('http://','https://',$baseurlnya);

?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8"/>
        <title><?php echo $titleNya;?></title>

        <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/print_beneficiary/print_beneficiary.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/print_beneficiary/print_beneficiary-media.css" media="print"/>

        <script src="<?php echo $baseurlnya;?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div id='templatemo_container_wrapper'>
            <div class="page" >
                <div class="row" style="margin-bottom:10px">
                    <table width="50%" border="0" cellpadding="2">
                        <tr>
                            <td height="100px" width="25%" style="vertical-align:middle;">
                                <?php
                                    if($PhotoSrc != ""){
                                        $logo = $PhotoSrc;
                                    }else{
                                        $logo = base_url().'/images/no-logo.png';
                                    }
                                ?>
                                <img src="<?=$logo?>" style="max-width:90%; max-height:90%; max-width:140px;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><b><?=lang("FFB Procurement Report")?></b></td>
                        </tr>
                        <tr>
                            <td><?=lang("Reporting Period")?></td>
                            <td>: <?=$ReportPeriodStart?> / <?=$ReportPeriodEnd?></td>
                        </tr>
                    </table>
                </div>
                <div class="row" style="margin-bottom:10px">
                    <table width="50%" border="0" cellpadding="2">
                        <tr>
                            <td colspan="2"><b><?=lang("Mill Profile")?></b></td>
                        </tr>
                        <tr>
                            <td><?=lang("Company")?></td>
                            <td>: <?=$CompanyName?></td>
                        </tr>
                        <tr>
                            <td><?=lang("Mill Name")?></td>
                            <td>: <?=$MillName?></td>
                        </tr>
                        <tr>
                            <td><?=lang("Mill Capacity")?></td>
                            <td>: <?=$ProductionCapacity?> Ton/Jam</td>
                        </tr>
                        <tr>
                            <td><?=lang("Mill Location")?></td>
                            <td>: <?=$Address?></td>
                        </tr>
                        <tr>
                            <td><?=lang("Certification")?></td>
                            <td>: </td>
                        </tr>
                    </table>
                </div>
                <div class="row" style="margin-bottom:20px">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="2"><?=lang("FFB Procurement by Source")?></th>
                                    <th><?=lang("RSPO / ISPO Certified")?></th>
                                    <th><?=lang("Percentage of Tracebility")?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $tracable = 0;
                                    $tracable_detail = "";
                                    $pembagi = 0;
                                    if($dataTracable){
                                        $tmpsource  = NULL;
                                        $count      = 0;
                                        $SourceType = array('SourceType'=> '4', 'jumlah'=> 0);
                                        $rowspan = 0;
                                        $sumArray = array();
                                        foreach($dataTracable as $value){
                                            echo '<tr>';
                                            $type = count(array_keys(array_column($dataTracable, "SourceType"), $value["SourceType"]));
                                            $SourceType['SourceType'] = $value["SourceType"];
                                            $SourceType['jumlah'] += 1;
                                            if($SourceType['jumlah'] == 1){
                                                $rowspan = $type;
                                                $SourceType['jumlah'] = 0;
                                            }
                                            if($tmpsource != $value["SourceType"]){
                                                foreach ($value as $id => $subvalues) {
                                                    if($id == "TotalTrace"){
                                                        $sumArray[$value["SourcTypeName"]] += $subvalues;
                                                    }
                                                }
                                                echo '<td rowspan="'.$rowspan.'">'.$value["SourcTypeName"].'</td>';
                                                echo '<td>'.$value["SourceName"].'</td>';
                                                echo '<td></td>';
                                                echo '<td>'.$value["TCPercentage"].' %</td>';
                                                $tmpsource = $value["SourceType"];
                                                $DataTypeSupplir[$value["SourceType"]] = array($value["SourcTypeName"],$type);
                                                $tracable += $value["TotalTrace"];	
                                                $pembagi++;
                                            }else{
                                                echo '<td>'.$value["SourceName"].'</td>';
                                                echo '<td></td>';
                                                echo '<td>'.$value["TCPercentage"].' %</td>';
                                            }
                                            echo '</tr>';
                                        }

                                        $dataType = "";
                                        if(count($DataTypeSupplir)>0){
                                            foreach($DataTypeSupplir as $row => $value){
                                                list($SourcTypeName,$jumlah)=$value;
                                                $dataType .= "<tr><td>$SourcTypeName</td><td>$jumlah</td></tr>";
                                            }
                                        }

                                        if(count($sumArray)){
                                            foreach($sumArray as $key => $value){
                                                $tracable_detail .= "['$key', $value],";
                                            }
                                        }
                                    }

                                    $tracable_detail = rtrim($tracable_detail,',');
                                    $tracable = ($tracable/$pembagi);

                                    $dataChartTracable = '["Traceable",'.number_format(($tracable),2).'],["Untraceable",'.number_format((100-$tracable),2).']';
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" style="margin-bottom:20px">
                    <div class="col-md-7">
                    </div>
                    <div class="col-md-5">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?=lang("Type of Supplier")?></th>
                                    <th><?=lang("Number of Supplier")?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?=$dataType?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>            
            <div class="page">                
                <div class="row">                    
                    <table width="100%" border="0" cellpadding="2" style="border:2px solid #95130b;">
                        <tr>
                            <td height="60px" width="25%" align="center" style="vertical-align:middle;">
                                <div id="container" style="height: 400px; width: 500"></div>
                                <h3 style="position:absolute;top:220;left:370"><?=number_format($tracable,2)?>%<br>Traceable</h3>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>            
            <div class="page">                
                <div class="row">
                    <table class="table">
                        <thead>
                            <tr><th colspan="6"><?=lang("Company Owned Estate (Estate Inti)")?></th></tr>
                            <tr>
                                <th><?=lang("Nama Supplier")?></th>
                                <th><?=lang("Kategori Kebun")?></th>
                                <th><?=lang("Annual Production")?></th>
                                <th><?=lang("Garden Area (Ha)")?></th>
                                <th><?=lang("FFB Supply (Ton)")?></th>
                                <th><?=lang("Tracebility")?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($EstateInti["data"])){
                                    foreach($EstateInti["data"] as $row){
                                        echo "
                                            <tr>
                                                <td>$row[SupplierName]</td>
                                                <td>$row[GardenType]</td>
                                                <td>$row[AnnualProduction]</td>
                                                <td>$row[GardenAreaHa]</td>
                                                <td>$row[FFBSupply]</td>
                                                <td>$row[Tracebility]</td>
                                            </tr>
                                        ";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="row" style="margin-top:20px">
                    <table class="table">
                        <thead>
                            <tr><th colspan="6"><?=lang("Plasma Smallholder")?></th></tr>
                            <tr>
                                <th><?=lang("Nama Supplier")?></th>
                                <th><?=lang("Kategori Kebun")?></th>
                                <th><?=lang("Annual Production")?></th>
                                <th><?=lang("Garden Area (Ha)")?></th>
                                <th><?=lang("FFB Supply (Ton)")?></th>
                                <th><?=lang("Tracebility")?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($Plasma["data"])){
                                    foreach($Plasma["data"] as $row){
                                        echo "
                                            <tr>
                                                <td>$row[SupplierName]</td>
                                                <td>$row[GardenType]</td>
                                                <td>$row[AnnualProduction]</td>
                                                <td>$row[GardenAreaHa]</td>
                                                <td>$row[FFBSupply]</td>
                                                <td>$row[Tracebility]</td>
                                            </tr>
                                        ";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="row" style="margin-top:20px">
                    <table class="table">
                        <thead>
                            <tr><th colspan="6"><?=lang("Other Suppliers (Direct Smallholder, Dealer/Agent/Vendor)")?></th></tr>
                            <tr>
                                <th><?=lang("Nama Supplier")?></th>
                                <th><?=lang("Kategori Kebun")?></th>
                                <th><?=lang("Annual Production")?></th>
                                <th><?=lang("Garden Area (Ha)")?></th>
                                <th><?=lang("FFB Supply (Ton)")?></th>
                                <th><?=lang("Tracebility")?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($Other["data"])){
                                    foreach($Other["data"] as $row){
                                        echo "
                                            <tr>
                                                <td>$row[SupplierName]</td>
                                                <td>$row[GardenType]</td>
                                                <td>$row[AnnualProduction]</td>
                                                <td>$row[GardenAreaHa]</td>
                                                <td>$row[FFBSupply]</td>
                                                <td>$row[Tracebility]</td>
                                            </tr>
                                        ";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="row" style="margin-top:20px">
                    <table class="table">
                        <thead>
                            <tr><th colspan="6"><?=lang("External Estates")?></th></tr>
                            <tr>
                                <th><?=lang("Nama Supplier")?></th>
                                <th><?=lang("Kategori Kebun")?></th>
                                <th><?=lang("Annual Production")?></th>
                                <th><?=lang("Garden Area (Ha)")?></th>
                                <th><?=lang("FFB Supply (Ton)")?></th>
                                <th><?=lang("Tracebility")?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($External["data"])){
                                    foreach($External["data"] as $row){
                                        echo "
                                            <tr>
                                                <td>$row[SupplierName]</td>
                                                <td>$row[GardenType]</td>
                                                <td>$row[AnnualProduction]</td>
                                                <td>$row[GardenAreaHa]</td>
                                                <td>$row[FFBSupply]</td>
                                                <td>$row[Tracebility]</td>
                                            </tr>
                                        ";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
$(function() {
    $('#container').highcharts({
        chart: {
            type: 'pie',
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
        },title: {
            text: 'FFB Procurement Report'
        },
        tooltip: {
            valueSuffix: '%'
        },
        legend: {
            enabled: true,
            align: 'bottom',
            verticalAlign: 'bottom',
            layout: 'vertical',
            useHTML: true,
            width: 250,
            labelFormatter: function() {
                var padLeft     = this.series._i === 0 ? 0 : 10;
                var fontBold    = this.series._i === 0 ? 800 : 200;
                return '<div style="float:left; width: ' + (250 - padLeft) +'px; margin-left: '+ padLeft +'px; font-weight:'+fontBold+'">' + this.name + ' ('+this.y+'%) </div><div style="font-weight: normal;">' + this.y + '</div>';
            }
        },
        subtitle: {
            text: '<?=$ReportPeriodStart?> / <?=$ReportPeriodEnd?>'
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Traceable/Untraceable',
            innerSize: 150,
            size: 200,
            showInLegend: true,
            legendIndex: 100,
            dataLabels: {
                formatter: function () {
                    return this.y > 1000 ? this.point.name : null;
                },
                color: '#ffffff',
                distance: -30
            },
            marker: {
                radius: 15
            },
            data: [
                <?=$dataChartTracable?>
            ]
        }, {
            name: 'Sources',
            innerSize: 100,
            size: 150,
            showInLegend: true,
            legendIndex: 300,
            dataLabels: {
                formatter: function () {
                    return this.y > 1000 ? this.point.name : null;
                },
                color: '#ffffff',
                distance: -30
            },
            marker: {
                radius: 5
            },
            colors: ['#f92525', '#8c0aaf','#2fd66f','#e1ff00'],
            data: [
                <?=$tracable_detail?>
            ]
        }],responsive: {
            rules: [{
                condition: {
                    maxWidth: 400
                },
                chartOptions: {
                    series: [{
                        id: 'versions',
                        dataLabels: {
                            enabled: false
                        }
                    }]
                }
            }]
        }
    });
});

</script>
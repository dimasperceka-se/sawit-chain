<?php
/**
 * @Author: nikolius
 * @Date:   2016-06-03 17:11:28
 */
$baseurlnya = base_url()."api/";
// $baseurlnya = str_replace('http://','https://',$baseurlnya);
if ($js!='') {?>
    <script>
        $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
        <?$key = array_keys($action);
        for ($i=0;$i<sizeof($action);$i++) {?>
            var m_<?php echo $key[$i]?> = <?php echo ($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
            <?}?>
        </script>
<?php
}
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
   <meta charset="utf-8"/>
   <title><?php echo $titleNya;?></title>

   <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/print_beneficiary/print_beneficiary.css"/>
   <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/print_beneficiary/print_beneficiary-media.css" media="print"/>

   <script src="<?php echo $baseurlnya;?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
   
   <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACXVwWCJen2OZeCAEYdRxP_HEh7CkxOvs"></script>-->
   <!--<script src="<?php echo $baseurlnya;?>assets/js/gmap3.js"></script>-->
</head>
<body>

<style type="text/css">
.gm-style-cc:last-child {
    display: none !important;
    height: 0px !important;
}
a[title="Report errors in the road map or imagery to Google"] {
    display: none !important;
    height: 0px !important;
}
a[href="https://www.google.com/intl/en-US_US/help/terms_maps.html"] {
    display: none !important;
    height: 0px !important;
}
</style>
<div id='row-fluid'>
    <div class="main-content" style="padding-top: 50px;padding-left:20px;padding-right:30px">
        <!-- Bagian ini untuk counter -->
        <div class="row">
            <!-- Start 4 counter pertama -->
            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head widget-tile">
                        <!--<div class="data-info col-md-8">-->
                        <div class="col-md-8">
                            <div class="value" id="registered_farmer"></div>
                            <div class="desc">
                                <?php echo lang('Registered Farmer') ?>
                            </div>
                        </div>
                        <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/petani2.png"></div>
                        <!--</div>-->
                    </div>
                    <ul class="widget-list colapsed" id="registered_farmer_detail">
                        <li>
                            <a class="link_mill" data-type="mapped_farmer" href="#">
                                <span class="label"><?=lang("Mapped Farmer")?></span>
                                <span class="value" id="mapped_farmer_val"></span>
                            </a>
                        </li>
                        <li>
                            <a class="link_mill" data-type="unmapped_farmer" href="#">
                                <span class="label"><?=lang("Unmapped Farmer")?></span>
                                <span class="value" id="unmapped_farmer_val"></span>
                            </a>
                        </li>
                        <li>
                            <a class="link_mill" data-type="total_mapped_garden" href="#">
                                <span class="label"><?=lang("Total Mapped Garden")?></span>
                                <span class="value" id="total_mapped_garden_val"></span>
                            </a>
                        </li>
                        <li>
                            <a class="link_mill" data-type="total_unmapped_garden" href="#">
                                <span class="label"><?=lang("Total Unmapped Garden")?></span>
                                <span class="value" id="total_unmapped_garden_val"></span>
                            </a>
                        </li>
                        <li>
                            <a class="link_mill" data-type="total_mapped_garden_area" href="#">
                                <span class="label"><?=lang("Mapped Garden Area")?> (Ha)</span>
                                <span class="value" id="total_mapped_garden_area_val"></span>
                            </a>
                        </li>
                        <li>
                            <a class="link_mill" data-type="total_unmapped_garden_area" href="#">
                                <span class="label"><?=lang("Unmapped Garden Area")?> (Ha)</span>
                                <span class="value" id="total_unmapped_garden_area_val"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head widget-tile">
                        <!--<div class="data-info col-md-8">-->
                        <div class="col-md-8">
                            <div class="value" id="supplier_detail"></div>
                            <div class="desc">
                                <?php echo lang('Supplier') ?>
                            </div>
                        </div>
                        <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/petani2.png"></div>
                        <!--</div>-->
                    </div>
                    <ul class="widget-list colapsed" id="supplier_detail_dropdown">
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="page-head xs-pt-10 xs-pb-10">
            <div class="row">
                <div class="col-md-2"><h2><?php echo lang('Supplier Transaction') ?></h2></div>
                <div class="col-md-10">
                    <?php //echo $this->load->view('list_region_traceability', $action, TRUE); ?>
                    <div class="pull-right xs-mr-50">&nbsp;</div>
                    <div class="btn-group btn-hspace pull-right">
                        <!-- <input type="text" id="datepicker1" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $tgl['awal'] ?>">
                        &nbsp;&nbsp;<?php echo lang('sampai') ?>&nbsp;&nbsp;
                        <input type="text" id="datepicker2" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $tgl['akhir'] ?>">&nbsp;&nbsp;
                        <button class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setRange()"><?php echo lang('Cari') ?></button>
                        &nbsp;&nbsp; -->
                        
                        <div class="btn-group btn-hspace pull-right">
                            <button class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setRange()"><?php echo lang('Cari') ?></button>
                        </div>
                        <div class="btn-group btn-hspace pull-right">
                            <select class="form-control" id="start_date">
                            </select>
                        </div>         
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content" style="padding-left:20px;padding-right:30px">
            <div class="row">
                <table width="80%" class="table" style="margin-top:10px;float:left">
                    <thead>
                        <tr>
                            <th><?=lang("Supplier Category")?></th>
                            <th><?=lang("Total Supplier Transaction")?></th>
                            <th><?=lang("Total Garden Transaction")?></th>
                            <th><?=lang("Total Garden Area Transaction")?></th>
                            <th><?=lang("Tonation")?></th>
                        </tr>
                    </thead>
                    <tbody id="supplier_transaction_data">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="main-content" style="padding-left:20px;padding-right:30px">                      
            <div class="row">
                <div class="col-md-12 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="volume_chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content" style="padding-left:20px;padding-right:30px">
            <div class="row">
                <table width="80%" class="table" style="margin-top:10px;float:left">
                    <thead>
                        <tr>
                            <th><?=lang("Supplier ID")?></th>
                            <th><?=lang("SPB Code")?></th>
                            <th><?=lang("SupplierName")?></th>
                            <th><?=lang("Supplier Alias")?></th>
                            <th><?=lang("Supplier Type")?></th>
                            <th><?=lang("Village")?></th>
                            <th><?=lang("Subdistrict")?></th>
                            <th><?=lang("District")?></th>
                            <th><?=lang("Lat")?></th>
                            <th><?=lang("Long")?></th>
                            <th><?=lang("Garden Area (Ha)")?></th>
                            <th><?=lang("Total Farmer")?></th>
                            <th><?=lang("Total Garden")?></th>
                        </tr>
                    </thead>
                    <tbody id="supplier_detail_list">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>
<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 20 2020
 *  File : kpi_koltiva.php
 *******************************************/
//cek apakah dashboard filter region aktif
$uri4 = $this->uri->segment(4);
?>
<script>
    <?php if($uri4 ==""){?>
        $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?php }?>
    <?$key = array_keys($action);
    for ($i=0;$i<sizeof($action);$i++) {?>
        var m_<?php echo $key[$i]?> = <?php echo ($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
        <?}?>
</script>
<div id="ext-content"></div>

<!-- original script -->

<!-- <br> -->
<!--<div class="page-head xs-pt-10 xs-pb-10" style="display: none;">--> <!-- SEMENTARA DIHIDE DULU YA -->
    <!--<div class="row">-->
        <!--<div class="col-md-2"><h2><?php //echo lang('Filter'); ?></h2></div>-->
        <!--<div class="col-md-10">-->
            <!--<div class="btn-group btn-hspace pull-right">-->
                <!--<button style="height:27px;padding: 0px 4px !important;" class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="runSearch()"><?php //echo lang('Cari') ?></button>-->
            <!--</div>-->
            
            <!--<div class="btn-group btn-hspace pull-right">-->
                <!--<select class="form-control DashCombo" name="fprovince" id="fprovince">-->
                    <!--<option value="all_province"><?php //echo lang('All Province'); ?></option>-->
                <!--</select>-->
            <!--</div>-->
        <!--</div>-->
    <!--</div>-->
<!--</div>-->

<!-- modified 14-4-2021 -->
<div class="row-fluid">
    <div class="page-head xs-pt-10 xs-pb-10">
        <div class="row">
            <div class="col-md-2"><h2><?php echo lang('Filter'); ?></h2></div>
            <div class="col-md-10">

                <div class="btn-group btn-hspace pull-right">
                    <button style="margin-top:3px;height:24px;padding: 1px 6px !important;" class="btn btn-primary pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="ajaxDataRenderer('<?php echo $action['data'];?>')">
                        <i class="icon icon-left s7-filter"></i><?php echo lang('Search') ?>
                    </button>
                </div>
                <!-- <div class="btn-group btn-hspace pull-right">
                    <select class="form-control" name="fyear" id="fyear" style="border-width: 2px;box-shadow: none;padding: 2px 4px !important;height: 28px;font-size: 12px;"></select>
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control" name="fmonth" id="fmonth" style="border-width: 2px;box-shadow: none;padding: 2px 4px !important;height: 28px;font-size: 12px;"></select>
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control" name="ClusterID" id="ClusterID" style="border-width: 2px;box-shadow: none;padding: 2px 4px !important;height: 28px;font-size: 12px;"></select>
                </div> -->
                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control DashCombo" name="filter_cluster_sawit" id="filter_cluster_sawit">
                        <option value="all_cluster"><?php echo lang('All Project Area'); ?></option>
                    </select>
                </div>
                
                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control DashCombo" name="filter_lock_date_sawit" id="filter_lock_date_sawit"></select>
                </div>

                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control DashCombo" name="filter_wave_sawit" id="filter_wave_sawit" onchange="selectWaveSawit(this)">                        
                        <option value="all_wave"><?php echo lang('All Program'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!--<span id="dash-last-updated"><?php echo lang('Last updated: ') ?></span>-->
                <div class="col-md-6">
                    <table width="400" border="0">
                        <tr>
                            <td width="200"><h5><?php echo lang('KPI KSATRIA SAWIT Summary:'); ?></h5></td>
                            <td>
                                <!-- <button style="float:left" onclick="ClickExportExcelSummary()" class="x-btn Sfr_BtnGridPaleBlue x-unselectable x-btn-toolbar x-toolbar-item x-btn-default-toolbar-small x-icon-text-left x-btn-icon-text-left x-btn-default-toolbar-small-icon-text-left">
                                    <span id="button-1037-btnInnerEl" class="x-btn-inner x-btn-inner-center" unselectable="on">Export</span>
                                    <span role="presentation" id="button-1037-btnIconEl" class="x-btn-icon-el  " unselectable="on" style="background-image:url(<?= base_url() ?>/images/icons/new/export.png);margin-left: 8px;">&nbsp;</span>                                
                                </button>
                                <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingSceSummary" style="display:none;margin-left:40px;margin-top: 5px" /> -->
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6"></div>
            </div>
        </div>
    </div>
</div>
<div id='row-fluid' style="display: none;"> <!-- display:none disini -->
    <div class="main-content" >

        <!-- Dashlet -->
        <div class="row">

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head">
                        <div class="col-md-8">
                            <div class="value" id="d_palm_oil_mill"></div>
                            <div class="desc">
                                <?php echo lang('Palm Oil Mills Participant')?>
                            </div>
                            <br>
                            <a title="<?php echo lang('Export Detail') ?>" onclick="ClickExportExcelDetail('palm_oil_mill');return false;" href="#"><img src="<?php echo base_url() ?>img/menu_top/excel-big.png" width="32" /></a>
                            <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingScepalm_oil_mill" style="display:none;margin-left:40px;margin-top: 5px;" />
                        </div>
                        <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/SME Registered.png"></div>
                    </div>
                    <ul class="widget-list colapsed" id="mill_register_detail"></ul>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head">
                    <div class="col-md-8">
                        <div class="value" id="d_sme_mapped"></div>
                        <div class="desc">
                            <?php echo lang('Palm Oil SME Mapped')?>
                        </div>
                        <br>
                        <a title="<?php echo lang('Export Detail') ?>" onclick="ClickExportExcelDetail('palm_sme');return false;" href="#"><img src="<?php echo base_url() ?>img/menu_top/excel-big.png" width="32" /></a>
                        <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingScepalm_sme" style="display:none;margin-left:40px;margin-top: 5px;" />
                    </div>
                    <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmers Active in Responsible Sourcing.png"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head">
                        <div class="col-md-8">
                            <div class="value" id="d_farmers_registered"></div>
                            <div class="desc">
                                <?php echo lang('Palm Oil Farmers') ?>
                            </div>
                            <br>
                            <a title="<?php echo lang('Export Detail') ?>" onclick="ClickExportExcelDetail('palm_farmer');return false;" href="#"><img src="<?php echo base_url() ?>img/menu_top/excel-big.png" width="32" /></a>
                            <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingScepalm_farmer" style="display:none;margin-left:40px;margin-top: 5px;" />
                        </div>
                        <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmer Registered.png"></div>
                    </div>
                    <ul class="widget-list colapsed" id="farmer_register_detail"></ul>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head">
                        <div class="col-md-8">    
                            <div class="value" id="d_farm_registered"></div>
                            <div class="desc">
                                <?php echo lang('Palm Oil Plantations')?>
                            </div>
                            <br>
                            <a title="<?php echo lang('Export Detail') ?>" onclick="ClickExportExcelDetail('palm_plantation');return false;" href="#"><img src="<?php echo base_url() ?>img/menu_top/excel-big.png" width="32" /></a>
                            <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingScepalm_plantation" style="display:none;margin-left:40px;margin-top: 5px;" />
                        </div>
                        <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmer Plantation Registered.png"></div>
                    </div>
                    <ul class="widget-list colapsed" id="farm_register_detail"></ul>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head">
                        <div class="col-md-8">
                            <div class="value" id="d_farm_ha"></div>
                            <div class="desc">
                                <?php echo lang('Palm Oil Plantations Area (Ha)')?>
                            </div>
                            <br>
                            <a title="<?php echo lang('Export Detail') ?>" onclick="ClickExportExcelDetail('palm_plantation_area');return false;" href="#"><img src="<?php echo base_url() ?>img/menu_top/excel-big.png" width="32" /></a>
                            <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingScepalm_plantation_area" style="display:none;margin-left:40px;margin-top: 5px;" />
                        </div>
                        <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmer Plantation Ha.png"></div>
                    </div>
                    <ul class="widget-list colapsed" id="ha_register_detail"></ul>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head">
                    <div class="col-md-8">
                        <div class="value" id="d_farmcloud"></div>
                        <div class="desc">
                            <?php echo lang('FarmCloud Users')?>
                        </div>
                        <br>
                        <a title="<?php echo lang('Export Detail') ?>" onclick="ClickExportExcelDetail('farmcloud');return false;" href="#"><img src="<?php echo base_url() ?>img/menu_top/excel-big.png" width="32" /></a>
                        <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingScefarmcloud" style="display:none;margin-left:40px;margin-top: 5px;" />
                    </div>
                    <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/FC users.png"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head">
                    <div class="col-md-8">
                        <div class="value" id="d_farmgate"></div>
                        <div class="desc">
                            <?php echo lang('Active FarmGate Users')?>
                        </div>
                        <br>
                        <a title="<?php echo lang('Export Detail') ?>" onclick="ClickExportExcelDetail('farmgate');return false;" href="#"><img src="<?php echo base_url() ?>img/menu_top/excel-big.png" width="32" /></a>
                        <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingScefarmgate" style="display:none;margin-left:40px;margin-top: 5px;" />
                    </div>
                    <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/FG users.png"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head">
                    <div class="col-md-8">
                        <div class="value" id="d_farmgt"></div>
                        <div class="desc">
                            <?php echo lang('FarmGate MT Traceable')?>
                        </div>
                        <br>
                        <a title="<?php echo lang('Export Detail') ?>" onclick="ClickExportExcelDetail('farmgatetrace');return false;" href="#"><img src="<?php echo base_url() ?>img/menu_top/excel-big.png" width="32" /></a>
                        <img src="<?php echo base_url() ?>assets/css/loading.gif" width="20" id="topImgLoadingScefarmgatetrace" style="display:none;margin-left:40px;margin-top: 5px;" />
                    </div>
                    <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/FarmRetail users.png"></div>
                    </div>
                </div>
            </div>

        </div>
        <br /><br />

        <div class="row">
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_palm_oil_mill"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_sme_mapped"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_farmers_registered"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">    
                        <div id="gauge_farm_registered"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_farm_ha"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_farmcloud"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_farmgate"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_farmretail"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<? if ($style!='') {?>
<style type="text/css">
    <?php echo $style?>
</style>
<?}?>
<script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script> 

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
            <div class="col-md-2">&nbsp;</div>
            <div class="col-md-10">

                <div class="btn-group btn-hspace pull-right">
                    <button style="margin-top:3px;height:24px;padding: 1px 6px !important;" class="btn btn-primary pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="ajaxDataRenderer('<?php echo $action['data'];?>')">
                        <i class="icon icon-left s7-filter"></i><?php echo lang('Search') ?>
                    </button>
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control" name="fyear" id="fyear" style="border-width: 2px;box-shadow: none;padding: 2px 4px !important;height: 28px;font-size: 12px;"></select>
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control" name="PartnerID" id="PartnerID" style="border-width: 2px;box-shadow: none;padding: 2px 4px !important;height: 28px;font-size: 12px;"></select>
                </div>
            </div>
        </div>
    </div>
</div>
<div id='row-fluid' style="display: none;"> <!-- display:none disini -->
    <div class="main-content" >

        <!-- Dashlet -->
        <div class="row">

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_farmers"></div>
                        <div class="desc">
                            <?php echo lang('Farmer Registered')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmer Registered.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_farmers_tc"></div>
                        <div class="desc">
                            <?php echo lang('Farmers Trained or Coached')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmers Trained or Coached.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">    
                        <div class="value" id="d_farm_registered"></div>
                        <div class="desc">
                            <?php echo lang('Farmer Plantation Registered')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmer Plantation Registered.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_farm_ha"></div>
                        <div class="desc">
                            <?php echo lang('Farmer Plantation (Ha)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmer Plantation Ha.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_farmers_responsource"></div>
                        <div class="desc">
                            <?php echo lang('Farmers active in Responsible Sourcing')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Farmers Active in Responsible Sourcing.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_trace_trans"></div>
                        <div class="desc">
                            <?php echo lang('Traceability Transactions')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/TRaceability Transactions.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_platform_users"></div>
                        <div class="desc">
                            <?php echo lang('Platform Users')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/Platform Users.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_sme"></div>
                        <div class="desc">
                            <?php echo lang('Small and Medium Enterprises Registered')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/SME Registered.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_farmx"></div>
                        <div class="desc">
                            <?php echo lang('FarmXtension Users')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/FX users.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_farmgate"></div>
                        <div class="desc">
                            <?php echo lang('FarmGate Users')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/FG users.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_farmretail"></div>
                        <div class="desc">
                            <?php echo lang('FarmRetail Users')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/FarmRetail users.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="d_farmcloud"></div>
                        <div class="desc">
                            <?php echo lang('FarmCloud Users')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koltiva/FC users.png"></div>
                </div>
            </div>

        </div>

        <!-- Chart -->
        <div class="row">

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farmers"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farmers_tc"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farm_registered"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farm_ha"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farmers_responsource"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_trace_trans"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_platform_users"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_sme"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farmx"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farmgate"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farmretail"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="c_farmcloud"></div>
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
<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-08 14:50:29
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-12-28 11:14:41
 */
//cek apakah dashboard filter region aktif
$uri4 = $this->uri->segment(4);
?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    <?php if($uri4 ==""){?>
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?php }?>

    <?$key = array_keys($action);
    for ($i = 0; $i < sizeof($action); $i++) {?>
    var m_<?php echo $key[$i]?> = <?php echo ($action[$key[$i]] === true ? 'true' : ($action[$key[$i]] === false ? 'false' : "'" . $action[$key[$i]] . "'"))?>;
    <?}?>
</script>
<div id="ext-content"></div>
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
                    <select class="form-control" name="fdistrict" id="fdistrict" style="border-width: 2px;box-shadow: none;padding: 2px 4px !important;height: 28px;font-size: 12px;"><option value="all_district"><?php echo lang('All').' '.lang('District'); ?></option></select>
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control" name="fprovince" id="fprovince" style="border-width: 2px;box-shadow: none;padding: 2px 4px !important;height: 28px;font-size: 12px;"><option value="all_province"><?php echo lang('All').' '.lang('Province'); ?></option></select>
                </div>

                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control" name="fcountry" id="fcountry" style="border-width: 2px;box-shadow: none;padding: 2px 4px !important;height: 28px;font-size: 12px;"><option value="all_country"><?php echo lang('All Country'); ?></option></select>
                </div>
            </div>
        </div>
    </div>
</div>
<div id='row-fluid'>
    <!-- original script -->
    <!-- <div class="page-head xs-pt-10 xs-pb-10"> -->
        <!-- <div class="row"> -->
            <!-- <div class="col-md-9"> -->
                <!--<button id="btnRegenDashboard" type="button" class="btn btn-primary">Regenerate Dashboard</button>-->
            <!-- </div> -->
            <!-- <div class="col-md-3"> -->
                <!--<?php //echo $this->load->view('list_region', $action, true); ?> -->
                <!-- <span style="margin:4px 12px 0px 0px;float:right;font-size:19px;"><?php //echo lang('Filter')?> : </span> -->
            <!-- </div> -->
        <!-- </div> -->
    <!-- </div> -->

    <div class="main-content" >
        <br />

        <div class="row">

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_registered">0</div>
                        <div class="desc">
                            <?php echo lang('Palm Oil Farmers Registered')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/petani2.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_consent_signed">0</div>
                        <div class="desc">
                            <?php echo lang('Consent Letters Signed')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/program-kpi-consent-Letters.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_plantation_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Palm Oil Plantations Registered')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Oil-Palm-Plantations.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_plant_ha_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Palm Oil Plantations Area by Farmer Interview (Ha)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/land_area.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_plantation_polygon_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Palm Oil Plantations Mapped with Polygon')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Oil-Palm-Plantations.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_plant_polygon_ha_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Palm Oil Plantations Hectare Mapped with Polygon (Ha)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/land_area.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_mills_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Palm Oil Mills Mapped')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/WAREHOUSE.PNG"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_agents_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Palm Oil SME Mapped')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/trader.png"></div>
                </div>
            </div>

        </div>

        <br /><br />

        <div class="row">

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_farmer_registered"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_consent_signed"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_plantation_mapped"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_plant_ha_mapped"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_plantation_polygon_mapped"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_plant_polygon_ha_mapped"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_mills_mapped"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_agents_mapped"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>

<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
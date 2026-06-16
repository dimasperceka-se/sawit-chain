<?php
/**
 * @Author: gitandi
 * @Date:   2019-06-27 12:50:17
 * @Last Modified by:   gitandi
 * @Last Modified time: 2019-06-27 12:50:17
 */
//cek apakah dashboard filter region aktif
$uri4 = $this->uri->segment(4);
?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
<?php if ($uri4 == "") { ?>
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
<?php } ?>

<?php
$key = array_keys($action);
for ($i = 0; $i < sizeof($action); $i++) {
    ?>
        var m_<?php echo $key[$i] ?> = <?php echo ($action[$key[$i]] === true ? 'true' : ($action[$key[$i]] === false ? 'false' : "'" . $action[$key[$i]] . "'")) ?>;
<?php } ?>
</script>
<div id="ext-content"></div>

<div id='row-fluid'>

    <div class="page-head xs-pt-10 xs-pb-10">
        <div class="row">
            <div class="col-md-2"><h2><?php echo lang('Filter') ?></h2></div>
            <div class="col-md-10">
                <div class="btn-group btn-hspace pull-right">
                    <button style="height:27px;padding: 0px 4px !important;" class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="runSearch()"><?php echo lang('Cari') ?></button>
                </div>

                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control DashCombo" name="fdistrict" id="fdistrict">
                        <option value="all_district"><?php echo lang('All District'); ?></option>
                    </select>
                </div>

                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control DashCombo" name="fprovince" id="fprovince">
                        <option value="all_province"><?php echo lang('All Province'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content" >
        <br/>

        <div class="row">
            <div class="col-md-12" style="padding: 0">
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_farmer_registered">0</div>
                            <div class="desc">
                                <?php echo lang('Palmoil Farmers Registered') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url() ?>img/general/petani2.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head widget-tile">
                            <!--<div class="data-info col-md-8">-->
                            <div class="col-md-8">
                                <div class="value" id="box_plantation_registered"></div>
                                <div class="desc">
                                    <?php echo lang('Palmoil Plantations Registered') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/Oil-Palm-Plantations.png"></div>
                            <!--</div>-->
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_farmer" data-type="farmer" href="#"><span class="label"><?php echo lang('Farmer') ?></span><span class="value" id="farmer_register"></span> </a></li>
                            <li><a class="link_farmer" data-type="sme" href="#"><span class="label"><?php echo lang('SME') ?></span><span class="value" id="sme_register"></span> </a></li>
                            <li><a class="link_farmer" data-type="mill" href="#"><span class="label"><?php echo lang('Mill') ?></span><span class="value" id="mill_register"></span> </a></li>
                            <!--<li><a class="link_farmer" data-type="" href="#" id="btn_detail_farmer"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <div class="col-md-8">
                                <div class="value" id="box_plant_ha_registered"></div>
                                <div class="desc">
                                    <?php echo lang('Palmoil Plantations Area Registered (Ha)') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/land_area.png"></div>
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_garden_ha" data-type="farmer" href="#"><span class="label"><?php echo lang('Farmer') ?></span><span class="value" id="farmer_plant_ha"></span> </a></li>
                            <li><a class="link_garden_ha" data-type="sme" href="#"><span class="label"><?php echo lang('SME') ?></span><span class="value" id="sme_plant_ha"></span> </a></li>
                            <li><a class="link_garden_ha" data-type="mill" href="#"><span class="label"><?php echo lang('Mill') ?></span><span class="value" id="mill_plant_ha"></span> </a></li>
                            <!--<li><a class="link_garden" data-type="" href="#" id="btn_detail_garden"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_farmer_mapped">0</div>
                            <div class="desc">
                                <?php echo lang('Palmoil Farmers Mapped') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url() ?>img/general/Average-Number-of-Plantations-Owned-by-Farmers.png"></div>
                    </div>
                </div>
            </div>


            <div class="col-md-12" style="padding: 0">

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <!--<div class="data-info col-md-8">-->
                            <div class="col-md-8">
                                <div class="value" id="box_plantation_mapped"></div>
                                <div class="desc">
                                    <?php echo lang('Palmoil Plantations Mapped') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/Oil-Palm-Plantations.png"></div>
                            <!--</div>-->
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_garden_mapped" data-type="farmer" href="#"><span class="label"><?php echo lang('Farmer') ?></span><span class="value" id="farmer_garden_mapped"></span> </a></li>
                            <li><a class="link_garden_mapped" data-type="sme" href="#"><span class="label"><?php echo lang('SME') ?></span><span class="value" id="sme_garden_mapped"></span> </a></li>
                            <li><a class="link_garden_mapped" data-type="mill" href="#"><span class="label"><?php echo lang('Mill') ?></span><span class="value" id="mill_garden_mapped"></span> </a></li>
                            <!--<li><a class="link_garden_mapped" data-type="" href="#" id="btn_detail_garden_mapped"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <div class="col-md-8">
                                <div class="value" id="box_plant_ha_mapped"></div>
                                <div class="desc">
                                    <?php echo lang('Palmoil Plantations Area Mapped (Ha)') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/land_area.png"></div>
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_garden_ha_mapped" data-type="farmer" href="#"><span class="label"><?php echo lang('Farmer') ?></span><span class="value" id="farmer_plant_ha_mapped"></span> </a></li>
                            <li><a class="link_garden_ha_mapped" data-type="sme" href="#"><span class="label"><?php echo lang('SME') ?></span><span class="value" id="sme_plant_ha_mapped"></span> </a></li>
                            <li><a class="link_garden_ha_mapped" data-type="mill" href="#"><span class="label"><?php echo lang('Mill') ?></span><span class="value" id="mill_plant_ha_mapped"></span> </a></li>
                            <!--<li><a class="link_garden" data-type="" href="#" id="btn_detail_garden"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <div class="col-md-8">
                                <div class="value" id="box_plantation_polygon_mapped"></div>
                                <div class="desc">
                                    <?php echo lang('Palmoil Plantations Mapped with Polygon') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/Oil-Palm-Plantations.png"></div>
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_poly" data-type="farmer" href="#"><span class="label"><?php echo lang('Farmer') ?></span><span class="value" id="farmer_poly"></span> </a></li>
                            <li><a class="link_poly" data-type="sme" href="#"><span class="label"><?php echo lang('SME') ?></span><span class="value" id="sme_poly"></span> </a></li>
                            <li><a class="link_poly" data-type="mill" href="#"><span class="label"><?php echo lang('Mill') ?></span><span class="value" id="mill_poly"></span> </a></li>
                            <!--<li><a class="link_poly" data-type="" href="#" id="btn_detail_poly"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <div class="col-md-8">
                                <div class="value" id="box_plant_polygon_ha_mapped"></div>
                                <div class="desc">
                                    <?php echo lang('Palmoil Plantations Hectare Mapped with Polygon (Ha)') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/land_area.png"></div>
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_poly_mapped" data-type="farmer" href="#"><span class="label"><?php echo lang('Farmer') ?></span><span class="value" id="farmer_poly_mapped"></span> </a></li>
                            <li><a class="link_poly_mapped" data-type="sme" href="#"><span class="label"><?php echo lang('SME') ?></span><span class="value" id="sme_poly_mapped"></span> </a></li>
                            <li><a class="link_poly_mapped" data-type="mill" href="#"><span class="label"><?php echo lang('Mill') ?></span><span class="value" id="mill_poly_mapped"></span> </a></li>
                            <!--<li><a class="link_poly_mapped" data-type="" href="#" id="btn_detail_farmer"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_agents_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Palmoil SME Mapped') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url() ?>img/general/trader.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_mills_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Palmoil Mills Mapped') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url() ?>img/general/WAREHOUSE.PNG"></div>
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
                        <div id="gauge_plantation_registered"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_plant_ha_registered"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_farmer_mapped"></div>
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

<script type="text/javascript" src="<?= base_url() ?>js/modules/<?= $js ?>.js"></script>
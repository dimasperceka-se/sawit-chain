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

<div id='row-fluid'>

    <div class="page-head xs-pt-10 xs-pb-10">
        <div class="row">
            <div class="col-md-9">
                <!--<button id="btnRegenDashboard" type="button" class="btn btn-primary">Regenerate Dashboard</button>-->
            </div>
            <div class="col-md-3">
                <?php echo $this->load->view('list_region', $action, true); ?>
                <span style="margin:4px 12px 0px 0px;float:right;font-size:19px;"><?php echo lang('Filter')?> : </span>
            </div>
        </div>
    </div>

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

            <div class="col-md-3" style="display:none;">  <!-- hide dulu permintaan zaenal 23-08-2019 -->
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_sales">0</div>
                        <!-- <div class="value" id="box_consent_signed">0</div> -->
                        <div class="desc">
                            <?php echo lang('Palm Oil Farmers Sales')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/productivity.png"></div>
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

            <!-- <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_plantation_polygon_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Oil Palm Plantations Mapped with Polygon')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Oil-Palm-Plantations.png"></div>
                </div>
            </div> -->

            <!-- <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_plant_polygon_ha_mapped">0</div>
                        <div class="desc">
                            <?php echo lang('Oil Palm Plantations Hectare Mapped with Polygon (Ha)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/land_area.png"></div>
                </div>
            </div> -->

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

            <div class="col-md-6 xs-mt-20" style="display:none;">  <!-- hide dulu permintaan zaenal 23-08-2019 -->
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_farmer_sales"></div>
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

            <div class="col-md-6 xs-mt-20" style="display:none;">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="gauge_plantation_polygon_mapped"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20" style="display:none;">
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
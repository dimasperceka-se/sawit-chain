<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-15 15:41:03
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-05 15:37:22
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
            <div class="col-md-2"><h2><?php echo lang('Filter'); ?></h2></div>
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

                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control DashCombo" name="ftype" id="ftype">
                        <option value="All"><?php echo lang('All'); ?></option>
                        <option value="Smallholder"><?php echo lang('Smallholder'); ?></option>
                        <option value="SME"><?php echo lang('SME'); ?></option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <div class="main-content" >
        <br />

        <div class="row">

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_baseline">0</div>
                        <div class="desc">
                            <?php echo lang('Farmer Baseline')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/farmer_baseline.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_postline">0</div>
                        <div class="desc">
                            <?php echo lang('Farmer Post-Line')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/farmer_postline.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_plantation_baseline">0</div>
                        <div class="desc">
                            <?php echo lang('Oil Palm Plantation Baselines')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Oil-Palm-Plantation-Baselines.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_plantation_postline">0</div>
                        <div class="desc">
                            <?php echo lang('Oil Palm Plantation Post-Line')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Oil-Palm-Plantation-Post-Line.png"></div>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_productivity_baseline">0</div>
                        <div class="desc">
                            <?php echo lang('Baseline Plantation Yield (Mt/Ha/Year)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Baseline-Plantation-Yield.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_productivity_postline">0</div>
                        <div class="desc">
                            <?php echo lang('Post-Line Plantation Yield (Mt/Ha/Year)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Post-Line-Plantation-Yield.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_productivity_per_tree_baseline">0</div>
                        <div class="desc">
                            <?php echo lang('Baseline Oil Palm Tree Yield (Kg/Tree/Year)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Baseline-Oil-Palm-Tree-Yield.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_productivity_per_tree_postline">0</div>
                        <div class="desc">
                            <?php echo lang('Post-Line Oil Palm Tree Yield (Kg/Tree/Year)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Post-Line-Oil-Palm-Tree-Yield.png"></div>
                </div>
            </div>

        </div>

        <!--                                CHART                                                                               -->
        <div class="row">

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_garden_per_year"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_average_productivity"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_average_tree_productivity"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>

<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-11 14:51:15
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-11 17:18:37
 */
//cek apakah dashboard filter region aktif
$uri4 = $this->uri->segment(4);
?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    <?php if($uri4 ==""){?>
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo lang('NDPE (No Deforestation, No Peat, No Exploitation Policy Compliance)') ?>');
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
                        <div class="value" id="box_land_convert_forest_2010">0</div>
                        <div class="desc">
                            <?php echo lang('Number of Plantations where land conversion from forest took place after 2010')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Oil-Palm-Plantations.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_land_convert_peat_2010">0</div>
                        <div class="desc">
                            <?php echo lang('Number of Plantations where land conversion from peat land area took place after 2010')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Oil-Palm-Plantations.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_labor_right_abuses">0</div>
                        <div class="desc">
                            <?php echo lang('Number of Farmers with Labor right abuses')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/NDPE-Labor-right-abuse.png"></div>
                </div>
            </div>

            <!-- di hide aja dl -->

            <div class="col-md-3" style="display: none;">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_sustain_smart_agri_farmer">0</div>
                        <div class="desc">
                            <?php echo lang('Number of farmers with sustainable and climate smart agriculture practices')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/NDPE-climate-smart-agriculture-practices.png"></div>
                </div>
            </div>
            <div class="col-md-3" style="display: none;">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_willing_survey">0</div>
                        <div class="desc">
                            <?php echo lang('Number of farmers willing to be mapped and surveyed')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/petani2.png"></div>
                </div>
            </div>

        </div>

        <!--                                CHART                                                                               -->
        <div class="row">

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_land_forest"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_persen_land_forest"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_land_peat"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_persen_land_peat"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_labor_right"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_persen_labor_right"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_farmer_labor_abuse_number"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_labor_abuse_number"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_farmer_smart"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_persen_farmer_smart"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20" style="display: none;">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_farmer_willing_survey"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20" style="display: none;">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_persen_farmer_willing_survey"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>

<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
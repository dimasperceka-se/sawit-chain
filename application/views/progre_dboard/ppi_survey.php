<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-19 16:25:06
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-09-20 16:37:45
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
                        <div class="value" id="box_ppi_baseline"></div>
                        <div class="desc">
                            <?php echo lang('Number of PPI Baseline') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/decreased-poverty-index.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_ppi_postline"></div>
                        <div class="desc">
                            <?php echo lang('Number of PPI Post-line') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/decreased-poverty-index.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_poverty_125_baseline"></div>
                        <div class="desc">
                            <?php echo lang('Poverty Level Baseline $1.25') ?> (%)
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/decreased-poverty-index.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_poverty_125_postline"></div>
                        <div class="desc">
                            <?php echo lang('Poverty Level Post-Line $1.25') ?> (%)
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/decreased-poverty-index.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_poverty_25_baseline"></div>
                        <div class="desc">
                            <?php echo lang('Poverty Level Baseline $2.5') ?> (%)
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/decreased-poverty-index.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_poverty_25_postline"></div>
                        <div class="desc">
                            <?php echo lang('Poverty Level Post-Line $2.5') ?> (%)
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/decreased-poverty-index.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_dec_pov_125"></div>
                        <div class="desc">
                            <?php echo lang('Decreased Poverty Index $1.25 (%)') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/decreased-poverty-index.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_dec_pov_25"></div>
                        <div class="desc">
                            <?php echo lang('Decreased Poverty Index $2.5 (%)') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/decreased-poverty-index.png"></div>
                </div>
            </div>

        </div>

        <!-- Chart -->
        <div class="row">
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="chart_poverty_15"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="chart_poverty_25"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>

<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js?10"></script>
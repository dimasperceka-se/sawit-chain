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
<script language="javascript" type="text/javascript" src="<?php echo base_url()?>js/plugins/bootstrap-datepicker.js"></script>
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
            <div class="col-md-12">
                <div class="btn-group btn-hspace pull-right">
                    <button class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setFilter()"><?php echo lang('Cari') ?></button>
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="mill_nm"><?php echo lang('All Mill') ?></span>&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu" id="mill_list" style="max-height: 300px; overflow: auto; float:right">
                    </ul>
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="millgroup_nm"><?php echo lang('All Mill Group') ?></span>&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu" id="millgroup_list" style="max-height: 300px; overflow: auto; float:right">
                    </ul>
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <input type="text" id="datepicker1" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $action['awal'] ?>">
                    &nbsp;&nbsp;<?php echo lang('sampai') ?>&nbsp;&nbsp;
                    <input type="text" id="datepicker2" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $action['akhir'] ?>">&nbsp;&nbsp;
                </div>
                <div class="btn-group btn-hspace pull-right">
                    <span style="margin:4px 12px 0px 0px;float:right;font-size:19px;"><?php echo lang('Filter') ?> : </span> 
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
                        <div class="value" id="box_sales"></div>
                        <div class="desc">
                            <?php echo lang('Traceable Sales (MT)') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/traceability.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_sales"></div>
                        <div class="desc">
                            <?php echo lang('Number of farmer Sales') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/cpg2.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_nr_transaction"></div>
                        <div class="desc">
                            <?php echo lang('Number of transactions') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/productivity.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_agent_sales"></div>
                        <div class="desc">
                            <?php echo lang('SME Sales') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/trader.png"></div>
                </div>
            </div>
            <!-- <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_no_sales"></div>
                        <div class="desc">
                            <?php echo lang('Number of farmer has not sold') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/cpg2.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_production"></div>
                        <div class="desc">
                            <?php echo lang('Registered Production on Farm Level (MT FFB)') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/productivity.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_distance"></div>
                        <div class="desc">
                            <?php echo lang('Average Distance of FFB delivered from Farm to Mill') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/Average-Oil-Palm-Plantation-Size.png"></div>
                </div>
            </div> -->
        </div>

        <div class="row">

            <!-- <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_production"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_sales"></div>
                    </div>
                </div>
            </div> -->

            <!-- <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_farmer_trnsct"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_farmer_sales"></div>
                    </div>
                </div>
            </div>    

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_production_sales"></div>
                    </div>
                </div>
            </div> 

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_average_distance"></div>
                    </div>
                </div>
            </div>             -->

        </div>

        <div class="row">
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20"> <!-- 1 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie2"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 xs-mt-20"> <!-- 5 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_farmer"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie21"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie22"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20"> <!-- 2 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie31"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20"> <!-- 3 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie32"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20"> <!-- 4 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_sales"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_certified_farmer"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_certified_production"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_uncertified_farmer"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_uncertified_production"></div>
                        </div>
                    </div>
                </div>
            </div>

    </div>

    <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>
<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
<script>
        var checkin = $('#datepicker1').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev) {
            if (ev.date.valueOf() > checkout.date.valueOf()) {
                var newDate = new Date(ev.date)
                newDate.setDate(newDate.getDate() + 1);
                checkout.setValue(newDate);
            }
        }).data('datepicker');
        var checkout = $('#datepicker2').datepicker({
            format: 'yyyy-mm-dd',
            onRender: function(date) {
                return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
            }
        }).on('changeDate', function(ev) {
        }).data('datepicker');
</script>
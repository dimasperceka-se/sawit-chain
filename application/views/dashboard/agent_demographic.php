<?php
/**
 * @Author: nikolius
 * @Date:   2018-01-16 07:47:59
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-19 10:36:55
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
        var m_<?=$key[$i]?> = <?=($action[$key[$i]] === true ? 'true' : ($action[$key[$i]] === false ? 'false' : "'" . $action[$key[$i]] . "'"))?>;
    <?}?>
</script>

<div id="ext-content">

	<div id='row-fluid' style="display: none;">

        <div class="page-head xs-pt-10 xs-pb-10">
            <div class="row">
                <div class="col-md-9"></div>
                <div class="col-md-3">
                    <?php echo $this->load->view('list_region', $action, true); ?>
                    <span style="margin:4px 12px 0px 0px;float:right;font-size:19px;"><?php echo lang('Filter')?> : </span>
                </div>
            </div>
        </div>
        <br />

        <div class="main-content">

        	<div class="row">

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_total_agent">0</div>
                            <div class="desc">
                                <?=lang('SME')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/male-staff.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_total_agent_female">0</div>
                            <div class="desc">
                                <?=lang('Female SME')?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/female-staff.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_avg_agent_age">0</div>
                            <div class="desc">
                                <?=lang('Average SME\'s Age (Years)')?>
                            </div>
                        </div>
                        <div class="icon col-md-4">&nbsp;</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_agent_complete_primary_school"></div>
                            <div class="desc">
                                <?php echo lang('SME Completed Primary School') ?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/lulus_sd.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_total_agent_staff">0</div>
                            <div class="desc">
                                <?=lang('SME Staffs')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/male-staff.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_total_agent_staff_female">0</div>
                            <div class="desc">
                                <?=lang('Female SME Staffs')?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/female-staff.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_avg_agent_staff_age">0</div>
                            <div class="desc">
                                <?=lang('Average SME Staff\'s Age (Years)')?>
                            </div>
                        </div>
                        <div class="icon col-md-4">&nbsp;</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_avg_agent_vehicle">0</div>
                            <div class="desc">
                                <?=lang('Average Nr of SME\'s Vehicles to collect FFB')?>
                            </div>
                        </div>
                        <div class="icon col-md-4">&nbsp;</div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_gender_agent"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_gender_agent_staff"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_avg_age_agent"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_avg_age_agent_staff"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_agent_age_class"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_agent_staff_age_class"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_agent_vehicle"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

	<p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>


<style type="text/css">
    <?=$style?>
</style>

<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
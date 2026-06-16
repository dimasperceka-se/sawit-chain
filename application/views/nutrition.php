<?php
if ($js!='') {?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
    $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?$key = array_keys($action);
    for ($i=0;$i<sizeof($action);$i++) {?>
        var m_<?php echo $key[$i]?> = <?php echo ($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
        <?}?>
    </script>
    <div id="ext-content"></div>
    <div id='row-fluid' style="display:none">
        <div class="page-head xs-pt-10 xs-pb-10">
            <div class="row">
                <div class="col-md-2"><h2><?php echo lang('Filter') ?></h2></div>
                <div class="col-md-10">
                    <?php echo $this->load->view('list_region', $action, TRUE); ?>
                </div>
            </div>
        </div>
        <div class="main-content" >
            <div class="row">
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box1"></div>
                            <div class="desc"><?php echo lang('Peserta GNP')?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/petani2.png"/></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box2"></div>
                            <div class="desc"><?php echo lang('Peserta GNP Perempuan (%)') ?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/perempuan.png"/></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box5"></div>
                            <div class="desc"><?php echo lang('Average Age of GNP Participant')?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/usia.png"/></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box3"></div>
                            <div class="desc"><?php echo lang('Average IDDS') ?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/score.png"/></div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box6"></div>
                            <div class="desc"><?php echo lang('Established Nutrition Gardens')?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/garden.png"/></div>
                    </div>
                </div>

                <!-- <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box4"></div>
                            <div class="desc"><?php echo lang('Average Nutrition Garden Area (M2)') ?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/garden_nutrition.png"/></div>
                    </div>
                </div> -->

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_total_nutrition_garden_area"></div>
                            <div class="desc"><?php echo lang('Total Nutrition Garden Area (M2)') ?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/garden_nutrition.png"/></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box7"></div>
                            <div class="desc"><?php echo lang('Established Fish Ponds')?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/fish-pond-gray.png"/></div>
                    </div>
                </div>

                <!-- <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box8"></div>
                            <div class="desc"><?php echo lang('Average Fish Pond Area (M2)')?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/fish-pond-area-gray.png"/></div>
                    </div>
                </div> -->

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_fish_pond_area"></div>
                            <div class="desc"><?php echo lang('Total Fish Pond Area (M2)')?></div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/fish-pond-area-gray.png"/></div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie2"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_nutrition_garden"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_nutrition_area"></div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie4"></div>
                        </div>
                    </div>
                </div> -->
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_livestock"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_livestock_province"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_vegetable"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_fishpond"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_fishpond_area"></div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie5"></div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
        <!-- End .row-fluid -->
        <?}
        if ($style!='') {?>
        <style type="text/css">
            <?php echo $style?>
        </style>
        <?}?>
        <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>

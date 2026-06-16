<?php
if ($js!='') {
    ?>
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
                                <div class="value" id="box_diversification"></div>
                                <div class="desc">
                                    <?php echo lang('Diversification (Shade Trees on cocoa farm)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/tanaman_lain.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_carbon"></div>
                                <div class="desc">
                                    <?php echo lang('Average Carbon Stock') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_carbon_stock.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_emission"></div>
                                <div class="desc">
                                    <?php echo lang('Average Emission per MT Cocoa') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_emisi.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_emission_reduction"></div>
                                <div class="desc">
                                    <?php echo lang('Emission Nett Reduction (tCO2e Cocoa)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_emisi.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_emission_reduction_farm"></div>
                                <div class="desc">
                                    <?php echo lang('Emission Nett Reduction (tCO2e/Farm Cocoa)(%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_emisi.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_emission_reduction_ha"></div>
                                <div class="desc">
                                    <?php echo lang('Emission Nett Reduction (tCO2e/Ha Cocoa)(%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_emisi.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_emission_reduction_mt"></div>
                                <div class="desc">
                                    <?php echo lang('Emission Nett Reduction (tCO2e/MT Cocoa)(%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_emisi.png"></div>
                        </div>
                    </div>
                        <!-- <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_fertilizer"></div>
                                <div class="desc">
                                    <?php echo lang('Average Fertilizer per Hectare (Kg)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_fertilizer.png"></div>
                        </div>
                    </div> -->
                    <!-- 
                        <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_compost"></div>
                                <div class="desc">
                                    <?php echo lang('Average Compost per Hectare (Kg)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_kompos.png"></div>
                        </div>
                        </div>
                    
                    -->
                </div>
                <div class="row">
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_other"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_shade_tree"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_tree_category"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_carbon"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_emission"></div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_compost"></div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_compost"></div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_fertilizer"></div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_fertilizer"></div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_tree_cat"></div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_pesticide"></div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_pesticide"></div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_pesticide_province"></div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_24d"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_pest24d_province"></div>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_tCO2e"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_tCO2e_farm"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_tCO2e_ha"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_tCO2e_mt"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End .box -->
            </div>
            <!-- End .row-fluid -->
            <?}
            if ($style!='') {?>
            <style type="text/css">
                <?php echo $style?>
            </style>
            <?}?>
            <?php if ($js): ?>
                <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>
            <?php endif ?>

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
                                <div class="value" id="box_compost"></div>
                                <div class="desc">
                                    <?php echo lang('Average Compost per Hectare (Kg)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_kompos.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_fertilizer"></div>
                                <div class="desc">
                                    <?php echo lang('Average Fertilizer per Hectare (Kg)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/env_fertilizer.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_pesticide"></div>
                                <div class="desc">
                                    <?php echo lang('Farmers using Pesticides (%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/pesticide.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_organic_pesticide"></div>
                                <div class="desc">
                                    <?php echo lang('Farmers using Organic Pesticides (%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/pesticide-organic.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_chemical_fertilizer"></div>
                                <div class="desc">
                                    <?php echo lang('Farmers using Chemical Fertilizers (%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/fertilizer-chemical.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_organic_fertilizer"></div>
                                <div class="desc">
                                    <?php echo lang('Farmers using Organic Fertilizers (%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/fertilizer-organic.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_protective_equip"></div>
                                <div class="desc">
                                    <?php echo lang('Farmers using Protective Equipment (%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/protective-equipment.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_handling_safe"></div>
                                <div class="desc">
                                    <?php echo lang('Farmers Handling Pesticide Bottles Safely (%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/handling-pesticide-bottle-safely.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_storing_safe"></div>
                                <div class="desc">
                                    <?php echo lang('Farmers Storing Pesticide Safely (%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/storing-pesticide-safely.png"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_compost"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_compost"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_fertilizer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_fertilizer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_tree_cat"></div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="pie_pesticide"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_pesticide_province"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
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
                                <div id="chart_disease"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_pest"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_pesticide"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_pesticide_storage"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_pesticide_handling"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_herbicide_use"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_herbicide_user"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_insecticide_use"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_insecticide_user"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_fungicide_use"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_fungicide_user"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_cultural_chemical"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_protective_equip"></div>
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

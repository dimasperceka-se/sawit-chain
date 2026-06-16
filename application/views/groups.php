<?php
if ($js!='') {?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
$('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
$('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?$key = array_keys($action);
    for ($i=0;$i<sizeof($action);$i++) {?>
        var m_<?=$key[$i]?> = <?=($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
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
                            <div class="value" id="box_cpg"></div>
                            <div class="desc">
                                <?php echo lang('Kelompok Produksi kakao (CPG)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/cpg2.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_cpg_female"></div>
                            <div class="desc">
                                <?php echo lang('Kepala Kelompok Perempuan (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/perempuan.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_coop"></div>
                            <div class="desc">
                                <?php echo lang('Organisasi Petani') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/koperasi.PNG"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_coop_female"></div>
                            <div class="desc">
                                <?php echo lang('Female in Farmer Organization Management (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/perempuan.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_trader"></div>
                            <div class="desc">
                                <?php echo lang('Cocoa Traders') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/trader.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_trader_female"></div>
                            <div class="desc">
                                <?php echo lang('Female Cocoa Traders (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/perempuan.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_nursery"></div>
                            <div class="desc">
                                <?php echo lang('Pembibitan') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/pembibitan.png"></div>
                    </div>
                </div>                        

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_nursery_capacity"></div>
                            <div class="desc">
                                <?php echo lang('Kapasitas Pembibitan Per Tahun') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/kapasitas_pembibitan.png"></div>
                    </div>
                </div>
            </div>          
            <div class="row">
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_cpg"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_established_cpg"></div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie2"></div>
                        </div>
                    </div>
                </div> -->
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_cpg_functional"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_cpg_management_gender"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_coop_functional"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_coop_management_gender"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_trader"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_trader_gender"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_nursery"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_nursery_capacity"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_nursery_ownership"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End .row-fluid -->
        <?}
        if ($style!='') {?>
        <style type="text/css">
            <?=$style?>
        </style>
        <?}?>
        <script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>

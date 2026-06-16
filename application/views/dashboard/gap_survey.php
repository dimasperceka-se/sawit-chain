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
                            <div class="value" id="farmer_baseline"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Baseline Survey Petani') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/farmer_baseline.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="garden_baseline"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Baseline Survey Kebun Kakao') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/garden_baseline_survey.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="farmer_postline"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Post-Line Survey Petani') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/farmer_postline.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="garden_postline"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Post-Line Survey Kebun Kakao') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/garden_postline_survey.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="productivity_baseline"></div>
                            <div class="desc">
                                <?php echo lang('Baseline Produktivitas (Kg/Ha/Thn)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/baseline_productivity.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="productivity_postline"></div>
                            <div class="desc">
                                <?php echo lang('Post-Line Produktivitas (Kg/Ha/Thn)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/postline_productivity.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="tree_baseline_prod"></div>
                            <div class="desc">
                                <?php echo lang('Baseline Produktivitas Pohon (Kg/Pohon/Thn)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/tree_productivity_baseline.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="tree_postline_prod"></div>
                            <div class="desc">
                                <?php echo lang('Post-Line Produktivitas Pohon (Kg/Pohon/Thn)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/tree_productivity_postline.png"></div>
                    </div>
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
                        <div id="tree_avg_prod"></div>
                    </div>
                </div>
            </div>
                  
        </div>
    </div>
    <?}
    if ($style!='') {?>
    <style type="text/css">
        <?=$style?>
    </style>
    <?}?>
    <script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
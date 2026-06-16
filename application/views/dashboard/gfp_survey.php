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
                            <div class="value" id="box_gfp_baseline"></div>
                            <div class="desc">
                                <?php echo lang('GFP Baseline') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/master-gfp-participant.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_gfp_postline"></div>
                            <div class="desc">
                                <?php echo lang('GFP Post-Line') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/master-gfp-participant.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_bank_account_baseline"></div>
                            <div class="desc">
                                <?php echo lang('Number of Bank Accounts Baseline') ?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/master-gfp-participant.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_bank_account_postline"></div>
                            <div class="desc">
                                <?php echo lang('Number of Bank Accounts Post-Line') ?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/master-gfp-participant.png"></div>
                    </div>
                </div>
            </div>          
        </div>
        <div class="row">
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_bank_accounts"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_farmer_saving"></div>
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
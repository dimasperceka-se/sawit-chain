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
                                <div class="value" id="box_gfp"></div>
                                <div class="desc">
                                    <?php echo lang('Farmer Trained in GFP') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_female"></div>
                                <div class="desc">
                                    <?php echo lang('Female Participants') ?> (%)
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_female_participant.png"></div>
                        </div>
                    </div>
                    <!-- <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_fin"></div>
                                <div class="desc">
                                    <?php echo lang('Farmer GFP Baseline') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp_baseline.png"></div>
                        </div>
                    </div> -->
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_account"></div>
                                <div class="desc">
                                    <?php echo lang('Farmer with Bank Account') ?> (%)
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_account.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_saving"></div>
                                <div class="desc">
                                    <?php echo lang('Farmer with Saving') ?> (%)
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_saving.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box_loan"></div>
                                <div class="desc">
                                    <?php echo lang('Farmer with Loan Experience') ?> (%)
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_loan.png"></div>
                        </div>
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_household"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_kelamin"></div>
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_account"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_saving"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_loan_exp"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_loan_from"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_loan_for"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_product"></div>
                            </div>
                        </div>
                    </div>        
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_future"></div>
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


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
        <div class="row-fluid">

        <div class="main-content" >
            <div class="row">
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_icco_latest"></div>
                            <div class="desc" id="box_icco_latest_date"></div>
                            <div class="desc">
                                <?php echo lang('Latest ICCO Price (USD/MT)')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_loan.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_district_latest"></div>
                            <div class="desc" id="box_district_latest_date"></div>
                            <div class="desc">
                                <?php echo lang('Latest District Price (USD/MT)')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/loan-approved.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_icco_avg"></div>
                            <div class="desc">
                                <?php echo lang('Average ICCO Price (USD/MT)')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/loan-rejected.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_district_avg"></div>
                            <div class="desc">
                                <?php echo lang('Average District Price (USD/MT)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="stock_price"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End .row-fluid -->
    </div>
    <?}
    if ($style!='') {?>
    <style type="text/css">
        <?php echo $style?>
    </style>
    <?}?>
    <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>
